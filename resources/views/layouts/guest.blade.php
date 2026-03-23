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
    <body class="font-sans antialiased" style="background: linear-gradient(135deg, #f7fcff 0%, #f0f7ff 100%); color: #1b1b18; min-height: 100vh;">
        <div class="min-h-screen flex items-center justify-center px-4 py-12" style="background: linear-gradient(160deg, #f4f6ff 0%, #eef3ff 40%, #f2f6ff 100%);">
            <div class="w-full max-w-sm">
                <!-- Brand -->
                <div class="flex flex-col items-center mb-8">
                    <x-application-logo class="h-16 w-16 mb-4" style="filter: drop-shadow(0 6px 16px rgba(80,91,147,0.5));"/>
                    <h1 class="text-2xl font-bold tracking-tight">
                        <span style="color: #505b93;">PK</span><span class="text-gray-900"> Cost Estimator</span>
                    </h1>
                    <p class="text-sm text-gray-400 mt-1 tracking-wide">Pakistan Construction Cost Forecasting</p>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl px-8 py-8" style="border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 22px 80px rgba(0,0,0,0.32), 0 8px 24px rgba(0,0,0,0.18);">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
