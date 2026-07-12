/* Shared behavior: mobile nav, flash dismissal, copy-to-clipboard with
   confirmation, and the completion celebration. Vanilla JS, no build
   step, honours prefers-reduced-motion. */

(function () {
  "use strict";

  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* Mobile nav toggle */
  var toggle = document.querySelector(".nav-toggle");
  var links = document.getElementById("nav-links");
  if (toggle && links) {
    toggle.addEventListener("click", function () {
      var open = links.classList.toggle("open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  /* Flash dismissal */
  document.querySelectorAll("[data-flash-dismiss]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var flash = btn.closest("[data-flash]");
      if (flash) { flash.remove(); }
    });
  });

  /* Copy buttons: <button data-copy-target="#id"> copies the target's
     text and confirms inline for a moment. */
  document.querySelectorAll("[data-copy-target]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var target = document.querySelector(btn.getAttribute("data-copy-target"));
      if (!target) { return; }
      var text = target.innerText;
      var confirm = function () {
        btn.classList.add("is-copied");
        window.setTimeout(function () { btn.classList.remove("is-copied"); }, 2200);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(confirm, function () { fallbackCopy(text, confirm); });
      } else {
        fallbackCopy(text, confirm);
      }
    });
  });

  function fallbackCopy(text, done) {
    var area = document.createElement("textarea");
    area.value = text;
    area.setAttribute("readonly", "");
    area.style.position = "fixed";
    area.style.left = "-9999px";
    document.body.appendChild(area);
    area.select();
    try { document.execCommand("copy"); } catch (err) { /* clipboard unavailable */ }
    document.body.removeChild(area);
    done();
  }

  /* Submit buttons show a small working state so slow saves feel alive. */
  document.querySelectorAll("form[data-loading]").forEach(function (form) {
    form.addEventListener("submit", function () {
      var btn = form.querySelector("button[type=submit]");
      if (btn && !btn.classList.contains("is-loading")) {
        btn.classList.add("is-loading");
      }
    });
  });

  /* Completion celebration: a brief, calm confetti drift. Triggered by
     an element with data-celebrate (rendered after approve/complete). */
  function celebrate() {
    if (reducedMotion) { return; }
    var colors = ["#BC5B32", "#D98E2B", "#5E7E4E", "#8A6FAE", "#E3A377"];
    var burst = document.createElement("div");
    burst.className = "celebrate-burst";
    burst.setAttribute("aria-hidden", "true");
    for (var i = 0; i < 36; i++) {
      var piece = document.createElement("span");
      piece.className = "celebrate-piece";
      piece.style.left = Math.random() * 100 + "vw";
      piece.style.background = colors[i % colors.length];
      piece.style.animationDelay = Math.random() * 500 + "ms";
      piece.style.setProperty("--fall-duration", 2200 + Math.random() * 1400 + "ms");
      piece.style.setProperty("--spin", 360 + Math.random() * 540 + "deg");
      burst.appendChild(piece);
    }
    document.body.appendChild(burst);
    window.setTimeout(function () { burst.remove(); }, 4400);
  }

  if (document.querySelector("[data-celebrate]")) {
    celebrate();
  }

  window.SousMeow = { celebrate: celebrate };
})();
