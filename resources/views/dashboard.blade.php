<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Dashboard</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════
             ADMIN DASHBOARD
        ═══════════════════════════════════════════ --}}
        @if(auth()->user()->role === 'admin')

            {{-- Row 1: 6 stat cards --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">

                {{-- Transactions --}}
                <div class="card p-5" style="border-top: 3px solid #0ea5e9;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #0ea5e9;">Transactions</p>
                            <p class="text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ number_format($total_transaction_count ?? 0) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Historical records</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #e0f2fe;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0ea5e9;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Historical Spend --}}
                <div class="card p-5" style="border-top: 3px solid #10b981;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #10b981;">Historical Spend</p>
                            <p class="text-lg font-black text-gray-900 mt-1.5 leading-none">{{ number_format(($historical_spend ?? 0) / 1000000, 1) }}M</p>
                            <p class="text-xs text-gray-400 mt-1">PKR total</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #d1fae5;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Historical Projects --}}
                <div class="card p-5" style="border-top: 3px solid #f59e0b;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #f59e0b;">Historical</p>
                            <p class="text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $historical_projects ?? 0 }}</p>
                            <p class="text-xs text-gray-400 mt-1">Past projects</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #fef3c7;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #f59e0b;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Forecast Projects --}}
                <div class="card p-5" style="border-top: 3px solid #8b5cf6;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #8b5cf6;">Forecast</p>
                            <p class="text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $forecast_projects ?? 0 }}</p>
                            <p class="text-xs text-gray-400 mt-1">Active estimates</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #ede9fe;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #8b5cf6;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Regions Covered --}}
                <div class="card p-5" style="border-top: 3px solid #06b6d4;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #06b6d4;">Regions</p>
                            <p class="text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $regions_covered ?? 0 }}</p>
                            <p class="text-xs text-gray-400 mt-1">With rate data</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #cffafe;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #06b6d4;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Rate Data Points --}}
                <div class="card p-5" style="border-top: 3px solid #ec4899;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider" style="color: #ec4899;">Rate Points</p>
                            <p class="text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $rate_data_points ?? 0 }}</p>
                            <p class="text-xs text-gray-400 mt-1">Element rates</p>
                        </div>
                        <div class="shrink-0 h-9 w-9 rounded-xl flex items-center justify-center" style="background: #fce7f3;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ec4899;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Row 2: Spend by Region + Quick Actions --}}
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- Spend by Region --}}
                <div class="lg:col-span-2 card-static p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Spend by Region</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Historical transaction totals</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background: #f0f0f8; color: #505b93;">PKR</span>
                    </div>

                    @php
                        $regionColors = ['#0ea5e9','#10b981','#f59e0b','#8b5cf6','#06b6d4','#ec4899'];
                        $maxSpend = $spendByRegion->max('total') ?: 1;
                    @endphp

                    <div class="space-y-3.5">
                        @forelse($spendByRegion as $i => $row)
                            @php $color = $regionColors[$i % count($regionColors)]; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $row->region_name }}</span>
                                    <span class="text-sm font-bold text-gray-900">PKR {{ number_format($row->total / 1000000, 2) }}M</span>
                                </div>
                                <div class="h-2 rounded-full" style="background: #f3f4f6;">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                         style="width: {{ round(($row->total / $maxSpend) * 100) }}%; background: {{ $color }};"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">No transaction data yet</p>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Actions + Meta --}}
                <div class="space-y-4">

                    {{-- Quick Actions --}}
                    <div class="card-static p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Quick Actions</p>
                        <div class="space-y-2.5">
                            <a href="{{ route('admin.transactions.create') }}"
                               class="btn-primary flex items-center gap-2.5 rounded-xl px-4 py-2.5 text-sm font-semibold text-white"
                               style="background: linear-gradient(135deg, #505b93, #3d4269);">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add Transaction
                            </a>
                            <a href="{{ route('admin.transactions.import') }}"
                               class="btn-primary flex items-center gap-2.5 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                Import CSV
                            </a>
                            <a href="{{ route('admin.users.create') }}"
                               class="btn-primary flex items-center gap-2.5 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                Add User
                            </a>
                        </div>
                    </div>

                    {{-- System Meta --}}
                    <div class="card-static p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">System</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Users</span>
                                <span class="text-sm font-bold text-gray-900">{{ $user_count ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Last Import</span>
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ $last_import ? $last_import->format('d M Y') : 'Never' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Rate Library</span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: #10b981;">
                                    <span class="h-1.5 w-1.5 rounded-full" style="background: #10b981;"></span>
                                    {{ ($rate_data_points ?? 0) > 0 ? 'Populated' : 'Empty' }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Row 3: Current Forecast Projects --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Active Forecast Projects</h3>
                    <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold" style="color: #505b93;">View all →</a>
                </div>

                @if($forecastProjects && $forecastProjects->count() > 0)
                    <div class="table-card">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background: #fafafa; border-bottom: 1px solid #ebebeb;">
                                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Project</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Region</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-400">GFA m²</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-400">Estimate (PKR)</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($forecastProjects as $project)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $project->name }}</td>
                                        <td class="px-5 py-3.5">
                                            @if($project->region)
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background: #f0f0f8; color: #505b93;">
                                                    {{ $project->region->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-gray-700">
                                            {{ $project->gross_floor_area ? number_format($project->gross_floor_area, 0) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold" style="color: #10b981;">
                                            @if($project->cost_estimate)
                                                {{ number_format($project->cost_estimate, 0) }}
                                            @else
                                                <span class="text-xs font-normal text-gray-400">Not estimated</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            @if($project->cost_estimate)
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold" style="background: #d1fae5; color: #065f46;">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Estimated
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold" style="background: #fef3c7; color: #92400e;">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <a href="{{ route('estimator.show', $project->id) }}"
                                               class="btn-primary inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold text-white"
                                               style="background: #505b93;">
                                                Open
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card p-8 text-center">
                        <p class="text-gray-400 text-sm">No forecast projects yet</p>
                        <a href="{{ route('admin.projects.create') }}"
                           class="btn-primary inline-block mt-4 rounded-xl px-5 py-2 text-sm font-semibold text-white"
                           style="background: #505b93;">
                            Create Project
                        </a>
                    </div>
                @endif
            </div>

        @endif

        {{-- ═══════════════════════════════════════════
             COST MANAGER DASHBOARD
        ═══════════════════════════════════════════ --}}
        @if(auth()->user()->role === 'cost_manager')
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="card p-8 text-center">
                    <div class="h-16 w-16 rounded-2xl mx-auto mb-5 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #0ea5e9, #0284c7); box-shadow: 0 6px 20px rgba(14,165,233,0.35);">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ready to Estimate?</h3>
                    <p class="text-sm text-gray-500 mb-6">Create parametric cost forecasts from the regional rate library.</p>
                    <a href="{{ route('estimator.index') }}"
                       class="btn-primary inline-block rounded-xl px-8 py-3 text-sm font-semibold text-white"
                       style="background: linear-gradient(135deg, #505b93, #3d4269);">
                        Open Estimator
                    </a>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════
             REVIEWER DASHBOARD
        ═══════════════════════════════════════════ --}}
        @if(auth()->user()->role === 'reviewer')
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="card p-5" style="border-top: 3px solid #8b5cf6;">
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: #8b5cf6;">Active Projects</p>
                    <p class="text-4xl font-black text-gray-900 mt-2">{{ $active_projects ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Forecast estimates in progress</p>
                </div>
                <div class="card p-8 text-center">
                    <div class="h-16 w-16 rounded-2xl mx-auto mb-5 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #ec4899, #db2777); box-shadow: 0 6px 20px rgba(236,72,153,0.35);">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">View Analytics</h3>
                    <p class="text-sm text-gray-500 mb-6">Review regional cost trends and rate coverage.</p>
                    <a href="{{ route('analytics.index') }}"
                       class="btn-primary inline-block rounded-xl px-8 py-3 text-sm font-semibold text-white"
                       style="background: linear-gradient(135deg, #505b93, #3d4269);">
                        Open Analytics
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
