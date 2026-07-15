# Rally deployment (Hostinger)

1. Upload the full `projects/rally` tree.
2. Point the domain / subdomain document root at `public/`.
3. Copy `config/config.example.php` to `config/config.php`.
4. Set:
   - `app.env` → `production`
   - `app.url` → HTTPS origin
   - `session.secure` → `true`
   - `db.driver` → `mysql` with Hostinger credentials
5. Ensure PHP 8.2+ with `pdo_mysql`, `mbstring`.
6. From SSH or Hostinger terminal:

```sh
cd /path/to/rally
php scripts/migrate.php          # required when upgrading an existing DB (non-destructive)
php scripts/migrate.php --status
php scripts/seed.php             # fresh install / demo reseed (safe to re-run)
php scripts/seed.php --fresh     # optional: destructive wipe + full reseed
php scripts/seed.php --status
```

## Schema upgrades (Classic / Baseline)

`CREATE TABLE IF NOT EXISTS` alone will not upgrade an existing database. Run `php scripts/migrate.php` after deploying builds that add:

### New columns on `rly_matches`

| Column | Purpose |
| --- | --- |
| `competition_type` | `classic` or `baseline` (default `classic`) |
| `baseline_tie_threshold` | Percentage-point tie band for Baseline daily games (nullable for Classic) |

### New tables

| Table | Purpose |
| --- | --- |
| `rly_user_metric_days` | Canonical daily observations (user × metric × source × date) |
| `rly_match_baselines` | Frozen baseline snapshots per player per match at acceptance |

### Legacy migration

If the database still has `competition_mode`, `direct`, or `baseline_improvement`, `migrate.php` maps them to `competition_type` (`classic` / `baseline`) and drops the old column. `migrate.php --status` reports `competition_mode: PRESENT (should be migrated away)` until fixed.

The migrator also upserts the five metric definitions (`steps`, `active_minutes`, `resting_heart_rate`, `hrv`, `sleep_duration`) with `classification` and `scoring_strategy` — required after older steps-only installs that lacked `scoring_strategy` (the profile `no such column: mt.scoring_strategy` error).

7. Confirm `storage/` is writable by PHP and not publicly listable.
8. Do not commit `config.php` or `.env`.

No Node.js, Composer, Docker, or background workers are required.

Simulation is admin-only when `app.env` is `production`.

## Verify after migrate

```sh
php scripts/migrate.php --status
```

Expect:

- All `rly_metric_types` columns: OK
- `rly_matches.competition_type` and `baseline_tie_threshold`: OK
- `competition_mode`: absent (OK)
- `rly_user_metric_days`: OK
- `rly_match_baselines`: OK

Optional: `php scripts/seed.php` to load demo matches including Classic and Baseline examples with canonical history.
