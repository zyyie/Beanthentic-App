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
(function () {
  var mode = window.__BEANTHENTIC_SESSION_GATE__ || 'protected';
  var skipServerVerify = !!window.__BEANTHENTIC_SESSION_GATE_SKIP_VERIFY__;

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
    try {
      var s = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
      if (s && String(s).replace(/\s/g, '')) {
        s = String(s).trim().replace(/\/+$/, '');
        // Accept either ".../assets" or legacy ".../assets/api" (session_verify adds /api/...).
        if (/\/api$/i.test(s)) {
          s = s.replace(/\/api$/i, '');
        }
        return s;
      }
    } catch (_e) {}
    try {
      if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
        var base = new URL('.', location.href).href;
        return String(base || '').replace(/\/+$/, '');
      }
    } catch (_e2) {}
    return '';
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
      } catch (_e) {}
    }
    try {
      sessionStorage.removeItem('register_farm_registration_ok');
      sessionStorage.removeItem('register_farm_farmer_id');
    } catch (_e2) {}
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

  function revealPage() {
    try {
      document.documentElement.classList.remove('beanthentic-auth-loading');
      document.documentElement.classList.add('beanthentic-auth-ready');
    } catch (_e) {}
  }

  function verifyOnServer(user) {
    var base = phpApiBase();
    if (!base || !user || !user.user_id) {
      return Promise.resolve({ ok: false, reason: 'no_base_or_uid' });
    }
    var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var tid = setTimeout(function () {
      if (ctrl) ctrl.abort();
    }, 12000);
    return fetch(base + '/api/session_verify.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: user.user_id, email: user.email }),
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
  } catch (_p) {}
  var isFile = proto === 'file:';
  var isHttp = proto === 'http:' || proto === 'https:';

  if (isFile) {
    try {
      localStorage.removeItem('beanthentic_user');
    } catch (_strip) {}
  }

  var localUser = parseUser(localStorage.getItem('beanthentic_user'));
  var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
  var user = localUser || sessionUser;

  if (!isFile) {
    if (localUser) {
      try {
        sessionStorage.setItem('beanthentic_user', JSON.stringify(localUser));
      } catch (_s) {}
      user = localUser;
    } else if (sessionUser) {
      try {
        localStorage.setItem('beanthentic_user', JSON.stringify(sessionUser));
      } catch (_l) {}
      user = sessionUser;
    }
  } else {
    user = sessionUser;
  }

  if (mode === 'protected') {
    if (!user) {
      try {
        window.location.replace('login.php');
      } catch (_r) {}
      return;
    }
    if (isHttp && (!user.user_id || user.user_id <= 0)) {
      clearClientAuth();
      try {
        window.location.replace('login.php');
      } catch (_r2) {}
      return;
    }
    var needsVerify = isHttp && user && user.user_id > 0 && !skipServerVerify;
    if (needsVerify) {
      injectHideBodyStyle();
      try {
        document.documentElement.classList.add('beanthentic-auth-loading');
      } catch (_c) {}
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
      } catch (_g) {}
      return;
    }
    if (isHttp && user.user_id > 0 && !skipServerVerify) {
      injectHideBodyStyle();
      try {
        document.documentElement.classList.add('beanthentic-auth-loading');
      } catch (_c2) {}
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
        clearClientAuth();
        try {
          window.location.replace('login.php');
        } catch (_e) {}
        return;
      }
      revealPage();
    });
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
        } catch (_e) {}
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
})();

(function injectBeanthenticFixedTopbar() {
  try {
    if (document.getElementById('beanthentic-fixed-topbar-js')) return;
    var s = document.createElement('script');
    s.id = 'beanthentic-fixed-topbar-js';
    s.src = 'js/beanthentic_fixed_topbar.js';
    s.defer = true;
    (document.head || document.getElementsByTagName('head')[0]).appendChild(s);
  } catch (_e) {}
})();
