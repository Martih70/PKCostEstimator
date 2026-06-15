<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Project Report</h2>
            <div class="flex gap-3">
                <a href="{{ route('estimator.show', $project->id) }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #505b93; color: white;">
                    Edit Estimate
                </a>
                <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="reportViewer()" x-init='exchangeRates = @json($exchangeRates); initExpandState();'>

        <div class="mb-6 flex items-center gap-4">
            <label class="text-sm font-medium text-gray-900">Currency:</label>
            <select x-model="currency" class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white; color: #1b1b18;">
                <option value="PKR">PKR (Pakistan Rupees)</option>
                <option value="USD">USD (US Dollar)</option>
                <option value="GBP">GBP (British Pound)</option>
                <option value="EUR">EUR (Euro)</option>
            </select>
        </div>

    <div class="space-y-12">

        {{-- Step 2d: Thin-dataset warning banner --}}
        @if($comparableProjectCount < 3)
        <div class="rounded-lg p-4 flex gap-3 items-start" style="background: #fef3c7; border: 1px solid #f59e0b;">
            <span class="text-lg" style="color: #92400e;">⚠</span>
            <div>
                <p class="text-sm font-semibold" style="color: #92400e;">Warning: This forecast is based on fewer than 3 comparable projects in this region.</p>
                <p class="text-sm mt-1" style="color: #92400e;">Treat all figures as indicative only and apply professional judgement before use.</p>
            </div>
        </div>
        @endif

        <!-- Project Header -->
        <div class="rounded-lg p-8" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Project Name</p>
                    <p class="text-lg font-bold text-gray-900">{{ $project->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Region</p>
                    <p class="text-lg font-bold text-gray-900">{{ $project->region?->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Gross Floor Area</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($project->gross_floor_area ?? 0, 0) }} m²</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Project ID</p>
                    <p class="text-lg font-bold text-gray-900">{{ $project->project_nr ?? '—' }}</p>
                </div>
                @if($project->seating_capacity)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Seating Capacity</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($project->seating_capacity) }} seats</p>
                </div>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Regional Adjustment</p>
                    <p class="text-sm font-bold text-gray-900">
                        {{ $project->region?->name }}
                        <span class="font-mono">({{ number_format($adjustmentFactor, 2) }})</span>
                    </p>
                    <p class="text-xs mt-0.5" style="color: #706f6c;">
                        @if($adjustmentFactor == 1.0)
                            Base rate — no adjustment
                        @elseif($adjustmentFactor > 1.0)
                            Rates adjusted +{{ number_format(($adjustmentFactor - 1) * 100, 1) }}% above base
                        @else
                            Rates adjusted {{ number_format(($adjustmentFactor - 1) * 100, 1) }}% below base
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Inflation Factor</p>
                    @if($escalationApplied)
                        <p class="text-sm font-bold text-gray-900">
                            {{ $escalationMonths }} months at {{ $monthlyRate }}%/month
                            = +{{ number_format(($escalationFactor - 1) * 100, 1) }}% uplift
                        </p>
                        <p class="text-xs mt-0.5" style="color: #706f6c;">
                            Base data avg: {{ $baseDataDate->format('M Y') }}
                            →
                            @if($project->project_start_date)
                                Start: {{ \Carbon\Carbon::parse($project->project_start_date)->format('M Y') }}
                            @else
                                Today: {{ $escalationTargetDate->format('M Y') }}
                            @endif
                        </p>
                        @unless($project->project_start_date)
                            <p class="text-xs mt-0.5" style="color: #f59e0b;">
                                No project start date set — uplift calculated to today's prices instead.
                            </p>
                        @endunless
                    @elseif($baseDataDate)
                        <p class="text-sm font-bold text-gray-900" style="color: #706f6c;">Not applied</p>
                        <p class="text-xs mt-0.5" style="color: #706f6c;">
                            Historical data is already at current price levels (0 months difference).
                        </p>
                    @else
                        <p class="text-sm font-bold text-gray-900" style="color: #706f6c;">Not applied</p>
                        <p class="text-xs mt-0.5" style="color: #f59e0b;">
                            No historical transaction data in this region to determine a base date.
                        </p>
                    @endif
                </div>
                @if($project->cost_estimate && $project->gross_floor_area)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Cost per m²</p>
                    <p class="text-lg font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($project->cost_estimate / $project->gross_floor_area, 0) }}))}`"></p>
                </div>
                @endif
                @if($project->cost_per_seat)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Cost per Seat</p>
                    <p class="text-lg font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $project->cost_per_seat }}))}`"></p>
                </div>
                @endif
            </div>
        </div>

        {{-- Step 2c: Confidence summary box --}}
        <div class="rounded-lg p-5" style="background: #f9fafb; border: 1px solid #e5e5e5;">
            <p class="text-sm font-semibold text-gray-900 mb-2">Data Confidence Summary</p>
            <p class="text-sm text-gray-700">Based on <strong>{{ $comparableProjectCount }}</strong> comparable historical project(s) in this region.</p>
            <div class="flex flex-wrap gap-4 mt-3 text-sm">
                @if($confidenceSummary['high'] > 0)
                    <span class="px-2 py-1 rounded-full font-medium" style="background: #d1fae5; color: #065f46;">{{ $confidenceSummary['high'] }} High</span>
                @endif
                @if($confidenceSummary['medium'] > 0)
                    <span class="px-2 py-1 rounded-full font-medium" style="background: #dbeafe; color: #1e40af;">{{ $confidenceSummary['medium'] }} Medium</span>
                @endif
                @if($confidenceSummary['low'] > 0)
                    <span class="px-2 py-1 rounded-full font-medium" style="background: #fef3c7; color: #92400e;">{{ $confidenceSummary['low'] }} Low</span>
                @endif
                @if($confidenceSummary['very_low'] > 0)
                    <span class="px-2 py-1 rounded-full font-medium" style="background: #fee2e2; color: #991b1b;">{{ $confidenceSummary['very_low'] }} Very Low</span>
                @endif
                @if($confidenceSummary['no_data'] > 0)
                    <span class="px-2 py-1 rounded-full font-medium" style="background: #f3f4f6; color: #374151;">{{ $confidenceSummary['no_data'] }} No data</span>
                @endif
            </div>
            @if(($confidenceSummary['low'] + $confidenceSummary['very_low'] + $confidenceSummary['no_data']) > 0)
            <p class="text-xs mt-3" style="color: #706f6c;">Rates with Low or Very Low confidence should be reviewed and adjusted by the QS before presenting to project sponsors.</p>
            @endif
        </div>

        <!-- Cost Breakdown Table -->
        <div class="rounded-lg overflow-x-auto" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <table class="w-full text-sm">
                <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900" style="min-width: 80px;">AC Code</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900">Component</th>
                        <th colspan="2" class="px-6 py-4 text-center font-semibold text-gray-900" style="border-right: 1px solid #e5e5e5; color: #10b981;">LOW</th>
                        <th colspan="2" class="px-6 py-4 text-center font-semibold text-gray-900" style="border-right: 1px solid #e5e5e5; color: #3b82f6;">MEDIUM</th>
                        <th colspan="2" class="px-6 py-4 text-center font-semibold text-gray-900" style="border-right: 1px solid #e5e5e5; color: #f59e0b;">HIGH</th>
                        <th colspan="2" class="px-6 py-4 text-center font-semibold text-gray-900" style="color: #ef4444;">HIGH+</th>
                    </tr>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #e5e5e5;">
                        <th colspan="2"></th>
                        <th class="px-3 py-2 text-center text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">PKR/m²</th>
                        <th class="px-3 py-2 text-right text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">Cost PKR</th>
                        <th class="px-3 py-2 text-center text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">PKR/m²</th>
                        <th class="px-3 py-2 text-right text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">Cost PKR</th>
                        <th class="px-3 py-2 text-center text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">PKR/m²</th>
                        <th class="px-3 py-2 text-right text-xs font-medium" style="color: #706f6c; border-right: 1px solid #e5e5e5;">Cost PKR</th>
                        <th class="px-3 py-2 text-center text-xs font-medium" style="color: #706f6c;">PKR/m²</th>
                        <th class="px-3 py-2 text-right text-xs font-medium" style="color: #706f6c;">Cost PKR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($breakdown as $elementId => $item)
                        {{-- AC Code Row (Expandable) --}}
                        <tr style="border-bottom: 1px solid #e5e5e5; cursor: pointer; background: #f9f9f9;" @click="toggleRow({{ $elementId }})">
                            <td class="px-6 py-3 font-semibold text-gray-900">
                                <span x-text="`${expandedRows[{{ $elementId }}] ? '▼' : '▶'}  {{ $item['code'] }}`"></span>
                                <span class="block text-xs font-medium px-2 py-0.5 rounded-full mt-1 w-fit"
                                    style="background: {{ $item['confidence']['bg'] }}; color: {{ $item['confidence']['color'] }};">
                                    {{ $item['confidence']['label'] }} ({{ $item['confidence']['count'] }})
                                </span>
                                @if($item['blend']['was_blended'])
                                    <span class="block text-xs font-medium mt-1 w-fit" style="color: #92400e;"
                                        title="Blended toward the regional mean: based on {{ $item['blend']['n'] }} local comparable project(s), pulled toward the all-region average of PKR {{ number_format($item['blend']['broad_mean'], 0) }}/m² (k = {{ $item['blend']['k'] }}). Click row for details.">
                                        ⚖ Blended (n={{ $item['blend']['n'] }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-700 font-medium">{{ $item['name'] }}</td>
                            <td class="px-3 py-3 text-center text-gray-900" x-text="formatNumber(convert({{ $item['low']['rate'] }}))"></td>
                            <td class="px-3 py-3 text-right font-medium text-gray-900" x-text="formatNumber(convert({{ $item['low']['cost'] }}))"></td>
                            <td class="px-3 py-3 text-center text-gray-900" x-text="formatNumber(convert({{ $item['medium']['rate'] }}))"></td>
                            <td class="px-3 py-3 text-right font-medium text-gray-900" x-text="formatNumber(convert({{ $item['medium']['cost'] }}))"></td>
                            <td class="px-3 py-3 text-center text-gray-900" x-text="formatNumber(convert({{ $item['high']['rate'] }}))"></td>
                            <td class="px-3 py-3 text-right font-medium text-gray-900" x-text="formatNumber(convert({{ $item['high']['cost'] }}))"></td>
                            <td class="px-3 py-3 text-center text-gray-900" x-text="formatNumber(convert({{ $item['high_plus']['rate'] }}))"></td>
                            <td class="px-3 py-3 text-right font-medium text-gray-900" x-text="formatNumber(convert({{ $item['high_plus']['cost'] }}))"></td>
                        </tr>

                        {{-- Stage 1: Blend audit detail (Hidden by default) --}}
                        @if($item['blend']['was_blended'])
                            <tr x-show="expandedRows[{{ $elementId }}]" x-transition style="border-bottom: 1px solid #e5e5e5; background: #fffaf0;">
                                <td colspan="9" class="px-6 py-2 text-xs" style="color: #92400e;">
                                    Medium rate blended: local PKR {{ number_format($item['blend']['pre_blend']['medium'], 0) }}/m²
                                    (based on {{ $item['blend']['n'] }} comparable project(s))
                                    → PKR {{ number_format($item['blend']['medium'], 0) }}/m²
                                    (regional mean PKR {{ number_format($item['blend']['broad_mean'], 0) }}/m², k = {{ $item['blend']['k'] }})
                                </td>
                            </tr>
                        @endif

                        {{-- PD Code Rows (Hidden by default) --}}
                        @if(count($item['pdCodes']) > 0)
                            @foreach($item['pdCodes'] as $pdCode)
                                <tr x-show="expandedRows[{{ $elementId }}]" x-transition style="border-bottom: 1px solid #e5e5e5; background: #fafafa;">
                                    <td class="px-6 py-3 text-gray-600 text-sm pl-12">
                                        <span style="color: #10b981; font-weight: 500;">└ {{ $pdCode['code'] }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 text-sm">{{ $pdCode['name'] }}</td>
                                    <td class="px-3 py-3 text-center text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['low']['rate'] }}))"></td>
                                    <td class="px-3 py-3 text-right text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['low']['cost'] }}))"></td>
                                    <td class="px-3 py-3 text-center text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['medium']['rate'] }}))"></td>
                                    <td class="px-3 py-3 text-right text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['medium']['cost'] }}))"></td>
                                    <td class="px-3 py-3 text-center text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['high']['rate'] }}))"></td>
                                    <td class="px-3 py-3 text-right text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['high']['cost'] }}))"></td>
                                    <td class="px-3 py-3 text-center text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['high_plus']['rate'] }}))"></td>
                                    <td class="px-3 py-3 text-right text-gray-700 text-sm" x-text="formatNumber(convert({{ $pdCode['high_plus']['cost'] }}))"></td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @foreach(['low' => ['LOW', '#10b981'], 'medium' => ['MEDIUM', '#3b82f6'], 'high' => ['HIGH', '#f59e0b'], 'high_plus' => ['HIGH+', '#ef4444']] as $band => $label)
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <p class="text-sm font-bold uppercase mb-4" style="color: {{ $label[1] }};">{{ $label[0] }} SCENARIO</p>

                    <div class="space-y-2 text-sm mb-4" style="border-bottom: 1px solid #e5e5e5; padding-bottom: 4px;">
                        <div class="flex justify-between">
                            <span style="color: #706f6c;">Construction</span>
                            <span class="font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $bandTotals[$band]['construction'] }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: #706f6c;">Externals (10%)</span>
                            <span class="font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $bandTotals[$band]['externals'] }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: #706f6c;">Project Support (15%)</span>
                            <span class="font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $bandTotals[$band]['overhead'] }}))}`"></span>
                        </div>
                    </div>

                    <div class="flex justify-between text-base font-bold" style="color: {{ $label[1] }};">
                        <span>TOTAL</span>
                        <span x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $bandTotals[$band]['subtotal'] }}))}`"></span>
                    </div>
                    @if($project->gross_floor_area)
                    <div class="flex justify-between text-xs mt-2" style="color: #706f6c;">
                        <span>Cost/m²</span>
                        <span x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($bandTotals[$band]['subtotal'] / $project->gross_floor_area, 0) }}))}`"></span>
                    </div>
                    @endif
                    @if($project->seating_capacity)
                    <div class="flex justify-between text-xs mt-1" style="color: #706f6c;">
                        <span>Cost/Seat</span>
                        <span x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($bandTotals[$band]['subtotal'] / $project->seating_capacity, 0) }}))}`"></span>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Priority 5: Dual-Metric Forecast Comparison --}}
        <div class="rounded-lg overflow-hidden" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15), 0 10px 10px -5px rgba(0,0,0,0.1);">

            {{-- Header --}}
            <div class="px-6 py-4 flex items-center justify-between" style="background: #1e1b4b;">
                <p class="text-sm font-bold uppercase tracking-wider text-white">Dual-Metric Forecast Comparison</p>
                {{-- Confidence score --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs text-indigo-300">Confidence</span>
                    <span class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="font-size:16px; color: {{ $i <= $confidenceScore ? '#fbbf24' : '#4338ca' }};">★</span>
                        @endfor
                    </span>
                    <span class="text-xs text-indigo-300">({{ $confidenceScore }}/5)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

                {{-- Area-based column --}}
                <div class="p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-4" style="color: #505b93;">
                        Area-Based &nbsp;·&nbsp; {{ $comparableProjectCount }} comparable project(s)
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Low</span>
                            <span class="font-mono font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($areaLow) }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-700">Mid</span>
                            <span class="font-mono font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($areaMid) }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">High</span>
                            <span class="font-mono font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($areaHigh) }}))}`"></span>
                        </div>
                    </div>
                    @if($avgAreaRatePerM2 > 0)
                    <p class="text-xs mt-4" style="color: #706f6c;">
                        Avg rate: PKR {{ number_format($avgAreaRatePerM2, 0) }} / m²
                    </p>
                    @endif
                    {{-- Stage 2: band-width context --}}
                    <p class="text-xs mt-2" style="color: #706f6c;">
                        Low/High band reflects {{ $areaBand['n'] }} comparable project(s)
                        (uncertainty ×{{ number_format($areaBand['multiplier'], 2) }})@if($areaUsedBroadSpread) — spread estimated from all regions due to limited local data @endif.
                        Wider band = less data = more uncertainty.
                    </p>
                </div>

                {{-- Seating-based column --}}
                <div class="p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-4" style="color: #0f766e;">
                        Seating-Based &nbsp;·&nbsp;
                        @if($seatMid !== null)
                            {{ $seatComparables }} comparable project(s)
                        @elseif(!$project->seating_capacity)
                            No seating capacity set on this project
                        @else
                            No historical data with seating in this region
                        @endif
                    </p>
                    @if($seatMid !== null)
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Low</span>
                            <span class="font-mono font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($seatLow) }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-700">Mid</span>
                            <span class="font-mono font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($seatMid) }}))}`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">High</span>
                            <span class="font-mono font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($seatHigh) }}))}`"></span>
                        </div>
                    </div>
                    @if($avgCostPerSeat)
                    <p class="text-xs mt-4" style="color: #706f6c;">
                        Avg rate: PKR {{ number_format($avgCostPerSeat, 0) }} / seat
                        &nbsp;·&nbsp; {{ $project->seating_capacity }} seats
                    </p>
                    @endif
                    {{-- Stage 2: band-width context --}}
                    <p class="text-xs mt-2" style="color: #706f6c;">
                        Low/High band reflects {{ $seatBand['n'] }} comparable project(s)
                        (uncertainty ×{{ number_format($seatBand['multiplier'], 2) }})@if($seatUsedBroadSpread) — spread estimated from all regions due to limited local data @endif.
                        Wider band = less data = more uncertainty.
                    </p>
                    @else
                    <p class="text-sm text-gray-400 italic mt-2">
                        @if(!$project->seating_capacity)
                            Set a seating capacity on this project to enable seating-based forecasting.
                        @else
                            Record seating capacity on historical projects in this region to enable this metric.
                        @endif
                    </p>
                    @endif
                </div>
            </div>

            {{-- Divergence / Agreement bar --}}
            @if($divergencePct !== null)
            <div class="px-6 py-4 border-t border-gray-200 {{ $divergenceWarning ? '' : '' }}"
                style="background: {{ $divergenceWarning ? '#fef3c7' : '#f0fdf4' }};">
                @if($divergenceWarning)
                    <p class="text-sm font-semibold" style="color: #92400e;">
                        ⚠ Divergence: {{ number_format($divergencePct, 1) }}% — the two methods disagree significantly.
                    </p>
                    <p class="text-xs mt-1" style="color: #92400e;">
                        Review the comparable projects for each metric before presenting figures to sponsors.
                    </p>
                @else
                    <p class="text-sm font-semibold" style="color: #065f46;">
                        ✓ Agreement: within {{ number_format($divergencePct, 1) }}% — forecasts are consistent.
                    </p>
                @endif
            </div>
            @endif

            {{-- Recommended budget --}}
            <div class="px-6 py-5 border-t border-gray-200" style="background: #fafafa;">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Recommended Budget Figure</p>
                        <p class="text-xs" style="color: #706f6c;">
                            @if($divergenceWarning)
                                Metrics diverge — apply QS judgement before use.
                            @else
                                Area-based HIGH band
                                @if($seatMid !== null) (both metrics agree) @endif
                            @endif
                        </p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ round($areaHigh) }}))}`"></p>
                </div>
            </div>

        </div>

        <!-- Stage 3/4: AI Executive Summary (Beta) -->
        <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5;">
            <h3 class="text-lg font-bold text-gray-900 mb-1">AI Executive Summary (Beta)</h3>
            <p class="text-xs mb-4" style="color: #706f6c;">
                AI commentary is advisory only. It cannot create or change any figure on this
                page — every number above comes from deterministic calculation on transaction
                data. Treat it as a second opinion, not a source of truth.
            </p>

            @if(!$aiConfigured)
                <p class="text-sm italic" style="color: #92400e;">
                    AI advisory tools require <code>ANTHROPIC_API_KEY</code> to be configured in <code>.env</code>.
                </p>
            @else
                {{-- Executive Summary --}}
                <div class="mb-4">
                    <button type="button"
                        @click="runAi('summary', '{{ route('project.ai.summary', $project->id) }}')"
                        :disabled="aiLoading.summary"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white transition disabled:opacity-50"
                        style="background: #505b93;">
                        <span x-show="!aiLoading.summary">Generate Summary</span>
                        <span x-show="aiLoading.summary">Thinking…</span>
                    </button>
                </div>

                <div x-show="aiErrors.summary" class="text-sm rounded-lg p-3 mb-3" style="background: #fef3c7; color: #92400e;" x-text="aiErrors.summary"></div>
                <div x-show="aiResults.summary" class="mb-4 rounded-lg p-4 space-y-3" style="background: white; border: 1px solid #e5e5e5;">
                    <p class="text-sm text-gray-900" x-text="aiResults.summary?.summary"></p>
                    <ul class="list-disc list-inside text-sm text-gray-900 space-y-1" x-show="(aiResults.summary?.key_drivers || []).length > 0">
                        <template x-for="(driver, idx) in (aiResults.summary?.key_drivers || [])" :key="idx">
                            <li x-text="driver"></li>
                        </template>
                    </ul>
                    <p x-show="aiResults.summary?.top_caveat" class="text-sm rounded-lg p-2" style="background: #fef3c7; color: #92400e;">
                        <strong>Worth checking:</strong> <span x-text="aiResults.summary?.top_caveat"></span>
                    </p>
                </div>

                {{-- Drill down --}}
                <div x-show="aiResults.summary" style="border-top: 1px solid #e5e5e5;" class="pt-4">
                    <button type="button" @click="showDrillDown = !showDrillDown" class="text-sm font-medium" style="color: #505b93;">
                        <span x-show="!showDrillDown">▶ Show more detail</span>
                        <span x-show="showDrillDown">▼ Hide detail</span>
                    </button>

                    <div x-show="showDrillDown" x-cloak class="mt-4">
                        <div class="flex flex-wrap gap-3 mb-4">
                            <button type="button"
                                @click="runAi('similar', '{{ route('project.ai.similar-projects', $project->id) }}')"
                                :disabled="aiLoading.similar"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white transition disabled:opacity-50"
                                style="background: #505b93;">
                                <span x-show="!aiLoading.similar">Find Similar Projects</span>
                                <span x-show="aiLoading.similar">Thinking…</span>
                            </button>
                            <button type="button"
                                @click="runAi('explain', '{{ route('project.ai.explain', $project->id) }}')"
                                :disabled="aiLoading.explain"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white transition disabled:opacity-50"
                                style="background: #505b93;">
                                <span x-show="!aiLoading.explain">Full Explanation</span>
                                <span x-show="aiLoading.explain">Thinking…</span>
                            </button>
                            <button type="button"
                                @click="runAi('senseCheck', '{{ route('project.ai.sense-check', $project->id) }}')"
                                :disabled="aiLoading.senseCheck"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white transition disabled:opacity-50"
                                style="background: #505b93;">
                                <span x-show="!aiLoading.senseCheck">All Risk Flags</span>
                                <span x-show="aiLoading.senseCheck">Thinking…</span>
                            </button>
                        </div>

                        {{-- Find Similar Projects --}}
                        <div x-show="aiErrors.similar" class="text-sm rounded-lg p-3 mb-3" style="background: #fef3c7; color: #92400e;" x-text="aiErrors.similar"></div>
                        <div x-show="aiResults.similar" class="mb-4 space-y-3">
                            <template x-for="(item, idx) in (aiResults.similar?.selections || [])" :key="idx">
                                <div class="rounded-lg p-3" style="background: white; border: 1px solid #e5e5e5;">
                                    <p class="font-semibold text-sm text-gray-900" x-text="item.project.name"></p>
                                    <p class="text-xs mt-1" style="color: #706f6c;">
                                        <span x-text="item.project.region"></span>
                                        <template x-if="item.project.gross_floor_area_m2"><span> • GFA <span x-text="item.project.gross_floor_area_m2"></span> m²</span></template>
                                        <template x-if="item.project.seating_capacity"><span> • <span x-text="item.project.seating_capacity"></span> seats</span></template>
                                        <template x-if="item.project.total_rate_per_m2"><span> • <span x-text="`${getCurrencySymbol()} ${formatNumber(convert(item.project.total_rate_per_m2))}/m²`"></span></span></template>
                                        <template x-if="item.project.cost_per_seat"><span> • <span x-text="`${getCurrencySymbol()} ${formatNumber(convert(item.project.cost_per_seat))}/seat`"></span></span></template>
                                    </p>
                                    <p class="text-sm mt-2 text-gray-900" x-text="item.reason"></p>
                                </div>
                            </template>
                            <p x-show="(aiResults.similar?.selections || []).length === 0" class="text-sm italic" style="color: #706f6c;">No similar projects were identified.</p>
                        </div>

                        {{-- Full Explanation --}}
                        <div x-show="aiErrors.explain" class="text-sm rounded-lg p-3 mb-3" style="background: #fef3c7; color: #92400e;" x-text="aiErrors.explain"></div>
                        <div x-show="aiResults.explain" class="mb-4 rounded-lg p-3" style="background: white; border: 1px solid #e5e5e5;">
                            <p class="text-sm text-gray-900" style="white-space: pre-wrap;" x-text="aiResults.explain?.explanation"></p>
                        </div>

                        {{-- All Risk Flags --}}
                        <div x-show="aiErrors.senseCheck" class="text-sm rounded-lg p-3 mb-3" style="background: #fef3c7; color: #92400e;" x-text="aiErrors.senseCheck"></div>
                        <div x-show="aiResults.senseCheck" class="mb-4 rounded-lg p-3 space-y-2" style="background: white; border: 1px solid #e5e5e5;">
                            <p class="text-sm text-gray-900" x-text="aiResults.senseCheck?.summary"></p>
                            <template x-for="(flag, idx) in (aiResults.senseCheck?.flags || [])" :key="idx">
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="text-xs font-semibold uppercase px-2 py-0.5 rounded"
                                        :style="flag.severity === 'high' ? 'background:#fee2e2;color:#991b1b;' : (flag.severity === 'medium' ? 'background:#fef3c7;color:#92400e;' : 'background:#f3f4f6;color:#374151;')"
                                        x-text="flag.severity"></span>
                                    <span class="text-gray-900" x-text="flag.message"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 pt-8">
            <a href="{{ route('admin.projects.index') }}" class="px-6 py-2 rounded-lg text-sm font-medium transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                Back to Projects
            </a>
            <a href="{{ route('estimator.show', $project->id) }}" class="px-6 py-2 rounded-lg text-sm font-medium text-white transition" style="background: #505b93;">
                Edit Estimate
            </a>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        function reportViewer() {
            return {
                currency: 'PKR',
                exchangeRates: {},
                expandedRows: {},

                // Stage 3/4: AI Advisory
                aiLoading: { summary: false, similar: false, explain: false, senseCheck: false },
                aiResults: { summary: null, similar: null, explain: null, senseCheck: null },
                aiErrors: { summary: null, similar: null, explain: null, senseCheck: null },
                showDrillDown: false,

                convert(pkriAmount) {
                    if (this.currency === 'PKR') return pkriAmount;
                    const rate = this.exchangeRates[this.currency.toUpperCase()] ?? 1;
                    return pkriAmount / rate;
                },

                getCurrencySymbol() {
                    const symbols = { 'PKR': 'PKR', 'USD': 'US$', 'GBP': '£', 'EUR': '€' };
                    return symbols[this.currency] || this.currency;
                },

                formatNumber(amount) {
                    const decimals = this.currency === 'PKR' ? 0 : 2;
                    return amount.toLocaleString('en-US', { maximumFractionDigits: decimals, minimumFractionDigits: 0 });
                },

                toggleRow(elementId) {
                    this.expandedRows[elementId] = !this.expandedRows[elementId];
                },

                initExpandState() {
                    this.expandedRows = {};
                },

                // Stage 3: AI Advisory — calls one of the project.ai.* endpoints.
                // These never return cost figures: only selections from a
                // candidate list, or explanatory/advisory text.
                async runAi(tool, url) {
                    this.aiLoading[tool] = true;
                    this.aiErrors[tool] = null;
                    this.aiResults[tool] = null;

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();

                        if (!res.ok || data.error) {
                            this.aiErrors[tool] = data.error || 'Request failed.';
                        } else {
                            this.aiResults[tool] = data;
                        }
                    } catch (e) {
                        this.aiErrors[tool] = 'Network error: ' + e.message;
                    } finally {
                        this.aiLoading[tool] = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
