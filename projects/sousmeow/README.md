# SousMeow

A guided AI cooking companion for makers, built as the primary showcase
project of this portfolio. SousMeow packages proven workflows as
**Cookbooks** made of step-by-step **Recipes**: you stock a **Pantry**
with facts about your project, each Recipe turns those facts into a
precise prompt, you run it in the AI you already use, paste the answer
back, confirm human **Quality Checks**, and approve the result. Finished
Cookbooks export as a **Project Kit**: clean Markdown files plus a
manifest.

SousMeow deliberately never calls an AI itself. No API keys, no token
markup, no black box. The product is structure, review discipline,
versioning, and a finished deliverable.

## Portfolio demo dashboard

Populate the marketing home page with hundreds of simulated chefs, projects,
and exports. Safe to re-run; only touches `demo+*.local` accounts.

```sh
php scripts/seed.php
php scripts/seed-demo.php --users=350        # default scale
php scripts/seed-demo.php --fresh-demo       # replace demo users first
php scripts/seed-demo.php --status           # read-only counts
```

Demo logins use `demo+1@kitchen.local` (through `demo+350@kitchen.local`) with
password `demo-kitchen-2026`. The home page dashboard blends live database
counts with seeded cookbook metrics and labels the section as a portfolio demo.

## Try it in two minutes

```sh
cd projects/sousmeow
php scripts/seed.php          # creates the SQLite schema, syncs seed content
                              # (upsert by slug; safe to re-run), prints a
                              # one-time admin password. Use --fresh to wipe all data.
php -S localhost:8090 -t public public/index.php
```

Open http://localhost:8090, create an account, start the **Launch Day
Kit**, and click "Fill the form with a sample Pantry" followed by "Paste
example response" on each Recipe. The entire loop, including the zip
export, works without any AI at all; sample content is always labeled.

## What is inside

| Piece | Where | Notes |
| --- | --- | --- |
| Recipe Runner | `app/Controllers/RunnerController.php`, `app/Views/runner/step.php` | The core loop: prompt with highlighted ingredients, paste, per-version Quality Checks, approve/revise, immutable version history |
| Prompt building | `app/Services/PromptBuilder.php` | `{{field}}` and `{{artifact:recipe-slug}}` substitution; approved earlier Recipes chain into later prompts |
| Safe rendering | `app/Services/SafeText.php` | Pasted responses are escaped first, then a small Markdown allowlist formats the escaped text |
| Export | `app/Services/ProjectKit.php` | ZipArchive kit with per-Recipe files and a provenance manifest |
| Data | `database/schema.sqlite.sql`, `database/schema.mysql.sql`, `database/seeds/content.php` | Same schema in both dialects; all seed content is real, no lorem ipsum |
| Design system | `public/assets/css/tokens.css` | "Cozy Engineering": warm paper surfaces, terracotta and sage, original cat mascot, watercolor-inspired washes |

## Security posture

- Every dynamic query uses PDO prepared statements (`app/Core/Database.php`
  is the only database access path).
- Every POST passes one CSRF gate in the front controller before routing.
- Authorization is server side: project and export queries are scoped to
  the owner at the data layer; `/admin` requires the admin role.
- All rendered user content is escaped; pasted AI responses are treated
  as untrusted input end to end.
- Admin accounts exist only via `php scripts/seed.php` (CLI-only, prints
  a temporary password once). There is no web installer.
- Sessions are HttpOnly, SameSite=Lax, idle-expired, and rotated at
  login. Auth endpoints are rate limited.
- CSP allows same-origin scripts only; fonts and assets are self-hosted.

## Deliberate limits (v1 scope)

Five first-party Cookbooks: three are fully executable today; two are workflow
previews with honest "coming soon" states and no checkout. No SMTP, no
payment SDK, no Node build step, no Docker, no background workers.
Password resets are an admin CLI action
(`php scripts/seed.php --reset-password you@example.com`).

## Documentation

- `docs/SPEC_LOCKED.md`: the scope-locked specification.
- `docs/REFINEMENT_NOTE.md`: assumptions and contradictions resolved.
- `docs/TESTING.md`: the documented test flow (manual and scripted).
- `docs/DEPLOYMENT.md`: Hostinger deployment steps.
