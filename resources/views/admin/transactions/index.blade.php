<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Transactions</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.transactions.import') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition" style="background: white; border: 1px solid #e5e5e5; color: #505b93;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('admin.transactions.create') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition" style="background: #505b93;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Transaction
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search -->
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" placeholder="Search transactions..." value="{{ request('search') }}" class="flex-1 rounded-lg bg-white px-4 py-2 text-sm" style="border: 1px solid #e5e5e5; focus:border: 1px solid #505b93;">
            <button type="submit" class="rounded-lg px-6 py-2 text-sm font-medium text-white transition" style="background: #505b93;">
                Search
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg" style="border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
            <table class="w-full text-sm">
                <thead class="border-b" style="background: #fafafa; border-color: #e5e5e5;">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">PD Code</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Project</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-900">Description</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-900">Amount</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-mono text-gray-900">{{ $transaction->pdCode->code }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $transaction->project->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $transaction->item_description }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">PKR {{ number_format($transaction->amount, 0) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.transactions.edit', $transaction->id) }}" style="color: #505b93; transition: all 0.2s;">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction->id) }}" style="display:inline" onsubmit="return confirm('Delete this transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color: #d97706; transition: all 0.2s;">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
