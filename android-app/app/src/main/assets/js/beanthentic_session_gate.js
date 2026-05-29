/**
 * Session gate for Beanthentic WebView / browser.
 *
 * Set window.__BEANTHENTIC_SESSION_GATE__ before this script:
 *   - 'protected' — index, account, tutorial, etc. (requires login backed by rules below)
 *   - 'guest'     — login.php / signup.php (redirect away only if session still valid vs DB when on http)
 *
 * file:// (Android assets): clears persisted localStorage user so only sessionStorage
 * from the current app session counts (fixes "may account agad" after reinstall-like clears).
 *
 * http(s)://: optional POST api/session_verify.php so stale or orphan client data is dropped
 * when MySQL has no matching user or server is unreachable.
 */
(function (global) {
  (function loadApiUrlsHelperSync() {
    try {
      if (global.BeanthenticApiUrls || document.getElementById('beanthentic-api-urls-js')) return;
      document.write(
        '<script id="beanthentic-api-urls-js" src="js/beanthentic_api_urls.js?v=20260515-6"><\/script>'
      );
    } catch (_ld) { }
  })();

  (function loadThemeSync() {
    try {
      if (global.BeanthenticTheme || document.getElementById('beanthentic-theme-js')) return;
      document.write(
        '<script id="beanthentic-theme-js" src="js/beanthentic_theme.js?v=20260527-9"><\/script>'
      );
    } catch (_th) { }
  })();

  (function bootThemeEarly() {
    try {
      if (document.documentElement.getAttribute('data-beanthentic-theme-effective')) return;
      var t = localStorage.getItem('beanthentic_app_theme') || sessionStorage.getItem('beanthentic_app_theme') || 'light';
      var eff =
        t === 'dark'
          ? 'dark'
          : t === 'system' && global.matchMedia && global.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light';
      document.documentElement.setAttribute('data-beanthentic-theme', t);
      document.documentElement.setAttribute('data-beanthentic-theme-effective', eff);
    } catch (_bt) { }
  })();

  var mode = global.__BEANTHENTIC_SESSION_GATE__ || 'protected';
  var skipServerVerify = !!global.__BEANTHENTIC_SESSION_GATE_SKIP_VERIFY__;

  function parseUser(raw) {
    if (!raw) return null;
    try {
      var u = JSON.parse(raw);
      return u && u.email ? u : null;
    } catch (_err) {
      return null;
    }
  }

  function phpApiBase() {
    if (window.BeanthenticApiUrls && typeof window.BeanthenticApiUrls.resolveApiBase === 'function') {
      return window.BeanthenticApiUrls.resolveApiBase();
    }
    function parseUrl(s) {
      try {
        return new URL(String(s || '').trim());
      } catch (_e) {
        return null;
      }
    }
    function isLoopbackHost(host) {
      var h = String(host || '').toLowerCase();
      return h === 'localhost' || h === '127.0.0.1' || h === '10.0.2.2' || h === '[::1]';
    }
    function hostsCompatible(storedBase, pageHref) {
      var su = parseUrl(storedBase);
      var pu = parseUrl(pageHref);
      if (!su || !pu) return false;
      if (su.hostname === pu.hostname && su.port === pu.port) return true;
      if (isLoopbackHost(su.hostname) && isLoopbackHost(pu.hostname)) return true;
      return false;
    }
    var pageBase = '';
    try {
      if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
        pageBase = new URL('.', location.href).href.replace(/\/+$/, '');
      }
    } catch (_e2) { }
    try {
      var s = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
      if (s && String(s).replace(/\s/g, '')) {
        s = String(s).trim().replace(/\/+$/, '');
        if (/\/api$/i.test(s)) {
          s = s.replace(/\/api$/i, '');
        }
        if (pageBase && !hostsCompatible(s, pageBase)) {
          return pageBase;
        }
        return s;
      }
    } catch (_e) { }
    return pageBase;
  }

  function clearClientAuth() {
    var keys = [
      'beanthentic_user',
      'beanthentic_farmer_id',
      'beanthentic_farmer_id_map',
      'beanthentic_farmer_profile',
      'beanthentic_farmer_profile_map',
      'beanthentic_prompt_register_after_tutorial',
      'beanthentic_new_signup_login_id',
    ];
    for (var i = 0; i < keys.length; i += 1) {
      try {
        localStorage.removeItem(keys[i]);
        sessionStorage.removeItem(keys[i]);
      } catch (_e) { }
    }
    try {
      sessionStorage.removeItem('register_farm_registration_ok');
      sessionStorage.removeItem('register_farm_farmer_id');
    } catch (_e2) { }
  }

  function injectTopbarCriticalStyle() {
    if (document.getElementById('beanthentic-topbar-critical-style')) return;
    var st = document.createElement('style');
    st.id = 'beanthentic-topbar-critical-style';
    st.textContent =
      ':root{--beanthentic-fixed-topbar-h:calc(env(safe-area-inset-top,0px) + 5.5rem);}' +
      'body.has-app-bottom-nav,body.pi-page,body.help-page,body.contact-page,body.account-settings-page,body.reg-summary-page,' +
      'body:has(header.msg-top),body:has(header.hist-hero),body:has(header.about-hero),body:has(header.mv-hero),body:has(header.fr-reg-hero){padding-top:var(--beanthentic-fixed-topbar-h);}' +
      'body.login-page,body.signup-page,body.tutorial-page,body.choose-lang-page,body.server-url-page{padding-top:0!important;}';
    var h = document.head || document.getElementsByTagName('head')[0];
    if (h) h.appendChild(st);
  }

  function injectHideBodyStyle() {
    if (document.getElementById('beanthentic-auth-gate-style')) return;
    var st = document.createElement('style');
    st.id = 'beanthentic-auth-gate-style';
    st.textContent =
      'html.beanthentic-auth-loading body{visibility:hidden!important;}html.beanthentic-auth-loading{min-height:100vh;background:#eef6ff;}';
    var h = document.head || document.getElementsByTagName('head')[0];
    if (h) h.appendChild(st);
  }

  injectTopbarCriticalStyle();

  function revealPage() {
    try {
      document.documentElement.classList.remove('beanthentic-auth-loading');
      document.documentElement.classList.add('beanthentic-auth-ready');
    } catch (_e) { }
  }

  // Public mode: do not enforce auth redirects/verification.
  // Used by forgot-password flow (settings.php?reset=1).
  if (mode === 'public') {
    revealPage();
    return;
  }

  function verifyOnServer(user) {
    if (!user || !user.user_id) {
      return Promise.resolve({ ok: false, reason: 'no_base_or_uid' });
    }
    var payload = { user_id: user.user_id, email: user.email };
    if (global.BeanthenticApiUrls && typeof global.BeanthenticApiUrls.fetchApiSequential === 'function') {
      return global.BeanthenticApiUrls.fetchApiSequential('session_verify.php', payload, {
        timeoutMs: 3500,
        maxTries: 2,
      }).then(function (body) {
        return { ok: !!(body && body.ok === true), body: body };
      });
    }
    var base = phpApiBase();
    if (!base) {
      return Promise.resolve({ ok: false, reason: 'no_base_or_uid' });
    }
    var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var tid = setTimeout(function () {
      if (ctrl) ctrl.abort();
    }, 3500);
    return fetch(base + '/api/session_verify.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      signal: ctrl ? ctrl.signal : undefined,
    })
      .then(function (r) {
        return r.text().then(function (t) {
          var j = null;
          try {
            j = t ? JSON.parse(t) : null;
          } catch (_p) {
            j = null;
          }
          return { ok: !!(r.ok && j && j.ok === true), status: r.status, body: j };
        });
      })
      .catch(function () {
        return { ok: false, reason: 'network' };
      })
      .then(function (out) {
        clearTimeout(tid);
        return out;
      });
  }

  var proto = '';
  try {
    proto = location.protocol || '';
  } catch (_p) { }
  var isFile = proto === 'file:';
  var isHttp = proto === 'http:' || proto === 'https:';

  if (isFile) {
    try {
      localStorage.removeItem('beanthentic_user');
    } catch (_strip) { }
  }

  var localUser = parseUser(localStorage.getItem('beanthentic_user'));
  var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
  var user = localUser || sessionUser;

  if (!isFile) {
    if (localUser) {
      try {
        sessionStorage.setItem('beanthentic_user', JSON.stringify(localUser));
      } catch (_s) { }
      user = localUser;
    } else if (sessionUser) {
      try {
        localStorage.setItem('beanthentic_user', JSON.stringify(sessionUser));
      } catch (_l) { }
      user = sessionUser;
    }
  } else {
    user = sessionUser;
  }

  if (mode === 'protected') {
    if (!user) {
      try {
        window.location.replace('login.php');
      } catch (_r) { }
      return;
    }
    if (isHttp && (!user.user_id || user.user_id <= 0)) {
      clearClientAuth();
      try {
        window.location.replace('login.php');
      } catch (_r2) { }
      return;
    }
    var needsVerify = isHttp && user && user.user_id > 0 && !skipServerVerify;
    if (needsVerify) {
      injectHideBodyStyle();
      try {
        document.documentElement.classList.add('beanthentic-auth-loading');
      } catch (_c) { }
    } else {
      revealPage();
    }
  }

  if (mode === 'guest') {
    if (!user) {
      revealPage();
      return;
    }
    if (isFile) {
      try {
        window.location.replace('account.php');
      } catch (_g) { }
      return;
    }
    if (isHttp && user.user_id > 0 && !skipServerVerify) {
      injectHideBodyStyle();
      try {
        document.documentElement.classList.add('beanthentic-auth-loading');
      } catch (_c2) { }
      window.__BEANTHENTIC_GUEST_VERIFY_USER__ = user;
    } else if (isHttp) {
      clearClientAuth();
      revealPage();
    }
  }

  function finishProtectedVerify() {
    var u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
    if (!u || mode !== 'protected') {
      revealPage();
      return;
    }
    if (!isHttp || !u.user_id || skipServerVerify) {
      revealPage();
      return;
    }
    verifyOnServer(u).then(function (out) {
      if (!out.ok) {
        try {
          sessionStorage.removeItem('beanthentic_login_notice');
          localStorage.removeItem('beanthentic_login_notice');
        } catch (_n) { }
        clearClientAuth();
        try {
          window.location.replace('login.php');
        } catch (_e) { }
        return;
      }
      revealPage();
    });
  }

  function hasHttpApiBase() {
    try {
      var s =
        localStorage.getItem('beanthentic_api_base') ||
        sessionStorage.getItem('beanthentic_api_base');
      return !!(s && /^https?:\/\//i.test(String(s).trim()));
    } catch (_b) {
      return false;
    }
  }

  function injectFarmerAlertsScript() {
    if (document.getElementById('beanthentic-farmer-alerts-js')) return;
    var s = document.createElement('script');
    s.id = 'beanthentic-farmer-alerts-js';
    s.src = 'js/beanthentic_farmer_account_alerts.js?v=20260527-4';
    (document.head || document.getElementsByTagName('head')[0] || document.body).appendChild(s);
  }

  function runSuspendCheckIfNeeded() {
    if (mode !== 'protected' || !user) return;
    if (!hasHttpApiBase() && !isHttp) return;
    injectFarmerAlertsScript();
    function tryCheck() {
      if (global.BeanthenticFarmerAccountAlerts && typeof global.BeanthenticFarmerAccountAlerts.refreshAccountAlerts === 'function') {
        global.BeanthenticFarmerAccountAlerts.refreshAccountAlerts();
        return;
      }
      window.setTimeout(tryCheck, 200);
    }
    tryCheck();
  }

  function finishGuestVerify() {
    var gu = window.__BEANTHENTIC_GUEST_VERIFY_USER__;
    window.__BEANTHENTIC_GUEST_VERIFY_USER__ = null;
    if (!gu || mode !== 'guest') {
      revealPage();
      return;
    }
    verifyOnServer(gu).then(function (out) {
      if (out.ok) {
        try {
          window.location.replace('account.php');
        } catch (_e) { }
        return;
      }
      clearClientAuth();
      revealPage();
    });
  }

  if (mode === 'protected' && isHttp && user && user.user_id > 0 && !skipServerVerify) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', finishProtectedVerify);
    } else {
      finishProtectedVerify();
    }
  }

  if (mode === 'guest' && isHttp && window.__BEANTHENTIC_GUEST_VERIFY_USER__) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', finishGuestVerify);
    } else {
      finishGuestVerify();
    }
  }

  if (mode === 'protected' && user) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', runSuspendCheckIfNeeded);
    } else {
      runSuspendCheckIfNeeded();
    }
  }
})(typeof window !== 'undefined' ? window : this);

(function injectBeanthenticFixedTopbar() {
  try {
    if (document.getElementById('beanthentic-fixed-topbar-js')) return;
    var s = document.createElement('script');
    s.id = 'beanthentic-fixed-topbar-js';
    s.src = 'js/beanthentic_fixed_topbar.js';
    (document.head || document.getElementsByTagName('head')[0]).appendChild(s);
  } catch (_e) { }
})();

