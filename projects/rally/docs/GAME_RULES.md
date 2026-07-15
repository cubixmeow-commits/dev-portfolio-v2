# Rally game rules

## One-Court Principle

Every match contests **exactly one metric** with one competition type and one scoring strategy. Do not combine metrics into a single match score.

## Competition Suitability Rule

A metric may become a Rally category when it can be measured consistently enough for the intended match format, compared honestly, and presented without implying medical diagnosis or guaranteed health superiority.

- **Performance metrics** measure intentional activity (steps, active minutes).
- **Health comparison metrics** compare physiological outcomes (resting heart rate, HRV, sleep duration).
- Winning a health-stat match does **not** mean someone is healthier overall.

Rally may allow Classic comparison of physiological health statistics, but it does not encourage players to manipulate those values or claim that winning means better overall health.

## Pushability Rule

Baseline matches are only available for metrics a player can safely and intentionally influence during the competition period.

## Competition types

Rally uses two competition types (stored as `competition_type` on the match):

| Code | Name | How each day is decided |
| --- | --- | --- |
| `classic` | **Classic** | Compare recorded values directly (or series averages for health metrics). |
| `baseline` | **Baseline** | Compare favorable percentage change from each player's frozen personal baseline. |

Legacy terms `direct`, `baseline_improvement`, and `competition_mode` are retired.

### Presentation surfaces

| Surface | Type | Strategy | Metrics |
| --- | --- | --- | --- |
| **Classic Daily Game Series** | Classic | `daily_wins` | Steps, Active Minutes |
| **Baseline Daily Game Series** | Baseline | `daily_wins` | Steps, Active Minutes |
| **Health Comparison Series** | Classic | `series_average` | Resting HR, HRV, Sleep Duration |

## Metric matrix (V1)

| Metric | `scoring_strategy` | Classic | Baseline |
| --- | --- | --- | --- |
| Daily Steps | `daily_wins` | ✓ | ✓ |
| Active Minutes | `daily_wins` | ✓ | ✓ |
| Resting Heart Rate | `series_average` | ✓ | ✗ |
| Heart Rate Variability | `series_average` | ✓ | ✗ |
| Sleep Duration | `series_average` | ✓ | ✗ |

**Hard rules:**

- Baseline **always** requires `daily_wins`.
- **Reject** `baseline` + `series_average`.
- **Reject** Baseline for health comparison metrics — those support **Classic only**.

## Scoring strategies

### Daily wins (`daily_wins`)

Used for Daily Steps and Active Minutes (Classic and Baseline).

Each day is one game. The player who wins more official daily games wins the match. The timeline rail awards series points for daily victories.

- **Classic:** higher (or lower, per metric) recorded value wins when the margin is decisive.
- **Baseline:** larger favorable **percentage change from frozen baseline** wins when the margin is decisive. Raw values remain visible.

Example (Classic): `Iain 8–6 Mike`

### Series average (`series_average`)

Used for Resting Heart Rate, Heart Rate Variability, and Sleep Duration — **Classic only**.

Each date still produces a comparable daily observation, but the match winner is the official average across the complete series. Daily rail markers show comparison ownership, not match points.

Example: Iain 62 ms average vs Mike 57 ms average → Iain wins.

## Baseline rules

### History window

Personal baselines are calculated from **`rly_user_metric_days`** for the player's **declared data source only** — never merged across sources.

- **Preferred:** up to **30** complete eligible days ending the day before match `start_date`.
- **Minimum:** **7** days in that window.
- If either player lacks enough history, the Baseline match cannot be accepted.

### Freeze at acceptance

When both players have declared sources and the invitation is accepted (transactionally):

1. Rally computes each player's baseline statistics (mean, median, standard deviation, min, max, sample count, window dates).
2. Results are stored in **`rly_match_baselines`** and **do not change** for the life of the match.
3. Preview numbers shown during create/accept are estimates; frozen snapshots are authoritative.

### Transparency and acknowledgement

Before a Baseline match starts, both players must review baseline summaries (mean, sample size, typical range) and check:

> I reviewed both baseline summaries and understand how this match will be scored.

The acknowledgement **resets** if the creator or acceptor changes metric, data source, or match start date.

### Percentage scoring (V1 limitation)

Baseline daily games use **percentage change from the frozen mean**, not z-score normalization.

- Higher-wins metrics: `(current − baseline_mean) / baseline_mean × 100`
- Daily tie threshold: `baseline_tie_threshold` in **percentage points** (default 1.00). Difference **below** the threshold is a tie; **equal to** the threshold is decisive.
- Standard deviation is calculated and stored on the frozen snapshot for **future** scoring models but is **not** used in V1.

### Baseline Manipulation Risk

Baseline competition compares each player's performance to their own recent history. That design is fair only when baselines reflect normal behavior.

**Sandbagging** — deliberately under-performing before a match to lower a frozen baseline — undermines the format. Rally discloses baseline windows and sample counts so opponents can judge whether a baseline looks unusually low or incomplete.

Players should treat suspicious baselines as a fairness concern, not a secret advantage. Rally does not automatically penalize low baselines in V1; transparency and informed acceptance are the primary safeguards. Do not enter a Baseline match if you believe the opponent's history window is not representative.

## Comparison (Classic)

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

`rly_matches.timezone` is the sole scoring boundary. `rly_users.timezone` may suggest defaults and format non-match dates only. Canonical observations and match-day projection both respect the match timezone calendar.

## Missing data

Never treat missing values as zero. Show “Awaiting sync.” Until settlement; then void if incomplete.

## Source comparability

Declared sources with different classes (e.g. watch vs phone) produce a warning. Play continues; do not normalize hardware differences in V1.

Canonical history and baselines are always **per declared source**. Rally never merges Apple Watch steps with phone steps for baseline calculation.

## Product language

Rally presents health and activity data for friendly comparison and entertainment. It does not provide medical advice or determine overall health. Prefer neutral copy: recorded value, current average, series leader, daily comparison, official result, percentage vs baseline.
