<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Project Report (Historical)</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.projects.historical') }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="historicalReportViewer()" x-init='exchangeRates = @json($exchangeRates); initExpandState();'>

        <!-- Currency Selector -->
        <div class="mb-6 flex items-center gap-4">
            <label class="text-sm font-medium text-gray-900">Currency:</label>
            <select x-model="currency" class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white; color: #1b1b18;">
                <option value="PKR">PKR (Pakistan Rupees)</option>
                <option value="USD">USD (US Dollar)</option>
                <option value="GBP">GBP (British Pound)</option>
                <option value="EUR">EUR (Euro)</option>
            </select>
        </div>

        <!-- Project Header -->
        <div class="rounded-lg p-8 mb-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
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
            </div>
        </div>

        <!-- Cost Breakdown Table -->
        <div class="rounded-lg overflow-x-auto mb-6" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <table class="w-full text-sm">
                <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900" style="min-width: 80px;">AC Code</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900">Component</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900">Actual Amount</th>
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
                            <td class="px-6 py-3 text-right font-medium text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $item['amount'] }}))}`"></td>
                        </tr>

                        {{-- PD Code Rows (Hidden by default) --}}
                        @if(count($item['pdCodes']) > 0)
                            @foreach($item['pdCodes'] as $pdCodeIndex => $pdCode)
                                <tr x-show="expandedRows[{{ $elementId }}]" x-transition style="border-bottom: 1px solid #e5e5e5; background: #fafafa; cursor: pointer;" @click="togglePdCode('{{ $elementId }}-{{ $pdCodeIndex }}')">
                                    <td class="px-6 py-3 text-gray-600 text-sm pl-12">
                                        <span style="color: #10b981; font-weight: 500;" x-text="`${expandedPdCodes['{{ $elementId }}-{{ $pdCodeIndex }}'] ? '▼' : '▶'} {{ $pdCode['code'] }}`"></span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 text-sm">{{ $pdCode['name'] }}</td>
                                    <td class="px-6 py-3 text-right text-gray-700 text-sm" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $pdCode['amount'] }}))}`"></td>
                                </tr>

                                {{-- Transaction Rows (Hidden by default) --}}
                                @if(count($pdCode['transactions']) > 0)
                                    @foreach($pdCode['transactions'] as $transaction)
                                        <tr x-show="expandedRows[{{ $elementId }}] && expandedPdCodes['{{ $elementId }}-{{ $pdCodeIndex }}']" x-transition style="border-bottom: 1px solid #e5e5e5; background: #ffffff;">
                                            <td class="px-6 py-2 text-gray-500 text-xs pl-20">
                                                <span>• {{ $transaction['date'] }}</span>
                                            </td>
                                            <td class="px-6 py-2 text-gray-500 text-xs">{{ $transaction['description'] }}</td>
                                            <td class="px-6 py-2 text-right text-gray-600 text-xs" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $transaction['amount'] }}))}`"></td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <div class="flex justify-between items-center">
                <p class="text-sm font-bold uppercase" style="color: #706f6c;">TOTAL PROJECT AMOUNT</p>
                <p class="text-2xl font-bold text-gray-900" x-text="`${getCurrencySymbol()} ${formatNumber(convert({{ $totalAmount }}))}`"></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 pt-8">
            <a href="{{ route('admin.projects.historical') }}" class="px-6 py-2 rounded-lg text-sm font-medium transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                Back to Projects
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
        function historicalReportViewer() {
            return {
                currency: 'PKR',
                exchangeRates: {},
                expandedRows: {},
                expandedPdCodes: {},

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

                togglePdCode(pdCodeKey) {
                    this.expandedPdCodes[pdCodeKey] = !this.expandedPdCodes[pdCodeKey];
                },

                initExpandState() {
                    this.expandedRows = {};
                    this.expandedPdCodes = {};
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
