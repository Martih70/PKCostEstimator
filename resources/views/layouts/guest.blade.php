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
        <div class="min-h-screen flex items-center justify-center px-4 py-12" style="background: linear-gradient(160deg, #ebf1ff 0%, #e2eaff 40%, #e8f1ff 100%);">
            <div class="w-full max-w-sm">
                <!-- Brand -->
                <div class="flex flex-col items-center mb-8">
                    <div class="h-14 w-14 rounded-xl flex items-center justify-center text-white font-bold text-xl mb-4" style="background: linear-gradient(135deg, #505b93, #3d4269); box-shadow: 0 8px 24px rgba(80,91,147,0.45);">
                        PK
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">PK Cost Estimator</h1>
                    <p class="text-sm text-gray-500 mt-1">Pakistan Construction Cost Forecasting</p>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl px-8 py-8" style="border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 17px 68px rgba(0,0,0,0.25), 0 6px 17px rgba(0,0,0,0.14);">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
