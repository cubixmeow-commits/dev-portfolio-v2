# Cadence Architecture

How the system works, where the single sources of truth live, and what was deliberately not built.

## 1. Shape of the system

Two programs share one MySQL schema:

- **The PHP application** (`app/`, `public/`): serves every page and API response. MVC-lite: a front controller, a route table, controllers that pull data through models, PHP templates with mandatory escaping.
- **The Java ops engine** (`engine/`): seeds demo data, resets the demo world, prints operational reports. One engine, two thin interfaces (CLI and the web admin page, which shells out to the same jar).

The schema is the contract. Both sides implement the same domain rules against it, and the streak rules have an executable specification (`scripts/streak-tests.php`) that pins the behavior.

## 2. Request flow

```
Apache (or php -S) rewrites non-file requests to public/index.php
  -> security headers
  -> app/bootstrap.php: autoloader, config, timezone, DB-backed session
  -> CSRF verify for every POST (one gate, no per-endpoint opt-in)
  -> Router matches method + path, calls Controller action
  -> Controller pulls data through Models (PDO prepared statements only)
  -> View::render wraps the template in the layout; e() escapes all output
```

There is exactly one route table (`app/routes.php`), one CSRF check, one place headers are set, and one admin gate (`Auth::requireAdmin`, which answers 404 rather than 403 so admin existence is not advertised).

## 3. Single sources of truth

| Rule | Lives in | Mirrored by |
|---|---|---|
| Streaks and points | `app/Models/CheckIn.php` | `engine/.../SeedEngine.java` |
| One check-in per day | `uq_checkin_per_day` unique key in the schema | both programs rely on it |
| Feed sentence copy | `ActivityEvent::sentence()` | nothing; every surface calls it |
| Badge catalog | `badges` table rows (codes) | seeded by `schema.sql` |
| Visual system | `assets/css/tokens.css` | every stylesheet consumes tokens |

**The streak rule, stated once:** "today" is the calendar date in the user's timezone. A check-in increments the streak when it lands on `last_checkin_date + 1 day`, resets to 1 after any larger gap, and is rejected as a duplicate by the unique key when it lands on the same day. Milestones at exactly 7, 30, and 100 pay +5 points, write a `streak_milestone` event, and award the matching badge. `current_streak` keeps the value as of the last check-in (stored semantics; the next check-in corrects it after a gap).

## 4. Data model notes

- **Denormalized counters** (`users.total_points`, `challenges.participant_count`, `challenge_participants.points`) exist for read speed on lists and are maintained inside the same transactions that change their inputs. Windowed leaderboards are computed from `check_ins` by date instead, so week and month boards are always correct without stored aggregates.
- **Events are append-only.** `activity_events` is the feed; it is cursor-paginated by id (monotonic), which stays stable while new events arrive, unlike OFFSET pagination.
- **Tokens are hashes.** Email verification and password reset store SHA-256 of the token; the raw value exists only in the emailed link.
- **Soft deletion** anonymizes identity fields in place (`deleted_{id}` handle, invalid email domain, scrambled password) so aggregate history stays consistent without orphan rows.

## 5. Alternatives considered

**Why no PHP framework.** The portfolio point is fundamentals: routing, PDO, sessions, CSRF, and escaping implemented by hand and readable in an afternoon. A framework would do those things better and invisibly, which is exactly the wrong tradeoff for a showcase. The cost (no ecosystem, more code owned) is acceptable at this scope; the structure still separates concerns so a future port to a framework would be mechanical.

**Why a shell_exec bridge instead of a Java web service.** The engine could run as a small HTTP service the PHP app calls. On shared hosting there is no supervisor, no open ports, and no place for a long-lived JVM, so a service would be dead on arrival at the deployment target. Invoking the jar per run costs JVM startup (irrelevant for tools that run seconds) and removes a network surface, an auth story between the two halves, and a failure mode. The bridge is hardened instead: allowlisted tool names, integers cast before the command line, `escapeshellarg` on every argument, jar path fixed in config.

**Why DB-backed sessions instead of PHP file sessions.** Files work until you have two web heads, want to revoke a session server-side, or want "log out everywhere" on password reset. A `sessions` table gives all three, is inspectable during review, and costs one indexed lookup per request. The `data` column was added to the spec's table so PHP's session handler can be fully database-backed rather than split between file payloads and DB metadata.

**Why generated avatars instead of uploads.** Uploads drag in an entire attack class (content-type spoofing, image parser exploits, storage quotas, moderation). Deterministic initials-plus-color avatars from a stored seed cost nothing, look coherent at every size, and made room for the streak ring, which carries more information than a photo anyway.

**Why the mail spool.** Shared-hosting sendmail silently drops mail often enough that a demo cannot depend on it. Writing RFC 5322 `.eml` files is reliable, reviewable (the reviewer can open the exact verification email), and swappable for SMTP by changing one class.

**Why lazy time-based notifications.** "Challenge starts tomorrow" and "challenge ended" notices classically need cron, which shared hosting may not offer dependably. They are synthesized on dashboard load and deduplicated by (user, type, link). The tradeoff, that an absent user is not notified until they return, is invisible in practice because notifications are in-app only anyway.

**Why cursor pagination for the feed and OFFSET for browse.** The feed is an infinite reverse-chronological stream where page drift is visible and ids are monotonic, so a `WHERE id < ?` cursor is correct and cheap. Challenge browse is a filtered, sorted, bounded catalog where jumping to page N matters and the row count is small; OFFSET is the simpler right tool there.

**Why MySQL and not SQLite/Postgres.** The deployment target is a standard LAMP host, where MySQL is the guaranteed database. The code touches nothing MySQL-exclusive beyond `INSERT ... ON DUPLICATE KEY` (rate limiter, session upsert), documented here for a future port.

## 6. Performance posture

Demo scale is hundreds of users and tens of thousands of rows; every hot query is indexed (see the KEY definitions in `schema.sql`) and measured pages render from a handful of queries. The known scaling cliffs, in order: the global feed (would need fan-out or caching well before a million events), live leaderboard windows (materialize per-window aggregates), and analytics charts (precompute daily rollups). None are worth their complexity at portfolio scale, and each has an obvious next step.
