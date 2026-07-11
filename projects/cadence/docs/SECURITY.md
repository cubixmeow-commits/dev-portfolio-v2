# Cadence Security

Threat model and the mitigations actually implemented, numbered to match the build specification's checklist. Cadence is a public demo, so the realistic adversaries are drive-by scanners, credential stuffers, and curious reviewers poking at the forms. The design goal is that none of them find anything, and that a reader can verify each claim in the referenced file.

## 1. SQL injection

All database access goes through PDO prepared statements with emulation off (`app/Core/Database.php`). Models never interpolate user input into SQL; the only interpolated values are integer LIMIT/OFFSET computed from clamped integers and column-safe constants. The QA gate greps the codebase for string-built SQL and `->query(` calls.

## 2. Cross-site scripting

Output is escaped by default: templates print through `e()` (`htmlspecialchars` with ENT_QUOTES, UTF-8). The few places that emit built HTML (feed sentences) escape every dynamic fragment inside the builder (`ActivityEvent::sentence`). The CSP (item 11) is the second layer: scripts are same-origin files only, no inline script executes.

## 3. Cross-site request forgery

One rotating per-session token, injected by `Csrf::field()` and verified in the front controller for every POST before routing (`public/index.php`). No endpoint can forget the check because no endpoint performs it; the gate is upstream. Fetch calls send the same token via `X-CSRF-Token`.

## 4. Sessions

DB-backed via a custom handler (`app/Core/Session.php`): 64-character ids, strict mode, cookies `httponly` + `SameSite=Lax` (+ `secure` in production config), 30-day idle expiry enforced on read and purged by GC. The id regenerates on login (`Auth::login`), and password reset deletes every session row for the account, which is "log out everywhere" for free.

## 5. Rate limiting

Fixed-window limiter on the `rate_limits` table with hashed keys and an atomic upsert (`app/Core/RateLimiter.php`). Applied to login (per IP and per account), registration, password reset request and submit, verification resend, demo login, and check-ins. Breaches answer with a friendly flash, not a stack trace.

## 6. Passwords and tokens

`password_hash()` bcrypt cost 12 (`security.bcrypt_cost`), minimum 10 characters, checked against a bundled common-password list. Verification and reset tokens are 32 random bytes; only their SHA-256 lands in the database, they expire (24h / 30min), and consumption is single-use. Reset issuance invalidates prior outstanding tokens.

## 7. User enumeration

Login answers "Email or password is incorrect." for unknown email and wrong password alike, and burns a bcrypt verify against a constant hash when the account does not exist so the timing signature matches. The forgot-password flow answers identically whether or not the email exists. Registration necessarily confirms an email is taken; that endpoint is rate limited (item 5) to blunt harvesting.

## 8. Admin gating

One middleware-style check, `Auth::requireAdmin()`, used by every admin action; there are no per-controller ad hoc role checks. It responds 404, not 403, so probing cannot confirm the admin surface exists. The admin link renders only for admin sessions, but rendering is cosmetic; the gate is server-side.

## 9. Shelling out to the engine

The single `shell_exec` call site (`AdminToolsController`) builds its command from: a jar path fixed in config (never from input), a tool name checked against an allowlist, numeric parameters cast to int and range-clamped, and `escapeshellarg` around every argument including the fixed ones. Report types come from a second allowlist. When `shell_exec` is disabled the page degrades to displaying the command, executing nothing.

## 10. Credentials

`config/config.php` and `config/engine.properties` are gitignored; only `.example` templates with placeholders are committed, and the QA gate greps for committed secrets. The config directory lives outside the web root in the recommended deployment (RUNBOOK section 1).

## 11. Security headers

Set once in the front controller: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: strict-origin-when-cross-origin`, and a CSP of `default-src 'self'` with `img-src 'self' data:` (badge icons are data URIs), `form-action 'self'`, `frame-ancestors 'none'`, and `script-src 'self'`. `style-src` allows inline style attributes because avatar colors and ring geometry are computed server-side per user; script execution, the part of CSP that blunts XSS, remains strict.

## 12. File uploads

None exist. Avatars are generated from a stored seed, which removes the upload attack class entirely (parser exploits, polyglot files, storage abuse) and is a deliberate design decision documented in ARCHITECTURE.md.

## Known accepted risks

- The demo member and admin ship with published passwords (`cadence-demo`, `cadence-admin`). This is the point of a demo; the RUNBOOK instructs changing the admin password at first login, the admin account survives resets, and the demo account holds no privileges.
- `style-src 'unsafe-inline'` (see item 11): accepted to keep the no-build-step, server-computed styling; script-src stays strict.
- Session fixation via adopted ids is prevented by strict mode plus regeneration on login; concurrent-session limits are out of scope for a demo.
