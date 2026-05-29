<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <script src="js/beanthentic_api_urls.js"></script>
  <script src="js/beanthentic_datetime.js"></script>
  <script src="js/beanthentic_message_badge.js"></script>
  <title>Messages · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    :root {
      --msg-green: #1a6b24;
      --msg-green-dark: #0f4a15;
      --msg-green-light: #e8f5e9;
      --msg-surface: #f4f7f5;
      --msg-bubble-in: #ffffff;
      --msg-text: #1e293b;
    }

    * { box-sizing: border-box; }

    body.msg-page {
      margin: 0;
      min-height: 100dvh;
      display: flex;
      flex-direction: column;
      background: var(--msg-surface);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    .msg-top {
      flex-shrink: 0;
      background: linear-gradient(165deg, #1c6f20 0%, var(--msg-green-dark) 100%);
      border-radius: 0 0 18px 18px;
      padding: 1rem 0.85rem 0.95rem;
      color: #fff;
      box-shadow: 0 4px 14px rgba(15, 74, 21, 0.2);
    }
    .msg-top-row {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      position: relative;
    }
    .msg-nav-back {
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 12px;
      text-decoration: none;
      -webkit-tap-highlight-color: transparent;
    }
    .msg-nav-back:active { background: rgba(255, 255, 255, 0.12); }
    .msg-nav-back svg { width: 20px; height: 20px; }
    .msg-nav-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 800;
      letter-spacing: 0.02em;
    }

    .msg-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: 520px;
      margin: 0 auto;
      min-height: 0;
      padding-bottom: calc(72px + env(safe-area-inset-bottom));
    }

    .msg-chat-head {
      flex-shrink: 0;
      margin: 0.75rem 0.85rem 0;
      padding: 0.75rem 0.9rem;
      background: #fff;
      border-radius: 14px;
      border: 1px solid rgba(26, 107, 36, 0.1);
      box-shadow: 0 2px 8px rgba(15, 74, 21, 0.06);
    }
    .msg-title {
      margin: 0;
      font-size: 1.15rem;
      font-weight: 800;
      line-height: 1.2;
      color: var(--msg-text);
    }
    .msg-title span { color: var(--msg-green); }
    .msg-status {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.35rem;
      padding: 0.2rem 0.55rem 0.2rem 0.35rem;
      border-radius: 999px;
      background: var(--msg-green-light);
      color: var(--msg-green);
      font-size: 0.75rem;
      font-weight: 600;
    }
    .msg-status-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
      animation: msg-pulse 2s ease-in-out infinite;
    }
    @keyframes msg-pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.55; }
    }

    .msg-chat-panel {
      flex: 1;
      min-height: 0;
      margin: 0.65rem 0.65rem 0;
      padding: 0.85rem 0.75rem;
      background: #fff;
      border-radius: 16px 16px 0 0;
      border: 1px solid rgba(26, 107, 36, 0.08);
      border-bottom: none;
      box-shadow: 0 -2px 12px rgba(15, 74, 21, 0.04);
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }

    .msg-thread {
      display: flex;
      flex-direction: column;
      gap: 0.55rem;
      width: 100%;
    }

    .bubble-wrap {
      display: flex;
      flex-direction: column;
      max-width: 85%;
      gap: 0.28rem;
    }
    .bubble-wrap--user {
      align-self: flex-end;
      align-items: flex-end;
    }
    .bubble-wrap--bot {
      align-self: flex-start;
      align-items: flex-start;
    }

    .bubble-label {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: #64748b;
      padding: 0 0.15rem;
    }
    .bubble-label::before {
      content: "";
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #94a3b8;
    }

    .bubble {
      display: block;
      width: max-content;
      max-width: min(280px, 100%);
      min-width: 2.75rem;
      padding: 0.65rem 0.95rem;
      font-size: 0.95rem;
      line-height: 1.45;
      letter-spacing: 0.01em;
      overflow-wrap: break-word;
      word-break: normal;
      white-space: pre-wrap;
      -webkit-font-smoothing: antialiased;
      transform: translateZ(0);
    }

    .bubble.bot {
      background: var(--msg-bubble-in);
      color: var(--msg-text);
      border: 1px solid #e2e8f0;
      border-radius: 16px 16px 16px 4px;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }
    .bubble.user {
      background: var(--msg-green);
      color: #fff;
      border-radius: 16px 16px 4px 16px;
      box-shadow: 0 2px 8px rgba(15, 74, 21, 0.22);
    }

    .bubble-time {
      font-size: 0.62rem;
      color: #94a3b8;
      padding: 0.15rem 0.1rem 0;
      line-height: 1.25;
      max-width: 100%;
      white-space: normal;
    }
    .bubble-wrap--user .bubble-time { text-align: right; }

    .msg-thread--empty .bubble.bot {
      max-width: 100%;
      text-align: center;
      color: #64748b;
      font-size: 0.88rem;
      border-style: dashed;
      background: var(--msg-surface);
    }

    .msg-input-wrap {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 20;
      padding: 0.5rem 0.65rem calc(0.55rem + env(safe-area-inset-bottom));
      background: linear-gradient(to top, #fff 85%, rgba(255, 255, 255, 0.97));
      border-top: 1px solid #e2e8f0;
      box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
    }
    .msg-input-inner {
      max-width: 520px;
      margin: 0 auto;
      display: flex;
      gap: 0.5rem;
      align-items: flex-end;
    }
    .msg-input {
      flex: 1;
      border: 1px solid #cbd5e1;
      border-radius: 22px;
      min-height: 44px;
      max-height: 120px;
      padding: 0.6rem 1rem;
      font-size: 0.95rem;
      color: var(--msg-text);
      background: #f8fafc;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .msg-input:focus {
      border-color: var(--msg-green);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(26, 107, 36, 0.12);
    }
    .msg-send {
      flex-shrink: 0;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      border: none;
      background: linear-gradient(145deg, #1d7a2a, var(--msg-green-dark));
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 10px rgba(15, 74, 21, 0.25);
      -webkit-tap-highlight-color: transparent;
    }
    .msg-send:active { transform: scale(0.96); }
    .msg-send svg { width: 18px; height: 18px; }
    .msg-send:disabled { opacity: 0.5; transform: none; }
  </style>
</head>
<body class="msg-page">
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
    <div class="msg-chat-head">
      <h1 class="msg-title"><span>Beanthentic</span> Chat</h1>
      <div class="msg-status"><span class="msg-status-dot" aria-hidden="true"></span>Active</div>
    </div>

    <div class="msg-chat-panel" id="msg-chat-panel">
      <section id="msg-thread" class="msg-thread" aria-label="Conversation"></section>
    </div>
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

      var cachedItems = [];
      var myPhone = '';
      var loading = false;
      var sending = false;

      function parseUser(raw) {
        if (!raw) return null;
        try { return JSON.parse(raw); } catch (_e) { return null; }
      }

      function normPhone(p) {
        var d = String(p || '').replace(/\D/g, '');
        if (!d) return '';
        if (d.charAt(0) === '0') d = d.slice(1);
        if (d.indexOf('63') === 0 && d.length >= 12) d = d.slice(2);
        if (d.length === 10 && d.charAt(0) === '9') return '+63' + d;
        return String(p || '').trim();
      }

      function apiBase() {
        if (window.BeanthenticApiUrls && typeof window.BeanthenticApiUrls.resolveApiBase === 'function') {
          var b = window.BeanthenticApiUrls.resolveApiBase();
          if (b) return String(b).replace(/\/+$/, '');
        }
        try {
          var s = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
          if (s && String(s).trim()) return String(s).trim().replace(/\/+$/, '');
        } catch (_e) {}
        try {
          if (location && (location.protocol === 'http:' || location.protocol === 'https:')) {
            return String(new URL('.', location.href).href).replace(/\/+$/, '');
          }
        } catch (_e2) {}
        return '';
      }

      function currentUser() {
        var u = null;
        try {
          u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
        } catch (_e3) {}
        return u && u.user_id ? u : null;
      }

      function messageSenderRole(m) {
        return String((m && m.sender_role) || '').toLowerCase();
      }

      /** Farmer app: my messages right (user), admin messages left (bot). */
      function isFromMe(m) {
        if (!m) return false;
        var role = messageSenderRole(m);
        if (role === 'admin') return false;
        if (role === 'farmer') return true;
        return false;
      }

      function mergeItems(incoming) {
        var map = {};
        var out = [];
        function push(item) {
          if (!item) return;
          var id = item.id != null ? String(item.id) : '';
          var key = id || ('t:' + String(item.created_at || '') + ':' + String(item.body || '').slice(0, 80));
          if (map[key]) return;
          map[key] = true;
          out.push(item);
        }
        (cachedItems || []).forEach(push);
        (incoming || []).forEach(push);
        out.sort(function (a, b) {
          var ta = String(a.created_at || '');
          var tb = String(b.created_at || '');
          if (ta === tb) return (Number(a.id) || 0) - (Number(b.id) || 0);
          return ta < tb ? -1 : ta > tb ? 1 : 0;
        });
        cachedItems = out;
        return out;
      }

      function formatBubbleTime(iso) {
        var DT = window.BeanthenticDateTime;
        if (DT && typeof DT.formatChatBubbleTime === 'function') {
          var short = DT.formatChatBubbleTime(iso);
          if (short) return short;
        }
        if (!DT || typeof DT.formatHomeDateTime !== 'function') return '';
        return DT.formatHomeDateTime(iso);
      }

      function deviceSqlDateTime() {
        var DT = window.BeanthenticDateTime;
        if (DT && typeof DT.deviceSqlDateTime === 'function') {
          return DT.deviceSqlDateTime();
        }
        return '';
      }

      function scrollChatToEnd() {
        var panel = document.getElementById('msg-chat-panel');
        if (panel) {
          panel.scrollTop = panel.scrollHeight;
          return;
        }
        var last = threadEl.lastElementChild;
        if (last) last.scrollIntoView({ behavior: 'smooth', block: 'end' });
      }

      function renderThread(items) {
        var list = items || cachedItems || [];
        threadEl.innerHTML = '';
        threadEl.classList.toggle('msg-thread--empty', !list.length);
        if (!list.length) {
          addBubble('Start a conversation with the admin.', 'bot', '', null);
          scrollChatToEnd();
          return;
        }
        for (var i = 0; i < list.length; i += 1) {
          var m = list[i] || {};
          var mine = isFromMe(m);
          addBubble(
            String(m.body || ''),
            mine ? 'user' : 'bot',
            mine ? '' : 'Admin',
            m.created_at || null
          );
        }
        scrollChatToEnd();
      }

      function addBubble(text, role, label, createdAt) {
        var wrap = document.createElement('div');
        wrap.className = 'bubble-wrap ' + (role === 'user' ? 'bubble-wrap--user' : 'bubble-wrap--bot');
        if (label) {
          var cap = document.createElement('div');
          cap.className = 'bubble-label';
          cap.textContent = label;
          wrap.appendChild(cap);
        }
        var bubble = document.createElement('div');
        bubble.className = 'bubble ' + role;
        bubble.textContent = text;
        wrap.appendChild(bubble);
        var timeStr = formatBubbleTime(createdAt);
        if (!timeStr && window.BeanthenticDateTime && typeof window.BeanthenticDateTime.formatNow === 'function') {
          timeStr = window.BeanthenticDateTime.formatNow();
        }
        if (!timeStr) {
          timeStr = formatBubbleTime(deviceSqlDateTime());
        }
        var timeEl = document.createElement('div');
        timeEl.className = 'bubble-time';
        timeEl.textContent = timeStr || '';
        wrap.appendChild(timeEl);
        threadEl.appendChild(wrap);
      }

      function loadThread(silent) {
        if (loading) return;
        var base = apiBase();
        var u = currentUser();
        if (!base || !u) {
          if (!silent) addBubble('Missing server URL or session. Login again.', 'bot', '', null);
          return;
        }
        loading = true;
        fetch(base + '/api/chat_thread.php?user_id=' + encodeURIComponent(String(u.user_id)), { method: 'GET' })
          .then(function (r) { return r.json(); })
          .then(function (j) {
            if (!j || j.ok !== true) throw new Error(j && j.error ? j.error : 'load failed');
            if (j.phone) myPhone = j.phone;
            var merged = mergeItems(j.items || []);
            renderThread(merged);
            if (window.BeanthenticMessageBadge && typeof window.BeanthenticMessageBadge.clear === 'function') {
              window.BeanthenticMessageBadge.clear();
            }
          })
          .catch(function (err) {
            if (!silent) {
              threadEl.innerHTML = '';
              addBubble('Cannot reach server. Check Wi‑Fi / Server URL.', 'bot', '', null);
            } else if (cachedItems.length) {
              renderThread(cachedItems);
            }
          })
          .finally(function () {
            loading = false;
          });
      }

      function sendMessage() {
        var text = inputEl.value.trim();
        if (!text || sending) return;
        var base = apiBase();
        var u = currentUser();
        if (!base || !u) {
          addBubble('Missing server URL or session. Login again.', 'bot', '', null);
          return;
        }

        sending = true;
        sendBtn.disabled = true;
        inputEl.value = '';

        var optimistic = {
          id: 'local-' + Date.now(),
          sender_role: 'farmer',
          sender_phone: myPhone,
          body: text,
          created_at: deviceSqlDateTime(),
        };
        cachedItems = mergeItems(cachedItems.concat([optimistic]));
        renderThread(cachedItems);

        fetch(base + '/api/chat_thread.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: u.user_id,
            text: text,
            client_created_at: deviceSqlDateTime(),
          }),
        })
          .then(function (r) { return r.json(); })
          .then(function (j) {
            if (!j || j.ok !== true) throw new Error(j && j.error ? j.error : 'send failed');
            if (j.phone) myPhone = j.phone;
            if (j.message) {
              cachedItems = cachedItems.filter(function (m) { return m.id !== optimistic.id; });
              mergeItems([j.message]);
              renderThread(cachedItems);
            }
            window.setTimeout(function () { loadThread(true); }, 400);
          })
          .catch(function () {
            cachedItems = cachedItems.filter(function (m) { return m.id !== optimistic.id; });
            renderThread(cachedItems);
            addBubble('Send failed. Check connection.', 'bot', '', null);
          })
          .finally(function () {
            sending = false;
            sendBtn.disabled = false;
          });
      }

      sendBtn.addEventListener('click', sendMessage);
      inputEl.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          sendMessage();
        }
      });

      loadThread(false);
      window.setInterval(function () {
        if (!sending) loadThread(true);
      }, 8000);
    })();
  </script>
</body>
</html>
