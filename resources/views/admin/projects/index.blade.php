<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Projects</h2>
            <a href="{{ route('admin.projects.create') }}" class="text-sm font-medium px-4 py-2 rounded-lg text-white transition" style="background: #505b93;">
                Add Project
            </a>
        </div>
    </x-slot>

    <div class="space-y-12">
            @if($forecastProjects->count() > 0)
                <div class="rounded-lg overflow-x-auto" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <table class="w-full text-sm">
                        <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Project ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Unique ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">Budget Cost</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">Cost Estimate</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">GFA (m²)</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-900">Exclude</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody style="border-color: #e5e5e5; border-top: 1px solid #e5e5e5;">
                            @foreach($forecastProjects as $project)
                                <tr style="border-bottom: 1px solid #e5e5e5;">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $project->project_id ?? '—' }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-900">{{ $project->unique_id ?? '—' }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $project->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $project->region?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right text-gray-900">
                                        @if($project->budget_cost)
                                            PKR {{ number_format($project->budget_cost, 0) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium" style="color: #10b981;">
                                        @if($project->cost_estimate)
                                            PKR {{ number_format($project->cost_estimate, 0) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-900">
                                        <form method="POST" action="{{ route('admin.projects.update', $project->id) }}" class="inline-flex gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="gross_floor_area" value="{{ (int)$project->gross_floor_area }}" step="1" class="w-24 rounded px-2 py-1 text-right" style="border: 1px solid #e5e5e5; background: white;" />
                                            <button type="submit" class="text-xs font-medium" style="color: #505b93;">
                                                ✓
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form method="POST" action="{{ route('admin.projects.update', $project->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="exclude_from_estimator" value="0" />
                                            <input type="checkbox" name="exclude_from_estimator" value="1" {{ $project->exclude_from_estimator ? 'checked' : '' }} onchange="this.form.submit()" class="cursor-pointer" />
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col gap-2 items-center">
                                            <a href="{{ route('estimator.show', $project->id) }}" class="block text-xs font-medium px-3 py-1 rounded-lg transition w-24" style="background: #505b93; color: white;">
                                                Estimate
                                            </a>
                                            @if($project->cost_estimate)
                                                <a href="{{ route('project.report', $project->id) }}" class="block text-xs font-medium px-3 py-1 rounded-lg transition w-24" style="background: #10b981; color: white;">
                                                    Report
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <p style="color: #706f6c;">No forecast projects yet</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
