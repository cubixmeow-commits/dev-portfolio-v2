// One-tap check-ins from the Today cards. Same endpoint as the
// challenge page; the card flips to done state on success and the nav
// ring updates. Server-rendered forms keep working without JS.
(function () {
  'use strict';

  document.querySelectorAll('.dash-checkin-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var card = form.closest('.today-card');
      var button = form.querySelector('button');
      var csrf = form.querySelector('[name="_csrf"]');
      button.disabled = true;
      button.textContent = 'Checking in';

      fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-Token': csrf ? csrf.value : ''
        },
        body: ''
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data.ok) {
            button.disabled = false;
            button.textContent = 'Check in';
            window.alert(data.error || 'Check-in failed. Try again.');
            return;
          }

          card.classList.add('today-card-done');
          var streak = card.querySelector('[data-role="streak"]');
          if (streak) {
            streak.textContent = data.streak;
            streak.classList.add('streak-inline-done', 'streak-pop');
          }
          var action = card.querySelector('[data-role="action"]');
          if (action) {
            var pill = document.createElement('span');
            pill.className = 'pill pill-accent';
            pill.textContent = data.milestone ? 'Milestone: ' + data.streak + ' days' : 'Done for today';
            action.replaceChildren(pill);
          }

          if (typeof data.ring === 'number') {
            document.querySelectorAll('.nav .ring-fill').forEach(function (ring) {
              var dash = parseFloat(ring.getAttribute('stroke-dasharray'));
              if (!isNaN(dash)) {
                ring.setAttribute('stroke-dashoffset', String(dash * (1 - data.ring)));
              }
            });
          }
        })
        .catch(function () {
          button.disabled = false;
          button.textContent = 'Check in';
        });
    });
  });
})();
