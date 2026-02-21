<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\EstimatingElement;
use App\Models\RegionalRate;
use App\Models\ExchangeRate;

class EstimatorController extends Controller
{
    public function index()
    {
        $regions = Region::orderBy('name')->get(['id', 'name', 'slug']);
        $elements = EstimatingElement::orderBy('sort_order')->get(['id', 'code', 'name']);
        $exchangeRates = ExchangeRate::orderByDesc('effective_date')
            ->get()->unique('currency_code')->keyBy('currency_code');

        // Build nested JSON: { regionId: { elementId: { low, medium, high, high_plus } } }
        $regionalRates = RegionalRate::all();
        $ratesData = [];
        foreach ($regionalRates as $rate) {
            $ratesData[$rate->region_id][$rate->estimating_element_id] = [
                'low' => (float) $rate->low_rate,
                'medium' => (float) $rate->medium_rate,
                'high' => (float) $rate->high_rate,
                'high_plus' => (float) $rate->high_plus_rate,
                'project_count' => $rate->project_count,
            ];
        }

        return view('estimator.index', [
            'regions' => $regions,
            'elements' => $elements,
            'ratesJson' => json_encode($ratesData),
            'exchangeRates' => $exchangeRates,
        ]);
    }
}
