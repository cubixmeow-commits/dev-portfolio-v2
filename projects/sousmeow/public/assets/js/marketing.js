/* Marketing homepage motion. Vanilla JS, no dependencies.
   Pre-animation states live only under html.tw-anim. */

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

  document.querySelectorAll("[data-reveal]").forEach(function (el) {
    once(el, { rootMargin: "0px 0px -50px" }, function () {
      el.classList.add("is-in");
    });
  });

  var hero = document.querySelector("[data-hero]");
  if (hero) {
    once(hero, { threshold: 0.35 }, function () {
      hero.classList.add("is-play");
    });
  }
})();
