<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>
    // Auth gate: redirect guests before homepage renders (prevents flicker).
    (function () {
      function parseUser(raw) {
        if (!raw) return null;
        try {
          var u = JSON.parse(raw);
          return (u && u.email) ? u : null;
        } catch (_err) {
          return null;
        }
      }
      try {
        var localUser = parseUser(localStorage.getItem('beanthentic_user'));
        if (localUser) {
          try { sessionStorage.setItem('beanthentic_user', JSON.stringify(localUser)); } catch (_err2) {}
          return;
        }
        var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
        if (sessionUser) {
          try { localStorage.setItem('beanthentic_user', JSON.stringify(sessionUser)); } catch (_err3) {}
          return;
        }
        if (!sessionUser) {
          window.location.replace('login.php');
        }
      } catch (e) {
        window.location.replace('login.php');
      }
    })();
  </script>
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
<body class="has-app-bottom-nav">
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
        <a href="#home" class="logo" aria-label="Beanthentic home">
          <img
            class="logo-mark"
            src="beanthentic_logo.png"
            alt="Beanthentic"
          />
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
        <div class="home-hero-story">
          <div class="home-hero-story-text">
            <h2 class="home-story-title">
              <span class="home-story-brown">The Sto</span><span class="home-story-r-wrap">r<svg class="home-story-accent-icon" viewBox="0 0 22 30" aria-hidden="true" focusable="false"><g transform="rotate(14 11 15)"><path d="M11 3 C4 9 3 17 5.5 24 L11 29 L11 3Z" fill="#143d1a" /><path d="M11 3 C18 9 19 17 16.5 24 L11 29 L11 3Z" fill="#2f8f44" /><path d="M11 3 L10.2 1.2" stroke="#0f2d14" stroke-width="0.9" stroke-linecap="round" /></g></svg></span><span class="home-story-brown">y Behind </span><br aria-hidden="true" />
              <span class="home-story-green">Beanthentic</span>
            </h2>
            <div class="home-story-intro">
              <p>Beanthentic was created with a vision to empower local coffee farmers, especially producers of Kapeng Barako in Lipa City. Recognizing the challenges faced by farmers in gaining recognition, accessing market opportunities, and showcasing the authenticity of their beans, the platform was designed to provide a dedicated profiling system for coffee growers.</p>
            </div>
            <figure class="home-story-inline-media">
              <img
                class="home-hero-harvest-img"
                src="home/coffee_cherries_hands.png"
                alt="Hands holding freshly harvested coffee cherries"
                width="1024"
                height="576"
                loading="lazy"
              />
            </figure>
          </div>
          <div class="home-hero-media">
            <figure class="home-hero-harvest">
              <img
                class="home-hero-harvest-img"
                src="home/coffee_farmer_harvest.png"
                alt="Coffee farmer harvesting ripe coffee cherries in the field"
                width="1024"
                height="576"
                loading="lazy"
              />
            </figure>
            <div class="home-hero-media-text">
              <p>Through Beanthentic, farmers can create verified profiles, share their farming practices, and highlight the origin and quality of their coffee beans. The platform connects local producers to buyers, promotes transparency, and helps preserve the rich heritage of Kapeng Barako.</p>
              <p>By building a digital community, Beanthentic supports collaboration among coffee farmers, strengthens their identity, and opens opportunities for growth. Ultimately, the goal of Beanthentic is to make local coffee farmers more visible, valued, and competitive while celebrating the authentic taste of Lipa City's coffee culture.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about-mission-vision">
      <div class="about-grid">
        <article class="about-card">
          <div class="about-pill"><span class="about-pill-dot"></span><span class="about-pill-text">Mission</span></div>
          <div class="about-topic is-active" data-about-panel="about-mission-vision" data-about-pill-label="Mission">
            <h4>Mission and Vision</h4>
            <p>Beanthentic's mission and vision guide how we support local coffee farmers through authentic, traceable, and technology-driven coffee systems.</p>
            <img
              class="mission-vision-image"
              src="mission_vision_about.png"
              alt="Beanthentic mission and vision"
            />
          </div>
          <div id="about-history" class="about-topic" data-about-panel="about-history" data-about-pill-label="History">
            <h4>HISTORY</h4>
            <div class="types-liberica-wrap">
              <img
                class="types-liberica-photo"
                src="history/coffee_history_cherries.png"
                alt="Coffee cherries"
              />
              <p>During the early 20th century, particularly during the 1950s and 1960s, the coffee industry bounced back, and the Philippine government, in collaboration with various international aid initiatives, actively promoted the replanting of coffee. They introduced Robusta to facilitate diversification within the nation's coffee production.</p>
              <p>For a faster recovery in the coffee industry, aficionados expanded coffee cultivation to diverse regions throughout the Philippines. These regions included Benguet in the northern Cordillera (region), parts of Mindanao and the island of Mindoro. These areas offered favourable climatic and altitudinal conditions for cultivating Arabica and Robusta; thus, the industry gradually regained its footing. However, challenges remained because the market dynamics were constantly shifting. Although the efforts were significant, the farmers knew they had not attained the full potential of the coffee industry.</p>
              <p>The coffee industry thrives globally, illustrated by the <em>Coffea arabica</em>, often regarded as the first variety cultivated in Lipa, and <em>Coffea canephora</em>, known as Robusta.</p>
            </div>

            <div class="types-liberica-wrap">
              <img
                class="types-liberica-photo"
                src="history/coffee_arabica_vs_robusta.png"
                alt="Arabica vs. Robusta comparison"
              />
              <p>However, another coffee variety debuted in 1843 within the tropical forests of West and Central Africa, specifically Liberia, designated as <em>Coffea liberica</em>. This particular variety flourished naturally in Sierra Leone, Cote d'Ivoire, and Ghana (Spring, 2024).</p>
              <p>Although Arabica and Robusta had gained widespread acclaim worldwide, Liberica was not readily accepted by coffee enthusiasts, except those seeking unique and diverse flavour profiles. This scenario unfolded against an increasing global demand for coffee, which the current supply could not sufficiently satisfy. The dependence on Robusta alone proved inadequate due to the declining Arabica stocks further strained by coffee rust. At this pivotal moment, <em>Coffea liberica</em> was introduced into the international coffee market, acting as a solution to the challenges faced by other coffee varieties.</p>
              <p>European colonial powers (including the Dutch and the British) embarked on the cultivation of Liberica coffee in their colonies, particularly in Southeast Asia, like the Philippines, Indonesia, and Malaysia, and also in the Caribbean. However, because Liberica shows greater resilience to diseases and pests, it became a viable crop in areas where Arabica faced challenges. Because it thrives in lowland tropical climates marked by high humidity, this distinct adaptability makes it a valuable alternative to other coffee species.</p>
              <p><em>Coffea liberica</em> was introduced to the Philippines by American colonizers between 1890 and 1894, just before the onset of the Filipino-American Revolution. This specific variety of coffee found an optimal environment in Batangas; however, Manuel Genato (Agoncillo) was the first to bring the <em>Coffea liberica</em> variety to Batangas from Manila in 1891. He cultivated three hectares of land in sitio Abra in Banay-banay, San Jose, and Lipa. Furthermore, he distributed coffee seeds to the towns of Rosario in Batangas, San Pablo in Laguna, and Tiaong and Sariaya in Quezon (Morada, 1925).</p>
            </div>
            <img
              class="history-bottom-image"
              src="history/arabica_liberica_robusta.png"
              alt="Arabica vs Liberica vs Robusta comparison"
            />
          </div>
          <div id="about-liberica" class="about-topic" data-about-panel="about-liberica" data-about-pill-label="Liberica">
            <h4>LIBERICA</h4>
            <p>Although most coffee aficionados are well-versed in Arabica and Robusta varieties, there exists a lesser-known third species of coffee bean: Liberica. This particular variety constitutes approximately 2% of global coffee consumption; however, most of this consumption transpires in the nation where Liberica coffee enjoys considerable popularity: the Philippines (Earl of Coffee, 2020).</p>
            <p>Liberica, or Liberian coffee (Coffea liberica), is frequently referred to as the "third coffee crop species" (after Coffea arabica and Coffea canephora). A commonly misquoted statistic suggests that Liberica comprises approximately 1 per cent of the global coffee supply; however, the production output of this species remains minuscule when compared with that of Arabica and Robusta. Although, during the latter portion of the 19th century, Liberica was positioned alongside Arabica as the second most significant species in the coffee trade (Ralph, 2023), this status has diminished over time.</p>
            <p>Liberica, a coffee species from Liberia (West Africa), was introduced with remarkable scale and efficiency beginning in the early 1870s. This introduction primarily responded to the devastating coffee leaf rust epidemic that severely impacted Arabica plantations in Sri Lanka. However, shortly thereafter, Liberica was utilized to expand coffee production into low-elevation tropical regions where climatic conditions-characterized by excessive heat and moisture-rendered Arabica cultivation untenable. Consequently, Liberica has cultivated a steadfast following in the Philippines, Malaysia, and Indonesia. Due to an outbreak of coffee rust disease in various regions, the Philippines emerged as one of the foremost exporters of this rare coffee species. This development was significant because Liberica effectively addressed the demand for coffee in areas where Arabica could not flourish.</p>
            <img
              class="liberica-types-image"
              src="liberica_types.png"
              alt="Types of coffee beans including Liberica"
            />
            <p>Researchers, governmental authorities, and cultivators appreciate the dimensions and vigor of Liberica. Liberica beans, which are notably larger, possess an irregular morphology in contrast to Arabica and Robusta beans. They are celebrated for their unique, smoky, and fruity profiles, often infused with subtle floral and woody undertones. Furthermore, these plants are resilient; they can endure elevated temperatures. This distinct flavor profile sets them apart from conventional coffee varieties. There exist multiple varieties of Liberica. These are:</p>
            <h4>1. Standard or Traditional Liberica</h4>
            <p>The term "Traditional" or "Standard" Liberica refers to the original and unaltered manifestation of Liberica that thrives naturally in the geographic regions of West Africa and Southeast Asia. It is, indeed, categorically distinct from its counterparts, Arabica and Robusta, particularly when considering variables such as bean size, shape, flavor profile, and resilience. The beans characteristic of Liberica are notably larger than those produced by Arabica and Robusta, presenting an asymmetrical, oval, or almond-like morphology with a pointed apex. However, its structural integrity is more pronounced, showcasing greater hardness and density; this renders the processing of such beans somewhat more arduous.</p>
            <p>This particular variety of Liberica is indigenous to West Africa-most prominently Liberia, Nigeria, and the Ivory Coast. In contemporary times, it is also cultivated in Ghana and various Southeast Asian localities, specifically in Batangas and Cavite within the Philippines, as well as Johor in Malaysia and regions such as Borneo and Sumatra in Indonesia. It is now predominantly enjoyed within local coffee cultures and is frequently incorporated into coffee blends to augment complexity. This burgeoning interest in specialty coffee markets can be attributed to its distinctive flavor profile, creating a revival because of its unique characteristics.</p>
            <h4>2. Kapeng Barako (Barako Coffee)</h4>
            <div class="barako-split">
              <div class="barako-split-text">
                <p>Kapeng Barako is a distinct Filipino variety of Coffea liberica, renowned for its potent, robust and fragrant profile. Primarily cultivated in the provinces of Batangas and Cavite within the Philippines, this coffee holds profound cultural and historical relevance.</p>
                <p>This variety was initially introduced to Batangas at Sitio Abra, situated between Banay-banay of San Jose and Banay-banay of Lipa of the notable Manuel Genato-Agoncillo. According to Morada (1925), the seeds were meticulously planted and nurtured across a three-hectare estate. Subsequently, they disseminated the harvested seeds not only in Batangas (Lipa and Rosario), Laguna (San Pablo), and Quezon (Sariaya and Tiaong) but also in some adjacent towns.</p>
                <p>Kapeng Barako's beans are comparatively larger than Arabica and Robusta, characterized by an almond shape and asymmetry. Farmers perform specialized processing of Kapeng Barako because the beans exhibit greater hardness and density.</p>
              </div>
              <img
                class="barako-image"
                src="kapeng_barako.png"
                alt="Kapeng Barako coffee cherries"
              />
            </div>
            <section class="types-liberica-section" aria-labelledby="types-liberica-heading">
              <h4 id="types-liberica-heading" class="types-liberica-title">TYPES OF LIBERICA</h4>
              <h4 class="types-liberica-subtitle">Johor Liberica</h4>
              <div class="types-liberica-wrap">
                <img
                  class="types-liberica-photo"
                  src="johor_liberica.png"
                  alt="Johor Liberica coffee cherries on the branch"
                />
                <p>Within the bounds of Robusta and Arabica coffees, coffee lovers can compare Liberica to a mythical unicorn. Regarded as the sweetest among coffee species, it is so exceedingly rare that it has been deemed endangered; various sources contend that it comprises less than 1% of global coffee production. However, for a farm in Johor, Malaysia, Liberica coffee represents not merely a product but a way of life (Khaw, 2022).</p>
                <p>Johor liberica represents a distinctive cultivar of Liberica coffee cultivated in Johor, Malaysia. It stands as one of the select locales across the globe that continues to engage in the commercial production of liberica. Thus, rendering it a unique and consequential coffee within the Southeast Asian context. Unlike its other liberica counterparts, Johor liberica is renowned for its smooth, nutty, and chocolatey flavor profile; this quality renders it more sophisticated than the robust and smoky Kapeng Barako hailing from the Philippines.</p>
                <p>The dimensions of Johor Liberica beans are analogous to those of other Liberica varieties. They exhibit an asymmetrical, oval, and almond-like shape. Its coffee presents nutty and dark chocolatey notes, accompanied by floral and fruity undertones. This variety is less smoky and bitter in comparison to traditional Liberica while simultaneously offering a smoother and milder experience than the potent Kapeng Barako. However, akin to the Kapeng Barako, it has a rich aroma laced with subtle sweetness, which adds to its allure.</p>
                <p>Johor (the premier liberica-producing region in Malaysia) contributes over 90% of the nation's coffee output; however, its significance extends beyond mere statistics. Johor liberica is rapidly gaining traction within specialty coffee circles because some roasters are keen to highlight its unique taste profile.</p>
              </div>
            </section>
            <section class="borneo-liberica-section" aria-labelledby="borneo-liberica-heading">
              <h4 id="borneo-liberica-heading" class="types-liberica-subtitle">Borneo Liberica</h4>
              <div class="types-liberica-wrap">
                <img
                  class="types-liberica-photo"
                  src="borneo_liberica.png"
                  alt="Borneo Liberica coffee cherries on the branch"
                />
                <p>Borneo liberica represents a rare variety of liberica coffee cultivated on the island of Borneo, which is divided among Malaysia, specifically Sabah &amp; Sarawak, Indonesia (known as <strong>Kalimantan</strong>) and Brunei. It flourishes in lowland tropical climates and is known for its mild, fruity and slightly floral flavor profile. This taste distinguishes it from the stronger and smokier Kapeng Barako and the nutty Johor Liberica. However, one must appreciate that the unique attributes of Borneo Liberica stem from its specific environmental conditions.</p>
                <p>Borneo liberica presents a mild yet intricate flavour profile; it boasts a fruity and floral aroma, encompassing hints of tropical fruits (such as jackfruit, mango, and citrus), caramel, and subtle spices. However, it exhibits lesser smokiness and bitterness than traditional Liberica. Furthermore, this variety is characterized by a wine-like acidity reminiscent of excelsa coffee, which enhances its overall appeal—although some may find it less robust than other variants.</p>
                <p>The arboreal species known as Bornean Liberica flourishes within the lowland, humid tropical forests of Borneo, and the beans undergo a traditional sun-drying process and are naturally processed, which improves the inherent sweetness. Bornean Liberica is frequently brewed through methods such as pour-over, drip, or espresso to accentuate its fruitiness (Ting, 2023), and the coffee roasters combine these beans with Arabica or Robusta varieties to achieve a harmonious balance of acidity and body.</p>
                <p>Although the world does not recognize Borneo for its coffee production, small-scale farms cultivate Liberica as an integral component of local agricultural practices. To bolster their coffee industry, the Sarawak region in Malaysia and Kalimantan in Indonesia are actively promoting the production of specialty coffee, and they are beginning to acknowledge Borneo Liberica due to its distinctive flavor profile. However, this coffee variety remains a niche due to the limited international availability.</p>
                <p>To further enhance its flavor profile and develop a singular taste distinct from other <em>Liberica</em> varieties, local farmers in Malaysia and Indonesia engage in experimental practices involving honey and anaerobic fermentation. Specialty coffee establishments in urban centers such as Kuala Lumpur, Jakarta, and Singapore progressively incorporate Borneo Liberica into their offer as a premium coffee selection.</p>
              </div>
            </section>
            <section class="west-african-liberica-section" aria-labelledby="west-african-liberica-heading">
              <h4 id="west-african-liberica-heading" class="types-liberica-subtitle">Nigerian and West African Liberica</h4>
              <div class="types-liberica-wrap">
                <img
                  class="types-liberica-photo"
                  src="west_african_liberica.png"
                  alt="West African Liberica coffee flowers and cherries"
                />
                <p>Liberica represents one of the initial coffee species cultivated within the region before its introduction to Southeast Asia in the 19th century. However, in contrast to the more ubiquitous Arabica and Robusta varieties, West African Liberica emerges as a rare and distinctive coffee cultivar characterized by a bold, woody, and subtly smoky flavor profile.</p>
                <p>Although it is not as extensively cultivated as Robusta across Africa today, it retains significance within local coffee traditions and specialty markets. Nigerian and West African Liberica flourishes in the lowland, humid tropical climates of Nigeria, Liberia, Ghana, and Ivory Coast; farmers frequently cultivate this variety on small farms alongside additional crops, such as cocoa and plantains. The beans undergo traditional sun-drying and fermentation processes to augment their complex flavors; thus, they are brewed within local coffee customs and occasionally combined with spices or milk. This variety also pairs well with robust black coffee or espresso-style beverages.</p>
                <p>African coffee lovers regarded Liberica, indigenous to West Africa, as a heritage crop in this region. It once served as a significant export during the colonial era; however, Robusta overcomes this variety in the landscape of African coffee production. Although coffee farmers continue to develop Nigerian Liberica on diminutive farms, primarily for local consumption and niche markets, its prominence has diminished. Furthermore, Ghana, Liberia, and the Ivory Coast contribute modest quantities of Liberica for regional utilization. This West African variety remains scarce and is not as extensively exported as its more popular counterparts, Robusta and Arabica. Some African farmers are attempting to reintroduce Liberica as a specialty coffee, primarily because of its distinctive flavour. In Europe and the United States, specialty coffee roasters are experimenting with West African Liberica, incorporating it into various blends and single-origin selections.</p>
              </div>
            </section>
          </div>
          <div id="about-robusta" class="about-topic" data-about-panel="about-robusta" data-about-pill-label="Robusta">
            <h4>ROBUSTA</h4>
            <p>Robusta coffee variety, as a significant cash crop, plays a pivotal role in Indonesia's agricultural landscape. According to Index Mundi, Indonesia ranks as the world's third-largest producer of Robusta, trailing only behind Brazil and Vietnam. The extensive expanses of suitable land for coffee cultivation and diverse microclimates render it particularly advantageous for producing Robusta coffee. Mia Lakhsmi Handayani, a Q Grader specializing in Robusta in Indonesia, asserts that &quot;These conditions [in Indonesia] also mean robusta flavors can range from more soft, subtle coffees with notes of chocolate and fruit grown in Central Java, to more intense flavors of vanilla and chocolate, like robusta grown in Sumatra&quot; (Perfect Daily Grind, 2022).</p>
            <div class="types-liberica-wrap types-liberica-wrap--side-by-side">
              <p>The majority of farmers in Indonesia are smallholders who are cultivating coffee on approximately one or two hectares of land. Because many of these smallholder farmers grow coffee primarily for subsistence, they frequently find themselves with insufficient funds to reinvest in their agricultural practices, this results in quality control becoming a significant concern. However, despite the abundance of coffee production in Indonesia, there is a need to enhance both quality and yields. For this reason, coffee farmers have initiated the development of this new breed of coffee.</p>
              <img
                class="types-liberica-photo"
                src="history/robusta_farmer_harvest.png"
                alt="Farmer harvesting Robusta coffee cherries in Indonesia"
              />
            </div>
            <p>BP 358 represents a high-yielding Robusta coffee variety meticulously developed in Indonesia within the framework of a government breeding initiative to enhance Robusta production. The goal was to create a cultivar that would be esteemed for its robust disease resistance, commendable cup quality, and adaptability to tropical climates. Indeed, it stands as the most extensively cultivated Robusta clone within Indonesia, frequently employed in commercial coffee production. This breed has been selected specifically for its impressive productivity and resilience against pests and diseases.</p>
            <p>Notably, this variety is recognized because the plant exhibits rapid growth and a robust structure characterized by strong branches. Furthermore, it is comparable to other varieties by its remarkably high yield than traditional Robusta strains, demonstrating exceptional suitability for low to mid-altitude regions (ranging from 200 to 800 meters above sea level). Moreover, BP 358 can resist coffee rust and other prevalent coffee diseases and tolerate pests and environmental stresses. Thus, it emerges as a particularly advantageous choice for commercial farms, although some may argue that reliance on a single variety could pose risks.</p>
            <p>In the realm of cup quality and flavor profile, BP 358 surpasses standard Robustas in a manner that renders it a favored selection for espresso blends characterized by a robust body, opulent crema, elevated caffeine content, and a trace of subdued bitterness. These attributes particularly suit instant coffee, espresso, and various commercial blends. Indonesian farmers extensively cultivate this variety across regions such as Sumatra, Java, and Sulawesi; however, it is also utilized in Vietnam and other nations engaged in Robusta production for large-scale coffee cultivation. Because of its prolific output, BP 358 holds significant importance due to its high yield and disease resistance, presenting a lucrative opportunity for farmers. It is additionally advantageous for espresso and commercial coffee blends due to its superior cup quality. This variety plays a pivotal role in Indonesia's Robusta coffee sector, which has enabled the country to emerge as one of the globe's preeminent producers.</p>
          </div>
          <div id="about-excelsa" class="about-topic" data-about-panel="about-excelsa" data-about-pill-label="Excelsa">
            <h4>EXCELSA</h4>
            <p><strong>Excelsa (Excelsa Coffee)</strong></p>
            <p>Excelsa coffee, once regarded as the fourth coffee variety alongside Arabica, Robusta and Liberica, is now reclassified as a variety of <em>Coffea liberica</em>; however, its unique characteristics remain noteworthy. This rare and exotic species is known for its tart, fruity, and complex flavour profile. Although today, Excelsa is mainly cultivated in Southeast Asia (including Vietnam and the Philippines) and India, its initial discovery occurred in 1903 in Central Africa, known as <em>Coffea dewevrei</em> or Dewevreie (San Max, 2021). Many believed this coffee variety to be a distinct species within the Coffea genus; however, it was not until 2006 that it received official recognition as the Dewevrei variety of the Liberica species.</p>
            <img
              class="history-bottom-image"
              src="history/excelsa_green_cherries.png"
              alt="Green Excelsa coffee cherries"
            />
            <p>Numerous scholars and enthusiasts have posited that this particular coffee variety constitutes a distinct species within the Coffea genus; however, it was not until 2006 that it attained formal acknowledgement as the Dewevrei variety of the Liberica species. Because of the prior ambiguities surrounding the classification of excelsa, its trajectory within the contemporary coffee marketplace has engendered considerable confusion, which leads to broader generalizations and synonymous applications of these designations. This, in turn, results in a dearth of reliable data about accurate production levels. Furthermore, it may also be accountable for a decline in the quality of Excelsa coffee. This scenario presents farmers with minimal motivation to adopt quality control measures. The International Coffee Organization (ICO) has refrained from providing official statistics regarding the production or trade of excelsa coffee because the demand for both Excelsa and Liberica coffee is not deemed commercially significant.</p>
            <p>Excelsa thrives optimally at elevations ranging from 1,000 to 1,300 meters, unlike Arabica and Robusta. It manifests as an arboreal (tree-like) organism rather than merely a shrub. This characteristic necessitates vertical space for its growth, as opposed to expanding laterally into the terrestrial environment. However, although it is known for its productivity and resilience, managing Excelsa poses significant challenges due to its requirement for extensive care.</p>
            <img
              class="history-bottom-image"
              src="history/excelsa_tree_branch.png"
              alt="Excelsa coffee tree branches with cherries"
            />
            <p>The foliage of the Excelsa plant is notably large (averaging 26 cm in length and 13 cm in width) and possesses a leathery texture. Its floral structures exhibit multiple blooms throughout the harvesting season, yet fruit maturation typically spans approximately one full year. These flowers surpass the size of those found on Arabica and Canephora plants. Furthermore, Excelsa yields asymmetrical beans that measure, on average, around 9 mm in length and 6 mm in width (SanMax).</p>
            <p>The size of Excelsa beans resembles that of Liberica; however, they are somewhat smaller in dimension. Their beans possess an asymmetrical, almond-like shape akin to those of Liberica. A tart and fruity flavor profile characterizes this variety, including dark berries, citrus, and tamarind. The taste is light yet full-bodied, offering coffee drinkers a multilayered sensory experience. Although excelsa often possesses a bright, wine-like acidity, it is somewhat nutty and woody in character yet less smoky compared to Liberica. Its caffeine content parallels Arabica and Liberica but is lower than Robusta. This attribute results in a milder energy boost when compared to the more robust effects of strong Robusta or Kapeng barako (<em>Coffea liberica</em>).</p>
          </div>
          <div id="about-how-to-get-there" class="about-topic" data-about-panel="about-how-to-get-there" data-about-pill-label="Travel">
            <h4>How to Get There</h4>
            <p>Coffee Bean Farms in Lipa City, Batangas can be reached by land transportation from Manila and other nearby areas via Sto. Tomas and Tanauan, Batangas. It is accessible through buses, jeepneys, and other public utility vehicles passing along South Luzon Expressway (SLEX) and Maharlika Highway going to Lipa City. The usual route follows Manila to Sto. Tomas, Sto. Tomas to Tanauan, and Tanauan to Lipa City proper. Travel time is approximately 2 to 3 hours depending on traffic conditions.</p>
            <p>Commuting options include buses and jeepneys that regularly ply routes from Manila to Batangas. From Manila, passengers may ride buses bound for Lipa City or Batangas Grand Terminal. These buses pass through Sto. Tomas and Tanauan before reaching Lipa City. Jeepneys are also available for inter-town travel once inside Batangas.</p>
            <p>The major terminals in the area are the following:</p>
            <p>Buendia Bus Terminals (Manila)<br>Sto. Tomas Public Terminal<br>Tanauan City Transport Terminal<br>Lipa City Grand Terminal</p>
            <p>These terminals are utilized for passenger transport and the movement of goods and services between Metro Manila and Batangas, as well as within nearby municipalities.</p>
            <p>The transport system in Lipa City is supported by buses, jeepneys, and tricycles that accommodate passengers going to commercial and agricultural destinations such as Coffee Bean Farms. These vehicles operate daily with regular trips along the Manila-Sto. Tomas-Tanauan-Lipa route, ensuring convenient access for commuters and visitors.</p>
          </div>
        </article>
      </div>
    </section>
    </div>
  </main>

  <nav class="app-bottom-nav" aria-label="Quick navigation">
    <div class="app-bottom-nav-inner">
      <a href="#home" class="app-bottom-nav-link is-active" aria-current="page">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Home</span>
      </a>
      <div class="app-bottom-nav-about">
        <a
          href="about.php#about-history"
          data-no-loader="true"
          class="app-bottom-nav-link app-bottom-nav-about-btn"
          id="bottom-nav-about-toggle"
        >
          <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
            <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><circle cx="12" cy="8" r="1" fill="currentColor" stroke="none"/></svg>
          </span>
          <span class="app-bottom-nav-label">About</span>
        </a>
        <div id="bottom-nav-about-menu-disabled" class="app-bottom-nav-about-menu" role="menu" hidden aria-label="About sections">
          <div class="app-bottom-nav-about-group" role="none">
            <button
              type="button"
              id="bottom-nav-history-toggle"
              class="app-bottom-nav-about-item app-bottom-nav-about-history-btn"
              aria-expanded="false"
              aria-controls="bottom-nav-history-submenu"
            >
              <span>History</span>
              <span class="app-bottom-nav-history-chevron" aria-hidden="true"></span>
            </button>
            <div id="bottom-nav-history-submenu" class="app-bottom-nav-history-submenu" role="group" aria-label="History — varieties" hidden>
              <a href="#about-liberica" class="app-bottom-nav-about-item app-bottom-nav-about-item--nested" role="menuitem">Liberica</a>
              <a href="#about-robusta" class="app-bottom-nav-about-item app-bottom-nav-about-item--nested" role="menuitem">Robusta</a>
              <a href="#about-excelsa" class="app-bottom-nav-about-item app-bottom-nav-about-item--nested" role="menuitem">Excelsa</a>
            </div>
          </div>
          <a href="#about-mission-vision" class="app-bottom-nav-about-item" role="menuitem">Mission and Vision</a>
          <a href="#about-how-to-get-there" class="app-bottom-nav-about-item" role="menuitem">How to Get There</a>
        </div>
      </div>
      <a href="http://10.0.2.2:5000/register-farm" data-beanthentic-flask="/register-farm" class="app-bottom-nav-link app-bottom-nav-link--featured">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Register</span>
      </a>
      <a href="http://10.0.2.2:5000/maps" data-beanthentic-flask="/maps" class="app-bottom-nav-link">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </span>
        <span class="app-bottom-nav-label">Map</span>
      </a>
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>

  <script>
    (function () {
      function flaskBase() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base');
          if (s && String(s).replace(/\s/g, '')) return String(s).replace(/\/$/, '');
        } catch (e) {}
        if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
          return (location.origin || '').replace(/\/$/, '');
        }
        return 'http://10.0.2.2:5000';
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
    // Bottom nav active state is handled globally in js/ui.js (syncAppBottomNavActive).
  </script>
</body>
</html>


