# Cookbook Expansion Analysis

Analysis completed before expanding the SousMeow marketplace from eight placeholder Cookbooks to a curated five-Cookbook first-party library.

## Project Location

- **Root:** `/workspace/projects/sousmeow/`
- **Entry:** `public/index.php` (front controller)
- **Routes:** `app/routes.php`
- **Seed:** `scripts/seed.php` + `database/seeds/`

## Current Architecture

SousMeow is a plain PHP 8 application with SQLite (local) or MySQL (production). It does not call external AI APIs. The product loop is:

1. Browse Cookbooks on the Marketplace (`GET /marketplace`)
2. Start an executable Cookbook (`POST /projects`)
3. Fill the Pantry once (`GET/POST /projects/{id}/pantry`)
4. Run Recipes in strict order (`GET /projects/{id}/run/{position}`)
5. Paste AI output, confirm Quality Checks, approve Artifacts
6. Export a Project Kit zip when all Recipes are approved

### Data model

| Concept | Storage | Notes |
|---------|---------|-------|
| Cookbook | `cookbooks` | Catalog metadata, `is_executable`, `status`, `accent` |
| Stage | `cookbook_stages` (added) | Named workflow phases; Recipes link via `stage_position` |
| Recipe | `recipes` | Ordered steps; `prompt_template` NULL for preview-only |
| Pantry field | `pantry_fields` | Ingredient schema per Cookbook |
| Quality Check | `recipe_checks` | Definition per Recipe |
| Project | `projects` | User run of one Cookbook |
| Pantry value | `pantry_values` | Per-project ingredient answers |
| Artifact | `artifacts` + `artifact_versions` | Immutable version chain per Recipe |
| Check confirmation | `artifact_checks` | Human confirmation per version |
| Export | `exports` + `storage/exports/` | Zip metadata and files |

There is no `Stage` PHP model class; stages are loaded via `CookbookStage` and grouped in views.

### Executable vs preview

| Flag | Executable | Preview |
|------|------------|---------|
| `is_executable` | `1` | `0` |
| `status` | `available` | `coming_soon` |
| `prompt_template` | Required non-null | NULL |
| `example_response` | One per Recipe | NULL (sample shown on detail only via `sample_output` field in seed, stored as `example_response` for display) |
| Pantry | Seeded | Seeded for full preview structure |
| Quality Checks | Seeded, enforced in Runner | Seeded, shown on detail page |
| Start button | Shown when logged in | Hidden; honest preview CTA |

`ProjectController::create()` rejects non-executable Cookbooks server-side. `RunnerController` 404s when `prompt_template` is null.

## How Launch Day Kit Works

**Slug:** `launch-day-kit`  
**Reference seed:** `database/seeds/cookbooks/launch-day-kit.php`

### Intended user

Indie makers and small teams shipping a v1 who need launch copy fast without inventing claims.

### Expected outcome

Positioning statement, landing page copy, channel-native announcements, and an honest FAQ packaged as a Project Kit.

### Workflow shape

Four sequential Recipes with no named stages (each Recipe is effectively one launch phase). Pantry is filled once with eight ingredients across six field types. Recipes 2-4 chain approved Artifacts from Recipe 1 via `{{artifact:position-the-dish}}`.

### Prompt structure

- Pantry placeholders: `{{product_name}}`, `{{one_liner}}`, etc.
- Artifact chaining: `{{artifact:recipe-slug}}`
- Explicit section headings in prompts
- "Do not invent" guardrails throughout

### Quality Checks

Three domain-specific checks per Recipe (12 total). Runner requires all checks confirmed on the latest version before approval.

### Project Kit

Four Markdown files (`01-position-the-dish.md` through `04-answer-the-table.md`) plus `README.md` with Pantry snapshot.

### Distinctive feature

Deadline-driven sequential campaign assembly: one approved positioning doc becomes ground truth for every downstream Recipe.

## Reusable Patterns

1. **Pantry once, chain forever** - early Recipes establish truth; later prompts reference `{{artifact:slug}}`.
2. **Example responses** - realistic Markdown samples enable Demo Mode without AI.
3. **Three checks per Recipe** - specific enough to encode domain judgment, not generic quality gates.
4. **Accent classes** - `accent-{hue}` on cards drives distinct cover bands via CSS.
5. **Idempotent seed** - skip content when `cookbooks` count > 0; `--fresh` for full reset.
6. **Honest availability** - badges and CTAs match `is_executable`; no fake checkout.

## Limitations Found

1. **No Stage entity** - workflow phases were implicit in Recipe order only; detail pages could not show phase groupings.
2. **Preview shells were thin** - old marketplace Cookbooks had title/summary only; no Pantry, checks, or why-it-matters on detail pages.
3. **Missing marketplace metadata** - no difficulty, usage demo metrics, or ratings in schema/UI.
4. **Seed structure** - single monolithic `content.php` did not scale to five full Cookbooks.
5. **One executable only** - infrastructure already supports multiple executables; only content was missing.

## Smallest Changes Required

1. Add columns to `cookbooks`: `difficulty`, `demo_completed_runs`, `demo_avg_rating`.
2. Add `cookbook_stages` table and `stage_position` on `recipes`.
3. Split seed content into per-Cookbook files; extend seed inserter for stages, full preview content, and optional second executable.
4. Update Marketplace views to show difficulty, usage, stages, Pantry, and Quality Checks on preview detail pages.
5. Replace seven placeholder marketplace Cookbooks with four fully designed preview Cookbooks (plus one new executable).

No changes to authentication, Runner state machine, artifact versioning, export logic, or global routing.

## Risks to Existing Executable Flow

| Risk | Mitigation |
|------|------------|
| Breaking Launch Day Kit IDs | Keep slug and structure; seed only on fresh DB |
| Runner regression | Do not alter `RunnerController` or `PromptBuilder` |
| False Start buttons | Server gate unchanged; only `is_executable = 1` starts |
| Schema drift SQLite/MySQL | Both schema files updated in lockstep |
| Em dash / placeholder copy | Grep audit on all changed files before commit |

## Expansion Plan

Final library (five Cookbooks, five distinct structural signatures):

| Cookbook | Availability | Stages | Recipes | Pantry |
|----------|--------------|--------|---------|--------|
| Launch Day Kit | Executable | 4 implicit | 4 | 8 |
| Validate a SaaS Idea | Executable | 5 | 12 | 10 |
| Build a Professional Portfolio | Preview | 4 | 14 | 12 |
| Plan a YouTube Video | Preview | 3 | 10 | 6 |
| Plan a Novel | Preview | 7 | 18 | 16 |

Full design specifications are in `COOKBOOK_LIBRARY.md`.
