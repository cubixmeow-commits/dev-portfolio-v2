# SousMeow architecture notes

Focused notes on cross-cutting design decisions. Not exhaustive; see the
code and `SPEC_LOCKED.md` for the rest.

## Discovery taxonomy: categories and collections

Two layers sit over the Cookbook catalog:

- **Categories** are the stable primary spine. Every publicly visible
  Cookbook has exactly one primary category (`cookbooks.primary_category_id`,
  `ON DELETE SET NULL`). Categories own the URL spine (`/categories/{slug}`),
  the breadcrumb, and admin sort. They are a fixed vocabulary; slugs do not
  change casually because the Cookbook seed pipeline references them.
- **Collections** are flexible discovery views (`/collections/{slug}`), a
  many-to-many layer for merchandising. A Cookbook belongs to many
  Collections but only one category. This is deliberately not a
  many-to-many top-level category system: stable spine plus flexible views.

### Membership resolution

`Services\CollectionResolver` is the single place membership resolves.
`collection_type` selects the strategy:

- `editorial`: explicit rows in `cookbook_collections`, curated in the seed.
- `dynamic`: computed at query time (`recently-added` = `created_at` desc).
- `attribute`: derived from Cookbook fields (`deep-workflows` =
  recipe count >= 6; `under-30-minutes` = `est_minutes` <= 30).

`min_display_count` is an honesty gate: a Collection surfaces (in nav,
strips, and its detail route) only once it has that many qualifying
Cookbooks. Below it, or with `is_visible = 0`, the detail route returns the
existing 404. `start-here` is executable-only, enforced in the resolver.

### Accent tokens

`accent` is stored as an allowlisted semantic key (`Services\Accent::KEYS`),
never a hex value or raw CSS. Seed content is validated against the
allowlist at sync time (an unknown key is a fatal seed error). The single
key-to-CSS mapping is `Services\Accent::cssClass()`; the CSS custom
properties live in `tokens.css` and the `.accent-*` classes in
`components.css`.

### Seeding

Categories and collections are versioned, slug-keyed, Git-diffable seed
files (`database/seeds/categories.php`, `collections.php`) synced by
`scripts/seed.php` with the same upsert-and-prune pattern as Cookbooks.
Each Cookbook seed declares its `primary_category` slug and editorial
`collections` by slug. Unknown category slugs, unknown or non-editorial
collection slugs, and off-allowlist accents abort the seed loudly before
any write.

## Alternatives considered

### Legacy `cookbooks.category` string (removal is a follow-up)

Before this taxonomy, each Cookbook stored a single free-text `category`
string, used for display and for category-based search. That column is
**kept for one transitional release** for rollback safety, populated by the
sync from the resolved category name. It is intentionally **not read by any
application query or search** after this change; every read goes through
`primary_category_id` and the `categories` relation.

Removing it now was rejected: keeping it one release lets a rollback to the
prior build find the column populated. The removal is a planned follow-up
once this release is proven in production:

1. Confirm no query references `cookbooks.category` (already true in code).
2. Drop the column in both `schema.mysql.sql` and `schema.sqlite.sql`, and
   add a guarded `DROP COLUMN` to `scripts/seed.php` migrate path.
3. Remove the derived-name write from `sync_content()`.

### Many-to-many top-level categories

Rejected. A Cookbook with several equal "categories" gives no stable URL
spine, breadcrumb, or admin sort, and makes "where does this live" a
per-Cookbook judgement. Collections already provide flexible many-to-many
discovery on top of the single-category spine.

### A view-tracking / analytics table

Deferred. View counting has no defined policy yet (refreshes, bots, and
previews would inflate it) and no surface needs it. Data-driven popularity
labels (most-completed, trending) are held out until both a counting policy
and a real ranking surface exist. Starts, completions, and ratings are
derived from existing records only.

### Foreign key on upgraded SQLite databases

On a fresh install both dialects get the `primary_category_id` foreign key
inline. On an existing database the additive migrate path adds the column
and index in both dialects, and on MySQL (the production target) also adds
the FK via a guarded `ALTER`. SQLite cannot `ALTER`-add a foreign key, so an
upgraded SQLite dev database gets the column and index without the FK;
`--fresh` rebuilds it with the FK if needed. Accepted because SQLite is the
local-dev dialect and MySQL is production.
