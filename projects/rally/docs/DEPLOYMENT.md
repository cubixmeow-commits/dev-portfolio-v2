# Rally deployment (Hostinger)

1. Upload the full `projects/rally` tree.
2. Point traffic at `public/` (same layout as SousMeow).
3. Copy `config/config.example.php` to `config/config.php`.
4. Copy `.env.example` to `.env` **or** set the same values in `config.php`.

## cubixmeow.com settings

If the live URL is under `/iain/projects/rally/` (same hosting pattern as SousMeow):

```env
APP_URL=https://cubixmeow.com
APP_BASE_PATH=/iain/projects/rally/public
```

Or in `config/config.php`:

```php
'app' => [
    'env' => 'production',
    'url' => 'https://cubixmeow.com',
    'base_url' => 'https://cubixmeow.com',
    'base_path' => '/iain/projects/rally/public',
    // …
],
'session' => [
    'cookie_name' => 'rally_session',
    'idle_ttl'    => 1209600,
    'secure'      => true,
],
```

Open the app at:

`https://cubixmeow.com/iain/projects/rally/public/`

If styles still fail or every page 404s inside Rally, `base_path` does not match `REQUEST_URI`. Compare with how SousMeow is configured (`/iain/projects/sousmeow/public`).

5. MySQL: set `db.driver` → `mysql` with Hostinger credentials (or keep SQLite if `storage/` is writable).
6. Ensure PHP 8.2+ with `pdo_mysql` / `pdo_sqlite`, `mbstring`.
7. From SSH or Hostinger terminal:

```sh
cd /path/to/rally
php scripts/seed.php
php scripts/seed.php --status
```

8. Confirm `storage/` is writable by PHP and not publicly listable.
9. Do not commit `config.php` or `.env`.

No Node.js, Composer, Docker, or background workers are required.

Simulation is admin-only when `app.env` is `production`.
