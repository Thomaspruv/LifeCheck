// LifeCheck Service Worker
// Version: 1.0
const CACHE_NAME = 'lifecheck-cache-v1';

// Assets to cache on install
const PRECACHE_URLS = [
    '/',
    '/dashboard',
    '/login',
    '/register',
];

// Install event — precache key assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_URLS);
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// Activate event — clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Fetch event — network-first for HTML pages, cache-first for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and non-http(s) requests
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // Skip API and dynamic data endpoints
    if (url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/_debugbar') ||
        url.pathname.startsWith('/telegram') ||
        url.pathname.startsWith('/language')) {
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts)
    if (
        url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?|ttf|eot)$/) ||
        url.pathname.startsWith('/build/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname === '/manifest.json'
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                return cached || fetch(request).then((response) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, response.clone());
                        return response;
                    });
                });
            })
        );
        return;
    }

    // Network-first for HTML pages
    event.respondWith(
        fetch(request)
            .then((response) => {
                return caches.open(CACHE_NAME).then((cache) => {
                    cache.put(request, response.clone());
                    return response;
                });
            })
            .catch(() => {
                return caches.match(request).then((cached) => {
                    return cached || caches.match('/dashboard');
                });
            })
    );
});
