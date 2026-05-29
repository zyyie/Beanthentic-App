/**
 * Farmer profile + photo in localStorage; sync to MySQL; notify all pages on change.
 */
(function (global) {
  var PROFILE_KEY = 'beanthentic_farmer_profile';
  var MAP_KEY = 'beanthentic_farmer_profile_map';
  var USER_KEY = 'beanthentic_user';

  function parseJson(raw) {
    if (!raw) return null;
    try {
      var o = JSON.parse(raw);
      return o && typeof o === 'object' ? o : null;
    } catch (_e) {
      return null;
    }
  }

  function getSessionUser() {
    var u = parseJson(localStorage.getItem(USER_KEY));
    if (u && u.email) return u;
    u = parseJson(sessionStorage.getItem(USER_KEY));
    return u && u.email ? u : null;
  }

  function signedInKey() {
    var u = getSessionUser();
    if (!u) return '';
    var email = String(u.email || '').trim().toLowerCase();
    if (email) return email;
    return String(u.phone || u.mobile || '').trim().toLowerCase();
  }

  function keyVariants(v) {
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

  function farmerProfilePhotoSrc(profile) {
    if (!profile || typeof profile !== 'object') return '';
    var raw = String(profile.profile_photo_data || profile.profile_photo || '').trim();
    if (!raw) return '';
    if (/^data:image\//i.test(raw) || /^https?:\/\//i.test(raw)) return raw;
    if (raw.charAt(0) === '/' && raw.length > 4) {
      try {
        return new URL(raw, global.location.href).href;
      } catch (_u) {
        return raw;
      }
    }
    var compact = raw.replace(/\s/g, '');
    if (/^[A-Za-z0-9+/=]+$/.test(compact) && compact.length > 240) {
      return 'data:image/jpeg;base64,' + compact;
    }
    return '';
  }

  function getFarmerProfile(emailOrKey) {
    var key = String(emailOrKey || signedInKey() || '').trim().toLowerCase();
    if (key) {
      var map = parseJson(localStorage.getItem(MAP_KEY)) || parseJson(sessionStorage.getItem(MAP_KEY));
      if (map && typeof map === 'object') {
        var keys = keyVariants(key);
        for (var i = 0; i < keys.length; i += 1) {
          if (map[keys[i]] && typeof map[keys[i]] === 'object') return map[keys[i]];
        }
      }
    }
    return parseJson(localStorage.getItem(PROFILE_KEY)) || parseJson(sessionStorage.getItem(PROFILE_KEY));
  }

  function cacheProfile(profile) {
    if (!profile || typeof profile !== 'object') return;
    var key = signedInKey();
    if (!key) return;
    try {
      var map = parseJson(localStorage.getItem(MAP_KEY)) || {};
      var keys = keyVariants(key);
      var last = profile;
      for (var i = 0; i < keys.length; i += 1) {
        var kk = keys[i];
        var prev = map[kk];
        var merged = Object.assign({}, prev || {}, profile);
        map[kk] = merged;
        last = merged;
      }
      localStorage.setItem(MAP_KEY, JSON.stringify(map));
      sessionStorage.setItem(MAP_KEY, JSON.stringify(map));
      localStorage.setItem(PROFILE_KEY, JSON.stringify(last));
      sessionStorage.setItem(PROFILE_KEY, JSON.stringify(last));
    } catch (_e) { }
  }

  function dispatchChanged(profile) {
    try {
      global.dispatchEvent(
        new CustomEvent('beanthentic-profile-changed', { detail: { profile: profile || null } })
      );
    } catch (_e) { }
  }

  function applyAvatar(opts) {
    opts = opts || {};
    var profile = opts.profile != null ? opts.profile : getFarmerProfile();
    var photoRaw = farmerProfilePhotoSrc(profile);
    var img = opts.imgEl;
    var wrap = opts.wrapEl;
    var initialsEl = opts.initialsEl;
    var name = opts.name || '';
    if (initialsEl && name) {
      var parts = String(name).trim().split(/\s+/).filter(Boolean);
      var ini =
        parts.length >= 2
          ? (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
          : parts.length === 1
            ? parts[0].slice(0, 2).toUpperCase()
            : 'BA';
      initialsEl.textContent = ini;
    }
    if (!img || !wrap) return;
    if (
      /^data:image\//i.test(photoRaw) ||
      /^https?:\/\//i.test(photoRaw) ||
      (photoRaw.charAt(0) === '/' && photoRaw.length > 4)
    ) {
      if (/^data:image\//i.test(photoRaw)) {
        img.src = photoRaw;
      } else {
        img.src = photoRaw + (photoRaw.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
      }
      img.removeAttribute('hidden');
      wrap.classList.add('has-photo');
    } else {
      try {
        img.removeAttribute('src');
      } catch (_rs) { }
      img.setAttribute('hidden', '');
      wrap.classList.remove('has-photo');
    }
  }

  function readFileAsDataUrl(file) {
    return new Promise(function (resolve, reject) {
      if (!file) {
        reject(new Error('No file'));
        return;
      }
      var reader = new FileReader();
      reader.onload = function () {
        resolve(String(reader.result || ''));
      };
      reader.onerror = function () {
        reject(reader.error || new Error('Could not read file'));
      };
      reader.readAsDataURL(file);
    });
  }

  function apiPostProfileUpdate(payload) {
    var urls = [];
    if (global.BeanthenticApiUrls && typeof global.BeanthenticApiUrls.phpApiUrlCandidates === 'function') {
      urls = global.BeanthenticApiUrls.phpApiUrlCandidates('farmer_profile_update.php');
    }
    try {
      if (global.location && /^https?:$/i.test(global.location.protocol)) {
        urls.push(global.location.origin + '/api/farmer_profile_update.php');
      }
    } catch (_o) { }
    var bases = [];
    try {
      var api = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
      if (api) bases.push(String(api).replace(/\/+$/, ''));
    } catch (_b) { }
    bases.forEach(function (b) {
      if (b) urls.push(b + '/api/farmer_profile_update.php');
    });
    urls = Array.from(new Set(urls.filter(Boolean)));

    var body = JSON.stringify(payload);
    var tryUrl = function (idx) {
      if (idx >= urls.length) {
        return Promise.reject(new Error('Could not reach profile API'));
      }
      return fetch(urls[idx], {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: body,
      })
        .then(function (res) {
          return res.json().then(function (data) {
            if (!res.ok || !data || !data.success) {
              var err = (data && (data.error || data.message)) || 'Update failed';
              throw new Error(err);
            }
            return data;
          });
        })
        .catch(function () {
          return tryUrl(idx + 1);
        });
    };
    return tryUrl(0);
  }

  function updateProfilePhoto(dataUrl) {
    var u = getSessionUser();
    if (!u) return Promise.reject(new Error('Please log in first'));
    var userId = parseInt(u.user_id, 10) || 0;
    var data = String(dataUrl || '').trim();
    if (!/^data:image\/(jpeg|jpg|png|webp);base64,/i.test(data)) {
      return Promise.reject(new Error('Invalid image'));
    }

    var profile = getFarmerProfile() || {};
    var patch = Object.assign({}, profile, {
      profile_photo_data: data,
      profile_photo: data,
    });
    cacheProfile(patch);
    dispatchChanged(patch);

    var payload = {
      user_id: userId,
      email: u.email || '',
      phone: u.phone || u.mobile || '',
      profile_photo_data: data,
    };

    return apiPostProfileUpdate(payload).then(function (res) {
      var serverPath = String(res.profile_photo || '').trim();
      if (serverPath) {
        patch.profile_photo = serverPath;
        patch.profile_photo_data = serverPath;
        cacheProfile(patch);
        dispatchChanged(patch);
      }
      return patch;
    });
  }

  global.BeanthenticProfileStore = {
    getSessionUser: getSessionUser,
    getFarmerProfile: getFarmerProfile,
    cacheProfile: cacheProfile,
    farmerProfilePhotoSrc: farmerProfilePhotoSrc,
    applyAvatar: applyAvatar,
    readFileAsDataUrl: readFileAsDataUrl,
    updateProfilePhoto: updateProfilePhoto,
    dispatchChanged: dispatchChanged,
  };
})(typeof window !== 'undefined' ? window : globalThis);
