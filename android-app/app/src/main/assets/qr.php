<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#25671E" />
  <script>window.__BEANTHENTIC_SESSION_GATE__ = 'protected';</script>
  <script src="js/beanthentic_session_gate.js"></script>
  <title>QR · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
  <style>
    .transaction-page header {
      background: linear-gradient(180deg, #1d7a2a 0%, #145e1e 100%);
      border-bottom: none;
      border-radius: 0 0 14px 14px;
      padding-top: env(safe-area-inset-top);
    }
    .transaction-page .nav {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      min-height: 4.6rem;
      padding: 0.72rem max(1.05rem, env(safe-area-inset-left, 0px)) 0.78rem
        max(1.05rem, env(safe-area-inset-right, 0px));
    }
    .txn-header-back {
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
    .txn-header-back svg {
      width: 24px;
      height: 24px;
    }
    .txn-header-brand {
      margin: 0;
      color: #ffffff;
      font-size: 2rem;
      line-height: 1;
      font-weight: 800;
      letter-spacing: -0.01em;
      text-align: center;
      white-space: nowrap;
    }
    .txn-shell {
      width: min(100%, 680px);
      margin: 0 auto;
      padding: 1.1rem 1.2rem 8.2rem;
      box-sizing: border-box;
    }
    .txn-title {
      margin: 0 0 1.05rem;
      font-size: 2.05rem;
      line-height: 1.06;
      font-weight: 800;
      color: #2f1c12;
      letter-spacing: -0.01em;
    }
    .txn-group {
      margin-bottom: 0.92rem;
    }
    .txn-label {
      display: block;
      margin: 0 0 0.42rem;
      font-size: 0.78rem;
      line-height: 1.15;
      font-weight: 700;
      color: #1f2937;
      letter-spacing: 0.01em;
    }
    .txn-control,
    .txn-select {
      width: 100%;
      min-height: 3rem;
      border: 1px solid #d8dde4;
      border-radius: 8px;
      background: #ffffff;
      color: #111827;
      font-size: 1.08rem;
      padding: 0.74rem 0.88rem;
      box-sizing: border-box;
      outline: none;
    }
    .txn-select {
      appearance: none;
      -webkit-appearance: none;
      background-image: linear-gradient(45deg, transparent 50%, #111827 50%), linear-gradient(135deg, #111827 50%, transparent 50%);
      background-position: calc(100% - 16px) calc(50% + 1px), calc(100% - 11px) calc(50% + 1px);
      background-size: 5px 5px, 5px 5px;
      background-repeat: no-repeat;
      padding-right: 2rem;
    }
    .txn-control:focus,
    .txn-select:focus {
      border-color: #1d7a2a;
      box-shadow: 0 0 0 3px rgba(29, 122, 42, 0.14);
    }
    .txn-row {
      display: grid;
      grid-template-columns: 1fr 185px;
      gap: 0.7rem;
      align-items: end;
    }
    .txn-submit {
      width: 100%;
      min-height: 3.2rem;
      border: none;
      border-radius: 10px;
      background: linear-gradient(165deg, #0f4a15 0%, #1d7a2a 100%);
      color: #ffffff;
      font-size: 1.12rem;
      font-weight: 800;
      letter-spacing: 0.01em;
      cursor: pointer;
      margin-top: 0.85rem;
    }
    .txn-confirm-note {
      margin-top: 0.55rem;
      display: flex;
      align-items: center;
      gap: 0.45rem;
      color: #2f2f2f;
      font-size: 0.68rem;
      line-height: 1.35;
    }
    .txn-confirm-note input {
      width: 0.95rem;
      height: 0.95rem;
      margin: 0;
      accent-color: #1d7a2a;
      flex-shrink: 0;
    }
    .txn-view[hidden] {
      display: none !important;
    }
    .txn-receipt {
      width: min(100%, 620px);
      margin: 0 auto;
      padding: 1.15rem 1.2rem 7.6rem;
      box-sizing: border-box;
    }
    .txn-receipt-title {
      margin: 0 0 1.4rem;
      text-align: center;
      color: #111827;
      font-size: 2rem;
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: -0.01em;
    }
    .txn-receipt-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: 0.62rem 1.15rem;
      align-items: baseline;
      color: #111827;
    }
    .txn-receipt-label {
      font-size: 0.9rem;
      font-weight: 700;
      line-height: 1.35;
    }
    .txn-receipt-value {
      justify-self: end;
      text-align: right;
      font-size: 0.98rem;
      line-height: 1.35;
      color: #111827;
    }
    .txn-receipt-divider {
      border: 0;
      border-top: 1px solid rgba(17, 24, 39, 0.2);
      margin: 1.15rem 0;
    }
    .txn-receipt-summary .txn-receipt-label {
      font-size: 1.45rem;
      font-weight: 800;
      color: #111827;
    }
    .txn-receipt-summary .txn-receipt-value {
      font-size: 1.55rem;
      font-weight: 800;
      color: #111827;
    }
    .txn-receipt-summary--change .txn-receipt-label {
      font-size: 1.28rem;
    }
    .txn-receipt-summary--change .txn-receipt-value {
      font-size: 1.4rem;
      font-weight: 700;
    }
    @media (max-width: 640px) {
      .txn-receipt {
        padding: 1rem 0.95rem 7rem;
      }
      .txn-receipt-title {
        font-size: 1.8rem;
      }
      .txn-receipt-label {
        font-size: 0.84rem;
      }
      .txn-receipt-value {
        font-size: 0.92rem;
      }
      .txn-receipt-summary .txn-receipt-label {
        font-size: 1.28rem;
      }
      .txn-receipt-summary .txn-receipt-value {
        font-size: 1.38rem;
      }
      .txn-receipt-summary--change .txn-receipt-label {
        font-size: 1.12rem;
      }
      .txn-receipt-summary--change .txn-receipt-value {
        font-size: 1.2rem;
      }
    }
    .txn-qty-row {
      display: grid;
      grid-template-columns: 1fr 64px;
      gap: 0;
      border: 1px solid #d8dde4;
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
      min-height: 3rem;
    }
    .txn-qty-input {
      border: 0;
      border-right: 1px solid #d8dde4;
      border-radius: 0;
      min-height: 100%;
      box-shadow: none !important;
    }
    .txn-qty-unit {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #111827;
      font-size: 1.05rem;
      font-weight: 600;
      letter-spacing: 0.01em;
      background: #fff;
    }
    @media (max-width: 640px) {
      .txn-shell {
        padding: 1rem 0.9rem 7.6rem;
      }
      .txn-title {
        font-size: 1.92rem;
      }
      .txn-row {
        grid-template-columns: 1fr 145px;
      }
    }
  </style>
</head>
<body class="has-app-bottom-nav transaction-page">
  <header>
    <div class="nav">
      <button type="button" id="txn-header-back" class="txn-header-back" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18 9 12l6-6"/></svg>
      </button>
      <p class="txn-header-brand">Transactions</p>
    </div>
  </header>

  <main>
    <section id="txn-entry-view" class="txn-shell txn-view" aria-label="Transaction form">
      <div class="txn-group">
        <label class="txn-label" for="buyer-full-name">Buyer's Full Name</label>
        <input id="buyer-full-name" class="txn-control" type="text" autocomplete="name" />
      </div>

      <div class="txn-row">
        <div class="txn-group">
          <label class="txn-label" for="txn-product">Product</label>
          <select id="txn-product" class="txn-select" aria-label="Product">
            <option value="" selected disabled></option>
            <option value="liberica">Liberica</option>
            <option value="robusta">Robusta</option>
            <option value="excelsa">Excelsa</option>
          </select>
        </div>
        <div class="txn-group">
          <label class="txn-label" for="txn-quantity">Quantity</label>
          <div class="txn-qty-row">
            <input id="txn-quantity-value" class="txn-control txn-qty-input" type="number" inputmode="numeric" min="1" step="1" placeholder="" aria-label="Quantity value" />
            <select id="txn-quantity" class="txn-select" aria-label="Quantity unit">
              <option value="" selected disabled hidden></option>
              <option value="kg">KG</option>
              <option value="g">G</option>
              <option value="lb">LB</option>
            </select>
          </div>
        </div>
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-amount">Amount</label>
        <input id="txn-amount" class="txn-control" type="number" inputmode="decimal" min="0" step="0.01" />
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-payment">Payment</label>
        <select id="txn-payment" class="txn-select" aria-label="Payment method">
          <option value="" selected disabled></option>
          <option value="cash">Cash</option>
          <option value="gcash">GCash</option>
          <option value="bank-transfer">Bank Transfer</option>
        </select>
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-payment-amount">Payment Amount</label>
        <input id="txn-payment-amount" class="txn-control" type="number" inputmode="decimal" min="0" step="0.01" />
      </div>

      <button id="txn-proceed-btn" type="button" class="txn-submit">Proceed Transaction</button>
    </section>

    <section id="txn-confirm-view" class="txn-shell txn-view" aria-label="Transaction confirmation" hidden>
      <div class="txn-group">
        <label class="txn-label" for="buyer-full-name-confirm">Buyer's Full Name</label>
        <input id="buyer-full-name-confirm" class="txn-control" type="text" readonly />
      </div>

      <div class="txn-row">
        <div class="txn-group">
          <label class="txn-label" for="txn-product-confirm">Product</label>
          <input id="txn-product-confirm" class="txn-control" type="text" readonly />
        </div>
        <div class="txn-group">
          <label class="txn-label" for="txn-quantity-confirm">Quantity</label>
          <div class="txn-qty-row">
            <input id="txn-quantity-confirm" class="txn-control txn-qty-input" type="text" readonly />
            <span id="txn-unit-confirm" class="txn-qty-unit"></span>
          </div>
        </div>
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-amount-confirm">Amount</label>
        <input id="txn-amount-confirm" class="txn-control" type="text" readonly />
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-payment-confirm">Payment</label>
        <input id="txn-payment-confirm" class="txn-control" type="text" readonly />
      </div>

      <div class="txn-group">
        <label class="txn-label" for="txn-payment-amount-confirm">Payment Amount</label>
        <input id="txn-payment-amount-confirm" class="txn-control" type="text" readonly />
      </div>

      <label class="txn-confirm-note" for="txn-confirm-check">
        <input id="txn-confirm-check" type="checkbox" />
        <span>I confirm that the details are correct</span>
      </label>

      <button id="txn-confirm-btn" type="button" class="txn-submit" disabled>Confirm</button>
    </section>

    <section id="txn-receipt-view" class="txn-receipt txn-view" aria-label="Transaction receipt" hidden>
      <h1 class="txn-receipt-title">Receipts</h1>

      <div class="txn-receipt-grid">
        <div class="txn-receipt-label">Buyer's Full Name</div>
        <div id="receipt-buyer" class="txn-receipt-value">-</div>

        <div class="txn-receipt-label">Product</div>
        <div id="receipt-product" class="txn-receipt-value">-</div>

        <div class="txn-receipt-label">Quantity</div>
        <div id="receipt-quantity" class="txn-receipt-value">-</div>

        <div class="txn-receipt-label">Amount</div>
        <div id="receipt-amount" class="txn-receipt-value">0.00</div>

        <div class="txn-receipt-label">Payment</div>
        <div id="receipt-payment" class="txn-receipt-value">-</div>

        <div class="txn-receipt-label">Payment Amount</div>
        <div id="receipt-payment-amount" class="txn-receipt-value">0.00</div>
      </div>

      <hr class="txn-receipt-divider" />

      <div class="txn-receipt-grid txn-receipt-summary">
        <div class="txn-receipt-label">Total</div>
        <div id="receipt-total" class="txn-receipt-value">0.00</div>
      </div>
      <div class="txn-receipt-grid txn-receipt-summary txn-receipt-summary--change">
        <div class="txn-receipt-label">Change</div>
        <div id="receipt-change" class="txn-receipt-value">0.00</div>
      </div>

      <hr class="txn-receipt-divider" />

      <div class="txn-receipt-grid">
        <div class="txn-receipt-label">Reference No.</div>
        <div id="receipt-reference" class="txn-receipt-value">-</div>
      </div>
    </section>
  </main>

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

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script src="js/txn_history_store.js"></script>
  <script>
    (function () {
      function flaskBase() {
        try {
          var s = localStorage.getItem('beanthentic_flask_base');
          if (s && String(s).replace(/\s/g, '')) {
            s = String(s).replace(/\/$/, '');
            // If a stale LAN IP was saved, reset to emulator-safe default.
            if (/^https?:\/\/192\.168\./i.test(s)) {
              try { localStorage.removeItem('beanthentic_flask_base'); } catch (_e) {}
            } else {
              return s;
            }
          }
        } catch (e) {}
        if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
          return (location.origin || '').replace(/\/$/, '');
        }
        return 'http://10.0.2.2:5000';
      }
      var b = flaskBase();
      document.querySelectorAll('a[data-beanthentic-flask]').forEach(function (a) {
        var p = a.getAttribute('data-beanthentic-flask');
        if (p) a.setAttribute('href', b + p);
      });

      function moneyLike(v) {
        var s = String(v == null ? '' : v).trim();
        if (!s) return '';
        var n = Number(s.replace(/,/g, ''));
        if (!isFinite(n)) return s;
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }
      function moneyNumber(v) {
        var n = Number(String(v == null ? '' : v).replace(/,/g, '').trim());
        return isFinite(n) ? n : 0;
      }
      function refNo() {
        var d = new Date();
        var dt = [
          d.getFullYear(),
          String(d.getMonth() + 1).padStart(2, '0'),
          String(d.getDate()).padStart(2, '0'),
          String(d.getHours()).padStart(2, '0'),
          String(d.getMinutes()).padStart(2, '0'),
          String(d.getSeconds()).padStart(2, '0')
        ].join('');
        var suffix = Math.floor(Math.random() * 900 + 100);
        return dt + suffix;
      }

      var proceedBtn = document.getElementById('txn-proceed-btn');
      var doneBtn = document.getElementById('txn-confirm-btn');
      var entryView = document.getElementById('txn-entry-view');
      var confirmView = document.getElementById('txn-confirm-view');
      var receiptView = document.getElementById('txn-receipt-view');
      var confirmCheck = document.getElementById('txn-confirm-check');
      var confirmBtn = document.getElementById('txn-confirm-btn');
      if (confirmBtn) confirmBtn.disabled = true;

      if (confirmCheck && confirmBtn) {
        confirmCheck.addEventListener('change', function () {
          confirmBtn.disabled = !confirmCheck.checked;
        });
      }

      if (proceedBtn && entryView && confirmView) {
        proceedBtn.addEventListener('click', function () {
          var buyer = document.getElementById('buyer-full-name');
          var product = document.getElementById('txn-product');
          var qty = document.getElementById('txn-quantity-value');
          var unit = document.getElementById('txn-quantity');
          var amount = document.getElementById('txn-amount');
          var payment = document.getElementById('txn-payment');
          var payAmount = document.getElementById('txn-payment-amount');

          var buyerOut = document.getElementById('buyer-full-name-confirm');
          var productOut = document.getElementById('txn-product-confirm');
          var qtyOut = document.getElementById('txn-quantity-confirm');
          var unitOut = document.getElementById('txn-unit-confirm');
          var amountOut = document.getElementById('txn-amount-confirm');
          var paymentOut = document.getElementById('txn-payment-confirm');
          var payAmountOut = document.getElementById('txn-payment-amount-confirm');

          if (buyerOut) buyerOut.value = (buyer && buyer.value.trim()) || '';
          if (productOut) productOut.value = (product && product.options[product.selectedIndex] && product.options[product.selectedIndex].text) || '';
          if (qtyOut) qtyOut.value = (qty && qty.value) || '';
          if (unitOut) unitOut.textContent = (unit && unit.value ? String(unit.value).toUpperCase() : '');
          if (amountOut) amountOut.value = moneyLike(amount && amount.value);
          if (paymentOut) paymentOut.value = (payment && payment.options[payment.selectedIndex] && payment.options[payment.selectedIndex].text) || '';
          if (payAmountOut) payAmountOut.value = moneyLike(payAmount && payAmount.value);

          entryView.hidden = true;
          confirmView.hidden = false;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }

      if (doneBtn && confirmView && receiptView) {
        doneBtn.addEventListener('click', function () {
          if (doneBtn.disabled) return;

          var buyerTxt = (document.getElementById('buyer-full-name-confirm').value || '').trim();
          var productTxt = (document.getElementById('txn-product-confirm').value || '').trim();
          var qtyTxt = (document.getElementById('txn-quantity-confirm').value || '').trim();
          var unitTxt = (document.getElementById('txn-unit-confirm').textContent || '').trim();
          var amountTxt = (document.getElementById('txn-amount-confirm').value || '').trim();
          var paymentTxt = (document.getElementById('txn-payment-confirm').value || '').trim();
          var paymentAmountTxt = (document.getElementById('txn-payment-amount-confirm').value || '').trim();

          var total = moneyNumber(amountTxt);
          var paid = moneyNumber(paymentAmountTxt);
          var change = Math.max(0, paid - total);

          var productSel = document.getElementById('txn-product');
          var varietyKey = (productSel && productSel.value) ? String(productSel.value).toLowerCase() : '';

          var rb = document.getElementById('receipt-buyer');
          var rp = document.getElementById('receipt-product');
          var rq = document.getElementById('receipt-quantity');
          var ra = document.getElementById('receipt-amount');
          var rpm = document.getElementById('receipt-payment');
          var rpa = document.getElementById('receipt-payment-amount');
          var rt = document.getElementById('receipt-total');
          var rc = document.getElementById('receipt-change');
          var rr = document.getElementById('receipt-reference');

          var refStr = refNo();

          if (rb) rb.textContent = buyerTxt || '-';
          if (rp) rp.textContent = productTxt || '-';
          if (rq) rq.textContent = (qtyTxt || '-') + (unitTxt ? unitTxt : '');
          if (ra) ra.textContent = moneyLike(total);
          if (rpm) rpm.textContent = paymentTxt || '-';
          if (rpa) rpa.textContent = moneyLike(paid);
          if (rt) rt.textContent = moneyLike(total);
          if (rc) rc.textContent = moneyLike(change);
          if (rr) rr.textContent = refStr;

          if (window.BeanthenticTxnHistory) {
            window.BeanthenticTxnHistory.push({
              buyer: buyerTxt,
              product: productTxt,
              variety: varietyKey,
              qty: qtyTxt,
              unit: unitTxt,
              amount: total,
              payment: paymentTxt,
              paymentAmount: paid,
              total: total,
              change: change,
              ref: refStr,
              at: new Date().toISOString()
            });
          }

          confirmView.hidden = true;
          receiptView.hidden = false;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }

      var headerBackBtn = document.getElementById('txn-header-back');
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
    })();
  </script>
</body>
</html>
