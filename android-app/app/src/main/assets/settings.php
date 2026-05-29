<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#508020" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script>
    (function () {
      try {
        var t = localStorage.getItem('beanthentic_app_theme') || sessionStorage.getItem('beanthentic_app_theme') || 'light';
        var eff = t === 'dark' ? 'dark' : (t === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-beanthentic-theme', t);
        document.documentElement.setAttribute('data-beanthentic-theme-effective', eff);
      } catch (_boot) {}
    })();
  </script>
  <script src="js/beanthentic_session_gate.js?v=20260513-4"></script>
  <script src="js/beanthentic_theme.js?v=20260527-9"></script>
  <title>Settings · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body.settings-page header,
    body.account-security-page header {
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 16px 16px;
      padding-top: env(safe-area-inset-top);
    }
    body.settings-page header .nav,
    body.account-security-page header .nav {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 4.6rem;
      padding: 0.72rem 1.05rem 0.78rem max(0.8rem, env(safe-area-inset-left, 0px));
      box-sizing: border-box;
    }
    body.settings-page .settings-nav-back,
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
    body.settings-page .settings-nav-back svg,
    body.account-security-page .account-security-nav-back svg { width: 20px; height: 20px; }
    body.settings-page .settings-nav-title,
    body.account-security-page .account-security-nav-title {
      margin: 0;
      color: #ffffff;
      font-size: clamp(1.15rem, 4.2vw, 1.55rem);
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: 0.01em;
      text-align: center;
    }
    body.settings-page .settings-main {
      width: min(96vw, 720px);
      max-width: 720px;
      margin: 0 auto;
      padding: 1rem 0.75rem 6rem;
      box-sizing: border-box;
    }
    @media (min-width: 768px) {
      body.settings-page .settings-main {
        width: min(92vw, 840px);
        max-width: 840px;
        padding-left: 1.25rem;
        padding-right: 1.25rem;
      }
    }
    body.settings-page .settings-card {
      background: #f7f7f7;
      border-radius: 16px;
      padding: 1.15rem 1.35rem 1.25rem;
      box-shadow: inset 0 0 0 1px rgba(17, 24, 39, 0.06);
      width: 100%;
      box-sizing: border-box;
    }
    body.settings-page .settings-menu-label {
      display: block;
      margin: 0 0 0.4rem;
      font-size: 0.78rem;
      font-weight: 700;
      color: #374151;
      letter-spacing: 0.02em;
    }
    body.settings-page .settings-dropdown {
      width: 100%;
      box-sizing: border-box;
      padding: 0.72rem 2.2rem 0.72rem 0.85rem;
      border-radius: 12px;
      border: 1px solid #d1d5db;
      background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23145e1e' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 0.65rem center;
      background-size: 1.1rem;
      appearance: none;
      -webkit-appearance: none;
      font-family: inherit;
      font-size: 0.92rem;
      font-weight: 600;
      color: #111827;
      cursor: pointer;
    }
    body.settings-page .settings-dropdown:focus {
      outline: none;
      border-color: #2273eb;
      box-shadow: 0 0 0 3px rgba(34, 115, 235, 0.2);
    }
    /* Section title lives in the green header only — no duplicate heading in the card */
    body.settings-page #settings-hub-main .settings-panel-title {
      display: none;
    }
    body.settings-page .settings-panel {
      display: none;
      margin-top: 0;
      padding-top: 0.15rem;
    }
    body.settings-page .settings-panel.is-active {
      display: block;
      animation: settingsFade 0.22s ease;
    }
    @keyframes settingsFade {
      from { opacity: 0; transform: translateY(4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    body.settings-page .settings-panel-title {
      margin: 0 0 0.35rem;
      font-size: 1.05rem;
      font-weight: 800;
      color: #145e1e;
    }
    body.settings-page .settings-panel-lead {
      margin: 0 0 0.85rem;
      font-size: 0.86rem;
      line-height: 1.45;
      color: #4b5563;
    }
    body.settings-page .settings-panel-lead strong { color: #111827; }
    body.settings-page .settings-open-link {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.35rem;
      padding: 0.55rem 0.95rem;
      border-radius: 999px;
      background: linear-gradient(135deg, #1b5e20, #145218);
      color: #fff;
      font-size: 0.86rem;
      font-weight: 700;
      text-decoration: none;
    }
    body.settings-page .settings-notif-card {
      margin-top: 0.15rem;
      background: #fff;
      width: 100%;
      box-sizing: border-box;
    }
    body.settings-page #panel-notifications .notif-row--compact {
      padding-left: 1.25rem;
      padding-right: 1.25rem;
    }
    body.settings-page #panel-notifications .notif-card__head {
      padding-left: 1.25rem;
      padding-right: 1.25rem;
    }
    body.settings-page #settings-notif-save-btn {
      width: 100%;
      box-sizing: border-box;
    }
    body.settings-page .settings-privacy-body {
      background: #fff;
      border-radius: 14px;
      border: 1px solid rgba(15, 23, 42, 0.08);
      padding: 1rem 1.2rem 1.15rem;
      font-size: 0.9rem;
      line-height: 1.45;
      color: #353535;
    }
    body.settings-page .settings-privacy-updated {
      margin: 0 0 0.75rem;
      font-size: 0.9rem;
      font-weight: 700;
      color: #1d1d1d;
    }
    body.settings-page .settings-privacy-body h2 {
      margin: 0.65rem 0 0.2rem;
      font-size: 1rem;
      font-weight: 800;
      color: #111827;
    }
    body.settings-page .settings-privacy-body h2:first-of-type {
      margin-top: 0.35rem;
    }
    body.settings-page .settings-privacy-body p {
      margin: 0 0 0.35rem;
    }
    body.settings-page .settings-privacy-body ul {
      margin: 0;
      padding-left: 1.1rem;
    }
    body.settings-page .settings-privacy-body li {
      margin-bottom: 0.2rem;
    }
    body.settings-page .settings-privacy-footnote {
      margin: 0.5rem 0 0;
      font-size: 0.82rem;
      color: #6f6f6f;
      font-style: italic;
    }
    body.settings-page .settings-general-block {
      background: #fff;
      border-radius: 14px;
      border: 1px solid rgba(15, 23, 42, 0.08);
      padding: 1rem 1.15rem 1.05rem;
      margin-bottom: 0.75rem;
    }
    body.settings-page .settings-general-block:last-child {
      margin-bottom: 0;
    }
    body.settings-page .settings-general-heading {
      margin: 0 0 0.45rem;
      font-size: 0.95rem;
      font-weight: 800;
      color: #145e1e;
    }
    body.settings-page .settings-general-lead {
      margin: 0 0 0.65rem;
      font-size: 0.84rem;
      line-height: 1.4;
      color: #4b5563;
    }
    body.settings-page .settings-lang-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.55rem;
    }
    body.settings-page .settings-lang-btn {
      border: 1px solid #d1d5db;
      border-radius: 12px;
      padding: 0.65rem 0.5rem;
      background: #fff;
      font-family: inherit;
      font-size: 0.9rem;
      font-weight: 700;
      color: #145e1e;
      cursor: pointer;
    }
    body.settings-page .settings-lang-btn.is-active {
      border-color: #145e1e;
      background: #ecfdf5;
      box-shadow: inset 0 0 0 1px rgba(20, 94, 30, 0.25);
    }
    body.settings-page .settings-version-box {
      margin-top: 0.5rem;
      padding: 0.85rem 0.9rem;
      border-radius: 12px;
      background: #fff;
      border: 1px solid #e5e7eb;
    }
    body.settings-page .settings-version-box dt {
      font-size: 0.72rem;
      font-weight: 700;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    body.settings-page .settings-version-box dd {
      margin: 0.15rem 0 0.65rem;
      font-size: 0.95rem;
      font-weight: 700;
      color: #111827;
    }
    body.settings-page .settings-version-box dd:last-child { margin-bottom: 0; }
    body.settings-page .account-security-form label {
      display: block;
      margin: 0.65rem 0 0.28rem;
      font-size: 0.78rem;
      font-weight: 700;
      color: #111827;
    }
    body.settings-page .account-security-form label:first-child { margin-top: 0; }
    body.settings-page .account-security-form input {
      width: 100%;
      box-sizing: border-box;
      padding: 0.68rem 0.8rem;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      background: #fff;
      font-family: inherit;
      font-size: 0.94rem;
    }
    body.settings-page .account-security-btn {
      margin-top: 1rem;
      width: 100%;
    }

    /* Dark mode — must be last so it overrides light rules above */
    html[data-beanthentic-theme-effective='dark'] body.settings-page,
    html[data-beanthentic-theme-effective='dark'] body.settings-page.account-security-page {
      background: #0d1117 !important;
      color: #e8edf2;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-card {
      background: #1a222c !important;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-general-block,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-notif-card,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-privacy-body,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-version-box,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .notif-card {
      background: #252f3c !important;
      border-color: rgba(255, 255, 255, 0.1) !important;
      color: #e8edf2;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-general-heading,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-panel-title {
      color: #6ee78a !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-general-lead,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-panel-lead,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-menu-label,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-privacy-footnote {
      color: #9ca8b8 !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-panel-lead strong,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-privacy-body h2,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .notif-row__text strong,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .notif-card__head strong {
      color: #e8edf2 !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-dropdown,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .account-security-form input {
      background-color: #252f3c !important;
      border-color: rgba(255, 255, 255, 0.14) !important;
      color: #e8edf2 !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .account-security-form label,
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-version-box dd {
      color: #e8edf2 !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .settings-version-box dt {
      color: #9ca8b8 !important;
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .notif-row {
      border-top-color: rgba(255, 255, 255, 0.08);
    }
    html[data-beanthentic-theme-effective='dark'] body.settings-page .notif-row__text span {
      color: #9ca8b8;
    }
  </style>
</head>
<body class="has-app-bottom-nav settings-page account-security-page">
  <header>
    <div class="nav" role="presentation">
      <a
        id="settings-nav-back"
        href="account_settings.html"
        class="settings-nav-back account-security-nav-back"
        aria-label="Back"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 id="settings-page-title" class="settings-nav-title account-security-nav-title">Settings</h1>
    </div>
  </header>

  <main class="settings-main auth-main" id="settings-hub-main">
    <div class="settings-card">
      <section id="panel-security" class="settings-panel is-active" data-panel="security" aria-labelledby="settings-page-title">
        <p class="settings-panel-lead" data-i18n="settings.securityLead">Change your password. Enter your current password first.</p>
        <form class="account-security-form" onsubmit="return false;">
          <label for="acct-current-pass" data-i18n="settings.currentPassword">Current Password</label>
          <input id="acct-current-pass" type="password" autocomplete="current-password" />
          <label for="acct-new-pass" data-i18n="settings.newPassword">New Password</label>
          <input id="acct-new-pass" type="password" autocomplete="new-password" />
          <label for="acct-confirm-pass" data-i18n="settings.confirmPassword">Confirm New Password</label>
          <input id="acct-confirm-pass" type="password" autocomplete="new-password" />
          <button type="button" id="acct-update-pass-btn" class="btn-primary account-security-btn" data-i18n="settings.updatePassword">Update Password</button>
        </form>
      </section>

      <section id="panel-notifications" class="settings-panel" data-panel="notifications" hidden aria-labelledby="settings-page-title">
        <p class="settings-panel-lead" data-i18n="settings.notifLead">Manage email, SMS, and in-app alerts for registrations and security events.</p>
        <div class="notif-card settings-notif-card">
          <div class="notif-card__head"><strong data-i18n="settings.notifHeading">Notifications</strong></div>
          <div class="notif-row notif-row--compact">
            <div class="notif-row__text"><strong data-i18n="settings.notifLogin">Login Verification</strong></div>
            <label class="switch">
              <input type="checkbox" id="notif-login-verification" />
              <span class="switch__track" aria-hidden="true"></span>
            </label>
          </div>
          <div class="notif-row notif-row--compact">
            <div class="notif-row__text"><strong data-i18n="settings.notifSuspicious">Suspicious Login Alerts</strong></div>
            <label class="switch">
              <input type="checkbox" id="notif-suspicious-login" />
              <span class="switch__track" aria-hidden="true"></span>
            </label>
          </div>
          <div class="notif-row notif-row--compact">
            <div class="notif-row__text"><strong data-i18n="settings.notifAccount">Account Updates</strong></div>
            <label class="switch">
              <input type="checkbox" id="notif-account-updates" />
              <span class="switch__track" aria-hidden="true"></span>
            </label>
          </div>
          <div class="notif-row notif-row--compact">
            <div class="notif-row__text"><strong data-i18n="settings.notifSecurity">Security Reminders</strong></div>
            <label class="switch">
              <input type="checkbox" id="notif-security-reminders" />
              <span class="switch__track" aria-hidden="true"></span>
            </label>
          </div>
          <div class="notif-row notif-row--compact" style="border-bottom:none;">
            <div class="notif-row__text"><strong data-i18n="settings.notifApp">App Updates</strong></div>
            <label class="switch">
              <input type="checkbox" id="notif-app-updates" />
              <span class="switch__track" aria-hidden="true"></span>
            </label>
          </div>
        </div>
        <button type="button" id="settings-notif-save-btn" class="btn-primary account-security-btn" style="margin-top:0.75rem;" data-i18n="settings.notifSave">
          Save Notification Settings
        </button>
      </section>

      <section id="panel-privacy" class="settings-panel" data-panel="privacy" hidden aria-labelledby="settings-page-title">
        <div class="settings-privacy-body">
          <p class="settings-privacy-updated">Last Updated: May 3, 2026</p>
          <p>
            Welcome to Beanthentic. We are committed to protecting the privacy and security of information
            shared by coffee farmers and stakeholders. This Privacy Notice explains how we collect, use, and
            safeguard data within our Random Forest-driven and QR Code-based Coffee Farmer Profiling System.
          </p>
          <h2>1. Information We Collect</h2>
          <p>To effectively automate the profiling process and assess eligibility for Geographical Indication (GI) recognition, we collect the following types of information:</p>
          <ul>
            <li>Farmer Profile Data: Full name, contact details, and farm location.</li>
            <li>Agricultural Data: farm size, coffee variety (e.g., Kapeng Barako), elevation, and specific farming practices.</li>
            <li>System-Generated Data: Unique QR codes assigned to profiles for identification and data retrieval.</li>
          </ul>
          <h2>2. How We Use Your Information</h2>
          <p>The data collected via Beanthentic is used strictly for:</p>
          <ul>
            <li>Profiling &amp; Eligibility Assessment: Automating the evaluation of whether a farm meets the regional standards for GI readiness.</li>
            <li>Data Management: Maintaining an organized database of coffee farmers in Lipa City and surrounding areas to support the local coffee industry.</li>
            <li>System Improvement: Refining our Random Forest algorithms to ensure more accurate profiling and data-driven insights.</li>
          </ul>
          <p>
            Note: Beanthentic is a profiling and support tool. This system does not issue official GI certifications, digital or printed.
            Its purpose is to automate data gathering to support the GI application process at a regional level.
          </p>
          <h2>3. Data Sharing and Disclosure</h2>
          <p>
            We do not sell or rent personal information to third parties. Data may be shared with authorized local government units or agricultural organizations solely for the purpose of supporting regional coffee industry initiatives and GI recognition readiness.
          </p>
          <h2>4. Data Security</h2>
          <p>
            We implement technical and organizational measures to protect your data against unauthorized access, loss, or alteration.
            Access to the profiling database is restricted to authorized personnel involved in the Beanthentic project.
          </p>
          <h2>5. Your Rights</h2>
          <p>As a participant in the Beanthentic profiling system, you have the right to:</p>
          <ul>
            <li>Access and review your profiled information.</li>
            <li>Request corrections to any inaccurate data.</li>
            <li>Inquire about the logic used in our automated profiling assessments.</li>
          </ul>
          <h2>6. Contact Us</h2>
          <p>
            If you have questions regarding this Privacy Notice or how your data is handled within the Beanthentic system,
            please contact the development team at the Polytechnic University of the Philippines - Sto. Tomas Campus.
          </p>
          <p class="settings-privacy-footnote">
            This notice is designed to comply with the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines.
          </p>
        </div>
      </section>

      <section id="panel-general" class="settings-panel" data-panel="general" hidden aria-labelledby="settings-page-title">
        <div class="settings-general-block">
          <h3 class="settings-general-heading" data-i18n="settings.langHeading">Language</h3>
          <select id="settings-lang-select" class="settings-dropdown" aria-label="Language">
            <option value="en" data-i18n="settings.langEnglish">English</option>
            <option value="fil" data-i18n="settings.langFilipino">Filipino</option>
          </select>
        </div>
        <div class="settings-general-block">
          <h3 class="settings-general-heading" data-i18n="settings.themeHeading">Theme</h3>
          <p class="settings-general-lead" data-i18n="settings.themeLead">Choose how the app looks on this device.</p>
          <label class="settings-menu-label" for="settings-theme-select" data-i18n="settings.themeLabel">Appearance</label>
          <select id="settings-theme-select" class="settings-dropdown" aria-label="Theme">
            <option value="light" data-i18n="settings.themeLight">Light</option>
            <option value="dark" data-i18n="settings.themeDark">Dark</option>
            <option value="system" data-i18n="settings.themeSystem">System default</option>
          </select>
        </div>
        <div class="settings-general-block">
          <h3 class="settings-general-heading" data-i18n="settings.aboutHeading">About App Version</h3>
          <p class="settings-general-lead" data-i18n="settings.aboutLead">Beanthentic Coffee — farmer registration and traceability for Lipa City.</p>
          <dl class="settings-version-box">
            <dt data-i18n="settings.aboutAppName">App name</dt>
            <dd>Beanthentic</dd>
            <dt data-i18n="settings.aboutVersion">Version</dt>
            <dd id="settings-app-version">1.0.0 (Capstone 2026)</dd>
            <dt data-i18n="settings.aboutBuild">Build</dt>
            <dd>May 2026</dd>
          </dl>
        </div>
      </section>
    </div>
  </main>

  <nav class="app-bottom-nav app-bottom-nav--mint" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="index.php#home" id="nav-home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label" data-i18n="nav.home">Home</span>
      </a>
      <a href="records.php" id="nav-qr" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--record" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 2h6v2H9z"/><path d="M9 12h6"/><path d="M9 16h6"/><path d="M9 20h4"/></svg>
        </span>
        <span class="app-bottom-nav-label" data-i18n="nav.record">Record</span>
      </a>
      <a href="register_summary.php" id="nav-register" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--pending" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--complete" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label" data-i18n="nav.register">Register</span>
      </a>
      <a href="transaction-history.html" id="nav-history" class="app-bottom-nav-link app-bottom-nav-link--history">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
        </span>
        <span class="app-bottom-nav-label" data-i18n="nav.history">History</span>
      </a>
      <a href="account.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </span>
        <span class="app-bottom-nav-label" data-i18n="nav.account">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/navigation.js"></script>
  <script src="js/auth_lang.js"></script>
  <script src="js/ui.js"></script>
  <script src="js/beanthentic_notification_prefs.js"></script>
  <script>
    (function () {
      var THEME_KEY = 'beanthentic_app_theme';
      var SECTION_KEY = 'beanthentic_settings_section';
      var currentSection = 'security';

      function msg(key) {
        if (window.BeanthenticAuthLang && typeof window.BeanthenticAuthLang.t === 'function') {
          return window.BeanthenticAuthLang.t(key);
        }
        return key;
      }

      function notify(msg, type) {
        if (window.uiController && typeof window.uiController.showNotification === 'function') {
          window.uiController.showNotification(msg, type || 'info');
        }
      }

      function showPanel(id) {
        currentSection = id;
        document.querySelectorAll('.settings-panel').forEach(function (el) {
          var on = el.getAttribute('data-panel') === id;
          el.classList.toggle('is-active', on);
          el.hidden = !on;
        });
        try {
          localStorage.setItem(SECTION_KEY, id);
          sessionStorage.setItem(SECTION_KEY, id);
        } catch (_s) {}
        if (typeof window.syncSettingsHubActiveSection === 'function') {
          window.syncSettingsHubActiveSection(id);
        }
        if (typeof updateHubTitle === 'function') {
          updateHubTitle(id);
        }
      }

      var SECTION_TITLE_KEYS = {
        security: 'title.changePassword',
        notifications: 'title.notifications',
        privacy: 'title.privacy',
        general: 'title.general'
      };

      function normalizeSection(id) {
        var s = String(id || '').trim().toLowerCase();
        if (s === 'language' || s === 'theme' || s === 'about') return 'general';
        return s;
      }

      function resolveInitialSection() {
        var valid = ['security', 'notifications', 'privacy', 'general'];
        var initial = 'security';
        try {
          var q = new URLSearchParams(location.search || '');
          var fromQ = q.get('section');
          if (fromQ) initial = normalizeSection(fromQ);
          else {
            var saved = localStorage.getItem(SECTION_KEY) || sessionStorage.getItem(SECTION_KEY);
            if (saved) initial = normalizeSection(saved);
          }
        } catch (_i) {}
        if (valid.indexOf(initial) < 0) initial = 'security';
        return initial;
      }

      function updateHubTitle(sectionId) {
        var pageTitle = document.getElementById('settings-page-title');
        var key = SECTION_TITLE_KEYS[sectionId] || 'title.settings';
        var label = msg(key);
        if (pageTitle) pageTitle.textContent = label;
        if (window.BeanthenticAuthLang && typeof window.BeanthenticAuthLang.applySettingsNavTitle === 'function') {
          window.BeanthenticAuthLang.applySettingsNavTitle();
        }
      }

      function initHub() {
        var initial = resolveInitialSection();
        showPanel(initial);
        updateHubTitle(initial);

        if (window.BeanthenticNotifPrefs) {
          window.BeanthenticNotifPrefs.load(document);
        }
        var notifSaveBtn = document.getElementById('settings-notif-save-btn');
        if (notifSaveBtn) {
          notifSaveBtn.addEventListener('click', function () {
            if (window.BeanthenticNotifPrefs) {
              window.BeanthenticNotifPrefs.save(document);
            }
            notify(msg('notify.notifSaved'), 'info');
          });
        }

        var langSelect = document.getElementById('settings-lang-select');
        if (langSelect) {
          if (window.BeanthenticAuthLang) {
            langSelect.value = window.BeanthenticAuthLang.getLang();
          }
          langSelect.addEventListener('change', function () {
            var lang = langSelect.value === 'fil' ? 'fil' : 'en';
            if (window.BeanthenticAuthLang && typeof window.BeanthenticAuthLang.setLang === 'function') {
              window.BeanthenticAuthLang.setLang(lang);
            }
            updateHubTitle(currentSection);
            notify(msg(lang === 'fil' ? 'notify.langFil' : 'notify.langEn'), 'info');
          });
        }

        var themeSelect = document.getElementById('settings-theme-select');
        if (themeSelect) {
          var storedTheme = 'light';
          if (window.BeanthenticTheme && typeof window.BeanthenticTheme.getStored === 'function') {
            storedTheme = window.BeanthenticTheme.getStored();
          } else {
            try {
              storedTheme = localStorage.getItem(THEME_KEY) || sessionStorage.getItem(THEME_KEY) || 'light';
            } catch (_t) {}
          }
          if (themeSelect.querySelector('option[value="' + storedTheme + '"]')) {
            themeSelect.value = storedTheme;
          }
          if (window.BeanthenticTheme && typeof window.BeanthenticTheme.applyTheme === 'function') {
            window.BeanthenticTheme.applyTheme(storedTheme, { skipSave: true });
          }
          themeSelect.addEventListener('change', function () {
            var v = themeSelect.value || 'light';
            if (window.BeanthenticTheme && typeof window.BeanthenticTheme.applyTheme === 'function') {
              window.BeanthenticTheme.applyTheme(v);
            } else {
              var eff = v === 'dark' ? 'dark' : (v === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
              try {
                localStorage.setItem(THEME_KEY, v);
                sessionStorage.setItem(THEME_KEY, v);
              } catch (_ts) {}
              document.documentElement.setAttribute('data-beanthentic-theme', v);
              document.documentElement.setAttribute('data-beanthentic-theme-effective', eff);
              if (document.body) {
                document.body.classList.toggle('beanthentic-dark-mode', eff === 'dark');
              }
            }
            notify(msg('notify.themeSaved'), 'info');
          });
        }

        if (window.BeanthenticAuthLang && typeof window.BeanthenticAuthLang.applyAppLang === 'function') {
          window.BeanthenticAuthLang.applyAppLang();
        }
        document.addEventListener('beanthentic-lang-changed', function () {
          updateHubTitle(currentSection);
        });

        var currentPass = document.getElementById('acct-current-pass');
        var newPass = document.getElementById('acct-new-pass');
        var confirmPass = document.getElementById('acct-confirm-pass');
        var updatePassBtn = document.getElementById('acct-update-pass-btn');
        if (updatePassBtn) {
          updatePassBtn.addEventListener('click', function () {
            var cur = String((currentPass && currentPass.value) || '').trim();
            var np = String((newPass && newPass.value) || '').trim();
            var cp = String((confirmPass && confirmPass.value) || '').trim();
            if (!cur || !np || !cp) {
              notify(msg('notify.passFill'));
              return;
            }
            if (np.length < 6) {
              notify(msg('notify.passShort'));
              return;
            }
            if (np !== cp) {
              notify(msg('notify.passMatch'));
              return;
            }
            if (currentPass) currentPass.value = '';
            if (newPass) newPass.value = '';
            if (confirmPass) confirmPass.value = '';
            notify(msg('notify.passUpdated'));
          });
        }
      }

      function init() {
        var backBtn = document.getElementById('settings-nav-back');
        if (backBtn) {
          try {
            var from = new URLSearchParams(location.search || '').get('from');
            if (from === 'account_settings') backBtn.setAttribute('href', 'account_settings.html');
            else backBtn.setAttribute('href', 'account_settings.html');
          } catch (_b) {
            backBtn.setAttribute('href', 'account_settings.html');
          }
        }
        initHub();
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
      else init();
    })();
  </script>
</body>
</html>
