<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EscalationRate;
use Illuminate\Http\Request;

class EscalationController extends Controller
{
    public function index()
    {
        $current = EscalationRate::latest()->first();
        $history = EscalationRate::with('updatedBy')->latest()->get();

        return view('admin.escalation.index', compact('current', 'history'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'monthly_rate_percent' => 'required|numeric|min:0|max:10',
            'notes' => 'required|string|max:500',
        ]);

        $validated['updated_by'] = auth()->id();

        EscalationRate::create($validated);

        return back()->with('success', 'Escalation rate updated.');
    }
}
