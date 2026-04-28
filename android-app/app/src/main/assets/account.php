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
</head>
<body class="has-app-bottom-nav account-portfolio-page">
  <header>
    <div class="nav">
      <button
        type="button"
        id="header-burger-btn"
        class="header-burger-btn"
        aria-label="Open menu"
        aria-expanded="false"
        aria-controls="header-nav-drawer"
      >
        <span class="header-burger-line" aria-hidden="true"></span>
        <span class="header-burger-line" aria-hidden="true"></span>
        <span class="header-burger-line" aria-hidden="true"></span>
      </button>
      <div class="nav-logo-wrap">
        <a href="index.php#home" class="logo" aria-label="Beanthentic home">
          <img class="logo-mark" src="beanthentic_logo.png" alt="Beanthentic" />
        </a>
      </div>
      <div class="nav-right-cluster">
        <div id="header-account-snippet" class="header-account-snippet" hidden>
          <span class="header-account-avatar" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <span id="header-account-display" class="header-account-name"></span>
        </div>
        <button
          type="button"
          id="header-notifications-btn"
          class="header-notifications-btn"
          aria-label="Notifications"
          title="Notifications"
        >
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
      </div>
    </div>
    <div id="header-nav-drawer" class="header-nav-drawer" hidden>
      <div class="header-nav-drawer-backdrop" aria-hidden="true"></div>
      <aside class="header-nav-drawer-panel" role="dialog" aria-modal="true" aria-label="Menu">
        <div class="header-nav-drawer-inner">
          <div id="header-drawer-account" class="header-drawer-account"></div>
          <a href="social.php" class="header-drawer-link header-drawer-link--social">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Social</span>
          </a>
          <a href="privacy.php" class="header-drawer-link">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            <span>Privacy Policy</span>
          </a>
          <a href="news.php" class="header-drawer-link">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M4 19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12z"/>
              <path d="M8 9h8"/>
              <path d="M8 13h8"/>
              <path d="M8 17h5"/>
            </svg>
            <span>Updates</span>
          </a>
          <a href="settings.php" class="header-drawer-link">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Settings</span>
          </a>
          <button type="button" id="header-sign-out-btn" class="header-drawer-signout" hidden>Sign out</button>
        </div>
      </aside>
    </div>
  </header>

  <main class="account-portfolio-main" id="account-main">
    <article class="account-portfolio" id="account-portfolio-root" hidden>
      <header class="account-portfolio-hero">
        <div class="account-portfolio-avatar" aria-hidden="true">
          <span class="account-portfolio-avatar-initials" id="account-avatar-initials"></span>
        </div>
        <p class="account-portfolio-kicker">Beanthentic member</p>
        <h1 class="account-portfolio-name" id="account-display-name"></h1>
        <p class="account-portfolio-tagline" id="account-display-email"></p>
      </header>

      <div class="account-portfolio-body">
        <section class="account-portfolio-section" aria-labelledby="account-about-heading">
          <h2 class="account-portfolio-section-title" id="account-about-heading">About you</h2>
          <p class="account-portfolio-bio" id="account-bio-text"></p>
        </section>

        <div class="account-portfolio-stats" role="list">
          <div class="account-portfolio-stat" role="listitem">
            <span class="account-portfolio-stat-label">Member since</span>
            <span class="account-portfolio-stat-value" id="account-member-since">—</span>
          </div>
          <div class="account-portfolio-stat" role="listitem">
            <span class="account-portfolio-stat-label">Account</span>
            <span class="account-portfolio-stat-value account-portfolio-stat-value--accent">Active</span>
          </div>
        </div>

        <section class="account-portfolio-section" aria-labelledby="account-contact-heading">
          <h2 class="account-portfolio-section-title" id="account-contact-heading">Contact</h2>
          <dl class="account-portfolio-dl">
            <div>
              <dt>Email</dt>
              <dd id="account-detail-email"></dd>
            </div>
          </dl>
        </section>

        <section class="account-portfolio-section" aria-labelledby="account-qr-heading">
          <h2 class="account-portfolio-section-title" id="account-qr-heading">Profile QR</h2>
          <div class="profile-qr-card">
            <a href="#" id="account-profile-qr-link" class="profile-qr-link" data-no-loader="true" target="_blank" rel="noopener">Open profile link</a>
            <div class="profile-qr-preview">
              <img id="account-profile-qr-img" class="profile-qr-img" alt="Profile QR code" />
            </div>
            <p class="profile-qr-url" id="account-profile-qr-url"></p>
            <p class="profile-qr-caption">Scan to open your profile.</p>
            <div class="profile-qr-actions">
              <button type="button" class="btn-primary profile-qr-btn" id="account-profile-qr-download">Download</button>
              <button type="button" class="btn-primary profile-qr-btn profile-qr-btn--secondary" id="account-profile-qr-share">Share</button>
            </div>
          </div>
        </section>

        <div class="account-portfolio-actions">
          <button type="button" id="account-page-sign-out" class="btn-primary account-portfolio-signout">Sign out</button>
          <a href="index.php#home" class="account-portfolio-back-link">← Back to home</a>
        </div>
      </div>
    </article>
  </main>

  <nav class="app-bottom-nav" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="index.php#home" id="nav-home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <a href="about.php" class="app-bottom-nav-link app-bottom-nav-about-btn">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><circle cx="12" cy="8" r="1" fill="currentColor" stroke="none"/></svg>
        </span>
        <span class="app-bottom-nav-label">About</span>
      </a>
      <a href="http://10.0.2.2:5000/gi" data-beanthentic-flask="/gi" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Register</span>
      </a>
      <a href="http://10.0.2.2:5000/maps" data-beanthentic-flask="/maps" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Map</span>
      </a>
      <a href="account.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin is-active" aria-current="page">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
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
      function renderProfile() {
        var u = getUser();
        var root = document.getElementById('account-portfolio-root');
        if (!root) return;
        if (!u) {
          root.hidden = true;
          return;
        }
        var rawName = (u.name && String(u.name).trim()) ? String(u.name).trim() : (String(u.email).split('@')[0] || 'Member');
        var name = formatDisplayName(u);
        var first = rawName.split(/\s+/)[0] || rawName;
        var email = String(u.email || '');
        var ini = document.getElementById('account-avatar-initials');
        if (ini) ini.textContent = initialsFromName(rawName);
        var elName = document.getElementById('account-display-name');
        if (elName) elName.textContent = name;
        var elEmail = document.getElementById('account-display-email');
        if (elEmail) elEmail.textContent = email;
        var elDetail = document.getElementById('account-detail-email');
        if (elDetail) elDetail.textContent = email;
        var elSince = document.getElementById('account-member-since');
        if (elSince) elSince.textContent = formatMemberSince(u.signedInAt);
        var elBio = document.getElementById('account-bio-text');
        if (elBio) {
          elBio.textContent =
            'Welcome, ' +
            first +
            ". You're part of the Beanthentic community—we connect you with traceable coffee, verified grower stories, and tools like the GI Portal and origin map.";
        }

        // Profile QR (points to dedicated /profile.php page)
        (function renderGiQr() {
          var img = document.getElementById('account-profile-qr-img');
          var linkEl = document.getElementById('account-profile-qr-link');
          if (!img) return;

          // IMPORTANT:
          // - api.qrserver.com only generates the QR image.
          // - What your phone opens when scanning is the QR "data=" content.
          // So we must put a PUBLICLY REACHABLE profile base URL here (not 127.x / 10.0.2.2).
          var base = '';
          if (location.protocol === 'http:' || location.protocol === 'https:') {
            base = (location.origin || '').replace(/\/$/, '');
          } else {
            // file:// (Android assets) → use user-configured public base URL
            try {
              var pub = localStorage.getItem('beanthentic_public_base');
              if (pub && String(pub).replace(/\s/g, '')) base = String(pub).trim().replace(/\/$/, '');
            } catch (e) {}
            if (!base) {
              try {
                base = String(prompt('Enter PUBLIC base URL for profile links (example: https://yourdomain.com)', '') || '').trim().replace(/\/$/, '');
                if (base) localStorage.setItem('beanthentic_public_base', base);
              } catch (e2) {}
            }
          }
          if (!base) base = 'http://10.0.2.2:5000';

          var profileUrl;
          var farmerId = '';
          try {
            farmerId = String(localStorage.getItem('beanthentic_farmer_id') || '').trim();
          } catch (_e) {}
          try {
            profileUrl = new URL('profile.php', base.replace(/\/?$/, '/')).href;
          } catch (e2) {
            profileUrl = base.replace(/\/$/, '') + '/profile.php';
          }

          var fullGiUrl = profileUrl
            + '?farmer_id=' + encodeURIComponent(farmerId)
            + '&name=' + encodeURIComponent(name)
            + '&email=' + encodeURIComponent(email);
          // Use the exact QRServer template: ...?size=150x150&data=<PROFILE_URL>
          var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(fullGiUrl);

          img.setAttribute('src', qrUrl);
          if (linkEl) linkEl.setAttribute('href', fullGiUrl);
          var urlTextEl = document.getElementById('account-profile-qr-url');
          if (urlTextEl) urlTextEl.textContent = fullGiUrl;

          var downloadBtn = document.getElementById('account-profile-qr-download');
          if (downloadBtn && !downloadBtn.dataset.bound) {
            downloadBtn.dataset.bound = 'true';
            downloadBtn.addEventListener('click', function () {
              try {
                fetch(qrUrl)
                  .then(function (r) { return r.blob(); })
                  .then(function (blob) {
                    var a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'beanthentic-profile-qr.png';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    setTimeout(function () { URL.revokeObjectURL(a.href); }, 1500);
                  });
              } catch (err) {
                if (window.uiController && typeof window.uiController.showNotification === 'function') {
                  window.uiController.showNotification('Download not supported on this device.', 'info');
                }
              }
            });
          }

          var shareBtn = document.getElementById('account-profile-qr-share');
          if (shareBtn && !shareBtn.dataset.bound) {
            shareBtn.dataset.bound = 'true';
            shareBtn.addEventListener('click', function () {
              var shareData = { title: 'Beanthentic GI Info', text: 'My Beanthentic GI info', url: fullGiUrl };
              if (navigator.share) {
                navigator.share(shareData).catch(function () { /* ignore */ });
                return;
              }
              try {
                navigator.clipboard.writeText(fullGiUrl).then(function () {
                  if (window.uiController && typeof window.uiController.showNotification === 'function') {
                    window.uiController.showNotification('Profile link copied.', 'info');
                  }
                });
              } catch (e3) {
                if (window.uiController && typeof window.uiController.showNotification === 'function') {
                  window.uiController.showNotification(fullGiUrl, 'info');
                }
              }
            });
          }
        })();
        root.hidden = false;
      }
      function init() {
        if (!getUser()) {
          redirectGuest();
          return;
        }
        renderProfile();
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
      window.addEventListener('beanthentic-auth-changed', function () {
        if (!/account\.php/i.test(location.pathname || '')) return;
        if (!getUser()) redirectGuest();
        else renderProfile();
      });
      document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('account-page-sign-out');
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
