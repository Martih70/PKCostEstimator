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
    </head>
    <body class="font-sans antialiased" style="background: linear-gradient(135deg, #fafafa 0%, #f0f0f0 100%); color: #1b1b18;">
        <div class="flex h-screen flex-col">
            <!-- Top Bar -->
            <header class="px-6 py-4" style="border-bottom: 1px solid #e5e5e5; background: white; box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.1);">
                <div class="flex items-center justify-between">
                    <!-- Logo/Brand -->
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-slate-700 flex items-center justify-center text-white font-bold text-sm">
                            PK
                        </div>
                        <h1 class="text-xl font-semibold text-gray-900">PK Cost Estimator</h1>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 h-10 w-10 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <nav class="w-64 overflow-y-auto" style="background: white; border-right: 1px solid #e5e5e5;">
                    <div class="flex flex-col h-full">
                        <!-- Navigation Links -->
                        <div class="flex-1 space-y-1 px-4 py-6">
                            <!-- Dashboard (all users) -->
                            <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('dashboard') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 5l3-3m0 0l3 3m-3-3v12" />
                                    </svg>
                                    <span>Dashboard</span>
                                </div>
                            </a>

                            <!-- Estimator (cost_manager, admin) -->
                            @if(auth()->user()->role === 'cost_manager' || auth()->user()->role === 'admin')
                                <a href="{{ route('estimator.index') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('estimator.*') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span>Estimator</span>
                                    </div>
                                </a>
                            @endif

                            <!-- Analytics (reviewer, admin) -->
                            @if(auth()->user()->role === 'reviewer' || auth()->user()->role === 'admin')
                                <a href="{{ route('analytics.index') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('analytics.*') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Analytics</span>
                                    </div>
                                </a>
                            @endif

                            <!-- Admin (admin only) -->
                            @if(auth()->user()->role === 'admin')
                                <div class="pt-4">
                                    <p class="px-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Administration</p>
                                    <div class="mt-3 space-y-1">
                                        <!-- Projects -->
                                        <a href="{{ route('admin.projects.index') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('admin.projects.*') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span>Projects</span>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.projects.create') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition ml-6" style="{{ request()->routeIs('admin.projects.create') ? 'color: #505b93; border-left: 3px solid transparent;' : 'border-left: 3px solid transparent;' }}">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Add Project</span>
                                            </div>
                                        </a>

                                        <!-- Transactions -->
                                        <a href="{{ route('admin.transactions.index') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('admin.transactions.*') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                                <span>Transactions</span>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.rates.index') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:text-gray-900 transition" style="{{ request()->routeIs('admin.rates.*') ? 'background: #f5f5f5; border-left: 3px solid #505b93; color: #1b1b18;' : 'border-left: 3px solid transparent;' }}">
                                            <div class="flex items-center gap-3">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                                <span>Rates</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </nav>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto">
                    <!-- Page Header -->
                    @isset($header)
                        <header class="border-b border-gray-200 bg-white px-6 py-4 lg:px-8">
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
