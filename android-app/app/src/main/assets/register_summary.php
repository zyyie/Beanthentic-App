<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_api_urls.js?v=20260515-7"></script>
  <script src="js/beanthentic_session_gate.js?v=20260515-4"></script>
  <title>Registration Summary · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body.reg-summary-page {
      margin: 0;
      background: #eef6ff;
    }
    .reg-hero {
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      border-radius: 0 0 22px 22px;
      padding: 1.05rem 1rem 1rem;
      color: #fff;
      box-shadow: 0 8px 18px rgba(15, 77, 18, 0.24);
    }
    .reg-hero-row {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 44px;
    }
    .reg-nav-back {
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px;
      text-decoration: none;
      -webkit-tap-highlight-color: transparent;
    }
    .reg-nav-back svg { width: 20px; height: 20px; }
    .reg-hero-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 800;
      letter-spacing: 0.01em;
      text-align: center;
      line-height: 1.1;
      white-space: nowrap;
    }
    .reg-hero-sub {
      margin: 0.2rem 0 0;
      font-size: 0.72rem;
      font-weight: 600;
      opacity: 0.9;
      text-align: center;
    }
    .reg-main {
      width: min(100%, 720px);
      margin: 0 auto;
      padding: 1rem 0.9rem calc(5.8rem + env(safe-area-inset-bottom, 0px));
      box-sizing: border-box;
    }
    .reg-card {
      background: #ffffff;
      border-radius: 18px;
      border: 1px solid rgba(15, 23, 42, 0.08);
      box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
      padding: 0.95rem 0.95rem 1.05rem;
    }
    .reg-avatar {
      width: 92px;
      height: 92px;
      border-radius: 999px;
      background: #e5e7eb;
      display: grid;
      place-items: center;
      margin: 0 auto 0.7rem;
      color: rgba(17, 24, 39, 0.55);
      position: relative;
      overflow: hidden;
    }
    .reg-avatar-img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 999px;
      z-index: 1;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
    .reg-avatar.has-photo .reg-avatar-img {
      opacity: 1;
      visibility: visible;
    }
    .reg-avatar.has-photo .reg-avatar-fallback {
      display: none;
    }
    .reg-avatar svg,
    .reg-avatar .reg-avatar-fallback { width: 44px; height: 44px; }
    .reg-section-title {
      margin: 0.9rem 0 0.55rem;
      font-size: 0.95rem;
      font-weight: 800;
      color: #145e1e;
      letter-spacing: -0.01em;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .reg-section-title::before {
      content: "";
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: #145e1e;
      display: inline-block;
      opacity: 0.9;
    }
    .reg-grid-2 {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.55rem;
    }
    .reg-grid-affiliation {
      margin-bottom: 0.15rem;
    }
    .reg-grid-3 {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 0.55rem;
    }
    .reg-field label {
      display: block;
      font-size: 0.7rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: 0.25rem;
    }
    .reg-field input, .reg-field select {
      width: 100%;
      min-height: 38px;
      border-radius: 10px;
      border: 1px solid rgba(17, 24, 39, 0.12);
      background: #ffffff;
      padding: 0.55rem 0.7rem;
      font-size: 0.85rem;
      box-sizing: border-box;
      color: #111827;
    }
    .reg-field input[readonly] { background: #f8fafc; }
    .reg-note {
      margin: 0.75rem 0 0;
      font-size: 0.75rem;
      color: #6b7280;
      line-height: 1.35;
      text-align: center;
    }
    @media (max-width: 520px) {
      .reg-grid-3 { grid-template-columns: 1fr; }
      .reg-grid-2 { grid-template-columns: 1fr; }
    }
  </style>
  <script id="beanthentic-fixed-topbar-js" src="js/beanthentic_fixed_topbar.js"></script>
</head>
<body class="has-app-bottom-nav reg-summary-page beanthentic-fixed-topbar-active">
  <header class="reg-hero">
    <div class="reg-hero-row">
      <a class="reg-nav-back" href="index.php#home" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <div>
        <p class="reg-hero-title">Farmer’s Registration</p>
        <p class="reg-hero-sub">Registration in Barako Federation Association</p>
      </div>
    </div>
  </header>

  <main class="reg-main">
    <section class="reg-card" aria-label="Registration summary">
      <div class="reg-avatar" aria-hidden="true">
        <img id="rs-profile-photo" class="reg-avatar-img" alt="" width="92" height="92" decoding="async" />
        <svg class="reg-avatar-fallback" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12 12a4.2 4.2 0 1 0-4.2-4.2A4.2 4.2 0 0 0 12 12Zm0 2c-4.4 0-8 2.2-8 4.9V21h16v-2.1c0-2.7-3.6-4.9-8-4.9Z"/>
        </svg>
      </div>

      <h2 class="reg-section-title">Personal Information</h2>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Last Name</label>
          <input id="rs-last-name" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>First Name</label>
          <input id="rs-first-name" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-field">
        <label>Birthday</label>
        <input id="rs-birthday" readonly placeholder="—" />
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-3">
        <div class="reg-field">
          <label>Province</label>
          <input id="rs-province" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Municipality</label>
          <input id="rs-municipality" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Barangay</label>
          <input id="rs-barangay" readonly placeholder="—" />
        </div>
      </div>

      <h2 class="reg-section-title">Affiliation</h2>
      <div class="reg-grid-2 reg-grid-affiliation">
        <div class="reg-field">
          <label>Affiliation Role</label>
          <input id="rs-federation" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>NCFRS</label>
          <input id="rs-ncfrs" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>RSBSA Registered</label>
          <input id="rs-rsbsa" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>RSBSA Registered Number</label>
          <input id="rs-rsbsa-number" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>RSBSA Status</label>
          <input id="rs-rsbsa-status" readonly placeholder="—" />
        </div>
      </div>

      <h2 class="reg-section-title">Farm Information</h2>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Status of Ownership</label>
          <input id="rs-ownership" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Total Plant Area</label>
          <input id="rs-plant-area" readonly placeholder="—" />
        </div>
      </div>

      <h2 class="reg-section-title">Tree Counts</h2>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Liberica (Kapeng Barako) Number of Bearing</label>
          <input id="rs-lib-bearing" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Liberica (Kapeng Barako) Number of Non Bearing</label>
          <input id="rs-lib-nonbearing" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Robusta Number of Bearing</label>
          <input id="rs-rob-bearing" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Robusta Number of Non Bearing</label>
          <input id="rs-rob-nonbearing" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Excelsa Number of Bearing</label>
          <input id="rs-exc-bearing" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Excelsa Number of Non Bearing</label>
          <input id="rs-exc-nonbearing" readonly placeholder="—" />
        </div>
      </div>

      <h2 class="reg-section-title">Production (2026)</h2>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Liberica (Kapeng Barako)</label>
          <input id="rs-prod-lib" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Unit</label>
          <input id="rs-prod-lib-unit" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Robusta</label>
          <input id="rs-prod-rob" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Unit</label>
          <input id="rs-prod-rob-unit" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Excelsa</label>
          <input id="rs-prod-exc" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Unit</label>
          <input id="rs-prod-exc-unit" readonly placeholder="—" />
        </div>
      </div>

      <p class="reg-note" id="rs-note" hidden></p>
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
      <a href="records.php" id="nav-qr" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--record" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 2h6v2H9z"/><path d="M9 12h6"/><path d="M9 16h6"/><path d="M9 20h4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Record</span>
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

  <script src="js/ui.js"></script>
  <script>
    (function () {
      function safeGetJson(key) {
        try {
          var raw = localStorage.getItem(key) || sessionStorage.getItem(key);
          return raw ? JSON.parse(raw) : null;
        } catch (_e) {
          return null;
        }
      }
      function getSessionUser() {
        try {
          var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          return raw ? JSON.parse(raw) : null;
        } catch (_e) {
          return null;
        }
      }
      function getSignedInKey() {
        var u = getSessionUser();
        if (!u) return '';
        if (u.email != null && String(u.email).trim()) return String(u.email).trim().toLowerCase();
        if (u.phone_number != null && String(u.phone_number).trim()) return String(u.phone_number).trim();
        if (u.login != null && String(u.login).trim()) return String(u.login).trim();
        return '';
      }
      function formatBirthdayDisplay(raw) {
        var s = String(raw == null ? '' : raw).trim();
        if (!s) return '';
        var m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (m) return m[2] + '/' + m[3] + '/' + m[1];
        return s;
      }
      function keyVariants(v) {
        var out = [];
        var k = String(v || '').trim().toLowerCase();
        if (!k) return out;
        out.push(k);
        var d = k.replace(/\D/g, '');
        if (d) {
          if (d.indexOf('63') === 0 && d.length >= 12) out.push('0' + d.slice(2));
          if (d.indexOf('0') === 0 && d.length >= 11) out.push('+63' + d.slice(1));
          if (d.length === 10 && d.charAt(0) === '9') {
            out.push('0' + d);
            out.push('+63' + d);
          }
        }
        return Array.from(new Set(out));
      }
      function pickProfile() {
        // Prefer per-account profile map so each account sees its own read-only data.
        try {
          var key = getSignedInKey();
          var profileMap = safeGetJson('beanthentic_farmer_profile_map');
          if (key && profileMap && typeof profileMap === 'object') {
            var keys = keyVariants(key);
            for (var i = 0; i < keys.length; i += 1) {
              if (profileMap[keys[i]]) return profileMap[keys[i]];
            }
          }
        } catch (_e0) {}
        return (
          safeGetJson('beanthentic_farmer_profile') ||
          safeGetJson('beanthentic_registered_farmer_profile') ||
          safeGetJson('beanthentic_farmer_registration') ||
          null
        );
      }
      function cacheProfileForSignedInUser(profile) {
        if (!profile || typeof profile !== 'object') return;
        try {
          var key = getSignedInKey();
          if (!key) return;
          var map = safeGetJson('beanthentic_farmer_profile_map');
          if (!map || typeof map !== 'object') map = {};
          var keys = keyVariants(key);
          var lastMerged = profile;
          for (var i = 0; i < keys.length; i += 1) {
            var kk = keys[i];
            var prev = map[kk];
            var merged = Object.assign({}, prev || {}, profile);
            if (prev && typeof prev === 'object' && String(prev.profile_photo_data || '').trim()) {
              var incoming = String(merged.profile_photo_data || '').trim();
              if (!incoming) merged.profile_photo_data = prev.profile_photo_data;
            }
            map[kk] = merged;
            lastMerged = merged;
          }
          localStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(map));
          sessionStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(map));
          localStorage.setItem('beanthentic_farmer_profile', JSON.stringify(lastMerged));
          sessionStorage.setItem('beanthentic_farmer_profile', JSON.stringify(lastMerged));
        } catch (_e) {}
      }
      function apiBases() {
        var out = [];
        try {
          if (window.location && /^https?:$/i.test(window.location.protocol) && window.location.origin) out.push(window.location.origin);
        } catch (_e) {}
        try {
          // Primary: the same base used by login/signup/register APIs.
          var api = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
          if (api) out.push(String(api).trim().replace(/\/+$/, ''));
        } catch (_eApi) {}
        try {
          var saved = localStorage.getItem('beanthentic_flask_base') || sessionStorage.getItem('beanthentic_flask_base');
          if (saved) out.push(String(saved).trim().replace(/\/+$/, ''));
        } catch (_e2) {}
        out.push('');
        return Array.from(new Set(out.filter(Boolean).concat([''])));
      }
      function profileQueryParams() {
        var u = getSessionUser();
        var loginKey = getSignedInKey();
        var q = [];
        if (loginKey) q.push('login_id=' + encodeURIComponent(loginKey));
        if (u && u.user_id > 0) q.push('user_id=' + encodeURIComponent(String(u.user_id)));
        if (u && u.farmer_id > 0) q.push('farmer_id=' + encodeURIComponent(String(u.farmer_id)));
        return q.length ? ('?' + q.join('&')) : '';
      }
      async function fetchProfileFromApi() {
        var loginKey = getSignedInKey();
        var u = getSessionUser();
        if (!loginKey && !(u && u.user_id > 0)) {
          try {
            var note0 = document.getElementById('rs-note');
            if (note0) {
              note0.hidden = false;
              note0.textContent = 'Cannot load profile: missing signed-in user (beanthentic_user). Please login again.';
            }
          } catch (_n0) {}
          return null;
        }
        var qs = profileQueryParams();
        var lastErr = '';
        if (window.BeanthenticApiUrls && window.BeanthenticApiUrls.phpApiUrlCandidates) {
          var urls = window.BeanthenticApiUrls.phpApiUrlCandidates('register-farm/farmer-profile' + qs);
          for (var j = 0; j < Math.min(urls.length, 3); j += 1) {
            try {
              var resA = await fetch(urls[j], { method: 'GET', credentials: 'same-origin' });
              if (!resA.ok) { lastErr = 'HTTP ' + resA.status; continue; }
              var dataA = await resA.json();
              if (dataA && dataA.success && dataA.found && dataA.profile) {
                cacheProfileForSignedInUser(dataA.profile);
                return dataA.profile;
              }
              if (dataA && dataA.success && dataA.found === false) lastErr = 'Not found in MySQL';
            } catch (errA) {
              lastErr = (errA && errA.message) ? String(errA.message) : 'Network error';
            }
          }
        }
        var bases = apiBases();
        for (var i = 0; i < bases.length; i += 1) {
          var base = bases[i];
          var url = (base ? base : '') + '/api/register-farm/farmer-profile' + qs;
          try {
            var res = await fetch(url, { method: 'GET', credentials: 'same-origin' });
            if (!res.ok) { lastErr = 'HTTP ' + res.status + ' @ ' + url; continue; }
            var data = await res.json();
            if (data && data.success && data.found && data.profile) {
              cacheProfileForSignedInUser(data.profile);
              return data.profile;
            }
            if (data && data.success && data.found === false) {
              lastErr = 'Not found in DB';
            }
          } catch (err) {
            try {
              lastErr = (err && err.message ? String(err.message) : String(err)) + ' @ ' + url;
            } catch (_e3) {
              lastErr = 'Network error @ ' + url;
            }
          }
        }
        try {
          var note = document.getElementById('rs-note');
          if (note && lastErr) {
            note.hidden = false;
            note.textContent = 'Cannot load profile from server: ' + lastErr;
          }
        } catch (_n) {}
        return null;
      }
      function mergeProfiles(local, api) {
        var out = Object.assign({}, local || {}, api || {});
        function pick(field) {
          var a = api && api[field];
          var b = local && local[field];
          if (a !== null && a !== undefined && String(a).trim() !== '') return a;
          if (b !== null && b !== undefined && String(b).trim() !== '') return b;
          return '';
        }
        out.birthday = pick('birthday') || pick('birth_date') || pick('date_of_birth');
        out.first_name = pick('first_name');
        out.last_name = pick('last_name');
        out.barangay = pick('barangay');
        out.province = pick('province') || 'Batangas';
        out.municipality = pick('municipality') || 'Lipa City';
        var photoLocal = local && String(local.profile_photo_data || local.profile_photo || '').trim();
        var photoApi = api && String(api.profile_photo_data || api.profile_photo || '').trim();
        out.profile_photo_data = photoApi || photoLocal || '';
        return out;
      }
      function normalizeProfilePhotoData(raw) {
        var s = String(raw || '').trim();
        if (!s) return '';
        if (/^data:image\//i.test(s) || /^https?:\/\//i.test(s)) return s;
        if (s.charAt(0) === '/' && s.length > 4) return s;
        var compact = s.replace(/\s/g, '');
        if (/^[A-Za-z0-9+/=]+$/.test(compact) && compact.length > 240) {
          return 'data:image/jpeg;base64,' + compact;
        }
        return s;
      }
      function displayCapitalize(raw) {
        var s = String(raw == null ? '' : raw).trim();
        if (!s) return '';
        if (s === '—' || s === '-') return s;
        if (/_/.test(s) && !/\s/.test(s)) {
          return s.split('_').filter(Boolean).map(function (w) {
            return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
          }).join(' ');
        }
        return s.replace(/\b([a-zA-Z])([a-zA-Z']*)\b/g, function (_, a, rest) {
          return a.toUpperCase() + (rest || '').toLowerCase();
        });
      }
      function set(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        if (value === null || value === undefined || value === '') {
          el.value = '—';
          return;
        }
        el.value = displayCapitalize(String(value));
      }
      function setBirthday(value) {
        var el = document.getElementById('rs-birthday');
        if (!el) return;
        var formatted = formatBirthdayDisplay(value);
        el.value = formatted ? formatted : '—';
      }
      function formatOwnershipDisplay(raw) {
        var s = String(raw == null ? '' : raw).trim().toLowerCase();
        if (!s) return '';
        var map = {
          landowner: 'Landowner',
          cloa_holder: 'CLOA holder',
          list_holder: 'LIST holder',
          sessional_farm_worker: 'Seasonal farm worker',
          others: 'Others',
          owner: 'Landowner',
          tenant: 'Seasonal farm worker',
          'co-owner': 'CLOA holder',
          co_owner: 'CLOA holder',
          coowner: 'CLOA holder',
          other: 'Others'
        };
        var label = map[s];
        if (label) return label;
        return displayCapitalize(String(raw).trim());
      }
      function setOwnershipField(id, raw) {
        var el = document.getElementById(id);
        if (!el) return;
        var txt = formatOwnershipDisplay(raw);
        el.value = txt ? txt : '—';
      }
      function prod(p, key) {
        var obj = (p && p.production && typeof p.production === 'object') ? p.production : {};
        var item = obj[key] || {};
        return item;
      }
      function renderProfile(p) {
        var note = document.getElementById('rs-note');
        if (!p) {
          if (note) {
            note.hidden = false;
            note.textContent = 'No saved farmer registration found for this account yet. Please register first.';
          }
          var photoEl0 = document.getElementById('rs-profile-photo');
          var avEl0 = document.querySelector('.reg-avatar');
          if (photoEl0 && avEl0) {
            try { photoEl0.removeAttribute('src'); } catch (_r0) {}
            photoEl0.style.opacity = '0';
            photoEl0.style.visibility = 'hidden';
            avEl0.classList.remove('has-photo');
          }
          return;
        }

        var name = String(p.name || '').trim();
        var first = String(p.first_name || '').trim();
        var last = String(p.last_name || '').trim();
        if (!first && !last && name) {
          var parts = name.split(/\s+/).filter(Boolean);
          if (parts.length >= 2) {
            first = parts[0];
            last = parts.slice(1).join(' ');
          } else if (parts.length === 1) {
            first = parts[0];
          }
        }
        set('rs-first-name', first);
        set('rs-last-name', last);
        setBirthday(p.birthday || p.birth_date || p.date_of_birth || '');

        set('rs-province', p.province || 'Batangas');
        set('rs-municipality', p.municipality || 'Lipa City');
        set('rs-barangay', p.barangay || '');

        set('rs-federation', p.federation || '—');
        var ncfrsText = String(p.ncfrs || '').trim().toLowerCase();
        if (ncfrsText === 'yes') ncfrsText = 'Yes';
        else if (ncfrsText === 'no') ncfrsText = 'No';
        else ncfrsText = '—';
        set('rs-ncfrs', ncfrsText);
        var rsbsaText = '—';
        if (typeof p.rsbsa_registered === 'string' && p.rsbsa_registered.trim()) rsbsaText = p.rsbsa_registered.trim();
        set('rs-rsbsa', rsbsaText);
        set('rs-rsbsa-number', p.rsbsa_number || '');
        var rsbsaStatusText = '—';
        var rsbSt = String(p.rsbsa_status || '').trim().toLowerCase();
        if (rsbSt === 'not_yet_applied') rsbsaStatusText = 'Not Yet Applied';
        else if (rsbSt === 'pending_rsbsa') rsbsaStatusText = 'Pending RSBSA';
        else if (p.rsbsa_status && String(p.rsbsa_status).trim()) rsbsaStatusText = String(p.rsbsa_status).trim();
        set('rs-rsbsa-status', rsbsaStatusText);

        setOwnershipField('rs-ownership', p.ownership_status || '');
        var area = '';
        if ((p.plant_area_value != null && String(p.plant_area_value).trim() !== '') || (p.plant_area_unit && String(p.plant_area_unit).trim())) {
          area = String(p.plant_area_value || '').trim() + (p.plant_area_unit ? (' ' + String(p.plant_area_unit).trim()) : '');
          area = area.trim();
        }
        set('rs-plant-area', area);

        var tc = (p && p.tree_counts && typeof p.tree_counts === 'object') ? p.tree_counts : {};
        set('rs-lib-bearing', (tc.liberica && tc.liberica.bearing) || p.liberica_bearing || '');
        set('rs-lib-nonbearing', (tc.liberica && tc.liberica.non_bearing) || p.liberica_non_bearing || '');
        set('rs-rob-bearing', (tc.robusta && tc.robusta.bearing) || p.robusta_bearing || '');
        set('rs-rob-nonbearing', (tc.robusta && tc.robusta.non_bearing) || p.robusta_non_bearing || '');
        set('rs-exc-bearing', (tc.excelsa && tc.excelsa.bearing) || p.excelsa_bearing || '');
        set('rs-exc-nonbearing', (tc.excelsa && tc.excelsa.non_bearing) || p.excelsa_non_bearing || '');

        var pl = prod(p, 'liberica');
        var pr = prod(p, 'robusta');
        var pe = prod(p, 'excelsa');
        set('rs-prod-lib', (pl.qty != null ? pl.qty : (pl.quantity != null ? pl.quantity : '')));
        set('rs-prod-lib-unit', pl.unit || 'kg');
        set('rs-prod-rob', (pr.qty != null ? pr.qty : (pr.quantity != null ? pr.quantity : '')));
        set('rs-prod-rob-unit', pr.unit || 'kg');
        set('rs-prod-exc', (pe.qty != null ? pe.qty : (pe.quantity != null ? pe.quantity : '')));
        set('rs-prod-exc-unit', pe.unit || 'kg');

        var photoRaw = normalizeProfilePhotoData(p.profile_photo_data);
        var photoEl = document.getElementById('rs-profile-photo');
        var avEl = document.querySelector('.reg-avatar');
        if (photoEl && avEl) {
          if (/^data:image\//i.test(photoRaw) || /^https?:\/\//i.test(photoRaw) || (photoRaw.charAt(0) === '/' && photoRaw.length > 4)) {
            photoEl.src = photoRaw;
            photoEl.style.opacity = '1';
            photoEl.style.visibility = 'visible';
            avEl.classList.add('has-photo');
          } else {
            try { photoEl.removeAttribute('src'); } catch (_r) {}
            photoEl.style.opacity = '0';
            photoEl.style.visibility = 'hidden';
            avEl.classList.remove('has-photo');
          }
        }
      }
      async function render() {
        var pLocal = pickProfile();
        var pApi = null;
        try {
          pApi = await fetchProfileFromApi();
        } catch (_ea) {}
        var p = mergeProfiles(pLocal, pApi) || pLocal || pApi;
        if (p) cacheProfileForSignedInUser(p);
        renderProfile(p);
        try {
          if (typeof window.beanthenticSyncRegisterNavIcon === 'function') {
            window.beanthenticSyncRegisterNavIcon();
          }
        } catch (_sb) {}
      }
      var regBack = document.querySelector('.reg-nav-back');
      if (regBack) {
        regBack.addEventListener('click', function (e) {
          e.preventDefault();
          try {
            window.location.assign(new URL('index.php#home', location.href).href);
          } catch (_eb) {
            window.location.href = 'index.php#home';
          }
        });
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { render(); });
      else render();
    })();
  </script>
</body>
</html>
