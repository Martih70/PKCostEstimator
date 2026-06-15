<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">How PK Cost Estimator Works</h2>
    </x-slot>

    @php
        // Small helper for consistent role-badge styling throughout this page.
        $roleBadge = function (string $label, string $bg, string $fg) {
            return '<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold mr-1" style="background:'.$bg.'; color:'.$fg.';">'.$label.'</span>';
        };
        $admin    = $roleBadge('Admin', '#ede9fe', '#6d28d9');
        $costMgr  = $roleBadge('Cost Manager', '#e0f2fe', '#0369a1');
        $reviewer = $roleBadge('Reviewer', '#fce7f3', '#be185d');
        $everyone = $roleBadge('Everyone', '#f3f4f6', '#374151');
    @endphp

    <div class="space-y-8 max-w-5xl">

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- INTRO — the big picture                                --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="rounded-lg p-6" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
            <p class="font-semibold text-gray-900 mb-2">The big picture</p>
            <p class="text-sm text-gray-600 leading-relaxed">
                PK Cost Estimator exists to answer one question: <em>what will a new building project in Pakistan
                probably cost?</em> It answers that by learning from buildings that have already been built. Real
                costs from completed (<strong>historical</strong>) projects are turned into a library of cost rates
                per region and building element, and those rates are then used to build forecasts for new
                (<strong>forecast</strong>) projects.
            </p>
            <p class="text-sm text-gray-600 leading-relaxed mt-3">
                Every tab in this app is a step in that one pipeline — from logging an invoice, right through to
                printing a forecast report. The diagram below shows the journey a number takes from "money spent
                on site" to "forecast for a new project"; the section underneath then walks through every tab in
                detail and explains how each one connects to the others.
            </p>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- VISUAL PIPELINE                                        --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="rounded-lg p-6" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); background: #fafafa;">
            <p class="font-semibold text-gray-900 mb-1">The pipeline, step by step</p>
            <p class="text-xs mb-5" style="color: #706f6c;">Follow the arrows from top to bottom — each step feeds the one below it.</p>

            <div class="space-y-0 max-w-2xl mx-auto">
                @php
                    $steps = [
                        ['color' => '#0ea5e9', 'bg' => '#e0f2fe', 'title' => '1 · Transactions',
                         'body'  => 'A real cost on site — an invoice, a delivery, a contractor payment — is logged as a transaction: a date, an amount, a description, and a PD code describing what kind of cost it was.'],
                        ['color' => '#a855f7', 'bg' => '#f3e8ff', 'title' => '2 · Historical Projects',
                         'body'  => 'Transactions accumulate against a historical project — a building that has been (or is being) built. A project\'s total cost is simply the sum of all its transactions.'],
                        ['color' => '#10b981', 'bg' => '#d1fae5', 'title' => '3 · Historical Enrichment',
                         'body'  => 'An admin records each historical project\'s Gross Floor Area (and seating capacity) — the missing ingredient needed to turn "this project cost X" into "this kind of work costs Y per m²".'],
                        ['color' => '#10b981', 'bg' => '#d1fae5', 'title' => '4 · Rate Calculation (Recalculate Rates)',
                         'body'  => 'The system groups every PD code into a building element (Substructure, Roof, Electrical, …), works out a cost-per-m² for each project and element, and distils all of that into Low / Medium / High / High+ rates per region.'],
                        ['color' => '#0ea5e9', 'bg' => '#e0f2fe', 'title' => '5 · Regional Rates Library',
                         'body'  => 'The resulting rate library — the "price book" the whole tool runs on — is shown on the Rates and Analytics pages.'],
                        ['color' => '#0ea5e9', 'bg' => '#e0f2fe', 'title' => '6 · Create Estimate',
                         'body'  => 'A Cost Manager picks a region and floor area for a new project, chooses a cost band (Low/Medium/High/High+) per building element, adds extras such as Externals or Contingency — and the Estimator totals it all up live.'],
                        ['color' => '#a855f7', 'bg' => '#f3e8ff', 'title' => '7 · Forecast & Historical Project Reports',
                         'body'  => 'Saving an estimate creates or updates a forecast project. The Forecast Projects report presents it as a clean, shareable summary, with the Inflation Factor applied so older rate data is expressed in today\'s (or the project\'s planned) prices. Historical Project Reports (Administration) do the same for completed projects, showing the actuals exactly as spent.'],
                        ['color' => '#8b5cf6', 'bg' => '#ede9fe', 'title' => '8 · AI Executive Summary (optional, Beta)',
                         'body'  => 'On a Forecast Project report, a Cost Manager or Admin can generate a brief AI executive summary of the figures above — a headline paragraph, key cost drivers, and anything worth double-checking — with the option to drill down into a full narrative explanation, comparable historical projects, and a full list of risk flags. The AI only explains and selects from figures already calculated here; it never produces or changes a cost figure.'],
                    ];
                @endphp

                @foreach($steps as $i => $step)
                    <div class="flex gap-4 items-start rounded-lg p-4" style="background: white; border: 1px solid #e5e5e5;">
                        <div class="shrink-0 w-3 h-3 rounded-full mt-1.5" style="background: {{ $step['color'] }};"></div>
                        <div>
                            <p class="font-semibold text-sm mb-1" style="color: {{ $step['color'] }};">{{ $step['title'] }}</p>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $step['body'] }}</p>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="flex justify-center py-1">
                            <svg class="h-5 w-5" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB-BY-TAB WALKTHROUGH                                 --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Every tab, explained</h3>
            <p class="text-sm mb-5" style="color: #706f6c;">
                The badges show who can see each tab — your sidebar only shows the ones your role grants you, which is why it looks different depending on who's logged in.
            </p>

            <div class="space-y-4">

                {{-- Dashboard --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#6366f1;"></span>
                        <p class="font-semibold text-gray-900">Dashboard</p>
                        <span class="ml-auto">{!! $everyone !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Your home base — and it adapts to your role. <strong>Admins</strong> see a system-health
                        snapshot: total transactions, historical spend, project counts, regions covered, how many
                        rate data-points exist, user count, the date of the last import, a spend-by-region
                        breakdown, and the list of forecast projects. <strong>Cost Managers</strong> get a quick
                        link into the Estimator. <strong>Reviewers</strong> get a quick link into Analytics and a
                        count of active forecast projects.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> the admin view is effectively a health check on every other tab — if "rate data points" or "last import" look stale, that's your cue to check Transactions, Historical Enrichment, and Rates.</p>
                </div>

                {{-- Create Estimate --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#0ea5e9;"></span>
                        <p class="font-semibold text-gray-900">Create Estimate (Estimator)</p>
                        <span class="ml-auto">{!! $costMgr !!}{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        This is where new cost forecasts get built. Pick a region, enter the gross floor area for
                        the project you're estimating, then for each building element (Substructure, External
                        Walls, Roof, Electrical, and so on) choose a cost band — <strong>Low</strong> (cheapest
                        observed rate), <strong>Medium</strong> (typical/median rate), <strong>High</strong>
                        (most expensive observed rate), or <strong>High+</strong> (High plus a 15–20% uplift for
                        contingency planning). Add optional extras — Externals, Project Support, Contingency, DD
                        Risk — and the total updates in real time as you work. Save it, and the figure is written
                        back onto the project as its forecast estimate.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> every rate you see here comes straight from the Regional Rates library (the Rates / Analytics tabs). If that library is empty, out of date, or missing a region, your bands will be empty too — run <em>Rates → Recalculate</em> after new transactions or enrichment data go in. Open Create Estimate directly from a project on the Forecast Projects page to pre-fill its region and floor area, and save to write the total straight back onto that project.</p>
                </div>

                {{-- Add Project --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#a855f7;"></span>
                        <p class="font-semibold text-gray-900">Add Project</p>
                        <span class="ml-auto">{!! $costMgr !!}{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Creates a brand-new project record — give it a project number, unique ID, name, region,
                        and optionally a budget, seating capacity and start date. Anything created here is
                        automatically a <strong>forecast</strong> project (one still being planned, as opposed to
                        a <strong>historical</strong> one that has already been built and paid for).
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this is the starting point for a new forecast — once the project exists, open it in Create Estimate to actually build its cost forecast.</p>
                </div>

                {{-- Forecast Projects --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#a855f7;"></span>
                        <p class="font-semibold text-gray-900">Forecast Projects</p>
                        <span class="ml-auto">{!! $costMgr !!}{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The list of forecast (still-being-planned) projects, with their current cost estimate and
                        gross floor area as last saved from Create Estimate. Each row links through to a polished
                        report broken down by building element and region.
                    </p>
                    <p class="text-sm text-gray-600 leading-relaxed mt-2">
                        Each report also has an <strong>AI Executive Summary</strong> (Beta) — generate a short
                        headline paragraph, up to three key cost drivers, and a "worth checking" note, then drill
                        down into a full narrative explanation, a list of comparable historical projects (with the
                        AI's reasons for picking them), and the complete set of risk flags. This is advisory only:
                        the AI explains and selects from the figures already on the page — it never creates or
                        changes a PKR figure.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this is purely a presentation layer over Create Estimate — change the estimate, and the figures here (and the report behind each row) change too.</p>
                </div>

                {{-- Analytics --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#ec4899;"></span>
                        <p class="font-semibold text-gray-900">Analytics</p>
                        <span class="ml-auto">{!! $reviewer !!}{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        A read-only view of the Regional Rates library — the same Low / Medium / High / High+
                        rates per region and building element that power the Estimator, presented for review and
                        comparison rather than for editing.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this mirrors the Rates admin page exactly — it's the reviewer's window onto the same price book that Create Estimate reads from.</p>
                </div>

                <div class="pt-2">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: #706f6c;">Administration — Admins only</p>
                    <p class="text-xs mt-0.5" style="color: #9ca3af;">These are the back-office tabs where the raw data is recorded, checked, and turned into the rate library everything else depends on.</p>
                </div>

                {{-- Historical Project Reports --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#a855f7;"></span>
                        <p class="font-semibold text-gray-900">Historical Project Reports</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The same idea as the Forecast Projects report, but for completed projects — presenting
                        their <em>actual</em> recorded costs (drawn straight from Transactions) as a clean,
                        shareable summary broken down by building element and PD code. These figures are shown
                        exactly as spent, with no Inflation Factor applied — this is a record of what happened,
                        not a forecast.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> draws on the same transaction data you can browse on Historical Enrichment, but formatted for sharing and printing rather than editing. Sits alongside Historical Project Data below — this tab is the report, that one is where its floor area, seating capacity and exclusion flag are edited. Each project's report has an "Edit Enrichment Data →" link that jumps straight to that project on Historical Enrichment.</p>
                </div>

                {{-- Historical Project Data --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#a855f7;"></span>
                        <p class="font-semibold text-gray-900">Historical Project Data</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The master list of completed projects — the raw material the entire rate-calculation
                        pipeline is built from. From here you can see which historical projects exist, their
                        region, their transaction-derived totals, and edit floor area, seating capacity and the
                        "exclude from estimator" flag.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> deeper editing of a historical project's cost data — adding floor area, reviewing its transactions — also happens on Historical Enrichment, one tab below. Historical Project Reports above presents this same project's transactions as a read-only report.</p>
                </div>

                {{-- Historical Enrichment --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#10b981;"></span>
                        <p class="font-semibold text-gray-900">Historical Enrichment</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        This is where you fill in the gaps on historical projects: Gross Floor Area, Seating
                        Capacity, Area Source and Verification status, Completion Date, and Notes — while
                        reviewing each project's full transaction breakdown by PD code. A historical project
                        <em>cannot</em> contribute a cost-per-m² rate to the system until its floor area is
                        known — this page is the bridge between "we have the costs" (Transactions) and
                        "we can calculate a rate" (the Recalculate step on the Rates page). The
                        <em>"Exclude from rate calculation"</em> checkbox lets you flag a project whose data is
                        unreliable — incomplete transactions, a guessed floor area — so it's left out the next
                        time rates are recalculated.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> everything you change here directly changes what the next "Recalculate Rates" run on the Rates page produces — and therefore what every Cost Manager sees in the Estimator. You can also jump here directly from a project's Historical Project Report via its "Edit Enrichment Data →" link.</p>
                </div>

                {{-- Transactions --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#0ea5e9;"></span>
                        <p class="font-semibold text-gray-900">Transactions</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The raw ledger — every individual cost line item, each tagged with a date, a PD code (the
                        cost category recorded on site, e.g. "electrical wiring" or "roofing materials"), the
                        project it belongs to, a description, and an amount. You can browse and search them, add
                        one manually, edit or delete a line, or bulk-import a whole batch via
                        <em>Transactions → Import CSV</em> (columns: Date, PD Code, Area Code, AC Code MH,
                        Project Nr, Item Description, Amount).
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this is the foundation everything else stands on. Every total you see — on Historical Enrichment, Historical Project Reports, Historical Project Data, and ultimately the Regional Rates library — is just these records summed and grouped (by PD code → building element).</p>
                </div>

                {{-- Rates --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#10b981;"></span>
                        <p class="font-semibold text-gray-900">Rates</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The regional rate library in full — Low / Medium / High / High+ cost-per-m² for every
                        region and building element, plus how many historical projects each figure is based on.
                        The <strong>Recalculate</strong> button re-runs the whole rate-calculation pipeline:
                        first it works out each (non-excluded) historical project's cost-per-element-per-m²,
                        then it aggregates those into Low/Medium/High/High+ figures per region. This page is
                        also where each region's <strong>Cost Adjustment Factor</strong> is tuned — a multiplier
                        used to account for cost differences between regions.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this <em>is</em> the price book — Create Estimate, Analytics, and both Reports tabs all read directly from what's stored here. Run "Recalculate" any time you've imported new transactions or changed enrichment data, so the rates stay current.</p>
                </div>

                {{-- Inflation Factor --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#8b5cf6;"></span>
                        <p class="font-semibold text-gray-900">Inflation Factor</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Sets a single monthly cost-inflation percentage (capped at 10%) — essentially Pakistan's
                        construction cost inflation rate — along with a note explaining the figure and a record
                        of who set it and when. The Regional Rates library is built from historical project costs
                        at the prices they were actually spent, which could be years ago; the Inflation Factor
                        lets a forecast report uplift those rates forward to the new project's planned start date
                        (or, if no start date is set, to today's prices), so a forecast stays realistic.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> applied only within Forecast Projects reports — Historical Project Reports show actuals exactly as spent, with no uplift.</p>
                </div>

                {{-- Users --}}
                <div class="rounded-lg p-5" style="border: 1px solid #e5e5e5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#f59e0b;"></span>
                        <p class="font-semibold text-gray-900">Users</p>
                        <span class="ml-auto">{!! $admin !!}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Manage who can log in and what they can do. There are three roles:
                    </p>
                    <ul class="text-sm text-gray-600 leading-relaxed list-disc list-inside mt-1 space-y-0.5">
                        <li><strong>Admin</strong> — full access, including every Administration tool described above.</li>
                        <li><strong>Cost Manager</strong> — can create estimates and view forecast project reports.</li>
                        <li><strong>Reviewer</strong> — can view Analytics and the rate library, but can't edit anything.</li>
                    </ul>
                    <p class="text-sm text-gray-600 leading-relaxed mt-2">
                        You can add, edit (including resetting passwords or changing roles), or remove users here.
                        You can't delete your own account.
                    </p>
                    <p class="text-xs mt-2" style="color: #706f6c;"><strong>Linked to:</strong> this is what decides which of the tabs above any given person actually sees — it's the reason the sidebar (and the badges on this very page) look different depending on who's logged in.</p>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- THE STORY END TO END                                   --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="rounded-lg p-6" style="background: #f8f7ff; border: 1px solid #c4b5fd;">
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #505b93;">Putting it all together — the story end to end</p>
            <ol class="text-sm text-gray-700 leading-relaxed space-y-3 list-decimal list-inside">
                <li>Someone on site in Pakistan spends money on a building project. That cost is logged as a <strong>Transaction</strong> — a date, an amount, a description, and a PD code saying what kind of cost it was.</li>
                <li>Transactions accumulate against a <strong>Historical Project</strong>. Its total cost is just the sum of everything logged against it.</li>
                <li>To turn "this project cost X" into "this kind of work costs Y per square metre", someone needs to know how big the building is — that's what <strong>Historical Enrichment</strong> records.</li>
                <li>With both costs and floor areas known, <strong>Recalculate Rates</strong> (on the Rates page) does the maths: PD codes are grouped into building elements, a cost-per-m² is worked out for each project and element, and the results are distilled into Low/Medium/High/High+ rates per region — the <strong>Regional Rates library</strong>.</li>
                <li>That library is the foundation everything else stands on. <strong>Analytics</strong> lets reviewers browse it; <strong>Create Estimate</strong> lets Cost Managers actually <em>use</em> it to build a forecast for a brand-new project.</li>
                <li>Saving that forecast creates a <strong>Forecast Project</strong>, listed (with its cost estimate and floor area) on the <strong>Forecast Projects</strong> page and presentable as a polished report, with the <strong>Inflation Factor</strong> applied so the rates are expressed in the project's planned (or today's) prices — while <strong>Historical Project Reports</strong> does the same for completed projects, showing the actuals exactly as spent.</li>
                <li>On that report, an optional <strong>AI Executive Summary</strong> can generate a brief headline, key cost drivers, and a "worth checking" note, with drill-downs into a full explanation, comparable historical projects, and risk flags — it only explains figures already on the page and can never create or change one.</li>
                <li><strong>Users</strong> sits slightly outside this chain — it's where Admins decide who can do what, which is why the sidebar looks different for everyone.</li>
            </ol>
        </div>

    </div>
</x-app-layout>
