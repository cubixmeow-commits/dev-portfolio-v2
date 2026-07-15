document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const panel = document.getElementById('nav-menu-panel');
  if (toggle && panel) {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
      panel.classList.toggle('is-open', !open);
    });
    document.addEventListener('click', (event) => {
      if (!panel.classList.contains('is-open')) return;
      if (event.target instanceof Node && !toggle.contains(event.target) && !panel.contains(event.target)) {
        toggle.setAttribute('aria-expanded', 'false');
        panel.classList.remove('is-open');
      }
    });
  }

  document.querySelectorAll('[data-flash-dismiss]').forEach((btn) => {
    btn.addEventListener('click', () => {
      btn.closest('[data-flash]')?.remove();
    });
  });

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });
});
