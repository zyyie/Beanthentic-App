<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#508020" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>Settings · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body.account-security-page header {
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 16px 16px;
      padding-top: env(safe-area-inset-top);
    }
    body.account-security-page header .nav {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 4.6rem;
      padding: 0.72rem 1.05rem 0.78rem max(0.8rem, env(safe-area-inset-left, 0px));
      box-sizing: border-box;
    }
    body.account-security-page .account-security-nav-back {
      position: absolute;
      left: 0.55rem;
      top: 50%;
      transform: translateY(-50%);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      padding: 8px;
      -webkit-tap-highlight-color: transparent;
    }
    body.account-security-page .account-security-nav-back svg { width: 20px; height: 20px; }
    body.account-security-page .account-security-nav-title {
      margin: 0;
      color: #ffffff;
      font-size: clamp(1.15rem, 4.2vw, 1.55rem);
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: 0.01em;
      text-align: center;
    }
  </style>
</head>
<body class="has-app-bottom-nav account-security-page">
  <header>
    <div class="nav" role="presentation">
      <a
        id="account-security-nav-back"
        href="account_settings.html"
        class="account-security-nav-back"
        aria-label="Back"
        onclick="if(window.history.length > 1){ event.preventDefault(); window.history.back(); }"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="account-security-nav-title">Account Security</h1>
    </div>
  </header>

  <main class="auth-main">
    <div class="auth-card account-security-shell" style="max-width: 58rem;">
      <section class="account-security-card">
        <h2 class="account-security-title">Password Reset</h2>
        <p class="account-security-lead">
          Secure workflow: Enter your <strong>current password</strong> to verify before changing to a new password.
        </p>

        <form class="account-security-form" onsubmit="return false;">
          <label for="acct-current-pass">Current Password</label>
          <input id="acct-current-pass" type="password" autocomplete="current-password" />

          <label for="acct-new-pass">New Password</label>
          <input id="acct-new-pass" type="password" autocomplete="new-password" />

          <label for="acct-confirm-pass">Confirm New Password</label>
          <input id="acct-confirm-pass" type="password" autocomplete="new-password" />

          <button
            type="button"
            id="acct-update-pass-btn"
            class="btn-primary account-security-btn"
          >
            Update Password
          </button>
        </form>
      </section>
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
  <script>
    (function () {
      function init() {
        var currentPass = document.getElementById('acct-current-pass');
        var newPass = document.getElementById('acct-new-pass');
        var confirmPass = document.getElementById('acct-confirm-pass');
        var updatePassBtn = document.getElementById('acct-update-pass-btn');

        if (!updatePassBtn) return;

        updatePassBtn.addEventListener('click', function () {
          var cur = String((currentPass && currentPass.value) || '').trim();
          var np = String((newPass && newPass.value) || '').trim();
          var cp = String((confirmPass && confirmPass.value) || '').trim();

          if (!cur || !np || !cp) {
            if (window.uiController && typeof window.uiController.showNotification === 'function') {
              window.uiController.showNotification('Please fill out all password fields.', 'info');
            }
            return;
          }
          if (np.length < 6) {
            if (window.uiController && typeof window.uiController.showNotification === 'function') {
              window.uiController.showNotification('New password must be at least 6 characters.', 'info');
            }
            return;
          }
          if (np !== cp) {
            if (window.uiController && typeof window.uiController.showNotification === 'function') {
              window.uiController.showNotification('New password and confirmation do not match.', 'info');
            }
            return;
          }

          if (currentPass) currentPass.value = '';
          if (newPass) newPass.value = '';
          if (confirmPass) confirmPass.value = '';

          if (window.uiController && typeof window.uiController.showNotification === 'function') {
            window.uiController.showNotification('Password updated.', 'info');
          }
        });
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
    })();
  </script>
</body>
</html>
