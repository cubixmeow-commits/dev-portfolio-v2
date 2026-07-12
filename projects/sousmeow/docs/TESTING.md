# SousMeow test flow

The documented flow below is what "done" means for this build. It was
executed against the SQLite configuration with PHP 8.4 (built-in server)
before completion, both as scripted HTTP checks and as a real-browser
pass (Chromium via Playwright) for the JavaScript behaviors.

## Setup

```sh
cd projects/sousmeow
php scripts/seed.php
php -S localhost:8090 -t public public/index.php
```

## 1. Static checks

- `find . -name '*.php' -exec php -l {} \;` reports no syntax errors.
- `grep -rn` for secrets, debug credentials, and stray production URLs
  comes back clean (the only credentials are the example placeholders in
  `config.example.php`).

## 2. Public site

- `/` explains the product in one screen; both CTAs work signed out.
- `/marketplace` lists 8 Cookbooks; exactly one is "Available now".
- Search: a nonsense query shows the No Search Results empty state; the
  clear button restores the shelf.
- A paid Cookbook detail (`/cookbooks/cold-outreach-kit`) shows its
  price, an honest "purchases are not open yet" note, a Preview recipe
  list, and no Start button. Free coming-soon Cookbooks likewise.
- Unknown URLs render the 404 page. `/kitchen` signed out redirects to
  login. `/admin` signed out redirects; signed in as a non-admin it
  returns 403.

## 3. The core loop (happy path)

1. Register (invalid emails, short passwords, and duplicate emails are
   rejected with inline errors; success lands in an empty Kitchen whose
   empty state offers the Launch Day Kit).
2. Start the Launch Day Kit; you land on the Pantry.
3. "Fill the form with a sample Pantry" prefills all 8 fields and shows
   the Sample data notice; saving redirects to Recipe 1.
4. Recipe 1: prompt shows with Pantry values highlighted; Copy prompt
   puts the exact text on the clipboard and confirms inline.
5. "Paste example response" stores v1 marked Sample data; the review
   state appears with 0 of 3 checks confirmed and Approve disabled.
6. Checks toggle live (fetch) and survive reload; with all confirmed,
   Approve enables. Approving celebrates and unlocks Recipe 2.
7. Recipes 2 through 4: same loop. Recipe 2's prompt contains the
   approved Recipe 1 artifact verbatim (prompt chaining).
8. After Recipe 4, you land on the export page in its ready state; Pack
   a fresh kit produces a zip listed under Past exports; the download
   is a valid zip holding `README.md` plus four numbered Markdown files
   with correct content, versions, and sample-data provenance.

## 4. Versions, checks, and locks

- Runner URLs for later recipes redirect back to the first unapproved
  recipe until order is satisfied; the runner before a saved Pantry
  redirects to the Pantry.
- Pasting a real response stores v1 "Pasted response". Editing stores
  v2 "Edited" and resets the checks to 0 (confirmations bind to a
  version). Viewing v1 is read-only with a restore action; restoring
  creates v3 "Restored". The version list shows all three.
- Approve with unconfirmed checks is refused server side (the button
  being disabled is cosmetic, the POST is validated).
- Approving, then "Revise this Recipe", withdraws the approval, marks
  the project incomplete again, and requires re-review to proceed.
- Invalid Pantry input (unknown select option, negative number, bad
  URL, empty required field) re-renders with inline errors and a 422.

## 5. Security checks

- Any POST without a CSRF token returns 419 (including check toggles).
- Project, runner, pantry, and export URLs belonging to another user
  return 404 for a different signed-in account and for guests.
- Export downloads are owner-scoped; a foreign export id returns 404.
- Pasted responses containing HTML/script render inert (escaped, then
  formatted by the Markdown allowlist); no raw HTML passthrough exists.
- `/admin` is admin-only; the role can only be granted by the CLI seed.

## 6. Small screens

At 390px wide: the runner rail collapses to dots plus the current
title, the prompt block, paste form, checks, and approve button are
full-width and reachable, and the nav collapses behind the toggle.

## 7. Motion and accessibility spot checks

- `prefers-reduced-motion: reduce` disables the confetti celebration
  and transitions.
- All interactive elements have visible focus states; checks are real
  checkboxes with labels; progress bars carry aria attributes.
