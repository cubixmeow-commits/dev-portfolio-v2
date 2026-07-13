# SousMeow portfolio simulation — complete playbook

This document describes **version 1** of the kitchen simulation system: 772
believable simulated chefs, a full day of activity preloaded per Pacific
calendar day, a public social-style metrics dashboard, and admin proof
metrics. Use it with **Claude Code**, **Cursor agents**, or manual CLI.

> **Honesty rule:** Everything created by this system is labeled portfolio
> simulation (`users.simulation = 1`). The public home page states this.
> Never present simulated chefs as real customers.

---

## Table of contents

1. [Goals](#goals)
2. [Architecture](#architecture)
3. [Timezone (California)](#timezone-california)
4. [User pool (772 creators)](#user-pool-772-creators)
5. [Daily activity (preload)](#daily-activity-preload)
6. [CLI reference](#cli-reference)
7. [Cron setup (production)](#cron-setup-production)
8. [Public dashboard](#public-dashboard)
9. [Admin metrics](#admin-metrics)
10. [Daily improvement loop (you + AI agent)](#daily-improvement-loop-you--ai-agent)
11. [Claude Code / agent prompts](#claude-code--agent-prompts)
12. [Schema](#schema)
13. [Troubleshooting](#troubleshooting)
14. [Future versions](#future-versions)

---

## Goals

| Goal | How v1 delivers |
|------|-----------------|
| Prove the product at scale | 772 real `users` rows, real projects/artifacts/exports |
| Believable usage | Model-layer actions + timestamps spread across 24h Pacific |
| Social SaaS feel | Public dashboard on `/` with live-ish activity feed |
| Hiring-manager proof | `/admin` with simulation stats + recent projects |
| Repeatable daily process | `simulate-day.php` once per calendar day |
| Low cost | PHP CLI only; one Cursor/Claude session/day for analysis |
| Safe on portfolio host | `simulation=1` flag; `--fresh` only deletes sim users |

---

## Architecture

```
personas.json (772 names, US-weighted)
        │
        ▼
simulate-users.php ──► users (simulation=1)
        │
        ▼
simulate-day.php ──► projects, pantry_values, artifacts,
  (one Pacific day)     artifact_versions, artifact_checks, exports
        │
        ├──► simulation_runs (audit: date, active count, actions)
        │
        ▼
SiteStats ──► public home dashboard + admin bundle
```

**Execution model (v1):** **Preload** — one PHP run writes the entire day's
activity with timestamps distributed across `00:00–23:59` Pacific. No
per-minute cron required.

**Data path:** All actions use the same models as the web app
(`Project::create`, `Artifact::approve`, etc.) via `SimulationKitchen`.

---

## Timezone (California)

| Setting | Value |
|---------|--------|
| IANA zone | `America/Los_Angeles` |
| Dashboard “today” | Midnight-to-midnight **Pacific** |
| Database storage | UTC `YYYY-MM-DD HH:MM:SS` |
| Conversion | `Simulation::pacificDayUtcRange()` |

Rush-hour weighting: ~70% of events fall between **8am–10pm PT**; the rest
random across the full day.

---

## User pool (772 creators)

### Personas file

`database/simulation/personas.json` — 772 entries:

```json
{
  "id": 1,
  "name": "Marcus Williams",
  "country": "United States",
  "code": "US"
}
```

- **~65% United States** (~502 names)
- **~35% international** (~270 names across 22 countries)

Regenerate from scratch:

```sh
php scripts/generate-personas.php --fresh
```

Append missing personas up to `Simulation::POOL_SIZE` (keeps existing ids 1–500):

```sh
php scripts/generate-personas.php
```

### Login credentials

| Field | Value |
|-------|--------|
| Email pattern | `kitchen+1@demo.local` … `kitchen+772@demo.local` |
| Password (all) | `demo-kitchen-2026` |
| DB flag | `users.simulation = 1` |

Example login: `kitchen+42@demo.local` / `demo-kitchen-2026`

### Create the pool

```sh
php scripts/simulate-users.php
```

Reset simulation users only:

```sh
php scripts/simulate-users.php --fresh
```

---

## Daily activity (preload)

Each run of `simulate-day.php`:

1. Picks **~12–18%** of the pool as “active” that day (~60–90 chefs).
2. For each active chef (random weighted):
   - **35%** — start a new project (maybe stock pantry)
   - **40%** — advance an open project (approve more recipes)
   - **25%** — complete a project + export row
3. Assigns each action a **random UTC timestamp** within the target Pacific date.
4. Records the run in `simulation_runs`.

Cookbook mix among executable books:

- Launch Day Kit — 40%
- Plan a YouTube Video — 30%
- Validate a SaaS Idea — 30%

**Idempotent:** Won't re-run the same Pacific date unless `--force`.

### Backfill multiple days (recommended for demos)

```sh
php scripts/simulate-day.php --date=2026-07-08
php scripts/simulate-day.php --date=2026-07-09
php scripts/simulate-day.php --date=2026-07-10
php scripts/simulate-day.php --date=yesterday
```

---

## CLI reference

### Prerequisites (once per environment)

```sh
cd projects/sousmeow
php scripts/seed.php              # schema + cookbooks + admin
php scripts/simulate-users.php    # 772 simulation creators
```

### Daily simulation

```sh
php scripts/simulate-day.php --date=yesterday
php scripts/simulate-day.php --date=today
php scripts/simulate-day.php --date=2026-07-12 --force
php scripts/simulate-day.php --status
```

### Status checks

```sh
php scripts/simulate-users.php --status
php scripts/simulate-day.php --status
```

### Local dev server

```sh
php -S localhost:8090 -t public public/index.php
```

Visit `/` for the public dashboard, `/admin` for proof metrics.

---

## Cron setup (production)

Hostinger / typical PHP host — **one job per day** is enough for v1.

**Suggested: 12:30 AM Pacific** (adjust for server UTC offset):

```cron
# Simulate the Pacific day that just ended (server cron may be UTC — convert!)
30 7 * * * cd /path/to/sousmeow && php scripts/simulate-day.php --date=yesterday >> storage/logs/simulation.log 2>&1
```

`07:30 UTC` ≈ `00:30 PDT` (verify during PST/PDT transitions).

### First deploy checklist

```sh
php scripts/seed.php
php scripts/simulate-users.php
# Backfill last 7 days for a rich dashboard:
for d in 7 6 5 4 3 2 1; do php scripts/simulate-day.php --date="$(date -d "$d days ago" +%Y-%m-%d -d America/Los_Angeles 2>/dev/null || ...)" ; done
# Simpler: run manually with explicit dates
php scripts/simulate-day.php --date=yesterday
```

---

## Public dashboard

**URL:** `/` (marketing home, below hero lede)

**Metrics (Pacific “today” where noted):**

| Stat | Source |
|------|--------|
| Chefs in the kitchen | `COUNT(users WHERE simulation=1)` |
| Active today (PT) | Distinct users with `projects.updated_at` in today's PT window |
| Kits packed today | `exports` created today PT |
| Cookbook satisfaction | Weighted avg of `cookbooks.demo_avg_rating` |
| Kits packed all time | All simulation exports |
| Approved today | Artifacts approved today PT |

**Panels:**

- **Kitchen activity** — recent completions, cooking, pantry, joins
- **Trending today** — cookbooks by activity today PT

**Disclaimer on page:** “Portfolio demonstration · simulated chefs and activity (Pacific time)”

---

## Admin metrics

**URL:** `/admin` (seed admin account)

Shows simulation-specific counts, `sim` badge on recent projects from
simulation users, and last `simulation_runs` row.

---

## Daily improvement loop (you + AI agent)

### Phase 1 — Measure (automated, $0)

After `simulate-day.php`, capture:

```sh
php scripts/simulate-day.php --status
```

Optional: export JSON snapshot (v2) — for now query `/admin` and note:

- Active today vs pool size
- Completion rate (completed / projects)
- Cookbook distribution
- Any script errors in cron log

### Phase 2 — Analyze (1 Claude/Cursor session/day)

Review with your agent:

1. Did today's simulation look realistic?
2. Which cookbook is under/over represented?
3. Is the public dashboard compelling?
4. Any product bugs exposed by volume?

### Phase 3 — Improve (human-approved)

Allowed changes without debate:

- `simulate-day.php` weights (DAU %, cookbook %, funnel)
- Dashboard copy / metrics labels
- Docs

Requires your approval:

- PHP/UI code changes
- Schema changes

### Phase 4 — Verify next day

Re-run simulation, compare admin + home dashboard.

---

## Claude Code / agent prompts

### Initial setup agent

```
Read docs/SIMULATION.md in projects/sousmeow. Run seed.php, simulate-users.php,
and backfill 5 days of simulate-day.php with Pacific dates. Confirm the home
page dashboard shows activity and document login credentials.
```

### Daily simulation agent

```
Read docs/SIMULATION.md. Run php scripts/simulate-day.php --date=yesterday
in projects/sousmeow. Report simulation_runs stats and whether the home page
activity feed has new rows. Do not modify code unless I ask.
```

### Daily analysis agent (with me)

```
Read docs/SIMULATION.md and inspect /admin metrics logic in SiteStats.php.
Yesterday's simulation run: [paste simulate-day --status output].
Help me decide 1-3 improvements for tomorrow. Prefer simulation tuning
over UI unless dashboard metrics look flat. Propose changes; wait for my
approval before editing files.
```

### Portfolio review prep

```
Read docs/SIMULATION.md. Summarize for a hiring manager: how simulation
proves real product behavior, how honesty is labeled, and how the daily
agent loop works. List demo login and admin path.
```

---

## Schema

### `users.simulation`

`TINYINT(1)` / `INTEGER` — `1` for portfolio simulation accounts.

### `simulation_runs`

| Column | Purpose |
|--------|---------|
| `pacific_date` | Primary key, calendar date simulated |
| `users_active` | Chefs active that day |
| `actions_count` | Approximate model actions |
| `executed_at` | When the script finished (UTC) |

### Tables touched by simulation

`users` → `projects` → `pantry_values` → `artifacts` → `artifact_versions`
→ `artifact_checks` → `exports`

Deleting a simulation user **cascades** all kitchen data.

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Dashboard empty | Run `simulate-users.php` then `simulate-day.php` |
| “Already simulated” | Use `--force` or pick a new `--date=` |
| “Pool too small” | Run `simulate-users.php` |
| “No executable cookbooks” | Run `seed.php` |
| Timestamps look wrong | Remember DB is UTC; dashboard uses Pacific windows |
| MySQL vs SQLite | Both supported; `simulation_runs` upsert differs by driver |

### Wipe simulation only

```sh
php scripts/simulate-users.php --fresh
php scripts/simulate-users.php
```

Does **not** delete real sign-ups or admin.

### Nuclear (all data)

```sh
php scripts/seed.php --fresh   # destroys everything — portfolio dev only
```

---

## Future versions

| Version | Idea |
|---------|------|
| v2 | `analytics-snapshot.json` after each run for agents |
| v2 | Rolling 15-min executor instead of preload |
| v2 | Scale pool to 1,000–2,000 |
| v3 | HTTP-level simulation (curl register flow) |
| v3 | Auto PR drafts from daily analysis |

---

## File map (for Claude Code)

| File | Role |
|------|------|
| `docs/SIMULATION.md` | This playbook |
| `database/simulation/personas.json` | 500 chef names |
| `scripts/generate-personas.php` | Regenerate personas |
| `scripts/simulate-users.php` | Create simulation user pool |
| `scripts/simulate-day.php` | Preload one Pacific day |
| `app/Services/Simulation.php` | Timezone, email, persona helpers |
| `app/Services/SimulationKitchen.php` | Model-layer kitchen actions |
| `app/Services/SiteStats.php` | Dashboard + admin aggregates |
| `app/Views/marketing/home.php` | Public dashboard UI |
| `app/Views/admin/index.php` | Admin simulation metrics |

---

## Quick reference card

```sh
# One-time
php scripts/seed.php
php scripts/simulate-users.php

# Daily (Pacific)
php scripts/simulate-day.php --date=yesterday

# Demo login
# kitchen+1@demo.local / demo-kitchen-2026

# Proof URLs
# /        — public dashboard
# /admin   — hiring-manager metrics
```
