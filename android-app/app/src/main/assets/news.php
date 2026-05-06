<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#25671E" />
  <title>Register Farm Updates · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
  <link rel="stylesheet" href="css/responsive.css" />
</head>
<body class="has-app-bottom-nav updates-page">
  <style>
    .updates-page header {
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      border-radius: 0 0 22px 22px;
      padding: 0.55rem 0.95rem 0.45rem;
      box-shadow: 0 8px 18px rgba(15, 77, 18, 0.24);
    }
    .updates-page .nav {
      display: block;
    }
    .updates-topbar {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.65rem;
      min-height: 64px;
      padding: 0.12rem 0.15rem;
      color: #ffffff;
      text-decoration: none;
      position: relative;
    }
    .updates-topbar-icon {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
      position: absolute;
      left: 0.35rem;
      top: 50%;
      transform: translateY(-50%);
    }
    .updates-topbar-title {
      margin: 0;
      font-size: 1.45rem;
      line-height: 1.2;
      font-weight: 800;
      letter-spacing: 0.01em;
      color: #ffffff;
    }
    .updates-topbar-right {
      position: absolute;
      right: 0.35rem;
      top: 50%;
      transform: translateY(-50%);
      display: flex;
      align-items: center;
      gap: 0.45rem;
    }
    .updates-topbar-icon-btn,
    .updates-topbar-account-btn {
      width: 40px;
      height: 40px;
      border-radius: 999px;
      border: none;
      background: rgba(255, 255, 255, 0.95);
      color: #166534;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
      cursor: pointer;
      -webkit-tap-highlight-color: transparent;
    }
    .updates-topbar-icon-btn svg,
    .updates-topbar-account-btn svg {
      width: 20px;
      height: 20px;
    }

    .updates-main {
      width: 100%;
      max-width: none;
      margin: 0;
      padding: 0.78rem max(0.95rem, calc(env(safe-area-inset-left, 0px) + 0.55rem))
        5.8rem max(0.95rem, calc(env(safe-area-inset-right, 0px) + 0.55rem));
      box-sizing: border-box;
    }
    .updates-progress-card {
      background: #ffffff;
      color: #1f4f26;
      border-radius: 10px;
      padding: 0.62rem 0.8rem 0.52rem;
      margin: 0 0 0.58rem;
      min-height: 82px;
      border: 1px solid #e5e7eb;
    }
    .updates-progress-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 0.6rem;
      margin-bottom: 0.35rem;
    }
    .updates-progress-title {
      margin: 0;
      font-size: 1.2rem;
      line-height: 1.05;
      font-weight: 800;
      letter-spacing: -0.01em;
    }
    .updates-progress-percent {
      margin: 0;
      font-size: 1.9rem;
      line-height: 1;
      font-weight: 800;
    }
    .updates-progress-percent-sign {
      font-size: 1rem;
      font-weight: 600;
      margin-left: 0.02rem;
      opacity: 0.9;
    }
    .updates-progress-percent span {
      font-size: 0.86rem;
      font-weight: 500;
      font-style: italic;
      margin-left: 0.18rem;
    }
    .updates-progress-track {
      width: 100%;
      height: 8px;
      border-radius: 999px;
      background: #d9d2c2;
      overflow: hidden;
    }
    .updates-progress-fill {
      width: 8%;
      height: 100%;
      background: #5f9a66;
      border-radius: 999px;
    }
    .updates-ai-analysis {
      margin: 0 0 0.7rem;
      border: 1px solid #d8decf;
      background: #f8faef;
      border-radius: 10px;
      padding: 0.62rem 0.72rem;
      color: #1f4f26;
    }
    .updates-ai-analysis-title {
      margin: 0 0 0.2rem;
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.03em;
      text-transform: uppercase;
    }
    .updates-ai-analysis-text {
      margin: 0;
      font-size: 0.82rem;
      line-height: 1.35;
      font-weight: 600;
      color: #2b5f32;
    }
    .updates-upload-shell {
      background: #dce8dd;
      border-radius: 12px;
      padding: 1.2rem 0.9rem 1.1rem;
      margin-top: 0.4rem;
      border: 1px solid #d3dfd5;
    }
    .updates-upload-title {
      margin: 0;
      text-align: center;
      color: #225f2d;
      font-size: 2rem;
      line-height: 1.12;
      font-weight: 800;
      letter-spacing: -0.01em;
    }
    .updates-upload-sub {
      margin: 0.4rem 0 0.75rem;
      text-align: center;
      color: #2c7a37;
      font-size: 0.8rem;
      font-style: italic;
      font-weight: 500;
    }
    .updates-upload-dropzone {
      border-radius: 12px;
      min-height: 215px;
      background: #f1f5f2;
      border: 1px solid #d7dfd8;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 0.85rem;
      margin-bottom: 0.82rem;
      cursor: pointer;
    }
    .updates-upload-dropzone:focus-within {
      border-color: #2f7d3b;
      box-shadow: 0 0 0 3px rgba(47, 125, 59, 0.16);
    }
    .updates-upload-input {
      position: absolute;
      opacity: 0;
      width: 1px;
      height: 1px;
      pointer-events: none;
    }
    .updates-upload-icon {
      width: 58px;
      height: 58px;
      margin: 0 auto 0.4rem;
      color: #236a2d;
    }
    .updates-upload-click {
      margin: 0;
      color: #22612c;
      font-size: 1.45rem;
      line-height: 0.95;
      font-weight: 800;
      letter-spacing: -0.01em;
    }
    .updates-upload-help {
      margin: 0.32rem 0 0;
      color: #2f7d3b;
      font-size: 0.62rem;
      font-weight: 500;
      font-style: italic;
    }
    .updates-upload-filename {
      margin: 0.45rem 0 0;
      text-align: center;
      color: #225f2d;
      font-size: 0.75rem;
      font-weight: 600;
      line-height: 1.2;
    }
    .updates-upload-status {
      margin: 0 0 0.72rem;
      border-radius: 10px;
      padding: 0.6rem 0.72rem;
      font-size: 0.78rem;
      line-height: 1.3;
      font-weight: 700;
    }
    .updates-upload-status.is-success {
      background: #e8f6eb;
      color: #1d6b2a;
      border: 1px solid #8fc99b;
    }
    .updates-upload-status.is-error {
      background: #fff1f1;
      color: #9f2323;
      border: 1px solid #efb3b3;
    }
    .updates-docs-box {
      border-radius: 12px;
      background: #ffffff;
      border: 1px solid #d7dfd8;
      padding: 0.86rem 0.9rem;
      color: #225f2d;
    }
    .updates-docs-title {
      margin: 0 0 0.45rem;
      font-size: 0.84rem;
      line-height: 1.12;
      font-weight: 800;
      text-transform: uppercase;
    }
    .updates-docs-list {
      margin: 0;
      padding-left: 1.1rem;
      font-size: 0.72rem;
      line-height: 1.18;
    }
  </style>

  <header>
    <div class="nav">
      <a href="index.php#home" class="updates-topbar" aria-label="Back to home">
        <svg class="updates-topbar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        <p class="updates-topbar-title">Beanthentic GI Updates</p>
        <span class="updates-topbar-right" aria-label="Updates actions">
          <button type="button" class="updates-topbar-icon-btn" aria-label="Notifications" title="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
              <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>
      </button>
          <a href="account.php" class="updates-topbar-account-btn" aria-label="Account" title="Account">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="8" r="3.75"/>
              <path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/>
            </svg>
          </a>
        </span>
          </a>
    </div>
  </header>

  <main class="updates-main">
    <section class="updates-progress-card" aria-label="GI process status">
      <div class="updates-progress-head">
        <p class="updates-progress-title">GI PROCESS UPDATE</p>
        <p class="updates-progress-percent">
          <span id="gi-progress-percent-value">0</span><span class="updates-progress-percent-sign">%</span>
          <span id="gi-progress-complete-label" hidden>Complete</span>
        </p>
        </div>
      <div class="updates-progress-track" aria-hidden="true">
        <div id="gi-progress-fill" class="updates-progress-fill"></div>
      </div>
    </section>
    <section class="updates-ai-analysis" aria-label="AI analysis of GI progress">
      <h3 class="updates-ai-analysis-title">AI Analysis</h3>
      <p id="gi-progress-analysis" class="updates-ai-analysis-text">No admin GI update yet. Progress remains at 0%.</p>
    </section>

    <section class="updates-upload-shell" aria-label="GI document support upload">
      <h2 class="updates-upload-title">Do you have documents that<br>can help support Kapeng<br>Barako GI?</h2>
      <p class="updates-upload-sub">Send it here to help Lipa!</p>
      <p id="updates-upload-status" class="updates-upload-status" hidden></p>
      <form id="gi-support-upload-form" method="post" enctype="multipart/form-data" action="">
        <label id="gi-support-dropzone" class="updates-upload-dropzone" for="gi-support-file" aria-label="Click to upload supporting documents">
          <input
            class="updates-upload-input"
            id="gi-support-file"
            name="gi_support_file"
            type="file"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
            required
          />
          <div>
            <svg class="updates-upload-icon" viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <path d="M18 46h28c6.6 0 12-5.4 12-12 0-6.2-4.7-11.4-10.8-11.9C45.8 15.2 39.6 10 32 10c-8.4 0-15.2 6.3-16 14.5C10.8 25.2 6 30 6 36c0 5.5 4.5 10 10 10h2z" fill="currentColor"/>
              <path d="M32 20v22M24 28l8-8 8 8" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="updates-upload-click">Click to upload</p>
            <p class="updates-upload-help">PDF, DOC, JPG, PNG supported</p>
            <p id="updates-upload-filename" class="updates-upload-filename"></p>
        </div>
        </label>
      </form>
      <section class="updates-docs-box" aria-label="Accepted documents">
        <h3 class="updates-docs-title">WHAT DOCUMENTS ARE ACCEPTED?</h3>
        <ul class="updates-docs-list">
          <li>Historical documents on origin</li>
          <li>Photos &amp; technical reports</li>
          <li>Qualifying Product Documents</li>
          <li>Production Practices Report</li>
        </ul>
      </section>
    </section>
  </main>

  <script>
    (function () {
      var fileInput = document.getElementById('gi-support-file');
      var uploadForm = document.getElementById('gi-support-upload-form');
      var dropzone = document.getElementById('gi-support-dropzone');
      var fileName = document.getElementById('updates-upload-filename');
      var status = document.getElementById('updates-upload-status');
      if (!fileInput || !fileName || !uploadForm || !dropzone || !status) return;

      function openPicker() {
        fileInput.click();
      }

      dropzone.addEventListener('click', function (event) {
        if (event.target === fileInput) return;
        event.preventDefault();
        openPicker();
      });

      dropzone.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openPicker();
        }
      });

      uploadForm.addEventListener('submit', function (event) {
        event.preventDefault();
      });

      fileInput.addEventListener('change', function () {
        var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
        if (!file) {
          fileName.textContent = '';
          status.hidden = true;
          status.className = 'updates-upload-status';
          return;
        }
        fileName.textContent = 'Selected: ' + file.name;
        status.hidden = false;
        status.className = 'updates-upload-status is-success';
        status.textContent = 'File attached. Ready for upload integration.';
      });
    })();
  </script>
  <script>
    (function () {
      var percentEl = document.getElementById('gi-progress-percent-value');
      var fillEl = document.getElementById('gi-progress-fill');
      var analysisEl = document.getElementById('gi-progress-analysis');
      if (!percentEl || !fillEl || !analysisEl) return;

      function clampPercent(value) {
        var num = Number(value);
        if (!Number.isFinite(num)) return null;
        if (num < 0) num = 0;
        if (num > 100) num = 100;
        return Math.round(num);
      }

      function parseFromObject(obj) {
        if (!obj || typeof obj !== 'object') return null;
        var fields = ['giProgress', 'gi_progress', 'progress', 'progressPercent', 'percent', 'percentage'];
        for (var i = 0; i < fields.length; i++) {
          var key = fields[i];
          if (Object.prototype.hasOwnProperty.call(obj, key)) {
            var parsed = clampPercent(obj[key]);
            if (parsed !== null) return parsed;
          }
        }
        return null;
      }

      function parseAdminProgress() {
        try {
          var params = new URLSearchParams(window.location.search || '');
          var fromQuery = clampPercent(params.get('giProgress') || params.get('progress'));
          if (fromQuery !== null) return fromQuery;
        } catch (_e) {}

        var directKeys = [
          'beanthentic_gi_progress_percent',
          'beanthentic_gi_progress',
          'admin_gi_progress',
          'gi_process_progress',
          'gi_progress_percent'
        ];
        for (var i = 0; i < directKeys.length; i++) {
          var key = directKeys[i];
          try {
            var localValue = localStorage.getItem(key);
            var parsedLocal = clampPercent(localValue);
            if (parsedLocal !== null) return parsedLocal;
          } catch (_err1) {}
          try {
            var sessionValue = sessionStorage.getItem(key);
            var parsedSession = clampPercent(sessionValue);
            if (parsedSession !== null) return parsedSession;
          } catch (_err2) {}
        }

        var listKeys = ['beanthentic_admin_updates', 'beanthentic_updates', 'admin_updates'];
        for (var j = 0; j < listKeys.length; j++) {
          var listKey = listKeys[j];
          var raw = null;
          try { raw = localStorage.getItem(listKey) || sessionStorage.getItem(listKey); } catch (_err3) {}
          if (!raw) continue;
          try {
            var data = JSON.parse(raw);
            if (Array.isArray(data)) {
              for (var idx = data.length - 1; idx >= 0; idx--) {
                var parsedItem = parseFromObject(data[idx]);
                if (parsedItem !== null) return parsedItem;
              }
            } else {
              var parsedObj = parseFromObject(data);
              if (parsedObj !== null) return parsedObj;
            }
          } catch (_err4) {}
        }

        return 0;
      }

      function generateAiAnalysis(percent) {
        if (percent <= 0) {
          return 'No admin GI progress update yet. Start by uploading and validating baseline supporting documents.';
        }
        if (percent < 25) {
          return 'Early-stage GI readiness detected. Focus on collecting origin evidence and technical documentation to accelerate progress.';
        }
        if (percent < 50) {
          return 'Foundational GI requirements are in progress. Prioritize document completeness and quality checks for faster validation.';
        }
        if (percent < 75) {
          return 'GI process is steadily advancing. Continue consolidating compliant records and address any missing proofs immediately.';
        }
        if (percent < 100) {
          return 'GI readiness is nearing completion. Finalize remaining verifications and prepare for final admin review.';
        }
        return 'GI process marked complete. Recommended next step: publish final documentation and maintain periodic compliance monitoring.';
      }

      var progressPercent = parseAdminProgress();
      percentEl.textContent = String(progressPercent);
      fillEl.style.width = String(progressPercent) + '%';
      analysisEl.textContent = generateAiAnalysis(progressPercent);

      var completeLabel = document.getElementById('gi-progress-complete-label');
      if (completeLabel) {
        var isComplete = progressPercent >= 100;
        completeLabel.hidden = !isComplete;
      }
    })();
  </script>

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
</body>
</html>

