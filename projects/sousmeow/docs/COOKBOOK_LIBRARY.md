# SousMeow First-Party Cookbook Library

Curated library of five first-party Cookbooks demonstrating distinct expert-guided workflows on the SousMeow platform.

## STRUCTURAL SIGNATURES

| Cookbook | Availability | Stage count | Recipe count | Pantry field count | Difficulty | Duration | Dominant Recipe types | Review pattern | Structural distinction | Project Kit |
|----------|--------------|-------------|--------------|-------------------|------------|----------|-------------------------|----------------|----------------------|-------------|
| Launch Day Kit | Executable | 4 | 4 | 8 | Intermediate | 25 min | Sequential copy assembly | 3 checks per Recipe, fast approval | Deadline-driven campaign assembly; positioning becomes ground truth | Positioning, landing page, announcements, FAQ |
| Validate a SaaS Idea | Executable | 5 | 12 | 10 | Advanced | 180 min | Evidence briefs, matrices, decision memos | 4 checks per Recipe, longer assumption review | Evidence gates ending in explicit go / revise / stop decision | Problem brief, ICP, competitor map, assumptions, MVP scope, experiment plan, decision memo |
| Build a Professional Portfolio | Preview | 4 | 14 | 12 | Intermediate | 150 min | Strategic selection + polished writing | 3 checks per Recipe, recruiter-lens review | Modular case-study tracks converging into one site brief | Positioning, case structures, homepage copy, navigation plan, implementation brief |
| Plan a YouTube Video | Preview | 3 | 10 | 6 | Beginner | 45 min | Rapid creative micro-deliverables | 3 checks per Recipe, immediate progress feel | Fast sequence of small visible outputs | Research brief, hooks, outline, titles, thumbnails, publishing checklist |
| Plan a Novel | Preview | 7 | 18 | 16 | Advanced | 240 min | Exploratory decisions + continuity planning | 3 checks per Recipe, decision-heavy branching | Deep Pantry with world rules and continuity control | Premise, cast, world rules, conflict engine, act outline, chapter roadmap, continuity guide |

No two Cookbooks share the same stage shape, recipe count, or review rhythm.

---

## 1. Launch Day Kit

**Slug:** `launch-day-kit`  
**Availability:** Executable (Available now)  
**Category:** Marketing | **Accent:** Terracotta | **Price:** Free  
**Demo metrics:** 847 completed runs, 4.8 average rating

### Metadata

- **Subtitle:** Turn plain product notes into everything you publish on launch day.
- **Intended user:** Indie makers and small teams shipping a v1
- **Expected outcome:** Positioning, landing page copy, announcements for your channels, and an honest FAQ
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Cover-art direction

Warm terracotta band across the top of the card; launch-day energy without hype. Suggested illustration: a small kitchen pass with four plated cards (positioning doc, landing hero, social post, FAQ sheet) under soft morning light.

### Pantry (8 ingredients)

| Key | Label | Type |
|-----|-------|------|
| product_name | Product name | text |
| one_liner | One-line description | text |
| audience | Who is it for? | textarea |
| key_features | Key features | textarea |
| tone | Voice and tone | select |
| channels | Launch channels | multiselect |
| price_usd | Monthly price (USD) | number |
| website_url | Website URL | url |

### Stages and Recipes

| Stage | Recipes |
|-------|---------|
| 1. Position | Position the Dish |
| 2. Convert | Plate the Landing Page |
| 3. Announce | Serve the Announcements |
| 4. Defend | Answer the Table |

**Dependencies:** Recipe 1 is Pantry-only. Recipes 2-4 chain `{{artifact:position-the-dish}}`.

**Sample output:** Driftlog positioning (fictional passive time logger for freelance designers).

### Project Kit

`01-position-the-dish.md`, `02-plate-the-landing-page.md`, `03-serve-the-announcements.md`, `04-answer-the-table.md`, `README.md`

---

## 2. Validate a SaaS Idea

**Slug:** `validate-saas-idea`  
**Availability:** Executable (Available now)  
**Category:** Research | **Accent:** Teal | **Price:** Free  
**Demo metrics:** 312 completed runs, 4.7 average rating

### Metadata

- **Subtitle:** Evidence-driven validation from problem brief to go, revise, or stop.
- **Intended user:** Founders with a SaaS hypothesis who need structured validation before building
- **Expected outcome:** Problem validation brief, ICP, competitor landscape, riskiest assumptions, MVP recommendation, validation experiment plan, and a go/revise/stop decision memo
- **Supported AI tools:** Any external research or writing assistant

### Cover-art direction

Teal diagonal-stripe band suggesting rigor and measurement. Illustration: a founder at a desk with three columns labeled Evidence, Assumptions, and Decision.

### Pantry (10 ingredients)

idea_name, problem_statement, target_buyer, current_workaround, founder_insight, known_competitors, price_hypothesis, reach_channel, success_signal, decision_deadline

### Stages and Recipes

| Stage | Recipes |
|-------|---------|
| 1. Frame the Bet | Problem Validation Brief, Pain Frequency Snapshot |
| 2. Know the Buyer | Ideal Customer Profile, Buyer Access Plan |
| 3. Map the Market | Competitor Landscape, Differentiation Hypothesis |
| 4. Stress the Assumptions | Riskiest Assumptions Register, Assumption Evidence Matrix, Kill Criteria |
| 5. Decide and Test | MVP Scope Recommendation, Validation Experiment Plan, Go / Revise / Stop Memo |

**Sample output:** LedgerLoop (invoice reconciliation for Shopify merchants) across all 12 Recipes.

**Distinctive Quality Checks (examples):**

- Are market claims supported by evidence or clearly marked as assumptions?
- Does the proposed experiment test the riskiest belief before a full build?
- Is the target customer narrow enough to reach through a realistic channel?

### Project Kit

Twelve numbered Markdown files plus README with full Pantry snapshot.

---

## 3. Build a Professional Portfolio

**Slug:** `build-professional-portfolio`  
**Availability:** Workflow preview (Runner coming soon)  
**Category:** Career | **Accent:** Amber | **Price:** Free  
**Demo metrics:** 428 completed runs, 4.9 average rating

### Metadata

- **Subtitle:** Modular case-study tracks that converge into one site brief you can actually build.
- **Intended user:** Designers, developers, and product people rebuilding a portfolio for their next role
- **Expected outcome:** Positioning statement, prioritized case studies, homepage and case copy, navigation plan, and a build-ready site brief

### Cover-art direction

Amber dotted band suggesting craft and polish. Illustration: three project cards fanning into a single homepage wireframe.

### Pantry (12 ingredients)

full_name, professional_title, years_experience, target_roles, strongest_skills, career_goal, project_one, project_two, project_three, design_tone, portfolio_platform, contact_preference

### Stages and Recipes (14)

| Stage | Recipes |
|-------|---------|
| 1. Position | Positioning Statement, Capability Inventory, Audience Targeting |
| 2. Select Projects | Project Shortlist, Case Study Priority, Skills Matrix, Gap Analysis |
| 3. Write Cases | Homepage Copy, Case Study Structure A, Case Study Structure B, Professional Summary |
| 4. Assemble Site | Navigation Plan, Portfolio Implementation Brief, Recruiter Scan Test |

**Sample output:** Mira Chen UX designer positioning statement.

**Distinctive Quality Checks (examples):**

- Does every selected project prove a distinct professional capability?
- Does the case study explain decisions and tradeoffs, not only features?
- Can a hiring manager understand the candidate's role within thirty seconds?

### Project Kit

Fourteen numbered Markdown files plus README.

---

## 4. Plan a YouTube Video

**Slug:** `plan-youtube-video`  
**Availability:** Workflow preview (Runner coming soon)  
**Category:** Content | **Accent:** Sage | **Price:** Free  
**Demo metrics:** 591 completed runs, 4.5 average rating

### Metadata

- **Subtitle:** From topic to publish-ready package in under an hour.
- **Intended user:** Beginner creators planning their next video before filming
- **Expected outcome:** Research brief, audience angle, hook options, video outline, title concepts, thumbnail directions, and a publishing checklist

### Cover-art direction

Sage gradient wave band suggesting momentum. Illustration: storyboard panels beside a bold thumbnail sketch.

### Pantry (6 ingredients)

video_topic, channel_niche, target_viewer, video_length, filming_setup, publish_day

### Stages and Recipes (10)

| Stage | Recipes |
|-------|---------|
| 1. Research and Angle | Research Brief, Audience Angle, Competitor Video Notes |
| 2. Structure and Hook | Hook Options, Video Outline, Retention Beats, B-Roll Shot List |
| 3. Package and Publish | Title Concepts, Thumbnail Directions, Publishing Checklist |

**Sample output:** One-pan weeknight pasta research brief for a beginner cooking channel.

**Distinctive Quality Checks (examples):**

- Does the hook create curiosity without making a misleading promise?
- Does the opening reach the central value before unnecessary background?
- Can the thumbnail concept be understood at mobile size?

### Project Kit

Ten numbered Markdown files plus README.

---

## 5. Plan a Novel

**Slug:** `plan-a-novel`  
**Availability:** Workflow preview (Runner coming soon)  
**Category:** Creative Writing | **Accent:** Lilac | **Price:** Free  
**Demo metrics:** 156 completed runs, 4.6 average rating

### Metadata

- **Subtitle:** Exploratory decisions that become a continuity-controlled outline.
- **Intended user:** Fiction writers starting a novel who need structure without killing discovery
- **Expected outcome:** Original premise, reader promise, protagonist and cast, world rules, conflict engine, structural outline, chapter roadmap, and continuity guide

### Cover-art direction

Lilac crosshatch band suggesting imagination within constraints. Illustration: a compass rose over chapter index cards.

### Pantry (16 ingredients)

working_title, genre, tone, target_reader, core_theme, protagonist_name, protagonist_want, protagonist_flaw, antagonist_force, setting_era, world_rule_one, world_rule_two, story_length, pov_style, comp_titles, non_negotiables

### Stages and Recipes (18)

| Stage | Recipes |
|-------|---------|
| 1. Spark | Original Premise, Genre and Tone |
| 2. Promise | Reader Promise, Emotional Payoff |
| 3. People | Protagonist Profile, Antagonist Pressure, Supporting Cast Map |
| 4. World | World Rules, Setting Constraints, Culture of Place |
| 5. Conflict | Conflict Engine, Stakes Escalation, Midpoint Shift |
| 6. Structure | Act Outline, Chapter Roadmap, Scene Beat Sheet |
| 7. Continuity | Continuity Guide, Revision Priorities |

**Sample output:** *The Keeper's Frequency* literary fiction premise (lighthouse keeper and forgotten radio signal).

**Distinctive Quality Checks (examples):**

- Does the protagonist's goal create pressure across the whole story?
- Do the world rules generate consequences without arbitrary exceptions?
- Does the outline escalate rather than repeat similar obstacles?

### Project Kit

Eighteen numbered Markdown files plus README.

---

## Seed source of truth

Full prompts, checks, and sample outputs live in:

- `database/seeds/cookbooks/launch-day-kit.php`
- `database/seeds/cookbooks/validate-saas-idea.php`
- `database/seeds/cookbooks/professional-portfolio.php`
- `database/seeds/cookbooks/plan-youtube-video.php`
- `database/seeds/cookbooks/plan-a-novel.php`

Loaded by `database/seeds/content.php` and inserted via `scripts/seed.php`.
