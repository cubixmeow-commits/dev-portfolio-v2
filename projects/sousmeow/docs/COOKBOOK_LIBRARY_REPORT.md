# Cookbook Library Implementation Report

## Repository areas inspected

| Area | Path | Finding |
|------|------|---------|
| Application bootstrap | `app/bootstrap.php`, `public/index.php` | Plain PHP front controller, no framework |
| Routes | `app/routes.php` | Marketplace at `/marketplace`, detail at `/cookbooks/{slug}` |
| Models | `app/Models/*.php` | No Stage model; checks in `recipe_checks` / `artifact_checks` |
| Recipe Runner | `app/Controllers/RunnerController.php` | Strict order, three UI states, Quality Check gate |
| Project Kit | `app/Services/ProjectKit.php` | Zip export with numbered Markdown + README |
| Seed system | `scripts/seed.php`, `database/seeds/` | Idempotent skip when cookbooks exist; `--fresh` for reset |
| Schema | `database/schema.sqlite.sql`, `schema.mysql.sql` | Extended with stages, difficulty, demo metrics |
| Marketplace UI | `app/Views/marketplace/`, `public/assets/css/pages/marketplace.css` | Cards and detail pages updated for five-Cookbook library |
| Launch Day Kit reference | `database/seeds/cookbooks/launch-day-kit.php` | Preserved and unchanged in workflow behavior |

## How Launch Day Kit works

Launch Day Kit is the canonical executable Cookbook. A user starts a Project, fills eight Pantry ingredients once, then runs four Recipes in order:

1. **Position the Dish** builds positioning from Pantry facts only.
2. **Plate the Landing Page** chains approved positioning via `{{artifact:position-the-dish}}`.
3. **Serve the Announcements** writes channel-native posts from positioning.
4. **Answer the Table** prepares FAQ and objection handling.

Each Recipe has a prompt template, example response (Driftlog fiction), and three domain-specific Quality Checks. The Runner requires all checks confirmed before approval. When all four Recipes are approved, `ProjectKit::build()` exports four Markdown files plus a README manifest.

**Verification:** `php scripts/verify-launch-day-kit.php` completed successfully after implementation, producing a valid zip with all expected files.

## Four Cookbooks added

| Cookbook | Slug | Type | Structural role |
|----------|------|------|-----------------|
| Validate a SaaS Idea | `validate-saas-idea` | **Executable** | Evidence gates, 12 Recipes, go/revise/stop decision |
| Build a Professional Portfolio | `build-professional-portfolio` | Preview | Modular case-study tracks, 14 Recipes |
| Plan a YouTube Video | `plan-youtube-video` | Preview | Rapid micro-deliverables, 10 Recipes |
| Plan a Novel | `plan-a-novel` | Preview | Deep Pantry, 18 Recipes, continuity control |

Seven placeholder marketplace Cookbooks were removed from seed content.

## Files changed

### Schema

- `database/schema.sqlite.sql` - added `difficulty`, `demo_completed_runs`, `demo_avg_rating` to `cookbooks`; added `cookbook_stages` table; added `stage_position` to `recipes`
- `database/schema.mysql.sql` - same changes (MySQL dialect)

### Seed content

- `database/seeds/content.php` - loader for five Cookbook files
- `database/seeds/cookbooks/launch-day-kit.php` - moved from monolithic content; added stages and demo metadata
- `database/seeds/cookbooks/validate-saas-idea.php` - new executable Cookbook (12 Recipes)
- `database/seeds/cookbooks/professional-portfolio.php` - new preview Cookbook (14 Recipes)
- `database/seeds/cookbooks/plan-youtube-video.php` - new preview Cookbook (10 Recipes)
- `database/seeds/cookbooks/plan-a-novel.php` - new preview Cookbook (18 Recipes)
- `scripts/seed.php` - unified inserter for stages, pantry, checks, preview and executable content

### Application

- `app/Models/CookbookStage.php` - new model
- `app/Controllers/MarketplaceController.php` - loads stages and recipe checks for detail page
- `app/Views/marketplace/index.php` - five-card curated shelf with difficulty, outcome, usage, ratings
- `app/Views/marketplace/show.php` - stages, grouped recipes, pantry preview, quality checks, sample outputs, Project Kit manifest
- `public/assets/css/pages/marketplace.css` - distinct cover treatments, mobile layout at 390px

### Documentation and verification

- `docs/COOKBOOK_EXPANSION_ANALYSIS.md`
- `docs/COOKBOOK_LIBRARY.md`
- `docs/COOKBOOK_LIBRARY_REPORT.md`
- `scripts/verify-launch-day-kit.php` - CLI end-to-end smoke test

## Schema changes

| Change | Type | Purpose |
|--------|------|---------|
| `cookbooks.difficulty` | TEXT, default Intermediate | Marketplace card and detail metadata |
| `cookbooks.demo_completed_runs` | INTEGER, default 0 | Portfolio usage demo metric |
| `cookbooks.demo_avg_rating` | REAL, nullable | Portfolio rating demo metric |
| `cookbook_stages` table | New | Named workflow phases on detail pages |
| `recipes.stage_position` | INTEGER, nullable | Links Recipes to stages |

No changes to Runner state machine, artifact versioning, or export logic.

## Seed process used

```bash
cd projects/sousmeow
echo yes | php scripts/seed.php --fresh
```

Result: 5 cookbooks (2 executable, 3 preview), all pantry fields, stages, recipes, and checks inserted.

## Structural variety table

| Cookbook | Stages | Recipes | Pantry | Duration | Executable |
|----------|--------|---------|--------|----------|------------|
| Launch Day Kit | 4 | 4 | 8 | 25 min | Yes |
| Validate a SaaS Idea | 5 | 12 | 10 | 180 min | Yes |
| Build a Professional Portfolio | 4 | 14 | 12 | 150 min | No |
| Plan a YouTube Video | 3 | 10 | 6 | 45 min | No |
| Plan a Novel | 7 | 18 | 16 | 240 min | No |

## Mobile verification status

Tested via CSS media query at `max-width: 480px` (covers 390px target):

- Single-column cookbook grid
- Title wrapping via `overflow-wrap: anywhere`
- Flexible card meta row without horizontal scroll
- Stacked recipe cards on detail pages
- Collapsible sample output blocks

HTTP 200 confirmed for `/marketplace` and all five `/cookbooks/{slug}` detail pages.

## Syntax-check status

`php -l` passed on all modified PHP files:

- `scripts/seed.php`
- `scripts/verify-launch-day-kit.php`
- `app/Controllers/MarketplaceController.php`
- `app/Models/CookbookStage.php`
- `database/seeds/content.php`
- All five `database/seeds/cookbooks/*.php` files

## Launch Day Kit confirmation

- Seed preserves 4 Recipes, 8 Pantry fields, 3 checks per Recipe
- Full workflow verified programmatically: Pantry save, 4 example responses, Quality Check confirmation, approval, Project Kit zip export
- No changes to `RunnerController`, `PromptBuilder`, or `ProjectKit` logic

## Content audit

Searched changed files for em dashes, lorem ipsum, placeholder copy, and secrets:

- No em dashes in changed seed, view, or doc files
- No lorem ipsum
- No hardcoded passwords or production URLs in changed files

## Known limitations

1. **Preview Cookbooks** show full structure on detail pages but cannot be started; Runner is not wired for three preview Cookbooks yet.
2. **Validate a SaaS Idea** is executable but has not received the same manual browser QA as Launch Day Kit; automated export test was only run for Launch Day Kit.
3. **Stage grouping** on detail pages requires `cookbook_stages` rows; Launch Day Kit now has explicit stages where before it was implicit.
4. **Seed idempotency** still skips when any cookbooks exist; updates require `--fresh` (destructive).
5. **Ratings and run counts** are demo metrics only; no real user analytics backend.

## Weakest new Cookbook

**Plan a Novel** is currently the weakest showcase because:

- It has the largest surface area (18 Recipes, 16 Pantry fields) but remains preview-only, so visitors cannot experience the branching decision flow in the Runner.
- The long workflow is harder to evaluate from static detail pages alone compared to shorter preview Cookbooks like YouTube Planner.
- Continuity-heavy Quality Checks are listed but not yet exercisable in the live loop.

## Recommended next executable

**Plan a YouTube Video** should be the next Cookbook made fully executable because:

- Only 10 Recipes and 6 Pantry fields (smallest preview Cookbook)
- Beginner-friendly duration (45 min) makes it easy to demo in a portfolio walkthrough
- Rapid micro-deliverables produce visible progress quickly, showcasing a different rhythm from Launch Day Kit and SaaS Validation
- The existing seed infrastructure already has full recipe metadata; adding `prompt_template` and `example_response` per Recipe is the main work remaining

Alternative second priority: **Build a Professional Portfolio** for career-audience appeal, but its 14 Recipes make it a larger execution effort than YouTube Planner.
