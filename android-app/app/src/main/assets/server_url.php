<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#143d22" />
  <script src="js/beanthentic_theme.js?v=20260527-9"></script>
  <title>Server URL · Beanthentic</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/components.css" />
  <link rel="stylesheet" href="css/responsive.css" />
  <style>
    .server-url-page { background: #e8f0ea; min-height: 100dvh; }
    .server-url-wrap { max-width: 32rem; margin: 0 auto; padding: 1rem 0.85rem 2.5rem; box-sizing: border-box; }
    .server-url-card {
      background: #fff;
      border-radius: 16px;
      padding: 1.1rem 1rem 1.2rem;
      box-shadow: 0 10px 28px rgba(15, 77, 18, 0.12);
      border: 1px solid rgba(20, 94, 30, 0.12);
    }
    .server-url-card h1 {
      margin: 0 0 0.35rem;
      font-size: 1.35rem;
      font-weight: 800;
      color: #0f3d14;
      letter-spacing: -0.02em;
    }
    .server-url-card .lead {
      margin: 0 0 1rem;
      font-size: 0.88rem;
      line-height: 1.45;
      color: #374151;
    }
    .server-url-card label {
      display: block;
      font-size: 0.78rem;
      font-weight: 700;
      color: #1f2937;
      margin: 0.65rem 0 0.28rem;
    }
    .server-url-card input[type="text"] {
      width: 100%;
      box-sizing: border-box;
      padding: 0.55rem 0.65rem;
      border-radius: 10px;
      border: 1px solid rgba(17, 24, 39, 0.18);
      font-size: 0.88rem;
    }
    .server-url-card .hint {
      font-size: 0.72rem;
      color: #64748b;
      margin: 0.25rem 0 0;
      line-height: 1.35;
    }
    .server-url-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.45rem;
      margin-top: 1rem;
    }
    .server-url-actions button {
      flex: 1 1 auto;
      min-width: 6.5rem;
      padding: 0.55rem 0.65rem;
      border-radius: 10px;
      border: 0;
      font-weight: 700;
      font-size: 0.82rem;
      cursor: pointer;
    }
    .server-url-actions .btn-save { background: #145e1e; color: #fff; }
    .server-url-actions .btn-secondary { background: #e5e7eb; color: #111827; }
    .server-url-actions .btn-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .server-url-status {
      margin-top: 0.85rem;
      padding: 0.65rem 0.7rem;
      border-radius: 10px;
      font-size: 0.8rem;
      line-height: 1.4;
      display: none;
    }
    .server-url-status.is-on { display: block; }
    .server-url-status.ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .server-url-status.err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .server-url-back {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      margin-bottom: 0.75rem;
      color: #145e1e;
      font-weight: 700;
      font-size: 0.88rem;
      text-decoration: none;
    }
  </style>
  <script>
    /* Filled when this HTML is served by app.py (Flask); stays empty on Apache/XAMPP. */
    window.__BEANTHENTIC_INJECTED_ORIGIN__ = "";
  </script>
</head>
<body class="server-url-page">
  <div class="server-url-wrap">
    <a class="server-url-back" href="login.php">← Back to sign in</a>
    <div class="server-url-card">
      <h1>Server URL</h1>
      <p class="lead">
        Kapag nagpalit ng Wi‑Fi o IP (hal. <code>http://192.168.x.x:8080/...</code>), ilagay dito ang tamang base URL.
        Ise-save sa app ang <strong>beanthentic_api_base</strong> (XAMPP / MySQL API) at opsyonal na <strong>Flask</strong> para sa maps/register farm module.
      </p>

      <label for="api-base">PHP assets base (dapat may folder na <code>api/</code>)</label>
      <input id="api-base" type="text" placeholder="http://192.168.0.100:8080/.../android-app/app/src/main/assets" autocomplete="off" />
      <p class="hint">Halimbawa: hanggang <code>.../main/assets</code> (walang slash sa dulo). Hindi dapat tumatapos sa <code>/api</code> — awtomatikong aayusin.</p>

      <label for="flask-base">Flask dev server (opsyonal)</label>
      <input id="flask-base" type="text" placeholder="http://192.168.0.100:8080" autocomplete="off" />
      <p class="hint">Para sa <code>/maps</code> at <code>/register-farm</code> kapag tumatakbo ang <code>app.py</code> sa PC.</p>

      <label for="client-web-base">Client Web (QR code — port 5001)</label>
      <input id="client-web-base" type="text" placeholder="http://192.168.0.100:5001" autocomplete="off" />
      <p class="hint">Dapat <strong>LAN IP</strong> ng PC (hindi <code>127.0.0.1</code>) para gumana ang QR sa phone. Hal.: <code>http://192.168.100.249:5001</code></p>

      <div class="server-url-actions">
        <button type="button" class="btn-secondary" id="btn-from-page">Kunin sa page na ito</button>
        <button type="button" class="btn-secondary" id="btn-flask-inject" hidden>Flask host (dev)</button>
        <button type="button" class="btn-secondary" id="btn-test">Subukan ang API</button>
        <button type="button" class="btn-save" id="btn-save">I-save</button>
        <button type="button" class="btn-danger" id="btn-clear">Burahin</button>
      </div>
      <div id="status" class="server-url-status" role="status" aria-live="polite"></div>
    </div>
  </div>
  <script>
    (function () {
      var DEVICE_HOST_IP = '192.168.0.100';
      var DEFAULT_API_BASE = 'http://' + DEVICE_HOST_IP + ':8080';
      var DEFAULT_CLIENT_WEB = 'http://' + DEVICE_HOST_IP + ':5001';
      var apiEl = document.getElementById('api-base');
      var flaskEl = document.getElementById('flask-base');
      var clientWebEl = document.getElementById('client-web-base');
      var statusEl = document.getElementById('status');

      function showStatus(msg, ok) {
        statusEl.textContent = msg;
        statusEl.className = 'server-url-status is-on ' + (ok ? 'ok' : 'err');
      }
      function hideStatus() {
        statusEl.className = 'server-url-status';
        statusEl.textContent = '';
      }

      function normalizeApiBase(raw) {
        var s = String(raw || '').trim().replace(/\/+$/, '');
        if (!s) return '';
        if (/^https?:\/\//i.test(s) === false) return '';
        if (/\/api$/i.test(s)) s = s.replace(/\/api$/i, '');
        return s;
      }

      function normalizeFlaskBase(raw) {
        var s = String(raw || '').trim().replace(/\/+$/, '');
        if (!s) return '';
        if (/^https?:\/\//i.test(s) === false) return '';
        return s;
      }

      function isLoopbackUrl(raw) {
        try {
          var h = new URL(String(raw || '').trim()).hostname.toLowerCase();
          return h === 'localhost' || h === '127.0.0.1';
        } catch (_e) {
          return false;
        }
      }

      function defaultClientWebFromPage() {
        try {
          var u = new URL(location.href);
          if (u.hostname === 'localhost' || u.hostname === '127.0.0.1') return '';
          return u.protocol + '//' + u.hostname + ':5001';
        } catch (_e2) {
          return '';
        }
      }

      function defaultFromCurrentPage() {
        try {
          var u = new URL(location.href);
          var path = u.pathname.replace(/[^/]*$/, '');
          var base = u.origin + path.replace(/\/+$/, '');
          return base.replace(/\/+$/, '');
        } catch (e) {
          return '';
        }
      }

      function loadFields() {
        try {
          apiEl.value =
            localStorage.getItem('beanthentic_api_base') ||
            sessionStorage.getItem('beanthentic_api_base') ||
            defaultFromCurrentPage() ||
            DEFAULT_API_BASE;
        } catch (e) {
          apiEl.value = defaultFromCurrentPage() || DEFAULT_API_BASE;
        }
        try {
          flaskEl.value =
            localStorage.getItem('beanthentic_flask_base') ||
            sessionStorage.getItem('beanthentic_flask_base') ||
            DEFAULT_API_BASE;
        } catch (e2) {
          flaskEl.value = DEFAULT_API_BASE;
        }
        try {
          var cw =
            localStorage.getItem('beanthentic_client_web_base') ||
            sessionStorage.getItem('beanthentic_client_web_base') ||
            defaultClientWebFromPage() ||
            DEFAULT_CLIENT_WEB;
          if (clientWebEl) clientWebEl.value = isLoopbackUrl(cw) ? (defaultClientWebFromPage() || DEFAULT_CLIENT_WEB) : cw;
        } catch (e3) {
          if (clientWebEl) clientWebEl.value = defaultClientWebFromPage() || DEFAULT_CLIENT_WEB;
        }
      }

      var injected = '';
      try {
        injected = String(window.__BEANTHENTIC_INJECTED_ORIGIN__ || '').trim();
      } catch (_i) {}
      var flaskInjectBtn = document.getElementById('btn-flask-inject');
      if (injected && flaskInjectBtn) {
        flaskInjectBtn.hidden = false;
        flaskInjectBtn.addEventListener('click', function () {
          flaskEl.value = injected;
          showStatus('Na-set ang Flask base mula sa app na ito (' + injected + ').', true);
        });
      }

      document.getElementById('btn-from-page').addEventListener('click', function () {
        var d = defaultFromCurrentPage();
        var cw = defaultClientWebFromPage();
        if (d) {
          apiEl.value = d;
          if (clientWebEl && cw) clientWebEl.value = cw;
          showStatus('Na-fill ang PHP base' + (cw ? ' at Client Web (QR)' : '') + ' mula sa kasalukuyang URL.', true);
        } else {
          showStatus('Hindi makuha ang URL (file:// o unsupported).', false);
        }
      });

      document.getElementById('btn-save').addEventListener('click', function () {
        hideStatus();
        var api = normalizeApiBase(apiEl.value);
        var fb = normalizeFlaskBase(flaskEl.value);
        var cw = normalizeFlaskBase(clientWebEl ? clientWebEl.value : '');
        if (!api) {
          showStatus('Invalid ang PHP base: dapat full http:// o https:// URL (folder ng assets, may api/).', false);
          return;
        }
        if (cw && isLoopbackUrl(cw)) {
          showStatus('Client Web URL: huwag gumamit ng 127.0.0.1 — ilagay ang LAN IP ng PC (hal. http://192.168.100.249:5001).', false);
          return;
        }
        try {
          localStorage.setItem('beanthentic_api_base', api);
          sessionStorage.setItem('beanthentic_api_base', api);
        } catch (e) {
          showStatus('Hindi ma-save (storage blocked).', false);
          return;
        }
        try {
          if (fb) {
            localStorage.setItem('beanthentic_flask_base', fb);
            sessionStorage.setItem('beanthentic_flask_base', fb);
          } else {
            localStorage.removeItem('beanthentic_flask_base');
            sessionStorage.removeItem('beanthentic_flask_base');
          }
        } catch (e2) {}
        try {
          var cwSave = cw || defaultClientWebFromPage();
          if (cwSave && !isLoopbackUrl(cwSave)) {
            localStorage.setItem('beanthentic_client_web_base', cwSave);
            sessionStorage.setItem('beanthentic_client_web_base', cwSave);
          }
        } catch (e4) {}
        showStatus('Na-save. I-refresh ang Account page para makita ang bagong QR URL.', true);
      });

      document.getElementById('btn-clear').addEventListener('click', function () {
        try {
          localStorage.removeItem('beanthentic_api_base');
          sessionStorage.removeItem('beanthentic_api_base');
          localStorage.removeItem('beanthentic_flask_base');
          sessionStorage.removeItem('beanthentic_flask_base');
          localStorage.removeItem('beanthentic_client_web_base');
          sessionStorage.removeItem('beanthentic_client_web_base');
        } catch (e) {}
        apiEl.value = '';
        flaskEl.value = '';
        if (clientWebEl) clientWebEl.value = '';
        showStatus('Na-clear ang naka-save na server URLs.', true);
      });

      document.getElementById('btn-test').addEventListener('click', function () {
        hideStatus();
        var api = normalizeApiBase(apiEl.value || defaultFromCurrentPage());
        if (!api) {
          showStatus('Walang valid na PHP base para subukan.', false);
          return;
        }
        var url = api + '/api/login.php';
        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({}),
        })
          .then(function (r) {
            return r.text().then(function (t) {
              var j = null;
              try {
                j = t ? JSON.parse(t) : null;
              } catch (p) {
                j = null;
              }
              return { ok: r.ok, j: j };
            });
          })
          .then(function (o) {
            if (o.j && typeof o.j === 'object' && Object.prototype.hasOwnProperty.call(o.j, 'ok')) {
              showStatus('OK — nakakausap ang PHP API sa:\n' + url, true);
            } else {
              showStatus(
                'Hindi JSON ang sagot (mali ang path o hindi PHP ang tumatakbo). Subukan ang "Kunin sa page na ito" o itama ang URL.',
                false
              );
            }
          })
          .catch(function () {
            showStatus('Hindi maabot ang ' + url + ' (network / CORS / maling host).', false);
          });
      });

      loadFields();
    })();
  </script>
</body>
</html>
