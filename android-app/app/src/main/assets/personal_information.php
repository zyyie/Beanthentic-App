<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>Personal Information · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body {
      margin: 0;
      background: #f3f8ff;
    }
    .pi-page header {
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 16px 16px;
      padding-top: env(safe-area-inset-top);
    }
    .pi-page .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 4.4rem;
      padding: 0.6rem 1rem 0.7rem max(0.78rem, env(safe-area-inset-left, 0px));
      column-gap: 0.5rem;
    }
    .pi-page .logo {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      text-decoration: none;
      color: #ffffff;
    }
    .pi-page .logo-mark {
      width: 52px;
      height: 52px;
      transform: none !important;
      animation: none;
      object-fit: contain;
    }
    .pi-brand {
      color: #ffffff;
      font-size: 2rem;
      font-weight: 800;
      line-height: 1;
      letter-spacing: -0.01em;
    }
    .pi-page .nav-right-cluster {
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }
    .pi-page .header-notifications-btn,
    .pi-account-btn {
      width: 40px;
      height: 40px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.45);
      background: #ffffff;
      color: #1d7a2a;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }
    .pi-page .header-notifications-icon,
    .pi-account-btn svg {
      width: 19px;
      height: 19px;
    }

    .pi-main {
      width: min(100%, 460px);
      margin: 0 auto;
      padding: 1.05rem 0.95rem 6.3rem;
      box-sizing: border-box;
    }
    .pi-back {
      display: inline-flex;
      align-items: center;
      gap: 0.32rem;
      color: #2f7a24;
      text-decoration: none;
      font-size: 0.88rem;
      margin-bottom: 0.8rem;
    }
    .pi-back svg {
      width: 16px;
      height: 16px;
    }
    .pi-title {
      margin: 0 0 0.9rem;
      font-size: 2.05rem;
      line-height: 1.08;
      font-weight: 800;
      color: #101827;
      letter-spacing: -0.01em;
    }
    .pi-title span {
      color: #1d7a2a;
    }
    .pi-card {
      background: #ffffff;
      border: 1px solid rgba(17, 24, 39, 0.06);
      border-radius: 14px;
      box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06);
      padding: 0.45rem 0.75rem;
    }
    .pi-avatar-wrap {
      display: flex;
      justify-content: center;
      padding: 0.5rem 0 0.35rem;
    }
    .pi-avatar {
      width: 112px;
      height: 112px;
      border-radius: 999px;
      background: #e5e7eb;
      display: grid;
      place-items: center;
      position: relative;
      overflow: hidden;
    }
    .pi-avatar-photo {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 999px;
      z-index: 1;
    }
    .pi-avatar-initials {
      font-size: 1.85rem;
      font-weight: 800;
      color: rgba(17, 24, 39, 0.45);
      letter-spacing: 0.03em;
    }
    .pi-avatar.has-photo .pi-avatar-initials {
      opacity: 0;
    }
    .pi-row {
      padding: 0.75rem 0.1rem 0.72rem;
      border-bottom: 1px solid rgba(17, 24, 39, 0.12);
    }
    .pi-row:last-child {
      border-bottom: none;
    }
    .pi-label {
      margin: 0 0 0.2rem;
      color: #4b5563;
      font-size: 0.8rem;
      line-height: 1.2;
    }
    .pi-value {
      margin: 0;
      color: #111827;
      font-size: 1.02rem;
      line-height: 1.24;
      font-weight: 800;
    }
  </style>
</head>
<body class="pi-page">
  <header>
    <div class="nav">
      <div class="nav-logo-wrap">
        <a href="index.php#home" class="logo" aria-label="Beanthentic home">
          <img class="logo-mark" src="beanthentic_logo.png" alt="Beanthentic" />
          <span class="pi-brand">Beanthentic</span>
        </a>
      </div>
      <div class="nav-right-cluster">
        <button type="button" id="header-notifications-btn" class="header-notifications-btn" aria-label="Notifications" title="Notifications">
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
        <a href="account_settings.html" class="pi-account-btn" aria-label="Account settings">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </a>
      </div>
    </div>
  </header>

  <main class="pi-main">
    <a class="pi-back" href="account_settings.html" aria-label="Back to account settings">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back
    </a>

    <h1 class="pi-title"><span>Personal</span> Information</h1>

    <section class="pi-card" aria-label="Personal Information Details">
      <div class="pi-avatar-wrap">
        <div class="pi-avatar" id="pi-avatar" aria-hidden="true">
          <img id="pi-photo" class="pi-avatar-photo" alt="" width="112" height="112" hidden decoding="async" />
          <span id="pi-initials" class="pi-avatar-initials">—</span>
        </div>
      </div>
      <div class="pi-row">
        <p class="pi-label">Full Name</p>
        <p class="pi-value" id="pi-full-name">—</p>
      </div>
      <div class="pi-row">
        <p class="pi-label">Date of Birth</p>
        <p class="pi-value" id="pi-dob">—</p>
      </div>
      <div class="pi-row">
        <p class="pi-label">Phone Number</p>
        <p class="pi-value" id="pi-phone">—</p>
      </div>
      <div class="pi-row">
        <p class="pi-label">Current Address</p>
        <p class="pi-value" id="pi-address">—</p>
      </div>
    </section>
  </main>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script>
    (function () {
      function parseUser(raw) {
        if (!raw) return null;
        try {
          var u = JSON.parse(raw);
          return (u && u.email) ? u : null;
        } catch (_e) {
          return null;
        }
      }
      function getUser() {
        var u = null;
        try { u = parseUser(localStorage.getItem('beanthentic_user')); } catch (_e1) {}
        if (!u) {
          try { u = parseUser(sessionStorage.getItem('beanthentic_user')); } catch (_e2) {}
        }
        return u;
      }
      function getKnownUserName(email) {
        var cleanEmail = String(email || '').trim().toLowerCase();
        if (!cleanEmail) return '';
        try {
          var raw = localStorage.getItem('beanthentic_user_name_map') || sessionStorage.getItem('beanthentic_user_name_map');
          var map = raw ? JSON.parse(raw) : {};
          return map && typeof map[cleanEmail] === 'string' ? String(map[cleanEmail]).trim() : '';
        } catch (_err) {
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
      function pickFarmerProfile() {
        try {
          var u = getUser();
          var key = u && u.email ? String(u.email).trim().toLowerCase() : '';
          var rawMap = localStorage.getItem('beanthentic_farmer_profile_map') || sessionStorage.getItem('beanthentic_farmer_profile_map');
          var map = rawMap ? JSON.parse(rawMap) : null;
          if (key && map && typeof map === 'object') {
            var keys = keyVariants(key);
            for (var i = 0; i < keys.length; i += 1) {
              if (map[keys[i]] && typeof map[keys[i]] === 'object') return map[keys[i]];
            }
          }
        } catch (_e0) {}
        return getFarmerProfile();
      }
      function getFarmerProfile() {
        try {
          var raw = localStorage.getItem('beanthentic_farmer_profile') || sessionStorage.getItem('beanthentic_farmer_profile');
          if (!raw) return null;
          var p = JSON.parse(raw);
          return p && typeof p === 'object' ? p : null;
        } catch (_e) {
          return null;
        }
      }
      function formatPhone(raw) {
        var s = String(raw || '').trim();
        if (!s) return '';
        s = s.replace(/[\s\-]/g, '');
        if (s.startsWith('+63')) return s;
        if (s.startsWith('63') && s.length >= 11) return '+63' + s.slice(2);
        if (s.startsWith('09') && s.length === 11) return '+63' + s.slice(1);
        return s;
      }
      function initialsFromName(name) {
        var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (parts.length >= 2) return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
        return '—';
      }
      function farmerProfilePhotoSrc(profile) {
        if (!profile || typeof profile !== 'object') return '';
        var raw = String(profile.profile_photo_data || profile.profile_photo || '').trim();
        if (!raw) return '';
        if (/^data:image\//i.test(raw) || /^https?:\/\//i.test(raw)) return raw;
        if (raw.charAt(0) === '/' && raw.length > 4) return raw;
        var compact = raw.replace(/\s/g, '');
        if (/^[A-Za-z0-9+/=]+$/.test(compact) && compact.length > 240) {
          return 'data:image/jpeg;base64,' + compact;
        }
        return '';
      }
      function formatDob(raw) {
        var s = String(raw || '').trim();
        if (!s) return '—';
        if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
          var d = new Date(s + 'T00:00:00');
          if (!isNaN(d.getTime())) {
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: '2-digit' });
          }
        }
        return s;
      }
      function render() {
        var u = getUser() || {};
        var p = pickFarmerProfile() || {};
        var email = String(u.email || '').trim();
        var fullFromFarmer = String(p.name || '').trim();
        if (!fullFromFarmer) {
          var first = String(p.first_name || '').trim();
          var last = String(p.last_name || '').trim();
          fullFromFarmer = (first || last) ? (first + ' ' + last).trim() : '';
        }
        var name = fullFromFarmer || getKnownUserName(email) || String(u.name || '').trim();
        if (!name) name = email.split('@')[0] || 'Member';

        var phone = formatPhone(p.phone || u.phone || u.mobile || '');
        if (!phone) {
          var maybe = email.split('@')[0] || '';
          if (/^\+?\d{10,14}$/.test(maybe) || /^09\d{9}$/.test(maybe)) phone = formatPhone(maybe);
        }

        var dob = formatDob(p.birthday || p.birth_date || p.birthdate || p.date_of_birth || u.birthday || u.birth_date || u.birthdate || u.date_of_birth);
        var address = String(
          p.address || p.current_address || p.location || p.barangay || u.address || u.current_address || u.location || ''
        ).trim() || '—';

        var iniEl = document.getElementById('pi-initials');
        var photoEl = document.getElementById('pi-photo');
        var avEl = document.getElementById('pi-avatar');
        if (iniEl) iniEl.textContent = initialsFromName(name);
        var photoRaw = farmerProfilePhotoSrc(p);
        if (photoEl && avEl) {
          if (/^data:image\//i.test(photoRaw) || /^https?:\/\//i.test(photoRaw) || (photoRaw.charAt(0) === '/' && photoRaw.length > 4)) {
            photoEl.src = photoRaw;
            photoEl.removeAttribute('hidden');
            avEl.classList.add('has-photo');
          } else {
            try { photoEl.removeAttribute('src'); } catch (_r) {}
            photoEl.setAttribute('hidden', '');
            avEl.classList.remove('has-photo');
          }
        }

        var elName = document.getElementById('pi-full-name');
        var elDob = document.getElementById('pi-dob');
        var elPhone = document.getElementById('pi-phone');
        var elAddress = document.getElementById('pi-address');
        if (elName) elName.textContent = name;
        if (elDob) elDob.textContent = dob;
        if (elPhone) elPhone.textContent = phone || '—';
        if (elAddress) elAddress.textContent = address;
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', render);
      else render();
    })();
  </script>
</body>
</html>
