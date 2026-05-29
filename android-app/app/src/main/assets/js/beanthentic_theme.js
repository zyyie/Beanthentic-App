/**

 * App theme: light, dark, or system (follows device).

 */

(function (global) {

  var KEY = 'beanthentic_app_theme';

  var THEME_CSS_VER = '20260527-9';

  var LATE_IDS = ['beanthentic-theme-css-late', 'beanthentic-theme-modules-late'];



  function getStored() {

    try {

      var v = localStorage.getItem(KEY) || sessionStorage.getItem(KEY);

      if (v === 'dark' || v === 'system' || v === 'light') return v;

    } catch (_e) { }

    return 'light';

  }



  function resolveEffective(mode) {

    if (mode === 'dark') return 'dark';

    if (mode === 'light') return 'light';

    try {

      return global.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

    } catch (_m) {

      return 'light';

    }

  }



  function updateMetaThemeColor(effective) {

    try {

      var meta = document.querySelector('meta[name="theme-color"]');

      if (meta) meta.setAttribute('content', effective === 'dark' ? '#121820' : '#508020');

    } catch (_meta) { }

  }



  function appendLateStylesheet(id, href) {

    if (document.getElementById(id)) return;

    var link = document.createElement('link');

    link.id = id;

    link.rel = 'stylesheet';

    link.href = href;

    (document.body || document.head).appendChild(link);

  }



  function removeLateStylesheets() {

    LATE_IDS.forEach(function (id) {

      var el = document.getElementById(id);

      if (el && el.parentNode) el.parentNode.removeChild(el);

    });

  }



  /** Load theme CSS last so dark rules beat page-inline <style> in body. */

  function syncLateThemeStyles(effective) {

    if (effective === 'dark') {

      appendLateStylesheet('beanthentic-theme-css-late', 'css/theme.css?v=' + THEME_CSS_VER);

      appendLateStylesheet(

        'beanthentic-theme-modules-late',

        'css/theme-modules.css?v=' + THEME_CSS_VER

      );

    } else {

      removeLateStylesheets();

    }

  }



  function applyTheme(mode, options) {

    var m = mode === 'dark' || mode === 'system' || mode === 'light' ? mode : 'light';

    var skipSave = options && options.skipSave;

    if (!skipSave) {

      try {

        localStorage.setItem(KEY, m);

        sessionStorage.setItem(KEY, m);

      } catch (_s) { }

    }

    var effective = resolveEffective(m);

    var root = document.documentElement;

    root.setAttribute('data-beanthentic-theme', m);

    root.setAttribute('data-beanthentic-theme-effective', effective);

    if (document.body) {

      document.body.classList.toggle('beanthentic-dark-mode', effective === 'dark');

    }

    updateMetaThemeColor(effective);

    syncLateThemeStyles(effective);

    try {

      global.dispatchEvent(

        new CustomEvent('beanthentic-theme-changed', {

          detail: { mode: m, effective: effective }

        })

      );

    } catch (_ev) { }

    return { mode: m, effective: effective };

  }



  function bootEarly() {

    try {

      var t = getStored();

      var effective = resolveEffective(t);

      document.documentElement.setAttribute('data-beanthentic-theme', t);

      document.documentElement.setAttribute('data-beanthentic-theme-effective', effective);

    } catch (_b) { }

  }



  function bindSystemListener() {

    try {

      var mq = global.matchMedia('(prefers-color-scheme: dark)');

      var handler = function () {

        if (getStored() === 'system') applyTheme('system', { skipSave: true });

      };

      if (typeof mq.addEventListener === 'function') mq.addEventListener('change', handler);

      else if (typeof mq.addListener === 'function') mq.addListener(handler);

    } catch (_mq) { }

  }



  function init() {

    applyTheme(getStored(), { skipSave: true });

    bindSystemListener();

    if (document.body) {

      document.body.classList.toggle(

        'beanthentic-dark-mode',

        resolveEffective(getStored()) === 'dark'

      );

    }

  }



  bootEarly();



  if (document.readyState === 'loading') {

    document.addEventListener('DOMContentLoaded', init);

  } else {

    init();

  }



  global.BeanthenticTheme = {

    KEY: KEY,

    getStored: getStored,

    getEffective: function () {

      return resolveEffective(getStored());

    },

    applyTheme: applyTheme

  };

})(typeof window !== 'undefined' ? window : this);


