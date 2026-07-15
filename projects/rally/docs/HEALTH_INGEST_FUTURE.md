# Future health data ingestion

V1 proves the game with seeded/simulated data. Real wearable connectors are a later phase.

## Normalized ingest boundary

Call `Rally\Services\ResultIngestionService::ingest()` with:

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

### Behavior

- Validates membership, metric, source, and value range
- Inserts or updates the single row for `(match_day_id, user_id)`
- Same `source_record_key` updates in place (idempotent)
- Rejects ordinary updates once the day is `official` or `void`
- Assigns values to the match-day using the **match timezone** calendar, not the user’s home timezone

## HealthKit (future)

An iOS companion would:

1. Read step samples for the match timezone’s calendar day
2. Map the device/source to a `rly_data_sources` slug
3. POST a normalized payload to a future authenticated API that calls `ingest()`
4. Respect settlement locks returned by the server

## Health Connect (future)

An Android companion follows the same boundary with Health Connect step records and the same payload shape.

## What not to do

- Do not write `rly_match_day_results` from controllers bypassing ingestion
- Do not add per-provider tables in V1
- Do not normalize watch vs phone step counts until product rules exist
