# Rally game rules

## One-Court Principle

Every match contests **exactly one metric** with one scoring strategy. Do not combine metrics into a single match score.

## Competition Suitability Rule

A metric may become a Rally category when it can be measured consistently enough for the intended match format, compared honestly, and presented without implying medical diagnosis or guaranteed health superiority.

- **Performance metrics** measure intentional activity (steps, active minutes).
- **Health comparison metrics** compare physiological outcomes (resting heart rate, HRV, sleep duration).
- Winning a health-stat match does **not** mean someone is healthier overall.

## Scoring strategies

### Daily wins (`daily_wins`)

Used for Daily Steps and Active Minutes.

Each day is one game. The player who wins more official daily games wins the match. The timeline rail awards series points for daily victories.

Example: `Iain 8–6 Mike`

### Series average (`series_average`)

Used for Resting Heart Rate, Heart Rate Variability, and Sleep Duration.

Each date still produces a comparable daily observation, but the match winner is the official average across the complete series. Daily rail markers show comparison ownership, not match points.

Example: Iain 62 ms average vs Mike 57 ms average → Iain wins.

## Comparison

Centralized in `MetricComparisonService` + `MetricCompetitionService` / `MatchScoringService`:

- If `higher_wins` is true, the higher value wins when the margin is decisive.
- If `higher_wins` is false, the lower value wins when the margin is decisive.
- `absolute_difference < tie_threshold` → **tie** / within-threshold (no daily win; series-average draw when final).
- `absolute_difference >= tie_threshold` → decisive (equal to threshold is decisive).

Pending days never affect confirmed scores or official averages. Missing values are never treated as zero. Void days are excluded from averages and award no points.

## Ties vs voids

| Result | Meaning | Series effect |
| --- | --- | --- |
| Tie / within threshold | Both values present; difference below threshold | No daily win; may produce a final average draw |
| Void | One or both values missing at settlement, or admin void | No win; excluded from averages |

## Day lifecycle

`scheduled` → `live` → `pending` → `official` | `void`

- **Live**: competition date is today in the match timezone.
- **Pending**: day has ended; settlement window still open.
- **Official**: settlement deadline passed with both values locked.
- **Void**: settlement with incomplete data.

## Settlement schedule

Day **N** becomes official at **06:00 on day N+2** in `rly_matches.timezone`.

## Match lifecycle

`invited` → `scheduled` → `active` → `settling` → `completed`

- **Settling**: final competition date has passed; at least one day is not yet official/void.
- **Completed**: every required day is official or void. Final winner/draw labels appear only then. Series-average matches remain provisional until all days settle.

## Timezone authority

`rly_matches.timezone` is the sole scoring boundary. `rly_users.timezone` may suggest defaults and format non-match dates only.

## Missing data

Never treat missing values as zero. Show “Awaiting sync.” Until settlement; then void if incomplete.

## Source comparability

Declared sources with different classes (e.g. watch vs phone) produce a warning. Play continues; do not normalize hardware differences in V1.

## Product language

Rally presents health and activity data for friendly comparison and entertainment. It does not provide medical advice or determine overall health. Prefer neutral copy: recorded value, current average, series leader, daily comparison, official result.
