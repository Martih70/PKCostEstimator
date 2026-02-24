<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Admin Dashboard -->
        @if(auth()->user()->role === 'admin')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Transactions Card -->
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: #706f6c;">TRANSACTIONS</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total_transaction_count ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg p-3 flex items-center justify-center" style="background: #0ea5e9; width: 48px; height: 48px;">
                            <svg class="h-6 w-6" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Amount Card -->
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: #706f6c;">TOTAL AMOUNT</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                PKR {{ number_format($total_amount ?? 0, 0) }}
                            </p>
                        </div>
                        <div class="rounded-lg p-3 flex items-center justify-center" style="background: #16a34a; width: 48px; height: 48px;">
                            <svg class="h-6 w-6" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Projects Card -->
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: #706f6c;">PROJECTS</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $active_projects ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg p-3 flex items-center justify-center" style="background: #a855f7; width: 48px; height: 48px;">
                            <svg class="h-6 w-6" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Last Import Card -->
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium" style="color: #706f6c;">LAST IMPORT</p>
                            <p class="text-sm font-semibold text-gray-900 mt-2">
                                @if($last_import)
                                    {{ $last_import->format('M d, Y H:i') }}
                                @else
                                    Never
                                @endif
                            </p>
                        </div>
                        <div class="rounded-lg p-3 flex items-center justify-center" style="background: #ea580c; width: 48px; height: 48px;">
                            <svg class="h-6 w-6" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="md:col-span-2 lg:col-span-1 rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <p class="text-sm font-medium mb-4" style="color: #706f6c;">QUICK ACTIONS</p>
                    <div class="space-y-3">
                        <a href="{{ route('admin.transactions.create') }}" class="block rounded-lg px-4 py-2 text-center text-sm font-medium text-white transition" style="background: #505b93; border: 1px solid #505b93; hover: #3d4269;">
                            Add Transaction
                        </a>
                        <a href="{{ route('admin.transactions.import') }}" class="block rounded-lg px-4 py-2 text-center text-sm font-medium transition" style="border: 1px solid #e5e5e5; color: #505b93; background: white;">
                            Import CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Current Projects Section -->
            <div class="mt-12">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f5f5f5;">
                        <span class="text-lg">📋</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Current Projects</h3>
                        <p class="text-sm" style="color: #706f6c;">Active forecast projects and their estimates</p>
                    </div>
                </div>

                @if($forecastProjects && $forecastProjects->count() > 0)
                    <div class="rounded-lg overflow-x-auto" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                        <table class="w-full text-sm">
                            <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Project Name</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                                    <th class="px-6 py-3 text-right font-semibold text-gray-900">GFA (m²)</th>
                                    <th class="px-6 py-3 text-right font-semibold text-gray-900">Cost Estimate</th>
                                    <th class="px-6 py-3 text-center font-semibold text-gray-900">Action</th>
                                </tr>
                            </thead>
                            <tbody style="border-color: #e5e5e5; border-top: 1px solid #e5e5e5;">
                                @foreach($forecastProjects as $project)
                                    <tr style="border-bottom: 1px solid #e5e5e5;">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $project->name }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $project->region?->name ?? '—' }}</td>
                                        <td class="px-6 py-4 text-right text-gray-900">
                                            @if($project->gross_floor_area)
                                                {{ number_format($project->gross_floor_area, 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium" style="color: #10b981;">
                                            @if($project->cost_estimate)
                                                PKR {{ number_format($project->cost_estimate, 0) }}
                                            @else
                                                <span style="color: #706f6c;">Not estimated</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('estimator.show', $project->id) }}" class="inline-block text-xs font-medium px-3 py-1 rounded-lg transition" style="background: #505b93; color: white;">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                        <p style="color: #706f6c;">No forecast projects yet</p>
                        <a href="{{ route('admin.projects.index') }}" class="inline-block mt-4 text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #505b93; color: white;">
                            Create Project
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Cost Manager Dashboard -->
        @if(auth()->user()->role === 'cost_manager')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="h-24 w-24 rounded-lg mx-auto mb-4 flex items-center justify-center" style="background: #0ea5e9;">
                        <svg class="h-14 w-14" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Ready to Estimate?</h3>
                    <p class="text-gray-600 mb-6">Start creating cost estimates using the parametric estimator.</p>
                    <a href="{{ route('estimator.index') }}" class="inline-block rounded-lg px-8 py-3 text-center text-sm font-medium text-white transition" style="background: #505b93;">
                        Open Estimator
                    </a>
                </div>
            </div>
        @endif

        <!-- Reviewer Dashboard -->
        @if(auth()->user()->role === 'reviewer')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <p class="text-sm font-medium" style="color: #706f6c;">ACTIVE PROJECTS</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $active_projects ?? 0 }}</p>
                </div>
                <div class="rounded-lg p-8 text-center" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="h-24 w-24 rounded-lg mx-auto mb-4 flex items-center justify-center" style="background: #ec4899;">
                        <svg class="h-14 w-14" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">View Analytics</h3>
                    <p class="text-gray-600 mb-6">Review cost trends and forecast accuracy.</p>
                    <a href="{{ route('analytics.index') }}" class="inline-block rounded-lg px-8 py-3 text-center text-sm font-medium text-white transition" style="background: #505b93;">
                        Open Analytics
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
