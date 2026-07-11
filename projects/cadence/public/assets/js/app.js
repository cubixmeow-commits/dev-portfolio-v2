// Shared behaviors, wired by data attributes. No inline handlers
// anywhere: the CSP allows same-origin script files only.
(function () {
  'use strict';

  // Two-step confirm for destructive forms: <form data-confirm="...">.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (form instanceof HTMLFormElement && form.dataset.confirm) {
      if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
      }
    }
  });

  // Dismissible elements: <button data-dismiss="#selector">.
  document.addEventListener('click', function (event) {
    var button = event.target instanceof Element ? event.target.closest('[data-dismiss]') : null;
    if (!button) return;
    var target = document.querySelector(button.dataset.dismiss);
    if (target) target.remove();
  });

  // Remembered dismissal (demo ribbon): hidden until we know the
  // visitor has not dismissed it this session, so it never flashes.
  var remembered = document.querySelectorAll('[data-dismiss-remember]');
  remembered.forEach(function (button) {
    var key = 'dismissed:' + button.dataset.dismissRemember;
    var target = document.getElementById(button.dataset.dismissRemember);
    if (!target) return;
    var store;
    try { store = window.sessionStorage; } catch (e) { store = null; }
    if (!store || !store.getItem(key)) {
      target.hidden = false;
    }
    button.addEventListener('click', function () {
      target.remove();
      if (store) store.setItem(key, '1');
    });
  });
})();
