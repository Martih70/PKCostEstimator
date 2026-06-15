<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Inflation Factor Management</h2>
    </x-slot>

    <div class="space-y-8 max-w-3xl">

        @if(session('success'))
            <div class="rounded-lg px-4 py-3 text-sm font-medium" style="background: #d1fae5; color: #065f46;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Current rate --}}
        <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #706f6c;">Current Inflation Factor</p>
            @if($current)
                <p class="text-3xl font-bold text-gray-900">{{ number_format($current->monthly_rate_percent, 3) }}% <span class="text-lg font-normal text-gray-500">per month</span></p>
                <p class="text-sm mt-2 text-gray-600">≈ {{ number_format($current->monthly_rate_percent * 12, 1) }}% per year</p>
                @if($current->notes)
                    <p class="text-sm mt-3 italic" style="color: #706f6c;">{{ $current->notes }}</p>
                @endif
                <p class="text-xs mt-2" style="color: #706f6c;">Set {{ $current->created_at->format('d M Y') }} by {{ $current->updatedBy?->name ?? 'system' }}</p>
            @else
                <p class="text-gray-500">No inflation factor set.</p>
            @endif
        </div>

        {{-- Set new rate --}}
        <div class="rounded-lg p-6" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
            <p class="text-sm font-semibold text-gray-900 mb-1">Set a New Rate</p>
            <p class="text-xs mb-4" style="color: #706f6c;">
                Each entry is saved to history — the most recent record is always the active rate.
                A note explaining your reasoning is required.
            </p>
            <form method="POST" action="{{ route('admin.escalation.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Monthly Rate (%)</label>
                    <input type="number" name="monthly_rate_percent" step="0.001" min="0" max="10"
                        value="{{ old('monthly_rate_percent', $current?->monthly_rate_percent) }}"
                        class="w-40 rounded-lg px-3 py-2 text-sm"
                        style="border: 1px solid #e5e5e5; background: white;">
                    <p class="text-xs mt-1" style="color: #706f6c;">Typical range: 0.3%–1.0% per month. Default is 0.500%.</p>
                    @error('monthly_rate_percent')
                        <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Notes (required)</label>
                    <textarea name="notes" rows="3" maxlength="500"
                        class="w-full rounded-lg px-3 py-2 text-sm"
                        style="border: 1px solid #e5e5e5; background: white;"
                        placeholder="e.g. Adjusted based on SBP inflation data for Q1 2026. Steel prices up ~8% YoY.">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium text-white transition"
                    style="background: #505b93;">
                    Save New Rate
                </button>
            </form>
        </div>

        {{-- History --}}
        @if($history->count() > 1)
        <div class="rounded-lg overflow-hidden" style="border: 1px solid #e5e5e5;">
            <div class="px-6 py-4 border-b" style="background: #fafafa; border-color: #e5e5e5;">
                <p class="text-sm font-semibold text-gray-900">Rate History</p>
            </div>
            <table class="w-full text-sm">
                <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Date Set</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-900">Rate/Month</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Notes</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Set By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $record)
                    <tr style="border-bottom: 1px solid #e5e5e5; {{ $loop->first ? 'background: #f0fdf4;' : '' }}">
                        <td class="px-6 py-3 text-gray-700">
                            {{ $record->created_at->format('d M Y') }}
                            @if($loop->first)
                                <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center font-mono text-gray-900">{{ number_format($record->monthly_rate_percent, 3) }}%</td>
                        <td class="px-6 py-3 text-gray-600">{{ $record->notes ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $record->updatedBy?->name ?? 'system' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</x-app-layout>
