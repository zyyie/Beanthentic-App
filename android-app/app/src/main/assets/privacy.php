<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Privacy Policy · Beanthentic Coffee</title>
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
          <a href="privacy.php" class="header-drawer-link" aria-current="page">
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

  <main class="auth-main" style="padding-top: 0.35rem; padding-bottom: 1.2rem;">
    <div class="auth-card" style="max-width: 49rem; background: #f1f3f4; border: none; box-shadow: none; padding: clamp(0.85rem, 2.8vw, 1.35rem) clamp(0.95rem, 3.2vw, 1.45rem) calc(4.95rem + env(safe-area-inset-bottom, 0px));">
      <a href="about.php" class="news-portal-back" onclick="if(window.history.length > 1){ event.preventDefault(); window.history.back(); }" style="margin-bottom: 1rem;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
          <path d="m12 19-7-7 7-7"/>
          <path d="M19 12H5"/>
        </svg>
        Back
      </a>
      <h1 style="margin-bottom: 0.25rem;">
        <span style="color:#256f2a;">Privacy</span>
        <span style="color:#5a351f;">Notice</span>
      </h1>
      <p style="margin: 0 0 0.85rem; font-size: 0.92rem; font-weight: 700; color: #1d1d1d;">Last Updated: May 3, 2026</p>
      <p style="margin: 0 0 0.38rem; font-size: 0.92rem; color:#353535; line-height:1.34;">
        Welcome to Beanthentic. We are committed to protecting the privacy and security of information
        shared by coffee farmers and stakeholders. This Privacy Notice explains how we collect, use, and
        safeguard data within our Random Forest-driven and QR Code-based Coffee Farmer Profiling System.
      </p>
      <div style="display:grid; gap:0.3rem; font-size:0.92rem; color:#1f1f1f; line-height:1.34;">
        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">1. Information We Collect</h2>
          <p style="margin:0 0 0.2rem;">To effectively automate the profiling process and assess eligibility for Geographical Indication (GI) recognition, we collect the following types of information:</p>
          <ul style="margin:0; padding-left:1.05rem;">
            <li>Farmer Profile Data: Full name, contact details, and farm location.</li>
            <li>Agricultural Data: farm size, coffee variety (e.g., Kapeng Barako), elevation, and specific farming practices.</li>
            <li>System-Generated Data: Unique QR codes assigned to profiles for identification and data retrieval.</li>
          </ul>
        </section>

        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">2. How We Use Your Information</h2>
          <p style="margin:0 0 0.2rem;">The data collected via Beanthentic is used strictly for:</p>
          <ul style="margin:0; padding-left:1.05rem;">
            <li>Profiling &amp; Eligibility Assessment: Automating the evaluation of whether a farm meets the regional standards for GI readiness.</li>
            <li>Data Management: Maintaining an organized database of coffee farmers in Lipa City and surrounding areas to support the local coffee industry.</li>
            <li>System Improvement: Refining our Random Forest algorithms to ensure more accurate profiling and data-driven insights.</li>
          </ul>
          <p style="margin:0.28rem 0 0;">
            Note: Beanthentic is a profiling and support tool. This system does not issue official GI certifications, digital or printed.
            Its purpose is to automate data gathering to support the GI application process at a regional level.
          </p>
        </section>

        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">3. Data Sharing and Disclosure</h2>
          <p style="margin:0;">
            We do not sell or rent personal information to third parties. Data may be shared with authorized local government units or agricultural organizations solely for the purpose of supporting regional coffee industry initiatives and GI recognition readiness.
          </p>
        </section>

        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">4. Data Security</h2>
          <p style="margin:0;">
            We implement technical and organizational measures to protect your data against unauthorized access, loss, or alteration.
            Access to the profiling database is restricted to authorized personnel involved in the Beanthentic project.
          </p>
        </section>

        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">5. Your Rights</h2>
          <p style="margin:0 0 0.2rem;">As a participant in the Beanthentic profiling system, you have the right to:</p>
          <ul style="margin:0; padding-left:1.05rem;">
            <li>Access and review your profiled information.</li>
            <li>Request corrections to any inaccurate data.</li>
            <li>Inquire about the logic used in our automated profiling assessments.</li>
          </ul>
        </section>

        <section style="margin:0;">
          <h2 style="margin:0 0 0.18rem; font-size:1rem; color:#111; font-weight: 800;">6. Contact Us</h2>
          <p style="margin:0;">
            If you have questions regarding this Privacy Notice or how your data is handled within the Beanthentic system,
            please contact the development team at the Polytechnic University of the Philippines - Sto. Tomas Campus.
          </p>
        </section>
      </div>
      <p style="margin:0.42rem 0 0; font-size:0.82rem; color:#6f6f6f; font-style: italic;">
        This notice is designed to comply with the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines.
      </p>
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
