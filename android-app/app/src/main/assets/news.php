<?php
  $title = 'News · Beanthentic Coffee';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#25671E" />
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
</head>
<body>
  <header class="header">
    <div class="header-inner">
      <button type="button" class="header-burger" aria-label="Open menu" aria-controls="header-nav-drawer" aria-expanded="false">
        <span class="header-burger-lines" aria-hidden="true"></span>
      </button>
      <a href="index.php#home" class="header-brand" aria-label="Beanthentic home">
        <span class="logo-mark" aria-hidden="true"></span>
        <span class="header-brand-text">Beanthentic</span>
      </a>
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
          <a href="https://www.facebook.com/" class="header-drawer-link header-drawer-link--social" target="_blank" rel="noopener noreferrer">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Social</span>
          </a>
          <a href="privacy.php" class="header-drawer-link">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
            <span>Privacy Policy</span>
          </a>
          <a href="news.php" class="header-drawer-link" aria-current="page">
            <svg class="header-drawer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M4 19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12z"/>
              <path d="M8 9h8"/>
              <path d="M8 13h8"/>
              <path d="M8 17h5"/>
            </svg>
            <span>News</span>
          </a>
          <button type="button" id="header-sign-out-btn" class="header-drawer-signout" hidden>Sign out</button>
        </div>
      </aside>
    </div>
  </header>

  <main class="auth-main news-main">
    <article class="auth-card news-card">
      <h1>News</h1>
      <p class="auth-lead">Official updates and documents supporting Kapeng Barako and local growers.</p>
      <figure class="news-cert-figure">
        <img
          class="news-cert-img"
          src="news/kapeng_barako_certificate.png"
          width="600"
          height="848"
          alt="IPOPHL Certificate of Registration for Batangas Kapeng Barako trademark"
          loading="lazy"
        />
        <figcaption class="news-cert-caption">
          Certificate of Registration — Intellectual Property Office of the Philippines (IPOPHL) for <strong>Batangas Kapeng Barako</strong>.
        </figcaption>
      </figure>
      <p style="margin-top:1.25rem;">
        <a href="index.php#home" class="btn-primary" style="display:inline-block;text-decoration:none;">Back to home</a>
      </p>
    </article>
  </main>

  <script src="js/ui.js"></script>
</body>
</html>

