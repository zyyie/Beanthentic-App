(function () {
  var STORAGE_KEY = 'beanthentic_app_lang';

  function getLang() {
    try {
      var v =
        localStorage.getItem(STORAGE_KEY) || sessionStorage.getItem(STORAGE_KEY);
      return v === 'fil' ? 'fil' : 'en';
    } catch (_e) {
      return 'en';
    }
  }

  function setLang(code) {
    var lang = code === 'fil' ? 'fil' : 'en';
    try {
      localStorage.setItem(STORAGE_KEY, lang);
    } catch (_l) {}
    try {
      sessionStorage.setItem(STORAGE_KEY, lang);
    } catch (_s) {}
    try {
      document.documentElement.lang = lang === 'fil' ? 'fil' : 'en';
    } catch (_d) {}
    return lang;
  }

  function text(el, value) {
    if (!el) return;
    el.textContent = value;
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
    var phoneLbl = document.querySelector('label[for="login-phone-local"]');
    var pwLbl = document.querySelector('label[for="login-password"]');
    text(phoneLbl, L.phone);
    text(pwLbl, L.password);
    var remember = document.querySelector('.login-remember__text');
    text(remember, L.remember);
    var forgot = document.querySelector('.login-forgot');
    text(forgot, L.forgot);
    var submit = document.querySelector('.login-submit-btn');
    text(submit, L.submit);
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

  window.BeanthenticAuthLang = {
    STORAGE_KEY: STORAGE_KEY,
    getLang: getLang,
    setLang: setLang,
    applyLoginAuthLang: applyLoginAuthLang,
    applySignupAuthLang: applySignupAuthLang
  };
})();
