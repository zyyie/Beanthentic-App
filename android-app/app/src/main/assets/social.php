<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Social · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body { margin: 0; background: #fff; }
    .social-shell {
      max-width: 430px;
      margin: 0 auto;
      padding: 0.65rem 0.55rem 6.1rem;
    }
    .social-back {
      display: inline-flex;
      align-items: center;
      gap: 0.28rem;
      text-decoration: none;
      color: #2f7a24;
      font-size: 0.9rem;
      margin-bottom: 0.55rem;
    }
    .social-back svg { width: 16px; height: 16px; }
    .social-title {
      margin: 0 0 0.6rem;
      font-size: 2rem;
      font-weight: 800;
      line-height: 1.08;
      color: #111827;
    }
    .social-title span { color: #1f7a2e; }
    .social-card {
      background: linear-gradient(165deg, #0f4a15 0%, #1d7a2a 100%);
      border-radius: 12px;
      padding: 0.7rem 0.72rem;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      text-decoration: none;
      margin-bottom: 0.72rem;
    }
    .social-card-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: rgba(255,255,255,0.12);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .social-card-icon svg { width: 22px; height: 22px; }
    .social-kicker {
      margin: 0;
      font-size: 0.75rem;
      opacity: 0.93;
      line-height: 1.1;
    }
    .social-main {
      margin: 0.12rem 0;
      font-size: 1.6rem;
      font-weight: 800;
      line-height: 1.05;
      letter-spacing: 0.01em;
    }
    .social-sub {
      margin: 0;
      font-size: 0.74rem;
      opacity: 0.95;
      line-height: 1.1;
    }
    .social-section-label {
      margin: 0 0 0.45rem;
      font-size: 0.88rem;
      font-weight: 700;
      color: #111827;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }
    .social-link-card {
      background: linear-gradient(165deg, #0f4a15 0%, #1d7a2a 100%);
      border-radius: 12px;
      padding: 0.95rem 0.8rem;
      min-height: 5.5rem;
      box-sizing: border-box;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 0.62rem;
      text-decoration: none;
    }
    .social-link-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: rgba(255,255,255,0.12);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .social-link-icon svg { width: 22px; height: 22px; }
    .social-link-title {
      margin: 0 0 0.14rem;
      font-size: 1.45rem;
      font-weight: 800;
      line-height: 1.08;
    }
    .social-link-sub {
      margin: 0;
      font-size: 0.74rem;
      opacity: 0.95;
      line-height: 1.15;
    }
  </style>
</head>
<body class="has-app-bottom-nav">
  <main class="social-shell">
    <a href="index.php#home" class="social-back" aria-label="Back to home">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back
    </a>
    <h1 class="social-title">Contact <span>Beanthentic</span></h1>

    <a href="tel:+63017000000" class="social-card" aria-label="Call Beanthentic">
      <span class="social-card-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.1 5.18 2 2 0 0 1 5.08 3h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.65 2.62a2 2 0 0 1-.45 2.11L9.1 10.62a16 16 0 0 0 4.28 4.28l1.17-1.18a2 2 0 0 1 2.11-.45c.84.31 1.72.53 2.62.65A2 2 0 0 1 22 16.92z"/></svg>
      </span>
      <span>
        <p class="social-kicker">Call us directly</p>
        <p class="social-main">+63 017 000 000</p>
        <p class="social-sub">Mon-Fri, 8am-5pm PST</p>
      </span>
    </a>

    <p class="social-section-label">Find us on social media</p>
    <a class="social-link-card" href="https://www.facebook.com/share/1G6kwxhijL/" target="_blank" rel="noopener noreferrer" aria-label="Open Beanthentic Coffee on Facebook">
      <span class="social-link-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
      </span>
      <span>
        <p class="social-link-title">Facebook</p>
        <p class="social-link-sub">Beanthentic Coffee</p>
      </span>
    </a>
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
      <a href="/register-farm" id="nav-register" class="app-bottom-nav-link app-bottom-nav-link--featured">
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
</body>
</html>
