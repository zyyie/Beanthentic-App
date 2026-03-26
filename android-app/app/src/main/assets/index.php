<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
  </style>
</head>
<body>
  <!-- Loading overlay (shown on open and on navigation clicks) -->
  <div id="page-loader" style="position:fixed; inset:0; background:#ffffff; display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:99999;">
    <img
      src="coffee_bean_loading.png"
      alt="Coffee bean loading"
      style="width:96px; height:96px; object-fit:contain;"
    />
    <div style="margin-top:12px; color:#777777; font-family:inherit; font-size:14px;">Please wait for a moment.</div>
  </div>

  <header>
    <div class="nav">
      <a href="#" class="logo" aria-label="Beanthentic home"><span>BEANTHENTIC</span></a>
      <nav class="nav-links">
        <a href="#home" id="home-nav-toggle" data-no-scroll="true">Home</a>
        <a
          href="#about"
          id="about-nav-toggle"
          class="about-nav-toggle"
          aria-haspopup="true"
          aria-expanded="false"
          data-no-scroll="true"
        >About Beanthentic</a>
        <div
          id="about-nav-dropdown"
          class="about-nav-dropdown"
          hidden
          aria-hidden="true"
        >
          <a
            href="#about-history-overall"
            id="about-nav-history-toggle"
            class="about-menu-item about-nav-item"
            data-about-history-target="about-history-overall"
            data-toggle-about-history-nav="true"
            aria-haspopup="true"
            aria-expanded="false"
            data-no-scroll="true"
            data-no-loader="true"
          >History</a>

          <div
            id="about-history-nav-submenu"
            class="about-history-nav-submenu"
            hidden
            aria-hidden="true"
          >
            <a
              href="#about-history-liberica"
              class="about-menu-item about-nav-item"
              data-about-history-target="about-history-liberica"
              data-no-scroll="true"
              data-no-loader="true"
            >Liberica</a>

            <a
              href="#about-history-robusta"
              class="about-menu-item about-nav-item"
              data-about-history-target="about-history-robusta"
              data-no-scroll="true"
              data-no-loader="true"
            >Robusta</a>

            <a
              href="#about-history-excelsa"
              class="about-menu-item about-nav-item"
              data-about-history-target="about-history-excelsa"
              data-no-scroll="true"
              data-no-loader="true"
            >Excelsa</a>
          </div>

          <a
            href="#about-mission-vision"
            class="about-menu-item about-nav-item"
            data-about-target="about-mission-vision"
            data-no-scroll="true"
            data-no-loader="true"
          >Mission and Vision</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="home-layout">
    <div class="home-main-content">
    <section id="home" class="hero">
      <div>
        <h1 class="hero-title">Welcome to <span>Beanthentic</span> Coffee</h1>
        <p class="hero-subtitle">
          Beanthentic Coffee is a platform that highlights the hard work of local farmers and the
          authenticity of their coffee beans. It showcases different coffee varieties such as Liberica, Robusta, and Excelsa, allowing consumers to
          learn about the origin, quality, and unique characteristics of each bean.
        </p>
        <div class="hero-cta">
          <p class="hero-note"><strong>Explore the origins of authentic coffee beans and the farmers behind every harvest</strong>.</p>
        </div>
        <div class="hero-slider" aria-label="Featured images">
          <div class="hero-slider-viewport">
            <div class="hero-slider-track" role="list">
              <div class="hero-slide" role="listitem" data-theme="one">
                <img class="hero-slide-img" src="https://images.unsplash.com/photo-1517881426553-5d7d3abfbabf?auto=format&fit=crop&w=800&q=80" alt="Coffee beans" />
                <div class="hero-slide-caption"><strong>Liberica Beans</strong><span>Rich aroma, bold character</span></div>
              </div>
              <div class="hero-slide" role="listitem" data-theme="two">
                <img class="hero-slide-img" src="https://images.unsplash.com/photo-1754648293032-090b43f4e45b?auto=format&fit=crop&w=800&q=80" alt="Coffee cherries" />
                <div class="hero-slide-caption"><strong>Harvest Season</strong><span>From cherries to beans</span></div>
              </div>
              <div class="hero-slide" role="listitem" data-theme="three">
                <img class="hero-slide-img" src="https://images.unsplash.com/photo-1746623691136-afb227ca4229?auto=format&fit=crop&w=800&q=80" alt="Farmers" />
                <div class="hero-slide-caption"><strong>Farmers</strong><span>Supporting sustainable coffee</span></div>
              </div>
            </div>
          </div>
          <div class="hero-slider-controls">
            <div class="hero-slider-dots" role="tablist">
              <button class="hero-slider-dot" type="button" role="tab" aria-selected="true" data-slide="0"></button>
              <button class="hero-slider-dot" type="button" role="tab" aria-selected="false" data-slide="1"></button>
              <button class="hero-slider-dot" type="button" role="tab" aria-selected="false" data-slide="2"></button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about" hidden>
      <div class="about-grid">
        <article class="about-card">
          <h3 class="about-main-title">Mission and Vision</h3>

          <div class="about-content" aria-label="About Beanthentic content">
            <div id="about-overview" class="about-topic is-active" data-about-panel="about">
              <p>
                Beanthentic is an innovative platform designed to support coffee farmers and promote authentic, high-quality coffee.
                It helps verify the origin and authenticity of coffee products while ensuring transparency and traceability within the coffee industry.
              </p>
              <p>
                Beanthentic integrates modern technology with agricultural practices to evaluate Geographic Indication (GI) eligibility and provide reliable data about coffee farms, production, and quality.
              </p>
            </div>
            <div id="about-history" class="about-topic" data-about-panel="about-history">
              <div class="about-split about-history-split" aria-label="History submenu and content">
                <aside class="about-menu about-history-menu" aria-label="History menu">
                  <div class="about-menu-title">History</div>
                  <a
                    href="#about-history-liberica"
                    class="about-menu-item about-history-menu-item is-active"
                    data-about-history-target="about-history-liberica"
                    data-no-scroll="true"
                    data-no-loader="true"
                  >Liberica</a>
                  <a
                    href="#about-history-robusta"
                    class="about-menu-item about-history-menu-item"
                    data-about-history-target="about-history-robusta"
                    data-no-scroll="true"
                    data-no-loader="true"
                  >Robusta</a>
                  <a
                    href="#about-history-excelsa"
                    class="about-menu-item about-history-menu-item"
                    data-about-history-target="about-history-excelsa"
                    data-no-scroll="true"
                    data-no-loader="true"
                  >Excelsa</a>
                </aside>

                <div class="about-content" aria-label="History content">
                  <div id="about-history-liberica" class="about-topic is-active" data-about-history-panel="about-history-liberica">
                    <h4>Liberica</h4>
                    <p>
                      Liberica became known for its bold character and distinct aroma. Beanthetic tracks its cultivation practices to
                      help consumers understand its origin and quality traits.
                    </p>
                  </div>

                  <div id="about-history-robusta" class="about-topic" data-about-history-panel="about-history-robusta">
                    <h4>Robusta</h4>
                    <p>
                      Robusta is widely grown for its strength and resilience. Through Beanthetic, farmers can record production details
                      and build traceability across each harvest.
                    </p>
                  </div>

                  <div id="about-history-excelsa" class="about-topic" data-about-history-panel="about-history-excelsa">
                    <h4>Excelsa</h4>
                    <p>
                      Excelsa stands out for its unique flavor profile and growing requirements. Beanthetic preserves these details to
                      support authenticity and informed choices.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div id="about-mission-vision" class="about-topic" data-about-panel="about-mission-vision">
              <div class="mv-banner-wrap" aria-label="Mission and Vision banner">
                <img
                  class="mv-banner-img"
                  src="mission_vision_banner.png"
                  alt="Mission and Vision"
                />
              </div>
            </div>
          </div>

        </article>
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
      var loader = document.getElementById('page-loader');
      if (!loader) return;

      var startedAt = Date.now();
      var minVisibleMs = 1200;
      var hideTimer = null;

      function hideLoader() {
        if (!loader) return;
        var elapsed = Date.now() - startedAt;
        var delay = Math.max(0, minVisibleMs - elapsed);
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = setTimeout(function () {
          loader.style.display = 'none';
        }, delay);
      }

      // Hide once the page is fully loaded (images, CSS, etc.)
      window.addEventListener('load', hideLoader);

      // Safety fallback (in case load event doesn't fire)
      if (hideTimer) clearTimeout(hideTimer);
      hideTimer = setTimeout(hideLoader, minVisibleMs + 2500);

      // Show loader on navigation clicks (links). Keep anchor clicks short.
      document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;

        var href = a.getAttribute('href') || '';
        if (!href) return;
        // Don't show loading overlay for hash navigation (in-page) or sidebar clicks.
        if (href.indexOf('#') === 0) return;
        if (a.closest && a.closest('.home-sidebar')) return;
        if (a.getAttribute('data-no-loader') === 'true') return;

        loader.style.display = 'flex';
        startedAt = Date.now();
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = null;
      });
    })();
  </script>
</body>
</html>

