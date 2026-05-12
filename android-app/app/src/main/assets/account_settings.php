<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>Account Settings · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
  <link rel="stylesheet" href="css/responsive.css" />
  <style>
    body.account-settings-page {
      background: #eef6ff;
    }
    .account-settings-main {
      width: min(100%, 560px);
      margin: 0 auto;
      padding: 1rem 0.85rem 6rem;
      box-sizing: border-box;
    }
    .account-settings-card {
      background: #ffffff;
      border: 1px solid rgba(15, 23, 42, 0.1);
      border-radius: 16px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
      padding: 1rem;
    }
    .account-settings-title {
      margin: 0;
      color: #145e1e;
      font-size: 1.35rem;
      font-weight: 800;
      line-height: 1.2;
    }
    .account-settings-sub {
      margin: 0.45rem 0 0;
      color: #475569;
      font-size: 0.96rem;
      line-height: 1.45;
    }
  </style>
</head>
<body class="has-app-bottom-nav account-settings-page">
  <header>
    <div class="nav">
      <div class="nav-logo-wrap">
        <a href="index.php#home" class="logo" aria-label="Beanthentic home">
          <img class="logo-mark" src="navbar_logo.png" alt="Beanthentic" />
        </a>
      </div>
      <div class="nav-right-cluster">
        <button type="button" id="header-notifications-btn" class="header-notifications-btn" aria-label="Notifications" title="Notifications">
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
        <a href="account_settings.php" class="header-notifications-btn" aria-label="Account settings" title="Account settings">
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </a>
      </div>
    </div>
  </header>

  <main class="account-settings-main">
    <section class="account-settings-card" aria-label="Account settings">
      <h1 class="account-settings-title">Account Settings</h1>
      <p class="account-settings-sub">This is your account settings page.</p>
    </section>
  </main>

  <nav class="app-bottom-nav" aria-label="Primary">
    <a href="index.php#home" id="nav-home" class="app-bottom-nav-link">
      <span class="app-bottom-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5.5v-6.75h-5V22H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
      <span class="app-bottom-nav-label">Home</span>
    </a>
    <a href="social.php" id="nav-social" class="app-bottom-nav-link">
      <span class="app-bottom-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
      <span class="app-bottom-nav-label">Social</span>
    </a>
    <a href="messages.php" id="nav-message" class="app-bottom-nav-link">
      <span class="app-bottom-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M21 12a8.5 8.5 0 0 1-8.5 8.5H6l-3 3V12A8.5 8.5 0 1 1 21 12z"/></svg></span>
      <span class="app-bottom-nav-label">Message</span>
    </a>
    <a href="notification_settings.php" id="nav-notif" class="app-bottom-nav-link">
      <span class="app-bottom-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg></span>
      <span class="app-bottom-nav-label">Notification</span>
    </a>
    <a href="account.php" id="nav-signin" class="app-bottom-nav-link">
      <span class="app-bottom-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg></span>
      <span class="app-bottom-nav-label">Account</span>
    </a>
  </nav>
</body>
</html>
