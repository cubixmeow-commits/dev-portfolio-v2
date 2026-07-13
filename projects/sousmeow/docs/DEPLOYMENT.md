# Deploying SousMeow to Hostinger

SousMeow is plain PHP 8 plus MySQL (or SQLite), which is exactly what
Hostinger shared hosting provides. No Node, no Composer, no workers.

## 1. Upload

Upload the `projects/sousmeow` folder (for example to
`~/domains/yourdomain.tld/sousmeow`). Keep the folder layout intact:
only `public/` may be web-served; `app/`, `config/`, `database/`,
`scripts/`, and `storage/` must sit outside the document root.

Point the domain or subdomain document root at `sousmeow/public`. The
included `public/.htaccess` routes clean URLs through `index.php` on
Apache with mod_rewrite (enabled by default on Hostinger).

## 2. Configure

```sh
cp config/config.example.php config/config.php
```

Edit `config/config.php`:

- `app.env` to `'production'` (hides error detail from visitors),
- `app.base_url` to your https URL,
- `app.base_path` if the app is not at the domain root (for example
  `/iain/projects/sousmeow/public` when the document root is
  `public_html` rather than `sousmeow/public`),
- `session.secure` to `true`,
- either keep `db.driver = 'sqlite'` (simplest; the database file lives
  in `storage/`, outside the web root), or set `db.driver = 'mysql'`
  and fill in the database created in hPanel (MySQL Databases).

`config.php` is gitignored and never leaves the server.

## 3. Seed

Run from the `sousmeow` directory (not the repo root):

```sh
cd ~/domains/yourdomain.tld/sousmeow   # or wherever you uploaded it
php scripts/seed.php --admin-email you@yourdomain.tld
```

This applies the schema, **syncs** cookbook content (upsert by slug; safe to
re-run after deploys), and prints the admin's temporary password exactly once.
Change it right after signing in (My Kitchen, Change password). The script is
CLI-only; requesting it over HTTP returns 404 because it sits outside `public/`.

After every deploy, confirm the catalog updated:

```sh
php scripts/seed.php --status
```

You should see `[OK] plan-youtube-video: db=executable ... prompts=10`. If it
shows `[!!]` or `MISSING`, run `php scripts/seed.php` again and read the health
output. Use `--fresh` only when you intend to wipe all projects and exports.

To rotate a forgotten password later:

```sh
php scripts/seed.php --reset-password user@example.com
```

## 3b. Import SQLite data into MySQL

If you developed locally on SQLite and switched `config.php` to MySQL,
`seed.php` only applies schema and catalog content — it does **not** copy
users, projects, artifacts, or exports from your old database.

Upload your SQLite file to the server (for example
`storage/sousmeow.sqlite`), then:

```sh
# Preview row counts without writing
php scripts/import-sqlite-to-mysql.php --dry-run --sqlite-path storage/sousmeow.sqlite

# Recommended: catalog already seeded on MySQL, import user data only
php scripts/import-sqlite-to-mysql.php --sqlite-path storage/sousmeow.sqlite --replace-users

# Or: SQLite is the full source of truth (replaces all MySQL rows)
php scripts/import-sqlite-to-mysql.php --full --sqlite-path storage/sousmeow.sqlite
```

`--replace-users` removes existing MySQL users before import so IDs match
SQLite (needed when the seed admin occupies id `1` but SQLite has
simulation users starting at `1`). Re-create the admin after import if
needed: `php scripts/seed.php --admin-email you@yourdomain.tld`.

Copy export zip files separately: they live in `storage/exports/` on disk,
not inside the database.

### Email link 404s

Verification and password-reset links use `APP_URL` and `APP_BASE_PATH`
from `.env` (or `config.php`). If links in email show "Page not found" but
the site works in the browser, the base path is wrong.

```sh
php scripts/print-url-config.php
```

Set `.env` to match how you open My Kitchen in the browser:

```env
APP_URL=https://cubixmeow.com
APP_BASE_PATH=/iain/projects/sousmeow/public
```

Then resend verification from the account page.

## 4. Verify

- `https://yourdomain.tld/` renders the marketing page with fonts and
  the cat mark (assets load from `public/assets`).
- Register a throwaway account and run the Launch Day Kit with "Paste
  example response" through to a zip download.
- `config/config.php` and `storage/` return 404 via the browser.

## Housekeeping

- Exports accumulate in `storage/exports/`; each zip is a few KB.
  Delete old ones freely; the export list will offer a fresh build if a
  file is missing.
- Back up either `storage/sousmeow.sqlite` (SQLite) or the MySQL
  database (hPanel backups cover this).
- PHP 8.1 or newer with the `pdo_sqlite`/`pdo_mysql`, `mbstring`, and
  `zip` extensions (all present on Hostinger's default PHP 8 setup).
