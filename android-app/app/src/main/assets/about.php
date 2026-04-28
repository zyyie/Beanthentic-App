<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>About · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .about-only-main {
      min-height: calc(100vh - 180px);
      padding: 1rem 0 6.25rem;
      background: #ffffff;
    }
    .about-only-wrap {
      max-width: 940px;
      margin: 0 auto;
      padding: 0 16px;
    }
    .about-only-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid rgba(0, 0, 0, 0.06);
      box-shadow: 0 8px 20px rgba(17, 24, 39, 0.08);
      overflow: hidden;
    }
    .about-only-link {
      display: block;
      padding: 1rem 1.25rem;
      text-decoration: none;
      color: #374151;
      font-size: 1.05rem;
      font-weight: 600;
      border-bottom: 1px solid #e5e7eb;
    }
    .about-only-link:last-child { border-bottom: none; }
    .about-only-link:hover {
      background: #f9fafb;
      color: #25671E;
    }
    .about-only-toggle {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.25rem;
      border: none;
      background: #fff;
      color: #374151;
      font-size: 1.05rem;
      font-weight: 600;
      border-bottom: 1px solid #e5e7eb;
      cursor: pointer;
      text-align: left;
    }
    .about-only-toggle:hover {
      background: #f9fafb;
      color: #25671E;
    }
    .about-only-chevron {
      width: 16px;
      height: 16px;
      transition: transform 0.2s ease;
      color: #9ca3af;
      flex-shrink: 0;
    }
    .about-only-toggle[aria-expanded="true"] .about-only-chevron {
      transform: rotate(180deg);
      color: #6b7280;
    }
    .about-only-submenu {
      background: #fff;
      border-bottom: 1px solid #e5e7eb;
    }
    .about-only-submenu[hidden] {
      display: none;
    }
    .about-only-sublink {
      display: block;
      padding: 0.72rem 1.25rem;
      text-decoration: none;
      color: #4b5563;
      font-size: 0.95rem;
      font-weight: 600;
      border-top: 1px solid #f3f4f6;
    }
    .about-only-sublink:hover {
      background: #f9fafb;
      color: #25671E;
    }
    /* Burger icon: white lines, no white box (About page only) */
    header .header-burger-btn {
      border: none;
      background: transparent;
      box-shadow: none;
      color: #ffffff;
    }
    header .header-burger-btn:hover {
      background: rgba(255, 255, 255, 0.10);
      box-shadow: none;
    }
    header .header-burger-btn:active {
      background: rgba(255, 255, 255, 0.16);
      transform: scale(0.97);
    }
    header .header-burger-btn:focus-visible {
      outline-color: rgba(255, 255, 255, 0.92);
    }
  </style>
</head>
<body class="has-app-bottom-nav">
  <header>
    <div class="nav">
      <button
        type="button"
        class="header-burger-btn"
        aria-label="Menu"
        aria-expanded="false"
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
    </div>
  </header>

  <main class="about-only-main">
    <div class="about-only-wrap">
      <div class="about-only-card">
        <button type="button" id="about-history-toggle" class="about-only-toggle" aria-expanded="false" aria-controls="about-history-submenu">
          <span>History</span>
          <svg class="about-only-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div id="about-history-submenu" class="about-only-submenu" hidden>
          <a href="index.php#about-liberica" class="about-only-sublink">Liberica</a>
          <a href="index.php#about-robusta" class="about-only-sublink">Robusta</a>
          <a href="index.php#about-excelsa" class="about-only-sublink">Excelsa</a>
        </div>
        <a href="mission-vision.php" class="about-only-link">Mission and Vision</a>
        <a href="how-to-get-there.php" class="about-only-link">How to Get There</a>
      </div>
    </div>
  </main>

  <nav class="app-bottom-nav" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="index.php#home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <a href="about.php" class="app-bottom-nav-link is-active" aria-current="page">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><circle cx="12" cy="8" r="1" fill="currentColor" stroke="none"/></svg>
        </span>
        <span class="app-bottom-nav-label">About</span>
      </a>
      <a href="http://10.0.2.2:5000/register-farm" data-beanthentic-flask="/register-farm" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Register</span>
      </a>
      <a href="http://10.0.2.2:5000/maps" data-beanthentic-flask="/maps" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Map</span>
      </a>
      <a href="login.php" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

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
      function applyFlaskNav() {
        var b = flaskBase();
        document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
          var p = a.getAttribute('data-beanthentic-flask');
          if (p) a.setAttribute('href', b + p);
        });
      }
      function initHistoryDropdown() {
        var toggle = document.getElementById('about-history-toggle');
        var menu = document.getElementById('about-history-submenu');
        if (!toggle || !menu) return;
        toggle.addEventListener('click', function () {
          var isOpen = toggle.getAttribute('aria-expanded') === 'true';
          toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
          menu.hidden = isOpen;
        });
      }
      function init() {
        applyFlaskNav();
        initHistoryDropdown();
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
    })();
  </script>
</body>
</html>

