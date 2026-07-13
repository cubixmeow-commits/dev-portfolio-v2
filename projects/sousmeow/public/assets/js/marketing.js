/* Marketing homepage motion. Every animation here communicates product
   progress: Pantry values filling prompts, the Runner loop advancing,
   Quality Checks stamping, artifacts packing into the kit.

   Contract with marketing.css: pre-animation states exist only under
   html.tw-anim, which this script adds. If the script never runs (JS
   blocked, reduced motion, old browser), the page renders complete and
   static. Vanilla JS, no dependencies. */

(function () {
  "use strict";

  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (reducedMotion || !("IntersectionObserver" in window)) { return; }

  document.documentElement.classList.add("tw-anim");

  function once(el, options, callback) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          observer.unobserve(entry.target);
          callback(entry.target);
        }
      });
    }, options);
    observer.observe(el);
  }

  /* ---------- Scroll reveals ---------- */

  document.querySelectorAll("[data-reveal]").forEach(function (el) {
    once(el, { rootMargin: "0px 0px -60px" }, function () {
      el.classList.add("is-in");
    });
  });

  /* ---------- Hero: Cookbook becomes a Project Kit ---------- */

  var hero = document.querySelector("[data-hero]");
  if (hero) {
    once(hero, { threshold: 0.4 }, function () {
      hero.classList.add("is-play");
    });
  }

  /* ---------- Pantry: values flow into the prompt ---------- */

  var pantry = document.querySelector("[data-pantry]");
  if (pantry) {
    var tokens = Array.prototype.slice.call(pantry.querySelectorAll(".pv"));
    var replay = pantry.querySelector("[data-pantry-replay]");
    var pantryTimers = [];

    var fieldFor = function (token) {
      return pantry.querySelector('.pf[data-field="' + token.getAttribute("data-token") + '"]');
    };

    var playPantry = function () {
      pantryTimers.forEach(window.clearTimeout);
      pantryTimers = [];
      pantry.classList.remove("is-done");
      tokens.forEach(function (token) { token.classList.remove("is-filled"); });
      pantry.querySelectorAll(".pf").forEach(function (pf) {
        pf.classList.remove("is-live", "is-sent");
      });

      tokens.forEach(function (token, i) {
        var field = fieldFor(token);
        pantryTimers.push(window.setTimeout(function () {
          if (field) { field.classList.add("is-live"); }
        }, 500 + i * 950));
        pantryTimers.push(window.setTimeout(function () {
          token.classList.add("is-filled");
          if (field) {
            field.classList.remove("is-live");
            field.classList.add("is-sent");
          }
        }, 500 + i * 950 + 620));
      });

      pantryTimers.push(window.setTimeout(function () {
        pantry.classList.add("is-done");
        if (replay) { replay.hidden = false; }
      }, 500 + tokens.length * 950 + 400));
    };

    once(pantry, { threshold: 0.35 }, playPantry);
    if (replay) { replay.addEventListener("click", playPantry); }
  }

  /* ---------- Runner: the loop, step by step ---------- */

  var runner = document.querySelector("[data-runner]");
  if (runner) {
    var stepButtons = Array.prototype.slice.call(runner.querySelectorAll(".run-step"));
    var STEPS = stepButtons.length;
    var current = 1;
    var autoTimer = null;

    var setStep = function (step) {
      current = step;
      for (var s = 1; s <= STEPS; s++) {
        runner.classList.toggle("is-step-" + s, s === step);
      }
      stepButtons.forEach(function (btn) {
        var n = parseInt(btn.getAttribute("data-step"), 10);
        btn.classList.toggle("is-on", n === step);
        btn.classList.toggle("is-done", n < step);
        btn.setAttribute("aria-pressed", n === step ? "true" : "false");
      });
    };

    var stopAuto = function () {
      if (autoTimer) { window.clearInterval(autoTimer); autoTimer = null; }
    };

    var startAuto = function () {
      stopAuto();
      autoTimer = window.setInterval(function () {
        // Rest a beat on "Next Recipe" before the loop begins again.
        setStep(current === STEPS ? 1 : current + 1);
      }, 2300);
    };

    setStep(1);
    once(runner, { threshold: 0.35 }, startAuto);

    stepButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        stopAuto(); // the visitor is driving now
        setStep(parseInt(btn.getAttribute("data-step"), 10));
      });
    });

    // Don't keep cycling far off-screen.
    new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { stopAuto(); }
        else if (!autoTimer && !runner.hasAttribute("data-user-driven")) { startAuto(); }
      });
    }, { threshold: 0.15 }).observe(runner);
    stepButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        runner.setAttribute("data-user-driven", "");
      });
    });
  }

  /* ---------- Quality Checks: the stamp comes down ---------- */

  var checks = document.querySelector("[data-checks]");
  if (checks) {
    once(checks, { threshold: 0.45 }, function () {
      checks.classList.add("is-stamped");
    });
  }

  /* ---------- Project Kit: artifacts pack one by one ---------- */

  var kit = document.querySelector("[data-kit]");
  if (kit) {
    var files = Array.prototype.slice.call(kit.querySelectorAll(".km-file"));
    var counter = kit.querySelector("[data-kit-count]");
    once(kit, { threshold: 0.4 }, function () {
      files.forEach(function (file, i) {
        window.setTimeout(function () {
          file.classList.add("is-in");
          if (counter) {
            counter.textContent = (i + 1) + " of " + files.length + " packed";
          }
        }, 300 + i * 380);
      });
    });
  }
})();
