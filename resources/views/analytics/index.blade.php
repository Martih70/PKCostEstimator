<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Analytics & Rate Library</h2>
            @if(auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('admin.rates.recalculate') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-600 transition">
                        Recalculate Rates
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Regional Rate Library -->
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Regional Rate Library</h3>
            <div class="overflow-x-auto rounded-lg" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <table class="w-full text-sm">
                    <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Region</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-900">Element</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Low</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">Medium</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">High</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-900">High+</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-900">Data Points</th>
                        </tr>
                    </thead>
                    <tbody style="border-color: #e5e5e5; border-top: 1px solid #e5e5e5;">
                        @foreach($regionalRates as $rate)
                            <tr style="border-bottom: 1px solid #e5e5e5;">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $rate->region->name }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $rate->estimatingElement->code }}</td>
                                <td class="px-6 py-4 text-right font-mono font-semibold" style="color: #10b981;">{{ number_format($rate->low_rate, 0) }}</td>
                                <td class="px-6 py-4 text-right font-mono font-semibold" style="color: #505b93;">{{ number_format($rate->medium_rate, 0) }}</td>
                                <td class="px-6 py-4 text-right font-mono font-semibold" style="color: #f59e0b;">{{ number_format($rate->high_rate, 0) }}</td>
                                <td class="px-6 py-4 text-right font-mono font-semibold" style="color: #ef4444;">{{ number_format($rate->high_plus_rate, 0) }}</td>
                                <td class="px-6 py-4 text-center text-xs" style="background: #f5f5f5; color: #706f6c;">{{ $rate->project_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <p class="text-sm font-medium" style="color: #706f6c;">TOTAL REGIONS</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $regions->count() }}</p>
            </div>
            <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <p class="text-sm font-medium" style="color: #706f6c;">ESTIMATING ELEMENTS</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $elements->count() }}</p>
            </div>
            <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                <p class="text-sm font-medium" style="color: #706f6c;">RATE DATA POINTS</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $regionalRates->count() }}</p>
            </div>
        </div>

        <!-- Legend -->
        <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <h3 class="font-semibold text-gray-900 mb-4">Cost Band Legend</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded" style="background: #10b981; border: 1px solid #059669;"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Low</p>
                        <p class="text-xs" style="color: #706f6c;">Minimum observed rate</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded" style="background: #505b93; border: 1px solid #3d4269;"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Medium</p>
                        <p class="text-xs" style="color: #706f6c;">Average rate</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded" style="background: #f59e0b; border: 1px solid #d97706;"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">High</p>
                        <p class="text-xs" style="color: #706f6c;">Maximum observed rate</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded" style="background: #ef4444; border: 1px solid #dc2626;"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">High+</p>
                        <p class="text-xs" style="color: #706f6c;">High + 15-20% buffer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
