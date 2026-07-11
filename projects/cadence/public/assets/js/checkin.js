// Optimistic check-in on the challenge page. Submits over fetch, flips
// the module to the done state, and pulses the streak number. The
// server stays authoritative: any error rolls the UI back and shows
// the server's message.
(function () {
  'use strict';

  var moduleEl = document.getElementById('checkin-module');
  var form = document.getElementById('checkin-form');
  if (!moduleEl || !form) return;

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    var button = document.getElementById('checkin-button');
    var note = form.querySelector('[name="note"]');
    var state = document.getElementById('checkin-state');
    var streakEl = moduleEl.querySelector('.streak-number');

    button.disabled = true;
    button.textContent = 'Checking in';

    var body = new URLSearchParams();
    body.set('note', note ? note.value : '');

    fetch(moduleEl.dataset.checkinUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-Token': moduleEl.dataset.csrf
      },
      body: body.toString()
    })
      .then(function (response) { return response.json(); })
      .then(function (data) {
        if (!data.ok) {
          button.disabled = false;
          button.textContent = 'Check in';
          window.alert(data.error || 'Check-in failed. Try again.');
          return;
        }

        state.innerHTML = '';
        var wrap = document.createElement('div');
        wrap.className = 'checkin-done';
        var heading = document.createElement('h3');
        heading.textContent = data.milestone ? 'Milestone: ' + data.streak + ' days' : 'Checked in for today';
        var line = document.createElement('p');
        line.className = 'muted small';
        line.textContent = data.message;
        wrap.appendChild(heading);
        wrap.appendChild(line);
        state.appendChild(wrap);

        if (streakEl) {
          streakEl.textContent = data.streak;
          streakEl.classList.remove('streak-pop');
          void streakEl.offsetWidth; // restart the animation
          streakEl.classList.add('streak-pop');
        }

        // Nav ring reflects the new state without a reload.
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
        window.alert('Network hiccup. Your check-in did not go through; try again.');
      });
  });
})();
