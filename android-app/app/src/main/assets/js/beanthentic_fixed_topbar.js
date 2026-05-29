/**
 * Pins the primary <header> bar with layout.css (position: fixed) and sets
 * --beanthentic-fixed-topbar-h + body/html classes so content is not hidden.
 * Also scrolls to top on load (unless URL has a fragment) and disables automatic scroll restoration.
 */
(function () {
  var TOPBAR_FALLBACK = 'calc(env(safe-area-inset-top, 0px) + 5.5rem)';

  function bodySkipsTopbarPadding() {
    try {
      if (!document.body) return false;
      var c = document.body.classList;
      return (
        c.contains('login-page') ||
        c.contains('signup-page') ||
        c.contains('tutorial-page') ||
        c.contains('choose-lang-page') ||
        c.contains('server-url-page')
      );
    } catch (_sk) {
      return false;
    }
  }

  function applyTopbarFallback() {
    try {
      document.documentElement.style.setProperty('--beanthentic-fixed-topbar-h', TOPBAR_FALLBACK);
      if (bodySkipsTopbarPadding()) return;
      document.documentElement.classList.add('beanthentic-fixed-topbar-active');
      if (document.body) document.body.classList.add('beanthentic-fixed-topbar-active');
    } catch (_fb) {}
  }

  applyTopbarFallback();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyTopbarFallback);
  }

  try {
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
  } catch (_sr) {}

  function scrollToTopUnlessFragment() {
    try {
      var h = typeof location !== 'undefined' ? location.hash || '' : '';
      if (h && h.length > 1) return;
      window.scrollTo(0, 0);
      if (document.documentElement) document.documentElement.scrollTop = 0;
      if (document.body) document.body.scrollTop = 0;
    } catch (_e) {}
  }

  function findVisibleHeader() {
    var nodes = document.querySelectorAll('header');
    for (var i = 0; i < nodes.length; i++) {
      var el = nodes[i];
      try {
        var st = window.getComputedStyle(el);
        if (st.display === 'none' || st.visibility === 'hidden') continue;
        if (Number(st.opacity) === 0) continue;
        return el;
      } catch (_e) {
        return el;
      }
    }
    return null;
  }

  function apply() {
    try {
      var h = findVisibleHeader();
      if (!h) {
        document.documentElement.classList.remove('beanthentic-fixed-topbar-active');
        if (document.body) document.body.classList.remove('beanthentic-fixed-topbar-active');
        document.documentElement.style.setProperty('--beanthentic-fixed-topbar-h', TOPBAR_FALLBACK);
        return;
      }
      var rect = h.getBoundingClientRect();
      var height = Math.max(0, Math.ceil(rect.height));
      document.documentElement.style.setProperty('--beanthentic-fixed-topbar-h', height + 'px');
      document.documentElement.classList.add('beanthentic-fixed-topbar-active');
      document.body.classList.add('beanthentic-fixed-topbar-active');
    } catch (_e2) {}
  }

  function start() {
    scrollToTopUnlessFragment();
    apply();
    requestAnimationFrame(function () {
      scrollToTopUnlessFragment();
      apply();
      requestAnimationFrame(function () {
        scrollToTopUnlessFragment();
        apply();
      });
    });
    window.addEventListener('resize', apply);
    window.addEventListener('orientationchange', function () {
      setTimeout(apply, 220);
    });
    try {
      var el = findVisibleHeader();
      if (el && typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(apply).observe(el);
      }
    } catch (_r) {}
  }

  window.addEventListener('load', function () {
    scrollToTopUnlessFragment();
    apply();
  });

  window.addEventListener('pageshow', function (ev) {
    if (ev.persisted) {
      scrollToTopUnlessFragment();
      apply();
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
