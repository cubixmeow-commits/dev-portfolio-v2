/* =============================================================
   Iain — Operational Software
   Vanilla JS. No dependencies, no build step, no external requests.
   1. Mobile nav toggle
   2. Scroll-reveal for the inline-SVG diagrams
   3. Build the "depth of work" density graph (reimagined
      contribution graph) from data attributes
   All motion honours prefers-reduced-motion.
   ============================================================= */
(function () {
  'use strict';

  var reduce = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- 1. Mobile nav toggle -------------------------------- */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ---- 2. Density graph -----------------------------------
     Each row declares data-level (1-4). We render a track of
     cells whose intensity rises toward that level with a soft
     texture, so it reads as "density of work" rather than a
     literal calendar heatmap. Purely decorative: the label +
     mono tag already carry the meaning, and the track is
     aria-hidden. --------------------------------------------- */
  var CELLS = 26;
  var rows = document.querySelectorAll('[data-density] li');
  rows.forEach(function (row) {
    var level = parseInt(row.getAttribute('data-level'), 10) || 1;
    var track = row.querySelector('.dr-track');
    if (!track) return;
    for (var i = 0; i < CELLS; i++) {
      var cell = document.createElement('span');
      cell.className = 'cell';
      // taper intensity toward the ends, with a gentle deterministic ripple
      var edge = Math.min(i, CELLS - 1 - i) / (CELLS / 2); // 0..~1
      var ripple = (Math.sin(i * 1.7 + level) + 1) / 2;    // 0..1
      var reach = level * (0.55 + 0.45 * edge) * (0.7 + 0.5 * ripple);
      var tier = Math.max(0, Math.min(4, Math.round(reach)));
      if (tier > 0) cell.className += ' i' + tier;
      track.appendChild(cell);
    }
  });

  function activateCells() {
    document.querySelectorAll('.dr-track .cell').forEach(function (c) {
      c.classList.add('on');
    });
  }

  /* ---- 3. Scroll-reveal ------------------------------------
     The CSS hides un-revealed diagram content only while html.anim is set.
     We reveal on scroll via IntersectionObserver, but never rely on it
     alone: a scroll/timeout "sweep" reveals anything in view, and setting
     window.__diagramsArmed tells the head failsafe not to drop html.anim.
     Result: the animation plays when it can, and content is never stuck
     invisible if the observer misfires on a given device. ----------------- */
  var diagrams = document.querySelectorAll('[data-diagram]');
  var density = document.querySelector('[data-density]');

  function revealDiagram(d) { d.classList.add('in'); }

  if (reduce || !('IntersectionObserver' in window)) {
    diagrams.forEach(revealDiagram);
    activateCells();
    window.__diagramsArmed = true;
    return;
  }

  var io = new IntersectionObserver(function (entries, obs) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        revealDiagram(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });
  diagrams.forEach(function (d) { io.observe(d); });

  // Animate density cells in when the graph scrolls into view
  if (density) {
    var dio = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { activateCells(); obs.disconnect(); }
      });
    }, { threshold: 0.2 });
    dio.observe(density);
  }

  // Backstop: reveal anything already within the viewport, in case the
  // observer callback doesn't fire for it. Runs on scroll, load, and once
  // on a short timer — so a diagram in view always ends up revealed.
  function sweep() {
    var vh = window.innerHeight || document.documentElement.clientHeight;
    diagrams.forEach(function (d) {
      if (d.classList.contains('in')) return;
      var r = d.getBoundingClientRect();
      if (r.bottom > 0 && r.top < vh * 0.92) revealDiagram(d);
    });
    if (density && !density.querySelector('.cell.on')) {
      var dr = density.getBoundingClientRect();
      if (dr.bottom > 0 && dr.top < vh * 0.92) activateCells();
    }
  }
  window.addEventListener('scroll', sweep, { passive: true });
  window.addEventListener('load', sweep);
  setTimeout(sweep, 1000);

  // The reveal mechanism is now fully in place; keep html.anim.
  window.__diagramsArmed = true;
})();
