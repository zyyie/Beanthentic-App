<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#2e6f1c" />
  <title>Sign up · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body class="has-app-bottom-nav">
  <header>
    <div class="nav">
      <a href="index.php#home" class="logo" aria-label="Beanthentic home">
        <img class="logo-mark" src="beantHentic_logo.png" alt="Beanthentic" />
      </a>
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
  </header>

  <main class="auth-main">
    <div class="auth-card">
      <h1>Create account</h1>
      <p class="auth-lead">Join Beanthentic to connect with local coffee farmers.</p>
      <form class="auth-form" method="post" action="#" autocomplete="on">
        <label for="signup-name">Full name</label>
        <input id="signup-name" name="name" type="text" required autocomplete="name" placeholder="Your name" />

        <label for="signup-email">Email</label>
        <input id="signup-email" name="email" type="email" required autocomplete="email" placeholder="you@example.com" />

        <label for="signup-password">Password</label>
        <input id="signup-password" name="password" type="password" required autocomplete="new-password" placeholder="••••••••" minlength="8" />

        <label for="signup-password2">Confirm password</label>
        <input id="signup-password2" name="password_confirm" type="password" required autocomplete="new-password" placeholder="••••••••" minlength="8" />

        <button type="submit" class="btn-primary">Create account</button>
      </form>
      <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
  </main>

  <footer>
    <div class="footer-inner">
      <span><span class="footer-dot"></span> Beanthentic &copy; <span id="year"><?php echo date('Y'); ?></span> · Brewed with care.</span>
      <span>Serving honest coffee, one cup at a time.</span>
    </div>
  </footer>

  <nav class="app-bottom-nav" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="index.php#home" id="nav-home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <div class="app-bottom-nav-about">
        <button
          type="button"
          class="app-bottom-nav-link app-bottom-nav-about-btn"
          id="bottom-nav-about-toggle"
          aria-expanded="false"
          aria-haspopup="true"
          aria-controls="bottom-nav-about-menu"
        >
          <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
            <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </span>
          <span class="app-bottom-nav-label">About</span>
        </button>
        <div id="bottom-nav-about-menu" class="app-bottom-nav-about-menu" role="menu" hidden aria-label="About sections">
          <a href="index.php#about-liberica" class="app-bottom-nav-about-item" role="menuitem">History</a>
          <a href="index.php#about-mission-vision" class="app-bottom-nav-about-item" role="menuitem">Mission and Vision</a>
          <a href="index.php#about-how-to-get-there" class="app-bottom-nav-about-item" role="menuitem">How to Get There</a>
        </div>
      </div>
      <a href="http://10.0.2.2:5000/gi" data-beanthentic-flask="/gi" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label">GI Portal</span>
      </a>
      <a href="http://10.0.2.2:5000/maps" data-beanthentic-flask="/maps" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Map</span>
      </a>
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin" data-no-loader="true">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Sign in</span>
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
</body>
</html>
