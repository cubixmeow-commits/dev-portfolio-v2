# Deploy checklist

Run top to bottom for every deploy to shared hosting. Details for each step live in docs/RUNBOOK.md.

## Before upload

- [ ] `php -l` clean across `app/` and `public/` (`find . -name '*.php' -exec php -l {} \;`)
- [ ] `php scripts/streak-tests.php` passes locally
- [ ] `engine/build.sh` produces a fresh `cadence-engine.jar`
- [ ] `grep -rPn "\x{2014}" .` returns nothing (no em dashes; repository formatting rule)
- [ ] No credentials staged: only `.example` config files in git

## Upload

- [ ] `app/`, `config/`, `database/`, `engine/build/cadence-engine.jar`, `storage/`, `scripts/` outside the web root
- [ ] Contents of `public/` in the web directory, including `.htaccess`
- [ ] `storage/` writable by the PHP user (`mail/`, `logs/`)

## Configure

- [ ] `config/config.php` created from the example: DB credentials, `base_url`, `base_path`, `env = production`, `session.secure = true`
- [ ] `config/engine.properties` created from the example
- [ ] `engine.jar_path` and `engine.properties_path` in config.php are absolute paths on the host

## Initialize

- [ ] Schema applied: `mysql ... < database/schema.sql`
- [ ] Demo world loaded: `java -jar cadence-engine.jar reset --confirm`
- [ ] Admin password changed via Settings after first login

## Verify (five minutes, in a private window)

- [ ] Homepage shows non-zero live numbers and the feed preview
- [ ] "Sign in as demo member" lands on a populated dashboard
- [ ] A check-in succeeds and the nav ring updates
- [ ] Register a throwaway account; the verification .eml appears in `storage/mail/`
- [ ] `/admin` renders charts; a report run from `/admin/tools` succeeds (or shows the SSH command if shell_exec is off)
- [ ] 404 page is styled (visit any bad URL)
