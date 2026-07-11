// Live handle availability on the register form. Progressive
// enhancement: the server validates again on submit, this only saves a
// round trip of disappointment.
(function () {
  'use strict';

  var input = document.getElementById('handle');
  var hint = document.getElementById('handle-hint');
  if (!input || !hint) return;

  var baseHint = hint.textContent;
  var timer = null;

  input.addEventListener('input', function () {
    clearTimeout(timer);
    var handle = input.value.trim().toLowerCase();

    if (handle.length < 3) {
      hint.textContent = baseHint;
      hint.style.color = '';
      return;
    }
    if (!/^[a-z0-9_]{3,30}$/.test(handle)) {
      hint.textContent = 'Lowercase letters, numbers, and underscores only.';
      hint.style.color = 'var(--warn)';
      return;
    }

    timer = setTimeout(function () {
      fetch(input.dataset.checkUrl + '?handle=' + encodeURIComponent(handle), { headers: { Accept: 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (input.value.trim().toLowerCase() !== handle) return;
          if (data.available) {
            hint.textContent = '@' + handle + ' is available.';
            hint.style.color = 'var(--accent)';
          } else {
            hint.textContent = '@' + handle + ' is taken. Try another.';
            hint.style.color = 'var(--warn)';
          }
        })
        .catch(function () {
          hint.textContent = baseHint;
          hint.style.color = '';
        });
    }, 350);
  });
})();
