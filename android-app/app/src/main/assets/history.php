<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>History · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body {
      background: #fff;
      margin: 0;
    }
    .hist-hero {
      background: linear-gradient(160deg, #1c6f20 0%, #0f4a15 100%);
      border-radius: 0 0 16px 16px;
      padding: 1.75rem 1rem 1.6rem;
      color: #fff;
    }
    .hist-hero-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      position: relative;
      min-height: 42px;
    }
    .hist-nav-back {
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
    .hist-nav-back svg { width: 18px; height: 18px; }
    .hist-nav-title {
      margin: 0;
      font-size: clamp(1.35rem, 4.4vw, 1.7rem);
      font-weight: 800;
      line-height: 1;
      letter-spacing: 0.02em;
    }

    .hist-main {
      max-width: 920px;
      margin: 0 auto;
      padding: 0.72rem 0.72rem 1.35rem;
    }
    .hist-back {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      color: #2f7a24;
      text-decoration: none;
      font-size: 0.9rem;
      margin: 0.1rem 0 0.85rem;
    }
    .hist-back svg { width: 16px; height: 16px; }

    .hist-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: #4a3728;
      color: #fff;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      padding: 0.38rem 0.95rem 0.38rem 0.65rem;
      border-radius: 999px;
      margin-bottom: 0.55rem;
    }
    .hist-badge-dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: #fff;
      opacity: 0.95;
    }

    .hist-page-title {
      text-align: center;
      margin: 0 0 0.65rem;
      font-size: clamp(1.45rem, 4.2vw, 1.85rem);
      font-weight: 800;
      letter-spacing: 0.08em;
      color: #145218;
    }

    .hist-lead {
      max-width: 720px;
      margin: 0 auto 0.85rem;
      text-align: center;
      font-size: 0.88rem;
      line-height: 1.35;
      color: #111827;
    }

    /* Float images right so paragraphs wrap beside them, then span full width below */
    .hist-feature {
      margin-top: 0.35rem;
    }
    .hist-feature::after {
      content: "";
      display: table;
      clear: both;
    }
    .hist-aside {
      float: right;
      width: min(42%, 300px);
      max-width: 300px;
      margin: 0 0 0.5rem 0.85rem;
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }
    @media (max-width: 719px) {
      .hist-aside {
        float: none;
        width: 100%;
        max-width: 300px;
        margin: 0 auto 1rem;
      }
    }
    .hist-aside img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 14px;
      background: #ececec;
      box-shadow: 0 12px 28px rgba(17, 24, 39, 0.12);
    }
    .hist-prose {
      color: #374151;
      font-size: 0.86rem;
      line-height: 1.58;
      text-align: justify;
      min-width: 0;
    }
    .hist-prose p {
      margin: 0 0 0.78rem;
    }

    .hist-section-title {
      margin: 1.15rem 0 0.55rem;
      font-size: 1.05rem;
      font-weight: 800;
      color: #145218;
      letter-spacing: -0.01em;
    }
    .hist-explore-title {
      margin-top: 0.35rem;
    }
    .hist-compare-frame {
      background: linear-gradient(180deg, #faf6ec 0%, #efe6d4 100%);
      border: 1px solid #e5dac4;
      border-radius: 16px;
      padding: 0.65rem;
      box-shadow: 0 10px 26px rgba(17, 24, 39, 0.07);
    }
    .hist-compare-frame img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 12px;
      max-width: 520px;
      margin: 0 auto;
    }

    .hist-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.65rem 0.5rem;
      margin-top: 0.45rem;
      margin-bottom: 1.1rem;
    }
    .hist-card {
      text-decoration: none;
      background: transparent;
      border-radius: 14px;
      min-height: 0;
      padding: 0.35rem 0.2rem 0.45rem;
      color: #145218;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      gap: 0.42rem;
      border: 1px solid transparent;
      outline: none;
      -webkit-tap-highlight-color: transparent;
      transition:
        transform 0.18s ease,
        background 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease;
    }
    .hist-card-icon-ring {
      width: 3rem;
      height: 3rem;
      border-radius: 999px;
      background: linear-gradient(165deg, #0f4a14 0%, #145218 42%, #1f8a2f 100%);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 2px 10px rgba(20, 82, 24, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.28);
      transition:
        transform 0.18s ease,
        box-shadow 0.18s ease,
        filter 0.18s ease;
    }
    .hist-coffee-icon {
      width: 1.72rem;
      height: 1.72rem;
      display: block;
    }
    .hist-card-label {
      font-size: 1.05rem;
      font-weight: 700;
      font-style: italic;
      line-height: 1.12;
      text-align: center;
      color: #145218;
      transition: color 0.18s ease;
    }
    .hist-card:hover,
    .hist-card:focus-visible {
      transform: translateY(-2px);
      background: #ebf7eb;
      border-color: rgba(28, 111, 32, 0.2);
      box-shadow: 0 6px 18px rgba(20, 82, 24, 0.1);
    }
    .hist-card:focus-visible {
      outline: 2px solid #1c6f20;
      outline-offset: 2px;
    }
    .hist-card:hover .hist-card-label,
    .hist-card:focus-visible .hist-card-label {
      color: #0f4a15;
    }
    .hist-card:hover .hist-card-icon-ring,
    .hist-card:focus-visible .hist-card-icon-ring {
      transform: scale(1.06);
      box-shadow: 0 4px 14px rgba(20, 82, 24, 0.45);
      filter: brightness(1.08);
    }
    .hist-card:active {
      transform: translateY(0) scale(0.985);
      background: #dff2df;
      border-color: rgba(28, 111, 32, 0.32);
      box-shadow: 0 3px 12px rgba(20, 82, 24, 0.12);
    }
    .hist-card:active .hist-card-icon-ring {
      transform: scale(1.02);
      filter: brightness(1.1);
    }

    .hist-detail {
      margin-top: 0.85rem;
      background: #f8faf8;
      border: 1px solid #e5ebe5;
      border-radius: 12px;
      padding: 0.78rem 0.85rem;
    }
    .hist-detail h2 {
      margin: 0 0 0.35rem;
      font-size: 1.02rem;
      font-weight: 800;
      color: #145218;
    }
    .hist-detail p {
      margin: 0;
      font-size: 0.86rem;
      line-height: 1.4;
      color: #1f2937;
    }

  </style>
</head>
<body class="has-app-bottom-nav">
  <header class="hist-hero">
    <div class="hist-hero-row">
      <a class="hist-nav-back" href="about.php" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="hist-nav-title">Coffee History</h1>
    </div>
  </header>

  <main class="hist-main">
    <div>
      <span class="hist-badge"><span class="hist-badge-dot" aria-hidden="true"></span> History</span>
    </div>

    <p class="hist-lead">
      Beanthentic Coffee is a platform that highlights the hard work of local farmers and the authenticity of their coffee beans.
      It showcases different coffee varieties such as Liberica, Robusta, and Excelsa, allowing consumers to learn about the origin, quality, and unique characteristics of each bean.
    </p>

    <h2 class="hist-section-title hist-explore-title">Explore varieties</h2>
    <div class="hist-cards">
      <a href="history-liberica.php" class="hist-card" aria-label="Open Liberica article">
        <span class="hist-card-icon-ring" aria-hidden="true">
          <svg class="hist-coffee-icon" viewBox="0 0 64 64" aria-hidden="true">
            <ellipse cx="32" cy="32" rx="14" ry="19" fill="#ffffff" transform="rotate(-18 32 32)"/>
            <path d="M31 16c-3 8-3 24 1 32" fill="none" stroke="#d1d5db" stroke-width="2.8" stroke-linecap="round"/>
          </svg>
        </span>
        <span class="hist-card-label">Liberica</span>
      </a>
      <a href="history-excelsa.php" class="hist-card" aria-label="Open Excelsa article">
        <span class="hist-card-icon-ring" aria-hidden="true">
          <svg class="hist-coffee-icon" viewBox="0 0 64 64" aria-hidden="true">
            <ellipse cx="32" cy="32" rx="14" ry="19" fill="#ffffff" transform="rotate(-18 32 32)"/>
            <path d="M31 16c-3 8-3 24 1 32" fill="none" stroke="#d1d5db" stroke-width="2.8" stroke-linecap="round"/>
          </svg>
        </span>
        <span class="hist-card-label">Excelsa</span>
      </a>
      <a href="history-robusta.php" class="hist-card" aria-label="Open Robusta article">
        <span class="hist-card-icon-ring" aria-hidden="true">
          <svg class="hist-coffee-icon" viewBox="0 0 64 64" aria-hidden="true">
            <ellipse cx="32" cy="32" rx="14" ry="19" fill="#ffffff" transform="rotate(-18 32 32)"/>
            <path d="M31 16c-3 8-3 24 1 32" fill="none" stroke="#d1d5db" stroke-width="2.8" stroke-linecap="round"/>
          </svg>
        </span>
        <span class="hist-card-label">Robusta</span>
      </a>
    </div>

    <div class="hist-feature">
      <aside class="hist-aside" aria-label="Coffee history visuals">
        <img src="history/coffee_arabica_vs_robusta.png" alt="Arabica vs Robusta comparison infographic" loading="lazy" decoding="async" />
      </aside>
      <div class="hist-prose">
        <p>
          During the early 20th century, particularly during the 1950s and 1960s, the coffee industry bounced back, and the Philippine government, in collaboration with various international aid initiatives, actively promoted the replanting of coffee. They introduced Robusta to facilitate diversification within the nation's coffee production.
        </p>
        <p>
          For a faster recovery in the coffee industry, aficionados expanded coffee cultivation to diverse regions throughout the Philippines. These regions included Benguet in the northern Cordillera (region), parts of Mindanao and the island of Mindoro. These areas offered favourable climatic and altitudinal conditions for cultivating Arabica and Robusta; thus, the industry gradually regained its footing. However, challenges remained because the market dynamics were constantly shifting. Although the efforts were significant, the farmers knew they had not attained the full potential of the coffee industry.
        </p>
        <p>
          The coffee industry thrives globally, illustrated by the <em>Coffea arabica</em>, often regarded as the first variety cultivated in Lipa, and <em>Coffea conephora</em>, known as Robusta.
        </p>
        <p>
          However, another coffee variety debuted in 1843 within the tropical forests of West and Central Africa, specifically Liberia, designated as <em>Coffea liberica</em>. This particular variety flourished naturally in Sierra Leone, Cote d'Ivoire, and Ghana (Spring, 2024).
        </p>
        <p>
          Although Arabica and Robusta had gained widespread acclaim worldwide, Liberica was not readily accepted by coffee enthusiasts, except those seeking unique and diverse flavour profiles. This scenario unfolded against an increasing global demand for coffee, which the current supply could not sufficiently satisfy. The dependence on Robusta alone proved inadequate due to the declining Arabica stocks further strained by coffee rust. At this pivotal moment, <em>Coffea liberica</em> was introduced into the international coffee market, acting as a solution to the challenges faced by other coffee varieties.
        </p>
        <p>
          European colonial powers (including the Dutch and the British) embarked on the cultivation of Liberica coffee in their colonies, particularly in Southeast Asia, like the Philippines, Indonesia, and Malaysia, and also in the Caribbean. However, because Liberica shows greater resilience to diseases and pests, it became a viable crop in areas where Arabica faced challenges. Because it thrives in lowland tropical climates marked by high humidity, this distinct adaptability makes it a valuable alternative to other coffee species.
        </p>
        <p>
          <em>Coffea liberica</em> was introduced to the Philippines by American colonizers between 1890 and 1894, just before the onset of the Filipino-American Revolution. This specific variety of coffee found an optimal environment in Batangas; however, Manuel Genato (Agoncillo) was the first to bring the <em>Coffea liberica</em> variety to Batangas from Manila in 1891. He cultivated three hectares of land in sitio Abra in Banay-banay, San Jose, and Lipa. Furthermore, he distributed coffee seeds to the towns of Rosario in Batangas, San Pablo in Laguna, and Tiaong and Sariaya in Quezon (Morada, 1925).
        </p>
      </div>
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
      var backBtn = document.querySelector('.hist-nav-back');
      if (backBtn) {
        backBtn.addEventListener('click', function (event) {
          event.preventDefault();
          try {
            if (window.history && window.history.length > 1) {
              window.history.back();
              return;
            }
          } catch (_e) {}
          window.location.href = 'about.php';
        });
      }
    })();
  </script>
</body>
</html>
