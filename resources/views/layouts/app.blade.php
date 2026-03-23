<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PK Cost Estimator') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Page background — deeper tint so white cards pop */
            body {
                background: linear-gradient(160deg, #e8eeff 0%, #dde6ff 40%, #e4eeff 100%) !important;
                background-attachment: fixed !important;
            }

            /* Card — resting shadow is already visible, hover lifts dramatically */
            .card {
                background: white;
                border: 1px solid rgba(0,0,0,0.06);
                border-radius: 14px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.06);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 48px rgba(0,0,0,0.14), 0 6px 16px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.05);
            }

            /* Static card (no hover lift) — still has solid resting shadow */
            .card-static {
                background: white;
                border: 1px solid rgba(0,0,0,0.06);
                border-radius: 14px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.06);
            }

            /* Table card */
            .table-card {
                background: white;
                border: 1px solid rgba(0,0,0,0.06);
                border-radius: 14px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.06);
            }

            /* Sidebar nav link hover */
            .nav-link {
                transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            }
            .nav-link:hover {
                background: #ecedf8 !important;
                transform: translateX(3px);
            }

            /* Button lift */
            .btn-primary {
                transition: transform 0.15s ease, box-shadow 0.2s ease, background 0.15s ease;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(80, 91, 147, 0.45);
            }
            .btn-primary:active {
                transform: translateY(0);
                box-shadow: 0 2px 6px rgba(80, 91, 147, 0.3);
            }

            /* Top bar */
            .topbar {
                background: white;
                border-bottom: 1px solid rgba(0,0,0,0.06);
                box-shadow: 0 4px 20px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.05);
            }

            /* Sidebar */
            .sidebar {
                background: white;
                border-right: 1px solid rgba(0,0,0,0.06);
                box-shadow: 4px 0 24px rgba(0,0,0,0.08);
            }

            /* Section labels */
            .section-label {
                font-size: 0.65rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #9ca3af;
                padding: 0 1rem;
            }

            /* Active nav indicator */
            .nav-active {
                background: linear-gradient(90deg, #eef0fb, #f5f5f5);
                border-left: 3px solid #505b93 !important;
                color: #1b1b18;
            }

            /* Stat icon badge */
            .stat-icon {
                border-radius: 10px;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }

            /* Header bar */
            .page-header {
                background: white;
                border-bottom: 1px solid #ebebeb;
                padding: 1rem 2rem;
                box-shadow: 0 1px 0 #ebebeb;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen flex-col">
            <!-- Top Bar -->
            <header class="topbar px-6 py-3.5">
                <div class="flex items-center justify-between">
                    <!-- Logo/Brand -->
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #505b93, #3d4269); box-shadow: 0 2px 8px rgba(80,91,147,0.4);">
                            PK
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-gray-900 leading-none">PK Cost Estimator</h1>
                            <p class="text-xs text-gray-400 mt-0.5">Construction Forecasting</p>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2.5 rounded-xl px-3 py-2" style="background: #f5f5f8; border: 1px solid #ebebeb;">
                            <div class="h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: linear-gradient(135deg, #505b93, #3d4269);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-semibold text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 capitalize mt-0.5">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 h-9 w-9 transition" title="Sign out">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Sidebar -->
                <nav class="sidebar w-64 overflow-y-auto">
                    <div class="flex flex-col h-full">
                        <!-- Navigation Links -->
                        <div class="flex-1 px-3 py-5 space-y-0.5">

                            <!-- Dashboard -->
                            <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #6366f1;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 5l3-3m0 0l3 3m-3-3v12" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Estimator (cost_manager, admin) -->
                            @if(auth()->user()->role === 'cost_manager' || auth()->user()->role === 'admin')
                                <div class="pt-5">
                                    <p class="section-label mb-2">Estimator</p>
                                    <a href="{{ route('estimator.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('estimator.*') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0ea5e9;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span>Create Estimate</span>
                                    </a>
                                </div>

                                <!-- Projects -->
                                <div class="pt-5">
                                    <p class="section-label mb-2">Projects</p>
                                    <a href="{{ route('admin.projects.create') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.projects.create') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a855f7;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Add Project</span>
                                    </a>
                                    <a href="{{ route('admin.projects.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-gray-500 {{ request()->routeIs('admin.projects.index') ? 'font-medium text-gray-900' : '' }} ml-4 border-l-[3px] border-transparent">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a855f7;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Forecast Reports</span>
                                    </a>
                                    <a href="{{ route('project.reports-historical') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-gray-500 {{ request()->routeIs('project.reports-historical', 'project.report-historical') ? 'font-medium text-gray-900' : '' }} ml-4 border-l-[3px] border-transparent">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a855f7;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Historical Reports</span>
                                    </a>
                                </div>
                            @endif

                            <!-- Analytics (reviewer, admin) -->
                            @if(auth()->user()->role === 'reviewer' || auth()->user()->role === 'admin')
                                <div class="pt-5">
                                    <p class="section-label mb-2">Insights</p>
                                    <a href="{{ route('analytics.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('analytics.*') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ec4899;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Analytics</span>
                                    </a>
                                </div>
                            @endif

                            <!-- Administration (admin only) -->
                            @if(auth()->user()->role === 'admin')
                                <div class="pt-5">
                                    <p class="section-label mb-2">Administration</p>

                                    <a href="{{ route('admin.projects.historical') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.projects.historical') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a855f7;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Projects (Historical)</span>
                                    </a>

                                    <a href="{{ route('admin.transactions.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.transactions.*') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0ea5e9;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span>Transactions</span>
                                    </a>

                                    <a href="{{ route('admin.rates.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.rates.*') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Rates</span>
                                    </a>

                                    <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.users.index') ? 'nav-active text-gray-900' : 'text-gray-600 border-l-[3px] border-transparent' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #f59e0b;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Users</span>
                                    </a>

                                    <a href="{{ route('admin.users.create') }}" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-gray-500 {{ request()->routeIs('admin.users.create') ? 'font-medium text-gray-900' : '' }} ml-4 border-l-[3px] border-transparent">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #f59e0b;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Add User</span>
                                    </a>

                                </div>
                            @endif
                        </div>

                    </div>
                </nav>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto">
                    <!-- Page Header -->
                    @isset($header)
                        <header class="page-header">
                            {{ $header }}
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <div class="p-6 lg:p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
