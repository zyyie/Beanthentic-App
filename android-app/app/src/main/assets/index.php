<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>
    // Auth gate: redirect guests before homepage renders (prevents flicker).
    (function () {
      function parseUser(raw) {
        if (!raw) return null;
        try {
          var u = JSON.parse(raw);
          return (u && u.email) ? u : null;
        } catch (_err) {
          return null;
        }
      }
      try {
        var localUser = parseUser(localStorage.getItem('beanthentic_user'));
        if (localUser) {
          try { sessionStorage.setItem('beanthentic_user', JSON.stringify(localUser)); } catch (_err2) {}
          return;
        }
        var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
        if (sessionUser) {
          try { localStorage.setItem('beanthentic_user', JSON.stringify(sessionUser)); } catch (_err3) {}
          return;
        }
        if (!sessionUser) {
          window.location.replace('login.php');
        }
      } catch (e) {
        window.location.replace('login.php');
      }
    })();
  </script>
  <title>Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    @keyframes coffeeJump {
      0% { transform: translateY(0) rotate(0deg) scale(1); }
      30% { transform: translateY(-12px) rotate(-8deg) scale(1.05); }
      60% { transform: translateY(0) rotate(6deg) scale(0.98); }
      100% { transform: translateY(0) rotate(0deg) scale(1); }
    }
    #page-loader img {
      animation: coffeeJump 0.9s ease-in-out infinite;
      transform-origin: center;
    }
    /* Homepage top bar format */
    .home-page header {
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      border-radius: 0 0 22px 22px;
      padding: 0.28rem 0.95rem 0.22rem;
      box-shadow: 0 8px 18px rgba(15, 77, 18, 0.24);
    }
    .home-page .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.8rem;
    }
    .home-page .nav-logo-wrap { margin: 0; }
    .home-page .logo {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      text-decoration: none;
      color: #fff;
    }
    .home-page .logo-mark {
      width: 108px;
      height: 108px;
      border-radius: 0;
      background: transparent;
      object-fit: contain;
      padding: 0;
    }
    .home-page .home-brand-text {
      font-size: 2.25rem;
      font-weight: 800;
      line-height: 1.05;
      letter-spacing: -0.02em;
      color: #fff;
    }
    .home-page .header-burger-btn { display: none !important; }
    .home-page .nav-right-cluster {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      margin-left: auto;
    }
    .home-page .header-notifications-btn,
    .home-page .home-account-btn {
      width: 40px;
      height: 40px;
      border-radius: 999px;
      border: none;
      background: #ffffff;
      color: #166534;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }
    .home-page .header-notifications-icon,
    .home-page .home-account-btn svg {
      width: 20px;
      height: 20px;
    }
    .home-page .home-top-meta {
      max-width: 980px;
      margin: 0.5rem auto 0.15rem;
      padding: 0 0.65rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.65rem;
      color: #111827;
      font-size: 0.92rem;
      font-weight: 600;
      letter-spacing: 0.01em;
    }
    .home-page .home-top-meta-left {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      color: #16381a;
      white-space: nowrap;
    }
    .home-page .home-top-meta-left svg {
      width: 16px;
      height: 16px;
      color: #1f7a2e;
      flex-shrink: 0;
    }
    .home-page .home-top-meta-date {
      color: #111827;
      white-space: nowrap;
    }
  </style>
</head>
<body class="has-app-bottom-nav home-page">
  <!-- Loading overlay (shown on open and on navigation clicks) -->
  <div id="page-loader" style="position:fixed; inset:0; background:#ffffff; display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:99999;">
    <img
      src="coffee_bean_loading.png"
      alt="Coffee bean loading"
      style="width:96px; height:96px; object-fit:contain;"
    />
    <div style="margin-top:12px; color:#777777; font-family:inherit; font-size:14px;">Please wait for a moment.</div>
  </div>

  <div
    id="home-register-gate"
    class="home-register-gate"
    hidden
    role="dialog"
    aria-modal="true"
    aria-labelledby="home-register-gate-title"
    aria-hidden="true"
  >
    <div class="home-register-gate-backdrop" aria-hidden="true"></div>
    <div class="home-register-gate-modal">
      <p id="home-register-gate-title" class="home-register-gate-text">
        To access other features, you must register first.
      </p>
      <button type="button" class="home-register-gate-btn" id="home-register-gate-proceed">Proceed</button>
    </div>
  </div>

  <header>
    <div class="nav">
      <div class="nav-logo-wrap">
        <a href="#home" class="logo" aria-label="Beanthentic home">
          <img
            class="logo-mark"
            src="navbar_logo.png"
            alt="Beanthentic"
          />
        </a>
      </div>
      <div class="nav-right-cluster">
        <button
          type="button"
          id="header-notifications-btn"
          class="header-notifications-btn"
          aria-label="Notifications"
          title="Notifications"
        >
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
        <a href="account_settings.html" class="home-account-btn" aria-label="Account settings">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </a>
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
            <span>GI Updates</span>
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

  <div class="home-top-meta" aria-label="Location and date">
    <div class="home-top-meta-left">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 22s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12z"></path>
        <circle cx="12" cy="10" r="2.5"></circle>
      </svg>
      <span>CITY OF LIPA, BATANGAS</span>
    </div>
    <div class="home-top-meta-date" id="homeTopMetaDate">Sat - May 23, 2026 · 08:00 AM</div>
  </div>

  <main class="home-mobile-layout">
    <section id="home" class="home-mobile-shell">
      <div class="home-mobile-hero-card" aria-hidden="true"></div>

      <div class="home-mobile-shortcuts" aria-label="Home shortcuts">
        <a class="home-mobile-shortcut" href="about.php">
          <span class="home-mobile-shortcut-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
              <line x1="12" y1="16" x2="12" y2="12" stroke="currentColor" stroke-width="2"></line>
              <circle cx="12" cy="8" r="1" fill="currentColor"></circle>
            </svg>
          </span>
          <span>About</span>
        </a>
        <a class="home-mobile-shortcut" href="news.php">
          <span class="home-mobile-shortcut-icon home-mobile-shortcut-icon--light">
            <svg
              class="home-mobile-shortcut-updates-svg"
              viewBox="0 0 24 24"
              fill="none"
              aria-hidden="true"
              stroke="currentColor"
              stroke-width="2.15"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M12 5.75a6.25 6.25 0 1 1-5.85 8.35" />
              <path d="M8.2 11.45H5.15V8.35" />
              <path d="M12 8.65v3.35l2.2 1.25" />
            </svg>
          </span>
          <span>GI Updates</span>
        </a>
        <a class="home-mobile-shortcut" href="social.php">
          <span class="home-mobile-shortcut-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
            </svg>
          </span>
          <span>Socials</span>
        </a>
        <a class="home-mobile-shortcut" href="http://10.0.2.2:5000/messages.php" data-beanthentic-flask="/messages.php">
          <span class="home-mobile-shortcut-icon home-mobile-shortcut-icon--light">
            <svg
              viewBox="0 0 24 24"
              aria-hidden="true"
              class="home-mobile-shortcut-msg-svg"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <!-- Custom messages glyph: green strokes on white circle (matches Updates-style pill) -->
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z" />
              <path d="M9 10h5.5M9 12.35h8.75M9 14.65h4.25" stroke-width="1.7" />
            </svg>
          </span>
          <span>Messages</span>
        </a>
      </div>

      <section class="home-mobile-story">
        <h1 class="home-mobile-story-title">The Story Behind <span>Beanthentic</span></h1>
        <p class="home-mobile-story-lead">
          Beanthentic Coffee is a platform that highlights the hard work of local farmers and the
          authenticity of their coffee beans. It showcases different coffee varieties such as Liberica,
          Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique
          characteristics of each bean.
        </p>

        <div class="home-mobile-story-list">
          <article class="home-mobile-story-row">
            <div
              class="home-mobile-story-thumb"
              aria-hidden="true"
              style="background-image:url('story_beans.png'); background-size:cover; background-position:center; background-repeat:no-repeat;"
            ></div>
            <p class="home-mobile-story-row-text">
              Beanthentic Coffee is a platform that highlights the hard work of local farmers and the
              authenticity of their coffee beans. It showcases different coffee varieties such as Liberica,
              Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique
              characteristics of each bean.
            </p>
          </article>
          <article class="home-mobile-story-row">
            <div
              class="home-mobile-story-thumb"
              aria-hidden="true"
              style="background-image:url('story_beans_2.png'); background-size:cover; background-position:center; background-repeat:no-repeat;"
            ></div>
            <p class="home-mobile-story-row-text">
              Beanthentic Coffee is a platform that highlights the hard work of local farmers and the
              authenticity of their coffee beans. It showcases different coffee varieties such as Liberica,
              Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique
              characteristics of each bean.
            </p>
          </article>
              </div>
            </section>
    </section>
  </main>

  <nav class="app-bottom-nav app-bottom-nav--mint" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="#home" id="nav-home" class="app-bottom-nav-link">
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
      <a href="http://10.0.2.2:5000/register-farm" id="nav-register" data-beanthentic-flask="/register-farm" class="app-bottom-nav-link app-bottom-nav-link--featured">
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
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>

  <script>
    (function () {
      var dateEl = document.getElementById('homeTopMetaDate');
      if (dateEl) {
        function renderNow() {
          try {
            var now = new Date();
            var dow = now.toLocaleDateString('en-US', { weekday: 'short' });
            var month = now.toLocaleDateString('en-US', { month: 'short' });
            var day = now.getDate();
            var year = now.getFullYear();
            var time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
            dateEl.textContent = dow + ' - ' + month + ' ' + day + ', ' + year + ' · ' + time;
          } catch (_e) {}
        }
        renderNow();
        // Keep it current while app stays open.
        setInterval(renderNow, 30000);
      }

      function flaskBase() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base');
          if (s && String(s).replace(/\s/g, '')) {
            s = String(s).replace(/\/$/, '');
            // If a stale LAN IP was saved, reset to emulator-safe default.
            if (/^https?:\/\/192\.168\./i.test(s)) {
              try { localStorage.removeItem('beanthentic_flask_base'); } catch (_e) {}
            } else {
              return s;
            }
          }
        } catch (e) {}
        if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
          return (location.origin || '').replace(/\/$/, '');
        }
        return 'http://10.0.2.2:5000';
      }
      function applyFlaskNav() {
        var b = flaskBase();
        document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
          var p = a.getAttribute('data-beanthentic-flask');
          if (p) a.setAttribute('href', b + p);
        });
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyFlaskNav);
      else applyFlaskNav();
    })();
  </script>

  <script>
    (function () {
      function syncBottomNavSignIn() {
        var a = document.getElementById('nav-signin');
        if (!a) return;
        var lbl = a.querySelector('.app-bottom-nav-label');
        var u = null;
        try {
          u = JSON.parse(localStorage.getItem('beanthentic_user') || 'null');
        } catch (e) {
          u = null;
        }
        if (u && u.email) {
          try {
            a.setAttribute('href', new URL('account.php', location.href).href);
          } catch (e1) {
            a.setAttribute('href', 'account.php');
          }
          if (lbl) lbl.textContent = 'Account';
          return;
        }
        try {
          a.setAttribute('href', new URL('login.php', location.href).href);
        } catch (e2) {
          a.setAttribute('href', 'login.php');
        }
        if (lbl) lbl.textContent = 'Sign In';
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', syncBottomNavSignIn);
      else syncBottomNavSignIn();
      window.addEventListener('beanthentic-auth-changed', syncBottomNavSignIn);
      document.addEventListener('DOMContentLoaded', function () {
        var a = document.getElementById('nav-signin');
        if (!a) return;
        a.addEventListener('click', function (e) {
          try {
            var u = JSON.parse(localStorage.getItem('beanthentic_user') || 'null');
            if (u && u.email) return;
          } catch (err) {
            /* fall through */
          }
          e.preventDefault();
          try {
            window.location.assign(new URL('login.php', location.href).href);
          } catch (err2) {
            window.location.assign('login.php');
          }
        }, true);
      });
    })();
  </script>

  <script>
    (function () {
      var loader = document.getElementById('page-loader');
      if (!loader) return;

      var startedAt = Date.now();
      var minVisibleMs = 2000;
      var hideTimer = null;

      function hideLoader() {
        if (!loader) return;
        var elapsed = Date.now() - startedAt;
        var delay = Math.max(0, minVisibleMs - elapsed);
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = setTimeout(function () {
          loader.style.display = 'none';
          try {
            window.dispatchEvent(new CustomEvent('beanthentic-home-loader-hidden'));
          } catch (_e) {}
        }, delay);
      }

      // Hide once the page is fully loaded (images, CSS, etc.)
      window.addEventListener('load', hideLoader);

      // Safety fallback (in case load event doesn't fire)
      if (hideTimer) clearTimeout(hideTimer);
      hideTimer = setTimeout(hideLoader, minVisibleMs + 2000);

      // Show loader on navigation clicks (links), including module links (Register Farm/Map/News/Social).
      document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;

        var href = a.getAttribute('href') || '';
        if (!href) return;
        // Don't show loading overlay for hash navigation (in-page) or sidebar clicks.
        if (href.indexOf('#') === 0) return;
        if (a.getAttribute('data-no-loader') === 'true') return;

        loader.style.display = 'flex';
        startedAt = Date.now();
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = null;
      });
    })();
  </script>

  <script>
    (function () {
      function hasFarmerRegistration() {
        try {
          var id = localStorage.getItem('beanthentic_farmer_id');
          return !!(id && String(id).trim());
        } catch (_e) {
          return false;
        }
      }

      function registerFarmHref() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base');
          var base = s && String(s).replace(/\s/g, '') ? String(s).replace(/\/$/, '') : '';
          if (!base && typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
            base = (location.origin || '').replace(/\/$/, '');
          }
          if (!base) base = 'http://10.0.2.2:5000';
          return base + '/register-farm';
        } catch (_e2) {
          return 'http://10.0.2.2:5000/register-farm';
        }
      }

      var gate = document.getElementById('home-register-gate');
      var proceedBtn = document.getElementById('home-register-gate-proceed');

      function showGate() {
        if (!gate || hasFarmerRegistration()) return;
        gate.hidden = false;
        gate.setAttribute('aria-hidden', 'false');
        document.body.classList.add('home-register-gate-open');
      }

      function hideGate() {
        if (!gate) return;
        gate.hidden = true;
        gate.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('home-register-gate-open');
      }

      window.addEventListener('beanthentic-home-loader-hidden', function () {
        if (hasFarmerRegistration()) return;
        showGate();
      });

      window.addEventListener('storage', function (e) {
        if (e && e.key === 'beanthentic_farmer_id' && hasFarmerRegistration()) {
          hideGate();
        }
      });

      document.addEventListener('DOMContentLoaded', function () {
        if (!proceedBtn || hasFarmerRegistration()) return;
        proceedBtn.addEventListener('click', function () {
          if (proceedBtn.disabled) return;
          proceedBtn.disabled = true;
          var loader = document.getElementById('page-loader');
          hideGate();
          if (loader) {
            loader.style.display = 'flex';
          }
          window.setTimeout(function () {
            try {
              window.location.assign(registerFarmHref());
            } catch (_err) {
              window.location.href = registerFarmHref();
            }
          }, 2000);
        });
      });
    })();
  </script>

  <script>
    // Bottom nav active state is handled globally in js/ui.js (syncAppBottomNavActive).
  </script>
</body>
</html>


