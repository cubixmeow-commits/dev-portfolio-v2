# Homepage Activity Dashboard — Presentation Layer Report

## 1. Files changed

| File | Change |
|------|--------|
| `app/Services/HomepageActivityPresenter.php` | **New** — interpretation layer for homepage activity |
| `app/Controllers/MarketingController.php` | Passes `activityBoard` bundle from presenter |
| `app/Views/marketing/home.php` | Three accomplishment sections + visitor-focused metrics |
| `public/assets/css/pages/marketing.css` | Achievements, insights grid, feed tone styles |

**Not changed:** simulation engine, runner, seeds, recipe definitions, `SiteStats` raw queries (admin still uses them).

---

## 2. Previous homepage metrics

| Metric | Label |
|--------|-------|
| Primary | Projects completed today |
| Pills | **Steps approved** · creators active |
| KPI 1 | Creators |
| KPI 2 | All-time projects |
| KPI 3 | **Satisfaction** (demo rating) |
| KPI 4 | Workflows (catalog count) |
| Heatmap | **Workflow activity** — "X actions across N weeks" |
| Feed badges | Project complete · In progress · **Details added** · New member |
| Feed copy | "Recipe 4 of 8", pantry saves, internal cookbook titles |
| Popular | "X active" + demo star rating |

---

## 3. New homepage metrics

| Metric | Label |
|--------|-------|
| Primary | Projects completed today |
| Pills | **Milestones reached** · creators active |
| KPI 1 | Active creators |
| KPI 2 | Finished projects |
| KPI 3 | **In progress now** |
| KPI 4 | **Finished this week** |
| Heatmap | **Project milestones** — "X milestones across N weeks" |
| Feed | Accomplishment sentences only |
| Popular | Finished today · in progress · **completion rate %** |

---

## 4. Activity formatter architecture

```
Simulation DB (projects, artifacts, exports, users)
        ↓
SiteStats (raw counts, heatmap grid — unchanged)
        ↓
HomepageActivityPresenter (homepage-only)
        ↓
MarketingController → home.php
```

`HomepageActivityPresenter::bundle()` returns:

- `metrics` — visitor-focused KPIs
- `heatmap` — relabeled heatmap metadata
- `feed` — mixed accomplishment feed
- `achievements` — earned milestone cards
- `insights` — trend summaries
- `popular` — workflows ranked by completion engagement

Internal event kinds collected, then translated:

| Internal signal | Visitor presentation |
|-----------------|-------------------|
| `project.completed_at` | "Maria completed a product launch package." |
| `project.pantry_saved_at` | "Alex started building a YouTube publishing plan." |
| `artifact approved` + recipe | "Jordan completed the messaging stage of a product launch." |
| `project resumed` (multi-day gap) | "Camille picked a SaaS validation report back up." |

Milestone names use a presentation map keyed by **recipe slug** (`MILESTONE_LABELS`) with fallback from recipe title — no seed file changes.

---

## 5. New activity feed examples

- "Priya completed a SaaS validation report."
- "Alex started building a YouTube publishing plan."
- "Jordan completed the messaging stage of a product launch."
- "Maria completed launch landing page copy."
- "Camille picked a product launch package back up."

Feed mixing interleaves milestones, starts, resumes, and completions; avoids consecutive same-user entries when possible.

---

## 6. Achievement system design

Achievements are **computed from simulated data**, not random:

| Achievement | Earned when |
|-------------|-------------|
| First project completed | User's earliest `completed_at` |
| Three workflows finished | 3+ distinct cookbooks completed |
| Returned to finish | Project span ≥ 2 days with late activity |
| Every review completed | All recipes approved for a cookbook |
| Across multiple categories | 2+ cookbook categories completed |
| Five finished projects | 5+ completed projects |

---

## 7. Community insights section

Insight cards summarize:

- Most completed workflow today
- Fastest growing workflow (starts today)
- Creators active today
- Projects completed this week (+ week-over-week note)
- Average project progress (%)
- Most in-progress workflow
- Most returned-to workflow (when data exists)

---

## 8. Heatmap improvements

- Title: **Project milestones**
- Subtitle: milestones count (not "actions")
- Tooltip: "N milestones" per day
- `aria-label`: milestone heatmap
- Underlying grid data unchanged (`SiteStats::activityHeatmap`)

---

## 9. Mobile review (375px)

- Metrics hero stacks (existing pattern)
- Feed + achievements stack single column via `.dashboard-boards`
- Insight grid uses `auto-fit` min 11rem cards
- Popular workflows retain readable metric pills
- Activity feed scroll preserved (`max-height`)

---

## 10. Accessibility

- Section `aria-labelledby` preserved
- Heatmap `role="img"` + descriptive `aria-label`
- Insight list uses `role="list"` / `role="listitem"`
- Achievement star mark `aria-hidden` (title in text)
- Feed badges convey event type in plain language
- Disclosure: "Portfolio demo" / "Simulated creator activity"

---

## 11. Claims deliberately avoided

- Pantry, Recipe, artifact, export terminology in feed
- "Actions" or internal event counts
- Random achievements
- Implied real customer activity (disclosure retained)

---

*Implemented on branch `cursor/homepage-activity-formatter-665b`.*
