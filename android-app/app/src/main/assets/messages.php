<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
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
    .msg-top-row { display:flex; align-items:center; justify-content:center; min-height: 42px; position: relative; padding-left: 0; }
    .msg-nav-back {
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
    .msg-nav-back svg { width: 18px; height: 18px; }
    .msg-nav-title {
      margin: 0;
      font-size: clamp(1.35rem, 4.4vw, 1.7rem);
      font-weight: 800;
      line-height: 1;
      letter-spacing: 0.02em;
    }

    .msg-main {
      max-width: none;
      margin: 0;
      padding: 0.65rem 0.9rem calc(68px + env(safe-area-inset-bottom));
      min-height: calc(100vh - 78px);
      position: relative;
    }
    .msg-topline { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem; }
    .msg-back { display:inline-flex; align-items:center; gap:0.28rem; color:#2f7a24; text-decoration:none; font-size:0.9rem; }
    .msg-back svg { width:16px; height:16px; }
    .msg-ellipsis { color:#111827; font-size:1.4rem; letter-spacing:0.08em; line-height:1; }
    .msg-title { margin: 0; font-size: 1.55rem; font-weight: 800; line-height:1.1; color:#0f172a; }
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
      word-break: break-word;
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
      <a class="msg-nav-back" href="index.php#home" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
      <h1 class="msg-nav-title">Messages</h1>
    </div>
  </header>

  <main class="msg-main">
    <h1 class="msg-title"><span>Beanthentic</span> Chat</h1>
    <div class="msg-status"><span class="msg-status-dot"></span>Active</div>

    <section id="msg-thread" class="msg-thread" aria-label="Conversation"></section>
  </main>

  <div class="msg-input-wrap">
    <div class="msg-input-inner">
      <input id="msg-input" class="msg-input" type="text" placeholder="Type a message..." aria-label="Type a message" />
      <button id="msg-send" class="msg-send" type="button" aria-label="Send message">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4 20-7z"/><path d="M22 2 11 13"/></svg>
      </button>
    </div>
  </div>
  <script>
    (function () {
      var inputEl = document.getElementById('msg-input');
      var sendBtn = document.getElementById('msg-send');
      var threadEl = document.getElementById('msg-thread');
      if (!inputEl || !sendBtn || !threadEl) return;

      function addBubble(text, role) {
        var bubble = document.createElement('div');
        bubble.className = 'bubble ' + role;
        bubble.textContent = text;
        threadEl.appendChild(bubble);
        bubble.scrollIntoView({ behavior: 'smooth', block: 'end' });
      }

      function botReplyFor(message) {
        var msg = String(message || '').toLowerCase();
        if (msg.indexOf('hello') !== -1 || msg.indexOf('hi') !== -1) {
          return 'Hello! How can I help you today?';
        }
        if (msg.indexOf('option') !== -1) {
          return 'You can ask about coffee history, GI updates, or farm registration.';
        }
        if (msg.indexOf('history') !== -1) {
          return 'Try opening History from the bottom navigation to explore coffee varieties.';
        }
        return 'Thanks for your message. I received it.';
      }

      function sendMessage() {
        var text = inputEl.value.trim();
        if (!text) return;
        addBubble(text, 'user');
        inputEl.value = '';

        window.setTimeout(function () {
          addBubble(botReplyFor(text), 'bot');
        }, 350);
      }

      sendBtn.addEventListener('click', sendMessage);
      inputEl.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          sendMessage();
        }
      });
    })();
  </script>
</body>
</html>
