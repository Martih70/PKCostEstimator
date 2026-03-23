<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Transactions</h2>
                <p class="text-xs text-gray-400 mt-0.5">Historical ledger data</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.transactions.import') }}"
                   class="btn-primary inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition"
                   style="border-color: #c4c8dc;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('admin.transactions.create') }}"
                   class="btn-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white transition"
                   style="background: linear-gradient(135deg, #505b93, #3d4269);">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Transaction
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">

        {{-- Search --}}
        <form method="GET" class="flex gap-3">
            <input type="search" name="search" placeholder="Search transactions…" value="{{ request('search') }}"
                   class="flex-1 rounded-xl bg-white px-4 py-2.5 text-sm text-gray-900" />
            <button type="submit"
                    class="btn-primary rounded-xl px-6 py-2.5 text-sm font-semibold text-white"
                    style="background: linear-gradient(135deg, #505b93, #3d4269);">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.transactions.index') }}"
                   class="rounded-xl border px-4 py-2.5 text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition"
                   style="border-color: #c4c8dc;">
                    Clear
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="table-card">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background: #f8f9fc;">
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">PD Code</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Project</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Description</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded" style="background: #f0f0f8; color: #505b93;">
                                    {{ $transaction->pdCode->code }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $transaction->project->name }}</td>
                            <td class="px-5 py-3.5 text-gray-500 max-w-xs truncate">{{ $transaction->item_description }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-900">PKR {{ number_format($transaction->amount, 0) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.transactions.edit', $transaction->id) }}"
                                       class="text-sm font-medium transition" style="color: #505b93;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction->id) }}"
                                          onsubmit="return confirm('Delete this transaction?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-400 hover:text-red-600 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $transactions->links() }}
        </div>

    </div>
</x-app-layout>
