<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <script src="js/beanthentic_api_urls.js?v=20260527-gi"></script>
  <title>Register Farm Updates · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
  <link rel="stylesheet" href="css/responsive.css" />
</head>
<body class="has-app-bottom-nav updates-page">
  <style>
    .updates-page {
      --gi-pale-border: #e5e7eb;
      --gi-pale-text: #1f4f26;
      background: #ffffff;
    }
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
      padding: 0;
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
      background: #ffffff;
      border-radius: 12px;
      padding: 0.55rem;
      margin-top: 0.4rem;
      border: 1px solid var(--gi-pale-border);
      box-shadow: 0 2px 10px rgba(17, 24, 39, 0.06);
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
    .gi-login-notice-modal {
      position: fixed;
      inset: 0;
      display: none;
      align-items: center;
      justify-content: center;
      background: rgba(9, 58, 17, 0.33);
      z-index: 100000;
      padding: 1rem;
      box-sizing: border-box;
    }
    .gi-login-notice-modal.is-open {
      display: flex;
    }
    .gi-login-notice-card {
      width: min(92vw, 420px);
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      color: #f8fafc;
      border-radius: 16px;
      box-shadow: 0 18px 40px rgba(9, 58, 17, 0.38);
      padding: 1rem 1rem 0.85rem;
    }
    .gi-login-notice-brand {
      margin: 0 0 0.55rem;
      font-size: 0.95rem;
      font-weight: 800;
    }
    .gi-login-notice-text {
      margin: 0;
      font-size: 1rem;
      line-height: 1.4;
      color: #ecfdf3;
      font-weight: 600;
    }
    .gi-login-notice-actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 0.9rem;
    }
    .gi-login-notice-ok {
      border: 1px solid rgba(13, 84, 23, 0.32);
      border-radius: 10px;
      background: #f0fdf4;
      color: #14532d;
      font-weight: 800;
      font-family: inherit;
      padding: 0.48rem 0.82rem;
      cursor: pointer;
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

    /* ---------------------------------------------------------------------- */
    /* GI compose (Gmail-style)                                                */
    /* ---------------------------------------------------------------------- */
    .gi-compose-card {
      background: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: none;
      border: none;
    }
    .gi-mail-from {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.55rem 0.85rem;
      border-bottom: 1px solid #0b4d12;
      background: linear-gradient(165deg, #0f5f16 0%, #054d19 100%);
      color: #ffffff;
    }
    .gi-mail-avatar {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      background: linear-gradient(145deg, #c9a227 0%, #8b5e2b 100%);
      border: 2px solid rgba(255, 255, 255, 0.85);
      color: #ffffff;
      display: grid;
      place-items: center;
      font-weight: 800;
      font-size: 0.82rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
    }
    .gi-mail-from-main { min-width: 0; }
    .gi-mail-from-name { margin: 0; font-weight: 700; font-size: 0.84rem; color: #ffffff; line-height: 1.1; }
    .gi-mail-from-sub { margin: 0.08rem 0 0; font-size: 0.74rem; color: #d1fae0; }
    .gi-mail-time { margin-left: auto; font-size: 0.72rem; color: #bbf7d0; white-space: nowrap; }
    .gi-compose-editor {
      display: block;
      width: 100%;
      min-height: 168px;
      border: 0;
      margin: 0;
      padding: 0.85rem 0.95rem;
      box-sizing: border-box;
      resize: vertical;
      font-family: inherit;
      font-size: 0.9rem;
      line-height: 1.5;
      color: #202124;
      background: #ffffff;
      outline: none;
    }
    .gi-compose-editor::placeholder { color: #9aa0a6; }
    .gi-compose-attachments {
      display: flex;
      flex-wrap: wrap;
      gap: 0.4rem;
      padding: 0 0.85rem 0.55rem;
    }
    .gi-compose-attachments:empty { display: none; padding: 0; }
    .gi-compose-attach-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      max-width: 100%;
      padding: 0.28rem 0.62rem;
      border-radius: 999px;
      background: #f1f3f4;
      color: #3c4043;
      font-size: 0.72rem;
      font-weight: 600;
      line-height: 1.2;
      border: 0;
      cursor: pointer;
      font-family: inherit;
    }
    .gi-compose-attach-chip:hover,
    .gi-compose-attach-chip:focus-visible {
      background: #e8eaed;
      outline: none;
    }
    .gi-compose-attach-chip span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      max-width: 9.5rem;
    }
    .updates-upload-status.is-viewable {
      width: 100%;
      text-align: left;
      cursor: pointer;
      font-family: inherit;
      transition: background 0.15s ease;
    }
    .updates-upload-status.is-viewable:hover {
      filter: brightness(0.97);
    }
    .gi-attach-preview {
      position: fixed;
      inset: 0;
      z-index: 1200;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      box-sizing: border-box;
    }
    .gi-attach-preview[hidden] { display: none !important; }
    .gi-attach-preview-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(17, 24, 39, 0.55);
    }
    .gi-attach-preview-panel {
      position: relative;
      z-index: 1;
      width: min(100%, 520px);
      max-height: min(88vh, 720px);
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 18px 40px rgba(17, 24, 39, 0.28);
    }
    .gi-attach-preview-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
      padding: 0.55rem 0.7rem;
      border-bottom: 1px solid #e8eaed;
      background: #f8f9fa;
    }
    .gi-attach-preview-title {
      margin: 0;
      font-size: 0.8rem;
      font-weight: 700;
      color: #202124;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      min-width: 0;
    }
    .gi-attach-preview-close {
      border: 0;
      background: transparent;
      color: #5f6368;
      width: 32px;
      height: 32px;
      border-radius: 999px;
      cursor: pointer;
      flex-shrink: 0;
    }
    .gi-attach-preview-close:hover { background: #e8eaed; }
    .gi-attach-preview-body {
      flex: 1 1 auto;
      min-height: 200px;
      max-height: calc(88vh - 56px);
      overflow: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem;
      background: #ffffff;
    }
    .gi-attach-preview-body img {
      max-width: 100%;
      max-height: 68vh;
      object-fit: contain;
      border-radius: 6px;
    }
    .gi-attach-preview-body iframe {
      width: 100%;
      min-height: 60vh;
      border: 0;
    }
    .gi-attach-preview-fallback {
      margin: 0;
      padding: 1rem;
      text-align: center;
      font-size: 0.86rem;
      line-height: 1.45;
      color: #4b5563;
    }
    .gi-attach-preview-open {
      display: inline-block;
      margin-top: 0.65rem;
      color: #1a73e8;
      font-weight: 700;
      text-decoration: none;
    }
    .gi-compose-toolbar {
      display: flex;
      align-items: center;
      gap: 0.15rem;
      padding: 0.45rem 0.55rem 0.55rem;
      border-top: 1px solid var(--gi-pale-border);
      background: #ffffff;
    }
    .gi-compose-send-group {
      display: inline-flex;
      align-items: stretch;
      border-radius: 18px;
      overflow: hidden;
      flex-shrink: 0;
      box-shadow: 0 1px 3px rgba(15, 95, 22, 0.28);
    }
    .gi-compose-send-main {
      border: 0;
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      color: #ffffff;
      font-size: 0.86rem;
      font-weight: 600;
      padding: 0.5rem 1.15rem;
      cursor: pointer;
      letter-spacing: 0.01em;
    }
    .gi-compose-send-main:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }
    .gi-compose-send-caret {
      border: 0;
      border-left: 1px solid rgba(255, 255, 255, 0.35);
      background: linear-gradient(165deg, #0f5f16 0%, #0b4d12 100%);
      color: #ffffff;
      width: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
    }
    .gi-compose-send-caret svg { width: 14px; height: 14px; }
    .gi-compose-attach-link {
      flex: 1;
      min-width: 0;
      border: 0;
      background: transparent;
      color: #5f6368;
      font-size: 0.82rem;
      font-weight: 600;
      text-align: left;
      padding: 0.35rem 0.25rem;
      cursor: pointer;
    }
    .gi-compose-attach-link:hover { color: var(--gi-pale-text); }
    .gi-compose-discard {
      width: 34px;
      height: 34px;
      border: 0;
      border-radius: 999px;
      background: transparent;
      color: #5f6368;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex-shrink: 0;
      margin-left: 0.1rem;
    }
    .gi-compose-discard:hover { background: #f1f3f4; }
    .gi-compose-discard svg { width: 18px; height: 18px; }
    .gi-hidden-input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; clip-path: inset(50%); }

    @media (max-width: 420px) {
      .gi-compose-editor { min-height: 148px; font-size: 0.86rem; }
      .gi-compose-send-main { padding: 0.48rem 0.95rem; font-size: 0.82rem; }
    }
  </style>

  <header>
    <div class="nav">
      <a href="index.php#home" class="updates-topbar" aria-label="Back to home">
        <svg class="updates-topbar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"/>
            </svg>
        <p class="updates-topbar-title">Beanthentic GI Updates</p>
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
      <div class="gi-compose-card" role="group" aria-label="Compose GI update">
        <div class="gi-mail-from" aria-label="Sender">
          <div class="gi-mail-avatar" aria-hidden="true">J</div>
          <div class="gi-mail-from-main">
            <p class="gi-mail-from-name" id="gi-mail-sender-name">Juan Dela Cruz</p>
            <p class="gi-mail-from-sub">to Beanthentic Admin</p>
          </div>
          <div class="gi-mail-time" id="gi-mail-time">—</div>
        </div>

        <form id="gi-support-upload-form" method="post" enctype="multipart/form-data" action="">
          <input
            class="gi-hidden-input"
            id="gi-support-file"
            name="gi_support_file"
            type="file"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
            multiple
          />

          <textarea
            id="gi-compose-message"
            class="gi-compose-editor"
            name="gi_message"
            rows="6"
            placeholder="Write your GI update message here..."
            aria-label="GI update message"
          ></textarea>

          <div class="gi-compose-attachments" id="gi-attachments-grid" aria-label="Attachments"></div>
          <p id="updates-upload-status" class="updates-upload-status" hidden></p>

          <footer class="gi-compose-toolbar" aria-label="Compose actions">
            <div class="gi-compose-send-group">
              <button type="submit" class="gi-compose-send-main" id="gi-compose-send">Send</button>
              <button type="button" class="gi-compose-send-caret" id="gi-compose-send-menu" aria-label="More send options" title="More send options">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
            </div>
            <button type="button" class="gi-compose-attach-link" id="gi-tool-attach">Attach files</button>
            <button type="button" class="gi-compose-discard" id="gi-compose-discard" aria-label="Discard draft" title="Discard draft">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </button>
          </footer>
        </form>
      </div>
    </section>
  </main>

  <div id="gi-send-error-modal" class="gi-login-notice-modal" aria-hidden="true">
    <div class="gi-login-notice-card" role="alertdialog" aria-modal="true" aria-labelledby="gi-send-error-brand" aria-describedby="gi-send-error-text">
      <p id="gi-send-error-brand" class="gi-login-notice-brand">Beanthentic</p>
      <p id="gi-send-error-text" class="gi-login-notice-text">Type a message or attach at least one file before sending.</p>
      <div class="gi-login-notice-actions">
        <button type="button" class="gi-login-notice-ok" id="gi-send-error-ok">OK</button>
      </div>
    </div>
  </div>

  <div id="gi-attach-preview" class="gi-attach-preview" hidden aria-hidden="true">
    <div class="gi-attach-preview-backdrop" data-gi-preview-close></div>
    <div class="gi-attach-preview-panel" role="dialog" aria-modal="true" aria-labelledby="gi-attach-preview-title">
      <div class="gi-attach-preview-head">
        <p id="gi-attach-preview-title" class="gi-attach-preview-title">Attachment</p>
        <button type="button" class="gi-attach-preview-close" id="gi-attach-preview-close" aria-label="Close preview">×</button>
      </div>
      <div id="gi-attach-preview-body" class="gi-attach-preview-body"></div>
    </div>
  </div>

  <script>
    (function () {
      var fileInput = document.getElementById('gi-support-file');
      var uploadForm = document.getElementById('gi-support-upload-form');
      var attachTool = document.getElementById('gi-tool-attach');
      var grid = document.getElementById('gi-attachments-grid');
      var status = document.getElementById('updates-upload-status');
      var discardBtn = document.getElementById('gi-compose-discard');
      var sendBtn = document.getElementById('gi-compose-send');
      var messageEl = document.getElementById('gi-compose-message');
      var timeEl = document.getElementById('gi-mail-time');
      var senderEl = document.getElementById('gi-mail-sender-name');

      if (timeEl) {
        try {
          var now = new Date();
          timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        } catch (_t) {
          timeEl.textContent = '—';
        }
      }

      try {
        var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
        var u = raw ? JSON.parse(raw) : null;
        var email = (u && u.email) ? String(u.email).trim().toLowerCase() : '';
        var nameGuess = '';
        try {
          var rawProfileMap = localStorage.getItem('beanthentic_farmer_profile_map') || sessionStorage.getItem('beanthentic_farmer_profile_map');
          var profileMap = rawProfileMap ? JSON.parse(rawProfileMap) : null;
          var p = profileMap && email && profileMap[email] ? profileMap[email] : null;
          if (p && typeof p === 'object') {
            var full = String(p.name || '').trim();
            var first = String(p.first_name || '').trim();
            var last = String(p.last_name || '').trim();
            nameGuess = full || ((first || last) ? (first + ' ' + last).trim() : '');
          }
        } catch (_p1) {}
        if (!nameGuess) {
          try {
            var rawProfile = localStorage.getItem('beanthentic_farmer_profile') || sessionStorage.getItem('beanthentic_farmer_profile');
            var lp = rawProfile ? JSON.parse(rawProfile) : null;
            if (lp && typeof lp === 'object') {
              var full2 = String(lp.name || '').trim();
              var first2 = String(lp.first_name || '').trim();
              var last2 = String(lp.last_name || '').trim();
              nameGuess = full2 || ((first2 || last2) ? (first2 + ' ' + last2).trim() : '');
            }
          } catch (_p2) {}
        }
        if (!nameGuess) {
          nameGuess = (u && u.name && String(u.name).trim()) ? String(u.name).trim() : '';
        }
        if (senderEl) senderEl.textContent = nameGuess || 'Farmer';
        var avatarEl = document.querySelector('.gi-mail-avatar');
        if (avatarEl) {
          var initial = (nameGuess || 'F').trim().charAt(0).toUpperCase() || 'F';
          avatarEl.textContent = initial;
        }
      } catch (_u) {}

      var errorModal = document.getElementById('gi-send-error-modal');
      var errorText = document.getElementById('gi-send-error-text');
      var errorOk = document.getElementById('gi-send-error-ok');
      var previewModal = document.getElementById('gi-attach-preview');
      var previewBody = document.getElementById('gi-attach-preview-body');
      var previewTitle = document.getElementById('gi-attach-preview-title');
      var previewClose = document.getElementById('gi-attach-preview-close');
      var attachedFiles = [];
      var attachPreviewUrls = [];

      if (!fileInput || !uploadForm || !grid || !status) return;

      function showGiErrorPopup(message) {
        if (errorText) errorText.textContent = message || 'Something went wrong. Please try again.';
        if (errorModal) {
          errorModal.classList.add('is-open');
          errorModal.setAttribute('aria-hidden', 'false');
        }
      }

      function closeGiErrorPopup() {
        if (errorModal) {
          errorModal.classList.remove('is-open');
          errorModal.setAttribute('aria-hidden', 'true');
        }
      }

      if (errorOk) errorOk.addEventListener('click', closeGiErrorPopup);
      if (errorModal) {
        errorModal.addEventListener('click', function (event) {
          if (event.target === errorModal) closeGiErrorPopup();
        });
      }

      function revokeAttachUrls() {
        attachPreviewUrls.forEach(function (url) {
          try { URL.revokeObjectURL(url); } catch (_e) {}
        });
        attachPreviewUrls = [];
      }

      function closeFilePreview() {
        if (!previewModal) return;
        previewModal.hidden = true;
        previewModal.setAttribute('aria-hidden', 'true');
        if (previewBody) previewBody.innerHTML = '';
      }

      function openFilePreview(file, index) {
        if (!file || !previewModal || !previewBody) return;
        var url = attachPreviewUrls[index];
        if (!url) {
          try {
            url = URL.createObjectURL(file);
            attachPreviewUrls[index] = url;
          } catch (_mk) {
            return;
          }
        }
        var name = String(file.name || 'Attachment');
        var mime = String(file.type || '').toLowerCase();
        previewBody.innerHTML = '';
        if (previewTitle) previewTitle.textContent = name;

        if (mime.indexOf('image/') === 0) {
          var img = document.createElement('img');
          img.src = url;
          img.alt = name;
          previewBody.appendChild(img);
        } else if (mime === 'application/pdf') {
          var frame = document.createElement('iframe');
          frame.src = url;
          frame.title = name;
          previewBody.appendChild(frame);
        } else {
          var msg = document.createElement('p');
          msg.className = 'gi-attach-preview-fallback';
          msg.textContent = 'Preview is not available for this file type.';
          var link = document.createElement('a');
          link.className = 'gi-attach-preview-open';
          link.href = url;
          link.target = '_blank';
          link.rel = 'noopener';
          link.textContent = 'Open file';
          msg.appendChild(document.createElement('br'));
          msg.appendChild(link);
          previewBody.appendChild(msg);
        }

        previewModal.hidden = false;
        previewModal.setAttribute('aria-hidden', 'false');
      }

      if (previewClose) previewClose.addEventListener('click', closeFilePreview);
      if (previewModal) {
        previewModal.addEventListener('click', function (event) {
          if (event.target && event.target.getAttribute('data-gi-preview-close') !== null) {
            closeFilePreview();
          }
        });
      }

      function openPicker() {
        try { fileInput.value = ''; } catch (_rv) {}
        try {
          if (typeof fileInput.showPicker === 'function') {
            fileInput.showPicker();
            return;
          }
        } catch (_sp) {}
        try { fileInput.click(); } catch (_e) {}
      }

      function bindAttachTrigger(el) {
        if (!el) return;
        el.addEventListener('click', function () { openPicker(); });
      }
      bindAttachTrigger(attachTool);

      function clearDraft() {
        if (messageEl) messageEl.value = '';
        try { fileInput.value = ''; } catch (_rv) {}
        attachedFiles = [];
        revokeAttachUrls();
        closeFilePreview();
        while (grid.firstChild) grid.removeChild(grid.firstChild);
        status.hidden = true;
        status.className = 'updates-upload-status';
        status.textContent = '';
        status.onclick = null;
      }

      if (discardBtn) discardBtn.addEventListener('click', clearDraft);

      function giApiBase() {
        try {
          if (window.BeanthenticApiUrls && typeof window.BeanthenticApiUrls.resolveHttpApiBase === 'function') {
            return String(window.BeanthenticApiUrls.resolveHttpApiBase() || '').replace(/\/+$/, '');
          }
        } catch (_ab) {}
        try {
          return String(
            localStorage.getItem('beanthentic_api_base') ||
            sessionStorage.getItem('beanthentic_api_base') ||
            ''
          ).replace(/\/+$/, '');
        } catch (_st) {}
        return '';
      }

      function giUserId() {
        try {
          var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          var u = raw ? JSON.parse(raw) : null;
          var id = u && (u.user_id != null) ? parseInt(u.user_id, 10) : 0;
          return Number.isFinite(id) ? id : 0;
        } catch (_uid) {
          return 0;
        }
      }

      uploadForm.addEventListener('submit', function (event) {
        event.preventDefault();
        var text = messageEl ? String(messageEl.value || '').trim() : '';
        var files = attachedFiles.length
          ? attachedFiles.slice()
          : (function () {
              try { return Array.prototype.slice.call(fileInput.files || []); } catch (_e) { return []; }
            })();
        if (!text && !files.length) {
          status.hidden = true;
          showGiErrorPopup('Type a message or attach at least one file before sending.');
          return;
        }
        var base = giApiBase();
        var userId = giUserId();
        if (!base || userId <= 0) {
          showGiErrorPopup('Set Server URL in the app and sign in again before sending GI updates.');
          return;
        }
        var fd = new FormData();
        fd.append('user_id', String(userId));
        fd.append('message', text);
        files.forEach(function (file) {
          fd.append('gi_support_file', file, file.name || 'attachment');
        });
        if (sendBtn) sendBtn.disabled = true;
        status.hidden = false;
        status.className = 'updates-upload-status';
        status.textContent = 'Sending…';
        fetch(base + '/api/gi_updates.php', { method: 'POST', body: fd })
          .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })
          .then(function (out) {
            if (!out.res.ok || !out.data || !out.data.ok) {
              throw new Error((out.data && out.data.error) ? out.data.error : 'Send failed.');
            }
            status.className = 'updates-upload-status is-success';
            status.textContent = out.data.message || 'GI update sent to admin for review.';
            clearDraft();
          })
          .catch(function (err) {
            status.hidden = true;
            showGiErrorPopup(err && err.message ? err.message : 'Could not send GI update.');
          })
          .finally(function () {
            if (sendBtn) sendBtn.disabled = false;
          });
      });

      function renderAttachmentChips(files) {
        revokeAttachUrls();
        while (grid.firstChild) grid.removeChild(grid.firstChild);
        var list = files || [];
        attachedFiles = list.slice(0, 8);
        if (!attachedFiles.length) return;
        attachedFiles.forEach(function (file, index) {
          var url = '';
          try {
            url = URL.createObjectURL(file);
            attachPreviewUrls[index] = url;
          } catch (_url) {}
          var chip = document.createElement('button');
          chip.type = 'button';
          chip.className = 'gi-compose-attach-chip';
          chip.setAttribute('aria-label', 'View ' + String(file.name || 'attachment'));
          var label = document.createElement('span');
          label.textContent = String(file.name || 'Attachment');
          chip.appendChild(label);
          chip.addEventListener('click', function () {
            openFilePreview(file, index);
          });
          grid.appendChild(chip);
        });
      }

      function bindStatusView(files) {
        if (!files.length) {
          status.onclick = null;
          status.classList.remove('is-viewable');
          return;
        }
        status.classList.add('is-viewable');
        status.onclick = function () {
          openFilePreview(files[0], 0);
        };
      }

      fileInput.addEventListener('change', function () {
        var files = [];
        try { files = Array.prototype.slice.call(fileInput.files || []); } catch (_e) { files = []; }
        renderAttachmentChips(files);
        if (!files.length) {
          attachedFiles = [];
          status.hidden = true;
          status.className = 'updates-upload-status';
          status.textContent = '';
          status.onclick = null;
          return;
        }
        status.hidden = false;
        status.className = 'updates-upload-status is-success is-viewable';
        status.textContent = files.length + ' file(s) attached. Tap to view.';
        bindStatusView(files);
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

      function applyGiProgress(percent) {
        var progressPercent = clampPercent(percent);
        if (progressPercent === null) progressPercent = 0;
        percentEl.textContent = String(progressPercent);
        fillEl.style.width = String(progressPercent) + '%';
        analysisEl.textContent = generateAiAnalysis(progressPercent);
        try {
          localStorage.setItem('beanthentic_gi_progress_percent', String(progressPercent));
        } catch (_ls) {}
        var completeLabel = document.getElementById('gi-progress-complete-label');
        if (completeLabel) {
          completeLabel.hidden = progressPercent < 100;
        }
      }

      applyGiProgress(parseAdminProgress());

      (function loadGiProgressFromServer() {
        var base = '';
        try {
          if (window.BeanthenticApiUrls && typeof window.BeanthenticApiUrls.resolveHttpApiBase === 'function') {
            base = String(window.BeanthenticApiUrls.resolveHttpApiBase() || '').replace(/\/+$/, '');
          }
        } catch (_b) {}
        if (!base) {
          try {
            base = String(
              localStorage.getItem('beanthentic_api_base') ||
              sessionStorage.getItem('beanthentic_api_base') ||
              ''
            ).replace(/\/+$/, '');
          } catch (_s) {}
        }
        var userId = 0;
        try {
          var raw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
          var u = raw ? JSON.parse(raw) : null;
          userId = u && u.user_id != null ? parseInt(u.user_id, 10) : 0;
        } catch (_u) {}
        if (!base || userId <= 0) return;
        fetch(base + '/api/gi_updates.php?user_id=' + encodeURIComponent(String(userId)))
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data && data.ok && data.progress_percent != null) {
              applyGiProgress(data.progress_percent);
            }
          })
          .catch(function () {});
      })();
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
</body>
</html>

