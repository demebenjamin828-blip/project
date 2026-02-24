const CACHE_NAME = 'elohim-v1';

// On n'oblige pas le cache pour l'instant pour ne pas bloquer tes modifs PHP
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('fetch', (event) => {
  // Stratégie : Réseau d'abord, pour que les notes soient toujours à jour
  event.respondWith(fetch(event.request));
});