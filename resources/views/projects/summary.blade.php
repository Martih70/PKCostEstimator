@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="projectSummary()">
    <!-- Sidebar: Project List -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow sticky top-24">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Projects</h3>
            </div>
            <div class="divide-y">
                @foreach($allProjects as $p)
                    <a href="{{ route('projects.summary', $p) }}" class="block px-4 py-3 text-sm {{ $p->id === $project->id ? 'bg-blue-50 text-blue-600 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ $p->project_number }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $project->project_number }}</h1>
            <p class="text-gray-600 mt-2">Region: {{ $project->region?->name ?? 'Unknown' }}</p>
            @if($project->gfa)
                <p class="text-gray-600">GFA: {{ number_format($project->gfa, 2) }} m²</p>
            @endif
        </div>

        <!-- Controls -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select x-model="currency" id="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="PKR">PKR</option>
                        @foreach($exchangeRates as $code => $rate)
                            @if($code !== 'PKR')
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="contingency" class="block text-sm font-medium text-gray-700 mb-1">Contingency (%)</label>
                    <input type="number" x-model.number="contingencyPct" id="contingency" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-700">Grand Total</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="`${currency} ${convertCurrency(grandTotal()).toLocaleString('en-US', { maximumFractionDigits: 0 })}`"></p>
                </div>
            </div>
        </div>

        <!-- Cost Summary Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Element Costs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Code</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Element</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900" x-text="`Actual Cost (${currency})`"></th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">PKR/m²</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Regional Low</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Regional Med</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Regional High</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($elements as $element)
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-600">{{ $element->code }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $element->name }}</td>
                                <td class="px-6 py-4 text-right font-mono text-gray-900" x-text="convertCurrency(elementCostWithContingency({{ $element->id }})).toLocaleString('en-US', { maximumFractionDigits: 0 })"></td>
                                <td class="px-6 py-4 text-right font-mono text-gray-600" x-text="elementRate({{ $element->id }}).toLocaleString('en-US', { maximumFractionDigits: 0 })"></td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $rates = $regionalRates->get($element->id);
                                    @endphp
                                    @if($rates)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 font-mono text-xs">
                                            {{ number_format($rates['low'], 0) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($rates)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 font-mono text-xs">
                                            {{ number_format($rates['medium'], 0) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($rates)
                                        <span class="inline-block px-2 py-1 bg-orange-100 text-orange-800 font-mono text-xs">
                                            {{ number_format($rates['high'], 0) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold">
                        <tr>
                            <td colspan="2" class="px-6 py-4">Grand Total (with contingency)</td>
                            <td class="px-6 py-4 text-right font-mono text-gray-900" x-text="convertCurrency(grandTotal()).toLocaleString('en-US', { maximumFractionDigits: 0 })"></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function projectSummary() {
        return {
            currency: 'PKR',
            contingencyPct: 10,
            elementCosts: @json($elementCosts->toArray()),
            gfa: {{ $project->gfa ?? 0 }},
            exchangeRates: @json($exchangeRates->mapWithKeys(fn($r) => [$r->currency_code => (float)$r->rate])->toArray()),

            elementCostWithContingency(elementId) {
                const cost = this.elementCosts[elementId] ?? 0;
                const contingency = this.contingencyPct / 100;
                return cost * (1 + contingency);
            },

            elementRate(elementId) {
                if (!this.gfa) return 0;
                const cost = this.elementCosts[elementId] ?? 0;
                return cost / this.gfa;
            },

            grandTotal() {
                let total = 0;
                Object.values(this.elementCosts).forEach(cost => {
                    total += cost;
                });
                const contingency = this.contingencyPct / 100;
                return total * (1 + contingency);
            },

            convertCurrency(amount) {
                const rate = this.exchangeRates[this.currency] ?? 1;
                return amount * rate;
            },

            init() {
                this.fetchLiveRates();
            },

            fetchLiveRates() {
                fetch('/api/exchange-rates')
                    .then(res => res.json())
                    .then(data => {
                        if (data.rates) {
                            data.rates.PKR = 1.0;
                            this.exchangeRates = data.rates;
                        }
                    })
                    .catch(err => console.log('Exchange rates API unavailable, using cached rates'));
            }
        };
    }
</script>
@endpush
@endsection
