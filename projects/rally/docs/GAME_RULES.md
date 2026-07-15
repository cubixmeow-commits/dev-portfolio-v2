# Rally game rules

## Series scoring

A match is a sequence of daily games. The series score counts **daily wins only**. Total steps across the series never decide the winner.

Example: `Iain 5–3 Mike` means five official daily wins for Iain and three for Mike.

## Comparison

Centralized in `MetricComparisonService`:

- If `higher_wins` is true, the higher value wins when the margin is decisive.
- If `higher_wins` is false, the lower value wins when the margin is decisive.
- `absolute_difference < tie_threshold` → **tie** (no win awarded).
- `absolute_difference >= tie_threshold` → decisive result.

Default `tie_threshold` is `100`.

## Ties vs voids

| Result | Meaning | Series points |
| --- | --- | --- |
| Tie | Both values present; difference below threshold | None |
| Void | One or both values missing at settlement, or admin void | None |

Derived summaries keep `ties` and `voids` as separate counters.

## Day lifecycle

`scheduled` → `live` → `pending` → `official` | `void`

- **Live**: competition date is today in the match timezone.
- **Pending**: day has ended; settlement window still open.
- **Official**: settlement deadline passed with both values locked.
- **Void**: settlement with incomplete data.

## Settlement schedule

Day **N** becomes official at **06:00 on day N+2** in `rly_matches.timezone`.

Example (America/Los_Angeles): competition date July 20 settles at 06:00 PDT on July 22.

## Match lifecycle

`invited` → `scheduled` → `active` → `settling` → `completed`

- **Settling**: final competition date has passed; at least one day is not yet official/void.
- **Completed**: every required day is official or void. Final winner/draw labels appear only then.

## Timezone authority

`rly_matches.timezone` is the sole scoring boundary. `rly_users.timezone` may suggest defaults and format non-match dates only.

## Missing data

Never treat missing values as zero. Show “Awaiting sync.” Until settlement; then void if incomplete.

## Source comparability

Declared sources with different classes (e.g. watch vs phone) produce a warning. Play continues; do not normalize hardware differences in V1.
