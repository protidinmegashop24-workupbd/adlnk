// Minimal service worker — exists mainly to satisfy browsers' "installable"
// criteria for klikwit's PWA (Add to Home Screen / Install App). It does
// not cache pages: this site is dynamic (login state, live link stats,
// admin content), and stale cached HTML would be worse than no offline
// support at all. It just passes every request straight through to the
// network, same as if there were no service worker in between.

const CACHE_NAME = 'klikwit-shell-v1';
const SHELL_ASSETS = [
  '/images/logo-mark.png',
  '/android-chrome-192x192.png',
  '/android-chrome-512x512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS)).catch(() => {})
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((names) => Promise.all(
      names.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  const isShellAsset = SHELL_ASSETS.includes(url.pathname);

  if (!isShellAsset) {
    return; // let the browser handle it normally (network, no caching)
  }

  event.respondWith(
    caches.match(event.request).then((cached) => cached || fetch(event.request))
  );
});
