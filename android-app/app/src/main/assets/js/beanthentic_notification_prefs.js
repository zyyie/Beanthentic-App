/**
 * Notification alert preferences (localStorage).
 */
(function (global) {
  var NOTIF_KEY = 'beanthentic_notification_prefs';

  var NOTIF_IDS = [
    'notif-login-verification',
    'notif-suspicious-login',
    'notif-account-updates',
    'notif-security-reminders',
    'notif-app-updates'
  ];

  var DEFAULTS = {
    'notif-login-verification': true,
    'notif-suspicious-login': true,
    'notif-account-updates': true,
    'notif-security-reminders': false,
    'notif-app-updates': false
  };

  function readRawPrefs() {
    try {
      var raw = localStorage.getItem(NOTIF_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch (_e) {
      return {};
    }
  }

  function resolvePref(id, prefs) {
    if (typeof prefs[id] === 'boolean') return prefs[id];
    if (id === 'notif-account-updates' && typeof prefs['notif-updates'] === 'boolean') {
      return prefs['notif-updates'];
    }
    if (id === 'notif-suspicious-login') {
      if (typeof prefs['notif-email-breaches'] === 'boolean') return prefs['notif-email-breaches'];
      if (typeof prefs['notif-inapp-breaches'] === 'boolean') return prefs['notif-inapp-breaches'];
    }
    if (id === 'notif-app-updates') {
      if (typeof prefs['notif-email-system'] === 'boolean') return prefs['notif-email-system'];
      if (typeof prefs['notif-inapp-system'] === 'boolean') return prefs['notif-inapp-system'];
    }
    if (id === 'notif-login-verification') {
      if (typeof prefs['notif-email-registrations'] === 'boolean') return prefs['notif-email-registrations'];
      if (typeof prefs['notif-inapp-registrations'] === 'boolean') return prefs['notif-inapp-registrations'];
    }
    return DEFAULTS[id] === true;
  }

  function loadNotifPrefs(root) {
    var scope = root && root.querySelector ? root : document;
    var prefs = readRawPrefs();
    NOTIF_IDS.forEach(function (id) {
      var el = scope.getElementById(id);
      if (!el) return;
      el.checked = resolvePref(id, prefs);
    });
  }

  function saveNotifPrefs(root) {
    var scope = root && root.querySelector ? root : document;
    var prefsOut = readRawPrefs();
    NOTIF_IDS.forEach(function (id) {
      var el = scope.getElementById(id);
      if (!el) return;
      prefsOut[id] = !!el.checked;
    });
    delete prefsOut['notif-updates'];
    try {
      localStorage.setItem(NOTIF_KEY, JSON.stringify(prefsOut));
    } catch (_e) { }
    return prefsOut;
  }

  function isAlertEnabled(alertId) {
    var prefs = readRawPrefs();
    var id = alertId;
    if (NOTIF_IDS.indexOf(id) < 0) return true;
    return resolvePref(id, prefs);
  }

  global.BeanthenticNotifPrefs = {
    NOTIF_KEY: NOTIF_KEY,
    NOTIF_IDS: NOTIF_IDS,
    DEFAULTS: DEFAULTS,
    load: loadNotifPrefs,
    save: saveNotifPrefs,
    isEnabled: isAlertEnabled
  };
})(typeof window !== 'undefined' ? window : this);
