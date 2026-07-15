document.addEventListener('DOMContentLoaded', () => {
  const a = document.getElementById('source-a');
  const b = document.getElementById('source-b');
  const warn = document.getElementById('source-mismatch-warning');
  if (!a || !b || !warn) return;

  const norm = (c) => {
    c = (c || '').toLowerCase();
    if (c.includes('watch') || c === 'wearable') return 'watch';
    if (c.includes('phone')) return 'phone';
    return c;
  };

  const check = () => {
    const optB = b.options[b.selectedIndex];
    if (!b.value) {
      warn.hidden = true;
      return;
    }
    const ca = norm(a.options[a.selectedIndex].dataset.class);
    const cb = norm(optB.dataset.class);
    warn.hidden = ca === cb;
  };

  a.addEventListener('change', check);
  b.addEventListener('change', check);
  check();
});
