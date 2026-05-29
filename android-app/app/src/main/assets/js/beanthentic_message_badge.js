/**
 * Red unread count on Messages icons (home shortcut, etc.).
 */
(function (global) {
  'use strict';

  function apiBase() {
    try {
      if (typeof global.beanthenticApiBase === 'function') {
        return String(global.beanthenticApiBase() || '').replace(/\/$/, '');
      }
    } catch (_e) {}
    try {
      var s =
        localStorage.getItem('beanthentic_flask_base') ||
        sessionStorage.getItem('beanthentic_flask_base');
      if (s && String(s).replace(/\s/g, '')) {
        return String(s).replace(/\/$/, '');
      }
    } catch (_e2) {}
    if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
      return (location.origin || '').replace(/\/$/, '');
    }
    return 'http://10.0.2.2:8080';
  }

  function currentUser() {
    try {
      var raw = sessionStorage.getItem('beanthentic_user') || localStorage.getItem('beanthentic_user');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch (_e) {
      return null;
    }
  }

  function setBadgeEl(el, count) {
    if (!el) return;
    var n = Math.max(0, parseInt(String(count), 10) || 0);
    if (n <= 0) {
      el.textContent = '';
      el.hidden = true;
      el.setAttribute('aria-hidden', 'true');
      return;
    }
    el.textContent = n > 99 ? '99+' : String(n);
    el.hidden = false;
    el.removeAttribute('aria-hidden');
  }

  function applyCount(count) {
    document.querySelectorAll('[data-message-unread-badge]').forEach(function (el) {
      setBadgeEl(el, count);
    });
  }

  function refreshMessageBadges(forceZero) {
    if (forceZero === true) {
      applyCount(0);
      return Promise.resolve(0);
    }
    var u = currentUser();
    var base = apiBase();
    if (!u || !u.user_id || !base) {
      applyCount(0);
      return Promise.resolve(0);
    }
    var url =
      base +
      '/api/chat_unread_count.php?user_id=' +
      encodeURIComponent(String(u.user_id));
    return fetch(url, { method: 'GET' })
      .then(function (r) {
        return r.json();
      })
      .then(function (j) {
        var n = j && j.ok === true ? parseInt(String(j.unread_count || 0), 10) || 0 : 0;
        applyCount(n);
        return n;
      })
      .catch(function () {
        applyCount(0);
        return 0;
      });
  }

  global.BeanthenticMessageBadge = {
    refresh: refreshMessageBadges,
    clear: function () {
      return refreshMessageBadges(true);
    },
  };

  function boot() {
    refreshMessageBadges(false);
    if (!global.__beanthenticMsgBadgePoll) {
      global.__beanthenticMsgBadgePoll = window.setInterval(function () {
        refreshMessageBadges(false);
      }, 30000);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(typeof window !== 'undefined' ? window : this);
