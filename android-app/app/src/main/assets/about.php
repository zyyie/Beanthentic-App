<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>About · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body {
      background: #fff;
      margin: 0;
    }
    .about-hero {
      background: linear-gradient(160deg, #1c6f20 0%, #0f4a15 100%);
      border-radius: 0 0 16px 16px;
      padding: 1.75rem 1rem 1.6rem;
      color: #fff;
    }
    .about-hero-row {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 42px;
    }
    .about-nav-back {
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px;
      text-decoration: none;
      -webkit-tap-highlight-color: transparent;
    }
    .about-nav-back svg { width: 18px; height: 18px; }
    .about-nav-title {
      margin: 0;
      font-size: clamp(1.35rem, 4.4vw, 1.7rem);
      font-weight: 800;
      line-height: 1;
      letter-spacing: 0.02em;
    }
    .about-main {
      max-width: min(100%, 680px);
      margin: 0 auto;
      padding: 1.25rem max(0.5rem, env(safe-area-inset-left, 0px)) 1.4rem max(0.5rem, env(safe-area-inset-right, 0px));
    }
    .about-back {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      color: #2f7a24;
      text-decoration: none;
      font-size: 0.9rem;
      margin: 0.1rem 0 0.75rem;
    }
    .about-back svg { width: 16px; height: 16px; }
    .about-title {
      font-size: 2.35rem;
      font-weight: 800;
      text-align: center;
      line-height: 1.1;
      margin: 0 0 1.05rem;
      color: #111827;
      letter-spacing: -0.02em;
      white-space: nowrap;
    }
    .about-title-accent { color: #1f7a2e; }
    .about-desc {
      max-width: min(100%, 40rem);
      margin: 0 auto 0.9rem;
      text-align: center;
      font-size: 0.96rem;
      color: #111827;
      line-height: 1.32;
    }
    .about-dots + .about-desc {
      margin-top: 0.15rem;
      margin-bottom: 0.35rem;
    }
    .about-image-slot {
      width: 100%;
      aspect-ratio: 16 / 10;
      min-height: 220px;
      border-radius: 14px;
      background: #ececec;
      margin: 0.7rem 0 0.75rem;
      overflow: hidden;
      position: relative;
    }
    .about-slide {
      width: 100%;
      height: 100%;
      object-fit: cover;
      background: #ececec;
      display: none;
    }
    .about-slide.is-active {
      display: block;
    }
    .about-dots {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.45rem;
      margin-bottom: 0.95rem;
    }
    .about-dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: #2f7a24;
      opacity: 0.72;
      transition: transform 0.18s ease, opacity 0.18s ease;
    }
    .about-dot.active {
      opacity: 1;
      transform: scale(1.25);
    }
    .about-link-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
      margin-top: 1.65rem;
    }
    .about-link-card {
      text-decoration: none;
      color: #111827;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.32rem;
      padding: 0.55rem 0.28rem 0.5rem;
      border-radius: 16px;
      outline: none;
      border: 2px solid transparent;
      background: transparent;
      -webkit-tap-highlight-color: transparent;
      transition:
        transform 0.18s ease,
        box-shadow 0.18s ease,
        background 0.18s ease,
        border-color 0.18s ease;
    }
    .about-link-icon {
      width: 3rem;
      height: 3rem;
      border-radius: 999px;
      background: #ffffff;
      border: 1px solid rgba(31, 122, 46, 0.24);
      color: #1f7a2e;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 2px 8px rgba(18, 40, 26, 0.08);
      transition:
        transform 0.18s ease,
        color 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease,
        background 0.18s ease;
    }
    .about-link-icon svg {
      width: 1.65rem;
      height: 1.65rem;
    }
    .about-link-text {
      font-size: 0.95rem;
      font-weight: 700;
      font-style: normal;
      text-align: center;
      line-height: 1.1;
      transition: color 0.18s ease, text-shadow 0.18s ease;
    }

    /* Whole card (icon + label): soft highlight */
    .about-link-card:hover,
    .about-link-card:focus-visible {
      transform: translateY(-2px);
      background: rgba(31, 122, 46, 0.08);
      border-color: rgba(31, 122, 46, 0.32);
      box-shadow:
        0 12px 22px rgba(17, 24, 39, 0.08),
        0 0 0 3px rgba(31, 122, 46, 0.18);
    }
    .about-link-card:hover .about-link-icon,
    .about-link-card:focus-visible .about-link-icon {
      color: #1c6f20;
      transform: scale(1.04);
      border-color: rgba(28, 111, 32, 0.45);
      box-shadow: 0 4px 12px rgba(27, 94, 32, 0.18);
      background: #f4faf4;
    }
    .about-link-card:hover .about-link-text,
    .about-link-card:focus-visible .about-link-text {
      color: #1c6f20;
      text-shadow: 0 0 14px rgba(31, 122, 46, 0.28);
    }

    /* Click / press: stronger glow — damay ang buong box kasama ang text */
    .about-link-card:active {
      transform: translateY(0) scale(0.985);
      background: rgba(31, 122, 46, 0.14);
      border-color: rgba(31, 122, 46, 0.55);
      box-shadow:
        0 8px 20px rgba(17, 24, 39, 0.10),
        0 0 0 4px rgba(31, 122, 46, 0.42),
        0 0 26px rgba(31, 122, 46, 0.32);
    }
    .about-link-card:active .about-link-icon {
      color: #155724;
      transform: scale(1.02);
      border-color: rgba(21, 87, 36, 0.55);
      background: #eaf6eb;
    }
    .about-link-card:active .about-link-text {
      color: #155724;
      text-shadow: 0 0 18px rgba(31, 122, 46, 0.45);
    }

    @media (min-width: 700px) {
      .about-main {
        max-width: min(100%, 820px);
        padding: 1.4rem 1rem 1.55rem;
      }
      .about-image-slot {
        min-height: 260px;
        aspect-ratio: 16 / 9;
      }
      .about-desc {
        font-size: 1.02rem;
        line-height: 1.34;
      }
    }
  </style>
</head>
<body class="has-app-bottom-nav">
  <header class="about-hero">
    <div class="about-hero-row">
      <a class="about-nav-back" href="account.php" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="about-nav-title">About</h1>
    </div>
  </header>

  <main class="about-main">
    <h1 class="about-title">About <span class="about-title-accent">Beanthentic</span></h1>
    <p class="about-desc">
      Beanthentic Coffee is a platform that highlights the hard work of local farmers and the authenticity of their coffee beans. It showcases different coffee varieties such as Liberica, Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique characteristics of each bean.
    </p>

    <div class="about-image-slot" aria-label="About slideshow">
      <img class="about-slide is-active" src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=1200&q=70" alt="Coffee beans photo 1" loading="lazy" referrerpolicy="no-referrer">
      <img class="about-slide" src="https://images.unsplash.com/photo-1459755486867-b55449bb39ff?auto=format&fit=crop&w=1200&q=70" alt="Coffee beans photo 2" loading="lazy" referrerpolicy="no-referrer">
      <img class="about-slide" src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1200&q=70" alt="Coffee beans photo 3" loading="lazy" referrerpolicy="no-referrer">
      <img class="about-slide" src="https://images.unsplash.com/photo-1512568400610-62da28bc8a13?auto=format&fit=crop&w=1200&q=70" alt="Coffee beans photo 4" loading="lazy" referrerpolicy="no-referrer">
    </div>
    <div class="about-dots" aria-hidden="true">
      <span class="about-dot" data-slide-dot="0"></span>
      <span class="about-dot active" data-slide-dot="1"></span>
      <span class="about-dot" data-slide-dot="2"></span>
      <span class="about-dot" data-slide-dot="3"></span>
    </div>

    <p class="about-desc">
      Beanthentic Coffee is a platform that highlights the hard work of local farmers and the authenticity of their coffee beans. It showcases different coffee varieties such as Liberica, Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique characteristics of each bean.
    </p>

    <div class="about-link-grid">
      <a href="history.php" class="about-link-card">
        <span class="about-link-icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- History: timeline -->
            <path d="M18 18v28"/>
            <circle cx="18" cy="20" r="3" fill="currentColor" stroke="none"/>
            <circle cx="18" cy="32" r="3" fill="currentColor" stroke="none"/>
            <circle cx="18" cy="44" r="3" fill="currentColor" stroke="none"/>
            <path d="M24 20h22"/>
            <path d="M24 32h18"/>
            <path d="M24 44h14"/>
          </svg>
        </span>
        <span class="about-link-text">History</span>
      </a>
      <a href="mission-vision.php" class="about-link-card">
        <span class="about-link-icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Eye + crosshair: vision + mission -->
            <path d="M8 32s9.5-14 24-14 24 14 24 14-9.5 14-24 14S8 32 8 32z"/>
            <circle cx="32" cy="32" r="7.5"/>
            <path d="M32 18v6M32 40v6M18 32h6M40 32h6"/>
          </svg>
        </span>
        <span class="about-link-text">Mission &amp; Vision</span>
      </a>
      <a href="how-to-get-there.php" class="about-link-card">
        <span class="about-link-icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Route + map pin -->
            <path d="M18 46c8-7 6-21 16-21s8 14 16 7"/>
            <path d="M46 32l4-4m0 0-4-4m4 4h-8"/>
            <path d="M22 22c0-6 4.7-10 10-10s10 4 10 10c0 7-10 18-10 18S22 29 22 22z"/>
            <circle cx="32" cy="22" r="3.2" fill="currentColor" stroke="none"/>
          </svg>
        </span>
        <span class="about-link-text">How to get there</span>
      </a>
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

  <script>
    (function () {
      var backBtn = document.querySelector('.about-nav-back');
      if (backBtn) {
        backBtn.addEventListener('click', function (event) {
          event.preventDefault();
          try {
            if (window.history && window.history.length > 1) {
              window.history.back();
              return;
            }
          } catch (_e) {}
          var fallback = 'account.php';
          try {
            var params = new URLSearchParams(window.location.search || '');
            if (params.get('from') === 'account_settings') fallback = 'account_settings.html';
          } catch (_err) {}
          window.location.href = fallback;
        });
      }

      var slides = Array.prototype.slice.call(document.querySelectorAll('.about-slide'));
      var dots = Array.prototype.slice.call(document.querySelectorAll('[data-slide-dot]'));
      if (!slides.length || !dots.length) return;
      var idx = 0;

      function showSlide(next) {
        idx = (next + slides.length) % slides.length;
        slides.forEach(function (s, i) { s.classList.toggle('is-active', i === idx); });
        dots.forEach(function (d, i) { d.classList.toggle('active', i === idx); });
      }

      dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { showSlide(i); });
      });

      setInterval(function () { showSlide(idx + 1); }, 2800);
    })();
  </script>
</body>
</html>

