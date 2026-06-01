<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RegionalRate;
use App\Models\EstimatingElement;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Models\EscalationRate;
use App\Models\PdCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectReportController extends Controller
{
    public function show($projectId)
    {
        $project = Project::with('region')->findOrFail($projectId);

        // Get all estimating elements in order
        $elements = EstimatingElement::orderBy('sort_order')->get();

        // Build cost breakdown by band
        $regionalRates = RegionalRate::where('region_id', $project->region_id)->get();
        $ratesMap = $regionalRates->keyBy('estimating_element_id');

        $breakdown = [];
        $gfa = $project->gross_floor_area ?? 0;
        $adjustmentFactor = (float) ($project->region->cost_adjustment_factor ?? 1.0);

        // Escalation calculation
        $currentEscalationRate = EscalationRate::latest()->first();
        $monthlyRate = $currentEscalationRate ? (float) $currentEscalationRate->monthly_rate_percent : 0.5;

        $escalationMonths = 0;
        $escalationFactor = 1.0;
        $baseDataDate = null;
        $escalationApplied = false;

        if ($project->project_start_date) {
            // One latest-transaction timestamp per comparable project, then average those
            $avgTimestamp = DB::table('projects')
                ->join('transactions', 'projects.id', '=', 'transactions.project_id')
                ->where('projects.project_type', 'historical')
                ->where('projects.region_id', $project->region_id)
                ->groupBy('projects.id')
                ->selectRaw("MAX(strftime('%s', transactions.transaction_date)) as latest_ts")
                ->get()
                ->avg('latest_ts');

            if ($avgTimestamp) {
                $baseDataDate = \Carbon\Carbon::createFromTimestamp((int) $avgTimestamp)->startOfMonth();
                $projectStart = \Carbon\Carbon::parse($project->project_start_date)->startOfMonth();
                $escalationMonths = (int) round($baseDataDate->diffInMonths($projectStart, false));

                if ($escalationMonths !== 0) {
                    $escalationFactor = pow(1 + $monthlyRate / 100, $escalationMonths);
                    $escalationApplied = true;
                }
            }
        }

        // Count total comparable historical projects in this region (any transactions)
        $comparableProjectCount = DB::table('projects')
            ->join('transactions', 'projects.id', '=', 'transactions.project_id')
            ->where('projects.project_type', 'historical')
            ->where('projects.region_id', $project->region_id)
            ->distinct()
            ->count('projects.id');

        // Count distinct comparable projects per estimating element
        $elementProjectCounts = DB::table('projects')
            ->join('transactions', 'projects.id', '=', 'transactions.project_id')
            ->join('pd_codes', 'transactions.pd_code_id', '=', 'pd_codes.id')
            ->where('projects.project_type', 'historical')
            ->where('projects.region_id', $project->region_id)
            ->groupBy('pd_codes.estimating_element_id')
            ->select('pd_codes.estimating_element_id', DB::raw('COUNT(DISTINCT projects.id) as project_count'))
            ->pluck('project_count', 'estimating_element_id');

        // Get all transactions for this project to build PD code breakdown
        $transactions = Transaction::where('project_id', $project->id)->get();

        foreach ($elements as $element) {
            $rate = $ratesMap->get($element->id);
            if (!$rate) continue;

            $dataCount = (int) ($elementProjectCounts[$element->id] ?? 0);
            $confidence = match(true) {
                $dataCount >= 5 => ['label' => 'High',     'count' => $dataCount, 'color' => '#065f46', 'bg' => '#d1fae5'],
                $dataCount >= 3 => ['label' => 'Medium',   'count' => $dataCount, 'color' => '#1e40af', 'bg' => '#dbeafe'],
                $dataCount === 2 => ['label' => 'Low',     'count' => $dataCount, 'color' => '#92400e', 'bg' => '#fef3c7'],
                $dataCount === 1 => ['label' => 'Very Low','count' => $dataCount, 'color' => '#991b1b', 'bg' => '#fee2e2'],
                default          => ['label' => 'No data', 'count' => $dataCount, 'color' => '#374151', 'bg' => '#f3f4f6'],
            };

            $combinedFactor = $adjustmentFactor * $escalationFactor;

            $breakdown[$element->id] = [
                'code' => $element->code,
                'name' => $element->name,
                'confidence' => $confidence,
                'low' => [
                    'rate' => (float) $rate->low_rate * $combinedFactor,
                    'cost' => (float) $rate->low_rate * $combinedFactor * $gfa,
                ],
                'medium' => [
                    'rate' => (float) $rate->medium_rate * $combinedFactor,
                    'cost' => (float) $rate->medium_rate * $combinedFactor * $gfa,
                ],
                'high' => [
                    'rate' => (float) $rate->high_rate * $combinedFactor,
                    'cost' => (float) $rate->high_rate * $combinedFactor * $gfa,
                ],
                'high_plus' => [
                    'rate' => (float) $rate->high_plus_rate * $combinedFactor,
                    'cost' => (float) $rate->high_plus_rate * $combinedFactor * $gfa,
                ],
                'pdCodes' => [], // Will be populated below
            ];

            // Get PD codes that map to this estimating element
            $pdCodesForElement = PdCode::where('estimating_element_id', $element->id)->get();

            // Build PD code breakdown
            foreach ($pdCodesForElement as $pdCode) {
                $pdTransactions = $transactions->filter(fn($t) => $t->pd_code_id == $pdCode->id);

                if ($pdTransactions->isEmpty()) {
                    continue;
                }

                $totalAmount = (float) $pdTransactions->sum('amount');
                $ratePerM2 = $gfa > 0 ? $totalAmount / $gfa : 0;

                // Calculate costs for each band (proportionally based on regional rates)
                $pdBreakdown = [
                    'code' => $pdCode->code,
                    'name' => $pdCode->name,
                    'low' => [
                        'rate' => $ratePerM2,
                        'cost' => $ratePerM2 * $gfa,
                    ],
                    'medium' => [
                        'rate' => $ratePerM2,
                        'cost' => $ratePerM2 * $gfa,
                    ],
                    'high' => [
                        'rate' => $ratePerM2,
                        'cost' => $ratePerM2 * $gfa,
                    ],
                    'high_plus' => [
                        'rate' => $ratePerM2,
                        'cost' => $ratePerM2 * $gfa,
                    ],
                ];

                $breakdown[$element->id]['pdCodes'][] = $pdBreakdown;
            }
        }

        // Tally confidence levels across all elements
        $confidenceSummary = ['high' => 0, 'medium' => 0, 'low' => 0, 'very_low' => 0, 'no_data' => 0];
        foreach ($breakdown as $item) {
            match($item['confidence']['label']) {
                'High'     => $confidenceSummary['high']++,
                'Medium'   => $confidenceSummary['medium']++,
                'Low'      => $confidenceSummary['low']++,
                'Very Low' => $confidenceSummary['very_low']++,
                default    => $confidenceSummary['no_data']++,
            };
        }

        // Calculate band totals and with add-ons
        $bandTotals = [];
        foreach (['low', 'medium', 'high', 'high_plus'] as $band) {
            $construction = 0;
            foreach ($breakdown as $item) {
                $construction += $item[$band]['cost'] ?? 0;
            }
            $externals = $construction * 0.10;
            $overhead = ($construction + $externals) * 0.15;
            $subtotal = $construction + $externals + $overhead;

            $bandTotals[$band] = [
                'construction' => $construction,
                'externals' => $externals,
                'overhead' => $overhead,
                'subtotal' => $subtotal,
            ];
        }

        // ── Priority 5: Dual-Metric Forecast Comparison ────────────────────────

        // Area-based totals (already computed above)
        $areaLow  = $bandTotals['low']['subtotal'];
        $areaMid  = $bandTotals['medium']['subtotal'];
        $areaHigh = $bandTotals['high']['subtotal'];
        $avgAreaRatePerM2 = $gfa > 0 ? $areaMid / $gfa : 0;

        // Seating-based calculation
        $seatLow = $seatMid = $seatHigh = $avgCostPerSeat = null;
        $seatComparables = 0;

        if ($project->seating_capacity) {
            $seatProjects = DB::table('projects')
                ->join('transactions', 'projects.id', '=', 'transactions.project_id')
                ->where('projects.project_type', 'historical')
                ->where('projects.region_id', $project->region_id)
                ->whereNotNull('projects.seating_capacity')
                ->where('projects.seating_capacity', '>', 0)
                ->groupBy('projects.id', 'projects.seating_capacity')
                ->select(
                    'projects.id',
                    'projects.seating_capacity',
                    DB::raw('SUM(transactions.amount) as total_cost'),
                    DB::raw('MAX(transactions.transaction_date) as last_tx_date')
                )
                ->get();

            $seatComparables = $seatProjects->count();

            if ($seatComparables > 0) {
                $weightedItems = [];
                $totalWeight   = 0;

                foreach ($seatProjects as $sp) {
                    $costPerSeat = $sp->total_cost / $sp->seating_capacity;
                    $ageMonths   = (int) now()->diffInMonths(\Carbon\Carbon::parse($sp->last_tx_date));

                    $weight = match(true) {
                        $ageMonths <= 12 => 1.0,
                        $ageMonths <= 24 => 0.6,
                        $ageMonths <= 36 => 0.3,
                        default          => 0.1,
                    };

                    $weightedItems[] = ['cost' => $costPerSeat, 'weight' => $weight];
                    $totalWeight += $weight;
                }

                $weightedSum  = array_sum(array_map(fn($x) => $x['cost'] * $x['weight'], $weightedItems));
                $weightedMean = $totalWeight > 0 ? $weightedSum / $totalWeight : 0;

                if ($seatComparables >= 3) {
                    usort($weightedItems, fn($a, $b) => $a['cost'] <=> $b['cost']);
                    $cumWeight = 0;
                    $p25 = $p50 = $p75 = null;
                    foreach ($weightedItems as $item) {
                        $cumWeight += $item['weight'];
                        $pct = $cumWeight / $totalWeight;
                        if ($p25 === null && $pct >= 0.25) $p25 = $item['cost'];
                        if ($p50 === null && $pct >= 0.50) $p50 = $item['cost'];
                        if ($p75 === null && $pct >= 0.75) $p75 = $item['cost'];
                    }
                    $perSeatLow  = $p25 ?? $weightedMean * 0.85;
                    $perSeatMid  = $p50 ?? $weightedMean;
                    $perSeatHigh = $p75 ?? $weightedMean * 1.15;
                } else {
                    $perSeatLow  = $weightedMean * 0.85;
                    $perSeatMid  = $weightedMean;
                    $perSeatHigh = $weightedMean * 1.15;
                }

                $seats          = $project->seating_capacity;
                $seatLow        = $perSeatLow  * $seats;
                $seatMid        = $perSeatMid  * $seats;
                $seatHigh       = $perSeatHigh * $seats;
                $avgCostPerSeat = $weightedMean;
            }
        }

        // Divergence between the two mid-points
        $divergencePct     = null;
        $divergenceWarning = false;
        if ($areaMid > 0 && $seatMid !== null) {
            $divergencePct     = abs($areaMid - $seatMid) / $areaMid * 100;
            $divergenceWarning = $divergencePct > 15;
        }

        // Overall confidence score (1–5)
        $bothMetrics    = $seatMid !== null && $areaMid > 0;
        $minComparables = $bothMetrics
            ? min($comparableProjectCount, $seatComparables)
            : max($comparableProjectCount, $seatComparables);

        $confidenceScore = match(true) {
            $bothMetrics && $minComparables >= 5 && !$divergenceWarning => 5,
            $bothMetrics && $minComparables >= 3                        => 4,
            !$bothMetrics && $minComparables >= 3                       => 3,
            !$bothMetrics && $minComparables === 2                      => 2,
            default                                                     => 1,
        };

        // ────────────────────────────────────────────────────────────────────────

        $exchangeRates = ExchangeRate::orderByDesc('effective_date')
            ->get()
            ->unique('currency_code')
            ->pluck('rate_to_pkr', 'currency_code')
            ->mapWithKeys(fn($rate, $code) => [$code => (float)$rate])
            ->toArray();

        $exchangeRates['PKR'] = 1.0;

        return view('reports.project', [
            'project'              => $project,
            'breakdown'            => $breakdown,
            'bandTotals'           => $bandTotals,
            'exchangeRates'        => $exchangeRates,
            'comparableProjectCount' => $comparableProjectCount,
            'confidenceSummary'    => $confidenceSummary,
            'adjustmentFactor'     => $adjustmentFactor,
            'escalationApplied'    => $escalationApplied,
            'escalationMonths'     => $escalationMonths,
            'escalationFactor'     => $escalationFactor,
            'monthlyRate'          => $monthlyRate,
            'baseDataDate'         => $baseDataDate,
            // Priority 5
            'areaLow'              => $areaLow,
            'areaMid'              => $areaMid,
            'areaHigh'             => $areaHigh,
            'avgAreaRatePerM2'     => $avgAreaRatePerM2,
            'seatLow'              => $seatLow,
            'seatMid'              => $seatMid,
            'seatHigh'             => $seatHigh,
            'avgCostPerSeat'       => $avgCostPerSeat,
            'seatComparables'      => $seatComparables,
            'divergencePct'        => $divergencePct,
            'divergenceWarning'    => $divergenceWarning,
            'confidenceScore'      => $confidenceScore,
        ]);
    }

    public function historicalIndex()
    {
        $historicalProjects = Project::with('region')
            ->where('project_type', 'historical')
            ->orderBy('name')
            ->get();

        return view('reports.project-historical-index', compact('historicalProjects'));
    }

    public function historical($projectId)
    {
        $project = Project::with('region')->findOrFail($projectId);

        // Get all estimating elements in order
        $elements = EstimatingElement::orderBy('sort_order')->get();

        // Get all transactions for this project
        $transactions = Transaction::where('project_id', $project->id)->get();

        $breakdown = [];
        $gfa = $project->gross_floor_area ?? 0;

        foreach ($elements as $element) {
            // Get PD codes for this element
            $pdCodesForElement = PdCode::where('estimating_element_id', $element->id)->get();

            $elementTotal = 0;
            $pdCodes = [];

            // Build PD code breakdown
            foreach ($pdCodesForElement as $pdCode) {
                $pdTransactions = $transactions->filter(fn($t) => $t->pd_code_id == $pdCode->id);

                if ($pdTransactions->isEmpty()) {
                    continue;
                }

                $totalAmount = (float) $pdTransactions->sum('amount');
                $elementTotal += $totalAmount;

                // Build individual transactions
                $transactionLines = [];
                foreach ($pdTransactions as $transaction) {
                    $transactionLines[] = [
                        'date' => $transaction->transaction_date->format('d/m/Y'),
                        'description' => $transaction->item_description,
                        'amount' => (float) $transaction->amount,
                    ];
                }

                // Sort by date
                usort($transactionLines, fn($a, $b) => strtotime($a['date']) - strtotime($b['date']));

                $pdCodes[] = [
                    'code' => $pdCode->code,
                    'name' => $pdCode->name,
                    'amount' => $totalAmount,
                    'transactions' => $transactionLines,
                ];
            }

            // Only include element if it has transactions
            if ($elementTotal > 0) {
                $breakdown[$element->id] = [
                    'code' => $element->code,
                    'name' => $element->name,
                    'amount' => $elementTotal,
                    'pdCodes' => $pdCodes,
                ];
            }
        }

        // Calculate total
        $totalAmount = array_sum(array_map(fn($item) => $item['amount'], $breakdown));

        // Get exchange rates
        $exchangeRates = ExchangeRate::orderByDesc('effective_date')
            ->get()
            ->unique('currency_code')
            ->pluck('rate_to_pkr', 'currency_code')
            ->mapWithKeys(fn($rate, $code) => [$code => (float)$rate])
            ->toArray();

        $exchangeRates['PKR'] = 1.0;

        return view('reports.project-historical', [
            'project' => $project,
            'breakdown' => $breakdown,
            'totalAmount' => $totalAmount,
            'exchangeRates' => $exchangeRates,
        ]);
    }
}
