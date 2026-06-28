<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="@auth {{ Auth::user()?->setting?->theme ?? 'light' }} @endauth"
      data-user-theme="@auth {{ Auth::user()?->setting?->theme ?? 'auto' }} @endauth"
      data-user-theme-color="@auth {{ Auth::user()?->setting?->theme_color ?? 'indigo' }} @endauth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-seo-meta :title="$title ?? null" :description="$seoDescription ?? null" />

        {{-- PWA / Home Screen Widget Meta Tags --}}
        <meta name="application-name" content="LifeCheck">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="LifeCheck">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#4f46e5">
        <meta name="msapplication-TileColor" content="#4f46e5">
        <meta name="msapplication-TileImage" content="/icons/icon-144x144.png">

        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
        <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/icons/icon-96x96.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
        <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Breadcrumbs -->
            @isset($breadcrumbs)
                <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                        <x-breadcrumbs :items="$breadcrumbs" />
                    </div>
                </div>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                        LifeCheck &mdash; {{ __('Suivez votre bien-être au quotidien') }}
                    </p>
                </div>
            </footer>
        </div>

        @stack('scripts')

        {{-- PWA Service Worker Registration --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('/sw.js').then(function (reg) {
                        console.log('[PWA] SW registered', reg.scope);
                    }, function (err) {
                        console.warn('[PWA] SW failed:', err);
                    });
                });
            }
        </script>
    </body>
</html>
