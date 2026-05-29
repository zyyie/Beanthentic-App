/**
 * On the dev PC, POST to your own LAN IP (192.168.x.x) often fails on Windows.
 * This worker proxies same-origin /api/* requests to 127.0.0.1:PORT.
 */
self.addEventListener('install', function () {
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim());
});

var localApiOrigin = '';

self.addEventListener('message', function (event) {
  var data = event.data || {};
  if (data.type === 'setLocalOrigin' && data.origin) {
    localApiOrigin = String(data.origin).replace(/\/+$/, '');
  }
});

self.addEventListener('fetch', function (event) {
  if (!localApiOrigin) return;
  var req = event.request;
  var url;
  try {
    url = new URL(req.url);
  } catch (_e) {
    return;
  }
  if (url.pathname.indexOf('/api/') !== 0) return;

  var targetUrl = localApiOrigin + url.pathname + url.search;
  event.respondWith(
    fetch(
      new Request(targetUrl, {
        method: req.method,
        headers: req.headers,
        body: req.method === 'GET' || req.method === 'HEAD' ? undefined : req.clone().body,
        redirect: 'follow',
      })
    )
  );
});
