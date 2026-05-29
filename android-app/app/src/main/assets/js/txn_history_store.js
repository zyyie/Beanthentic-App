(function (global) {
  var KEY_PREFIX = 'beanthentic_tx_history';
  var MAX = 300;
  var lastFetchError = '';

  function storageKeyForUser(user) {
    var uid = user && user.user_id != null ? String(user.user_id) : '';
    return uid ? KEY_PREFIX + '_u' + uid : KEY_PREFIX;
  }

  function parseUser() {
    try {
      var raw =
        sessionStorage.getItem('beanthentic_user') || localStorage.getItem('beanthentic_user');
      if (!raw) return null;
      var u = JSON.parse(raw);
      return u && typeof u === 'object' ? u : null;
    } catch (_e) {
      return null;
    }
  }

  function loadLocal() {
    try {
      var user = parseUser();
      var key = storageKeyForUser(user);
      var raw = localStorage.getItem(key);
      if (!raw) return [];
      var a = JSON.parse(raw);
      return Array.isArray(a) ? a : [];
    } catch (_e) {
      return [];
    }
  }

  function saveLocal(list) {
    try {
      var user = parseUser();
      var key = storageKeyForUser(user);
      localStorage.setItem(key, JSON.stringify((list || []).slice(0, MAX)));
    } catch (_e) {}
  }

  function fetchFromDatabase() {
    lastFetchError = '';
    var user = parseUser();
    if (!user || !user.user_id || !global.BeanthenticApiUrls) {
      lastFetchError = 'Sign in to load transaction history from the database.';
      return Promise.resolve([]);
    }
    return global.BeanthenticApiUrls.fetchApiSequential(
      'farmer_transaction_history.php',
      { user_id: user.user_id },
      { timeoutMs: 10000, maxTries: 4 }
    )
      .then(function (body) {
        if (!body || body.ok !== true || !Array.isArray(body.records)) {
          lastFetchError =
            (body && body.error) || 'Could not load transaction history from database.';
          return [];
        }
        return body.records;
      })
      .catch(function () {
        lastFetchError =
          'Cannot reach app server. Start XAMPP MySQL and python app.py (port 8080).';
        return [];
      });
  }

  function refreshFromDatabase() {
    return fetchFromDatabase().then(function (records) {
      saveLocal(records);
      return records;
    });
  }

  function load() {
    return loadLocal();
  }

  function getLastFetchError() {
    return lastFetchError;
  }

  function pushRecord(rec) {
    if (!rec || !rec.ref) return;
    var list = loadLocal();
    var exists = false;
    for (var i = 0; i < list.length; i++) {
      if (list[i].ref === rec.ref) {
        list[i] = Object.assign({}, list[i], rec);
        exists = true;
        break;
      }
    }
    if (!exists) list.unshift(rec);
    saveLocal(list);
  }

  function markSentByRef(ref) {
    if (!ref) return;
    var list = loadLocal();
    var changed = false;
    for (var i = 0; i < list.length; i++) {
      if (list[i].ref === ref) {
        list[i].sentToClient = true;
        list[i].sentAt = new Date().toISOString();
        list[i].status = 'sent_to_client';
        changed = true;
        break;
      }
    }
    if (changed) saveLocal(list);
    refreshFromDatabase();
  }

  global.BeanthenticTxnHistory = {
    KEY: KEY_PREFIX,
    storageKeyForUser: storageKeyForUser,
    load: load,
    save: saveLocal,
    push: pushRecord,
    markSentByRef: markSentByRef,
    refreshFromDatabase: refreshFromDatabase,
    getLastFetchError: getLastFetchError
  };
})(window);
