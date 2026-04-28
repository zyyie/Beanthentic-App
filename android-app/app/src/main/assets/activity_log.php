<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#508020" />
  <title>Activity Log · Beanthentic Coffee</title>
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
    <div class="auth-card" style="max-width: 58rem;">
      <a href="settings.php" class="news-portal-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
          <path d="m12 19-7-7 7-7"/>
          <path d="M19 12H5"/>
        </svg>
        All settings
      </a>

      <h1>Activity Log</h1>
      <p class="auth-lead">Search and filter admin actions (login times, IP addresses, and specific data changes).</p>

      <section class="activity-card">
        <div class="activity-controls">
          <div class="activity-control">
            <label for="activity-filter">Action Filter</label>
            <select id="activity-filter">
              <option value="all">All actions</option>
              <option value="login">Admin Login</option>
              <option value="logout">Admin Logout</option>
              <option value="security">Security Change</option>
              <option value="data">Data Update</option>
            </select>
          </div>
          <div class="activity-control activity-control--search">
            <label for="activity-search">Search</label>
            <input id="activity-search" type="search" placeholder="Search (date, action, details, IP)" />
          </div>
        </div>

        <div class="activity-table-wrap" role="region" aria-label="Activity table" tabindex="0">
          <table class="activity-table">
            <thead>
              <tr>
                <th>Date &amp; Time</th>
                <th>Action</th>
                <th>Details</th>
                <th>IP Address</th>
              </tr>
            </thead>
            <tbody id="activity-tbody"></tbody>
          </table>
        </div>
      </section>
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
      var rows = [
        { dt: '2026-03-25 10:22', type: 'login', action: 'Admin Login', details: 'Successful login', ip: '127.0.0.1' },
        { dt: '2026-03-25 12:04', type: 'security', action: 'Security Change', details: 'Enabled 2FA (UI sample)', ip: '127.0.0.1' },
        { dt: '2026-03-25 14:18', type: 'data', action: 'Data Update', details: 'Edited farmer record remarks', ip: '127.0.0.1' },
        { dt: '2026-03-26 09:05', type: 'logout', action: 'Admin Logout', details: 'Session ended', ip: '127.0.0.1' }
      ];

      function norm(s) { return String(s || '').toLowerCase(); }
      function matchesQuery(r, q) {
        if (!q) return true;
        var hay = [r.dt, r.action, r.details, r.ip].join(' | ');
        return norm(hay).indexOf(norm(q)) !== -1;
      }

      function render() {
        var tbody = document.getElementById('activity-tbody');
        if (!tbody) return;
        var filter = (document.getElementById('activity-filter') || {}).value || 'all';
        var q = (document.getElementById('activity-search') || {}).value || '';

        var out = rows.filter(function (r) {
          if (filter && filter !== 'all' && r.type !== filter) return false;
          return matchesQuery(r, q);
        });

        tbody.innerHTML = out.map(function (r) {
          return (
            '<tr>' +
              '<td>' + r.dt + '</td>' +
              '<td><span class="activity-pill activity-pill--' + r.type + '">' + r.action + '</span></td>' +
              '<td>' + r.details + '</td>' +
              '<td><code class="activity-ip">' + r.ip + '</code></td>' +
            '</tr>'
          );
        }).join('') || '<tr><td colspan="4" class="activity-empty">No results.</td></tr>';
      }

      function init() {
        var filterEl = document.getElementById('activity-filter');
        var searchEl = document.getElementById('activity-search');
        if (filterEl) filterEl.addEventListener('change', render);
        if (searchEl) searchEl.addEventListener('input', render);
        render();
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
    })();
  </script>
</body>
</html>
