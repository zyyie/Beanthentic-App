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
  <style>
    .privacy-page header {
      position: sticky;
      top: 0;
      z-index: 30;
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 16px 16px;
      padding-top: env(safe-area-inset-top);
    }
    .privacy-page header .nav {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 4.6rem;
      padding: 0.72rem 1.05rem 0.78rem max(0.8rem, env(safe-area-inset-left, 0px));
      box-sizing: border-box;
    }
    .privacy-nav-back {
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
    .privacy-nav-back svg { width: 20px; height: 20px; }
    .privacy-nav-title {
      margin: 0;
      font-size: clamp(1.25rem, 4.6vw, 1.7rem);
      font-weight: 800;
      line-height: 1.08;
      letter-spacing: 0.01em;
      text-align: center;
      white-space: nowrap;
    }
    .privacy-nav-title .privacy-part { color: #c8f5c9; }
    .privacy-nav-title .notice-part { color: #f5e6d8; }
  </style>
</head>
<body class="has-app-bottom-nav privacy-page">
  <header>
    <div class="nav" role="presentation">
      <a
        id="privacy-nav-back"
        href="about.php"
        class="privacy-nav-back"
        aria-label="Back"
        onclick="if(window.history.length > 1){ event.preventDefault(); window.history.back(); }"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="privacy-nav-title" id="privacy-page-heading">
        <span class="privacy-part">Privacy</span>
        <span class="notice-part">Notice</span>
      </h1>
    </div>
  </header>

  <main class="auth-main" style="padding-top: 0.35rem; padding-bottom: 1.2rem;">
    <div class="auth-card" style="max-width: 49rem; background: #f1f3f4; border: none; box-shadow: none; padding: clamp(0.85rem, 2.8vw, 1.35rem) clamp(0.95rem, 3.2vw, 1.45rem) calc(4.95rem + env(safe-area-inset-bottom, 0px));">
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
      var back = document.getElementById('privacy-nav-back');
      if (!back) return;
      try {
        var params = new URLSearchParams(window.location.search || '');
        if (params.get('from') === 'account_settings') {
          back.setAttribute('href', 'account_settings.html');
        }
      } catch (_e) {}
    })();
  </script>
</body>
</html>
