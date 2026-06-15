<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RegionalRate;
use App\Models\EstimatingElement;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Models\EscalationRate;
use App\Models\PdCode;
use App\Models\Setting;
use App\Services\AiAdvisoryService;
use App\Services\ForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectReportController extends Controller
{
    /**
     * PD codes with no estimating_element_id (site/external works,
     * preliminaries, property acquisition, other expenses) are grouped under
     * these categories on the historical actuals report — see
     * unmappedPdCodeGroupKey() and historical().
     */
    private const UNMAPPED_PD_CODE_GROUPS = [
        'site'     => ['code' => 'EXT',  'name' => 'Site & External Works'],
        'prelims'  => ['code' => 'PRE',  'name' => 'Preliminaries & Overheads'],
        'property' => ['code' => 'PROP', 'name' => 'Property Acquisition'],
        'other'    => ['code' => 'OTH',  'name' => 'Other Expenses'],
    ];

    public function __construct(private ForecastService $forecastService)
    {
    }

    /**
     * Build the full set of computed report figures for a project. This is
     * the single source of truth for both the report view and the Stage 3
     * AI advisory endpoints — every figure either of them shows is computed
     * here, once, deterministically.
     */
    public function buildReportData($projectId): array
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

        // Stage 1: shrinkage strength for blending sparse elemental rates
        // toward the all-region mean (see ForecastService::blendRate).
        $shrinkageK = (float) Setting::get('forecast_shrinkage_k', 3);

        // Stage 2: uncertainty constant controlling how much the Low/High
        // band widens as the number of comparable projects shrinks
        // (see ForecastService::uncertaintyMultiplier).
        $uncertaintyC = (float) Setting::get('forecast_uncertainty_c', 2);

        // Escalation calculation
        $currentEscalationRate = EscalationRate::latest()->first();
        $monthlyRate = $currentEscalationRate ? (float) $currentEscalationRate->monthly_rate_percent : 0.5;

        $escalationMonths = 0;
        $escalationFactor = 1.0;
        $baseDataDate = null;
        $escalationApplied = false;

        // Escalate to the project's planned start date if known, otherwise to
        // today — so a forecast with no start date is still uplifted to
        // current price levels rather than left at the historical data's vintage.
        $escalationTargetDate = $project->project_start_date
            ? \Carbon\Carbon::parse($project->project_start_date)->startOfMonth()
            : now()->startOfMonth();

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
            $escalationMonths = (int) round($baseDataDate->diffInMonths($escalationTargetDate, false));

            if ($escalationMonths !== 0) {
                $escalationFactor = pow(1 + $monthlyRate / 100, $escalationMonths);
                $escalationApplied = true;
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

        // Regional adjustment + escalation combined factor, applied to
        // every blended rate below (and reused for the Stage 2 area-based
        // spread scaling further down).
        $combinedFactor = $adjustmentFactor * $escalationFactor;

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

            // Stage 1: blend the raw local rates toward the all-region mean
            // before applying the regional adjustment and escalation.
            $blend = $this->forecastService->blendRegionalRate($rate, $shrinkageK);

            $breakdown[$element->id] = [
                'code' => $element->code,
                'name' => $element->name,
                'confidence' => $confidence,
                'blend' => $blend,
                'low' => [
                    'rate' => $blend['low'] * $combinedFactor,
                    'cost' => $blend['low'] * $combinedFactor * $gfa,
                ],
                'medium' => [
                    'rate' => $blend['medium'] * $combinedFactor,
                    'cost' => $blend['medium'] * $combinedFactor * $gfa,
                ],
                'high' => [
                    'rate' => $blend['high'] * $combinedFactor,
                    'cost' => $blend['high'] * $combinedFactor * $gfa,
                ],
                'high_plus' => [
                    'rate' => $blend['high_plus'] * $combinedFactor,
                    'cost' => $blend['high_plus'] * $combinedFactor * $gfa,
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

        // Area-based Mid (unchanged by Stage 2 — see plan step 2d)
        $areaMid  = $bandTotals['medium']['subtotal'];
        $avgAreaRatePerM2 = $gfa > 0 ? $areaMid / $gfa : 0;

        // Stage 2: widen the area-based Low/High band based on how many
        // comparable projects support it. base_spread is the standard
        // deviation of each comparable project's total rate (PKR/m2,
        // summed across elements); if fewer than 2 local comparables
        // exist, fall back to the spread across all regions.
        $areaRates = $this->forecastService->areaRatesForRegion($project->region_id);
        $nArea = count($areaRates);
        $areaUsedBroadSpread = $nArea < 2;
        $areaBaseSpreadPerM2 = $this->forecastService->standardDeviation(
            $areaUsedBroadSpread ? $this->forecastService->broadAreaRates() : $areaRates
        );

        // Scale the per-m2 rate spread into total-subtotal terms via the
        // same path construction costs take to become the subtotal
        // (x combinedFactor x gfa x markup ratio for externals/overhead).
        $markupRatio = $bandTotals['medium']['construction'] > 0
            ? $bandTotals['medium']['subtotal'] / $bandTotals['medium']['construction']
            : 1.0;
        $areaSpreadTotal = $areaBaseSpreadPerM2 * $combinedFactor * $gfa * $markupRatio;

        $areaBand = $this->forecastService->widenBand($areaMid, $areaSpreadTotal, $nArea, $uncertaintyC);
        $areaLow  = $areaBand['low'];
        $areaHigh = $areaBand['high'];

        // Seating-based calculation
        $seatLow = $seatMid = $seatHigh = $avgCostPerSeat = null;
        $seatComparables = 0;
        $seatBand = null;
        $seatUsedBroadSpread = false;

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
                    $p50 = null;
                    foreach ($weightedItems as $item) {
                        $cumWeight += $item['weight'];
                        if ($p50 === null && ($cumWeight / $totalWeight) >= 0.50) {
                            $p50 = $item['cost'];
                        }
                    }
                    $perSeatMid = $p50 ?? $weightedMean;
                } else {
                    $perSeatMid = $weightedMean;
                }

                $seats          = $project->seating_capacity;
                $seatMid        = $perSeatMid  * $seats;
                $avgCostPerSeat = $weightedMean;

                // Stage 2: widen the seat-based Low/High band based on how
                // many comparable projects support it. base_spread is the
                // standard deviation of cost-per-seat across comparables;
                // if fewer than 2 local comparables exist, fall back to the
                // spread across all regions.
                $seatLocalRates = array_column($weightedItems, 'cost');
                $seatUsedBroadSpread = $seatComparables < 2;
                $seatBaseSpreadPerSeat = $this->forecastService->standardDeviation(
                    $seatUsedBroadSpread ? $this->forecastService->broadSeatRates() : $seatLocalRates
                );
                $seatSpreadTotal = $seatBaseSpreadPerSeat * $seats;

                $seatBand = $this->forecastService->widenBand($seatMid, $seatSpreadTotal, $seatComparables, $uncertaintyC);
                $seatLow  = $seatBand['low'];
                $seatHigh = $seatBand['high'];
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

        return [
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
            'escalationTargetDate' => $escalationTargetDate,
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
            // Stage 2: band-width context
            'areaBand'             => $areaBand,
            'areaUsedBroadSpread'  => $areaUsedBroadSpread,
            'seatBand'             => $seatBand,
            'seatUsedBroadSpread'  => $seatUsedBroadSpread,
            // Stage 3: shrinkage/uncertainty inputs, useful as AI context
            'shrinkageK'           => $shrinkageK,
            'uncertaintyC'         => $uncertaintyC,
        ];
    }

    public function show($projectId)
    {
        $data = $this->buildReportData($projectId);
        $data['aiConfigured'] = app(AiAdvisoryService::class)->isConfigured();

        return view('reports.project', $data);
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

        // Some PD codes (site/external works, preliminaries & overheads,
        // property acquisition, other expenses) aren't mapped to any of the
        // 9 estimating elements above. Group their transactions here so this
        // report's total reconciles with the admin transaction-derived total
        // for the same project, instead of silently dropping them.
        $unmappedGroups = [];
        foreach (PdCode::whereNull('estimating_element_id')->get() as $pdCode) {
            $pdTransactions = $transactions->filter(fn($t) => $t->pd_code_id == $pdCode->id);

            if ($pdTransactions->isEmpty()) {
                continue;
            }

            $totalAmount = (float) $pdTransactions->sum('amount');

            $transactionLines = [];
            foreach ($pdTransactions as $transaction) {
                $transactionLines[] = [
                    'date' => $transaction->transaction_date->format('d/m/Y'),
                    'description' => $transaction->item_description,
                    'amount' => (float) $transaction->amount,
                ];
            }
            usort($transactionLines, fn($a, $b) => strtotime($a['date']) - strtotime($b['date']));

            $groupKey = $this->unmappedPdCodeGroupKey($pdCode->code);
            $unmappedGroups[$groupKey]['amount'] = ($unmappedGroups[$groupKey]['amount'] ?? 0) + $totalAmount;
            $unmappedGroups[$groupKey]['pdCodes'][] = [
                'code' => $pdCode->code,
                'name' => $pdCode->name,
                'amount' => $totalAmount,
                'transactions' => $transactionLines,
            ];
        }

        foreach (self::UNMAPPED_PD_CODE_GROUPS as $key => $group) {
            if (!isset($unmappedGroups[$key])) {
                continue;
            }

            $breakdown['group_' . $key] = [
                'code' => $group['code'],
                'name' => $group['name'],
                'amount' => $unmappedGroups[$key]['amount'],
                'pdCodes' => $unmappedGroups[$key]['pdCodes'],
            ];
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

    /**
     * Which UNMAPPED_PD_CODE_GROUPS category a PD code with no
     * estimating_element_id falls into, based on its code prefix.
     */
    private function unmappedPdCodeGroupKey(string $code): string
    {
        return match(true) {
            str_starts_with($code, '20G') => 'site',
            str_starts_with($code, '10S'), str_starts_with($code, '19S') => 'prelims',
            str_starts_with($code, '01P') => 'property',
            default => 'other',
        };
    }
}
