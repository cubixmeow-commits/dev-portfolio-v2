# Cadence Runbook

Operating instructions for a standard LAMP shared host (the target is cubixmeow.com) and for local work. Everything here has been executed, not just written down.

## 1. Deploy to shared hosting

### 1.1 Layout

Upload the project so that only `public/` is inside the web root:

```
~/apps/cadence/           app/, config/, database/, engine/, storage/, scripts/
~/public_html/cadence/    contents of public/ (index.php, assets/, .htaccess)
```

Then edit `~/public_html/cadence/index.php` and point the two `require` lines at `~/apps/cadence/app/`. If the host forces everything under `public_html`, keep the provided `.htaccess` and add one in the project root with `Deny from all` for `config/` and `storage/`; the code works either way, but credentials outside the web root is the preferred posture.

### 1.2 Apache

`public/.htaccess` already routes non-file requests to `index.php` and needs `mod_rewrite` (enabled on effectively every shared host). If the app lives in a subdirectory, set `app.base_path` in `config/config.php`, for example `/cadence`.

### 1.3 PHP settings

PHP 8.1 or newer with `pdo_mysql`. No extensions beyond stock. Set `app.env` to `production` in `config.php` so errors render as the styled 500 page and details go to the error log only.

## 2. Apply the schema

```sh
mysql -u USER -p DBNAME < database/schema.sql
```

The file is idempotent: it drops and recreates all Cadence tables and reseeds the badge catalog. Running it against a live database therefore erases data; that is what it is for (fresh installs and full rebuilds). Post-launch changes go in `database/migrations/` as numbered files applied in order.

## 3. Build the jar

Any machine with a JDK 17+ (the host does not need one; build locally and upload the jar):

```sh
engine/build.sh          # produces engine/build/cadence-engine.jar
```

The script uses only `javac` and `jar`. MySQL Connector/J is vendored in `engine/lib/` and unpacked into the jar, so the output is a single self-contained artifact.

## 4. Seed and reset

```sh
# Standard demo world: 500 users, 12 challenges, 120 days, curated demo member
java -jar engine/build/cadence-engine.jar reset --confirm

# Add more data on top of what exists (rarely what you want; reset is the norm)
java -jar engine/build/cadence-engine.jar seed --users=200 --challenges=6 --history-days=60

# Reports
java -jar engine/build/cadence-engine.jar report --type=engagement
java -jar engine/build/cadence-engine.jar report --type=retention
java -jar engine/build/cadence-engine.jar report --type=challenge-health
```

The engine reads `config/engine.properties` (or `--db-config=/path`). Every run is recorded in `ops_runs` and shows up in the web admin's run history.

Facts worth knowing:

- Reset deletes rows where `is_demo = 1` plus all challenges (all challenge content is seed content; the app has no user-created challenges). Real accounts keep their identity but lose challenge progress, and their point totals are zeroed to stay consistent.
- The RNG seed is fixed (42), so reset produces the same counts every run. Pass `--seed=N` for a different but equally reproducible world.
- The demo member is `demo@cadence.demo` / `cadence-demo` (handle `sam`). The one-click ribbon button signs into this account.
- The admin is `admin@cadence.demo` / `cadence-admin`, created only if missing, never touched by reset. Change the password in Settings after first login.

## 5. Web ops tools

`/admin/tools` (admin role required) runs the same jar via `shell_exec`, streams stdout into the page, and offers reports for download. If the host disables `shell_exec`, the page detects it, disables the Run buttons, and shows the exact SSH command next to each tool; run history keeps working because the CLI records to the same table.

If a web-triggered run ever fails to launch (button click, then an immediate "failed" row), check that `engine.jar_path` and `engine.java_bin` in `config.php` are absolute and correct for the host.

## 6. Mail spool

Shared-hosting sendmail is unreliable, so the default `Mailer` driver writes each message as an `.eml` file to `storage/mail/`. Verification and reset links are in those files; any mail client opens them.

To swap in real delivery: set `mail.driver` to `mail` in `config.php` to use PHP's native `mail()`, or implement an SMTP transport in `app/Core/Mailer.php` (one class, one method to change). Keep the spool in development; it doubles as an audit trail of everything the app would have sent.

## 7. Sessions and housekeeping

Sessions live in the `sessions` table (30-day idle expiry, purged by PHP's session GC as requests come in). `rate_limits` is self-cleaning per window. `storage/logs/` holds per-run engine output; delete old files freely, the summary survives in `ops_runs`.

## 8. Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| Blank page or "Missing config/config.php" | Copy the .example files and fill them in (section 1) |
| 500 on every page, log says SQLSTATE 1049 | `db.name` in config.php does not match the created database |
| Styles load but every route 404s | `mod_rewrite` off or `.htaccess` not uploaded; also check `app.base_path` |
| Login always says session expired (419) | Cookies blocked or `session.secure` is true while serving plain HTTP |
| Rate limited during testing | `DELETE FROM rate_limits;` resets all windows |
| Web run stuck "running" with an empty log | Wrong `engine.jar_path` or the PHP user cannot execute `java`; run the shown SSH command to confirm |
| Engine says "badges table is empty" | Apply `database/schema.sql` first; the badge catalog is part of it |
| Demo button says the account is missing | Run `reset --confirm`; it rebuilds the curated member |
| Streak math questioned in review | `php scripts/streak-tests.php` runs the written case table against the live code |
