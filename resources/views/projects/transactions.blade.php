@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar: Project List + Element Filter -->
    <div class="lg:col-span-1">
        <!-- Projects -->
        <div class="bg-white rounded-lg shadow sticky top-24 mb-4">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 text-sm">Projects</h3>
            </div>
            <div class="divide-y max-h-64 overflow-y-auto">
                @foreach($allProjects as $p)
                    <a href="{{ route('projects.transactions', $p) }}" class="block px-4 py-3 text-sm {{ $p->id === $project->id ? 'bg-blue-50 text-blue-600 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ $p->project_number }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Element Filter -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 text-sm">Elements</h3>
            </div>
            <div class="divide-y">
                <a href="{{ route('projects.transactions', $project) }}" class="block px-4 py-3 text-sm {{ !$filterElement ? 'bg-blue-50 text-blue-600 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    All
                </a>
                @foreach($elements as $element)
                    @if(in_array($element->id, $activeElementIds))
                        <a href="{{ route('projects.transactions', [$project, 'element' => $element->id]) }}" class="block px-4 py-3 text-sm {{ $filterElement == $element->id ? 'bg-blue-50 text-blue-600 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                            {{ $element->code }}
                        </a>
                    @endif
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
        </div>

        <!-- Transactions by Element -->
        @forelse($grouped as $elementId => $transactions)
            @php
                $element = $elements->firstWhere('id', $elementId);
                $subtotal = $subtotals[$elementId] ?? 0;
            @endphp
            @if($element)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-100 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $element->code }} — {{ $element->name }}
                    </h2>
                    <span class="font-mono font-semibold text-gray-900">
                        PKR {{ number_format($subtotal, 0) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Date</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">PD Code</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Description</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $transaction->pdCode->code }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $transaction->item_description }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-900">
                                        PKR {{ number_format($transaction->amount, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600">No transactions found for this project.</p>
            </div>
        @endforelse

        <!-- Grand Total Footer -->
        <div class="bg-gray-900 text-white rounded-lg px-6 py-4 flex justify-between items-center font-semibold">
            <span>Grand Total</span>
            <span class="font-mono text-lg">PKR {{ number_format($grandTotal, 0) }}</span>
        </div>
    </div>
</div>
@endsection
