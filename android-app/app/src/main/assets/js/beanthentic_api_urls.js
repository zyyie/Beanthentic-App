/**
 * Resolve MySQL/Flask API base and login/signup URL candidates.
 * On the dev PC (Flask injects __BEANTHENTIC_LOCAL_API_ORIGIN__), try 127.0.0.1
 * first (Windows often blocks POST to your own LAN IP). Service worker proxies
 * same-origin /api/* in the background — never block auth on SW registration.
 */
(function (global) {
  var DEFAULT_FETCH_TIMEOUT_MS = 3500;
  var DEFAULT_MAX_TRIES = 2;
  var swRegisterPromise = null;

  function parseUrl(s) {
    try {
      return new URL(String(s || '').trim());
    } catch (_e) {
      return null;
    }
  }

  function isLoopbackHost(host) {
    var h = String(host || '').toLowerCase();
    return (
      h === 'localhost' ||
      h === '127.0.0.1' ||
      h === '10.0.2.2' ||
      h === '[::1]' ||
      h === '0.0.0.0'
    );
  }

  function hostsCompatible(storedBase, pageHref) {
    var su = parseUrl(storedBase);
    var pu = parseUrl(pageHref);
    if (!su || !pu) return false;
    if (su.hostname === pu.hostname && su.port === pu.port) return true;
    if (isLoopbackHost(su.hostname) && isLoopbackHost(pu.hostname)) return true;
    return false;
  }

  function normalizeHttpApiBase(raw) {
    var s = String(raw || '').trim().replace(/\/+$/, '');
    if (!s || !/^https?:\/\//i.test(s)) return '';
    if (/\/api$/i.test(s)) s = s.replace(/\/api$/i, '');
    try {
      var u = new URL(s);
      // Flask app.py serves /api/*.php at host root (port 8080 or 5000), not under .../assets/
      if (u.port === '8080' || u.port === '5000') {
        return u.origin;
      }
    } catch (_e) {}
    return s;
  }

  function storedApiBaseRaw() {
    try {
      var s =
        localStorage.getItem('beanthentic_api_base') ||
        sessionStorage.getItem('beanthentic_api_base');
      return normalizeHttpApiBase(s);
    } catch (_e) {
      return '';
    }
  }

  function pageApiBase() {
    try {
      if (
        typeof location !== 'undefined' &&
        (location.protocol === 'http:' || location.protocol === 'https:')
      ) {
        return new URL('.', location.href).href.replace(/\/+$/, '');
      }
    } catch (_e2) {}
    return '';
  }

  function loopbackInjectedOrigin() {
    try {
      var o = global.__BEANTHENTIC_LOCAL_API_ORIGIN__;
      if (o && String(o).trim()) {
        return String(o).trim().replace(/\/+$/, '');
      }
    } catch (_e) {}
    return '';
  }

  function usesLocalApiProxy() {
    return !!loopbackInjectedOrigin();
  }

  function registerLocalApiServiceWorker() {
    if (!usesLocalApiProxy()) return Promise.resolve(null);
    if (!('serviceWorker' in navigator)) return Promise.resolve(null);
    if (swRegisterPromise) return swRegisterPromise;

    var local = loopbackInjectedOrigin();
    swRegisterPromise = navigator.serviceWorker
      .register('js/sw-beanthentic-api.js', { scope: '/' })
      .then(function (reg) {
        function sendOrigin(sw) {
          if (!sw) return;
          try {
            sw.postMessage({ type: 'setLocalOrigin', origin: local });
          } catch (_m) {}
        }
        sendOrigin(reg.installing);
        sendOrigin(reg.waiting);
        sendOrigin(reg.active);
        reg.addEventListener('updatefound', function () {
          sendOrigin(reg.installing);
        });
        return navigator.serviceWorker.ready.then(function (readyReg) {
          sendOrigin(readyReg.active);
          return readyReg;
        });
      })
      .catch(function () {
        swRegisterPromise = null;
        return null;
      });

    return swRegisterPromise;
  }

  function whenLocalApiProxyReady() {
    return registerLocalApiServiceWorker().then(function () {
      if (!usesLocalApiProxy()) return;
      if (!('serviceWorker' in navigator)) return;
      return navigator.serviceWorker.ready;
    });
  }

  function isFilePage() {
    try {
      return (location.protocol || '') === 'file:';
    } catch (_fp) {
      return false;
    }
  }

  /**
   * HTTP base for PHP api/* — required on Android assets (file:// cannot run PHP).
   * Uses saved server URL from login / server_url.php (set when Wi‑Fi/IP changes).
   */
  function resolveHttpApiBase() {
    var stored = storedApiBaseRaw();
    if (stored) return stored;
    var localOrigin = normalizeHttpApiBase(loopbackInjectedOrigin());
    if (localOrigin) return localOrigin;
    var page = normalizeHttpApiBase(pageApiBase());
    if (page) return page;
    return '';
  }

  function resolveApiBase() {
    var http = resolveHttpApiBase();
    if (http) return http;
    var page = pageApiBase();
    if (usesLocalApiProxy() && page) return page;
    return page || '';
  }

  function syncApiBaseToCurrentPage() {
    var page = pageApiBase();
    if (!page) return;
    try {
      localStorage.setItem('beanthentic_api_base', page);
      sessionStorage.setItem('beanthentic_api_base', page);
    } catch (_s) {}
    registerLocalApiServiceWorker();
  }

  function isSimpleFlaskPage() {
    try {
      var pl = (location.pathname || '').toLowerCase();
      return pl.indexOf('/assets/') < 0 && pl.indexOf('/android-app/') < 0;
    } catch (_e) {
      return false;
    }
  }

  function phpApiUrlCandidates(apiScript) {
    var out = [];
    var apiPath = 'api/' + apiScript;
    function pushU(u) {
      var s = String(u || '').trim();
      if (!s) return;
      if (out.indexOf(s) < 0) out.push(s);
    }

    var o = '';
    var pageHost = '';
    try {
      o = (location.origin || '').replace(/\/+$/, '');
      pageHost = location.hostname || '';
    } catch (_o) {}
    var onLoopback = isLoopbackHost(pageHost);
    var stored = storedApiBaseRaw();
    var local = loopbackInjectedOrigin();
    var filePage = isFilePage();
    var httpBase = resolveHttpApiBase();

    if (httpBase) {
      pushU(httpBase + '/' + apiPath);
    }

    if (local) {
      pushU(local + '/' + apiPath);
    }

    if (filePage) {
      return out;
    }

    try {
      pushU(new URL(apiPath, location.href).href);
    } catch (_e0) {}

    if (usesLocalApiProxy() || isSimpleFlaskPage()) {
      if (stored && hostsCompatible(stored, location.href)) {
        pushU(stored + '/' + apiPath);
      }
      return out;
    }

    try {
      var dir = new URL('.', location.href).href.replace(/\/+$/, '');
      if (dir) pushU(dir + '/' + apiPath);
    } catch (_e1) {}
    try {
      pushU(o + '/' + apiPath);
    } catch (_y) {}

    if (stored && hostsCompatible(stored, location.href)) {
      pushU(stored + '/' + apiPath);
    }

    var p = '';
    try {
      p = location.pathname || '';
    } catch (_p2) {}
    var pl = p.toLowerCase();
    [
      '/android-app/app/src/main/assets/',
      '/beanthentic-app/android-app/app/src/main/assets/',
    ].forEach(function (key) {
      var ix = pl.indexOf(key);
      if (ix >= 0) pushU(o + p.substring(0, ix + key.length - 1) + '/' + apiPath);
    });
    var ixA = pl.lastIndexOf('/assets/');
    if (ixA >= 0) pushU(o + p.substring(0, ixA + '/assets'.length) + '/' + apiPath);

    if (onLoopback) {
      pushU('http://localhost/Beanthentic-App/android-app/app/src/main/assets/' + apiPath);
      pushU('http://127.0.0.1/Beanthentic-App/android-app/app/src/main/assets/' + apiPath);
    }

    return out;
  }

  function postJsonWithTimeout(url, payload, timeoutMs) {
    timeoutMs = timeoutMs || DEFAULT_FETCH_TIMEOUT_MS;
    var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var tid = null;
    if (ctrl) {
      tid = setTimeout(function () {
        try {
          ctrl.abort();
        } catch (_a) {}
      }, timeoutMs);
    }
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload || {}),
      signal: ctrl ? ctrl.signal : undefined,
    })
      .then(function (res) {
        return res.text().then(function (txt) {
          var body = null;
          try {
            body = txt ? JSON.parse(txt) : null;
          } catch (_p) {
            body = null;
          }
          return { okHttp: !!res.ok, body: body };
        });
      })
      .finally(function () {
        if (tid) clearTimeout(tid);
      });
  }

  function isApiJsonBody(body) {
    return body && typeof body === 'object' && Object.prototype.hasOwnProperty.call(body, 'ok');
  }

  function getJsonWithTimeout(url, timeoutMs) {
    timeoutMs = timeoutMs || DEFAULT_FETCH_TIMEOUT_MS;
    var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var tid = null;
    if (ctrl) {
      tid = setTimeout(function () {
        try {
          ctrl.abort();
        } catch (_a) {}
      }, timeoutMs);
    }
    return fetch(url, {
      method: 'GET',
      headers: { Accept: 'application/json' },
      signal: ctrl ? ctrl.signal : undefined,
    })
      .then(function (res) {
        return res.text().then(function (txt) {
          var body = null;
          try {
            body = txt ? JSON.parse(txt) : null;
          } catch (_p) {
            body = null;
          }
          return { okHttp: !!res.ok, body: body };
        });
      })
      .finally(function () {
        if (tid) clearTimeout(tid);
      });
  }

  function fetchGetApiSequential(apiScript, options) {
    options = options || {};
    var timeoutMs = options.timeoutMs || DEFAULT_FETCH_TIMEOUT_MS;
    var maxTries = options.maxTries || DEFAULT_MAX_TRIES;

    if (usesLocalApiProxy()) {
      registerLocalApiServiceWorker();
    }

    var urls = phpApiUrlCandidates(apiScript);
    if (!urls.length) {
      return Promise.resolve({ ok: false, skipped: true, error: 'Cannot reach server API.' });
    }

    var limit = Math.min(urls.length, Math.max(1, maxTries));
    var i = 0;

    function tryNext() {
      if (i >= limit) {
        return Promise.resolve({ ok: false, skipped: true, error: 'Cannot reach server API.' });
      }
      var url = urls[i++];
      return getJsonWithTimeout(url, timeoutMs)
        .then(function (resObj) {
          var body = resObj && resObj.body;
          if (isApiJsonBody(body) && body.ok === true) {
            return body;
          }
          return tryNext();
        })
        .catch(function () {
          return tryNext();
        });
    }

    return tryNext();
  }

  function fetchApiSequential(apiScript, payload, options) {
    options = options || {};
    var timeoutMs = options.timeoutMs || DEFAULT_FETCH_TIMEOUT_MS;
    var maxTries = options.maxTries || DEFAULT_MAX_TRIES;

    if (usesLocalApiProxy()) {
      registerLocalApiServiceWorker();
    }

    var urls = phpApiUrlCandidates(apiScript);
    if (!urls.length) {
      return Promise.resolve({ ok: false, skipped: true, error: 'Cannot reach server API.' });
    }

    var limit = Math.min(urls.length, Math.max(1, maxTries));
    var i = 0;

    function tryNext() {
      if (i >= limit) {
        return Promise.resolve({ ok: false, skipped: true, error: 'Cannot reach server API.' });
      }
      var url = urls[i++];
      return postJsonWithTimeout(url, payload, timeoutMs)
        .then(function (resObj) {
          var body = resObj && resObj.body;
          if (isApiJsonBody(body)) {
            return body;
          }
          return tryNext();
        })
        .catch(function () {
          return tryNext();
        });
    }

    return tryNext();
  }

  syncApiBaseToCurrentPage();

  global.BeanthenticApiUrls = {
    resolveApiBase: resolveApiBase,
    resolveHttpApiBase: resolveHttpApiBase,
    isFilePage: isFilePage,
    phpApiUrlCandidates: phpApiUrlCandidates,
    syncApiBaseToCurrentPage: syncApiBaseToCurrentPage,
    hostsCompatible: hostsCompatible,
    postJsonWithTimeout: postJsonWithTimeout,
    fetchApiSequential: fetchApiSequential,
    fetchGetApiSequential: fetchGetApiSequential,
    whenLocalApiProxyReady: whenLocalApiProxyReady,
    usesLocalApiProxy: usesLocalApiProxy,
    loopbackInjectedOrigin: loopbackInjectedOrigin,
    DEFAULT_FETCH_TIMEOUT_MS: DEFAULT_FETCH_TIMEOUT_MS,
  };
})(typeof window !== 'undefined' ? window : this);
