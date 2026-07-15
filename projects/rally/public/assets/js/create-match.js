document.addEventListener('DOMContentLoaded', () => {
  const a = document.getElementById('source-a');
  const b = document.getElementById('source-b');
  const warn = document.getElementById('source-mismatch-warning');
  const lengthEl = document.getElementById('length-days');
  const thresholdEl = document.getElementById('tie-threshold');
  const cards = document.querySelectorAll('.metric-card input[type="radio"]');

  const norm = (c) => {
    c = (c || '').toLowerCase();
    if (c.includes('watch') || c === 'wearable') return 'watch';
    if (c.includes('phone')) return 'phone';
    return c;
  };

  const checkSources = () => {
    if (!a || !b || !warn) return;
    const optB = b.options[b.selectedIndex];
    if (!b.value) {
      warn.hidden = true;
      return;
    }
    const ca = norm(a.options[a.selectedIndex].dataset.class);
    const cb = norm(optB.dataset.class);
    warn.hidden = ca === cb;
  };

  const applyMetricDefaults = (input, { force = true } = {}) => {
    if (!input || !lengthEl || !thresholdEl) return;
    const len = input.dataset.length;
    const thr = input.dataset.threshold;
    if (force || !lengthEl.dataset.touched) {
      if (len === '7' || len === '14') {
        lengthEl.value = len;
      }
    }
    if (force || !thresholdEl.dataset.touched) {
      if (thr !== undefined && thr !== '') {
        thresholdEl.value = thr;
      }
    }
    document.querySelectorAll('.metric-card').forEach((card) => {
      card.classList.toggle('is-selected', card.querySelector('input') === input);
    });
  };

  if (a && b && warn) {
    a.addEventListener('change', checkSources);
    b.addEventListener('change', checkSources);
    checkSources();
  }

  cards.forEach((input) => {
    input.addEventListener('change', () => applyMetricDefaults(input, { force: true }));
  });

  if (lengthEl) {
    lengthEl.addEventListener('change', () => {
      lengthEl.dataset.touched = '1';
    });
  }
  if (thresholdEl) {
    thresholdEl.addEventListener('change', () => {
      thresholdEl.dataset.touched = '1';
    });
  }

  const selected = document.querySelector('.metric-card input[type="radio"]:checked');
  if (selected) {
    applyMetricDefaults(selected, { force: false });
  }
});
