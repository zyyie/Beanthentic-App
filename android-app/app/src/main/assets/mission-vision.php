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
    .mv-hero {
      background: linear-gradient(160deg, #1c6f20 0%, #0f4a15 100%);
      border-radius: 0 0 16px 16px;
      padding: 1.75rem 1rem 1.6rem;
      color: #fff;
    }
    .mv-hero-row {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 42px;
    }
    .mv-nav-back {
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
    .mv-nav-back svg { width: 18px; height: 18px; }
    .mv-nav-title {
      margin: 0;
      font-size: clamp(1.35rem, 4.4vw, 1.7rem);
      font-weight: 800;
      line-height: 1;
      letter-spacing: 0.02em;
    }
    .section-main {
      min-height: calc(100vh - 180px);
      padding: 1rem 0 6.25rem;
      background: #ffffff;
    }
    .section-wrap { max-width: 980px; margin: 0 auto; padding: 0 16px; }
    .section-card {
      background: transparent;
      border-radius: 0;
      border: none;
      box-shadow: none;
      padding: 0;
      text-align: left;
      max-width: none;
      width: 100%;
    }
    .section-title {
      margin: 0 0 0.75rem;
      max-width: 40rem;
      color: #166534;
      font-size: clamp(0.92rem, 2.5vw, 1.05rem);
      font-weight: 700;
      line-height: 1.35;
      letter-spacing: -0.01em;
    }
    .section-copy {
      margin: 0 0 1.35rem;
      max-width: 40rem;
      color: #111827;
      font-size: 0.98rem;
      line-height: 1.65;
    }
    .mv-panels {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2.25rem;
      margin-top: 0.15rem;
      text-align: center;
      width: 100%;
    }
    .mv-panel {
      background: transparent;
      border: none;
      border-radius: 0;
      padding: 0;
      width: 100%;
      max-width: 38rem;
      margin: 0 auto;
    }
    .mv-panel-title {
      margin: 0 auto 0.85rem;
      font-size: clamp(1.5rem, 5vw, 2.05rem);
      font-weight: 800;
      letter-spacing: 0.02em;
      text-transform: none;
      color: #15803d;
      line-height: 1.15;
      text-align: center;
    }
    .mv-panel-text {
      margin: 0 auto;
      color: #111827;
      font-size: 0.98rem;
      line-height: 1.68;
      text-align: center;
    }
    .section-actions {
      margin-bottom: 0.85rem;
      text-align: left;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      text-decoration: none;
      font-size: 0.82rem;
      font-weight: 600;
      color: #25671E;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body class="has-app-bottom-nav">
  <header class="mv-hero">
    <div class="mv-hero-row">
      <a class="mv-nav-back" href="about.php" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="mv-nav-title">Mission and Vision</h1>
    </div>
  </header>
  <main class="section-main">
    <div class="section-wrap">
      <article class="section-card">
        <p class="section-copy">
          Beanthentic's mission and vision guide how we support local coffee farmers through authentic,
          traceable, and technology-driven coffee systems.
        </p>
        <div class="mv-panels" role="region" aria-label="Mission and vision statements">
          <section class="mv-panel" aria-labelledby="mv-vision-heading">
            <h2 id="mv-vision-heading" class="mv-panel-title">Vision</h2>
            <p class="mv-panel-text">
              To become a leading platform that promotes authentic, high-quality coffee by empowering farmers
              through technology, while ensuring transparency, sustainability, and global recognition of locally
              produced coffee.
            </p>
          </section>
          <section class="mv-panel" aria-labelledby="mv-mission-heading">
            <h2 id="mv-mission-heading" class="mv-panel-title">Mission</h2>
            <p class="mv-panel-text">
              Beanthentic is committed to supporting coffee farmers by providing an integrated system that
              verifies authenticity and enhances traceability through innovative technology. It improves
              farmers' livelihoods and product integrity, while strengthening consumer trust in locally
              sourced coffee.
            </p>
          </section>
        </div>
      </article>
    </div>
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
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
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

