// Ops tools: launch engine runs and stream their stdout into the live
// output panel by polling the status endpoint until the run finishes.
(function () {
  'use strict';

  var root = document.getElementById('tools-root');
  if (!root || root.dataset.shell !== '1') return;

  var panel = document.getElementById('output-panel');
  var logEl = document.getElementById('output-log');
  var statusEl = document.getElementById('output-status');
  var downloadEl = document.getElementById('output-download');
  var pollTimer = null;

  function setStatus(status) {
    statusEl.textContent = status;
    statusEl.className = 'pill' + (status === 'success' ? ' pill-accent' : status === 'failed' ? ' pill-warn' : '');
  }

  function poll(runId) {
    fetch(root.dataset.statusUrl + '/' + runId, { headers: { Accept: 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.ok) {
          setStatus('failed');
          logEl.textContent += '\n' + (data.error || 'Status check failed.');
          return;
        }
        logEl.textContent = data.log || '(waiting for output...)';
        logEl.scrollTop = logEl.scrollHeight;
        setStatus(data.status);

        if (data.status === 'running') {
          pollTimer = setTimeout(function () { poll(runId); }, 1500);
        } else {
          // Finished: offer the captured output as a download and
          // refresh run history on the next natural reload.
          downloadEl.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(data.log || '');
          downloadEl.hidden = false;
        }
      })
      .catch(function () {
        pollTimer = setTimeout(function () { poll(runId); }, 3000);
      });
  }

  root.querySelectorAll('.tool-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (pollTimer) clearTimeout(pollTimer);

      var body = new URLSearchParams(new FormData(form));
      body.set('tool', form.dataset.tool);

      var button = form.querySelector('button[type="submit"]');
      button.disabled = true;

      panel.hidden = false;
      downloadEl.hidden = true;
      logEl.textContent = 'Starting ' + form.dataset.tool + '...';
      setStatus('running');

      fetch(root.dataset.runUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-Token': root.dataset.csrf
        },
        body: body.toString()
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          button.disabled = false;
          if (!data.ok) {
            setStatus('failed');
            logEl.textContent = data.error + (data.command ? '\n\nRun over SSH instead:\n' + data.command : '');
            return;
          }
          poll(data.runId);
        })
        .catch(function () {
          button.disabled = false;
          setStatus('failed');
          logEl.textContent = 'Could not reach the server. Try again.';
        });
    });
  });
})();
