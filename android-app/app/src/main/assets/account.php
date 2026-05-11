<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Account · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .account-portfolio-page header .nav {
      display: flex;
      align-items: center;
      position: relative;
      justify-content: center;
      min-height: 4.2rem;
      width: 100%;
    }
    .account-portfolio-page .account-header-back {
      position: absolute;
      left: 0.7rem;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      line-height: 0;
    }
    .account-portfolio-page .account-header-back svg {
      width: 24px;
      height: 24px;
    }
    .account-portfolio-page .account-header-title {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      margin: 0;
      color: #ffffff;
      font-size: 1.65rem;
      line-height: 1;
      font-weight: 800;
      letter-spacing: -0.01em;
      text-align: center;
      white-space: nowrap;
    }
    body.account-portfolio-page {
      background: #eef6ff;
    }
    .account-simple-main {
      width: min(100%, 520px);
      margin: 0 auto;
      padding: 1rem 0.72rem 6.2rem;
      box-sizing: border-box;
    }
    .account-dash {
      background: #ffffff;
      border: 1px solid rgba(15, 23, 42, 0.08);
      border-radius: 18px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
      padding: 0.5rem 0.5rem 1.05rem;
    }
    .account-dash-top {
      text-align: center;
      padding: 0.25rem 0.25rem 0.45rem;
    }
    .account-simple-avatar {
      width: 128px;
      height: 128px;
      border-radius: 999px;
      background: #d9d9d9;
      display: grid;
      place-items: center;
      margin: 0 auto 0.68rem;
    }
    .account-simple-avatar span {
      font-weight: 800;
      font-size: 1.7rem;
      color: rgba(17, 24, 39, 0.5);
      letter-spacing: 0.02em;
    }
    .account-simple-name {
      margin: 0;
      font-size: 2rem;
      font-weight: 800;
      color: #111827;
      line-height: 1.06;
      letter-spacing: -0.01em;
    }
    .account-simple-phone {
      margin: 0.15rem 0 0;
      font-size: 0.72rem;
      color: #1f2937;
      font-style: italic;
      line-height: 1.2;
    }
    .account-simple-status {
      margin: 0.12rem 0 0;
      font-size: 0.72rem;
      font-weight: 700;
      color: #6b7280;
      line-height: 1.1;
    }
    .account-filter-row {
      margin: 0.65rem 0 0.75rem;
      display: flex;
      justify-content: center;
      gap: 0.48rem;
      flex-wrap: wrap;
    }
    .account-filter-pill {
      border: 0;
      border-radius: 999px;
      min-height: 34px;
      padding: 0.38rem 1rem;
      font-size: 0.82rem;
      font-weight: 700;
      color: #4b5563;
      background: #e5e7eb;
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
    }
    .account-filter-count {
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.02em;
      color: #1f2937;
      opacity: 0.9;
    }
    .account-filter-pill.is-active {
      color: #ffffff;
      background: #145e1e;
    }
    .account-filter-pill.is-active .account-filter-count {
      color: rgba(255, 255, 255, 0.95);
      opacity: 0.95;
    }
    .account-stats {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 145px));
      gap: 0.35rem;
      justify-content: center;
    }
    .account-stat {
      border-radius: 12px;
      padding: 0.4rem 0.4rem 0.42rem;
      min-height: 92px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .account-stat-title {
      margin: 0;
      font-size: 0.56rem;
      font-weight: 700;
      line-height: 1.2;
      color: #0f172a;
    }
    .account-stat-sub {
      margin: 0 0 0.28rem;
      font-size: 0.45rem;
      font-style: italic;
      color: #9ca3af;
      line-height: 1.1;
    }
    .account-stat-value {
      margin: 0;
      font-size: 1.8rem;
      line-height: 0.95;
      font-weight: 800;
      letter-spacing: -0.01em;
      color: #ffffff;
      text-transform: uppercase;
    }
    .account-stat--total {
      background: #145e1e;
    }
    .account-stat--total .account-stat-title {
      color: #ffffff;
    }
    .account-stat--total .account-stat-sub {
      color: #d9efe0;
    }
    .account-stat--remaining {
      background: #dfe8e0;
    }
    .account-stat--remaining .account-stat-value {
      color: #145e1e;
    }
    .account-qr-wrap {
      margin-top: 0.95rem;
      display: flex;
      justify-content: center;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
      background: #ffffff;
      border: 1px solid rgba(17, 24, 39, 0.12);
      border-radius: 12px;
      padding: 0.28rem;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .account-qr-img {
      width: 170px;
      height: 170px;
      display: block;
      object-fit: contain;
      background: #ffffff;
      border-radius: 8px;
      border: 1px solid rgba(17, 24, 39, 0.05);
      padding: 0.35rem;
      box-sizing: border-box;
    }
  </style>
</head>
<body class="has-app-bottom-nav account-portfolio-page">
  <header>
    <div class="nav">
      <button type="button" id="account-header-back" class="account-header-back" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18 9 12l6-6"/></svg>
      </button>
      <p class="account-header-title">Farmer Account</p>
    </div>
  </header>

  <main class="account-simple-main" id="account-main">
    <section class="account-dash" id="account-simple-root" hidden>
      <div class="account-dash-top">
        <div class="account-simple-avatar" aria-hidden="true">
          <span id="account-simple-initials"></span>
        </div>
        <p class="account-simple-name" id="account-simple-name">—</p>
        <p class="account-simple-phone" id="account-simple-phone">—</p>
      </div>

      <div class="account-filter-row" aria-label="Coffee filters">
        <button class="account-filter-pill is-active" type="button" data-variety="all">
          <span class="account-filter-label">All</span>
          <span class="account-filter-count" id="pill-count-all">0KG</span>
        </button>
        <button class="account-filter-pill" type="button" data-variety="liberica">
          <span class="account-filter-label">Liberica</span>
          <span class="account-filter-count" id="pill-count-liberica">0KG</span>
        </button>
        <button class="account-filter-pill" type="button" data-variety="excelsa">
          <span class="account-filter-label">Excelsa</span>
          <span class="account-filter-count" id="pill-count-excelsa">0KG</span>
        </button>
        <button class="account-filter-pill" type="button" data-variety="robusta">
          <span class="account-filter-label">Robusta</span>
          <span class="account-filter-count" id="pill-count-robusta">0KG</span>
        </button>
      </div>

      <section class="account-stats" aria-label="Beans summary">
        <article class="account-stat account-stat--total">
          <p class="account-stat-title">Initial Beans</p>
          <p class="account-stat-sub">(Total beans)</p>
          <p class="account-stat-value" id="account-total-kg">0KG</p>
        </article>
        <article class="account-stat account-stat--remaining">
          <p class="account-stat-title">Beans Remaining</p>
          <p class="account-stat-sub">(As of now)</p>
          <p class="account-stat-value" id="account-remaining-kg">0KG</p>
        </article>
      </section>

      <div class="account-qr-wrap">
        <img id="account-qr-img" class="account-qr-img" alt="Account QR code" />
      </div>
    </section>
  </main>

  <nav class="app-bottom-nav app-bottom-nav--mint" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="index.php#home" id="nav-home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <a href="qr.php" id="nav-qr" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--transaction" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 7.25h9v2H6z"/><path fill="currentColor" d="M15 6 19 8.25 15 10.5z"/><path fill="currentColor" d="M9 14.25h9v2H9z"/><path fill="currentColor" d="M9 13.25 5 15.25 9 17.25z"/></svg>
        </span>
        <span class="app-bottom-nav-label">Transaction</span>
      </a>
      <a href="register_summary.php" id="nav-register" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--pending" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--complete" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Register</span>
      </a>
      <a href="transaction-history.html" id="nav-history" class="app-bottom-nav-link app-bottom-nav-link--history">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
        </span>
        <span class="app-bottom-nav-label">History</span>
      </a>
      <a href="account.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script src="js/txn_history_store.js"></script>
  <script>
    (function () {
      var back = document.getElementById('account-header-back');
      if (!back) return;
      back.addEventListener('click', function () {
        try {
          if (window.history.length > 1) {
            window.history.back();
            return;
          }
        } catch (_e) {}
        window.location.href = 'index.php#home';
      });
    })();
  </script>
  <script>
    (function () {
      function flaskBase() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base');
          if (s && String(s).replace(/\s/g, '')) return String(s).replace(/\/$/, '');
        } catch (e) {}
        if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
          return (location.origin || '').replace(/\/$/, '');
        }
        return 'http://10.0.2.2:5000';
      }
      function fixHomeAboutFromLogin() {
        if (typeof location === 'undefined') return;
        if (location.protocol !== 'http:' && location.protocol !== 'https:') return;
        var o = (location.origin || '').replace(/\/$/, '');
        var home = document.getElementById('nav-home');
        if (home) home.setAttribute('href', o + '/#home');
        var menu = document.getElementById('bottom-nav-about-menu');
        if (menu) {
          menu.querySelectorAll('a[href*="#about-"]').forEach(function (a) {
            var href = a.getAttribute('href') || '';
            var m = href.match(/#(about-[a-z0-9-]+)$/i);
            if (m) a.setAttribute('href', o + '/#' + m[1]);
          });
        }
        var logo = document.querySelector('header a.logo');
        if (logo) logo.setAttribute('href', o + '/#home');
        document.querySelectorAll('.account-portfolio-quick a[href*="index.php#"]').forEach(function (a) {
          var href = a.getAttribute('href') || '';
          var m = href.match(/#([a-z0-9-]+)\s*$/i);
          if (m) a.setAttribute('href', o + '/#' + m[1]);
        });
        document.querySelectorAll('.account-portfolio-back-link[href*="index.php#"]').forEach(function (a) {
          var href = a.getAttribute('href') || '';
          var m = href.match(/#([a-z0-9-]+)\s*$/i);
          if (m) a.setAttribute('href', o + '/#' + m[1]);
        });
        var acc = document.getElementById('nav-signin');
        if (acc) {
          try {
            acc.setAttribute('href', new URL('account.php', o + '/').href);
          } catch (e) {
            acc.setAttribute('href', o + '/account.php');
          }
        }
      }
      function applyFlaskNav() {
        var b = flaskBase();
        document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
          var p = a.getAttribute('data-beanthentic-flask');
          if (p) a.setAttribute('href', b + p);
        });
      }
      function runNavFixes() {
        fixHomeAboutFromLogin();
        applyFlaskNav();
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', runNavFixes);
      else runNavFixes();
    })();
  </script>
  <script>
    // Bottom nav active state is handled globally in js/ui.js (syncAppBottomNavActive).
  </script>
  <script>
    (function () {
      var USER_NAME_MAP_KEY = 'beanthentic_user_name_map';

      function getKnownUserName(email) {
        var cleanEmail = String(email || '').trim().toLowerCase();
        if (!cleanEmail) return '';
        try {
          var raw = localStorage.getItem(USER_NAME_MAP_KEY) || sessionStorage.getItem(USER_NAME_MAP_KEY);
          var map = raw ? JSON.parse(raw) : {};
          return map && typeof map[cleanEmail] === 'string' ? String(map[cleanEmail]).trim() : '';
        } catch (_err) {
          return '';
        }
      }
      function normalizeLoginId(raw) {
        var s = String(raw || '').trim();
        if (!s) return '';
        if (s.indexOf('@') >= 0) return s.toLowerCase();
        var digits = s.replace(/\D/g, '');
        if (digits.indexOf('0') === 0) digits = digits.slice(1);
        if (digits.indexOf('63') === 0) digits = digits.slice(2);
        if (digits.length === 10 && digits.charAt(0) === '9') return '+63' + digits;
        return '';
      }

      function getUser() {
        function parseUser(raw) {
          if (!raw) return null;
          try {
            var u = JSON.parse(raw);
            if (u && u.email) return u;
          } catch (_err) {}
          return null;
        }
        try {
          var localUser = parseUser(localStorage.getItem('beanthentic_user'));
          if (localUser) {
            var localName = getKnownUserName(localUser.email);
            if (localName) localUser.name = localName;
            try { sessionStorage.setItem('beanthentic_user', JSON.stringify(localUser)); } catch (_err2) {}
            return localUser;
          }
          var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
          if (sessionUser) {
            var sessionName = getKnownUserName(sessionUser.email);
            if (sessionName) sessionUser.name = sessionName;
            try { localStorage.setItem('beanthentic_user', JSON.stringify(sessionUser)); } catch (_err3) {}
            return sessionUser;
          }
        } catch (e) {}
        return null;
      }
      function redirectGuest() {
        try {
          location.replace(new URL('login.php', location.href).href);
        } catch (e) {
          location.replace('login.php');
        }
      }
      function initialsFromName(name) {
        var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
          return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        }
        if (parts.length === 1 && parts[0].length >= 2) {
          return parts[0].slice(0, 2).toUpperCase();
        }
        if (parts.length === 1) {
          return (parts[0].charAt(0) + parts[0].charAt(0)).toUpperCase();
        }
        return '?';
      }
      function formatMemberSince(ts) {
        var n = Number(ts);
        if (!n || !isFinite(n)) return '—';
        try {
          return new Date(n).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        } catch (e) {
          return '—';
        }
      }
      function formatDisplayName(u) {
        if (!u) return 'Member';
        if (u.needs_registration) return 'Member';
        var rawName = (u.name && String(u.name).trim()) ? String(u.name).trim() : '';
        if (!rawName) return (String(u.email).split('@')[0] || 'Member');
        var parts = rawName.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
          var firstName = parts[0];
          var lastName = parts[parts.length - 1];
          return lastName + ', ' + firstName;
        }
        return rawName;
      }
      function formatPhoneDisplay(raw) {
        var s = String(raw || '').trim();
        if (!s) return '';
        s = s.replace(/[\s\-]/g, '');
        if (s.startsWith('+63')) return s;
        if (s.startsWith('63') && s.length >= 11) return '+63' + s.slice(2);
        if (s.startsWith('09') && s.length === 11) return '+63' + s.slice(1);
        // If it looks like digits-only (e.g. 9920...), keep as-is.
        return s;
      }
      function getRegisteredFarmerProfile(email) {
        var cleanEmail = String(email || '').trim().toLowerCase();
        // Per-account profile (preferred).
        if (cleanEmail) {
          try {
            var mapRaw = localStorage.getItem('beanthentic_farmer_profile_map') || sessionStorage.getItem('beanthentic_farmer_profile_map');
            var map = mapRaw ? JSON.parse(mapRaw) : null;
            var prof = map && typeof map === 'object' ? map[cleanEmail] : null;
            if (prof && typeof prof === 'object') return prof;
          } catch (_em) {}
        }
        var keys = ['beanthentic_farmer_profile', 'beanthentic_registered_farmer_profile'];
        for (var i = 0; i < keys.length; i++) {
          var key = keys[i];
          var raw = null;
          try { raw = localStorage.getItem(key) || sessionStorage.getItem(key); } catch (_e1) {}
          if (!raw) continue;
          try {
            var data = JSON.parse(raw);
            if (data && typeof data === 'object') {
              return data;
            }
          } catch (_e2) {}
        }
        return null;
      }
      function renderProfile() {
        var u = getUser();
        var root = document.getElementById('account-simple-root');
        if (!root) return;
        if (!u) {
          root.hidden = true;
          return;
        }
        var email = String(u.email || '').trim();
        var farmerProfile = getRegisteredFarmerProfile(email);
        if (u.needs_registration) farmerProfile = null;
        var normalized = normalizeLoginId(email);
        var knownName = getKnownUserName(email) || (normalized ? getKnownUserName(normalized) : '');
        var farmerName = '';
        if (farmerProfile) {
          var fpName = String(farmerProfile.name || '').trim();
          var fpFirst = String(farmerProfile.first_name || '').trim();
          var fpLast = String(farmerProfile.last_name || '').trim();
          if (fpLast || fpFirst) {
            farmerName = (fpLast + ', ' + fpFirst).replace(/^,\s*/, '').replace(/\s+,/g, ',').trim();
          } else {
            farmerName = fpName;
          }
        }
        var rawName = knownName || farmerName || ((u.name && String(u.name).trim()) ? String(u.name).trim() : '');
        var displayName = rawName || (email.split('@')[0] || 'Member');
        if (u.needs_registration) displayName = 'Member';
        // If the "name" is actually a phone/number, don't show it as name.
        if (/^\+?\d{8,}$/.test(displayName.replace(/\s/g, ''))) {
          displayName = knownName || farmerName || 'Member';
        }

        // Prefer explicit phone fields; fall back to login identifier if it looks like a phone number.
        var phoneRaw = String((farmerProfile && (farmerProfile.phone || farmerProfile.mobile)) || u.phone || u.mobile || '').trim();
        if (!phoneRaw) {
          var maybePhone = email.split('@')[0];
          if (/^\+?\d{10,14}$/.test(maybePhone) || /^09\d{9}$/.test(maybePhone)) phoneRaw = maybePhone;
        }
        var phone = formatPhoneDisplay(phoneRaw);
        var ini = document.getElementById('account-simple-initials');
        if (ini) ini.textContent = initialsFromName(displayName);
        var elName = document.getElementById('account-simple-name');
        if (elName) elName.textContent = displayName;
        var elPhone = document.getElementById('account-simple-phone');
        if (elPhone) elPhone.textContent = phone || '—';
        var qr = document.getElementById('account-qr-img');
        if (qr) {
          var qrData = '';
          try {
            qrData = new URL('profile.php?name=' + encodeURIComponent(displayName) + '&email=' + encodeURIComponent(email), location.href).href;
          } catch (_e) {
            qrData = 'profile.php?name=' + encodeURIComponent(displayName) + '&email=' + encodeURIComponent(email);
          }
          qr.src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(qrData);
        }
        root.hidden = false;
      }

      function toKg(qty, unit) {
        var q = Number(String(qty || '').trim());
        if (!Number.isFinite(q)) return 0;
        var u = String(unit || '').trim().toUpperCase();
        if (u === 'KG') return q;
        if (u === 'G') return q / 1000;
        if (u === 'LB') return q * 0.45359237;
        // default assume KG if unit missing
        return q;
      }

      function formatKg(value) {
        var n = Number(value);
        if (!Number.isFinite(n)) n = 0;
        // show 1 decimal only if needed
        var rounded = Math.round(n * 10) / 10;
        var isInt = Math.abs(rounded - Math.round(rounded)) < 1e-9;
        return (isInt ? String(Math.round(rounded)) : String(rounded.toFixed(1))) + 'KG';
      }

      function emptyVarietyTotals() {
        return { all: 0, liberica: 0, excelsa: 0, robusta: 0 };
      }

      function computeSoldTotalsByVariety() {
        var out = { all: 0, liberica: 0, excelsa: 0, robusta: 0 };
        var list = [];
        try {
          list = window.BeanthenticTxnHistory && typeof window.BeanthenticTxnHistory.load === 'function'
            ? window.BeanthenticTxnHistory.load()
            : [];
        } catch (_e) {
          list = [];
        }
        if (!Array.isArray(list)) list = [];
        for (var i = 0; i < list.length; i++) {
          var r = list[i] || {};
          var v = String(r.variety || r.product || '').trim().toLowerCase();
          var kg = toKg(r.qty, r.unit);
          if (!kg) continue;
          out.all += kg;
          if (v === 'liberica' || v === 'excelsa' || v === 'robusta') {
            out[v] += kg;
          }
        }
        return out;
      }

      function computeInitialTotalsByVariety() {
        var out = emptyVarietyTotals();
        var u = getUser();
        var p = getRegisteredFarmerProfile(u && u.email ? u.email : '') || {};
        var production = (p && p.production && typeof p.production === 'object') ? p.production : {};
        var keys = ['liberica', 'excelsa', 'robusta'];
        for (var i = 0; i < keys.length; i++) {
          var k = keys[i];
          var item = production[k] || {};
          var kg = toKg(item.qty, item.unit || 'kg');
          if (!kg || !isFinite(kg)) kg = 0;
          out[k] = kg;
          out.all += kg;
        }
        return out;
      }

      function renderBeanTotals(selectedVariety) {
        var totals = computeInitialTotalsByVariety();
        var sold = computeSoldTotalsByVariety();
        var sel = selectedVariety || 'all';
        if (!totals.hasOwnProperty(sel)) sel = 'all';

        var pillAll = document.getElementById('pill-count-all');
        var pillLib = document.getElementById('pill-count-liberica');
        var pillExc = document.getElementById('pill-count-excelsa');
        var pillRob = document.getElementById('pill-count-robusta');
        if (pillAll) pillAll.textContent = formatKg(totals.all);
        if (pillLib) pillLib.textContent = formatKg(totals.liberica);
        if (pillExc) pillExc.textContent = formatKg(totals.excelsa);
        if (pillRob) pillRob.textContent = formatKg(totals.robusta);

        var totalEl = document.getElementById('account-total-kg');
        var remainingEl = document.getElementById('account-remaining-kg');
        var totalVal = totals[sel];
        var remainingVal = Math.max(0, Number(totalVal || 0) - Number(sold[sel] || 0));
        if (totalEl) totalEl.textContent = formatKg(totalVal);
        if (remainingEl) remainingEl.textContent = formatKg(remainingVal);
      }

      function bindVarietyFilters() {
        var pills = Array.from(document.querySelectorAll('.account-filter-pill[data-variety]'));
        if (pills.length === 0) return;

        var setActive = (key) => {
          pills.forEach((b) => b.classList.toggle('is-active', b.dataset.variety === key));
          renderBeanTotals(key);
        };

        pills.forEach((btn) => {
          if (btn.dataset.bound === '1') return;
          btn.dataset.bound = '1';
          btn.addEventListener('click', function () {
            var key = btn.dataset.variety || 'all';
            setActive(key);
          });
        });

        // default
        setActive('all');
      }
      function init() {
        if (!getUser()) {
          redirectGuest();
          return;
        }
        renderProfile();
        bindVarietyFilters();
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
      window.addEventListener('beanthentic-auth-changed', function () {
        if (!/account\.php/i.test(location.pathname || '')) return;
        if (!getUser()) redirectGuest();
        else {
          renderProfile();
          bindVarietyFilters();
        }
      });
      document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('account-simple-logout');
        if (!btn) return;
        btn.addEventListener('click', function () {
          try {
            localStorage.removeItem('beanthentic_user');
            sessionStorage.removeItem('beanthentic_user');
          } catch (e) {}
          try {
            window.location.assign(new URL('index.php#home', location.href).href);
          } catch (e2) {
            window.location.assign('index.php#home');
          }
        });
      });
    })();
  </script>
</body>
</html>

