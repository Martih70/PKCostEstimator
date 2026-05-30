<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoricalEnrichmentController extends Controller
{
    public function index()
    {
        $projects = Project::with('region')
            ->where('project_type', 'historical')
            ->leftJoin('transactions', 'projects.id', '=', 'transactions.project_id')
            ->select(
                'projects.id',
                'projects.name',
                'projects.project_nr',
                'projects.region_id',
                'projects.gross_floor_area',
                'projects.seating_capacity',
                DB::raw('SUM(transactions.amount) as total_value')
            )
            ->groupBy('projects.id', 'projects.name', 'projects.project_nr', 'projects.region_id', 'projects.gross_floor_area', 'projects.seating_capacity')
            ->orderBy('projects.name')
            ->get();

        $regions = Region::orderBy('name')->get();

        $pdCodes = DB::table('transactions')
            ->join('projects', 'transactions.project_id', '=', 'projects.id')
            ->where('projects.project_type', 'historical')
            ->distinct()
            ->orderBy('transactions.raw_pd_code')
            ->pluck('transactions.raw_pd_code');

        $projectsJson = $projects->map(fn($p) => [
            'id'               => $p->id,
            'name'             => $p->name,
            'project_nr'       => $p->project_nr,
            'region_id'        => $p->region_id,
            'region_name'      => $p->region?->name,
            'gross_floor_area' => $p->gross_floor_area,
            'seating_capacity' => $p->seating_capacity,
            'total_value'      => $p->total_value,
        ])->values();

        return view('admin.historical-enrichment.index', compact('projects', 'regions', 'pdCodes', 'projectsJson'));
    }

    public function show($id)
    {
        $project = Project::with('region')->where('project_type', 'historical')->findOrFail($id);

        $transactions = DB::table('transactions')
            ->where('project_id', $id)
            ->orderBy('raw_pd_code')
            ->orderBy('transaction_date')
            ->get();

        $totalValue = $transactions->sum('amount');
        $txCount    = $transactions->count();

        // Group by PD code
        $grouped = $transactions->groupBy('raw_pd_code')->map(function ($rows) use ($totalValue) {
            $subtotal = $rows->sum('amount');
            return [
                'pd_code'  => $rows->first()->raw_pd_code,
                'subtotal' => $subtotal,
                'pct'      => $totalValue > 0 ? round($subtotal / $totalValue * 100, 1) : 0,
                'lines'    => $rows->map(fn($r) => [
                    'date'        => substr($r->transaction_date, 0, 10),
                    'description' => $r->item_description,
                    'amount'      => $r->amount,
                ])->values(),
            ];
        })->values();

        $dates    = $transactions->pluck('transaction_date')->filter()->sort()->values();
        $earliest = $dates->first() ? substr($dates->first(), 0, 10) : null;
        $latest   = $dates->last()  ? substr($dates->last(),  0, 10) : null;
        $durationMonths = null;
        if ($earliest && $latest && $earliest !== $latest) {
            $durationMonths = (int) round(
                \Carbon\Carbon::parse($earliest)->diffInDays(\Carbon\Carbon::parse($latest)) / 30.44
            );
        }

        return response()->json([
            'project' => [
                'id'               => $project->id,
                'name'             => $project->name,
                'project_nr'       => $project->project_nr,
                'region'           => $project->region?->name,
                'gross_floor_area' => $project->gross_floor_area,
                'seating_capacity' => $project->seating_capacity,
                'area_source'      => $project->area_source,
                'area_verified'    => $project->area_verified,
                'completion_date'  => $project->completion_date?->format('Y-m-d'),
                'notes'            => $project->notes,
            ],
            'total_value'      => $totalValue,
            'tx_count'         => $txCount,
            'earliest_date'    => $earliest,
            'latest_date'      => $latest,
            'duration_months'  => $durationMonths,
            'grouped'          => $grouped,
        ]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::where('project_type', 'historical')->findOrFail($id);

        $validated = $request->validate([
            'gross_floor_area' => 'nullable|numeric|min:1',
            'seating_capacity' => 'nullable|integer|min:1|max:65535',
            'area_source'      => 'nullable|in:Drawing,Survey,Satellite,Estimate,Unknown',
            'area_verified'    => 'nullable|in:Pending,Yes,No',
            'completion_date'  => 'nullable|date',
            'notes'            => 'nullable|string|max:2000',
        ]);

        $project->update($validated);

        // Recalculate derived rates for response (not stored)
        $totalValue = DB::table('transactions')->where('project_id', $id)->sum('amount');
        $ratePerM2  = ($project->gross_floor_area > 0)  ? round($totalValue / $project->gross_floor_area, 2)  : null;
        $costPerSeat = ($project->seating_capacity > 0) ? round($totalValue / $project->seating_capacity, 2) : null;

        return response()->json([
            'success'      => true,
            'rate_per_m2'  => $ratePerM2,
            'cost_per_seat' => $costPerSeat,
        ]);
    }
}
