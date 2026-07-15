# Future health data ingestion

V1 proves the game with seeded/simulated data. Real wearable connectors are a later phase. The ingest architecture is already in place: canonical history first, then projection into matches.

## Two-layer storage

| Layer | Table | Written by |
| --- | --- | --- |
| Canonical history | `rly_user_metric_days` | `UserMetricDayService::ingest()` |
| Competition snapshot | `rly_match_day_results` | `ResultIngestionService::ingest()` (via projection or direct simulation) |

Never treat match-day rows as the system of record for player history. Never bypass ingestion to write `rly_match_day_results` from controllers.

## End-to-end flow

```
health source (HealthKit, Health Connect, Fitbit, …)
  → UserMetricDayService::ingest()
  → MatchObservationProjectionService::projectObservation()
  → ResultIngestionService::ingest()
  → rly_match_day_results
  → SettlementService / MatchScoringService
```

A future **mobile companion app** should call `UserMetricDayService::ingest()` once per observation. Projection fans the value into every eligible accepted match for that user, metric, calendar date, and declared source.

## Canonical ingest boundary

Call `Rally\Services\UserMetricDayService::ingest()` with:

```json
{
  "user_id": 1,
  "metric_type": "steps",
  "observation_date": "2026-07-21",
  "value": 11248,
  "data_source": "apple_watch",
  "source_record_key": "external-record-key",
  "source_timezone": "America/Los_Angeles",
  "is_manual": false,
  "observed_at": "2026-07-21T23:58:00Z",
  "ingested_at": "2026-07-22T08:14:00Z",
  "project": true
}
```

### Behavior

- Validates user, metric, source, value range, and `observation_date` format (`YYYY-MM-DD`)
- Upserts `rly_user_metric_days` — unique per `(user, metric, source, observation_date)`; same `source_record_key` updates in place (idempotent)
- Rejects future observation dates (unless admin override)
- When `project` is true (default), calls `MatchObservationProjectionService::projectObservation()`
- **Source-specific history** — observations from different `data_source_id` values are never merged. Baselines and history queries always filter by the declared source.

### Projection rules

`MatchObservationProjectionService` finds match days where:

- `competition_date` equals `observation_date`
- Match metric matches the observation metric
- Invitation is `accepted`
- User is a participant
- Observation `data_source_id` equals the player's **declared** match source

If the source does not match, projection records `source_mismatch` and skips the write. Official or void days reject ordinary updates.

Projection always delegates to `ResultIngestionService::ingest()` — it never writes `rly_match_day_results` directly.

## Match-day ingest (internal)

`ResultIngestionService::ingest()` remains the sole write path for competition snapshots:

```json
{
  "user_id": 1,
  "match_day_id": 12,
  "metric_type": "steps",
  "value": 11248,
  "data_source": "apple_watch",
  "source_record_key": "external-record-key",
  "is_manual": false,
  "ingested_at": "2026-07-21T08:14:00Z"
}
```

- Validates membership, metric, source, and value range
- Inserts or updates the single row for `(match_day_id, user_id)`
- Rejects ordinary updates once the day is `official` or `void`
- Assigns values to the match-day using the **match timezone** calendar, not the user's home timezone

Connectors should prefer `UserMetricDayService` so canonical history, Baseline calculation, and profiles stay accurate.

## Baseline dependency

`BaselineService` reads only from `rly_user_metric_days` for the match's declared source:

- Preferred window: 30 days before `start_date`
- Minimum: 7 days
- Frozen at acceptance into `rly_match_baselines`

Insufficient canonical history blocks Baseline match acceptance. Seed and simulation data should populate `rly_user_metric_days` (not only match results) for realistic Baseline demos.

## HealthKit (future iOS companion)

An iOS companion would:

1. Read samples for the relevant metric on the match timezone's calendar day (or the user's chosen observation date)
2. Map the device/source to a `rly_data_sources` slug
3. POST a normalized payload to a future authenticated API that calls `UserMetricDayService::ingest()`
4. Respect settlement locks and projection errors returned by the server
5. Use stable `source_record_key` values for idempotent sync

## Health Connect (future Android companion)

An Android companion follows the same boundary with Health Connect records and the same `UserMetricDayService` payload shape.

## Match timezone authority

Day boundaries for projection and settlement use `rly_matches.timezone`. The connector may send `source_timezone` for audit metadata, but Rally assigns observations to competition dates according to match rules when projecting.

## What not to do

- Do not write `rly_match_day_results` from controllers bypassing `ResultIngestionService`
- Do not skip `rly_user_metric_days` in production connectors (orphans baselines and profile history)
- Do not merge observations across data sources
- Do not add per-provider tables in V1
- Do not normalize watch vs phone step counts until product rules exist

## Migration and local dev

```sh
php scripts/migrate.php          # creates rly_user_metric_days, rly_match_baselines (non-destructive)
php scripts/seed.php             # populates canonical history + matches
php scripts/seed.php --fresh     # optional full reset
```
