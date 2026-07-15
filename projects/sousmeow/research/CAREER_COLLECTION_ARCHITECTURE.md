# Career Collection — architecture findings (pre-implementation)

## Canonical seed schema

Cookbook seeds under `database/seeds/cookbooks/*.php` return arrays upserted by `scripts/seed.php`.
Required cookbook keys: `slug`, `title`, `tagline`, `description`, `primary_category`, `audience`, `outcome`, `is_executable`, `accent`, `stages`, `fields`, `recipes`.
Optional: `collections`, `price_cents`, `difficulty`, `est_minutes`, `demo_completed_runs`, `demo_avg_rating`, `sort_order`.

Recipe keys used by Runner today: `slug`, `title`, `summary`, `stage_position`, `why_it_matters`, `unlocks_text`, `est_minutes`, `prompt_template`, `example_response`, `output_sections`, `checks`.

## Runner capabilities

`RunnerController` + `runner/step.php` wizards: understand → prompt → paste → review → approved.
Understanding step displays `summary`, `why_it_matters`, `unlocks_text`.
**Gap:** no first-class `before_you_begin`, `common_problems`, or `recovery_guidance` fields — guidance was buried in `why_it_matters` prose.

## Career category today

`career-freelance` already exists with:
- `build-professional-portfolio` (preview)
- `prep-for-an-interview` (executable, 8 recipes)

No related-Cookbook recommendations on marketplace detail pages.

## Integrity approach for this collection

1. Extend `recipes` with `before_you_begin`, `common_problems`, `recovery_guidance` (TEXT) and teach the Runner understand step to render them.
2. Place privacy/prerequisites in Cookbook `description` + first Recipe `before_you_begin` (no cookbook-level column without a broader migration).
3. Enhance existing Interview Cookbook in place (same slug) instead of duplicating.
4. Zero/null demo metrics for honesty (`demo_completed_runs` = 0, `demo_avg_rating` = null).
5. Unique stage shapes for all new/updated Cookbooks.

## Overlap decision

**Prep for an Interview** → upgraded to full Interview Prep + Mock Interview (Cookbook 2). Portfolio remains preview-only (case-study site brief), distinct from LinkedIn Profile Cookbook.
