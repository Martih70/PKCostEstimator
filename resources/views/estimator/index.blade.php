<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Cost Estimator</h2>
                @if($project)
                    <p class="text-sm mt-0.5" style="color: #706f6c;">
                        {{ $project->name }}
                        @if($project->project_id)
                            <span class="font-mono">· {{ $project->project_id }}</span>
                        @endif
                        @if($project->region)
                            · {{ $project->region->name }}
                        @endif
                    </p>
                @else
                    <p class="text-sm mt-0.5" style="color: #706f6c;">Select a forecast project to build and save an estimate</p>
                @endif
            </div>
            @if($project)
                <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: #f5f5f5; border: 1px solid #e5e5e5; color: #505b93;">
                    ← Back to Projects
                </a>
            @endif
        </div>
    </x-slot>

<div x-data="estimator()" style="background: linear-gradient(135deg, #fafafa 0%, #f0f0f0 100%); min-height: 100vh;" class="pb-20">
    <!-- Sticky Header -->
    <div class="sticky top-16 z-40" style="background: white; border-bottom: 1px solid #e5e5e5; box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.1);">
        <div class="max-w-7xl mx-auto px-6 py-6">
            @if($project)
                <div class="flex items-center justify-end gap-3 mb-4 pb-4" style="border-bottom: 1px solid #e5e5e5;">
                    <span x-show="saveSuccess" x-cloak x-transition class="text-sm font-medium" style="color: #10b981;">Saved ✓</span>
                    <span x-show="saveError" x-cloak x-transition class="text-sm font-medium" style="color: #ef4444;" x-text="saveError"></span>
                    <button type="button" @click="save()" :disabled="saving" class="text-sm font-medium px-4 py-2 rounded-lg text-white transition disabled:opacity-50" style="background: #505b93;">
                        <span x-text="saving ? 'Saving…' : 'Save Estimate'"></span>
                    </button>
                </div>
            @else
                <div class="flex items-center gap-3 mb-4 pb-4 flex-wrap" style="border-bottom: 1px solid #e5e5e5;">
                    <label for="projectPicker" class="text-sm font-medium text-gray-900 whitespace-nowrap">Estimating for:</label>
                    <select id="projectPicker" onchange="if(this.value) window.location.href = '/estimator/' + this.value;" class="rounded-lg px-3 py-2 text-sm flex-1 sm:flex-none" style="border: 1px solid #e5e5e5; background: white; color: #1b1b18;">
                        <option value="">Scratch (not saved to a project)…</option>
                        @foreach($forecastProjects as $fp)
                            <option value="{{ $fp->id }}">{{ $fp->name }}@if($fp->project_id) ({{ $fp->project_id }})@endif</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: #706f6c;">Estimated Project Cost</p>
            <div class="flex items-baseline justify-between">
                <div>
                    <p class="text-4xl font-semibold text-gray-900" x-text="'PKR ' + grandTotalPkr().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-semibold" style="color: #505b93;" x-text="grandTotalConverted().toLocaleString('en-US', { maximumFractionDigits: 2 })"></p>
                    <p class="text-xs mt-1" style="color: #706f6c;" x-text="currency"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-6 py-12 space-y-12">
        <!-- Project Parameters -->
        <section class="space-y-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f5f5f5;">
                    <span class="text-lg">⚙️</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Project Parameters</h2>
                    <p class="text-sm" style="color: #706f6c;">Configure your project settings</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Region Card -->
                <div class="rounded-lg p-5 transition-all duration-200" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <label for="region" class="block text-xs font-semibold uppercase tracking-wider mb-3" style="color: #706f6c;">Region</label>
                    <div class="relative">
                        <select id="region" x-model="regionId" class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none transition-all appearance-none" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18; padding-right: 2.5rem; -webkit-appearance: none;">
                            <option value="">Select region...</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" style="background: white; color: #1b1b18;">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #706f6c;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Floor Area Card -->
                <div class="rounded-lg p-5 transition-all duration-200" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <label for="floorArea" class="block text-xs font-semibold uppercase tracking-wider mb-3" style="color: #706f6c;">Floor Area (m²)</label>
                    <input type="number" id="floorArea" x-model.number="floorArea" min="0" step="0.01" class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none transition-all" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                </div>

                <!-- Currency Card -->
                <div class="rounded-lg p-5 transition-all duration-200" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <label for="currency" class="block text-xs font-semibold uppercase tracking-wider mb-3" style="color: #706f6c;">Currency</label>
                    <div class="relative">
                        <select id="currency" x-model="currency" class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none transition-all appearance-none" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18; padding-right: 2.5rem; -webkit-appearance: none;">
                            <option value="">PKR</option>
                            @foreach($exchangeRates as $code => $rate)
                                @if($code !== 'PKR')
                                    <option value="{{ $code }}" style="background: white; color: #1b1b18;">{{ $code }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #706f6c;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Construction Elements -->
        <section class="space-y-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f5f5f5;">
                    <span class="text-lg">🏗️</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Construction Elements</h2>
                    <p class="text-sm" style="color: #706f6c;">Select cost bands for each component</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($elements as $element)
                <div class="rounded-lg p-6 transition-all duration-200" style="background: #fffaf0; border: 1px solid #e5e5e5; border-left: 4px solid #505b93; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);" x-cloak>
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #505b93;">{{ $element->code }}</p>
                            <p class="text-base font-semibold text-gray-900">{{ $element->name }}</p>
                        </div>
                        <template x-if="getRate({{ $element->id }})">
                            <div class="text-right">
                                <p class="text-2xl font-semibold" style="color: #10b981;" x-text="elementCost({{ $element->id }}).toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                                <p class="text-xs mt-1" style="color: #706f6c;">PKR</p>
                            </div>
                        </template>
                        <template x-if="!getRate({{ $element->id }}) && regionId">
                            <span class="text-xs font-medium px-3 py-1 rounded-lg" style="color: #706f6c; background: #f5f5f5;">No data</span>
                        </template>
                    </div>

                    <template x-if="regionId && getRate({{ $element->id }})">
                        <div class="space-y-4">
                            <!-- Rate Band Buttons -->
                            <div class="flex gap-2">
                                <button @click="elementBands[{{ $element->id }}] = 'low'" :class="elementBands[{{ $element->id }}] === 'low' ? 'bg-emerald-100 border-emerald-400 text-emerald-700' : 'bg-white text-gray-700 hover:bg-gray-50'" class="flex-1 px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-150" style="border-color: #e5e5e5;">Low</button>
                                <button @click="elementBands[{{ $element->id }}] = 'medium'" :class="elementBands[{{ $element->id }}] === 'medium' ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-white text-gray-700 hover:bg-gray-50'" class="flex-1 px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-150" style="border-color: #e5e5e5;">Med</button>
                                <button @click="elementBands[{{ $element->id }}] = 'high'" :class="elementBands[{{ $element->id }}] === 'high' ? 'bg-amber-100 border-amber-400 text-amber-700' : 'bg-white text-gray-700 hover:bg-gray-50'" class="flex-1 px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-150" style="border-color: #e5e5e5;">High</button>
                                <button @click="elementBands[{{ $element->id }}] = 'high_plus'" :class="elementBands[{{ $element->id }}] === 'high_plus' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-white text-gray-700 hover:bg-gray-50'" class="flex-1 px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-150" style="border-color: #e5e5e5;">High+</button>
                            </div>

                            <!-- Rate Info Cards -->
                            <div class="grid grid-cols-2 gap-3 pt-3" style="border-top: 1px solid #e5e5e5;">
                                <div class="p-3 rounded-lg" style="background: #f5f5f5; border: 1px solid #e5e5e5;">
                                    <p class="text-xs font-medium mb-1" style="color: #706f6c;">Rate</p>
                                    <p class="text-lg font-semibold" style="color: #505b93;" x-text="getRate({{ $element->id }}).toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                                    <p class="text-xs" style="color: #706f6c;">PKR/m²</p>
                                </div>
                                <div class="p-3 rounded-lg" style="background: #f5f5f5; border: 1px solid #e5e5e5;">
                                    <p class="text-xs font-medium mb-1" style="color: #706f6c;">Cost</p>
                                    <p class="text-lg font-semibold" style="color: #10b981;" x-text="elementCost({{ $element->id }}).toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                                    <p class="text-xs" style="color: #706f6c;">PKR</p>
                                </div>
                            </div>

                            <!-- Manual Override -->
                            <div class="pt-3" style="border-top: 1px solid #e5e5e5;">
                                <label class="text-xs font-medium block mb-2" style="color: #706f6c;">Manual Override (PKR/m²)</label>
                                <input type="number" x-model.number="elementOverrides[{{ $element->id }}]" min="0" step="0.01" placeholder="Leave blank" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none transition-all" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                            </div>
                        </div>
                    </template>

                    <template x-if="!regionId">
                        <p class="text-sm" style="color: #706f6c;">Select a region to view rates</p>
                    </template>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Adjustments & Summary -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Adjustments -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f5f5f5;">
                        <span class="text-lg">⚡</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Add-ons & Risk</h2>
                        <p class="text-sm" style="color: #706f6c;">Optional cost adjustments</p>
                    </div>
                </div>

                <div class="rounded-lg p-6 space-y-4" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-colors duration-150" style="background: #fafafa;">
                        <input type="checkbox" x-model="externalsEnabled" class="w-4 h-4 rounded" style="border: 1px solid #e5e5e5; background: white;">
                        <span class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Externals</p>
                            <p class="text-xs" style="color: #706f6c;">+10% of construction</p>
                        </span>
                        <span class="text-sm font-semibold flex-shrink-0" style="color: #10b981;" x-show="externalsEnabled" x-text="`PKR ${externalsAmount().toLocaleString()}`"></span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-colors duration-150" style="background: #fafafa;">
                        <input type="checkbox" x-model="overheadEnabled" class="w-4 h-4 rounded" style="border: 1px solid #e5e5e5; background: white;">
                        <span class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Project Support</p>
                            <p class="text-xs" style="color: #706f6c;">+15% of subtotal</p>
                        </span>
                        <span class="text-sm font-semibold flex-shrink-0" style="color: #505b93;" x-show="overheadEnabled" x-text="`PKR ${overheadAmount().toLocaleString()}`"></span>
                    </label>

                    <div class="pt-4 space-y-3" style="border-top: 1px solid #e5e5e5;">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Contingency %</label>
                            <input type="number" x-model.number="contingencyPct" min="0" step="1" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none transition-all" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">DD Risk %</label>
                            <input type="number" x-model.number="ddRiskPct" min="0" step="1" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none transition-all" style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f5f5f5;">
                        <span class="text-lg">💰</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Cost Breakdown</h2>
                        <p class="text-sm" style="color: #706f6c;">Detailed cost summary</p>
                    </div>
                </div>

                <div class="rounded-lg p-6 space-y-3" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
                    <div class="flex justify-between py-2" style="border-bottom: 1px solid #e5e5e5;">
                        <p class="text-sm" style="color: #706f6c;">Construction</p>
                        <p class="text-sm font-medium text-gray-900" x-text="'PKR ' + constructionSubtotal().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                    </div>

                    <template x-if="externalsEnabled">
                        <div class="flex justify-between py-2" style="border-bottom: 1px solid #e5e5e5;">
                            <p class="text-sm" style="color: #706f6c;">Externals</p>
                            <p class="text-sm font-medium" style="color: #10b981;" x-text="'PKR ' + externalsAmount().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                        </div>
                    </template>

                    <template x-if="overheadEnabled">
                        <div class="flex justify-between py-2" style="border-bottom: 1px solid #e5e5e5;">
                            <p class="text-sm" style="color: #706f6c;">Overhead</p>
                            <p class="text-sm font-medium" style="color: #505b93;" x-text="'PKR ' + overheadAmount().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                        </div>
                    </template>

                    <div class="flex justify-between py-3 px-3 rounded-lg" style="border-bottom: 1px solid #e5e5e5; background: #f5f5f5;">
                        <p class="text-sm font-medium text-gray-900">Subtotal</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="'PKR ' + preContingencyTotal().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                    </div>

                    <template x-if="contingencyPct > 0">
                        <div class="flex justify-between py-2" style="border-bottom: 1px solid #e5e5e5;">
                            <p class="text-sm" style="color: #706f6c;" x-text="`Contingency (${contingencyPct}%)`"></p>
                            <p class="text-sm font-medium" style="color: #f59e0b;" x-text="'PKR ' + contingencyAmount().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                        </div>
                    </template>

                    <template x-if="ddRiskPct > 0">
                        <div class="flex justify-between py-2" style="border-bottom: 1px solid #e5e5e5;">
                            <p class="text-sm" style="color: #706f6c;" x-text="`DD Risk (${ddRiskPct}%)`"></p>
                            <p class="text-sm font-medium" style="color: #ef4444;" x-text="'PKR ' + ddRiskAmount().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                        </div>
                    </template>

                    <div class="rounded-lg px-4 py-3 mt-3 flex justify-between" style="background: #f5f5f5; border: 1px solid #e5e5e5;">
                        <p class="text-sm font-medium" style="color: #505b93;">Total (PKR)</p>
                        <p class="text-sm font-semibold" style="color: #505b93;" x-text="'PKR ' + grandTotalPkr().toLocaleString('en-US', { maximumFractionDigits: 0 })"></p>
                    </div>

                    <div class="rounded-lg px-4 py-3 flex justify-between" style="background: #f5f5f5; border: 1px solid #e5e5e5;">
                        <p class="text-sm font-medium" style="color: #10b981;" x-text="currency"></p>
                        <p class="text-sm font-semibold" style="color: #10b981;" x-text="grandTotalConverted().toLocaleString('en-US', { maximumFractionDigits: 2 })"></p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
    function estimator() {
        return {
            regionId: {{ $project?->region_id ?? 'null' }},
            floorArea: {{ $project?->gross_floor_area ?? 0 }},
            currency: 'PKR',
            externalsEnabled: false,
            overheadEnabled: false,
            contingencyPct: 10,
            ddRiskPct: 5,
            elementBands: {},
            elementOverrides: {},
            rates: {!! $ratesJson !!},
            exchangeRates: @json($exchangeRates->mapWithKeys(fn($r) => [$r->currency_code => (float)$r->rate])->toArray()),
            elements: @json($elements),
            projectId: {{ $project?->id ?? 'null' }},
            saving: false,
            saveSuccess: false,
            saveError: '',

            init() {
                this.elements.forEach(el => {
                    this.$watch('regionId', () => {
                        this.elementBands[el.id] = 'medium';
                    });
                    this.elementBands[el.id] = 'medium';
                });
                this.fetchLiveRates();
            },

            fetchLiveRates() {
                fetch('/api/exchange-rates')
                    .then(res => res.json())
                    .then(data => {
                        if (data.rates) {
                            data.rates.PKR = 1.0;
                            this.exchangeRates = data.rates;
                        }
                    })
                    .catch(err => console.log('Exchange rates API unavailable, using cached rates'));
            },

            getRate(elementId) {
                if (!this.regionId) return null;
                const regionIdStr = String(this.regionId);
                const rate = this.rates[regionIdStr]?.[elementId];
                if (!rate) return null;
                const band = this.elementBands[elementId];
                const bandMap = { low: 'low', medium: 'medium', high: 'high', high_plus: 'high_plus' };
                return rate[bandMap[band]] ?? null;
            },

            effectiveRate(elementId) {
                if (this.elementOverrides[elementId]) return this.elementOverrides[elementId];
                return this.getRate(elementId);
            },

            elementCost(elementId) {
                const rate = this.effectiveRate(elementId);
                if (!rate || !this.floorArea) return 0;
                return rate * this.floorArea;
            },

            constructionSubtotal() {
                let total = 0;
                this.elements.forEach(el => {
                    total += this.elementCost(el.id);
                });
                return total;
            },

            externalsAmount() {
                if (!this.externalsEnabled) return 0;
                return this.constructionSubtotal() * 0.10;
            },

            overheadAmount() {
                if (!this.overheadEnabled) return 0;
                return (this.constructionSubtotal() + this.externalsAmount()) * 0.15;
            },

            preContingencyTotal() {
                return this.constructionSubtotal() + this.externalsAmount() + this.overheadAmount();
            },

            contingencyAmount() {
                if (this.contingencyPct <= 0) return 0;
                return this.preContingencyTotal() * (this.contingencyPct / 100);
            },

            ddRiskAmount() {
                if (this.ddRiskPct <= 0) return 0;
                return (this.preContingencyTotal() + this.contingencyAmount()) * (this.ddRiskPct / 100);
            },

            grandTotalPkr() {
                return this.preContingencyTotal() + this.contingencyAmount() + this.ddRiskAmount();
            },

            grandTotalConverted() {
                const rate = this.exchangeRates[this.currency] ?? 1;
                return this.grandTotalPkr() * rate;
            },

            hasNoData(elementId) {
                if (!this.regionId) return false;
                const regionIdStr = String(this.regionId);
                return !this.rates[regionIdStr]?.[elementId];
            },

            async save() {
                if (!this.projectId) return;

                this.saving = true;
                this.saveSuccess = false;
                this.saveError = '';

                try {
                    const res = await fetch(`/estimator/${this.projectId}/save`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            cost_estimate: this.grandTotalPkr(),
                            gross_floor_area: this.floorArea,
                        }),
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.saveSuccess = true;
                        setTimeout(() => this.saveSuccess = false, 3000);
                    } else {
                        this.saveError = data.message || 'Failed to save estimate';
                    }
                } catch (err) {
                    this.saveError = 'Failed to save estimate';
                } finally {
                    this.saving = false;
                }
            }
        };
    }
</script>
@endpush
</x-app-layout>
