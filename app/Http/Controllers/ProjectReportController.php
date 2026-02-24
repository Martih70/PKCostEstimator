<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RegionalRate;
use App\Models\EstimatingElement;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Models\PdCode;
use Illuminate\Http\Request;

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

        // Get all transactions for this project to build PD code breakdown
        $transactions = Transaction::where('project_id', $project->id)->get();

        foreach ($elements as $element) {
            $rate = $ratesMap->get($element->id);
            if (!$rate) continue;

            $breakdown[$element->id] = [
                'code' => $element->code,
                'name' => $element->name,
                'low' => [
                    'rate' => (float) $rate->low_rate,
                    'cost' => (float) $rate->low_rate * $gfa,
                ],
                'medium' => [
                    'rate' => (float) $rate->medium_rate,
                    'cost' => (float) $rate->medium_rate * $gfa,
                ],
                'high' => [
                    'rate' => (float) $rate->high_rate,
                    'cost' => (float) $rate->high_rate * $gfa,
                ],
                'high_plus' => [
                    'rate' => (float) $rate->high_plus_rate,
                    'cost' => (float) $rate->high_plus_rate * $gfa,
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

        $exchangeRates = ExchangeRate::orderByDesc('effective_date')
            ->get()
            ->unique('currency_code')
            ->pluck('rate_to_pkr', 'currency_code')
            ->mapWithKeys(fn($rate, $code) => [$code => (float)$rate])
            ->toArray();

        $exchangeRates['PKR'] = 1.0;

        return view('reports.project', [
            'project' => $project,
            'breakdown' => $breakdown,
            'bandTotals' => $bandTotals,
            'exchangeRates' => $exchangeRates,
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
