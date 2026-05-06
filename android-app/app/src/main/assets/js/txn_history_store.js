(function (global) {
  var KEY = 'beanthentic_tx_history';
  var MAX = 200;

  function load() {
    try {
      var raw = localStorage.getItem(KEY);
      if (!raw) return [];
      var a = JSON.parse(raw);
      return Array.isArray(a) ? a : [];
    } catch (_e) {
      return [];
    }
  }

  function save(list) {
    try {
      localStorage.setItem(KEY, JSON.stringify(list.slice(0, MAX)));
    } catch (_e) {}
  }

  function pushRecord(rec) {
    if (!rec || !rec.ref) return;
    var list = load();
    list.unshift(rec);
    save(list);
  }

  global.BeanthenticTxnHistory = {
    KEY: KEY,
    load: load,
    push: pushRecord
  };
})(window);
