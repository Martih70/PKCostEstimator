<?php

namespace App\Http\Controllers;

use App\Models\RegionalRate;
use App\Models\EstimatingElement;
use App\Models\Region;

class AnalyticsController extends Controller
{
    public function index()
    {
        $regionalRates = RegionalRate::with('region', 'estimatingElement')->orderBy('region_id')->get();
        $regions = Region::all();
        $elements = EstimatingElement::orderBy('sort_order')->get();

        return view('analytics.index', [
            'regionalRates' => $regionalRates,
            'regions' => $regions,
            'elements' => $elements,
        ]);
    }
}
