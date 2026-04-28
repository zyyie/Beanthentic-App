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

  <main class="auth-main">
    <div class="auth-card" style="max-width: 48rem;">
      <a href="index.php#home" class="news-portal-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
          <path d="m12 19-7-7 7-7"/>
          <path d="M19 12H5"/>
        </svg>
        Back to Home
      </a>
      <h1>Privacy Policy</h1>
      <p class="auth-lead">How Beanthentic Coffee handles information when you use this app.</p>
      <div style="display:grid; gap:1rem; font-size:0.92rem; color:#6b574b; line-height:1.7;">
        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">1. Overview</h2>
          <p style="margin:0;">
            Beanthentic is a coffee-focused platform designed to help users explore local coffee information, support farmer visibility,
            and use tools such as account access, GI registration, profile sharing, maps, and related settings pages. This Privacy Policy
            explains what information may be collected through the app, how it is used, and how it is protected.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">2. Information We May Collect</h2>
          <p style="margin:0 0 0.35rem;">
            Depending on the feature you use, Beanthentic may collect information that you voluntarily provide, including:
          </p>
          <ul style="margin:0; padding-left:1.15rem;">
            <li>Your name and email address when you create or use an account.</li>
            <li>Farmer or farm registration details submitted through the GI-related forms.</li>
            <li>Profile-related information shown through QR or profile sharing features.</li>
            <li>Preference data such as notification settings and saved app options.</li>
          </ul>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">3. How We Use Your Information</h2>
          <p style="margin:0 0 0.35rem;">
            The information collected through Beanthentic may be used to:
          </p>
          <ul style="margin:0; padding-left:1.15rem;">
            <li>Provide account access and personalize the app experience.</li>
            <li>Process and display GI registration or profile-related submissions.</li>
            <li>Generate profile links, QR references, and related account tools.</li>
            <li>Save your settings and improve usability across sessions.</li>
            <li>Support platform administration, review workflows, and user assistance.</li>
          </ul>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">4. Local Storage and Device Data</h2>
          <p style="margin:0;">
            Some Beanthentic features use local or session storage on your device to remember your account state, settings, saved profile
            preferences, and similar app data. This helps the app function smoothly without requiring you to re-enter the same details every time.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">5. Profile Links, QR Features, and Shared Data</h2>
          <p style="margin:0;">
            If you use Beanthentic profile or QR features, some information you choose to save or share may be included in generated links
            or profile pages. Please review the information you submit and only share links or QR codes when you are comfortable doing so.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">6. Data Sharing</h2>
          <p style="margin:0;">
            Beanthentic does not sell your personal information. Information may be used within the platform to support its intended services,
            including farmer visibility, account functions, registration review workflows, and user support. Data may also be displayed where
            required by the feature you are actively using.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">7. Data Security</h2>
          <p style="margin:0;">
            We aim to handle information responsibly and limit collection to what is relevant to the app’s functions. However, no digital
            system can guarantee absolute security. Users should also protect their own devices, accounts, and shared links.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">8. Your Choices</h2>
          <p style="margin:0;">
            You may choose what information to provide in Beanthentic. You may also sign out, adjust settings, and avoid sharing optional
            profile or registration details when not required by a feature.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">9. Updates to This Policy</h2>
          <p style="margin:0;">
            This Privacy Policy may be updated from time to time to reflect changes in the app, its features, or its data practices.
            Continued use of Beanthentic after updates means you accept the revised policy.
          </p>
        </section>

        <section>
          <h2 style="margin:0 0 0.35rem; font-size:1rem; color:#2c241c;">10. Contact</h2>
          <p style="margin:0;">
            If you have questions about this Privacy Policy or how your information is used in Beanthentic, please contact the team through
            the official channels linked in <strong>Social</strong> within the app menu.
          </p>
        </section>
      </div>
    </div>
  </main>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
</body>
</html>
