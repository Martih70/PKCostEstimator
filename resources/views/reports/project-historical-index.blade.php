<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Project Reports (Historical)</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-lg text-sm" style="background: #fffaf0; border: 1px solid #e5e5e5; padding: 16px; color: #706f6c;">
            <p>View detailed reports of actual project transactions. Click on any project to see AC code breakdowns and underlying PD code details.</p>
        </div>

        @if($historicalProjects->count() > 0)
            <div class="rounded-lg overflow-x-auto" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <table class="w-full text-sm">
                    <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Project Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">GFA (m²)</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody style="border-color: #e5e5e5; border-top: 1px solid #e5e5e5;">
                        @foreach($historicalProjects as $project)
                            <tr style="border-bottom: 1px solid #e5e5e5;">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $project->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $project->region?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right text-gray-900">{{ number_format($project->gross_floor_area ?? 0, 0) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('project.report-historical', $project->id) }}" class="text-sm font-medium" style="color: #505b93;">
                                        View Report →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <p style="color: #706f6c;">No historical projects available</p>
            </div>
        @endif
    </div>
</x-app-layout>
