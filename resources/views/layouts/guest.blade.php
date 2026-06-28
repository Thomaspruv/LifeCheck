<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-user-theme-color="indigo">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LifeCheck') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-primary-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                {{-- Logo --}}
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 font-bold text-2xl">
                        <x-application-logo class="h-10 w-auto" />
                        <span>LifeCheck</span>
                    </a>
                </div>

                {{-- Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-card border border-gray-200 dark:border-gray-700 p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
