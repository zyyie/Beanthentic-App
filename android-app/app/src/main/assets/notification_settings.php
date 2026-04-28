<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#508020" />
  <title>Notification Settings · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body class="has-app-bottom-nav">
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

  <main class="auth-main">
    <div class="auth-card" style="max-width: 44rem;">
      <a href="index.php#home" class="news-portal-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
          <path d="m12 19-7-7 7-7"/>
          <path d="M19 12H5"/>
        </svg>
        Back to Home
      </a>

      <h1>Notification Settings</h1>
      <p class="auth-lead">Configure dashboard preferences.</p>

      <div class="notif-card">
        <div class="notif-card__head"><strong>Email Notifications</strong></div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>System Events</strong><span>Get notified about system updates and maintenance.</span></div>
          <label class="switch"><input type="checkbox" id="notif-email-system" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>User Registrations</strong><span>Receive alerts when new users register.</span></div>
          <label class="switch"><input type="checkbox" id="notif-email-registrations" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row" style="border-bottom:none;">
          <div class="notif-row__text"><strong>Security Breaches</strong><span>Immediate alerts for security incidents.</span></div>
          <label class="switch"><input type="checkbox" id="notif-email-breaches" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
      </div>

      <div class="notif-card">
        <div class="notif-card__head"><strong>SMS Notifications</strong></div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>System Events</strong><span>Get notified about system updates and maintenance.</span></div>
          <label class="switch"><input type="checkbox" id="notif-sms-system" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>User Registrations</strong><span>Receive alerts when new users register.</span></div>
          <label class="switch"><input type="checkbox" id="notif-sms-registrations" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row" style="border-bottom:none;">
          <div class="notif-row__text"><strong>Security Breaches</strong><span>Immediate alerts for security incidents.</span></div>
          <label class="switch"><input type="checkbox" id="notif-sms-breaches" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
      </div>

      <div class="notif-card">
        <div class="notif-card__head"><strong>In-App Notifications</strong></div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>System Events</strong><span>Get notified about system updates and maintenance.</span></div>
          <label class="switch"><input type="checkbox" id="notif-inapp-system" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row">
          <div class="notif-row__text"><strong>User Registrations</strong><span>Receive alerts when new users register.</span></div>
          <label class="switch"><input type="checkbox" id="notif-inapp-registrations" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
        <div class="notif-row" style="border-bottom:none;">
          <div class="notif-row__text"><strong>Security Breaches</strong><span>Immediate alerts for security incidents.</span></div>
          <label class="switch"><input type="checkbox" id="notif-inapp-breaches" /><span class="switch__track" aria-hidden="true"></span></label>
        </div>
      </div>

      <button type="button" class="btn-primary" id="notif-save-btn" style="margin-top:0.85rem; width:100%; border-radius:14px;">
        Save Notification Settings
      </button>
    </div>
  </main>

  <footer>
    <div class="footer-inner">
      <span><span class="footer-dot"></span> Beanthentic &copy; <span id="year"><?php echo date('Y'); ?></span> · Brewed with care.</span>
      <span>Serving honest coffee, one cup at a time.</span>
    </div>
  </footer>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script>
    (function () {
      var NOTIF_KEY = 'beanthentic_notification_prefs';
      function getNotifIds() {
        return [
          'notif-email-system',
          'notif-email-registrations',
          'notif-email-breaches',
          'notif-sms-system',
          'notif-sms-registrations',
          'notif-sms-breaches',
          'notif-inapp-system',
          'notif-inapp-registrations',
          'notif-inapp-breaches'
        ];
      }

      function loadNotifPrefs() {
        try {
          var raw = localStorage.getItem(NOTIF_KEY);
          var prefs = raw ? JSON.parse(raw) : {};
          getNotifIds().forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.checked = !!prefs[id];
          });
        } catch (e) {}
      }

      function saveNotifPrefs() {
        try {
          var prefsOut = {};
          getNotifIds().forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            prefsOut[id] = !!el.checked;
          });
          localStorage.setItem(NOTIF_KEY, JSON.stringify(prefsOut));
        } catch (e) {}
        if (window.uiController && typeof window.uiController.showNotification === 'function') {
          window.uiController.showNotification('Notification settings saved.', 'info');
        }
      }

      function init() {
        loadNotifPrefs();
        var btn = document.getElementById('notif-save-btn');
        if (btn) btn.addEventListener('click', saveNotifPrefs);
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
    })();
  </script>
</body>
</html>
