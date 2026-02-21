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
