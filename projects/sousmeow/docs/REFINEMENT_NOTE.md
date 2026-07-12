# SousMeow v1: Refinement Note

This note records the assumptions made before implementation, the
contradictions that were resolved, and the simplifications applied while
building SousMeow v1. It exists so a reviewer can see every judgement call
in one place.

## The missing appended specification

The build brief ended with the placeholder line
`[APPEND THE LOCKED SPECIFICATION HERE, UNCHANGED]`, but no specification
document was actually appended. The brief itself, however, is detailed
enough to be buildable: it fixes the effort distribution, the core loop,
the entity vocabulary (Cookbook, Recipe, Pantry, Artifact, Project Kit),
the exact counts (eight Pantry fields, six ingredient types, four
Recipes, seven non-executable marketplace Cookbooks), the security bar,
and the deployment target.

Resolution: the brief was codified, unchanged in substance, into
`docs/SPEC_LOCKED.md` and treated as the locked specification. No scope
was invented beyond what the brief names, and nothing named in the brief
was dropped.

## Assumptions

1. **Project location.** The repository is a portfolio with one folder
   per project under `projects/`. SousMeow lives at `projects/sousmeow/`
   and follows the same layout as the existing `cadence` project:
   `app/{Core,Controllers,Models,Views}`, `public/`, `config/`,
   `database/`, `scripts/`, `docs/`.
2. **Doc paths.** The brief says `/docs/REFINEMENT_NOTE.md` and
   `/docs/SPEC_LOCKED.md`. Paths are read as relative to the SousMeow
   project folder (repo-root `docs/` does not exist and would break the
   one-folder-per-project convention).
3. **Database engine.** Hostinger shared hosting offers MySQL, and this
   development container has no MySQL server. The PDO layer therefore
   supports two drivers behind one config switch: SQLite (default, zero
   setup, used for local runs and the demo) and MySQL (for Hostinger).
   Both schemas ship in `database/`. Every query uses prepared
   statements and portable SQL, so the application code is identical
   under both drivers.
4. **The sample Cookbook.** The brief fixes its shape (four Recipes,
   eight Pantry fields, six ingredient types) but not its subject. The
   chosen subject is "Launch Day Kit": it turns a maker's product notes
   into launch copy (positioning, landing page copy, announcement posts,
   FAQ). This subject was chosen because it exercises long-form paste
   flows well and produces a genuinely useful exported kit.
5. **Ingredient types.** The six supported types are: short text, long
   text, single select, multi select, number, and URL. The eight sample
   fields use all six.
6. **Example responses.** The four seeded example AI responses are
   written for a fictional product called Driftlog (a time-logging app
   for freelance designers) so they read as real AI output, and they are
   always labeled "Sample data" in the interface.
7. **Fonts.** Instrument Sans and IBM Plex Mono woff2 files are reused
   from the sibling `cadence` project (both are OFL licensed and already
   vendored in this repository). Self-hosted, no CDN calls.

## Contradictions resolved

1. **"Never present a blank form" vs. required Pantry input.** The
   Pantry form cannot be prefilled with user data on first visit, so
   every field carries a purposeful placeholder, helper text explaining
   how the field is used in prompts, and the page opens with a short
   explanation of what the Pantry feeds. A "Fill with sample Pantry"
   affordance (clearly marked sample data, mirroring the Demo Mode rule)
   lets a first-time visitor experience the loop without composing
   content.
2. **"Version history" ambiguity.** Locked per the brief: version
   history in the Runner means Artifact response versions. Cookbooks
   have no publishing versions anywhere in v1.
3. **Paid Cookbooks with no payments.** Paid marketplace entries show
   their price and an honest "Purchases are not open yet" state. There
   is no checkout form, no simulated success, and no Start button on any
   non-executable Cookbook.
4. **"Demo Mode" vs. "treat pasted AI responses as untrusted".** The
   example response travels the same code path as a real paste: stored
   as an immutable version, escaped on render, validated server side.
   Demo Mode is a content shortcut, not a code shortcut.

## Simplifications (deliberate, per the locked scope)

- No external AI API, SMTP, Stripe, Node, Docker, or worker. The loop is
  copy prompt, run in your own AI, paste back.
- Sessions are native PHP sessions with secure cookie settings; no
  remember-me tokens and no password reset email (no SMTP). The admin
  can reset a password with `php scripts/seed.php --reset-password`.
- Quality Checks are human-confirmed checkboxes defined per Recipe.
  Nothing claims automatic evaluation.
- Markdown-ish rendering of pasted responses is a small allowlist
  renderer that works on already-escaped text (headings, bold, lists,
  code fences). No HTML from the paste ever reaches the page.
- Export is a ZIP of Markdown files plus a manifest, generated
  synchronously with ZipArchive and stored under `storage/exports/`.
- The admin area is a single read-only overview page, in line with its
  5 percent effort share.
- No JavaScript build step. Vanilla CSS and JS, matching the repository
  rule set.

## Explicitly out of scope (guarded against re-entry)

Enterprise scalability, microservices, plugin systems, generic workflow
engines, direct AI API calls, multi-tenancy, real payments, and Cookbook
authoring tools. The marketplace is a presentation shell by design.
