<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#143d22" />
  <script>
    // Guest-only page guard: if already signed in, never stay on login page.
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
        var localUser = parseUser(localStorage.getItem('beanthentic_user'));
        var sessionUser = parseUser(sessionStorage.getItem('beanthentic_user'));
        var user = localUser || sessionUser;
        if (user) {
          try {
            localStorage.setItem('beanthentic_user', JSON.stringify(user));
            sessionStorage.setItem('beanthentic_user', JSON.stringify(user));
          } catch (_syncErr) {}
          window.location.replace('account.php');
          return;
        }
        var hasLang = false;
        try {
          hasLang = !!(
            localStorage.getItem('beanthentic_app_lang') ||
            sessionStorage.getItem('beanthentic_app_lang')
          );
        } catch (_langRead) {
          hasLang = true;
        }
        if (!hasLang) {
          window.location.replace('choose_language.html?next=login.php');
        }
      } catch (_e) {
        /* stay on page if storage is unavailable */
      }
    })();
  </script>
  <title>Sign in · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body class="login-page">
  <header class="login-page-header">
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
        <a href="index.php#home" class="logo" aria-label="Beanthentic home">
          <img class="logo-mark" src="beanthentic_logo.png" alt="Beanthentic" />
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

  <main class="auth-main login-main">
    <div class="auth-card login-card">
      <div class="login-brand">
        <img
          class="login-brand-full-img"
          src="beanthentic_logo.png"
          alt="Beanthentic"
          width="320"
          height="320"
          decoding="async"
        />
      </div>
      <h1 class="login-greeting">Mabuhay, Lipeño!</h1>
      <p class="auth-lead login-lead">Welcome back. Enter your account details.</p>
      <form class="auth-form login-form" method="post" action="#" autocomplete="on">
        <label for="login-phone-local">Phone Number</label>
        <div class="login-phone-row">
          <span class="login-phone-cc" aria-hidden="true">+63</span>
          <input
            id="login-phone-local"
            name="phone_local"
            type="text"
            required
            inputmode="numeric"
            autocomplete="tel-national"
            class="login-phone-input"
            placeholder="9XXXXXXXXX"
            maxlength="96"
          />
        </div>

        <label for="login-password">Password</label>
        <div class="signup-password-field">
          <input
            id="login-password"
            name="password"
            type="password"
            required
            autocomplete="current-password"
            placeholder="••••••••"
            class="signup-password-input"
          />
          <button type="button" class="signup-password-toggle" aria-label="Show password" aria-pressed="false" data-target="login-password">
            <svg class="signup-password-toggle-icon signup-password-toggle-icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
            <svg class="signup-password-toggle-icon signup-password-toggle-icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>

        <div class="login-options">
          <label class="login-remember">
            <span class="login-remember__control">
              <input id="login-remember" class="login-remember__input" name="remember" type="checkbox" checked />
            </span>
            <span class="login-remember__text">Remember me</span>
          </label>
          <a class="login-forgot" href="settings.php">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-primary login-submit-btn">Login</button>
      </form>
      <p class="auth-switch login-switch">Need an account? <a href="signup.php">Sign up here!</a></p>
    </div>
  </main>

  <div id="login-success-loader" class="tutorial-loader login-success-loader" hidden aria-hidden="true" aria-busy="false">
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

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
  <script src="js/auth_lang.js"></script>
  <script>
    (function () {
      function applyLoginStrings() {
        if (window.BeanthenticAuthLang) window.BeanthenticAuthLang.applyLoginAuthLang();
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyLoginStrings);
      } else {
        applyLoginStrings();
      }
    })();
  </script>
  <script>
    (function () {
      function fixHeaderLogoHome() {
        if (typeof location === 'undefined') return;
        if (location.protocol !== 'http:' && location.protocol !== 'https:') return;
        var o = (location.origin || '').replace(/\/$/, '');
        var logo = document.querySelector('header a.logo');
        if (logo) logo.setAttribute('href', o + '/#home');
      }
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fixHeaderLogoHome);
      else fixHeaderLogoHome();
    })();
  </script>
  <script>
    (function () {
      var USER_NAME_MAP_KEY = 'beanthentic_user_name_map';

      function getKnownUserName(email) {
        var cleanEmail = String(email || '').trim().toLowerCase();
        if (!cleanEmail) return '';
        try {
          var raw = localStorage.getItem(USER_NAME_MAP_KEY) || sessionStorage.getItem(USER_NAME_MAP_KEY);
          var map = raw ? JSON.parse(raw) : {};
          return map && typeof map[cleanEmail] === 'string' ? String(map[cleanEmail]).trim() : '';
        } catch (_err) {
          return '';
        }
      }

      function saveKnownUserName(email, name) {
        var cleanEmail = String(email || '').trim().toLowerCase();
        var cleanName = String(name || '').trim();
        if (!cleanEmail || !cleanName) return;
        try {
          var raw = localStorage.getItem(USER_NAME_MAP_KEY) || sessionStorage.getItem(USER_NAME_MAP_KEY);
          var map = raw ? JSON.parse(raw) : {};
          map[cleanEmail] = cleanName;
          localStorage.setItem(USER_NAME_MAP_KEY, JSON.stringify(map));
          sessionStorage.setItem(USER_NAME_MAP_KEY, JSON.stringify(map));
        } catch (_err) {
          /* ignore */
        }
      }

      function normalizeLoginId(raw) {
        var s = String(raw || '').trim();
        if (!s) return '';
        if (s.indexOf('@') >= 0) return s.toLowerCase();
        var digits = s.replace(/\D/g, '');
        if (digits.indexOf('0') === 0) digits = digits.slice(1);
        if (digits.indexOf('63') === 0) digits = digits.slice(2);
        if (digits.length === 10 && digits.charAt(0) === '9') return '+63' + digits;
        return '';
      }

      var REGISTERED_ACCOUNTS_KEY = 'beanthentic_registered_accounts';

      function getRegisteredAccounts() {
        try {
          var raw = localStorage.getItem(REGISTERED_ACCOUNTS_KEY);
          var o = raw ? JSON.parse(raw) : {};
          return o && typeof o === 'object' ? o : {};
        } catch (_err) {
          return {};
        }
      }

      function getRegisteredRecord(loginId) {
        return getRegisteredAccounts()[loginId] || null;
      }

      function bindPasswordToggles(root) {
        root.querySelectorAll('.signup-password-toggle').forEach(function (btn) {
          if (btn.dataset.bound === '1') return;
          btn.dataset.bound = '1';
          var id = btn.getAttribute('data-target');
          var input = id ? document.getElementById(id) : null;
          if (!input) return;
          var hideIc = btn.querySelector('.signup-password-toggle-icon--hide');
          var showIc = btn.querySelector('.signup-password-toggle-icon--show');
          btn.addEventListener('click', function () {
            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.setAttribute('aria-pressed', showing ? 'false' : 'true');
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            if (hideIc) hideIc.hidden = !showing;
            if (showIc) showIc.hidden = showing;
          });
        });
      }

      document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('.login-form');
        if (!form) return;
        bindPasswordToggles(form);
        var phoneEl = document.getElementById('login-phone-local');
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          var localVal = phoneEl ? String(phoneEl.value || '').trim() : '';
          if (!localVal) return;
          var loginId = localVal.indexOf('@') >= 0 ? localVal.toLowerCase() : normalizeLoginId(localVal);
          if (!loginId) {
            try {
              window.alert('Please enter a valid Philippine mobile number (9XXXXXXXXX) or your email.');
            } catch (_a) {}
            return;
          }
          var record = getRegisteredRecord(loginId);
          if (!record) {
            try {
              window.alert(
                'Hindi pa naka-register ang account na ito. Mag-sign up muna bago mag-log in.'
              );
            } catch (_ns) {}
            return;
          }
          var pwEl = document.getElementById('login-password');
          var pwVal = pwEl ? String(pwEl.value || '') : '';
          if (pwVal !== record.password) {
            try {
              window.alert('Mali ang password. Subukan ulit.');
            } catch (_wp) {}
            return;
          }
          var savedName = getKnownUserName(loginId);
          var name = savedName;
          if (!name) {
            if (loginId.indexOf('@') > 0) name = loginId.split('@')[0];
            else name = loginId.replace(/^\+63/, '');
          }
          var rememberEl = document.getElementById('login-remember');
          var remember = rememberEl ? !!rememberEl.checked : true;
          try {
            var user = {
              email: loginId,
              name: name,
              signedInAt: Date.now()
            };
            if (savedName) saveKnownUserName(loginId, savedName);
            var payload = JSON.stringify(user);
            sessionStorage.setItem('beanthentic_user', payload);
            if (remember) {
              localStorage.setItem('beanthentic_user', payload);
            } else {
              try {
                localStorage.removeItem('beanthentic_user');
              } catch (_r) {}
            }
          } catch (err) {
            /* ignore */
          }
          var overlay = document.getElementById('login-success-loader');
          if (overlay) {
            overlay.hidden = false;
            overlay.removeAttribute('aria-hidden');
            overlay.setAttribute('aria-busy', 'true');
          }
          try {
            sessionStorage.setItem('beanthentic_skip_tutorial_loader', '1');
          } catch (_sk) {}
          window.setTimeout(function () {
            try {
              window.location.assign(new URL('tutorial.php', location.href).href);
            } catch (e2) {
              window.location.assign('tutorial.php');
            }
          }, 1200);
        });
      });
    })();
  </script>
</body>
</html>

