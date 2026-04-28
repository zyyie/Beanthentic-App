<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Mission and Vision · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .section-main {
      min-height: calc(100vh - 180px);
      padding: 1rem 0 6.25rem;
      background: #ffffff;
    }
    .section-wrap { max-width: 980px; margin: 0 auto; padding: 0 16px; }
    .section-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid rgba(0, 0, 0, 0.06);
      box-shadow: 0 8px 20px rgba(17, 24, 39, 0.08);
      padding: 1rem;
    }
    .section-title {
      margin: 0 0 0.6rem;
      color: #25671E;
      font-size: 1.3rem;
      font-weight: 700;
    }
    .section-copy { margin: 0 0 0.8rem; color: #374151; line-height: 1.6; }
    .section-img {
      width: 100%;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      display: block;
      margin-top: 0.6rem;
    }
    .section-actions { margin-bottom: 0.8rem; }
    .back-link {
      display: inline-flex;
      align-items: center;
      text-decoration: none;
      font-weight: 600;
      color: #25671E;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body class="has-app-bottom-nav">
  <main class="section-main">
    <div class="section-wrap">
      <div class="section-actions">
        <a class="back-link" href="about.php">← Back to About</a>
      </div>
      <article class="section-card">
        <h1 class="section-title">Mission and Vision</h1>
        <p class="section-copy">
          Beanthentic's mission and vision guide how we support local coffee farmers through authentic,
          traceable, and technology-driven coffee systems.
        </p>
        <img class="section-img" src="mission_vision_about.png" alt="Beanthentic mission and vision" />
      </article>
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
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
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
      var b = flaskBase();
      document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
        var p = a.getAttribute('data-beanthentic-flask');
        if (p) a.setAttribute('href', b + p);
      });
    })();
  </script>
  <script src="js/ui.js"></script>
</body>
</html>

