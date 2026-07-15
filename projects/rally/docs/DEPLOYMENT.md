# Rally deployment (Hostinger)

1. Upload the full `projects/rally` tree.
2. Point the domain / subdomain document root at `public/`.
3. Copy `config/config.example.php` to `config/config.php`.
4. Set:
   - `app.env` → `production`
   - `app.url` → HTTPS origin
   - `session.secure` → `true`
   - `db.driver` → `mysql` with Hostinger credentials
5. Ensure PHP 8.2+ with `pdo_mysql`, `mbstring`.
6. From SSH or Hostinger terminal:

```sh
cd /path/to/rally
php scripts/migrate.php          # required when upgrading an existing DB
php scripts/seed.php             # fresh install / demo reseed
php scripts/migrate.php --status
php scripts/seed.php --status
```

If you previously seeded Rally (steps-only) and then deployed the multi-metric build,
`php scripts/migrate.php` is required. `CREATE TABLE IF NOT EXISTS` will not add the
new `rly_metric_types` columns on its own — that is what caused the profile
`no such column: mt.scoring_strategy` error.

7. Confirm `storage/` is writable by PHP and not publicly listable.
8. Do not commit `config.php` or `.env`.

No Node.js, Composer, Docker, or background workers are required.

Simulation is admin-only when `app.env` is `production`.
