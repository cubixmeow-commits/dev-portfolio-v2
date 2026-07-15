# Rally architecture

## Pattern lineage

Rally reuses SousMeow’s structural conventions (front controller, Core stack, CSRF, CLI seed, dual schema files) without copying product identity. Namespace: `Rally\`.

## Layers

| Layer | Role |
| --- | --- |
| `app/Core` | Env, Config, Database, Session, Auth, Csrf, Router, View, Flash, RateLimiter |
| `app/Controllers` | Thin HTTP handlers |
| `app/Models` | Static data access (`User`, `GameMatch`, `MetricType`, `DataSource`) |
| `app/Services` | Domain rules |
| `app/Views` | PHP templates |

## Competition model

Two orthogonal axes:

1. **Competition type** (`rly_matches.competition_type`) — `classic` or `baseline`.
2. **Scoring strategy** (`rly_metric_types.scoring_strategy`) — `daily_wins` or `series_average`.

| Code | Label | Daily winner decided by |
| --- | --- | --- |
| `classic` | Classic | Raw recorded values (`MetricComparisonService`) |
| `baseline` | Baseline | Favorable % change from frozen baseline (`BaselineCompetitionService`) |

**Presentation surfaces** (`MetricCompetitionService::formatSurfaceLabel()`):

| Surface | competition_type | scoring_strategy |
| --- | --- | --- |
| Classic Daily Game Series | `classic` | `daily_wins` |
| Baseline Daily Game Series | `baseline` | `daily_wins` |
| Health Comparison Series | `classic` | `series_average` |

### Metric matrix (V1)

| Metric slug | scoring_strategy | Classic | Baseline |
| --- | --- | --- | --- |
| `steps` | `daily_wins` | ✓ | ✓ |
| `active_minutes` | `daily_wins` | ✓ | ✓ |
| `resting_heart_rate` | `series_average` | ✓ | ✗ |
| `hrv` | `series_average` | ✓ | ✗ |
| `sleep_duration` | `series_average` | ✓ | ✗ |

`MetricCompetitionService::assertValidCombination()` rejects `baseline` + `series_average` and Baseline for health metrics. Baseline always requires `daily_wins`.

### Pushability Rule

Baseline matches are only available for metrics a player can safely and intentionally influence during the competition period.

### Health comparison stance

Rally may allow Classic comparison of physiological health statistics, but it does not encourage players to manipulate those values or claim that winning means better overall health.

### Migration from legacy names

Older builds used `competition_mode` with values `direct` and `baseline_improvement`. `scripts/migrate.php` (via `migrate_columns.php`) maps:

- `direct` / empty → `competition_type = 'classic'`
- `baseline` / `baseline_improvement` → `competition_type = 'baseline'`

Then drops `competition_mode`. New code reads only `competition_type`.

## Domain services

| Service | Responsibility |
| --- | --- |
| `Clock` | Application clock with development override (`storage/clock_override.txt`) |
| `UserMetricDayService` | Canonical daily observations in `rly_user_metric_days`; sole write path for player history |
| `MatchObservationProjectionService` | Fans canonical observations into eligible `rly_match_day_results` via `ResultIngestionService` |
| `BaselineService` | Estimate, freeze, and serve personal baseline snapshots (`rly_match_baselines`) |
| `BaselineCompetitionService` | Percentage-change math and daily Baseline outcomes |
| `MetricComparisonService` | Higher/lower wins + tie threshold (Classic raw values) |
| `MetricCompetitionService` | Strategy/type helpers, surface labels, combination validation |
| `MetricFormatter` | Centralized unit / sleep duration formatting |
| `MatchScoringService` | Derived summaries for all type × strategy pairs (never stored) |
| `ResultIngestionService` | Sole write path for match-day competition snapshots |
| `SettlementService` | Day + match lifecycle against Clock + match timezone |
| `MatchService` | Create, accept/decline, baseline preview, transactional freeze on accept |
| `ActivityFeedService` | Derived competition events for `/activity` |
| `PersonalRecordsService` | Derived personal records for profiles (Classic vs Baseline buckets) |
| `SimulationService` | Dev clock + ingestion tooling |

## Schema (`rly_` prefix)

### Core tables

- `rly_users`
- `rly_metric_types` (classification, scoring_strategy, defaults, context_note, …)
- `rly_data_sources`
- `rly_matches` — includes `competition_type` (`classic`|`baseline`), `baseline_tie_threshold` (nullable; Baseline only)
- `rly_match_days`
- `rly_match_day_results` — one numeric `metric_value` per player per day (competition snapshot)
- `rly_rate_events` (auth rate limiting only)

### Canonical history and baseline tables

- **`rly_user_metric_days`** — canonical observations independent of any match
  - Unique per `(user_id, metric_type_id, data_source_id, observation_date)`
  - Optional `source_record_key` for idempotent connector updates
  - **Never merge sources** — each row belongs to exactly one `data_source_id`

- **`rly_match_baselines`** — frozen baseline snapshots per `(match_id, user_id)`
  - `baseline_mean`, `baseline_median`, `baseline_standard_deviation`, min/max, sample_count, window dates
  - Written once at acceptance inside a transaction (`BaselineService::freezeForMatch`)
  - Stddev is persisted for future scoring models; V1 uses percentage change only

No followers, comments, posts, rankings, or stored career-stats tables.

Unique constraint on results: one row per `(match_day_id, user_id)`. Updates happen in place until the day is `official` or `void`.

## Canonical vs competition data

| Store | Table | Purpose |
| --- | --- | --- |
| Canonical history | `rly_user_metric_days` | Long-lived player observations by source |
| Competition snapshot | `rly_match_day_results` | Values contested for a specific match day |
| Frozen baseline | `rly_match_baselines` | Personal mean/etc. locked at match accept |

Canonical rows are **not** match scores. Match rows are **not** a substitute for history. Projection connects them when source, metric, date, and membership align.

## Data flow

### Health ingest → scoring

```
health source (seed / simulation / future mobile app)
  → UserMetricDayService::ingest()
       writes rly_user_metric_days
  → MatchObservationProjectionService::projectObservation()
       finds eligible match days (accepted match, same metric, competition_date,
       declared source matches observation source)
  → ResultIngestionService::ingest()
       writes rly_match_day_results
  → SettlementService::refreshMatch()
  → MatchScoringService::summarize()
       derives wins / averages / leader (not persisted)
```

Direct match-day writes (simulation shortcuts) may call `ResultIngestionService` alone, but production connectors should prefer the canonical path so history and baselines stay consistent.

### Baseline freeze at acceptance

Inside `MatchService::accept()` (or auto-accept create):

1. Validate Baseline availability (`BaselineService::availabilityForSources` — 7-day minimum, 30-day preferred window of dates **before** `start_date`).
2. Require `baseline_acknowledged` checkbox.
3. In one DB transaction: update match sources/status → `BaselineService::freezeForMatch()` → `SettlementService::refreshMatch()`.
4. Frozen rows are never overwritten.

Baseline preview on create/accept is **estimated** from live `rly_user_metric_days`; frozen rows are the authority during the match.

### Baseline daily scoring

`MatchScoringService` delegates daily outcomes to `BaselineCompetitionService::dayOutcome()`:

- Favorable-direction percentage: `(current − mean) / mean × 100` for higher-wins metrics.
- Compare percentages with `baseline_tie_threshold` (percentage points, not z-score).
- Series winner = most daily wins (same as Classic `daily_wins`).

## Clock discipline

Domain code uses `Clock::now()` / `Clock::nowUtcString()`. Do not call `date()`, `time()`, or `new DateTimeImmutable()` for “now” inside services. Explicit timestamps from storage remain fine.

## Timezone authority

`rly_matches.timezone` defines:

- `rly_match_days.competition_date` boundaries
- Settlement schedule (06:00 on day N+2)
- Which calendar day a canonical observation projects into

`rly_users.timezone` is presentational only.

## Baseline transparency (UI)

Create and accept flows show estimated means, sample counts, and typical ranges. The acknowledgement checkbox resets when the user changes metric, data source, or start date (client-side on create; accept page notes source changes recalculate preview and reset acknowledgement).

## CLI migration

```sh
php scripts/migrate.php          # non-destructive, idempotent
php scripts/migrate.php --status
php scripts/seed.php             # seed demo data
php scripts/seed.php --fresh     # optional destructive wipe + reseed
```

`migrate.php` adds columns, creates history tables, upserts metrics, and migrates legacy `competition_mode` values.

## Scoring data flow (summary)

1. Ingest canonical observations via `UserMetricDayService` (or match snapshots via `ResultIngestionService`)
2. `SettlementService::refreshMatch` advances day statuses
3. `MatchScoringService::summarize` derives wins/averages/ties/voids/leader from the match metric’s type and strategy
4. Views receive prepared presentation models (`MetricFormatter`, `MetricCompetitionService::scoreline`, `BaselineCompetitionService` for Baseline panels)
