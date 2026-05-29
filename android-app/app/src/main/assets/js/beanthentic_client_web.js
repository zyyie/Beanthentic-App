/**
 * Client Web profile URLs for farmer QR codes.
 * Each account gets a unique URL from XAMPP farmers.farmer_id:
 *   {client_web_base}/farmer/{farmer_id}
 */
(function (global) {
  var STORAGE_KEY = 'beanthentic_client_web_base';
  var MAP_KEY = 'beanthentic_farmer_id_map';

  function parseUrl(s) {
    try {
      return new URL(String(s || '').trim());
    } catch (_e) {
      return null;
    }
  }

  function keyVariantsProfile(v) {
    var out = [];
    var k = String(v || '').trim().toLowerCase();
    if (!k) return out;
    out.push(k);
    var d = k.replace(/\D/g, '');
    if (d) {
      if (d.indexOf('63') === 0 && d.length >= 12) out.push('0' + d.slice(2));
      if (d.indexOf('0') === 0 && d.length >= 11) out.push('+63' + d.slice(1));
      if (d.length === 10 && d.charAt(0) === '9') {
        out.push('0' + d);
        out.push('+63' + d);
      }
    }
    return Array.from(new Set(out));
  }

  function loginKeysForUser(user, loginKey) {
    var keys = [];
    if (loginKey) keys.push(String(loginKey).trim());
    if (user) {
      if (user.email) keys.push(String(user.email).trim());
      if (user.phone_number) keys.push(String(user.phone_number).trim());
      if (user.phone) keys.push(String(user.phone).trim());
    }
    var seen = {};
    var out = [];
    keys.forEach(function (k) {
      var v = String(k || '').trim();
      if (!v || seen[v]) return;
      seen[v] = true;
      out.push(v);
    });
    return out;
  }

  function readMap() {
    try {
      var raw =
        localStorage.getItem(MAP_KEY) || sessionStorage.getItem(MAP_KEY);
      var map = raw ? JSON.parse(raw) : {};
      return map && typeof map === 'object' ? map : {};
    } catch (_e) {
      return {};
    }
  }

  function writeMap(map) {
    try {
      var serialized = JSON.stringify(map || {});
      localStorage.setItem(MAP_KEY, serialized);
      sessionStorage.setItem(MAP_KEY, serialized);
    } catch (_e) { }
  }

  function isLoopbackHost(host) {
    var h = String(host || '').toLowerCase();
    return (
      h === 'localhost' ||
      h === '127.0.0.1' ||
      h === '[::1]' ||
      h === '0.0.0.0'
    );
  }

  /** Same Wi‑Fi host as this page, Client Web on port 5001 (phones cannot use 127.0.0.1). */
  function deriveClientWebFromPage() {
    try {
      if (typeof location === 'undefined') return '';
      if (location.protocol !== 'http:' && location.protocol !== 'https:') return '';
      var u = new URL(location.href);
      if (isLoopbackHost(u.hostname)) return '';
      return u.protocol + '//' + u.hostname + ':5001';
    } catch (_e) {
      return '';
    }
  }

  function resolveClientWebBase() {
    var candidates = [];
    try {
      var stored =
        localStorage.getItem(STORAGE_KEY) || sessionStorage.getItem(STORAGE_KEY);
      if (stored) candidates.push(String(stored).trim());
    } catch (_s) { }
    var fromPage = deriveClientWebFromPage();
    if (fromPage) candidates.push(fromPage);
    try {
      if (global.__BEANTHENTIC_CLIENT_WEB_BASE__) {
        var inj = String(global.__BEANTHENTIC_CLIENT_WEB_BASE__).trim();
        var iu = parseUrl(inj);
        if (iu && !isLoopbackHost(iu.hostname)) candidates.push(inj);
      }
    } catch (_i) { }

    for (var i = 0; i < candidates.length; i++) {
      var raw = String(candidates[i] || '').trim().replace(/\/+$/, '');
      if (!raw) continue;
      var u = parseUrl(raw);
      if (!u || (u.protocol !== 'http:' && u.protocol !== 'https:')) continue;
      if (isLoopbackHost(u.hostname)) continue;
      try {
        localStorage.setItem(STORAGE_KEY, raw);
        sessionStorage.setItem(STORAGE_KEY, raw);
      } catch (_w) { }
      return raw;
    }
    var fallback = fromPage || deriveClientWebFromPage();
    if (fallback) return fallback.replace(/\/+$/, '');
    return 'http://192.168.0.100:5001';
  }

  function buildProfileUrl(farmerId, user, loginKey) {
    var base = resolveClientWebBase();
    var id = parseInt(String(farmerId || '0'), 10);
    if (id > 0) return base + '/farmer/' + id;
    return base + '/';
  }

  /**
   * Save farmer_id from XAMPP for this login (phone/email). New signup = new farmer_id = new QR.
   */
  function persistFarmerIdForUser(user, farmerId, loginKey) {
    var fid = parseInt(String(farmerId || '0'), 10);
    if (!(fid > 0)) return 0;

    if (user && typeof user === 'object') {
      user.farmer_id = fid;
    }

    var map = readMap();
    var keys = loginKeysForUser(user, loginKey);
    keys.forEach(function (k) {
      keyVariantsProfile(k).forEach(function (variant) {
        map[variant] = String(fid);
      });
    });
    writeMap(map);

    try {
      localStorage.setItem('beanthentic_farmer_id', String(fid));
      sessionStorage.setItem('beanthentic_farmer_id', String(fid));
      if (user) {
        var payload = JSON.stringify(user);
        localStorage.setItem('beanthentic_user', payload);
        sessionStorage.setItem('beanthentic_user', payload);
      }
    } catch (_e) { }

    return fid;
  }

  /**
   * Resolve farmer_id for the logged-in user only (never another account's global id).
   */
  function resolveFarmerId(user, loginKey) {
    if (user && user.farmer_id != null) {
      var fromUser = parseInt(String(user.farmer_id), 10);
      if (fromUser > 0) return fromUser;
    }

    var map = readMap();
    var keys = loginKeysForUser(user, loginKey);
    for (var ki = 0; ki < keys.length; ki++) {
      var variants = keyVariantsProfile(keys[ki]);
      for (var vi = 0; vi < variants.length; vi++) {
        var mapped = parseInt(String(map[variants[vi]] || '0'), 10);
        if (mapped > 0) return mapped;
      }
    }

    return 0;
  }

  function fetchFarmerIdFromDatabase(user, loginKey) {
    if (!global.BeanthenticApiUrls) {
      return Promise.resolve(0);
    }
    var login =
      loginKey ||
      String(
        (user && (user.phone_number || user.email || user.phone)) || ''
      ).trim();
    if (!login && !(user && user.user_id)) {
      return Promise.resolve(0);
    }

    var payload = { user_id: (user && user.user_id) || 0, login: login };
    if (login.indexOf('@') >= 0) payload.email = login;
    else if (login) payload.phone_number = login;

    return global.BeanthenticApiUrls.fetchApiSequential(
      'registration_status.php',
      payload,
      { timeoutMs: 5000, maxTries: 3 }
    ).then(function (body) {
      if (!body || body.ok !== true) return 0;
      var fid = parseInt(String(body.farmer_id || '0'), 10);
      return fid > 0 ? fid : 0;
    }).catch(function () {
      return 0;
    });
  }

  function setAccountQrImageRemote(qrImgEl, profileUrl) {
    if (!qrImgEl || !profileUrl) return;
    var remoteUrl =
      'https://api.qrserver.com/v1/create-qr-code/?size=640x640&margin=1&data=' +
      encodeURIComponent(profileUrl);
    qrImgEl.onload = function () {
      try {
        qrImgEl.removeAttribute('hidden');
      } catch (_h) { }
    };
    qrImgEl.onerror = function () {
      try {
        qrImgEl.setAttribute('hidden', '');
      } catch (_e) { }
    };
    qrImgEl.src = remoteUrl;
    try {
      qrImgEl.dataset.downloadUrl = remoteUrl;
    } catch (_d) { }
  }

  function setAccountQrImage(qrImgEl, profileUrl, attempt) {
    if (!qrImgEl || !profileUrl) return;
    var tries = attempt || 0;
    try {
      qrImgEl.dataset.profileUrl = profileUrl;
    } catch (_d0) { }

    // Show QR immediately (CDN / local lib may load later).
    if (!qrImgEl.src || tries === 0) {
      setAccountQrImageRemote(qrImgEl, profileUrl);
    }

    if (global.QRCode && typeof global.QRCode.toDataURL === 'function') {
      global.QRCode.toDataURL(
        profileUrl,
        { width: 280, margin: 1, errorCorrectionLevel: 'M' },
        function (err, dataUrl) {
          if (!err && dataUrl) {
            qrImgEl.onload = function () {
              try {
                qrImgEl.removeAttribute('hidden');
              } catch (_h) { }
            };
            qrImgEl.src = dataUrl;
            try {
              qrImgEl.dataset.downloadUrl = dataUrl;
            } catch (_d1) { }
            return;
          }
          setAccountQrImageRemote(qrImgEl, profileUrl);
        }
      );
      return;
    }
    if (tries < 80) {
      setTimeout(function () {
        setAccountQrImage(qrImgEl, profileUrl, tries + 1);
      }, 50);
      return;
    }
    setAccountQrImageRemote(qrImgEl, profileUrl);
  }

  function paintAccountQr(farmerId, user, loginKey) {
    var qr = document.getElementById('account-qr-img');
    var urlEl = document.getElementById('account-qr-profile-url');
    var idEl = document.getElementById('account-qr-farmer-id');
    var fid = parseInt(String(farmerId || '0'), 10);
    var profileUrl = buildProfileUrl(fid, user, loginKey);

    if (idEl) {
      idEl.textContent = fid > 0 ? 'Farmer ID: ' + fid : 'Walang farmer ID sa database';
    }
    if (urlEl) {
      urlEl.textContent = profileUrl;
      urlEl.hidden = !profileUrl;
    }
    if (qr) {
      setAccountQrImage(qr, profileUrl);
    }
    return fid;
  }

  /**
   * Load farmer_id from XAMPP then build unique QR for this account.
   */
  function refreshAccountQrFromUser(user, loginKey) {
    if (!user) return Promise.resolve(0);

    var cached = resolveFarmerId(user, loginKey);
    if (cached > 0) {
      paintAccountQr(cached, user, loginKey);
    }

    return fetchFarmerIdFromDatabase(user, loginKey).then(function (dbId) {
      var fid = dbId > 0 ? dbId : cached;
      if (fid > 0) {
        persistFarmerIdForUser(user, fid, loginKey);
      }
      paintAccountQr(fid, user, loginKey);
      return fid;
    });
  }

  global.BeanthenticClientWeb = {
    resolveClientWebBase: resolveClientWebBase,
    resolveFarmerId: resolveFarmerId,
    persistFarmerIdForUser: persistFarmerIdForUser,
    fetchFarmerIdFromDatabase: fetchFarmerIdFromDatabase,
    buildProfileUrl: buildProfileUrl,
    paintAccountQr: paintAccountQr,
    refreshAccountQrFromUser: refreshAccountQrFromUser,
    STORAGE_KEY: STORAGE_KEY,
    MAP_KEY: MAP_KEY,
  };
})(typeof window !== 'undefined' ? window : this);
