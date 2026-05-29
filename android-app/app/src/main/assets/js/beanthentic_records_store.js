(function (global) {
  var KEY_PREFIX = 'beanthentic_farmer_records';
  var MAX = 200;
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

  function clearDemoCache() {
    try {
      var user = parseUser();
      var key = storageKeyForUser(user);
      localStorage.removeItem(key);
      sessionStorage.removeItem(key);
    } catch (_e) {}
  }

  function fetchPendingFromApi() {
    lastFetchError = '';
    var user = parseUser();
    if (!user || !user.user_id) {
      lastFetchError = 'Sign in on the app to load records from the database.';
      return Promise.resolve([]);
    }
    if (!global.BeanthenticApiUrls) {
      lastFetchError = 'API helper not loaded.';
      return Promise.resolve([]);
    }
    return global.BeanthenticApiUrls.fetchApiSequential(
      'farmer_pending_records.php',
      { user_id: user.user_id },
      { timeoutMs: 10000, maxTries: 4 }
    ).then(function (body) {
      if (!body || body.ok !== true) {
        lastFetchError = (body && body.error) ? String(body.error) : 'Could not load records from database.';
        return [];
      }
      if (!Array.isArray(body.records)) {
        lastFetchError = 'Invalid records response from server.';
        return [];
      }
      return body.records;
    }).catch(function () {
      lastFetchError = 'Cannot reach app server API. Start XAMPP MySQL and python app.py on port 8080.';
      return [];
    });
  }

  function load() {
    return loadLocal();
  }

  function getLastFetchError() {
    return lastFetchError;
  }

  function refreshFromDatabase() {
    return fetchPendingFromApi().then(function (records) {
      saveLocal(records);
      return records;
    });
  }

  function ensureSeed() {
    clearDemoCache();
    return refreshFromDatabase();
  }

  function update(id, patch) {
    var list = loadLocal();
    var idx = -1;
    for (var i = 0; i < list.length; i++) {
      if (list[i].id === id) {
        idx = i;
        break;
      }
    }
    if (idx < 0) return null;
    var next = {};
    var src = list[idx];
    for (var k in src) {
      if (Object.prototype.hasOwnProperty.call(src, k)) next[k] = src[k];
    }
    for (var p in patch) {
      if (Object.prototype.hasOwnProperty.call(patch, p)) next[p] = patch[p];
    }
    list[idx] = next;
    saveLocal(list);
    return next;
  }

  function postAction(rec, action) {
    var user = parseUser();
    var txId = parseInt(String(rec.customer_transaction_id || '0'), 10);
    if (!user || !user.user_id || !(txId > 0) || !global.BeanthenticApiUrls) {
      return Promise.resolve(null);
    }
    return global.BeanthenticApiUrls.fetchApiSequential(
      'farmer_record_action.php',
      {
        user_id: user.user_id,
        customer_transaction_id: txId,
        action: action
      },
      { timeoutMs: 8000, maxTries: 2 }
    ).then(function (body) {
      if (!body || body.ok !== true) return null;
      return body;
    }).catch(function () {
      return null;
    });
  }

  function approve(id) {
    var rec = null;
    var list = loadLocal();
    for (var i = 0; i < list.length; i++) {
      if (list[i].id === id) {
        rec = list[i];
        break;
      }
    }
    var approvedAt = new Date().toISOString();
    var approvedRec = rec
      ? (function () {
          var o = {};
          for (var k in rec) {
            if (Object.prototype.hasOwnProperty.call(rec, k)) o[k] = rec[k];
          }
          o.status = 'approved';
          o.approvedAt = approvedAt;
          return o;
        })()
      : null;

    return postAction(rec || {}, 'approve').then(function (ok) {
      if (!ok) return null;
      var list2 = loadLocal().filter(function (r) {
        return r.id !== id;
      });
      saveLocal(list2);
      return approvedRec;
    });
  }

  function dismiss(id) {
    var rec = null;
    var list = loadLocal();
    for (var i = 0; i < list.length; i++) {
      if (list[i].id === id) {
        rec = list[i];
        break;
      }
    }
    return postAction(rec || {}, 'dismiss').then(function (ok) {
      if (!ok) return null;
      var list2 = loadLocal().filter(function (r) {
        return r.id !== id;
      });
      saveLocal(list2);
      return { id: id, status: 'dismissed' };
    });
  }

  global.BeanthenticRecords = {
    KEY: KEY_PREFIX,
    storageKeyForUser: storageKeyForUser,
    load: load,
    save: saveLocal,
    clearDemoCache: clearDemoCache,
    ensureSeed: ensureSeed,
    refreshFromDatabase: refreshFromDatabase,
    getLastFetchError: getLastFetchError,
    approve: approve,
    dismiss: dismiss
  };
})(window);
