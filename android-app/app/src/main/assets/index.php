<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_api_urls.js?v=20260527-2"></script>
  <script src="js/beanthentic_profile_store.js?v=20260529-1"></script>
  <script src="js/beanthentic_session_gate.js?v=20260515-3"></script>
  <title>Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    @keyframes coffeeJump {
      0% { transform: translateY(0) rotate(0deg) scale(1); }
      30% { transform: translateY(-12px) rotate(-8deg) scale(1.05); }
      60% { transform: translateY(0) rotate(6deg) scale(0.98); }
      100% { transform: translateY(0) rotate(0deg) scale(1); }
    }
    #page-loader img {
      animation: coffeeJump 0.9s ease-in-out infinite;
      transform-origin: center;
    }
    /* Homepage top bar format — compact bar (logo drives height); keep notch inset */
    .home-page header {
      background: linear-gradient(165deg, #1d7f2e 0%, #176b26 100%);
      border-radius: 0 0 22px 22px;
      padding: calc(env(safe-area-inset-top, 0px) + 0.14rem) max(0.75rem, env(safe-area-inset-right, 0px)) 0.12rem
        max(0.75rem, env(safe-area-inset-left, 0px));
      box-shadow: 0 8px 18px rgba(23, 107, 38, 0.22);
    }
    .home-page .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.65rem;
      padding: 0;
      min-height: 0;
    }
    .home-page .nav-logo-wrap { margin: 0; }
    .home-page .logo {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      text-decoration: none;
      color: #fff;
    }
    .home-page .logo-mark {
      width: 96px;
      height: 96px;
      border-radius: 0;
      background: transparent;
      object-fit: contain;
      padding: 0;
      transform: none !important;
      animation: none;
    }
    .home-page .home-brand-text {
      font-size: 2.25rem;
      font-weight: 800;
      line-height: 1.05;
      letter-spacing: -0.02em;
      color: #fff;
    }
    .home-page .header-burger-btn { display: none !important; }
    .home-page .nav-right-cluster {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      margin-left: auto;
    }
    .home-page .header-notifications-btn,
    .home-page .home-account-btn {
      width: 36px;
      height: 36px;
      border-radius: 999px;
      border: none;
      background: #ffffff;
      color: #166534;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }
    .home-page .home-account-btn {
      overflow: hidden;
    }
    .home-page .home-account-btn img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
      object-position: center;
    }
    .home-page .home-account-btn.has-photo svg {
      display: none !important;
    }
    .home-page .header-notifications-icon,
    .home-page .home-account-btn svg {
      width: 18px;
      height: 18px;
    }
    .home-page .home-top-meta {
      width: min(100%, var(--bt-app-content-max, 100%));
      max-width: var(--bt-app-content-max, 100%);
      margin: 0.85rem auto 0.75rem;
      padding: 0 max(0.75rem, env(safe-area-inset-right, 0px)) 0 max(0.75rem, env(safe-area-inset-left, 0px));
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.65rem;
      color: #111827;
      font-size: 0.92rem;
      font-weight: 600;
      letter-spacing: 0.01em;
    }
    .home-page .home-top-meta-left {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      color: #16381a;
      white-space: nowrap;
    }
    .home-page .home-top-meta-left svg {
      width: 16px;
      height: 16px;
      color: #1f7a2e;
      flex-shrink: 0;
    }
    .home-page .home-top-meta-date {
      color: #111827;
      white-space: nowrap;
    }

    /* Video controls overlay (home hero card) — height from components.css */
    .home-video-controls {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      padding: 0.55rem 0.65rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.55rem;
      background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.62) 70%, rgba(0,0,0,0.72) 100%);
      color: #ffffff;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .home-mobile-hero-card.is-video-playing .home-video-controls {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
    #homeHeroVideo {
      cursor: pointer;
    }
    .home-video-btn {
      border: 0;
      border-radius: 999px;
      padding: 0.55rem;
      width: 44px;
      height: 44px;
      color: #0b3b14;
      background: #ffffff;
      box-shadow: 0 8px 18px rgba(0,0,0,0.25);
      cursor: pointer;
      white-space: nowrap;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 0;
    }
    .home-video-btn:active { transform: translateY(1px); }
    .home-video-btn svg { width: 20px; height: 20px; }
    .home-video-btn svg[hidden] { display: none !important; }
    .home-video-volume-wrap {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .home-video-seek-wrap {
      flex: 1;
      display: inline-flex;
      align-items: center;
      min-width: 0;
    }
    .home-video-seek {
      width: 100%;
      accent-color: #ffffff;
      cursor: pointer;
    }
    .home-video-volume-pop {
      position: absolute;
      right: 0.05rem;
      bottom: 3.15rem;
      width: 44px;
      height: 160px;
      padding: 0.55rem 0.35rem;
      border-radius: 14px;
      background: rgba(255,255,255,0.92);
      box-shadow: 0 14px 34px rgba(0,0,0,0.22);
      display: none;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(6px);
    }
    .home-video-volume-wrap.is-open .home-video-volume-pop { display: flex; }
    .home-video-volume {
      width: 140px;
      height: 24px;
      transform: rotate(-90deg);
      accent-color: #32c24d;
    }
  </style>
</head>
<body class="has-app-bottom-nav home-page">
  <!-- Loading overlay (shown on open and on navigation clicks) -->
  <div id="page-loader" style="position:fixed; inset:0; background:#ffffff; display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:99999;">
    <img
      src="coffee_bean_loading.png"
      alt="Coffee bean loading"
      style="width:96px; height:96px; object-fit:contain;"
    />
    <div style="margin-top:12px; color:#777777; font-family:inherit; font-size:14px;">Please wait for a moment.</div>
  </div>

  <div
    id="home-register-gate"
    class="home-register-gate"
    hidden
    role="dialog"
    aria-modal="true"
    aria-labelledby="home-register-gate-title"
    aria-hidden="true"
  >
    <div class="home-register-gate-backdrop" aria-hidden="true"></div>
    <div class="home-register-gate-modal">
      <p id="home-register-gate-title" class="home-register-gate-text">
        To access other features, you must register first.
      </p>
      <button type="button" class="home-register-gate-btn" id="home-register-gate-proceed">Proceed</button>
    </div>
  </div>

  <header>
    <div class="nav">
      <div class="nav-logo-wrap">
        <a href="#home" class="logo" aria-label="Beanthentic home">
          <img
            class="logo-mark"
            src="navbar_logo.png"
            alt="Beanthentic"
          />
        </a>
      </div>
      <div class="nav-right-cluster">
        <button
          type="button"
          id="header-notifications-btn"
          class="header-notifications-btn"
          aria-label="Notifications"
          title="Notifications"
        >
          <svg class="header-notifications-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
        <a href="account_settings.html" class="home-account-btn" id="homeHeaderProfileBtn" aria-label="Account settings">
          <img id="homeHeaderProfileImg" alt="Profile photo" hidden />
          <svg id="homeHeaderProfileIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </a>
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
            <span>GI Updates</span>
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

  <div class="home-top-meta" aria-label="Location and date">
    <div class="home-top-meta-left">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 22s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12z"></path>
        <circle cx="12" cy="10" r="2.5"></circle>
      </svg>
      <span>CITY OF LIPA, BATANGAS</span>
              </div>
    <div class="home-top-meta-date" id="homeTopMetaDate">Sat - May 23, 2026 · 08:00 AM</div>
              </div>

  <main class="home-mobile-layout">
    <section id="home" class="home-mobile-shell">
      <div class="home-mobile-hero-card" aria-label="Beanthentic video preview">
        <video
          id="homeHeroVideo"
          src="home/beanthentic_vid.mp4"
          autoplay
          muted
          playsinline
          preload="metadata"
          aria-label="Tap to play or pause video"
          style="width:100%; height:100%; object-fit:cover; display:block; border-radius:inherit;"
        ></video>
        <div class="home-video-controls" aria-label="Video volume">
          <div class="home-video-seek-wrap" aria-label="Video progress">
            <input id="homeVideoSeek" class="home-video-seek" type="range" min="0" max="1000" value="0" />
          </div>
          <div class="home-video-volume-wrap">
            <button type="button" class="home-video-btn" id="homeVideoVolumeBtn" aria-label="Volume">
              <svg id="homeVideoVolumeIconOn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 5 6 9H2v6h4l5 4V5z"></path>
                <path d="M15.5 8.5a4.5 4.5 0 0 1 0 7"></path>
                <path d="M18.5 5.5a8.5 8.5 0 0 1 0 13"></path>
              </svg>
              <svg id="homeVideoVolumeIconOff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
                <path d="M11 5 6 9H2v6h4l5 4V5z"></path>
                <path d="M22 9l-6 6"></path>
                <path d="M16 9l6 6"></path>
              </svg>
            </button>
            <div class="home-video-volume-pop" aria-label="Volume slider">
              <input id="homeVideoVolume" class="home-video-volume" type="range" min="0" max="100" value="0" />
            </div>
          </div>
        </div>
      </div>

      <div class="home-mobile-shortcuts" aria-label="Home shortcuts">
        <a class="home-mobile-shortcut" href="about.php?from=home">
          <span class="home-mobile-shortcut-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
              <line x1="12" y1="16" x2="12" y2="12" stroke="currentColor" stroke-width="2"></line>
              <circle cx="12" cy="8" r="1" fill="currentColor"></circle>
            </svg>
          </span>
          <span>About</span>
        </a>
        <a class="home-mobile-shortcut" href="news.php">
          <span class="home-mobile-shortcut-icon home-mobile-shortcut-icon--light">
            <svg
              class="home-mobile-shortcut-updates-svg"
              viewBox="0 0 24 24"
              fill="none"
              aria-hidden="true"
              stroke="currentColor"
              stroke-width="2.15"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M12 5.75a6.25 6.25 0 1 1-5.85 8.35" />
              <path d="M8.2 11.45H5.15V8.35" />
              <path d="M12 8.65v3.35l2.2 1.25" />
            </svg>
          </span>
          <span>GI Updates</span>
        </a>
        <a class="home-mobile-shortcut" href="social.php">
          <span class="home-mobile-shortcut-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
            </svg>
          </span>
          <span>Socials</span>
        </a>
        <a class="home-mobile-shortcut" href="http://10.0.2.2:8080/messages.php" data-beanthentic-flask="/messages.php">
          <span class="home-mobile-shortcut-icon home-mobile-shortcut-icon--light">
            <span class="beanthentic-msg-badge" data-message-unread-badge hidden aria-hidden="true"></span>
            <svg
              viewBox="0 0 24 24"
              aria-hidden="true"
              class="home-mobile-shortcut-msg-svg"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <!-- Custom messages glyph: green strokes on white circle (matches Updates-style pill) -->
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z" />
              <path d="M9 10h5.5M9 12.35h8.75M9 14.65h4.25" stroke-width="1.7" />
            </svg>
          </span>
          <span>Messages</span>
        </a>
            </div>

      <section class="home-mobile-story">
        <div class="home-mobile-story-list">
          <article class="home-mobile-story-row">
            <div class="home-mobile-story-media">
              <h1 class="home-mobile-story-title">The Story Behind <span>Beanthentic</span></h1>
              <div
                class="home-mobile-story-thumb"
                aria-hidden="true"
                style="background-image:url('story_beans.png'); background-size:cover; background-position:center; background-repeat:no-repeat;"
              ></div>
            </div>
            <div class="home-mobile-story-copy">
              <p class="home-mobile-story-row-text">
                The platform was created to bridge the gap between coffee farmers and consumers by promoting transparency, authenticity, and appreciation for locally produced coffee. Through Beanthentic Coffee, customers can better understand the value of every coffee bean and the effort invested by farmers from cultivation to harvesting.
              </p>
              <p class="home-mobile-story-row-text home-mobile-story-row-text--follow">
                Beanthentic Coffee started with a simple goal: to help local coffee farmers receive fair recognition and better opportunities for their hard work. Many farmers produce high-quality coffee beans such as Liberica, Robusta, and Excelsa, yet their products are often undervalued in the market. Because of limited exposure and lack of direct connection with consumers, farmers sometimes struggle to sell their beans at the right price. This inspired the creation of Beanthentic Coffee — a platform that promotes authentic locally grown coffee while sharing the story, origin, and quality behind every bean.
              </p>
            </div>
          </article>
              </div>
            </section>
    </section>
  </main>

  <nav class="app-bottom-nav app-bottom-nav--mint" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="#home" id="nav-home" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <a href="records.php" id="nav-qr" class="app-bottom-nav-link">
          <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--record" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 2h6v2H9z"/><path d="M9 12h6"/><path d="M9 16h6"/><path d="M9 20h4"/></svg>
          </span>
        <span class="app-bottom-nav-label">Record</span>
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

  <script src="js/beanthentic_datetime.js"></script>
  <script src="js/beanthentic_message_badge.js"></script>
  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>

  <script>
    (function () {
      var dateEl = document.getElementById('homeTopMetaDate');
      if (dateEl) {
        function renderNow() {
          try {
            if (window.BeanthenticDateTime && typeof window.BeanthenticDateTime.formatNow === 'function') {
              dateEl.textContent = window.BeanthenticDateTime.formatNow();
              return;
            }
          } catch (_e) {}
        }
        renderNow();
        setInterval(renderNow, 30000);
      }

      function flaskBase() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base') || sessionStorage.getItem('beanthentic_flask_base');
          if (s && String(s).replace(/\s/g, '')) {
            return String(s).replace(/\/$/, '');
          }
        } catch (e) {}
        if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
          return (location.origin || '').replace(/\/$/, '');
        }
        return 'http://10.0.2.2:8080';
      }
      function applyFlaskNav() {
        var b = flaskBase();
        document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
          var p = a.getAttribute('data-beanthentic-flask');
          if (p) a.setAttribute('href', b + p);
        });
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyFlaskNav);
      else applyFlaskNav();
    })();
  </script>

  <script>
    (function () {
      function initHomeVideoControls() {
        var v = document.getElementById('homeHeroVideo');
        var volume = document.getElementById('homeVideoVolume');
        var seek = document.getElementById('homeVideoSeek');
        var volWrap = (volume && volume.closest) ? volume.closest('.home-video-volume-wrap') : null;
        var volBtn = document.getElementById('homeVideoVolumeBtn');
        var volOn = document.getElementById('homeVideoVolumeIconOn');
        var volOff = document.getElementById('homeVideoVolumeIconOff');
        var heroCard = v ? v.closest('.home-mobile-hero-card') : null;
        if (!v || !volume || !seek) return;

        // Keep autoplay-friendly: start muted, then unmute when user touches volume.
        try { volume.value = v.muted ? '0' : String(Math.round((v.volume || 0) * 100)); } catch (_e0) {}

        function syncControlsVisibility() {
          if (!heroCard) return;
          try {
            if (v.paused || v.ended) {
              heroCard.classList.remove('is-video-playing');
            } else {
              heroCard.classList.add('is-video-playing');
              if (volWrap) volWrap.classList.remove('is-open');
            }
          } catch (_cv) {}
        }

        function syncVolumeIcon() {
          try {
            var isMuted = v.muted || (v.volume === 0);
            if (volOn) volOn.hidden = isMuted;
            if (volOff) volOff.hidden = !isMuted;
          } catch (_e) {}
        }
        function syncSeekBar() {
          try {
            var dur = Number(v.duration || 0);
            if (!dur || !isFinite(dur)) {
              seek.value = '0';
              return;
            }
            var pct = Math.max(0, Math.min(1, Number(v.currentTime || 0) / dur));
            seek.value = String(Math.round(pct * 1000));
          } catch (_seekSync) {}
        }

        if (volWrap) {
          volWrap.addEventListener('click', function (e) {
            e.stopPropagation();
          });
        }

        if (volBtn && volWrap) {
          volBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            try { volWrap.classList.toggle('is-open'); } catch (_e) {}
          });
          document.addEventListener('click', function (e) {
            if (!volWrap.classList.contains('is-open')) return;
            var t = e.target;
            if (t && volWrap.contains(t)) return;
            volWrap.classList.remove('is-open');
          }, true);
        }

        volume.addEventListener('input', function (e) {
          e.stopPropagation();
          var val = 0;
          try { val = Math.max(0, Math.min(100, parseInt(volume.value, 10) || 0)); } catch (_e) { val = 0; }
          try {
            v.muted = val === 0;
            v.volume = val / 100;
          } catch (_e2) {}
          syncVolumeIcon();
        });
        seek.addEventListener('input', function (e) {
          e.stopPropagation();
          try {
            var dur = Number(v.duration || 0);
            if (!dur || !isFinite(dur)) return;
            var pct = Math.max(0, Math.min(1000, parseInt(seek.value, 10) || 0)) / 1000;
            v.currentTime = dur * pct;
          } catch (_seekSet) {}
        });

        v.addEventListener('play', syncControlsVisibility);
        v.addEventListener('pause', syncControlsVisibility);
        v.addEventListener('ended', syncControlsVisibility);
        v.addEventListener('timeupdate', syncSeekBar);
        v.addEventListener('loadedmetadata', syncSeekBar);
        v.addEventListener('durationchange', syncSeekBar);

        v.addEventListener('click', function (e) {
          e.preventDefault();
          try {
            if (v.paused) {
              var p = v.play();
              if (p && typeof p.catch === 'function') p.catch(function () {});
            } else {
              v.pause();
            }
          } catch (_tap) {}
          syncControlsVisibility();
        });

        syncControlsVisibility();
        setTimeout(syncControlsVisibility, 0);
        v.addEventListener('loadedmetadata', syncControlsVisibility);
        syncVolumeIcon();
        syncSeekBar();
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initHomeVideoControls);
      else initHomeVideoControls();
    })();
  </script>

  <script>
    (function () {
      function syncHeaderProfilePhoto() {
        var btn = document.getElementById('homeHeaderProfileBtn');
        var img = document.getElementById('homeHeaderProfileImg');
        var icon = document.getElementById('homeHeaderProfileIcon');
        if (!btn || !img || !icon) return;

        try {
          if (!window.BeanthenticProfileStore) throw new Error('missing store');
          var profile = window.BeanthenticProfileStore.getFarmerProfile();
          var src = window.BeanthenticProfileStore.farmerProfilePhotoSrc(profile);
          if (src) {
            img.src = src + (src.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
            img.removeAttribute('hidden');
            btn.classList.add('has-photo');
            icon.setAttribute('hidden', '');
            return;
          }
        } catch (_e) {}

        try { img.removeAttribute('src'); } catch (_e2) {}
        img.setAttribute('hidden', '');
        btn.classList.remove('has-photo');
        icon.removeAttribute('hidden');
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', syncHeaderProfilePhoto);
      else syncHeaderProfilePhoto();
      window.addEventListener('beanthentic-profile-changed', syncHeaderProfilePhoto);
      window.addEventListener('beanthentic-auth-changed', syncHeaderProfilePhoto);
    })();
  </script>

  <script>
    (function () {
      function syncBottomNavSignIn() {
        var a = document.getElementById('nav-signin');
        if (!a) return;
        var lbl = a.querySelector('.app-bottom-nav-label');
        var u = null;
        try {
          u = JSON.parse(localStorage.getItem('beanthentic_user') || 'null');
        } catch (e) {
          u = null;
        }
        if (u && u.email) {
          try {
            a.setAttribute('href', new URL('account.php', location.href).href);
          } catch (e1) {
            a.setAttribute('href', 'account.php');
          }
          if (lbl) lbl.textContent = 'Account';
          return;
        }
        try {
          a.setAttribute('href', new URL('login.php', location.href).href);
        } catch (e2) {
          a.setAttribute('href', 'login.php');
        }
        if (lbl) lbl.textContent = 'Sign In';
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', syncBottomNavSignIn);
      else syncBottomNavSignIn();
      window.addEventListener('beanthentic-auth-changed', syncBottomNavSignIn);
      document.addEventListener('DOMContentLoaded', function () {
        var a = document.getElementById('nav-signin');
        if (!a) return;
        a.addEventListener('click', function (e) {
          try {
            var u = JSON.parse(localStorage.getItem('beanthentic_user') || 'null');
            if (u && u.email) return;
          } catch (err) {
            /* fall through */
          }
          e.preventDefault();
          try {
            window.location.assign(new URL('login.php', location.href).href);
          } catch (err2) {
            window.location.assign('login.php');
          }
        }, true);
      });
    })();
  </script>

  <script>
    (function () {
      var loader = document.getElementById('page-loader');
      if (!loader) return;

      var startedAt = Date.now();
      var minVisibleMs = 2000;
      var hideTimer = null;

      function hideLoader() {
        if (!loader) return;
        var elapsed = Date.now() - startedAt;
        var delay = Math.max(0, minVisibleMs - elapsed);
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = setTimeout(function () {
          loader.style.display = 'none';
          try {
            window.dispatchEvent(new CustomEvent('beanthentic-home-loader-hidden'));
          } catch (_e) {}
        }, delay);
      }

      // Hide once the page is fully loaded (images, CSS, etc.)
      window.addEventListener('load', hideLoader);

      // Safety fallback (in case load event doesn't fire)
      if (hideTimer) clearTimeout(hideTimer);
      hideTimer = setTimeout(hideLoader, minVisibleMs + 2000);

      // Show loader on navigation clicks (links), including module links (Register Farm/Map/News/Social).
      document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;

        var href = a.getAttribute('href') || '';
        if (!href) return;
        // Don't show loading overlay for hash navigation (in-page) or sidebar clicks.
        if (href.indexOf('#') === 0) return;
        if (a.getAttribute('data-no-loader') === 'true') return;

        loader.style.display = 'flex';
        startedAt = Date.now();
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = null;
      });
    })();
  </script>

  <script>
    (function () {
      /**
       * Login id stored in beanthentic_user.email (phone or email). Legacy sessions may only
       * have phone_number / login — without this, shouldShowGate() is false and no popup.
       */
      function getSignedInLoginKey() {
        function fromUser(u) {
          if (!u || typeof u !== 'object') return '';
          var e = u.email != null ? String(u.email).trim() : '';
          if (e) return e.toLowerCase();
          var ph = u.phone_number != null ? String(u.phone_number).trim() : '';
          if (ph) return ph.toLowerCase();
          var lg = u.login != null ? String(u.login).trim() : '';
          if (lg) return lg.toLowerCase();
          return '';
        }
        try {
          var rawL = localStorage.getItem('beanthentic_user');
          var rawS = sessionStorage.getItem('beanthentic_user');
          return fromUser(rawL ? JSON.parse(rawL) : null) || fromUser(rawS ? JSON.parse(rawS) : null) || '';
        } catch (_e) {
          return '';
        }
      }

      /** Match login.php / ui.js: same account may be keyed as +639…, 09…, or 9… in farmer_id_map. */
      function loginKeyVariants(v) {
        var out = [];
        var s = String(v || '').trim().toLowerCase();
        if (!s) return out;
        out.push(s);
        var d = s.replace(/\D/g, '');
        if (d.length === 10 && d.charAt(0) === '9') {
          out.push('+63' + d);
          out.push('0' + d);
        }
        if (d.indexOf('63') === 0 && d.length >= 12) out.push('0' + d.slice(2));
        if (d.indexOf('0') === 0 && d.length >= 11) out.push('+63' + d.slice(1));
        var seen = {};
        return out.filter(function (x) {
          if (!x || seen[x]) return false;
          seen[x] = true;
          return true;
        });
      }

      function sanitizeFarmerIdMapForCurrentUser() {
        try {
          var email = getSignedInLoginKey();
          if (!email) return;
          var keys = loginKeyVariants(email);
          var rawMap = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
          if (!rawMap) return;
          var map = JSON.parse(rawMap);
          if (!map || typeof map !== 'object') return;
          var changed = false;
          for (var i = 0; i < keys.length; i += 1) {
            var k = keys[i];
            if (!Object.prototype.hasOwnProperty.call(map, k)) continue;
            var n = Number(map[k]);
            if (map[k] != null && (!Number.isFinite(n) || n <= 0)) {
              delete map[k];
              changed = true;
            }
          }
          if (changed) {
            try {
              localStorage.setItem('beanthentic_farmer_id_map', JSON.stringify(map));
              sessionStorage.setItem('beanthentic_farmer_id_map', JSON.stringify(map));
            } catch (_w) {}
          }
        } catch (_e) {}
      }

      function hasFarmerRegistration() {
        try {
          var rawUser = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          var user = rawUser ? JSON.parse(rawUser) : null;
          if (!user) return false;
          if (user.registration_complete === true) return true;
          var st = String(user.farmer_status || '').toLowerCase();
          if (st === 'active') return true;
          if (st === 'pending' || user.registration_complete === false) return false;
          /*
           * Signup creates farmers row + farmer_id immediately with status pending — NOT “completed”
           * registration. Do not treat farmer_id / map alone as complete (old bug: no popup forever).
           */
          return false;
        } catch (_e) {
          return false;
        }
      }

      /** Remove invalid farmer_id on the user object (stale "0" or bad cache). */
      function normalizeUserFarmerIdInStorage() {
        try {
          var rawL = localStorage.getItem('beanthentic_user');
          var rawS = sessionStorage.getItem('beanthentic_user');
          var raw = rawL || rawS;
          if (!raw) return;
          var u = JSON.parse(raw);
          var fid = Number(u.farmer_id);
          if (u.farmer_id != null && (!Number.isFinite(fid) || fid <= 0)) {
            delete u.farmer_id;
            u.needs_registration = true;
            var p = JSON.stringify(u);
            sessionStorage.setItem('beanthentic_user', p);
            try {
              if (rawL) localStorage.setItem('beanthentic_user', p);
            } catch (_e) {}
          }
        } catch (_e2) {}
      }

      /**
       * When XAMPP says not registered, clear stale farmer_id in local storage (old acc often
       * has cached IDs; showGate() was returning early because hasFarmerRegistration() stayed true).
       */
      function applyServerRegistrationToClient(dbOut) {
        if (!dbOut || typeof dbOut !== 'object') return;
        var key = getSignedInLoginKey();
        var rawL = null;
        var rawS = null;
        try {
          rawL = localStorage.getItem('beanthentic_user');
          rawS = sessionStorage.getItem('beanthentic_user');
        } catch (_e) {}
        var raw = rawL || rawS;
        if (!raw) return;

        function writeUser(u) {
          var p = JSON.stringify(u);
          try {
            sessionStorage.setItem('beanthentic_user', p);
          } catch (_w1) {}
          try {
            if (rawL) localStorage.setItem('beanthentic_user', p);
          } catch (_w2) {}
        }

        function clearMapForLoginKeys() {
          if (!key) return;
          try {
            var rawMap = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
            var map = rawMap ? JSON.parse(rawMap) : {};
            if (!map || typeof map !== 'object') return;
            var keys = loginKeyVariants(key);
            var changed = false;
            for (var i = 0; i < keys.length; i += 1) {
              if (Object.prototype.hasOwnProperty.call(map, keys[i])) {
                delete map[keys[i]];
                changed = true;
              }
            }
            if (changed) {
              var s = JSON.stringify(map);
              localStorage.setItem('beanthentic_farmer_id_map', s);
              sessionStorage.setItem('beanthentic_farmer_id_map', s);
            }
          } catch (_m) {}
        }

        var u = JSON.parse(raw);
        if (dbOut.registered === true && dbOut.farmer_id != null) {
          var nf = Number(dbOut.farmer_id);
          if (Number.isFinite(nf) && nf > 0) {
            u.farmer_id = nf;
            u.farmer_status = dbOut.farmer_status || 'active';
            u.registration_complete = true;
            u.needs_registration = false;
            writeUser(u);
            clearMapForLoginKeys();
            try {
              var map2 = JSON.parse(localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map') || '{}');
              if (!map2 || typeof map2 !== 'object') map2 = {};
              var k2 = loginKeyVariants(key);
              for (var j = 0; j < k2.length; j += 1) {
                map2[k2[j]] = String(nf);
              }
              var s2 = JSON.stringify(map2);
              localStorage.setItem('beanthentic_farmer_id_map', s2);
              sessionStorage.setItem('beanthentic_farmer_id_map', s2);
            } catch (_up) {}
          }
          return;
        }
        if (dbOut.registered === false) {
          var stubId = dbOut.farmer_id != null ? Number(dbOut.farmer_id) : Number(u.farmer_id);
          if (Number.isFinite(stubId) && stubId > 0) {
            u.farmer_id = stubId;
          }
          u.farmer_status = dbOut.farmer_status || 'pending';
          u.registration_complete = false;
          u.needs_registration = true;
          writeUser(u);
          try {
            var map3 = JSON.parse(localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map') || '{}');
            if (!map3 || typeof map3 !== 'object') map3 = {};
            var k3 = loginKeyVariants(key);
            for (var m = 0; m < k3.length; m += 1) {
              if (u.farmer_id != null) map3[k3[m]] = String(u.farmer_id);
            }
            var s3 = JSON.stringify(map3);
            localStorage.setItem('beanthentic_farmer_id_map', s3);
            sessionStorage.setItem('beanthentic_farmer_id_map', s3);
            if (u.farmer_id != null) {
              localStorage.setItem('beanthentic_farmer_id', String(u.farmer_id));
              sessionStorage.setItem('beanthentic_farmer_id', String(u.farmer_id));
            }
          } catch (_mapPending) {}
        }
      }

      function registerFarmHref() {
        // Always use same-origin route so this works on 192... Flask host.
        return '/register-farm';
      }

      // Lazy lookup: some WebViews may execute scripts before full DOM is available.
      var gate = document.getElementById('home-register-gate');
      var proceedBtn = document.getElementById('home-register-gate-proceed');

      function ensureGateEls() {
        if (!gate) gate = document.getElementById('home-register-gate');
        if (!proceedBtn) proceedBtn = document.getElementById('home-register-gate-proceed');
      }

      function showGate() {
        ensureGateEls();
        if (!gate || hasFarmerRegistration()) return;
        // #page-loader is z-index 99999; gate must show on top and not stay hidden under white overlay.
        var loaderEl = document.getElementById('page-loader');
        if (loaderEl) loaderEl.style.display = 'none';
        gate.hidden = false;
        gate.setAttribute('aria-hidden', 'false');
        try {
          gate.style.zIndex = '100010';
        } catch (_z) {}
        document.body.classList.add('home-register-gate-open');
      }

      function hideGate() {
        ensureGateEls();
        if (!gate) return;
        gate.hidden = true;
        gate.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('home-register-gate-open');
      }

      function shouldShowGateForNewUserFlow() {
        try {
          // Primary: URL marker (works reliably on 192... without storage timing issues).
          if (typeof location !== 'undefined') {
            var sp = new URLSearchParams(String(location.search || '').replace(/^\\?/, ''));
            if (sp.get('new_user') === '1') return true;
          }
          // Back-compat: storage flag (older code paths).
          var flag =
            sessionStorage.getItem('beanthentic_prompt_register_after_tutorial') ||
            localStorage.getItem('beanthentic_prompt_register_after_tutorial');
          return flag === '1';
        } catch (_e) {
          return false;
        }
      }

      function shouldShowGate() {
        // Always gate signed-in users until they have a farmer registration.
        // This matches: "if user is on homepage and not yet registered, popup should show".
        try {
          var loginKey = getSignedInLoginKey();
          if (!loginKey) return false;
        } catch (_e) {
          return false;
        }
        if (hasFarmerRegistration()) return false;
        // Keep honoring new-user markers, but don't require them.
        return true;
      }

      function currentUserIdentity() {
        try {
          var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          var u = raw ? JSON.parse(raw) : null;
          if (!u) return null;
          var loginKey = '';
          if (u.email != null && String(u.email).trim()) loginKey = String(u.email).trim().toLowerCase();
          else if (u.phone_number != null && String(u.phone_number).trim()) loginKey = String(u.phone_number).trim().toLowerCase();
          else if (u.login != null && String(u.login).trim()) loginKey = String(u.login).trim().toLowerCase();
          if (!loginKey) return null;
          var uid = Number(u.user_id);
          return {
            email: loginKey,
            user_id: Number.isFinite(uid) && uid > 0 ? uid : 0
          };
        } catch (_e) {
          return null;
        }
      }

      function registrationStatusUrlCandidates() {
        if (window.BeanthenticApiUrls && window.BeanthenticApiUrls.phpApiUrlCandidates) {
          return window.BeanthenticApiUrls.phpApiUrlCandidates('registration_status.php');
        }
        try {
          return [new URL('api/registration_status.php', location.href).href];
        } catch (_e) {
          return [];
        }
      }

      function checkRegistrationFromDb() {
        var ident = currentUserIdentity();
        if (!ident || !ident.email) {
          return Promise.resolve(null);
        }
        var payload = { email: ident.email };
        if (ident.user_id > 0) payload.user_id = ident.user_id;
        if (window.BeanthenticApiUrls && window.BeanthenticApiUrls.fetchApiSequential) {
          return window.BeanthenticApiUrls
            .fetchApiSequential('registration_status.php', payload, { timeoutMs: 3500, maxTries: 2 })
            .then(function (body) {
              return body && body.ok === true ? body : null;
            });
        }
        var urls = registrationStatusUrlCandidates();
        var i = 0;
        function tryNext() {
          if (i >= urls.length) return Promise.resolve(null);
          var url = urls[i++];
          return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          })
            .then(function (res) {
              return res.text().then(function (txt) {
                var body = null;
                try { body = txt ? JSON.parse(txt) : null; } catch (_p) { body = null; }
                if (res.ok && body && body.ok === true) return body;
                return tryNext();
              });
            })
            .catch(function () {
              return tryNext();
            });
        }
        return tryNext();
      }

      function clearGateFlag() {
        try {
          sessionStorage.removeItem('beanthentic_prompt_register_after_tutorial');
          localStorage.removeItem('beanthentic_prompt_register_after_tutorial');
        } catch (_e) {}
        // Remove URL marker so it doesn't keep re-opening the gate.
        try {
          if (typeof location !== 'undefined' && typeof history !== 'undefined' && history.replaceState) {
            var url = new URL(location.href);
            url.searchParams.delete('new_user');
            history.replaceState({}, '', url.pathname + (url.search ? url.search : '') + (url.hash ? url.hash : ''));
          }
        } catch (_u) {}
      }

      function gateDebugEnabled() {
        try {
          return typeof location !== 'undefined' && String(location.search || '').indexOf('gate_debug=1') !== -1;
        } catch (_e) {
          return false;
        }
      }

      function emitGateDebug(stage) {
        if (!gateDebugEnabled()) return;
        var info = {};
        try {
          info.stage = stage || '';
          info.pathname = (location && location.pathname) ? String(location.pathname) : '';
          info.hasGateEl = !!gate;
          info.hasProceedEl = !!proceedBtn;
          info.user = getSignedInLoginKey();
          try {
            var rawDbg = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
            var uDbg = rawDbg ? JSON.parse(rawDbg) : null;
            info.farmer_status = uDbg ? uDbg.farmer_status : '';
            info.registration_complete = uDbg ? uDbg.registration_complete : '';
          } catch (_db) {}
          info.hasFarmerRegistration = hasFarmerRegistration();
          info.flag_session = sessionStorage.getItem('beanthentic_prompt_register_after_tutorial');
          info.flag_local = localStorage.getItem('beanthentic_prompt_register_after_tutorial');
          info.new_signup_session = sessionStorage.getItem('beanthentic_new_signup_login_id');
          info.new_signup_local = localStorage.getItem('beanthentic_new_signup_login_id');
          info.farmer_id = localStorage.getItem('beanthentic_farmer_id');
          info.farmer_id_map = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
        } catch (_e2) {
          info.error = 'debug_failed';
        }
        try { console.log('[GateDebug]', info); } catch (_c) {}
        try {
          var box = document.getElementById('gate-debug-box');
          if (!box) {
            box = document.createElement('pre');
            box.id = 'gate-debug-box';
            box.style.position = 'fixed';
            box.style.left = '10px';
            box.style.bottom = '90px';
            box.style.zIndex = '99999';
            box.style.maxWidth = 'min(92vw, 520px)';
            box.style.maxHeight = '38vh';
            box.style.overflow = 'auto';
            box.style.margin = '0';
            box.style.padding = '10px 12px';
            box.style.background = 'rgba(17,24,39,0.92)';
            box.style.color = '#fff';
            box.style.borderRadius = '12px';
            box.style.fontSize = '12px';
            box.style.lineHeight = '1.35';
            box.style.whiteSpace = 'pre-wrap';
            document.body.appendChild(box);
          }
          box.textContent = JSON.stringify(info, null, 2);
        } catch (_d) {}
      }

      function tryShowRegisterGate(reason) {
        emitGateDebug(reason || 'tryShowRegisterGate');
        normalizeUserFarmerIdInStorage();
        sanitizeFarmerIdMapForCurrentUser();
        if (!shouldShowGate()) return;
        checkRegistrationFromDb().then(function (dbOut) {
          if (dbOut && dbOut.registered === true) {
            applyServerRegistrationToClient(dbOut);
            hideGate();
            return;
          }
          if (dbOut && dbOut.registered === false) {
            applyServerRegistrationToClient(dbOut);
          }
          if (!dbOut && shouldShowGate()) {
            try {
              var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
              var u = raw ? JSON.parse(raw) : null;
              if (u && u.needs_registration === true) {
                applyServerRegistrationToClient({ registered: false });
              }
            } catch (_f) {}
          }
          if (!shouldShowGate()) return;
          showGate();
          clearGateFlag();
        });
      }

      document.addEventListener('DOMContentLoaded', function () {
        emitGateDebug('dom_ready');
        sanitizeFarmerIdMapForCurrentUser();
        // 1s after DOM ready
        window.setTimeout(function () {
          tryShowRegisterGate('dom_ready+1000ms');
        }, 1000);
      });

      window.addEventListener('beanthentic-home-loader-hidden', function () {
        // Loader was covering the modal (z-index). Retry 1s after loader hides as well.
        window.setTimeout(function () {
          tryShowRegisterGate('loader_hidden+1000ms');
        }, 1000);
      });

      window.addEventListener('storage', function (e) {
        if (e && e.key === 'beanthentic_farmer_id' && hasFarmerRegistration()) {
          hideGate();
        }
      });

      document.addEventListener('DOMContentLoaded', function () {
        ensureGateEls();
        if (!proceedBtn || proceedBtn.dataset.bound === '1') return;
        proceedBtn.dataset.bound = '1';
        proceedBtn.addEventListener('click', function () {
          if (proceedBtn.disabled) return;
          proceedBtn.disabled = true;
          var loader = document.getElementById('page-loader');
          hideGate();
          if (loader) {
            loader.style.display = 'flex';
          }
          // Navigate immediately; no need to wait.
          try {
            window.location.assign(registerFarmHref());
          } catch (_err) {
            window.location.href = registerFarmHref();
          }
        });
      });
    })();
  </script>

  <script>
    // Bottom nav active state is handled globally in js/ui.js (syncAppBottomNavActive).
  </script>

  <div id="farmer-warning-modal" class="farmer-account-modal farmer-account-modal--warning" hidden role="alertdialog" aria-labelledby="farmer-warning-title" aria-modal="true">
    <div class="farmer-account-modal__backdrop" data-farmer-modal-close></div>
    <div class="farmer-account-modal__card">
      <div class="farmer-account-modal__accent" aria-hidden="true"></div>
      <div class="farmer-account-modal__icon farmer-account-modal__icon--warning" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <p class="farmer-account-modal__eyebrow">Official notice</p>
      <h2 id="farmer-warning-title" class="farmer-account-modal__title">Administrative warning</h2>
      <p class="farmer-account-modal__lead">Your account received a warning from the Beanthentic administrator. Please review the details below.</p>
      <div class="farmer-account-modal__reason-box">
        <span class="farmer-account-modal__reason-label">Reason for warning</span>
        <p id="farmer-warning-category" class="farmer-account-modal__reason-category" hidden></p>
        <p id="farmer-warning-text" class="farmer-account-modal__reason-text"></p>
      </div>
      <p id="farmer-warning-meta" class="farmer-account-modal__footnote"></p>
      <button type="button" id="farmer-warning-ok" class="farmer-account-modal__btn">I understand</button>
    </div>
  </div>

  <div id="farmer-suspend-modal" class="farmer-account-modal farmer-account-modal--suspend" hidden role="alertdialog" aria-labelledby="farmer-suspend-title" aria-modal="true">
    <div class="farmer-account-modal__backdrop" data-farmer-modal-close></div>
    <div class="farmer-account-modal__card">
      <div class="farmer-account-modal__accent farmer-account-modal__accent--danger" aria-hidden="true"></div>
      <div class="farmer-account-modal__icon farmer-account-modal__icon--suspend" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
      </div>
      <p class="farmer-account-modal__eyebrow">Account restriction</p>
      <h2 id="farmer-suspend-title" class="farmer-account-modal__title">Account suspended</h2>
      <p class="farmer-account-modal__lead">Access to some features is temporarily restricted until the suspension period ends.</p>
      <div class="farmer-account-modal__reason-box farmer-account-modal__reason-box--danger">
        <span class="farmer-account-modal__reason-label">Reason for suspension</span>
        <p id="farmer-suspend-category" class="farmer-account-modal__reason-category" hidden></p>
        <p id="farmer-suspend-text" class="farmer-account-modal__reason-text"></p>
      </div>
      <p id="farmer-suspend-until" class="farmer-account-modal__meta"></p>
      <button type="button" id="farmer-suspend-ok" class="farmer-account-modal__btn farmer-account-modal__btn--danger">Return to sign in</button>
    </div>
  </div>

  <style>
    .farmer-account-modal {
      position: fixed;
      inset: 0;
      z-index: 100001;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: max(16px, env(safe-area-inset-left)) max(16px, env(safe-area-inset-right)) max(20px, env(safe-area-inset-bottom));
      font-family: inherit;
    }
    .farmer-account-modal[hidden] { display: none !important; }
    .farmer-account-modal__backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 35, 18, 0.52);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
    }
    .farmer-account-modal__card {
      position: relative;
      z-index: 1;
      max-width: 380px;
      width: 100%;
      background: #fff;
      border-radius: 20px;
      padding: 0 0 20px;
      text-align: center;
      box-shadow: 0 20px 50px rgba(15, 45, 20, 0.22), 0 4px 12px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      border: 1px solid rgba(37, 103, 30, 0.08);
    }
    .farmer-account-modal__accent {
      height: 5px;
      background: linear-gradient(90deg, #e65100 0%, #ff9800 55%, #ffb74d 100%);
    }
    .farmer-account-modal__accent--danger {
      background: linear-gradient(90deg, #b71c1c 0%, #e53935 55%, #ef5350 100%);
    }
    .farmer-account-modal__icon {
      width: 56px;
      height: 56px;
      margin: 18px auto 10px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }
    .farmer-account-modal__icon--warning {
      background: linear-gradient(145deg, #fff8e1 0%, #ffe0b2 100%);
      color: #e65100;
    }
    .farmer-account-modal__icon--suspend {
      background: linear-gradient(145deg, #ffebee 0%, #ffcdd2 100%);
      color: #c62828;
    }
    .farmer-account-modal__eyebrow {
      margin: 0 0 4px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #6b7c6b;
    }
    .farmer-account-modal__title {
      margin: 0 0 8px;
      padding: 0 22px;
      font-size: 20px;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: #1a2e1a;
      line-height: 1.25;
    }
    .farmer-account-modal__lead {
      margin: 0 0 16px;
      padding: 0 22px;
      font-size: 13px;
      line-height: 1.5;
      color: #5c6b5c;
    }
    .farmer-account-modal__reason-box {
      margin: 0 18px 12px;
      padding: 14px 16px;
      text-align: left;
      background: #f6f9f5;
      border: 1px solid #e2ebe0;
      border-radius: 12px;
      border-left: 4px solid #e65100;
    }
    .farmer-account-modal__reason-box--danger {
      background: #fff8f8;
      border-color: #f5d5d5;
      border-left-color: #c62828;
    }
    .farmer-account-modal__reason-label {
      display: block;
      margin: 0 0 8px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #6b7c6b;
    }
    .farmer-account-modal__reason-category {
      margin: 0 0 6px;
      font-size: 15px;
      font-weight: 700;
      color: #1a2e1a;
      line-height: 1.35;
    }
    .farmer-account-modal__reason-text {
      margin: 0;
      font-size: 14px;
      line-height: 1.55;
      color: #374837;
      word-break: break-word;
    }
    .farmer-account-modal__footnote {
      margin: 0 0 16px;
      padding: 0 22px;
      font-size: 12px;
      color: #7a8a7a;
      line-height: 1.4;
    }
    .farmer-account-modal__meta {
      margin: 0 0 16px;
      padding: 10px 14px;
      margin-left: 18px;
      margin-right: 18px;
      font-size: 13px;
      font-weight: 600;
      color: #b71c1c;
      line-height: 1.45;
      background: #ffebee;
      border-radius: 10px;
      border: 1px solid #ffcdd2;
    }
    .farmer-account-modal__btn {
      width: calc(100% - 36px);
      margin: 0 18px;
      border: none;
      border-radius: 999px;
      padding: 13px 18px;
      background: linear-gradient(180deg, #2f8f22 0%, #25671e 100%);
      color: #fff;
      font-weight: 700;
      font-size: 15px;
      letter-spacing: 0.02em;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(37, 103, 30, 0.35);
    }
    .farmer-account-modal__btn:active {
      transform: scale(0.98);
      box-shadow: 0 2px 8px rgba(37, 103, 30, 0.3);
    }
    .farmer-account-modal__btn--danger {
      background: linear-gradient(180deg, #e53935 0%, #c62828 100%);
      box-shadow: 0 4px 14px rgba(198, 40, 40, 0.35);
    }
    html.beanthentic-account-suspended,
    html.beanthentic-account-suspended body {
      overflow: hidden;
    }
    .farmer-account-modal--blocking {
      pointer-events: auto;
    }
    .farmer-account-modal--blocking .farmer-account-modal__backdrop {
      cursor: default;
    }
    .farmer-account-modal--blocking .farmer-account-modal__card {
      box-shadow: 0 24px 60px rgba(120, 0, 0, 0.28);
    }
  </style>
  <script src="js/beanthentic_client_web.js"></script>
  <script src="js/beanthentic_farmer_account_alerts.js?v=20260527-4"></script>
</body>
</html>


