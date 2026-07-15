# Rally

**Health Stats Competition**

Rally is a competitive platform where wearable health and activity statistics become a head-to-head sport. Players contest multi-day series on a single metric. Every day is a comparable observation. Rally is not a fitness tracker or medical tool — it is a competition engine for recorded values.

## Competition Suitability Rule

A metric may become a Rally category when it can be measured consistently enough for the intended match format, compared honestly, and presented without implying medical diagnosis or guaranteed health superiority.

- **Performance metrics** measure intentional activity.
- **Health comparison metrics** compare physiological outcomes.
- Winning a health-stat match does **not** mean someone is healthier overall.

## One-Court Principle

Each match uses exactly one metric type and one scoring strategy. Do not combine steps, calories, distance, sleep, and heart metrics into one overall match score.

## Try it in two minutes

```sh
cd projects/rally
php scripts/seed.php          # schema + seeded demo (safe to re-run; use --fresh to wipe)
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

Seed installs a simulated application clock at **2026-07-21 noon PDT** so the Iain vs Mike showcase is mid-series (steps 5–3, Game 9 live), with additional active/completed series for every enabled metric.

## Playable beta scope

- User accounts, login / logout / register
- Five seeded metrics: Daily Steps, Active Minutes, Resting Heart Rate, HRV, Sleep Duration
- Two scoring strategies: **daily wins** and **series average**
- Match creation with metric cards, default length/threshold, 7- or 14-day series
- Invitations with accept / decline
- Live → pending → official settlement
- Tie threshold (difference **<** threshold = tie; equal = decisive)
- Source mismatch warnings (honest, non-blocking)
- Derived series scores (never stored)
- Activity feed of structured competition events (`/activity`)
- Derived personal records on player profiles
- Shareable daily result cards for both strategies
- Development simulation controls for every metric
- Seeded demo matches (active, upcoming, completed, draw, void, pending, source mismatch)

## Explicit non-goals

Native apps, HealthKit / Health Connect / Fitbit / Garmin ingestion, tournaments, brackets, Elo, seasons, payments, push notifications, chat, followers, comments, likes, teams, medical interpretation, multi-metric courts inside one match.

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
├── scripts/       seed.php (CLI only)
├── storage/       SQLite DB + clock override (gitignored)
└── tests/         run.php deterministic domain tests
```

## Installation

```sh
cd projects/rally
cp config/config.example.php config/config.php   # optional local edits
# optional: cp .env.example .env
php scripts/seed.php
php scripts/seed.php --status
php -S localhost:8091 -t public public/index.php
```

MySQL (production): set `db.driver` to `mysql` and credentials in `config.php`, then run `php scripts/seed.php`.

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

`absolute_difference < tie_threshold` → tie / within threshold. Difference **equal to** the threshold is decisive for the metric direction (`higher_wins`). For series averages the threshold applies to the final official average difference.

## Missing-data policy

Before settlement: show “Awaiting sync.” Never treat missing as zero. At settlement with one or both players missing: mark the day **void**. Voids award no wins and are excluded from official averages.

## Source mismatch policy

Each player declares a data source. Matching source classes show a comparable notice. Differing classes (e.g. watch vs phone) show a clear warning. Matches still proceed in V1.

## Derived-score architecture

Series score, averages, streaks, margins, voids, and ties are computed by `MatchScoringService` from official days. Activity events and personal records are also derived. Nothing is stored in statistics or social tables.

## Simulation workflow

Visit `/simulation` in development (or as admin). Select any metric match, advance the application clock, edit day values through `ResultIngestionService`, settle days, recalculate strategy summaries, and preview feed events. Reset demo data with `php scripts/seed.php`. Clock override is stored in `storage/clock_override.txt`.

## Security notes

Prepared statements only (`Database`), CSRF on every POST, password hashing (`PASSWORD_DEFAULT`), session hardening, match membership checks, simulation gated, official days locked against ordinary ingestion.

## Hostinger deployment

1. Upload `projects/rally` and set the site document root to `.../rally/public`
2. Copy `config.example.php` → `config.php`: `env=production`, HTTPS URL, `session.secure=true`, MySQL credentials
3. `php scripts/seed.php` then `php scripts/seed.php --status`
4. Ensure `storage/` is writable and not web-accessible

See also `docs/DEPLOYMENT.md`.

## Future health ingestion

`ResultIngestionService::ingest()` is the normalized boundary for a future iOS/Android connector. Seeds and simulation already use it. See `docs/HEALTH_INGEST_FUTURE.md`.

## Testing

```sh
php tests/run.php
```

## Documentation

| Doc | Contents |
| --- | --- |
| `docs/GAME_RULES.md` | Strategies, settlement, voids, ties, suitability |
| `docs/ARCHITECTURE.md` | Services, clock, schema |
| `docs/HEALTH_INGEST_FUTURE.md` | Connector boundary |
| `docs/DEPLOYMENT.md` | Hostinger checklist |
| `docs/DESIGN.md` | Visual system notes |

## Disclaimer

Rally presents health and activity data for friendly comparison and entertainment. It does not provide medical advice or determine overall health.

## Known limitations

- Beta data is seeded/simulated only
- No email verification or password-reset emails
- No real wearable sync
- Shared hosting: CLI seed required (no web installer)
