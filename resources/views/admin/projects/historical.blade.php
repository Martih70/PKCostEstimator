<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Historical Projects</h2>
            <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-lg text-sm" style="background: #fffaf0; border: 1px solid #e5e5e5; padding: 16px; color: #706f6c;">
            <p>These are the historical projects used as the data source for deriving construction cost rates across regions. Data from these projects is used to calculate the Low/Medium/High/High+ cost bands in the estimator.</p>
        </div>

        @if($historicalProjects->count() > 0)
            <div class="rounded-lg overflow-x-auto" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <table class="w-full text-sm">
                    <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Project ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Unique ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Budget Cost</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">GFA (m²)</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-900">Exclude</th>
                        </tr>
                    </thead>
                    <tbody style="border-color: #e5e5e5; border-top: 1px solid #e5e5e5;">
                        @foreach($historicalProjects as $project)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <p style="color: #706f6c;">No historical projects yet</p>
            </div>
        @endif
    </div>
</x-app-layout>
