<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\EstimatingElement;
use App\Models\RegionalRate;
use App\Models\ExchangeRate;
use App\Models\Project;

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
            'project' => null,
        ]);
    }

    public function show($projectId)
    {
        $project = Project::findOrFail($projectId);

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
            'project' => $project,
        ]);
    }

    public function saveEstimate($projectId)
    {
        $project = Project::findOrFail($projectId);

        $validated = request()->validate([
            'cost_estimate' => 'required|numeric|min:0',
            'gross_floor_area' => 'nullable|numeric|min:0',
        ]);

        $updateData = ['cost_estimate' => $validated['cost_estimate']];

        if (isset($validated['gross_floor_area'])) {
            $updateData['gross_floor_area'] = $validated['gross_floor_area'];
        }

        $project->update($updateData);

        return response()->json(['success' => true, 'message' => 'Estimate saved successfully']);
    }
}
