<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Add Transaction</h2>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('admin.transactions.store') }}" class="rounded-lg border border-gray-200 bg-white p-8 space-y-6">
            @csrf

            <!-- Date -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-2">Date</label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700" />
                @error('transaction_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- PD Code -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-2">PD Code</label>
                <select name="pd_code_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700">
                    <option value="">-- Select PD Code --</option>
                    @foreach($pdCodes as $code)
                        <option value="{{ $code->id }}" {{ old('pd_code_id') == $code->id ? 'selected' : '' }}>
                            {{ $code->code }} — {{ $code->description }}
                        </option>
                    @endforeach
                </select>
                @error('pd_code_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Project -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-2">Project</label>
                <select name="project_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700">
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-2">Description</label>
                <input type="text" name="item_description" value="{{ old('item_description') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700" />
                @error('item_description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-2">Amount (PKR)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700" />
                @error('amount')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 rounded-lg bg-slate-700 px-6 py-2 text-center font-medium text-white hover:bg-slate-600 transition">
                    Add Transaction
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="flex-1 rounded-lg border border-gray-300 px-6 py-2 text-center font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
