const CACHE_NAME = 'pks-cache-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
  '/offline.html',
  // Usually we'd cache css/js but Vite hashes them.
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request).catch(() => caches.match('/offline.html'));
      })
  );
});
