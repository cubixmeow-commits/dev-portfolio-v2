/* Recipe Runner enhancements: live Quality Check toggles over fetch,
   with the plain form submit as the no-JS fallback. */

(function () {
  "use strict";

  document.documentElement.classList.add("js-on");
  document.body.classList.add("js-on");

  var form = document.querySelector("[data-checks-form]");
  if (!form) { return; }

  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
  var counter = document.querySelector("[data-checks-counter]");
  var approveButton = document.querySelector("[data-approve-button]");
  var approveNote = document.querySelector("[data-approve-note]");
  var saveButton = form.querySelector("[data-checks-save]");
  if (saveButton) { saveButton.style.display = "none"; }

  function updateSummary(confirmed, total, canApprove) {
    if (counter) {
      counter.textContent = confirmed + " of " + total + " confirmed";
      counter.classList.toggle("badge-sage", canApprove);
      counter.classList.toggle("badge-neutral", !canApprove);
    }
    if (approveButton) {
      approveButton.disabled = !canApprove;
      if (canApprove && approveNote) {
        approveNote.textContent = "Everything is confirmed. Approving locks this version into your Project Kit.";
      }
    }
  }

  form.querySelectorAll("[data-check-box]").forEach(function (box) {
    box.addEventListener("change", function () {
      var item = box.closest(".check-item");
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
        })
        .catch(function () {
          // Revert optimistic UI; the fallback save button reappears so
          // the review can still be recorded.
          box.checked = !wanted;
          if (item) { item.classList.toggle("is-checked", box.checked); }
          if (saveButton) { saveButton.style.display = ""; }
        });
    });
  });
})();
