<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Regional Rates</h2>
            <form method="POST" action="{{ route('admin.rates.recalculate') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-600 transition">
                    Recalculate Rates
                </button>
            </form>
        </div>
    </x-slot>

    {{-- Regional Adjustment Factors panel --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg px-4 py-3 text-sm font-medium" style="background: #d1fae5; color: #065f46;">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg mb-8" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
        <div class="px-6 py-4 border-b" style="background: #fafafa; border-color: #e5e5e5;">
            <h3 class="font-semibold text-gray-900">Regional Adjustment Factors</h3>
            <p class="text-xs mt-1" style="color: #706f6c;">
                A multiplier applied to all elemental rates for each region before costs are calculated.
                1.00 = base rate (Islamabad/Rawalpindi). 1.15 = rates 15% above base. 0.85 = 15% below base.
                Adjust with care — this affects every forecast in that region.
            </p>
        </div>
        <table class="w-full text-sm">
            <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900">Current Factor</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900">Effect</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900">Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($regions as $region)
                <tr style="border-bottom: 1px solid #e5e5e5;">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $region->name }}</td>
                    <td class="px-6 py-4 text-center font-mono text-gray-900">{{ number_format($region->cost_adjustment_factor, 4) }}</td>
                    <td class="px-6 py-4 text-center text-sm" style="color: #706f6c;">
                        @php $f = (float) $region->cost_adjustment_factor; @endphp
                        @if($f == 1.0)
                            Base rate
                        @elseif($f > 1.0)
                            +{{ number_format(($f - 1) * 100, 1) }}% above base
                        @else
                            {{ number_format(($f - 1) * 100, 1) }}% below base
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('admin.regions.update', $region->id) }}" class="inline-flex gap-2 items-center">
                            @csrf
                            @method('PUT')
                            <input type="number" name="cost_adjustment_factor"
                                value="{{ $region->cost_adjustment_factor }}"
                                step="0.0100" min="0.1" max="5.0"
                                class="w-24 rounded px-2 py-1 text-right font-mono text-sm"
                                style="border: 1px solid #e5e5e5; background: white;" />
                            <button type="submit" class="text-sm font-medium px-3 py-1 rounded-lg text-white transition"
                                style="background: #505b93;">Save</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900">Element</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-900">Low (PKR/m²)</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-900">Medium (PKR/m²)</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-900">High (PKR/m²)</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-900">High+ (PKR/m²)</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900">Projects</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($regionalRates as $rate)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $rate->region->name }}</td>
                        <td class="px-6 py-4 text-gray-900">{{ $rate->estimatingElement->code }} — {{ $rate->estimatingElement->name }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 font-mono">{{ number_format($rate->low_rate, 0) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 font-mono">{{ number_format($rate->medium_rate, 0) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 font-mono">{{ number_format($rate->high_rate, 0) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 font-mono">{{ number_format($rate->high_plus_rate, 0) }}</td>
                        <td class="px-6 py-4 text-center text-gray-600 text-xs bg-gray-50">{{ $rate->project_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
