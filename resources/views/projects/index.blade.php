@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
    <p class="text-gray-600 mt-2">Select a project to view detailed cost breakdowns</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($allProjects as $project)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $project->project_number }}</h2>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $project->region?->name ?? 'Unknown' }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    @if($project->excluded_from_estimate)
                        <span class="inline-block text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded mb-2">Excluded</span>
                    @endif
                </p>
                <div class="space-y-1 mb-4">
                    <p class="text-sm">
                        <span class="text-gray-600">GFA:</span>
                        <span class="font-mono text-gray-900">
                            @if($project->gfa)
                                {{ number_format($project->gfa, 2) }} m²
                            @else
                                —
                            @endif
                        </span>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('projects.summary', $project) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 text-center">
                        Summary
                    </a>
                    <a href="{{ route('projects.transactions', $project) }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 text-sm font-medium rounded hover:bg-gray-300 text-center">
                        Details
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No projects found.</p>
        </div>
    @endforelse
</div>
@endsection
