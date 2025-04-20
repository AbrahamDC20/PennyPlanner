self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('budget-buddy-cache').then((cache) => {
            return cache.addAll([
                '/',
                '/assets/styles.css',
                '/views/index.php',
                '/views/transactions.php',
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
