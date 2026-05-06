<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#ffffff" />
  <script>
    (function () {
      function parseUser(raw) {
        if (!raw) return null;
        try {
          var u = JSON.parse(raw);
          if (u && u.email) return u;
        } catch (_err) {}
        return null;
      }
      try {
        var user =
          parseUser(localStorage.getItem('beanthentic_user')) ||
          parseUser(sessionStorage.getItem('beanthentic_user'));
        if (user) return;
        window.location.replace('login.php');
      } catch (_e) {
        window.location.replace('login.php');
      }
    })();
  </script>
  <title>Tutorial · Beanthentic</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body class="tutorial-page">
  <div id="tutorial-loader" class="tutorial-loader" aria-live="polite" aria-busy="true">
    <img
      class="tutorial-loader-bean"
      src="coffee_bean_loading.png"
      alt=""
      width="96"
      height="96"
      decoding="async"
    />
    <p class="tutorial-loader-text">Please wait for a moment</p>
  </div>

  <div id="tutorial-main" class="tutorial-main" hidden>
    <div class="tutorial-sheet" role="document">
      <section id="tutorial-step-1" class="tutorial-step tutorial-step--active" aria-label="Tutorial step 1 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img"
              src="tutorial_welcome_art.png"
              alt="Partnership, farming, and location illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-welcome-copy" class="tutorial-welcome-copy" lang="en">
            Welcome to <strong>Beanthentic</strong>, a digital platform built to automate farmer profiling and measure Lipa City&rsquo;s <strong>Kapeng Barako</strong> Geographical Indication (GI) Readiness.
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-1">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>

      <section id="tutorial-step-2" class="tutorial-step" aria-label="Tutorial step 2 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img tutorial-welcome-img--dashboard"
              src="tutorial_step2_art.png"
              alt="Dashboard and farmer registration overview illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-dashboard-copy" class="tutorial-welcome-copy" lang="en">
            Simply log in to your dashboard to view a real-time summary of registered farmers and the overall progress of our local coffee industry.
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-2">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>

      <section id="tutorial-step-3" class="tutorial-step" aria-label="Tutorial step 3 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img tutorial-welcome-img--register"
              src="tutorial_step3_art.png"
              alt="Maps, progress, and verification illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-register-copy" class="tutorial-welcome-copy" lang="en">
            To create a record, click &lsquo;Register Farmer,&rsquo; fill in the farm details, upload your documents, and hit submit to save the profile.
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-3">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>

      <section id="tutorial-step-4" class="tutorial-step" aria-label="Tutorial step 4 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img tutorial-welcome-img--qr"
              src="tutorial_step4_art.png"
              alt="QR code, security, and coffee farmer data illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-qrcode-copy" class="tutorial-welcome-copy" lang="en">
            The system will automatically generate a unique QR code for each farmer, allowing you to scan and access their data instantly during field visits.
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-4">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>

      <section id="tutorial-step-5" class="tutorial-step" aria-label="Tutorial step 5 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img tutorial-welcome-img--reports"
              src="tutorial_step5_art.png"
              alt="Three-step workflow: assign, process, and report illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-reports-copy" class="tutorial-welcome-copy" lang="en">
            You can easily search, filter, or export these records to create organized reports for the team and regional stakeholders.
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-5">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>

      <section id="tutorial-step-6" class="tutorial-step" aria-label="Tutorial step 6 of 6">
        <div class="tutorial-sheet-body tutorial-welcome">
          <div class="tutorial-welcome-art">
            <img
              class="tutorial-welcome-img tutorial-welcome-img--devices"
              src="tutorial_step6_art.png"
              alt="Laptop and smartphone sync illustration"
              width="320"
              height="320"
              decoding="async"
            />
          </div>
          <p id="tutorial-help-copy" class="tutorial-welcome-copy" lang="en">
            If you hit any snags or need technical help, just reach out to the Documentation Lead or your admin&mdash;let&rsquo;s go!
          </p>
        </div>
        <div class="tutorial-sheet-actions">
          <button type="button" class="tutorial-next-btn" id="tutorial-continue-6">
            CONTINUE <span class="tutorial-next-arrow" aria-hidden="true">→</span>
          </button>
        </div>
      </section>
    </div>
  </div>

  <script>
    (function () {
      var LOADER_MS = 1200;
      var COPY_STEP1 = {
        en:
          'Welcome to <strong>Beanthentic</strong>, a digital platform built to automate farmer profiling and measure Lipa City&rsquo;s <strong>Kapeng Barako</strong> Geographical Indication (GI) Readiness.',
        tl:
          'Maligayang pagdating sa <strong>Beanthentic</strong>, isang digital na plataporma na ginawa para awtomatikong mag-profile ng mga magsasaka at sukatin ang kahandaan ng <strong>Kapeng Barako</strong> ng Lungsod ng Lipa para sa Geographical Indication (GI).'
      };

      var COPY_STEP2 = {
        en:
          'Simply log in to your dashboard to view a real-time summary of registered farmers and the overall progress of our local coffee industry.',
        tl:
          'Mag-log in lamang sa iyong dashboard para makita ang buod ng mga rehistradong magsasaka nang real-time at ang kabuuang usad ng ating lokal na industriya ng kape.'
      };

      var COPY_STEP3 = {
        en:
          'To create a record, click &lsquo;Register Farmer,&rsquo; fill in the farm details, upload your documents, and hit submit to save the profile.',
        tl:
          'Para gumawa ng rekord, i-click ang &lsquo;Register Farmer,&rsquo; punan ang mga detalye ng bukid, i-upload ang iyong mga dokumento, at pindutin ang submit para ma-save ang profile.'
      };

      var COPY_STEP4 = {
        en:
          'The system will automatically generate a unique QR code for each farmer, allowing you to scan and access their data instantly during field visits.',
        tl:
          'Awtomatiko ay gagawa ang sistema ng natatanging QR code para sa bawat magsasaka, para ma-scan mo at ma-access ang kanilang datos kaagad kapag bumibisita sa bukid.'
      };

      var COPY_STEP5 = {
        en:
          'You can easily search, filter, or export these records to create organized reports for the team and regional stakeholders.',
        tl:
          'Maaari mong madaling maghanap, mag-filter, o mag-export ng mga rekord na ito para makagawa ng maayos na ulat para sa koponan at mga stakeholder sa rehiyon.'
      };

      var COPY_STEP6 = {
        en:
          'If you hit any snags or need technical help, just reach out to the Documentation Lead or your admin&mdash;let&rsquo;s go!',
        tl:
          'Kung may aberya ka o kailangan ng teknikal na tulong, kontakin lamang ang Documentation Lead o ang iyong admin&mdash;tara na!'
      };

      var loader = document.getElementById('tutorial-loader');
      var main = document.getElementById('tutorial-main');
      var step1 = document.getElementById('tutorial-step-1');
      var step2 = document.getElementById('tutorial-step-2');
      var step3 = document.getElementById('tutorial-step-3');
      var step4 = document.getElementById('tutorial-step-4');
      var step5 = document.getElementById('tutorial-step-5');
      var step6 = document.getElementById('tutorial-step-6');
      var btnContinue1 = document.getElementById('tutorial-continue-1');
      var btnContinue2 = document.getElementById('tutorial-continue-2');
      var btnContinue3 = document.getElementById('tutorial-continue-3');
      var btnContinue4 = document.getElementById('tutorial-continue-4');
      var btnContinue5 = document.getElementById('tutorial-continue-5');
      var btnContinue6 = document.getElementById('tutorial-continue-6');
      var copyStep1 = document.getElementById('tutorial-welcome-copy');
      var copyStep2 = document.getElementById('tutorial-dashboard-copy');
      var copyStep3 = document.getElementById('tutorial-register-copy');
      var copyStep4 = document.getElementById('tutorial-qrcode-copy');
      var copyStep5 = document.getElementById('tutorial-reports-copy');
      var copyStep6 = document.getElementById('tutorial-help-copy');

      function getTutorialLangCode() {
        var code = 'en';
        try {
          var app =
            localStorage.getItem('beanthentic_app_lang') ||
            sessionStorage.getItem('beanthentic_app_lang');
          if (app === 'fil') code = 'tl';
        } catch (_a) {}
        return code;
      }

      function applyTutorialLang() {
        var code = getTutorialLangCode();
        var langAttr = code === 'tl' ? 'tl' : 'en';
        if (copyStep1) {
          copyStep1.setAttribute('lang', langAttr);
          copyStep1.innerHTML = COPY_STEP1[code];
        }
        if (copyStep2) {
          copyStep2.setAttribute('lang', langAttr);
          copyStep2.innerHTML = COPY_STEP2[code];
        }
        if (copyStep3) {
          copyStep3.setAttribute('lang', langAttr);
          copyStep3.innerHTML = COPY_STEP3[code];
        }
        if (copyStep4) {
          copyStep4.setAttribute('lang', langAttr);
          copyStep4.innerHTML = COPY_STEP4[code];
        }
        if (copyStep5) {
          copyStep5.setAttribute('lang', langAttr);
          copyStep5.innerHTML = COPY_STEP5[code];
        }
        if (copyStep6) {
          copyStep6.setAttribute('lang', langAttr);
          copyStep6.innerHTML = COPY_STEP6[code];
        }
      }

      function showStep(which) {
        var steps = [step1, step2, step3, step4, step5, step6];
        for (var i = 0; i < steps.length; i++) {
          var el = steps[i];
          if (!el) continue;
          el.classList.toggle('tutorial-step--active', i + 1 === which);
        }
        try {
          window.scrollTo(0, 0);
        } catch (_s) {}
      }

      function showMain() {
        if (loader) {
          loader.hidden = true;
          loader.setAttribute('aria-busy', 'false');
        }
        if (main) main.hidden = false;
        showStep(1);
        applyTutorialLang();
      }

      var skipLoader = false;
      try {
        skipLoader = sessionStorage.getItem('beanthentic_skip_tutorial_loader') === '1';
        if (skipLoader) sessionStorage.removeItem('beanthentic_skip_tutorial_loader');
      } catch (_e) {}

      if (skipLoader) {
        showMain();
      } else {
        window.setTimeout(showMain, LOADER_MS);
      }

      function goHome() {
        try {
          window.location.assign(new URL('index.php#home', location.href).href);
        } catch (_e) {
          window.location.assign('index.php#home');
        }
      }

      if (btnContinue1) {
        btnContinue1.addEventListener('click', function () {
          showStep(2);
        });
      }
      if (btnContinue2) {
        btnContinue2.addEventListener('click', function () {
          showStep(3);
        });
      }
      if (btnContinue3) {
        btnContinue3.addEventListener('click', function () {
          showStep(4);
        });
      }
      if (btnContinue4) {
        btnContinue4.addEventListener('click', function () {
          showStep(5);
        });
      }
      if (btnContinue5) {
        btnContinue5.addEventListener('click', function () {
          showStep(6);
        });
      }
      if (btnContinue6) {
        btnContinue6.addEventListener('click', function () {
          goHome();
        });
      }
    })();
  </script>
</body>
</html>
