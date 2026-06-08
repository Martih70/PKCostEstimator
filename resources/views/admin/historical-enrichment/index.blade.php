<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Historical Project Enrichment</h2>
    </x-slot>

    <div x-data="enrichment()" x-init="init()">

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SECTION A — Project Lookup & Filter                    --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="rounded-lg mb-6" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">

            {{-- Filter bar --}}
            <div class="px-6 py-4 border-b grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6" style="background: #fafafa; border-color: #e5e5e5;">

                <input type="text" x-model="filters.search" placeholder="Search name or number…"
                    class="col-span-2 md:col-span-1 rounded-lg px-3 py-2 text-sm"
                    style="border: 1px solid #e5e5e5; background: white;" />

                <select x-model="filters.region" class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>

                <select x-model="filters.enriched" class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;">
                    <option value="">All Statuses</option>
                    <option value="complete">Complete</option>
                    <option value="partial">Partial</option>
                    <option value="missing">Missing</option>
                </select>

                <select x-model="filters.pdCode" class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;">
                    <option value="">All PD Codes</option>
                    @foreach($pdCodes as $code)
                        <option value="{{ $code }}">{{ $code }}</option>
                    @endforeach
                </select>

                <input type="number" x-model="filters.minValue" placeholder="Min value (PKR)"
                    class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;" />

                <input type="number" x-model="filters.maxValue" placeholder="Max value (PKR)"
                    class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;" />
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Project Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Nr</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Region</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-900">Total Value (PKR)</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-900">GFA (m²)</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-900">Seats</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-900">Rate/m²</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-900">Cost/Seat</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-900">Enriched?</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-900">Excluded?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in filteredProjects" :key="p.id">
                            <tr @click="selectProject(p.id)"
                                :style="selectedId === p.id ? 'background: #ede9fe; cursor: pointer;' : 'cursor: pointer;'"
                                style="border-bottom: 1px solid #e5e5e5;"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900" x-text="p.name"></td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs" x-text="p.project_nr ?? '—'"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="p.region_name ?? '—'"></td>
                                <td class="px-4 py-3 text-right text-gray-900 font-mono" x-text="p.total_value ? formatPKR(p.total_value) : '—'"></td>
                                <td class="px-4 py-3 text-right text-gray-600" x-text="p.gross_floor_area ? Number(p.gross_floor_area).toLocaleString() : '—'"></td>
                                <td class="px-4 py-3 text-right text-gray-600" x-text="p.seating_capacity ?? '—'"></td>
                                <td class="px-4 py-3 text-right text-gray-600" x-text="p.gross_floor_area && p.total_value ? formatPKR(p.total_value / p.gross_floor_area) : '—'"></td>
                                <td class="px-4 py-3 text-right text-gray-600" x-text="p.seating_capacity && p.total_value ? formatPKR(p.total_value / p.seating_capacity) : '—'"></td>
                                <td class="px-4 py-3 text-center">
                                    <span x-html="enrichedBadge(p)"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span x-show="p.exclude_from_estimator"
                                        class="inline-block px-2 py-0.5 rounded text-xs font-semibold"
                                        style="background: #fee2e2; color: #ef4444;">Excluded</span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredProjects.length === 0">
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">No projects match the current filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-2 text-xs border-t" style="color: #706f6c; border-color: #e5e5e5; background: #fafafa;">
                Showing <span x-text="filteredProjects.length"></span> of <span x-text="allProjects.length"></span> historical projects
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SECTION B — Project Detail & Enrichment Panel          --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="selectedId" x-cloak>

            {{-- Loading spinner --}}
            <div x-show="loading" class="rounded-lg p-12 text-center" style="border: 1px solid #e5e5e5;">
                <p class="text-gray-500 text-sm">Loading project…</p>
            </div>

            <div x-show="!loading && detail" class="space-y-6">

                {{-- B1 — Project Header --}}
                <div class="rounded-lg p-6" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Project Name</p>
                            <p class="font-bold text-gray-900 text-lg" x-text="detail?.project?.name"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Project Number</p>
                            <p class="font-mono text-gray-900" x-text="detail?.project?.project_nr ?? '—'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Region</p>
                            <p class="text-gray-900" x-text="detail?.project?.region ?? '—'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Type</p>
                            <p class="text-gray-900">Historical</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Total Project Value</p>
                            <p class="font-bold text-2xl" style="color: #505b93;" x-text="detail ? 'PKR ' + formatPKR(detail.total_value) : '—'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Transaction Records</p>
                            <p class="text-gray-900" x-text="detail?.tx_count"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Date Range</p>
                            <p class="text-gray-900 text-sm" x-text="detail ? (detail.earliest_date ?? '—') + ' → ' + (detail.latest_date ?? '—') : '—'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #706f6c;">Implied Duration</p>
                            <p class="text-gray-900" x-text="detail?.duration_months ? detail.duration_months + ' months' : '—'"></p>
                        </div>
                    </div>
                </div>

                {{-- B2 — Transaction Breakdown --}}
                <div class="rounded-lg overflow-hidden" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="px-6 py-4 border-b" style="background: #fafafa; border-color: #e5e5e5;">
                        <p class="font-semibold text-gray-900">Transaction Breakdown by PD Code</p>
                        <p class="text-xs mt-1" style="color: #706f6c;">Click a row to expand individual line items.</p>
                    </div>
                    <table class="w-full text-sm">
                        <thead style="background: #fafafa; border-bottom: 1px solid #e5e5e5;">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">PD Code</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Description(s)</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">Total Amount (PKR)</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-900">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(group, idx) in detail?.grouped ?? []" :key="group.pd_code">
                                <template x-if="true">
                                    <tbody>
                                        {{-- PD Code summary row --}}
                                        <tr @click="togglePdGroup(idx)"
                                            style="border-bottom: 1px solid #e5e5e5; cursor: pointer;"
                                            class="hover:bg-indigo-50 transition-colors">
                                            <td class="px-6 py-3 font-semibold text-gray-900">
                                                <span x-text="expandedGroups.includes(idx) ? '▼' : '▶'" class="text-xs mr-2" style="color: #505b93;"></span>
                                                <span x-text="group.pd_code"></span>
                                            </td>
                                            <td class="px-6 py-3 text-gray-600 text-xs truncate max-w-xs" x-text="group.descriptions.join(', ')" :title="group.descriptions.join(', ')"></td>
                                            <td class="px-6 py-3 text-right font-mono text-gray-900" x-text="formatPKR(group.subtotal)"></td>
                                            <td class="px-6 py-3 text-right text-gray-600" x-text="group.pct + '%'"></td>
                                        </tr>
                                        {{-- Expanded line items --}}
                                        <template x-if="expandedGroups.includes(idx)">
                                            <template x-for="line in group.lines" :key="line.description">
                                                <tr style="background: #f8f7ff; border-bottom: 1px solid #ede9fe;">
                                                    <td class="px-6 py-2 pl-12 text-gray-600" colspan="2">
                                                        <span class="text-xs mr-3" style="color: #706f6c;" x-text="line.date"></span>
                                                        <span x-text="line.description"></span>
                                                    </td>
                                                    <td class="px-6 py-2 text-right font-mono text-gray-700" x-text="formatPKR(line.amount)"></td>
                                                    <td class="px-6 py-2 text-right text-gray-400 text-xs"
                                                        x-text="detail.total_value > 0 ? (line.amount / detail.total_value * 100).toFixed(1) + '%' : ''"></td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </template>
                            </template>
                        </tbody>
                        <tfoot style="background: #fafafa; border-top: 2px solid #e5e5e5;">
                            <tr>
                                <td class="px-6 py-3 font-bold text-gray-900" colspan="2">TOTAL</td>
                                <td class="px-6 py-3 text-right font-bold font-mono text-gray-900" x-text="detail ? 'PKR ' + formatPKR(detail.total_value) : '—'"></td>
                                <td class="px-6 py-3 text-right font-bold text-gray-900">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- B3 — Enrichment Form --}}
                <div class="rounded-lg p-6" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <p class="font-semibold text-gray-900 mb-4">Enrichment Data</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Gross Floor Area (m²)</label>
                            <input type="number" x-model="form.gross_floor_area" min="1" step="1"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid #e5e5e5; background: white;" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Seating Capacity</label>
                            <input type="number" x-model="form.seating_capacity" min="1" step="1"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid #e5e5e5; background: white;" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Area Source</label>
                            <select x-model="form.area_source" class="w-full rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;">
                                <option value="">— select —</option>
                                <option value="Drawing">Drawing</option>
                                <option value="Survey">Survey</option>
                                <option value="Satellite">Satellite</option>
                                <option value="Estimate">Estimate</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Area Verified</label>
                            <select x-model="form.area_verified" class="w-full rounded-lg px-3 py-2 text-sm" style="border: 1px solid #e5e5e5; background: white;">
                                <option value="">— select —</option>
                                <option value="Pending">Pending</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Completion Date</label>
                            <input type="date" x-model="form.completion_date"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid #e5e5e5; background: white;" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-900 mb-1">Notes</label>
                            <textarea x-model="form.notes" rows="3" maxlength="2000"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid #e5e5e5; background: white;"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" x-model="form.exclude_from_estimator"
                                    class="mt-0.5 h-4 w-4 rounded"
                                    style="accent-color: #ef4444;" />
                                <span>
                                    <span class="block text-sm font-medium text-gray-900">Exclude from rate calculation</span>
                                    <span class="block text-xs mt-0.5" style="color: #706f6c;">
                                        Tick this if the project has incomplete transaction data or an unreliable GFA.
                                        It will be omitted when <code class="font-mono">php artisan calculate:rates</code> is next run.
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Live calculations panel --}}
                    <div class="mt-6 rounded-lg p-5" style="background: #f8f7ff; border: 1px solid #c4b5fd;">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-4" style="color: #505b93;">Calculated Rates (based on entries above)</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-xs mb-1" style="color: #706f6c;">Total Project Value</p>
                                <p class="font-mono font-semibold text-gray-900" x-text="detail ? 'PKR ' + formatPKR(detail.total_value) : '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs mb-1" style="color: #706f6c;">Gross Floor Area</p>
                                <p class="font-mono font-semibold text-gray-900" x-text="form.gross_floor_area ? Number(form.gross_floor_area).toLocaleString() + ' m²' : '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs mb-1" style="color: #706f6c;">Rate per m²</p>
                                <p class="font-mono font-semibold"
                                    :style="liveRateM2 ? 'color: #505b93;' : 'color: #9ca3af;'"
                                    x-text="liveRateM2 ? 'PKR ' + formatPKR(liveRateM2) + ' / m²' : '— enter GFA above'"></p>
                            </div>
                            <div>
                                <p class="text-xs mb-1" style="color: #706f6c;">Seating Capacity</p>
                                <p class="font-mono font-semibold text-gray-900" x-text="form.seating_capacity ? Number(form.seating_capacity).toLocaleString() + ' seats' : '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs mb-1" style="color: #706f6c;">Cost per Seat</p>
                                <p class="font-mono font-semibold"
                                    :style="liveCostPerSeat ? 'color: #505b93;' : 'color: #9ca3af;'"
                                    x-text="liveCostPerSeat ? 'PKR ' + formatPKR(liveCostPerSeat) + ' / seat' : '— enter seats above'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Save button + success/error feedback --}}
                    <div class="mt-5 flex items-center gap-4">
                        <button @click="save()" :disabled="saving"
                            class="px-6 py-2 rounded-lg text-sm font-medium text-white transition"
                            :style="saving ? 'background: #9ca3af; cursor: not-allowed;' : 'background: #505b93;'">
                            <span x-text="saving ? 'Saving…' : 'Save Enrichment Data'"></span>
                        </button>
                        <p x-show="saveSuccess" class="text-sm font-medium" style="color: #059669;">✓ Saved successfully</p>
                        <p x-show="saveError" class="text-sm font-medium" style="color: #ef4444;" x-text="saveError"></p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Prompt when nothing selected --}}
        <div x-show="!selectedId" class="rounded-lg p-10 text-center" style="background: #fafafa; border: 1px dashed #e5e5e5;">
            <p class="text-gray-500">Select a project from the table above to view its detail and enrichment form.</p>
        </div>

    </div>

    @push('scripts')
    <script>
    function enrichment() {
        return {
            allProjects: @json($projectsJson),

            filters: { search: '', region: '', enriched: '', pdCode: '', minValue: '', maxValue: '' },
            selectedId: null,
            loading: false,
            detail: null,
            expandedGroups: [],

            form: {
                gross_floor_area: '',
                seating_capacity: '',
                area_source: '',
                area_verified: '',
                completion_date: '',
                notes: '',
                exclude_from_estimator: false,
            },

            saving: false,
            saveSuccess: false,
            saveError: '',
            _abortController: null,

            init() {},

            get filteredProjects() {
                return this.allProjects.filter(p => {
                    if (this.filters.search) {
                        const q = this.filters.search.toLowerCase();
                        if (!p.name.toLowerCase().includes(q) && !(p.project_nr ?? '').toLowerCase().includes(q)) return false;
                    }
                    if (this.filters.region && String(p.region_id) !== String(this.filters.region)) return false;
                    if (this.filters.enriched) {
                        const hasGfa   = p.gross_floor_area != null;
                        const hasSeats = p.seating_capacity != null;
                        if (this.filters.enriched === 'complete' && !(hasGfa && hasSeats)) return false;
                        if (this.filters.enriched === 'partial'  && !((hasGfa || hasSeats) && !(hasGfa && hasSeats))) return false;
                        if (this.filters.enriched === 'missing'  && (hasGfa || hasSeats)) return false;
                    }
                    if (this.filters.minValue && (p.total_value ?? 0) < Number(this.filters.minValue)) return false;
                    if (this.filters.maxValue && (p.total_value ?? 0) > Number(this.filters.maxValue)) return false;
                    return true;
                });
            },

            get liveRateM2() {
                const gfa = parseFloat(this.form.gross_floor_area);
                const val = this.detail?.total_value;
                return (gfa > 0 && val > 0) ? Math.round(val / gfa) : null;
            },

            get liveCostPerSeat() {
                const seats = parseFloat(this.form.seating_capacity);
                const val   = this.detail?.total_value;
                return (seats > 0 && val > 0) ? Math.round(val / seats) : null;
            },

            enrichedBadge(p) {
                const hasGfa   = p.gross_floor_area != null;
                const hasSeats = p.seating_capacity != null;
                if (hasGfa && hasSeats)
                    return '<span class="inline-block w-3 h-3 rounded-full" style="background:#10b981;" title="Complete"></span>';
                if (hasGfa || hasSeats)
                    return '<span class="inline-block w-3 h-3 rounded-full" style="background:#f59e0b;" title="Partial"></span>';
                return '<span class="inline-block w-3 h-3 rounded-full" style="background:#ef4444;" title="Missing"></span>';
            },

            async selectProject(id) {
                // Cancel any in-flight request for a previous selection
                if (this._abortController) this._abortController.abort();
                this._abortController = new AbortController();

                this.selectedId  = id;
                this.loading     = true;
                this.detail      = null;
                this.expandedGroups = [];
                this.saveSuccess = false;
                this.saveError   = '';

                try {
                    const res  = await fetch(`/admin/historical-enrichment/${id}`, { signal: this._abortController.signal });
                    const data = await res.json();
                    this.detail = data;
                    this.form.gross_floor_area       = data.project.gross_floor_area ?? '';
                    this.form.seating_capacity       = data.project.seating_capacity ?? '';
                    this.form.area_source            = data.project.area_source ?? '';
                    this.form.area_verified          = data.project.area_verified ?? '';
                    this.form.completion_date        = data.project.completion_date ?? '';
                    this.form.notes                  = data.project.notes ?? '';
                    this.form.exclude_from_estimator = data.project.exclude_from_estimator ?? false;
                } catch (e) {
                    if (e.name !== 'AbortError') this.saveError = 'Failed to load project data.';
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        document.getElementById('detail-panel')?.scrollIntoView({ behavior: 'smooth' });
                    });
                }
            },

            togglePdGroup(idx) {
                if (this.expandedGroups.includes(idx)) {
                    this.expandedGroups = this.expandedGroups.filter(i => i !== idx);
                } else {
                    this.expandedGroups.push(idx);
                }
            },

            async save() {
                this.saving      = true;
                this.saveSuccess = false;
                this.saveError   = '';

                try {
                    const res = await fetch(`/admin/historical-enrichment/${this.selectedId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.saveSuccess = true;

                        // Update the lookup table row in-memory
                        const row = this.allProjects.find(p => p.id === this.selectedId);
                        if (row) {
                            row.gross_floor_area       = this.form.gross_floor_area ? parseFloat(this.form.gross_floor_area) : null;
                            row.seating_capacity       = this.form.seating_capacity ? parseInt(this.form.seating_capacity) : null;
                            row.exclude_from_estimator = this.form.exclude_from_estimator;
                        }

                        setTimeout(() => { this.saveSuccess = false; }, 4000);
                    } else {
                        this.saveError = data.message ?? 'Save failed.';
                    }
                } catch (e) {
                    this.saveError = 'Network error — please try again.';
                } finally {
                    this.saving = false;
                }
            },

            formatPKR(n) {
                if (n == null || n === '' || n === undefined) return '—';
                const num = Number(n);
                if (isNaN(num)) return '—';
                return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
        };
    }
    </script>
    @endpush

</x-app-layout>
