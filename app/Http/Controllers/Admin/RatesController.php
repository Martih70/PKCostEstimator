<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegionalRate;
use Illuminate\Support\Facades\Artisan;

class RatesController extends Controller
{
    public function index()
    {
        $regionalRates = RegionalRate::with('region', 'estimatingElement')
            ->orderBy('region_id')
            ->orderBy('estimating_element_id')
            ->get();

        return view('admin.rates.index', compact('regionalRates'));
    }

    public function recalculate()
    {
        Artisan::call('calculate:rates');

        return back()->with('success', 'Rates recalculated successfully.');
    }
}
