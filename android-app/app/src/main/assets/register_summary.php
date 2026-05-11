<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Registration Summary · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body { margin: 0; background: #eef6ff; }
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
      padding: 0.9rem 0.9rem calc(5.8rem + env(safe-area-inset-bottom, 0px));
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
    }
    .reg-avatar svg { width: 44px; height: 44px; }
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
</head>
<body class="has-app-bottom-nav">
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
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
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
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>Federation Association</label>
          <input id="rs-federation" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>Association Name</label>
          <input id="rs-association" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>NCFRS</label>
          <input id="rs-ncfrs" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label>RSBSA Registered</label>
          <input id="rs-rsbsa" readonly placeholder="—" />
        </div>
      </div>
      <div style="height:0.55rem"></div>
      <div class="reg-grid-2">
        <div class="reg-field">
          <label>RSBSA Registered Number</label>
          <input id="rs-rsbsa-number" readonly placeholder="—" />
        </div>
        <div class="reg-field">
          <label></label>
          <input readonly placeholder="—" style="opacity:0; pointer-events:none;" />
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
      function getSignedInKey() {
        try {
          var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          var u = raw ? JSON.parse(raw) : null;
          return (u && u.email) ? String(u.email).trim().toLowerCase() : '';
        } catch (_e) {
          return '';
        }
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
          for (var i = 0; i < keys.length; i += 1) map[keys[i]] = profile;
          localStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(map));
          sessionStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(map));
          localStorage.setItem('beanthentic_farmer_profile', JSON.stringify(profile));
          sessionStorage.setItem('beanthentic_farmer_profile', JSON.stringify(profile));
        } catch (_e) {}
      }
      function apiBases() {
        var out = [];
        try {
          if (window.location && /^https?:$/i.test(window.location.protocol) && window.location.origin) out.push(window.location.origin);
        } catch (_e) {}
        try {
          var saved = localStorage.getItem('beanthentic_flask_base') || sessionStorage.getItem('beanthentic_flask_base');
          if (saved) out.push(String(saved).trim().replace(/\/+$/, ''));
        } catch (_e2) {}
        out.push('');
        return Array.from(new Set(out.filter(Boolean).concat([''])));
      }
      async function fetchProfileFromApi() {
        var loginKey = getSignedInKey();
        if (!loginKey) return null;
        var bases = apiBases();
        for (var i = 0; i < bases.length; i += 1) {
          var base = bases[i];
          var url = (base ? base : '') + '/api/register-farm/farmer-profile?login_id=' + encodeURIComponent(loginKey);
          try {
            var res = await fetch(url, { method: 'GET', credentials: 'same-origin' });
            if (!res.ok) continue;
            var data = await res.json();
            if (data && data.success && data.found && data.profile) {
              cacheProfileForSignedInUser(data.profile);
              return data.profile;
            }
          } catch (_err) {}
        }
        return null;
      }
      function set(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        el.value = (value === null || value === undefined || value === '') ? '—' : String(value);
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

        set('rs-province', p.province || 'Batangas');
        set('rs-municipality', p.municipality || 'Lipa City');
        set('rs-barangay', p.barangay || '');

        set('rs-federation', p.federation || '—');
        set('rs-association', p.association || '—');
        var ncfrsText = String(p.ncfrs || '').trim().toLowerCase();
        if (ncfrsText === 'yes') ncfrsText = 'Yes';
        else if (ncfrsText === 'no') ncfrsText = 'No';
        else ncfrsText = '—';
        set('rs-ncfrs', ncfrsText);
        var rsbsaText = '—';
        if (typeof p.rsbsa_registered === 'string' && p.rsbsa_registered.trim()) rsbsaText = p.rsbsa_registered.trim();
        set('rs-rsbsa', rsbsaText);
        set('rs-rsbsa-number', p.rsbsa_number || '');

        set('rs-ownership', p.ownership_status || '');
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
      }
      async function render() {
        var p = pickProfile();
        if (!p) p = await fetchProfileFromApi();
        renderProfile(p);
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { render(); });
      else render();
    })();
  </script>
</body>
</html>
