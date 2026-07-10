/* =============================================================
   Iain — Operational Software
   Vanilla JS. No dependencies, no build step.
   1. Mobile nav toggle
   2. Draw-on animation for SVG schematics as they scroll into view
      (respects prefers-reduced-motion)
   ============================================================= */
(function () {
  'use strict';

  /* ---- 1. Mobile nav toggle -------------------------------- */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    // close the menu after choosing a destination
    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ---- 2. Draw-on schematic animation ---------------------- */
  var reduce = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var diagrams = document.querySelectorAll('[data-diagram]');

  if (reduce || !('IntersectionObserver' in window)) {
    // Reveal everything immediately, no motion.
    diagrams.forEach(function (d) { d.classList.add('in-view'); });
    return;
  }

  // Set the dash length per drawable path so the "pen" stroke length is exact.
  diagrams.forEach(function (d) {
    d.querySelectorAll('.draw').forEach(function (path) {
      try {
        var len = path.getTotalLength();
        if (len && isFinite(len)) {
          path.style.setProperty('--len', Math.ceil(len));
        }
      } catch (err) { /* non-path or unsupported: fall back to CSS default */ }
    });
  });

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2, rootMargin: '0px 0px -6% 0px' });

  diagrams.forEach(function (d) { io.observe(d); });
})();
