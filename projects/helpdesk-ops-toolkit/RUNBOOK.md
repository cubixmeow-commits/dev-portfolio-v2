# Helpdesk Ops Toolkit — Runbook

Operational procedures for IT Support agents. Audience: support staff and their
back-up. (Names, roles, and the escalation chain below are realistic but
fictional, for the portfolio demo.)

---

## 1. Triage a new ticket

New tickets arrive from the public **Submit a ticket** form with status
`Open` and no assignee.

1. **Open the queue** — sign in as an agent and go to **Tickets**. Sort/scan the
   `Open` items; the queue is ordered by status, then priority, then age.
2. **Confirm category and priority.** Adjust if the requester mis-tagged it:
   - `high` — a person or a whole team can't work (outage, locked account for
     someone on shift, security concern).
   - `medium` — impaired but working (slow app, single failed peripheral).
   - `low` — requests and nice-to-haves (new monitor, guest Wi-Fi code).
3. **Assign it.** Open the ticket, choose an agent under **Assign to**, set the
   status to `In progress`, and **Save changes**. Assignment is a logged change,
   not a verbal hand-off.
4. **Work it, then record the outcome.** When done, set the status to `Resolved`
   (fix delivered) or `Closed` (no action needed / duplicate) and add a
   **resolution note** describing what you did. The note is appended to the
   ticket record with your name and a timestamp; `resolved_at` is set
   automatically.

**Reopening:** setting a ticket back to `Open`/`In progress` clears
`resolved_at` so resolution-time reporting stays honest.

---

## 2. Assets: surplus and hand-off

1. Go to **Assets**. Filter by status/type/department as needed.
2. To retire equipment from active use, click **Surplus** on the row (or edit
   the asset and set status to `surplus`/`retired`).
3. For an inventory hand-off, use **Export CSV** — it exports the current
   filtered list (e.g. `status = surplus`) as a spreadsheet for the surplus
   process.

---

## 3. Run the weekly report (Java tool)

Run every Monday for the previous week's numbers.

```sh
cd java
./build.sh                      # first time only, or after code changes
HOT_DB_URL="jdbc:mysql://127.0.0.1:3306/helpdesk_ops" \
  HOT_DB_USER=svc_report HOT_DB_PASS=**** \
  java -jar target/ticket-report.jar --range weekly
```

- The summary prints to the console **and** is saved to
  `reports/ticket-report-YYYYMMDD-HHMMSS.txt`.
- Each run logs to standard error: a start line, the number of rows pulled, and
  a completion or failure line. Keep these for troubleshooting.

The same figures are available live on the PHP **Reports** page — use that for a
quick look, the Java tool for the archived weekly file.

---

## 4. Error codes & what they mean

**Java tool (process exit code):**

| Exit | Meaning | What to do |
|-----:|---------|------------|
| `0`  | Success | Report generated and saved. |
| `2`  | Database error (`SQLException`) | MySQL down, wrong `HOT_DB_URL`/creds, schema not imported, or the `mysql-connector-j` driver missing from the classpath. Rebuild with `./build.sh` (bundles the driver). |
| `3`  | File write error (`IOException`) | The `reports/` directory isn't writable. Check permissions/disk. |

**PHP app:**

| Symptom | Meaning | What to do |
|---------|---------|------------|
| "The helpdesk is temporarily unavailable" (HTTP 503) | App can't reach the database | Verify MySQL is running and `HOT_DB_*` env vars are set; import `schema.sql`/`seed.sql`. |
| "Bad request (invalid CSRF token)" (HTTP 400) | Stale form / expired session | Go back, reload the page, and resubmit. |
| Redirected to the sign-in page | Not signed in as an agent | Sign in; Reports and Assets are agent-only. |

---

## 5. Escalation path (fictional)

1. **Tier 1 — Support agent** (you): triage, common fixes, account unlocks,
   asset moves.
2. **Tier 2 — Systems specialist** (M. Chen / D. Okafor): network, server, and
   application issues beyond a standard fix; anything `high` open > 4 hours.
3. **Tier 3 — IT Administrator** (J. Nguyen): outages affecting a whole
   department, security incidents, and anything requiring policy or vendor
   involvement. Page immediately for suspected security events.

When escalating, set the ticket to the correct assignee, add a note with what
you've already tried, and keep the priority accurate.
