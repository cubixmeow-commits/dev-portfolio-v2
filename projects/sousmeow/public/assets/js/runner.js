/* Recipe Runner wizard enhancements: live Quality Check toggles over
   fetch (plain form submit stays as the no-JS fallback), accessible
   save announcements, and the "Needs revision" path into the collapsed
   Response options. Server state stays authoritative throughout. */

(function () {
  "use strict";

  document.documentElement.classList.add("js-on");
  document.body.classList.add("js-on");

  var form = document.querySelector("[data-checks-form]");
  var options = document.querySelector("[data-response-options]");

  /* "Revise the response" opens the collapsed Response options. */
  function openOptions() {
    if (!options) { return; }
    options.open = true;
    options.scrollIntoView({ behavior: "smooth", block: "start" });
    var summary = options.querySelector("summary");
    if (summary) { summary.focus({ preventScroll: true }); }
  }
  document.querySelectorAll("[data-open-options]").forEach(function (btn) {
    btn.addEventListener("click", openOptions);
  });

  if (!form) { return; }

  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
  var counter = document.querySelector("[data-checks-counter]");
  var approveButton = document.querySelector("[data-approve-button]");
  var approveNote = document.querySelector("[data-approve-note]");
  var summarySection = document.querySelector("[data-review-summary]");
  var summaryConfirmed = document.querySelector("[data-summary-confirmed]");
  var announce = document.querySelector("[data-save-announce]");
  var saveButton = form.querySelector("[data-checks-save]");
  if (saveButton) { saveButton.style.display = "none"; }

  function say(message) {
    if (announce) { announce.textContent = message; }
  }

  function updateSummary(confirmed, total, canApprove) {
    if (counter) {
      counter.textContent = confirmed + " of " + total + " confirmed";
      counter.classList.toggle("badge-sage", canApprove);
      counter.classList.toggle("badge-neutral", !canApprove);
    }
    if (summaryConfirmed) {
      summaryConfirmed.textContent = confirmed + " of " + total + " quality checks confirmed";
    }
    if (summarySection) {
      summarySection.classList.toggle("is-ready", canApprove);
    }
    if (approveButton) {
      approveButton.disabled = !canApprove;
      if (approveNote) {
        approveNote.textContent = canApprove
          ? "Everything is confirmed. Approving locks this version into your Project Kit."
          : "The approve button wakes up when every check is confirmed.";
      }
    }
  }

  form.querySelectorAll("[data-check-box]").forEach(function (box) {
    box.addEventListener("change", function () {
      var item = box.closest("[data-review-card]");
      var wanted = box.checked;
      if (item) { item.classList.toggle("is-checked", wanted); }

      var body = new URLSearchParams();
      body.set("check_id", box.value);
      body.set("confirmed", wanted ? "1" : "0");

      fetch(form.action, {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "X-CSRF-Token": csrf,
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: body.toString(),
        credentials: "same-origin"
      })
        .then(function (res) {
          if (!res.ok) { throw new Error("save-failed"); }
          return res.json();
        })
        .then(function (data) {
          updateSummary(data.confirmed, data.total, data.can_approve);
          say("Saved. " + data.confirmed + " of " + data.total + " checks confirmed.");
        })
        .catch(function () {
          // Revert optimistic UI; the fallback save button reappears so
          // the review can still be recorded.
          box.checked = !wanted;
          if (item) { item.classList.toggle("is-checked", box.checked); }
          if (saveButton) { saveButton.style.display = ""; }
          say("Saving failed. Use the Save review button to record your review.");
        });
    });
  });

  /* "Needs revision" never confirms the check: it clears the box when
     set (recording that decision) and opens the revision path. */
  document.querySelectorAll("[data-needs-revision]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var box = document.getElementById(btn.getAttribute("data-for-check"));
      if (box && box.checked) {
        box.checked = false;
        box.dispatchEvent(new Event("change"));
      }
      openOptions();
    });
  });
})();
