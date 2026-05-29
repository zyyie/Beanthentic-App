<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>Records · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .records-page header {
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 14px 14px;
      padding-top: env(safe-area-inset-top);
    }
    .records-page .nav {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 4.6rem;
      padding: 0.72rem max(1.05rem, env(safe-area-inset-left, 0px)) 0.78rem
        max(1.05rem, env(safe-area-inset-right, 0px));
    }
    .records-header-back {
      position: absolute;
      left: max(1.05rem, env(safe-area-inset-left, 0px));
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      line-height: 0;
    }
    .records-header-back svg {
      width: 24px;
      height: 24px;
    }
    .records-header-brand {
      margin: 0;
      color: #ffffff;
      font-size: 2rem;
      line-height: 1;
      font-weight: 800;
      letter-spacing: -0.01em;
      text-align: center;
      white-space: nowrap;
    }
    .records-shell {
      width: min(100%, 680px);
      margin: 0 auto;
      padding: 1.35rem 1.15rem 8.4rem;
      box-sizing: border-box;
    }
    .records-list {
      display: flex;
      flex-direction: column;
      gap: 0.95rem;
    }
    .records-empty {
      margin: 2rem 0 0;
      text-align: center;
      color: #6b7280;
      font-size: 0.95rem;
      line-height: 1.45;
    }
    .record-card {
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #ffffff;
      padding: 1rem 1rem 1.05rem;
      box-shadow: 0 1px 2px rgba(17, 24, 39, 0.04);
    }
    .record-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      margin-bottom: 0.85rem;
    }
    .record-card-name {
      margin: 0;
      font-size: 1.08rem;
      line-height: 1.25;
      font-weight: 800;
      color: #111827;
    }
    .record-badge {
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 1.55rem;
      padding: 0.15rem 0.62rem;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.01em;
    }
    .record-badge--pending {
      background: #fff3cd;
      color: #b45309;
    }
    .record-badge--approved {
      background: #dcfce7;
      color: #166534;
    }
    .record-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: 0.72rem 1rem;
      margin-bottom: 0.72rem;
    }
    .record-field {
      min-width: 0;
    }
    .record-label {
      display: block;
      margin-bottom: 0.18rem;
      font-size: 0.72rem;
      line-height: 1.2;
      font-weight: 600;
      color: #6b7280;
    }
    .record-value {
      display: block;
      font-size: 0.92rem;
      line-height: 1.35;
      font-weight: 700;
      color: #111827;
      word-break: break-word;
    }
    .record-row-full {
      margin-bottom: 0.72rem;
    }
    .record-row-full .record-value {
      font-weight: 600;
    }
    .record-ref .record-value {
      font-weight: 800;
      letter-spacing: 0.01em;
    }
    .record-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.65rem;
      margin-top: 0.35rem;
    }
    .record-btn {
      min-height: 2.75rem;
      border: none;
      border-radius: 999px;
      font-size: 0.98rem;
      font-weight: 800;
      cursor: pointer;
    }
    .record-btn--approve {
      background: linear-gradient(165deg, #0f4a15 0%, #1d7a2a 100%);
      color: #ffffff;
    }
    .record-btn--dismiss {
      background: #f3f4f6;
      color: #374151;
    }
    @media (max-width: 640px) {
      .records-shell {
        padding: 1.15rem 0.95rem 7.8rem;
      }
      .record-grid {
        gap: 0.62rem 0.75rem;
      }
    }
  </style>
</head>
<body class="has-app-bottom-nav records-page">
  <header>
    <div class="nav">
      <button type="button" id="records-header-back" class="records-header-back" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18 9 12l6-6"/></svg>
      </button>
      <p class="records-header-brand">Records</p>
    </div>
  </header>

  <main class="records-shell">
    <div id="records-list" class="records-list" aria-live="polite"></div>
    <p id="records-empty" class="records-empty" hidden>No records right now.</p>
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
      <a href="login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin">
        <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
          <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
        </span>
        <span class="app-bottom-nav-label">Account</span>
      </a>
    </div>
  </nav>

  <script src="js/beanthentic_api_urls.js"></script>
  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script src="js/beanthentic_records_store.js"></script>
  <script src="js/txn_history_store.js"></script>
  <script>
    (function () {
      var listRoot = document.getElementById('records-list');
      var emptyEl = document.getElementById('records-empty');
      var store = window.BeanthenticRecords;

      function moneyPhp(n) {
        var num = Number(n);
        if (!isFinite(num)) return 'PHP 0.00';
        return 'PHP ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      function qtyLabel(rec) {
        var q = String(rec.qty || '').trim();
        var u = String(rec.unit || '').trim().toUpperCase();
        if (!q) return '-';
        return q + (u ? ' ' + u : '');
      }

      function field(label, value, extraClass) {
        var wrap = document.createElement('div');
        wrap.className = 'record-field' + (extraClass ? ' ' + extraClass : '');
        var lbl = document.createElement('span');
        lbl.className = 'record-label';
        lbl.textContent = label;
        var val = document.createElement('span');
        val.className = 'record-value';
        val.textContent = value;
        wrap.appendChild(lbl);
        wrap.appendChild(val);
        return wrap;
      }

      function fullRow(label, value, extraClass) {
        var wrap = document.createElement('div');
        wrap.className = 'record-row-full' + (extraClass ? ' ' + extraClass : '');
        wrap.appendChild(field(label, value));
        return wrap;
      }

      function renderCard(rec) {
        var card = document.createElement('article');
        card.className = 'record-card';
        card.setAttribute('data-id', rec.id);

        var head = document.createElement('div');
        head.className = 'record-card-head';
        var name = document.createElement('h2');
        name.className = 'record-card-name';
        name.textContent = rec.buyer || 'Unknown buyer';
        var badge = document.createElement('span');
        badge.className = 'record-badge record-badge--' + (rec.status === 'approved' ? 'approved' : 'pending');
        badge.textContent = rec.status === 'approved' ? 'Approved' : 'Pending';
        head.appendChild(name);
        head.appendChild(badge);
        card.appendChild(head);

        var grid = document.createElement('div');
        grid.className = 'record-grid';
        grid.appendChild(field('Type', (rec.transaction_type || 'pickup').replace(/_/g, ' ')));
        grid.appendChild(field('Product', rec.product || '-'));
        grid.appendChild(field('Quantity', qtyLabel(rec)));
        grid.appendChild(field('Amount', moneyPhp(rec.amount)));
        grid.appendChild(field('Payment Method', rec.payment || '-'));
        card.appendChild(grid);

        if (rec.pickup_date) {
          card.appendChild(fullRow('Pick-up Date', rec.pickup_date));
        }
        if (rec.valid_id_path) {
          card.appendChild(fullRow('Valid ID', rec.valid_id_filename || 'Uploaded'));
        }
        card.appendChild(fullRow('Reference No', rec.ref || '-', 'record-ref'));

        if (rec.status === 'pending') {
          var actions = document.createElement('div');
          actions.className = 'record-actions';
          var approveBtn = document.createElement('button');
          approveBtn.type = 'button';
          approveBtn.className = 'record-btn record-btn--approve';
          approveBtn.textContent = 'Approve';
          approveBtn.addEventListener('click', function () {
            approveBtn.disabled = true;
            dismissBtn.disabled = true;
            store.approve(rec.id).then(function (updated) {
              if (!updated) {
                approveBtn.disabled = false;
                dismissBtn.disabled = false;
                alert('Could not approve — check app server and database.');
                return;
              }
              if (window.BeanthenticTxnHistory && window.BeanthenticTxnHistory.refreshFromDatabase) {
                window.BeanthenticTxnHistory.refreshFromDatabase();
              }
              renderList();
            });
          });
          var dismissBtn = document.createElement('button');
          dismissBtn.type = 'button';
          dismissBtn.className = 'record-btn record-btn--dismiss';
          dismissBtn.textContent = 'Dismiss';
          dismissBtn.addEventListener('click', function () {
            approveBtn.disabled = true;
            dismissBtn.disabled = true;
            store.dismiss(rec.id).then(function (result) {
              if (!result) {
                approveBtn.disabled = false;
                dismissBtn.disabled = false;
                alert('Could not dismiss — check app server and database.');
                return;
              }
              renderList();
            });
          });
          actions.appendChild(approveBtn);
          actions.appendChild(dismissBtn);
          card.appendChild(actions);
        }

        return card;
      }

      function renderList() {
        if (!listRoot || !store) return;
        listRoot.innerHTML = '';
        if (emptyEl) {
          emptyEl.hidden = false;
          emptyEl.textContent = 'Loading records…';
        }
        store.refreshFromDatabase().then(function (all) {
          var filtered = (all || []).filter(function (rec) {
            return String(rec.status || '').toLowerCase() === 'pending';
          });
          listRoot.innerHTML = '';
          if (filtered.length === 0) {
            if (emptyEl) {
              emptyEl.hidden = false;
              var err = store.getLastFetchError ? store.getLastFetchError() : '';
              emptyEl.textContent = err
                ? err
                : 'No pending records. Submit a transaction on Client Web — it will appear here for Approve or Dismiss.';
            }
            return;
          }
          if (emptyEl) emptyEl.hidden = true;
          filtered.forEach(function (rec) {
            listRoot.appendChild(renderCard(rec));
          });
        });
      }

      var headerBackBtn = document.getElementById('records-header-back');
      if (headerBackBtn) {
        headerBackBtn.addEventListener('click', function () {
          try {
            if (window.history.length > 1) {
              window.history.back();
              return;
            }
          } catch (_e) {}
          window.location.href = 'index.php#home';
        });
      }

      if (store.clearDemoCache) store.clearDemoCache();
      renderList();
      setInterval(function () {
        if (!document.hidden) renderList();
      }, 12000);
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden) renderList();
      });
    })();
  </script>
</body>
</html>
