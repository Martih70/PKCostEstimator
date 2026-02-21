<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Import Transactions</h2>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('admin.transactions.process-import') }}" enctype="multipart/form-data" class="rounded-lg border border-gray-200 bg-white p-8 space-y-6">
            @csrf

            <!-- Info -->
            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                <p class="text-sm text-blue-900">
                    <strong>CSV Format:</strong> Date (DD/MM/YYYY), PD Code, Area Code, AC Code MH, Project Nr, Item Description, Amount
                </p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 uppercase mb-4">Select CSV File</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer" onclick="document.getElementById('file-input').click()">
                    <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    <p class="text-sm font-medium text-gray-700 mb-1">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-600">CSV file (up to 10MB)</p>
                    <input type="file" id="file-input" name="file" accept=".csv,.txt" required style="display:none" />
                </div>
                <div id="file-name" class="mt-3 text-sm text-gray-700 hidden">
                    Selected file: <span id="file-label" class="font-semibold"></span>
                </div>
                @error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 rounded-lg bg-slate-700 px-6 py-2 text-center font-medium text-white hover:bg-slate-600 transition">
                    Import
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="flex-1 rounded-lg border border-gray-300 px-6 py-2 text-center font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', (e) => {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('file-label').textContent = fileName;
                document.getElementById('file-name').classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
