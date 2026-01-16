// Service Worker for image caching
const CACHE_NAME = 'driving-test-images-v1';
const IMAGE_CACHE_NAME = 'driving-test-images-v1';

// Image paths to cache (WebP optimized images)
const IMAGE_PATHS = [
    '/images/ticket_images_webp/',
    '/images/ticket_images_custom_webp/',
    '/images/signs/',
];

// Install event - cache initial resources
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME && name !== IMAGE_CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Only cache image requests from our image paths
    const isImageRequest = IMAGE_PATHS.some((path) => url.pathname.startsWith(path));

    if (isImageRequest) {
        event.respondWith(
            caches.open(IMAGE_CACHE_NAME).then((cache) => {
                return cache.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        // Return cached response immediately
                        return cachedResponse;
                    }

                    // Fetch from network and cache
                    return fetch(event.request).then((networkResponse) => {
                        // Only cache successful responses
                        if (networkResponse.ok) {
                            cache.put(event.request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => {
                        // If network fails and no cache, return a placeholder or error
                        return new Response('Image not available', { status: 503 });
                    });
                });
            })
        );
    }
});

// Listen for messages to clear cache
self.addEventListener('message', (event) => {
    if (event.data === 'clearCache') {
        caches.delete(IMAGE_CACHE_NAME).then(() => {
            console.log('Image cache cleared');
        });
    }
});
