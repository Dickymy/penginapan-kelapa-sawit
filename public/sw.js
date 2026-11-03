// Penginapan Kelapa Sawit — Progressive Web App Service Worker (v3)
const CACHE_NAME = 'pks-cache-v3';
const PRECACHE_ASSETS = [
    '/',
    '/manifest.json',
    '/offline.html',
    '/images/logo.webp',
    '/images/hero.webp'
];

// Install: precache critical app shell assets
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_ASSETS);
        }).catch(err => {
            console.warn('[SW] Pre-caching warning:', err);
        })
    );
});

// Activate: clean up old cache versions immediately
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch handler: smart routing based on destination
self.addEventListener('fetch', event => {
    const request = event.request;

    // Only process GET requests
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Bypass caching for admin, member, payment, and auth routes
    if (url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/member') ||
        url.pathname.startsWith('/booking/pay') ||
        url.pathname.startsWith('/api/') ||
        url.pathname.includes('webhook')) {
        return;
    }

    // 1. Navigation requests (HTML pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try to serve previously cached page
                    const cachedPage = await caches.match(request);
                    if (cachedPage) return cachedPage;

                    // Fallback to offline page
                    const offlinePage = await caches.match('/offline.html');
                    return offlinePage || new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
                })
        );
        return;
    }

    // 2. Image requests (Stale-while-revalidate)
    if (request.destination === 'image' || url.pathname.match(/\.(png|jpg|jpeg|svg|webp|gif|ico)$/i)) {
        event.respondWith(
            caches.open(CACHE_NAME).then(async cache => {
                const cachedResponse = await cache.match(request);
                const fetchPromise = fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        cache.put(request, networkResponse.clone());
                    }
                    return networkResponse;
                }).catch(() => null);

                // Return cached version if available, otherwise wait for network
                return cachedResponse || fetchPromise || new Response('', { status: 404 });
            })
        );
        return;
    }

    // 3. Static assets: CSS, JS, Fonts (Cache First with network fallback)
    if (url.pathname.match(/\.(css|js|woff2?|ttf|eot)$/i)) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Default network fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
