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
- `session.secure` to `true`,
- either keep `db.driver = 'sqlite'` (simplest; the database file lives
  in `storage/`, outside the web root), or set `db.driver = 'mysql'`
  and fill in the database created in hPanel (MySQL Databases).

`config.php` is gitignored and never leaves the server.

## 3. Seed

Run once over SSH from the `sousmeow` directory:

```sh
php scripts/seed.php --admin-email you@yourdomain.tld
```

This creates the schema for the configured driver, seeds the Cookbooks,
and prints the admin's temporary password exactly once. Change it right
after signing in (My Kitchen, Change password). The script is CLI-only;
requesting it over HTTP returns 404 because it sits outside `public/`.

To rotate a forgotten password later:

```sh
php scripts/seed.php --reset-password user@example.com
```

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
