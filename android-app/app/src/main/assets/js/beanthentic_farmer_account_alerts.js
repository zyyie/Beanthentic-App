/**
 * Farmer warning / suspend popups — homepage only (index.php).
 * Works on file:///android_asset/ via beanthentic_api_base (XAMPP HTTP).
 */
(function (global) {
  'use strict';

  var POLL_MS = 15000;
  var booted = false;

  function readUser() {
    try {
      var raw =
        sessionStorage.getItem('beanthentic_user') ||
        localStorage.getItem('beanthentic_user');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch (_e) {
      return null;
    }
  }

  function writeUser(user) {
    if (!user) return;
    try {
      var raw = JSON.stringify(user);
      sessionStorage.setItem('beanthentic_user', raw);
      localStorage.setItem('beanthentic_user', raw);
    } catch (_w) {}
  }

  function loginKeyFromUser(user) {
    if (!user) return '';
    var e = user.email != null ? String(user.email).trim() : '';
    if (e) return e;
    var ph = user.phone_number != null ? String(user.phone_number).trim() : '';
    if (ph) return ph;
    var lg = user.login != null ? String(user.login).trim() : '';
    return lg;
  }

  function resolveFarmerId(user) {
    if (!user) return 0;
    var fid = parseInt(String(user.farmer_id || '0'), 10);
    if (fid > 0) return fid;
    if (global.BeanthenticClientWeb && typeof global.BeanthenticClientWeb.resolveFarmerId === 'function') {
      return global.BeanthenticClientWeb.resolveFarmerId(user, loginKeyFromUser(user)) || 0;
    }
    try {
      var n = parseInt(
        sessionStorage.getItem('beanthentic_farmer_id') ||
          localStorage.getItem('beanthentic_farmer_id') ||
          '0',
        10
      );
      return n > 0 ? n : 0;
    } catch (_f) {
      return 0;
    }
  }

  function warningSeenKey(user, w) {
    var uid = user && user.user_id != null ? String(user.user_id) : '0';
    if (w && w.token) return 'beanthentic_warning_seen_' + uid + '_' + String(w.token);
    return (
      'beanthentic_warning_seen_' +
      uid +
      '_' +
      String((w && w.count) || 0) +
      '_' +
      String((w && w.at) || '') +
      '_' +
      String((w && w.message) || '').slice(0, 80)
    );
  }

  function parseModerationMessage(raw) {
    var msg = String(raw || '').trim();
    if (!msg) return { category: '', detail: '', full: '' };
    var category = '';
    var detail = msg;
    var colon = msg.indexOf(':');
    if (colon > 0 && colon < 80) {
      category = msg.slice(0, colon).trim();
      detail = msg.slice(colon + 1).trim();
      if (!detail) detail = category;
    }
    return { category: category, detail: detail, full: msg };
  }

  function formatNoticeWhen(at) {
    if (!at) return '';
    var s = String(at).replace('T', ' ').trim();
  var m = s.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
    if (!m) return s;
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var h = parseInt(m[4], 10);
    var ampm = h >= 12 ? 'PM' : 'AM';
    var h12 = h % 12 || 12;
    return months[parseInt(m[2], 10) - 1] + ' ' + parseInt(m[3], 10) + ', ' + m[1] + ' · ' + h12 + ':' + m[5] + ' ' + ampm;
  }

  function fillReasonBlock(categoryEl, textEl, rawMessage) {
    var parsed = parseModerationMessage(rawMessage);
    if (categoryEl) {
      if (parsed.category) {
        categoryEl.textContent = parsed.category;
        categoryEl.hidden = false;
        categoryEl.removeAttribute('hidden');
      } else {
        categoryEl.textContent = '';
        categoryEl.hidden = true;
        categoryEl.setAttribute('hidden', '');
      }
    }
    if (textEl) {
      textEl.textContent = parsed.category ? parsed.detail : parsed.full;
    }
  }

  function showWarningPopup(user, w) {
    if (!w || !w.message) return;
    var key = warningSeenKey(user, w);
    try {
      if (sessionStorage.getItem(key) === '1') return;
    } catch (_s) {}

    var modal = document.getElementById('farmer-warning-modal');
    var categoryEl = document.getElementById('farmer-warning-category');
    var text = document.getElementById('farmer-warning-text');
    var meta = document.getElementById('farmer-warning-meta');
    var ok = document.getElementById('farmer-warning-ok');
    if (!modal || !text) return;

    fillReasonBlock(categoryEl, text, w.message);
    if (meta) {
      var parts = [];
      if (w.count != null && Number(w.count) > 0) {
        parts.push('Warning #' + String(w.count));
      }
      var when = formatNoticeWhen(w.at);
      if (when) parts.push('Issued ' + when);
      meta.textContent = parts.join(' · ');
      meta.hidden = !parts.length;
      if (!parts.length) meta.setAttribute('hidden', '');
      else meta.removeAttribute('hidden');
    }
    modal.removeAttribute('hidden');
    modal.style.display = 'flex';

    function close() {
      modal.style.display = 'none';
      modal.setAttribute('hidden', '');
      try {
        sessionStorage.setItem(key, '1');
      } catch (_k) {}
    }
    if (ok) ok.onclick = close;
    modal.querySelectorAll('[data-farmer-modal-close]').forEach(function (el) {
      el.onclick = close;
    });
  }

  function clearClientAuth() {
    var keys = [
      'beanthentic_user',
      'beanthentic_farmer_id',
      'beanthentic_farmer_id_map',
      'beanthentic_farmer_profile',
      'beanthentic_farmer_profile_map',
    ];
    keys.forEach(function (k) {
      try {
        localStorage.removeItem(k);
        sessionStorage.removeItem(k);
      } catch (_e) {}
    });
  }

  function buildSuspendNotice(s) {
    var msg = (s && s.message) ? String(s.message) : '';
    if (!msg && s && s.reason) msg = String(s.reason);
    if (!msg) {
      msg = 'Your account is suspended. You cannot use the app until access is restored.';
    }
    return msg;
  }

  function redirectToLoginSuspended() {
    try {
      sessionStorage.removeItem('beanthentic_login_notice');
      localStorage.removeItem('beanthentic_login_notice');
    } catch (_n) {}
    clearClientAuth();
    try {
      window.location.replace('login.php');
    } catch (_e) {
      window.location.href = 'login.php';
    }
  }

  function showSuspendPopup(s, blocking) {
    if (!s || !s.message) return;
    var modal = document.getElementById('farmer-suspend-modal');
    var categoryEl = document.getElementById('farmer-suspend-category');
    var text = document.getElementById('farmer-suspend-text');
    var until = document.getElementById('farmer-suspend-until');
    var ok = document.getElementById('farmer-suspend-ok');
    if (!modal || !text) {
      redirectToLoginSuspended();
      return;
    }

    fillReasonBlock(categoryEl, text, s.message || s.reason || '');
    if (until) {
      until.textContent = s.until
        ? 'Access restores on ' + formatNoticeWhen(s.until) + '. You cannot use the app until then.'
        : 'Please contact the Beanthentic administrator for assistance.';
    }
    modal.classList.toggle('farmer-account-modal--blocking', !!blocking);
    document.documentElement.classList.toggle('beanthentic-account-suspended', !!blocking);
    modal.removeAttribute('hidden');
    modal.style.display = 'flex';

    function closeToLogin() {
      redirectToLoginSuspended();
    }
    if (ok) {
      ok.textContent = blocking ? 'Return to sign in' : 'I understand';
      ok.onclick = blocking ? function () { closeToLogin(); } : function () {
        modal.style.display = 'none';
        modal.setAttribute('hidden', '');
        if (blocking) redirectToLoginSuspended();
      };
    }
    modal.querySelectorAll('[data-farmer-modal-close]').forEach(function (el) {
      el.onclick = blocking ? closeToLogin : function () {
        modal.style.display = 'none';
        modal.setAttribute('hidden', '');
      };
    });
    if (blocking) {
      modal.querySelectorAll('.farmer-account-modal__backdrop').forEach(function (el) {
        el.onclick = null;
      });
    }
  }

  function enforceSuspendedBlock(s) {
    showSuspendPopup(s, true);
  }

  function statusQuery(user) {
    var parts = [];
    if (user.user_id != null && Number(user.user_id) > 0) {
      parts.push('user_id=' + encodeURIComponent(String(user.user_id)));
    }
    var login = loginKeyFromUser(user);
    if (login) {
      parts.push('login=' + encodeURIComponent(login));
    }
    var fid = resolveFarmerId(user);
    if (fid > 0) {
      parts.push('farmer_id=' + encodeURIComponent(String(fid)));
    }
    return 'farmer_account_status.php?' + parts.join('&');
  }

  function hydrateUserFromServer(user) {
    if (!user) return Promise.resolve(null);
    var login = loginKeyFromUser(user);
    var hasIds =
      Number(user.user_id) > 0 && (Number(user.farmer_id) > 0 || resolveFarmerId(user) > 0);
    if (hasIds) return Promise.resolve(user);
    if (!login && !(Number(user.user_id) > 0)) return Promise.resolve(user);

    var api = global.BeanthenticApiUrls;
    if (!api || typeof api.fetchApiSequential !== 'function') {
      return Promise.resolve(user);
    }

    var payload = { user_id: Number(user.user_id) || 0, login: login };
    if (login.indexOf('@') >= 0) payload.email = login;
    else if (login) payload.phone_number = login;

    return api
      .fetchApiSequential('registration_status.php', payload, { timeoutMs: 6000, maxTries: 4 })
      .then(function (body) {
        if (!body || body.ok !== true) return user;
        if (body.user_id != null) user.user_id = body.user_id;
        if (body.farmer_id != null) {
          user.farmer_id = body.farmer_id;
          if (
            global.BeanthenticClientWeb &&
            typeof global.BeanthenticClientWeb.persistFarmerIdForUser === 'function'
          ) {
            global.BeanthenticClientWeb.persistFarmerIdForUser(user, body.farmer_id, login);
          }
        }
        writeUser(user);
        return user;
      })
      .catch(function () {
        return user;
      });
  }

  function fetchAccountAlerts(user) {
    if (!user) return Promise.resolve(null);
    var login = loginKeyFromUser(user);
    if (!(Number(user.user_id) > 0) && !login) return Promise.resolve(null);

    var api = global.BeanthenticApiUrls;
    var script = statusQuery(user);

    if (api && typeof api.fetchGetApiSequential === 'function') {
      return api.fetchGetApiSequential(script, { timeoutMs: 8000, maxTries: 6 });
    }

    var base =
      api && typeof api.resolveHttpApiBase === 'function' ? api.resolveHttpApiBase() : '';
    var urls = [];
    if (base) urls.push(String(base).replace(/\/+$/, '') + '/api/' + script);
    if (api && typeof api.phpApiUrlCandidates === 'function') {
      urls = urls.concat(api.phpApiUrlCandidates(script));
    }
    if (!urls.length) urls.push('api/' + script);

    var i = 0;
    function tryNext() {
      if (i >= urls.length) return Promise.resolve(null);
      var url = urls[i++];
      return fetch(url, { method: 'GET', headers: { Accept: 'application/json' } })
        .then(function (r) {
          return r.json();
        })
        .then(function (body) {
          if (body && body.ok === true) return body;
          return tryNext();
        })
        .catch(function () {
          return tryNext();
        });
    }
    return tryNext();
  }

  function applyAccountAlerts(user, body) {
    if (!user || !body) return;
    if (body.user_id != null && Number(body.user_id) > 0) {
      user.user_id = body.user_id;
    }
    if (body.farmer_id != null && Number(body.farmer_id) > 0) {
      user.farmer_id = body.farmer_id;
    }
    user.account_warning = body.account_warning || null;
    user.account_suspended = body.account_suspended || null;
    writeUser(user);

    if (body.account_suspended && body.account_suspended.message) {
      enforceSuspendedBlock(body.account_suspended);
      return;
    }
    if (body.account_warning && body.account_warning.message) {
      showWarningPopup(user, body.account_warning);
    }
  }

  function refreshAccountAlerts() {
    var user = readUser();
    if (!user) return Promise.resolve();
    return hydrateUserFromServer(user).then(function (u) {
      if (!u) return;
      return fetchAccountAlerts(u).then(function (body) {
        if (body) applyAccountAlerts(u, body);
      });
    });
  }

  function startPolling() {
    if (booted) {
      refreshAccountAlerts();
      return;
    }
    booted = true;
    refreshAccountAlerts();
    global.setInterval(refreshAccountAlerts, POLL_MS);
    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) refreshAccountAlerts();
    });
    window.addEventListener('beanthentic-home-loader-hidden', function () {
      window.setTimeout(refreshAccountAlerts, 400);
    });
    window.setEventListener('focus', refreshAccountAlerts);
  }

  function boot() {
    var delays = [0, 800, 2500, 6000];
    delays.forEach(function (ms) {
      window.setTimeout(refreshAccountAlerts, ms);
    });

    var ready =
      global.BeanthenticApiUrls && typeof global.BeanthenticApiUrls.whenLocalApiProxyReady === 'function'
        ? global.BeanthenticApiUrls.whenLocalApiProxyReady()
        : Promise.resolve();
    ready.then(startPolling).catch(startPolling);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  global.BeanthenticFarmerAccountAlerts = {
    refreshAccountAlerts: refreshAccountAlerts,
    applyAccountAlerts: applyAccountAlerts,
  };
})(typeof window !== 'undefined' ? window : this);
