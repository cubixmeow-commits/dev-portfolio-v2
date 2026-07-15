document.addEventListener('DOMContentLoaded', () => {
  const a = document.getElementById('source-a');
  const b = document.getElementById('source-b');
  const warn = document.getElementById('source-mismatch-warning');
  const lengthEl = document.getElementById('length-days');
  const thresholdEl = document.getElementById('tie-threshold');
  const cards = document.querySelectorAll('.metric-card input[name="metric_type_id"]');
  const typeInputs = document.querySelectorAll('input[name="competition_type"]');
  const baselineNote = document.getElementById('baseline-unavailable-note');
  const baselineControls = document.getElementById('baseline-controls');
  const baselineCard = document.getElementById('card-baseline');
  const baselineRadio = document.getElementById('competition-baseline');
  const ack = document.getElementById('baseline-acknowledged');
  const startDate = document.getElementById('start-date');

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

  const selectedMetric = () => document.querySelector('.metric-card input[name="metric_type_id"]:checked');

  const resetAcknowledgement = () => {
    if (ack) ack.checked = false;
  };

  const syncCompetitionAvailability = () => {
    const input = selectedMetric();
    const allowsBaseline = input && input.dataset.baseline === '1';
    if (baselineNote) baselineNote.hidden = !!allowsBaseline;
    if (baselineCard) {
      baselineCard.classList.toggle('is-disabled', !allowsBaseline);
      baselineCard.style.opacity = allowsBaseline ? '' : '0.45';
    }
    if (baselineRadio) {
      baselineRadio.disabled = !allowsBaseline;
      if (!allowsBaseline && baselineRadio.checked) {
        const classic = document.querySelector('input[name="competition_type"][value="classic"]');
        if (classic) {
          classic.checked = true;
          classic.dispatchEvent(new Event('change', { bubbles: true }));
        }
      }
    }
    const type = document.querySelector('input[name="competition_type"]:checked');
    const isBaseline = type && type.value === 'baseline';
    if (baselineControls) baselineControls.hidden = !isBaseline;
    document.querySelectorAll('.competition-card').forEach((card) => {
      const radio = card.querySelector('input[name="competition_type"]');
      card.classList.toggle('is-selected', !!(radio && radio.checked));
    });
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
      const radio = card.querySelector('input[name="metric_type_id"]');
      if (radio) card.classList.toggle('is-selected', radio === input);
    });
    syncCompetitionAvailability();
  };

  if (a && b && warn) {
    a.addEventListener('change', () => {
      checkSources();
      resetAcknowledgement();
    });
    b.addEventListener('change', () => {
      checkSources();
      resetAcknowledgement();
    });
    checkSources();
  }

  cards.forEach((input) => {
    input.addEventListener('change', () => {
      applyMetricDefaults(input, { force: true });
      resetAcknowledgement();
    });
  });

  typeInputs.forEach((input) => {
    input.addEventListener('change', () => {
      syncCompetitionAvailability();
      resetAcknowledgement();
    });
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
  if (startDate) {
    startDate.addEventListener('change', resetAcknowledgement);
  }

  const selected = selectedMetric();
  if (selected) {
    applyMetricDefaults(selected, { force: false });
  }
  syncCompetitionAvailability();
});
