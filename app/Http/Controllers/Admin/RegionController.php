<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $validated = $request->validate([
            'cost_adjustment_factor' => 'required|numeric|min:0.1|max:5.0',
        ]);

        $region->update($validated);

        return back()->with('success', "Adjustment factor for {$region->name} updated.");
    }
}
