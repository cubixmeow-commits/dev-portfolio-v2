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

## Domain services

| Service | Responsibility |
| --- | --- |
| `Clock` | Application clock with development override (`storage/clock_override.txt`) |
| `MetricComparisonService` | Higher/lower wins + tie threshold |
| `MatchScoringService` | Derived series summary from official days |
| `ResultIngestionService` | Sole write path for match-day values |
| `SettlementService` | Day + match lifecycle against Clock + match timezone |
| `MatchService` | Create, accept/decline, source comparability |
| `SimulationService` | Dev clock + ingestion tooling |

## Schema (`rly_` prefix)

- `rly_users`
- `rly_metric_types`
- `rly_data_sources`
- `rly_matches`
- `rly_match_days`
- `rly_match_day_results`
- `rly_rate_events` (auth rate limiting only)

Unique constraint: one result row per `(match_day_id, user_id)`. Updates happen in place.

## Clock discipline

Domain code uses `Clock::now()` / `Clock::nowUtcString()`. Do not call `date()`, `time()`, or `new DateTimeImmutable()` for “now” inside services. Explicit timestamps from storage remain fine.

## Scoring data flow

1. Ingest provisional values via `ResultIngestionService`
2. `SettlementService::refreshMatch` advances day statuses
3. `MatchScoringService::summarize` derives wins/ties/voids/leader
