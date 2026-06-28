<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        <meta name="theme-color" content="#6366f1">
        <meta name="msapplication-TileColor" content="#6366f1">
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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')

        {{-- PWA Service Worker Registration --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('/sw.js').then(function (reg) {
                        console.log('[PWA] Service Worker registered', reg.scope);
                    }, function (err) {
                        console.warn('[PWA] Service Worker registration failed:', err);
                    });
                });
            }
        </script>
    </body>
</html>
