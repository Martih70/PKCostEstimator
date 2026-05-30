<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Region;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $historicalProjects = Project::with('region')
            ->where('project_type', 'historical')
            ->orderBy('name')
            ->get();

        $forecastProjects = Project::with('region')
            ->where('project_type', 'forecast')
            ->orderBy('name')
            ->get();

        return view('admin.projects.index', compact('historicalProjects', 'forecastProjects'));
    }

    public function historical()
    {
        $historicalProjects = Project::with('region')
            ->where('project_type', 'historical')
            ->orderBy('name')
            ->get();

        return view('admin.projects.historical', compact('historicalProjects'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('admin.projects.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|string|unique:projects,project_id',
            'unique_id' => 'required|string|unique:projects,unique_id',
            'name' => 'required|string',
            'region_id' => 'required|exists:regions,id',
            'budget_cost' => 'nullable|numeric|min:0',
            'seating_capacity' => 'nullable|integer|min:1|max:65535',
            'project_start_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // New projects created via form are always forecast projects
        $validated['project_type'] = 'forecast';

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'gross_floor_area' => 'nullable|numeric|min:0',
            'seating_capacity' => 'nullable|integer|min:1|max:65535',
            'project_start_date' => 'nullable|date',
            'exclude_from_estimator' => 'boolean',
        ]);

        $project->update($validated);

        $updatedField = collect(['gross_floor_area', 'seating_capacity', 'project_start_date'])
            ->first(fn($f) => $request->has($f));

        return back()->with('updated_id', $id)->with('updated_field', $updatedField);
    }
}
