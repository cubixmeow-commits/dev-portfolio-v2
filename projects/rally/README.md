# Rally

**Health Stats Competition**

Rally is a competitive platform where wearable health and activity statistics become a head-to-head sport. Players contest multi-day series on a single metric. Every day is a comparable observation. Rally is not a fitness tracker or medical tool — it is a competition engine for recorded values.

## Competition Suitability Rule

A metric may become a Rally category when it can be measured consistently enough for the intended match format, compared honestly, and presented without implying medical diagnosis or guaranteed health superiority.

- **Performance metrics** measure intentional activity.
- **Health comparison metrics** compare physiological outcomes.
- Winning a health-stat match does **not** mean someone is healthier overall.

Rally may allow Classic comparison of physiological health statistics, but it does not encourage players to manipulate those values or claim that winning means better overall health.

## Pushability Rule

Baseline matches are only available for metrics a player can safely and intentionally influence during the competition period.

## One-Court Principle

Each match uses exactly one metric type, one competition type (**Classic** or **Baseline**), and one scoring strategy. Do not combine steps, calories, distance, sleep, and heart metrics into one overall match score.

## Competition types and surfaces

Rally separates **how** values are compared from **which** metric is contested:

| Code | Name | Meaning |
| --- | --- | --- |
| `classic` | **Classic** | Head-to-head on recorded values (or series averages for health metrics). |
| `baseline` | **Baseline** | Head-to-head on favorable percentage change from each player's frozen personal baseline. |

Legacy names `direct`, `baseline_improvement`, and the `competition_mode` column are retired; `php scripts/migrate.php` maps them to `competition_type`.

**Presentation surfaces** (from `MetricCompetitionService::formatSurfaceLabel()`):

- **Classic Daily Game Series** — Classic + `daily_wins` (steps, active minutes).
- **Baseline Daily Game Series** — Baseline + `daily_wins` (steps, active minutes only).
- **Health Comparison Series** — Classic + `series_average` (HRV, resting heart rate, sleep duration).

## Metric matrix (V1)

| Metric | Scoring strategy | Classic | Baseline |
| --- | --- | --- | --- |
| Daily Steps | `daily_wins` | Yes | Yes |
| Active Minutes | `daily_wins` | Yes | Yes |
| Resting Heart Rate | `series_average` | Yes | **No** |
| Heart Rate Variability | `series_average` | Yes | **No** |
| Sleep Duration | `series_average` | Yes | **No** |

Rules enforced in `MetricCompetitionService::assertValidCombination()`:

- Baseline **always** requires `daily_wins`.
- **Reject** `baseline` + `series_average`.
- **Reject** Baseline for health comparison metrics (HRV, resting heart rate, sleep duration).

## Canonical health history

Player history lives in **`rly_user_metric_days`** (canonical observations). Match competition snapshots live in **`rly_match_day_results`**. They are related but not interchangeable.

**Ingest flow:**

```
health source
  → UserMetricDayService::ingest()
  → MatchObservationProjectionService::projectObservation()
  → ResultIngestionService::ingest()
  → rly_match_day_results
  → MatchScoringService (derived scoring)
```

- **Source-specific history** — observations are keyed by `(user, metric, data_source, observation_date)`. Never merge readings from different sources into one baseline or history stream.
- **Baseline snapshots** — frozen rows in **`rly_match_baselines`** at match acceptance (transactional with accept). Preferred window: **30** eligible days before match start; **7-day minimum**. Standard deviation is stored for future use; V1 Baseline scoring uses percentage change, not z-score.
- **Match timezone authority** — day boundaries and projection eligibility use `rly_matches.timezone`, not the player's account timezone.

## Try it in two minutes

```sh
cd projects/rally
php scripts/migrate.php          # upgrade existing DB (non-destructive)
php scripts/seed.php             # schema + seeded demo (safe to re-run)
php scripts/seed.php --fresh     # optional: wipe and reseed
php -S localhost:8091 -t public public/index.php
```

Open http://localhost:8091 and sign in:

| Player | Email | Password |
| --- | --- | --- |
| Iain | `iain@rally.demo` | `rally-demo-2026` |
| Mike | `mike@rally.demo` | `rally-demo-2026` |
| Sarah | `sarah@rally.demo` | `rally-demo-2026` |
| Jordan | `jordan@rally.demo` | `rally-demo-2026` |
| Elena | `elena@rally.demo` | `rally-demo-2026` |
| Marcus | `marcus@rally.demo` | `rally-demo-2026` |

Seed installs a simulated application clock at **2026-07-21 noon PDT** so the Iain vs Mike steps showcase is mid-series (5–3, Game 9 live). The seed also creates **every unique player pairing × all five metrics** (15 pairs × 5 = 75 matches), plus one pending invitation.

## Playable beta scope

- User accounts, login / logout / register
- Five seeded metrics: Daily Steps, Active Minutes, Resting Heart Rate, HRV, Sleep Duration
- Two competition types: **Classic** (`classic`) and **Baseline** (`baseline`)
- Two scoring strategies: **daily wins** and **series average**
- Match creation with metric cards, competition type, default length/threshold, 7- or 14-day series
- Baseline preview, transparency, and acknowledgement checkbox (resets on source, metric, or start-date change)
- Invitations with accept / decline
- Live → pending → official settlement
- Tie threshold (difference **<** threshold = tie; equal = decisive); Baseline uses a separate percentage-point threshold
- Source mismatch warnings (honest, non-blocking)
- Derived series scores (never stored)
- Activity feed of structured competition events (`/activity`)
- Derived personal records on player profiles (Classic vs Baseline records)
- Shareable daily result cards for all strategies and competition types
- Development simulation controls for every metric
- Seeded demo matches (active, upcoming, completed, draw, void, pending, source mismatch, Classic and Baseline)

## Explicit non-goals

Native apps, HealthKit / Health Connect / Fitbit / Garmin ingestion, tournaments, brackets, Elo, seasons, payments, push notifications, chat, followers, comments, likes, teams, medical interpretation, multi-metric courts inside one match, z-score Baseline scoring in V1.

## Technical stack

- PHP 8.2+ (verified on 8.3), MySQL or SQLite via PDO
- Plain PHP front controller (no Composer, no Node build, no Docker)
- Apache-friendly `public/` document root (Hostinger-ready)

## Folder structure

```
projects/rally/
├── app/           Core, Controllers, Models, Services, Views
├── config/        config.example.php (copy to config.php)
├── database/      schema.mysql.sql, schema.sqlite.sql
├── docs/          Architecture, game rules, future ingest
├── public/        Web root (index.php, assets)
├── scripts/       migrate.php, seed.php (CLI only)
├── storage/       SQLite DB + clock override (gitignored)
└── tests/         run.php deterministic domain tests
```

## Installation

```sh
cd projects/rally
cp config/config.example.php config/config.php   # optional local edits
# optional: cp .env.example .env
php scripts/migrate.php
php scripts/seed.php
php scripts/seed.php --status
php -S localhost:8091 -t public public/index.php
```

MySQL (production): set `db.driver` to `mysql` and credentials in `config.php`, then run `php scripts/migrate.php` and `php scripts/seed.php`.

### Upgrading an existing install

`CREATE TABLE IF NOT EXISTS` will not alter existing tables. Run the non-destructive migrator:

```sh
php scripts/migrate.php
php scripts/migrate.php --status
```

That adds missing columns (`competition_type`, `baseline_tie_threshold` on `rly_matches`), creates `rly_user_metric_days` and `rly_match_baselines`, upserts metric definitions, and migrates legacy `competition_mode` / `direct` / `baseline_improvement` values to `classic` / `baseline`. For a full demo reset afterward, run `php scripts/seed.php --fresh`.

## Environment configuration

| Key | Purpose |
| --- | --- |
| `app.env` | `development` unlocks simulation for signed-in users; `production` restricts simulation to admins |
| `app.url` / `APP_URL` | Absolute origin |
| `app.base_path` / `APP_BASE_PATH` | Subdirectory deploy prefix |
| `app.settlement_hour` | Default `6` — local hour when a day becomes official |
| `app.settlement_lag_days` | Default `2` — Day N settles at 06:00 on Day N+2 in the **match** timezone |
| `db.*` | sqlite (default) or mysql |
| `session.*` | Cookie name `rally_session`, idle TTL, secure flag |

## Authenticated navigation

Dashboard · Matches · Activity · Create · Profile (plus Simulation in development)

## Match lifecycle

`invited` → `scheduled` → `active` → `settling` → `completed` (or `cancelled`)

A match becomes **completed** only when every match day is `official` or `void`. After the final competition date ends but before all days settle, status is **settling**. Do not show a final winner while provisional.

## Settlement rules

Day **N** becomes official at **06:00 on day N+2** in `rly_matches.timezone` (not player account timezones). User timezone is presentational only.

Statuses: `scheduled` → `live` → `pending` → `official` | `void`

## Tie-threshold rules

**Classic:** `absolute_difference < tie_threshold` → tie / within threshold. Difference **equal to** the threshold is decisive for the metric direction (`higher_wins`). For series averages the threshold applies to the final official average difference.

**Baseline:** daily games compare favorable percentage change from each player's frozen baseline mean. `absolute_percentage_difference < baseline_tie_threshold` → tie (default 1.00 percentage points). Equal to the threshold is decisive. Series score remains daily wins.

## Missing-data policy

Before settlement: show “Awaiting sync.” Never treat missing as zero. At settlement with one or both players missing: mark the day **void**. Voids award no wins and are excluded from official averages.

## Source mismatch policy

Each player declares a data source. Matching source classes show a comparable notice. Differing classes (e.g. watch vs phone) show a clear warning. Matches still proceed in V1. Canonical history and baselines always use the **declared source only** — never blend sources.

## Derived-score architecture

Series score, averages, streaks, margins, voids, and ties are computed by `MatchScoringService` from official days. Activity events and personal records are also derived. Nothing is stored in statistics or social tables. Frozen baselines in `rly_match_baselines` are the exception — they are persisted snapshots, not live scores.

## Simulation workflow

Visit `/simulation` in development (or as admin). Select any metric match, advance the application clock, edit day values through `UserMetricDayService` / `ResultIngestionService`, settle days, recalculate strategy summaries, and preview feed events. Reset demo data with `php scripts/seed.php`. Clock override is stored in `storage/clock_override.txt`.

## Security notes

Prepared statements only (`Database`), CSRF on every POST, password hashing (`PASSWORD_DEFAULT`), session hardening, match membership checks, simulation gated, official days locked against ordinary ingestion.

## Hostinger deployment

1. Upload `projects/rally` and set the site document root to `.../rally/public`
2. Copy `config.example.php` → `config.php`: `env=production`, HTTPS URL, `session.secure=true`, MySQL credentials
3. Upgrade schema if this is an existing install: `php scripts/migrate.php`
4. `php scripts/seed.php` (demo reseed) then `php scripts/migrate.php --status`
5. Ensure `storage/` is writable and not web-accessible

See also `docs/DEPLOYMENT.md`.

## Future health ingestion

A future mobile companion app should call `UserMetricDayService::ingest()` once per canonical observation; projection fans values into eligible matches via `MatchObservationProjectionService` and `ResultIngestionService`. Seeds and simulation already use this path. See `docs/HEALTH_INGEST_FUTURE.md`.

## Testing

```sh
php tests/run.php
```

## Documentation

| Doc | Contents |
| --- | --- |
| `docs/GAME_RULES.md` | Classic/Baseline, strategies, settlement, voids, ties, suitability |
| `docs/ARCHITECTURE.md` | Services, canonical history, schema, data flow |
| `docs/HEALTH_INGEST_FUTURE.md` | Connector boundary via `UserMetricDayService` |
| `docs/DEPLOYMENT.md` | Hostinger checklist and migration |
| `docs/DESIGN.md` | Visual system notes |

## Disclaimer

Rally presents health and activity data for friendly comparison and entertainment. It does not provide medical advice or determine overall health.

## Known limitations

- Beta data is seeded/simulated only
- No email verification or password-reset emails
- No real wearable sync yet (ingest boundary is defined)
- Baseline scoring uses percentage change, not z-score (stddev stored for future)
- Shared hosting: CLI seed/migrate required (no web installer)
