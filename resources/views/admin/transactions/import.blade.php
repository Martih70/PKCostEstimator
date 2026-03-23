<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transactions.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Import Transactions</h2>
                <p class="text-xs text-gray-400 mt-0.5">Upload a CSV file to bulk import historical transaction data</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.transactions.process-import') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-static p-6 space-y-6">

                {{-- CSV format info --}}
                <div class="rounded-xl p-4" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: #1d4ed8;">Expected CSV Format</p>
                    <p class="text-xs" style="color: #1e40af; line-height: 1.6;">
                        Date (DD/MM/YYYY) &nbsp;·&nbsp; PD Code &nbsp;·&nbsp; Area Code &nbsp;·&nbsp; AC Code MH &nbsp;·&nbsp; Project Nr &nbsp;·&nbsp; Item Description &nbsp;·&nbsp; Amount
                    </p>
                </div>

                {{-- Drop zone --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">CSV File</label>

                    <div id="drop-zone"
                         class="rounded-xl p-10 text-center cursor-pointer transition"
                         style="border: 2px dashed #d1d5db; background: #fafafa;"
                         onclick="document.getElementById('file-input').click()"
                         ondragover="event.preventDefault(); this.style.borderColor='#505b93'; this.style.background='#f0f0f8';"
                         ondragleave="this.style.borderColor='#d1d5db'; this.style.background='#fafafa';"
                         ondrop="handleDrop(event)">
                        <svg class="h-10 w-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-600">Click to browse or drag & drop</p>
                        <p class="text-xs text-gray-400 mt-1">CSV files up to 10MB</p>
                        <input type="file" id="file-input" name="file" accept=".csv,.txt" required class="hidden" />
                    </div>

                    <div id="file-name" class="hidden mt-3 flex items-center gap-2 rounded-lg px-3 py-2.5" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #16a34a;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold" style="color: #15803d;" id="file-label"></span>
                    </div>

                    @error('file')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="btn-primary flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white"
                            style="background: linear-gradient(135deg, #505b93, #3d4269);">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import
                    </button>
                    <a href="{{ route('admin.transactions.index') }}"
                       class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', e => showFile(e.target.files[0]?.name));

        function handleDrop(e) {
            e.preventDefault();
            const file = e.dataTransfer.files[0];
            if (file) {
                fileInput.files = e.dataTransfer.files;
                showFile(file.name);
            }
            document.getElementById('drop-zone').style.borderColor = '#d1d5db';
            document.getElementById('drop-zone').style.background = '#fafafa';
        }

        function showFile(name) {
            if (!name) return;
            document.getElementById('file-label').textContent = name;
            document.getElementById('file-name').classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-layout>
