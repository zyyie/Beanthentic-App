(function () {
  var STORAGE_KEY = 'beanthentic_app_lang';

  var STRINGS = {
    en: {
      'nav.home': 'Home',
      'nav.record': 'Record',
      'nav.register': 'Register',
      'nav.history': 'History',
      'nav.account': 'Account',
      'title.settings': 'Settings',
      'title.changePassword': 'Change Password',
      'title.notifications': 'Notification Settings',
      'title.privacy': 'Privacy Notice',
      'title.general': 'General',
      'account.pageTitle': 'Account Settings',
      'account.personalInfo': 'Personal Information',
      'account.faq': 'Frequently Asked Questions',
      'account.aboutUs': 'About Us',
      'account.privacyNotice': 'Privacy Notice',
      'account.settings': 'Settings',
      'account.security': 'Security',
      'account.changePassword': 'Change Password',
      'account.notifications': 'Notifications',
      'account.privacy': 'Privacy',
      'account.general': 'General',
      'account.help': 'Help',
      'settings.securityLead': 'Change your password. Enter your current password first.',
      'settings.currentPassword': 'Current Password',
      'settings.newPassword': 'New Password',
      'settings.confirmPassword': 'Confirm New Password',
      'settings.updatePassword': 'Update Password',
      'settings.notifLead': 'Manage email, SMS, and in-app alerts for registrations and security events.',
      'settings.notifHeading': 'Notifications',
      'settings.notifLogin': 'Login Verification',
      'settings.notifSuspicious': 'Suspicious Login Alerts',
      'settings.notifAccount': 'Account Updates',
      'settings.notifSecurity': 'Security Reminders',
      'settings.notifApp': 'App Updates',
      'settings.notifSave': 'Save Notification Settings',
      'settings.langHeading': 'Language',
      'settings.langLead': 'Choose a language for the app. Changes apply immediately.',
      'settings.langLabel': 'Language',
      'settings.langEnglish': 'English',
      'settings.langFilipino': 'Filipino',
      'settings.themeHeading': 'Theme',
      'settings.themeLead': 'Choose how the app looks on this device.',
      'settings.themeLabel': 'Appearance',
      'settings.themeLight': 'Light',
      'settings.themeDark': 'Dark',
      'settings.themeSystem': 'System default',
      'settings.aboutHeading': 'About App Version',
      'settings.aboutLead': 'Beanthentic Coffee — farmer registration and traceability for Lipa City.',
      'settings.aboutAppName': 'App name',
      'settings.aboutVersion': 'Version',
      'settings.aboutBuild': 'Build',
      'notify.langEn': 'Language set to English.',
      'notify.langFil': 'Language set to Filipino.',
      'notify.themeSaved': 'Theme preference saved on this device.',
      'notify.notifSaved': 'Notification settings saved.',
      'notify.passFill': 'Please fill out all password fields.',
      'notify.passShort': 'New password must be at least 6 characters.',
      'notify.passMatch': 'New password and confirmation do not match.',
      'notify.passUpdated': 'Password updated.'
    },
    fil: {
      'nav.home': 'Home',
      'nav.record': 'Rekord',
      'nav.register': 'Magrehistro',
      'nav.history': 'Kasaysayan',
      'nav.account': 'Account',
      'title.settings': 'Mga Setting',
      'title.changePassword': 'Palitan ang Password',
      'title.notifications': 'Mga Setting ng Notification',
      'title.privacy': 'Abiso sa Privacy',
      'title.general': 'Pangkalahatan',
      'account.pageTitle': 'Mga Setting ng Account',
      'account.personalInfo': 'Personal na Impormasyon',
      'account.faq': 'Mga Madalas Itanong',
      'account.aboutUs': 'Tungkol sa Amin',
      'account.privacyNotice': 'Abiso sa Privacy',
      'account.settings': 'Mga Setting',
      'account.security': 'Seguridad',
      'account.changePassword': 'Palitan ang Password',
      'account.notifications': 'Mga Notification',
      'account.privacy': 'Privacy',
      'account.general': 'Pangkalahatan',
      'account.help': 'Tulong',
      'settings.securityLead': 'Palitan ang iyong password. Ilagay muna ang kasalukuyang password.',
      'settings.currentPassword': 'Kasalukuyang Password',
      'settings.newPassword': 'Bagong Password',
      'settings.confirmPassword': 'Kumpirmahin ang Bagong Password',
      'settings.updatePassword': 'I-update ang Password',
      'settings.notifLead': 'Pamahalaan ang email, SMS, at in-app na alerto para sa rehistro at seguridad.',
      'settings.notifHeading': 'Mga Notification',
      'settings.notifLogin': 'Login Verification',
      'settings.notifSuspicious': 'Suspicious Login Alerts',
      'settings.notifAccount': 'Account Updates',
      'settings.notifSecurity': 'Security Reminders',
      'settings.notifApp': 'App Updates',
      'settings.notifSave': 'I-save ang Notification Settings',
      'settings.langHeading': 'Wika',
      'settings.langLead': 'Pumili ng wika para sa app. Agad na mailalapat ang pagbabago.',
      'settings.langLabel': 'Wika',
      'settings.langEnglish': 'English',
      'settings.langFilipino': 'Filipino',
      'settings.themeHeading': 'Tema',
      'settings.themeLead': 'Piliin kung paano magmukha ang app sa device na ito.',
      'settings.themeLabel': 'Itsura',
      'settings.themeLight': 'Maliwanag',
      'settings.themeDark': 'Madilim',
      'settings.themeSystem': 'Default ng system',
      'settings.aboutHeading': 'Tungkol sa Bersyon ng App',
      'settings.aboutLead': 'Beanthentic Coffee — rehistro ng magsasaka at traceability para sa Lipa City.',
      'settings.aboutAppName': 'Pangalan ng app',
      'settings.aboutVersion': 'Bersyon',
      'settings.aboutBuild': 'Build',
      'notify.langEn': 'Naka-set ang wika: English.',
      'notify.langFil': 'Naka-set ang wika: Filipino.',
      'notify.themeSaved': 'Na-save ang tema sa device na ito.',
      'notify.notifSaved': 'Na-save ang notification settings.',
      'notify.passFill': 'Pakipunan ang lahat ng password field.',
      'notify.passShort': 'Ang bagong password ay dapat hindi bababa sa 6 na character.',
      'notify.passMatch': 'Hindi magkatugma ang bagong password at kumpirmasyon.',
      'notify.passUpdated': 'Na-update ang password.'
    }
  };

  function getLang() {
    try {
      var v = localStorage.getItem(STORAGE_KEY) || sessionStorage.getItem(STORAGE_KEY);
      return v === 'fil' ? 'fil' : 'en';
    } catch (_e) {
      return 'en';
    }
  }

  function t(key) {
    var lang = getLang();
    var pack = STRINGS[lang] || STRINGS.en;
    if (pack[key] != null) return pack[key];
    return (STRINGS.en[key] != null ? STRINGS.en[key] : key);
  }

  function setLang(code) {
    var lang = code === 'fil' ? 'fil' : 'en';
    try {
      localStorage.setItem(STORAGE_KEY, lang);
    } catch (_l) { }
    try {
      sessionStorage.setItem(STORAGE_KEY, lang);
    } catch (_s) { }
    try {
      document.documentElement.lang = lang === 'fil' ? 'fil' : 'en';
    } catch (_d) { }
    applyAppLang();
    try {
      document.dispatchEvent(
        new CustomEvent('beanthentic-lang-changed', { detail: { lang: lang } })
      );
    } catch (_e) { }
    return lang;
  }

  function text(el, value) {
    if (!el) return;
    el.textContent = value;
  }

  function applyI18n(root) {
    var scope = root && root.querySelector ? root : document;
    scope.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (!key) return;
      var val = t(key);
      if (el.tagName === 'OPTION') {
        el.textContent = val;
      } else {
        el.textContent = val;
      }
    });
    scope.querySelectorAll('[data-i18n-html]').forEach(function (el) {
      var key = el.getAttribute('data-i18n-html');
      if (!key) return;
      el.innerHTML = t(key);
    });
  }

  function applyThemeSelectOptions() {
    var sel = document.getElementById('settings-theme-select');
    if (!sel) return;
    var opts = sel.querySelectorAll('option');
    opts.forEach(function (opt) {
      var key = opt.getAttribute('data-i18n');
      if (key) opt.textContent = t(key);
    });
  }

  function applyLangSelect() {
    var sel = document.getElementById('settings-lang-select');
    if (!sel) return;
    sel.value = getLang();
    var opts = sel.querySelectorAll('option');
    opts.forEach(function (opt) {
      var key = opt.getAttribute('data-i18n');
      if (key) opt.textContent = t(key);
    });
  }

  function applySettingsNavTitle() {
    var titleEl = document.getElementById('settings-page-title');
    if (!titleEl || !document.body.classList.contains('settings-page')) return;
    var section = 'security';
    try {
      section =
        document.body.getAttribute('data-active-settings-section') ||
        new URLSearchParams(location.search || '').get('section') ||
        'security';
      if (section === 'language' || section === 'theme' || section === 'about') section = 'general';
    } catch (_s) { }
    var keyMap = {
      security: 'title.changePassword',
      notifications: 'title.notifications',
      privacy: 'title.privacy',
      general: 'title.general'
    };
    titleEl.textContent = t(keyMap[section] || 'title.settings');
  }

  function applyLoginAuthLang() {
    var lang = getLang();
    var L =
      lang === 'fil'
        ? {
          lead: 'Maligayang pagbalik. Ilagay ang detalye ng iyong account.',
          phone: 'Numero ng telepono',
          password: 'Password',
          remember: 'Tandaan ako',
          forgot: 'Nakalimutan ang password?',
          submit: 'Mag-login',
          switchHtml:
            'Wala pang account? <a href="signup.php">Mag-sign up dito!</a>'
        }
        : {
          lead: 'Welcome back. Enter your account details.',
          phone: 'Phone Number',
          password: 'Password',
          remember: 'Remember me',
          forgot: 'Forgot Password?',
          submit: 'Login',
          switchHtml: 'Need an account? <a href="signup.php">Sign up here!</a>'
        };

    text(document.querySelector('.login-lead'), L.lead);
    text(document.querySelector('label[for="login-phone-local"]'), L.phone);
    text(document.querySelector('label[for="login-password"]'), L.password);
    text(document.querySelector('.login-remember__text'), L.remember);
    text(document.querySelector('.login-forgot'), L.forgot);
    text(document.querySelector('.login-submit-btn'), L.submit);
    var sw = document.querySelector('.login-switch');
    if (sw) sw.innerHTML = L.switchHtml;
  }

  function applySignupAuthLang() {
    var lang = getLang();
    var L =
      lang === 'fil'
        ? {
          lead: 'Samahan kami! Gumawa ng iyong sariling account.',
          phone: 'Numero ng telepono',
          password: 'Password',
          confirm: 'Kumpirmahin ang password',
          submit: 'MAG-SIGN UP',
          switchHtml:
            'May account na? <a href="login.php">Mag-log in dito!</a>'
        }
        : {
          lead: 'Join us! Create your own account.',
          phone: 'Phone Number',
          password: 'Password',
          confirm: 'Confirm Password',
          submit: 'SIGNUP',
          switchHtml:
            'Already have an account? <a href="login.php">Log in here!</a>'
        };

    text(document.querySelector('.signup-page .login-lead'), L.lead);
    text(document.querySelector('label[for="signup-phone-local"]'), L.phone);
    text(document.querySelector('label[for="signup-password"]'), L.password);
    text(document.querySelector('label[for="signup-password2"]'), L.confirm);
    text(document.querySelector('.signup-page .login-submit-btn'), L.submit);
    var sw = document.querySelector('.signup-page .login-switch');
    if (sw) sw.innerHTML = L.switchHtml;
  }

  function applyAppLang() {
    applyI18n(document);
    applyThemeSelectOptions();
    applyLangSelect();
    applySettingsNavTitle();
    if (document.querySelector('.login-lead')) applyLoginAuthLang();
    if (document.querySelector('.signup-page .login-lead')) applySignupAuthLang();
  }

  window.BeanthenticAuthLang = {
    STORAGE_KEY: STORAGE_KEY,
    STRINGS: STRINGS,
    getLang: getLang,
    setLang: setLang,
    t: t,
    applyAppLang: applyAppLang,
    applyI18n: applyI18n,
    applySettingsNavTitle: applySettingsNavTitle,
    applyLoginAuthLang: applyLoginAuthLang,
    applySignupAuthLang: applySignupAuthLang
  };

  try {
    document.documentElement.lang = getLang() === 'fil' ? 'fil' : 'en';
  } catch (_init) { }

  function bootLang() {
    applyAppLang();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootLang);
  } else {
    bootLang();
  }
})();
