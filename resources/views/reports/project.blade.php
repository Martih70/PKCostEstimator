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
            </div>
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
                </div>
            @endforeach
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
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
