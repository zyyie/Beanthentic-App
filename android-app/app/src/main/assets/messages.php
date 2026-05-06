<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <title>Messages · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    body { margin: 0; background: #fff; }
    .msg-top {
      background: linear-gradient(160deg, #1c6f20 0%, #0f4a15 100%);
      border-radius: 0 0 16px 16px;
      padding: 1.15rem 0.8rem 1.02rem;
      color: #fff;
    }
    .msg-top-row { display:flex; align-items:center; justify-content:space-between; }
    .msg-brand { display:flex; align-items:center; gap:0.55rem; }
    .msg-logo {
      width: 34px; height: 34px; border-radius: 999px; background:#fff; padding:4px; object-fit:contain;
    }
    .msg-name { font-size: 1rem; font-weight: 700; line-height: 1.1; }
    .msg-actions { display:flex; gap:0.34rem; }
    .msg-icon-btn {
      width: 28px; height: 28px; border-radius:999px; background:#fff; color:#1f7a2e;
      border:none; display:inline-flex; align-items:center; justify-content:center;
    }
    .msg-icon-btn svg { width: 15px; height: 15px; }

    .msg-main {
      max-width: 430px;
      margin: 0 auto;
      padding: 0.65rem 0.55rem calc(68px + env(safe-area-inset-bottom));
      min-height: calc(100vh - 78px);
      position: relative;
    }
    .msg-topline { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem; }
    .msg-back { display:inline-flex; align-items:center; gap:0.28rem; color:#2f7a24; text-decoration:none; font-size:0.9rem; }
    .msg-back svg { width:16px; height:16px; }
    .msg-ellipsis { color:#111827; font-size:1.4rem; letter-spacing:0.08em; line-height:1; }
    .msg-title { margin: 0; font-size: 2rem; font-weight: 800; line-height:1.1; color:#0f172a; }
    .msg-title span { color:#1f7a2e; }
    .msg-status { display:flex; align-items:center; gap:0.35rem; color:#58a86f; font-size:0.82rem; margin-top:0.2rem; margin-bottom:0.9rem; }
    .msg-status-dot { width:6px; height:6px; border-radius:999px; background:#58a86f; display:inline-block; }

    .msg-thread { display:flex; flex-direction:column; gap:0.72rem; }
    .bubble {
      max-width: 78%;
      border-radius: 18px;
      padding: 0.62rem 0.9rem;
      font-size: 1rem;
      line-height: 1.25;
    }
    .bubble.bot { background:#f4f9fd; color:#1f7a2e; align-self:flex-start; }
    .bubble.user {
      background: linear-gradient(165deg, #0f4a15 0%, #1d7a2a 100%);
      color:#fff; align-self:flex-end;
    }

    .msg-input-wrap {
      position: fixed;
      left: 0; right: 0; bottom: 0;
      padding: 0.42rem 0.55rem calc(0.48rem + env(safe-area-inset-bottom));
      background: #fff;
    }
    .msg-input-inner {
      max-width: 430px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 0.45rem;
      align-items: center;
    }
    .msg-input {
      border: 1px solid #cfd8dc;
      border-radius: 999px;
      min-height: 38px;
      padding: 0.55rem 0.9rem;
      font-size: 0.92rem;
      color:#111827;
      background: #fff;
    }
    .msg-send {
      width: 34px; height: 34px; border-radius:999px;
      border: none; background: #1f7a2e; color:#fff;
      display:inline-flex; align-items:center; justify-content:center;
    }
    .msg-send svg { width: 16px; height: 16px; }
  </style>
</head>
<body>
  <header class="msg-top">
    <div class="msg-top-row">
      <div class="msg-brand">
        <img class="msg-logo" src="beanthentic_logo.png" alt="Beanthentic" />
        <div class="msg-name">Beanthentic</div>
      </div>
      <div class="msg-actions">
        <button type="button" class="msg-icon-btn" aria-label="Notifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </button>
        <a href="account.php" class="msg-icon-btn" aria-label="Account">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/></svg>
        </a>
      </div>
    </div>
  </header>

  <main class="msg-main">
    <div class="msg-topline">
      <a href="index.php#home" class="msg-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back
      </a>
      <div class="msg-ellipsis" aria-hidden="true">···</div>
    </div>

    <h1 class="msg-title"><span>Beanthentic</span> Chats</h1>
    <div class="msg-status"><span class="msg-status-dot"></span>Active</div>

    <section class="msg-thread" aria-label="Conversation">
      <div class="bubble user">Show me other options</div>
      <div class="bubble bot">Hello!</div>
    </section>
  </main>

  <div class="msg-input-wrap">
    <div class="msg-input-inner">
      <input class="msg-input" type="text" placeholder="Type a message..." aria-label="Type a message" />
      <button class="msg-send" type="button" aria-label="Send message">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4 20-7z"/><path d="M22 2 11 13"/></svg>
      </button>
    </div>
  </div>
</body>
</html>
