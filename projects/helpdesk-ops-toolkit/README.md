# Helpdesk Ops Toolkit

A portfolio project demonstrating the core competencies of a government IT
Support / Computer Systems Specialist role: ticket triage, cross-department
support, reporting, end-user documentation, and asset support — deliberately
**department-agnostic**, built to speak to the broad IT support job market
rather than one specific posting.

**Stack:** PHP 8 · MySQL · Java (JDBC). No framework, no build step for the web
app, and no external requests — the report charts are hand-drawn inline SVG,
not a charting CDN.

> Part of the [dev-portfolio](https://github.com/cubixmeow-commits) collection.
> All sample data (tickets, names, departments) is fictional.

---

## Architecture

Two coordinated pieces sharing one MySQL database:

1. **PHP web app** (`web/`) — the ticketing system, reporting dashboard, asset
   tracker, and knowledge base. The primary, demoable artifact.
2. **Java CLI tool** (`java/`) — `TicketReportGenerator`, a standalone weekly
   ops-summary generator that connects to the *same* database over JDBC and
   writes a console + file report.

This split mirrors the real-world pattern of "the department's web system" plus
"a support script someone in IT wrote to automate a recurring report," and
shows the same data adapted to reporting needs in two languages.

### Database table prefix

Every table uses the project-unique prefix **`hot_`** (`hot_tickets`,
`hot_assets`, `hot_kb_articles`, `hot_departments`, `hot_users`) so this
project can share a database with other portfolio projects without collision.

---

## Features

**PHP app**
- **Tickets** (`index.php`) — public "submit a ticket" view and an auth-gated
  agent queue with filter by status / category / priority / department, plus
  per-ticket status changes, assignment, and resolution notes.
- **Reports** (`reports.php`) — ticket volume by week, tickets by
  category/status/priority, open-vs-closed, and average resolution time,
  rendered as inline-SVG charts. Same query logic as the Java tool.
- **Assets** (`assets.php`) — inventory list with filters, add/edit, reassign,
  a surplus/retire lifecycle, and CSV export.
- **Knowledge Base** (`kb/`) — plain-language, end-user articles (monitor won't
  turn on, requesting software access, connecting to Wi-Fi, password/lockout)
  plus a technical reference, rendered from Markdown.

**Java tool** — `TicketReportGenerator --range weekly`: queries `hot_tickets`,
prints a summary table (by category/status/priority) to the console, and writes
a timestamped report to `reports/`. Logs start / row count / completion or
failure, with basic error handling and exit codes.

---

## Setup

### 1. Database (production: MySQL)

```sh
mysql -u USER -p -e "CREATE DATABASE helpdesk_ops CHARACTER SET utf8mb4;"
mysql -u USER -p helpdesk_ops < db/schema.sql
mysql -u USER -p helpdesk_ops < db/seed.sql
```

### 2. PHP app

Connection settings are read from the environment (no credentials in the repo):

```sh
export HOT_DB_DSN="mysql:host=127.0.0.1;dbname=helpdesk_ops;charset=utf8mb4"
export HOT_DB_USER="youruser"
export HOT_DB_PASS="yourpass"
php -S localhost:8000 -t web
# then open http://localhost:8000
```

Agent sign-in for the demo: **`agent` / `demo123`** (shown on the login page;
override with `HOT_AGENT_USER` / `HOT_AGENT_PASS`).

#### No MySQL handy? Local SQLite demo

To try the app without a MySQL server, build a SQLite copy of the schema + seed
and point the app at it:

```sh
php db/make_demo_db.php
HOT_DB_DSN="sqlite:$(pwd)/db/hot_demo.sqlite" php -S localhost:8000 -t web
```

MySQL remains the canonical target; the SQLite mirror exists only so the app is
runnable/screenshottable locally. (Report analytics are computed in PHP, so the
same code runs against both.)

### 3. Java report tool

```sh
cd java
./build.sh                 # Maven -> target/ticket-report.jar (bundles the JDBC driver)
HOT_DB_URL="jdbc:mysql://127.0.0.1:3306/helpdesk_ops" \
  HOT_DB_USER=youruser HOT_DB_PASS=yourpass \
  java -jar target/ticket-report.jar --range weekly
```

The report prints to the console and is saved under `reports/`.

---

## Layout

```
helpdesk-ops-toolkit/
├── db/
│   ├── schema.sql            # MySQL schema (hot_ prefix) — canonical
│   ├── seed.sql              # ~40 tickets, 15 assets, 6 KB articles, depts, users
│   ├── sqlite_schema.sql     # local-demo mirror
│   └── make_demo_db.php      # builds db/hot_demo.sqlite for local runs
├── web/                      # PHP app (document root)
│   ├── index.php  ticket.php  reports.php  assets.php  assets_export.php
│   ├── login.php  logout.php  config.php
│   ├── kb/                    # knowledge base
│   ├── lib/                   # db, auth, repo, helpers (Markdown), view
│   └── static/css/style.css   # "civic" design system
├── java/
│   ├── src/main/java/com/cubixmeow/helpdesk/TicketReportGenerator.java
│   ├── pom.xml  build.sh  run.sh
├── reports/                  # Java report output (gitignored)
├── README.md  RUNBOOK.md  END_USER_GUIDE.md
```

---

## Design note

The web UI uses a deliberately plain, **civic** look inspired by public-sector
IT portals (the U.S. Web Design System and GOV.UK): an official-notice banner,
a solid navy masthead, a simple tab nav, and high-contrast tables. It is
intentionally distinct from the rest of the portfolio — each project gets its
own design — and, like the rest of the portfolio, ships with no framework and
no external requests.

## Documentation

- **[RUNBOOK.md](RUNBOOK.md)** — how to triage a ticket, run the weekly report,
  read error codes, and escalate.
- **[END_USER_GUIDE.md](END_USER_GUIDE.md)** — for non-technical staff: how to
  submit a ticket and check its status.
