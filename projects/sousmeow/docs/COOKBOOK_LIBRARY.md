# SousMeow First-Party Cookbook Library

Curated library of twenty-four first-party Cookbooks demonstrating distinct expert-guided workflows on the SousMeow platform. Every primary category has exactly two Cookbooks.

## Mandatory authoring rule

**Every Cookbook proposal MUST pass [Product Law 002 — Remove Cognitive Load](PRODUCT_LAW_002_REMOVE_COGNITIVE_LOAD.md) before authoring.**

That document is the authoritative source for the Complexity Scorecard and Complexity Gate. Do not duplicate it here. Proposals that fail two or more scorecard checks must be simplified or rejected.

## STRUCTURAL SIGNATURES

Each Cookbook must keep a unique **stage shape** (recipes per stage, e.g. `2-2-1`). Recipe counts may repeat when Product Law 002 favors a short surface, as long as stage shapes stay unique.

| Cookbook | Availability | Stages | Recipes | Fields | Difficulty | Duration | Stage shape | Category |
|----------|--------------|--------|---------|--------|------------|----------|-------------|----------|
| Launch Day Kit | Executable | 4 | 4 | 8 | Intermediate | 25 min | 1-1-1-1 | marketing-growth |
| Validate a SaaS Idea | Executable | 5 | 12 | 10 | Advanced | 180 min | 2-2-2-3-3 | start-grow-business |
| Build a Professional Portfolio | Preview | 4 | 14 | 12 | Intermediate | 150 min | 3-4-4-3 | career-freelance |
| Plan a YouTube Video | Executable | 3 | 10 | 6 | Beginner | 45 min | 3-4-3 | content-audience |
| Plan a Novel | Preview | 7 | 18 | 16 | Advanced | 240 min | 2-2-3-3-3-3-2 | creative-worlds |
| Build a Study Plan | Executable | 4 | 5 | 7 | Beginner | 40 min | 1-1-1-2 | learning-teaching |
| Write an Email That Gets Answered | Executable | 3 | 3 | 7 | Beginner | 28 min | 1-1-1 | writing-publishing |
| Write a Feature Spec | Executable | 3 | 6 | 8 | Intermediate | 50 min | 2-2-2 | software-product |
| Name Your Brand Voice | Executable | 3 | 7 | 6 | Beginner | 30 min | 2-3-2 | design-brand |
| Compare Three Competitors | Executable | 3 | 8 | 8 | Intermediate | 55 min | 2-4-2 | research-insights |
| Make a Criteria Decision | Executable | 2 | 2 | 6 | Beginner | 20 min | 1-1 | planning-productivity |
| Finish a Personal Project | Executable | 3 | 9 | 7 | Beginner | 70 min | 3-3-3 | personal-projects |
| Price Your Offer | Executable | 1 | 1 | 5 | Beginner | 15 min | 1 | start-grow-business |
| Write a Campaign Brief | Executable | 3 | 5 | 7 | Intermediate | 45 min | 2-2-1 | marketing-growth |
| Pack a Release Checklist | Executable | 3 | 4 | 7 | Intermediate | 40 min | 1-2-1 | software-product |
| Plan a Newsletter Issue | Executable | 3 | 8 | 6 | Beginner | 35 min | 2-3-3 | content-audience |
| Outline an Article | Executable | 3 | 5 | 6 | Beginner | 25 min | 1-3-1 | writing-publishing |
| Critique a Screen | Executable | 3 | 7 | 7 | Intermediate | 40 min | 2-2-3 | design-brand |
| Prep for an Interview | Executable | 3 | 8 | 7 | Beginner | 35 min | 3-2-3 | career-freelance |
| Synthesize Interview Notes | Executable | 4 | 6 | 7 | Intermediate | 50 min | 1-2-2-1 | research-insights |
| Plan a Lesson | Executable | 3 | 5 | 7 | Beginner | 45 min | 2-1-2 | learning-teaching |
| Document a Simple Process | Executable | 3 | 7 | 6 | Beginner | 30 min | 3-2-2 | planning-productivity |
| Design a Game Loop | Executable | 3 | 8 | 7 | Intermediate | 50 min | 4-2-2 | creative-worlds |
| Set a Thirty-Day Goal | Executable | 3 | 6 | 6 | Beginner | 22 min | 1-4-1 | personal-projects |

No two Cookbooks share the same stage shape.

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
**Availability:** Executable (Available now)  
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

## 6. Build a Study Plan

**Slug:** `build-a-study-plan`  
**Availability:** Executable (Available now)  
**Category:** Learning & Teaching | **Accent:** Pine | **Price:** Free  
**Demo metrics:** 214 completed runs, 4.7 average rating

### Metadata

- **Subtitle:** Turn a looming exam or skill goal into a calm, realistic practice schedule.
- **Intended user:** Students and self-learners facing an exam, certification, or skill checkpoint
- **Expected outcome:** Success criteria, topic map, weekly rhythm, retrieval practice kit, final-week plan
- **Prerequisites:** A real deadline, an honest estimate of weekly hours, and materials you already have
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

University learning-center methods (Cornell LSC study strategies; common five-day exam plans; spaced / retrieval practice). All SousMeow copy and examples are original. See `docs/COOKBOOK_SOURCE_TRANSFORM_STUDY_PLAN.md`.

### Pantry (7 ingredients)

subject_name, success_goal, deadline, hours_per_week, starting_point, weak_spots, materials

### Stages and Recipes (5)

| Stage | Recipes |
|-------|---------|
| 1. Aim | Define what finished looks like |
| 2. Map | Map the subject into study chunks |
| 3. Schedule | Place practice on real days |
| 4. Practice kit | Build your self-test kit, Plan the final week |

### Project Kit

Five numbered Markdown files plus README.

---

## 7. Write an Email That Gets Answered

**Slug:** `write-email-that-gets-answered`  
**Availability:** Executable (Available now)  
**Category:** Writing & Publishing | **Accent:** Clay | **Price:** Free  
**Demo metrics:** 389 completed runs, 4.8 average rating

### Metadata

- **Subtitle:** Turn a muddy draft into one clear ask a busy reader can act on.
- **Intended user:** Anyone who needs a clear reply from a busy person
- **Expected outcome:** Reader brief, subject + short email draft, revised send-ready email, pre-send checklist
- **Prerequisites:** A real recipient, one primary ask, and the facts they need to act
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

Digital.gov / Federal Plain Language principles and public guidance on clear email (audience first, important information first, active voice, short sentences). All SousMeow copy is original. See `docs/COOKBOOK_SOURCE_TRANSFORM_CLEAR_EMAIL.md`.

### Pantry (7 ingredients)

who_receives, your_relationship, the_ask, why_now, facts_they_need, tone, your_signoff

### Stages and Recipes (3)

| Stage | Recipes |
|-------|---------|
| 1. Aim | Name the reader and the one ask |
| 2. Draft | Write subject and short email |
| 3. Send-ready | Revise for clarity and pack a checklist |

### Project Kit

Three numbered Markdown files plus README.

---

## 8. Write a Feature Spec

**Slug:** `write-a-feature-spec`  
**Availability:** Executable (Available now)  
**Category:** Software & Product | **Accent:** Indigo | **Price:** Free  
**Demo metrics:** 214 completed runs, 4.7 average rating

### Metadata

- **Subtitle:** Turn a fuzzy feature idea into a one-page spec your team could build from.
- **Intended user:** Solo makers and small product teams writing down a feature before building
- **Expected outcome:** Problem brief, users, must-haves, non-goals, acceptance checks, ship checklist
- **Prerequisites:** A named feature, the product it sits in, and the user problem you actually know
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

Public agile user-story / INVEST teaching and lightweight PRD patterns. All SousMeow copy is original. See `docs/COOKBOOK_SOURCE_TRANSFORM_EMPTY_CATEGORIES.md`.

### Pantry (8 ingredients)

feature_name, product_context, user_type, problem_today, must_haves, constraints, success_signal, launch_window

### Stages and Recipes (6)

| Stage | Recipes |
|-------|---------|
| 1. Scope | Name the problem; Name who benefits |
| 2. Spec | Write must-haves; Write non-goals |
| 3. Ready | Write acceptance checks; Pack ship checklist |

### Project Kit

Six numbered Markdown files plus README.

---

## 9. Name Your Brand Voice

**Slug:** `name-your-brand-voice`  
**Availability:** Executable (Available now)  
**Category:** Design & Brand | **Accent:** Plum | **Price:** Free  
**Demo metrics:** 267 completed runs, 4.8 average rating

### Metadata

- **Subtitle:** Decide how you sound so every sentence stops sounding like someone else.
- **Intended user:** Solopreneurs and small teams who need a clear voice before designing visuals
- **Expected outcome:** Audience notes, promise, voice traits, do/don't examples, sample lines, visual cues, one-page brief
- **Prerequisites:** A brand or project name, who you serve, and words you already like or avoid
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

Public brand-voice teaching and plain-language brand guidance. See `docs/COOKBOOK_SOURCE_TRANSFORM_EMPTY_CATEGORIES.md`.

### Pantry (6 ingredients)

brand_name, what_you_offer, who_you_serve, three_words_you_want, three_words_you_avoid, where_voice_shows_up

### Stages and Recipes (7)

| Stage | Recipes |
|-------|---------|
| 1. Audience | Name who you serve; Name what you stand for |
| 2. Voice | Pick voice traits; Write do and don't; Write sample lines |
| 3. Brief | Note visual cues; Pack voice one-pager |

### Project Kit

Seven numbered Markdown files plus README.

---

## 10. Compare Three Competitors

**Slug:** `compare-three-competitors`  
**Availability:** Executable (Available now)  
**Category:** Research & Insights | **Accent:** Slate | **Price:** Free  
**Demo metrics:** 198 completed runs, 4.6 average rating

### Metadata

- **Subtitle:** Put three real alternatives side by side and leave with a clear opportunity note.
- **Intended user:** Founders and PMs who need a fast, honest competitor read before positioning
- **Expected outcome:** Research frame, competitor cards, offer/audience/pricing comparison, gaps, decision memo
- **Prerequisites:** Your offer, three named alternatives, and only facts you actually know
- **Supported AI tools:** Any external research or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

University entrepreneurship competitive-analysis teaching. Invent no funding, revenue, or feature claims. See `docs/COOKBOOK_SOURCE_TRANSFORM_EMPTY_CATEGORIES.md`.

### Pantry (8 ingredients)

research_goal, your_offer, competitor_a, competitor_b, competitor_c, known_facts, target_audience, decision_deadline

### Stages and Recipes (8)

| Stage | Recipes |
|-------|---------|
| 1. Field | Frame the question; Name three competitors |
| 2. Compare | Compare offers; Compare audiences; Compare pricing signals; Map gaps |
| 3. Conclude | Write opportunity notes; Pack decision memo |

### Project Kit

Eight numbered Markdown files plus README.

---

## 11. Make a Criteria Decision

**Slug:** `make-a-criteria-decision`  
**Availability:** Executable (Available now)  
**Category:** Planning & Productivity | **Accent:** Ochre | **Price:** Free  
**Demo metrics:** 441 completed runs, 4.9 average rating

### Metadata

- **Subtitle:** Choose between real options using criteria you already trust.
- **Intended user:** Anyone stuck between two or three real options and delaying a call
- **Expected outcome:** Framed options and criteria, scored table, choice, and first next step
- **Prerequisites:** A real decision, real options, and criteria you can name without research
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

University advising / structured decision-making worksheets (options, criteria, weigh, choose). See `docs/COOKBOOK_SOURCE_TRANSFORM_EMPTY_CATEGORIES.md`.

### Pantry (6 ingredients)

decision_title, option_a, option_b, option_c, criteria_list, non_negotiables

### Stages and Recipes (2)

| Stage | Recipes |
|-------|---------|
| 1. Frame | Frame options and criteria |
| 2. Decide | Score and choose |

### Project Kit

Two numbered Markdown files plus README.

---

## 12. Finish a Personal Project

**Slug:** `finish-a-personal-project`  
**Availability:** Executable (Available now)  
**Category:** Personal Projects | **Accent:** Moss | **Price:** Free  
**Demo metrics:** 312 completed runs, 4.7 average rating

### Metadata

- **Subtitle:** Give a personal project a finish line, a calendar, and a first week of real moves.
- **Intended user:** People with a meaningful personal project that keeps slipping
- **Expected outcome:** Done definition, why-now, constraints, chunks, week sequence, risks, check-in template, first-week actions, finish checklist
- **Prerequisites:** A project name, a done definition, hours you can give, and a real deadline
- **Supported AI tools:** Any external chat or writing assistant (copy/paste workflow)

### Public source inspiration (ideas only)

Public SMART-goal advising and simple work-breakdown / project planning teaching. See `docs/COOKBOOK_SOURCE_TRANSFORM_EMPTY_CATEGORIES.md`.

### Pantry (7 ingredients)

project_name, done_definition, why_now, hours_per_week, deadline, materials_ready, known_blockers

### Stages and Recipes (9)

| Stage | Recipes |
|-------|---------|
| 1. Aim | Define done; Name why now; Name constraints |
| 2. Plan | Break into chunks; Sequence the weeks; List risks |
| 3. Finish | Write check-in template; Plan first week; Pack finish checklist |

### Project Kit

Nine numbered Markdown files plus README.

---

## 13–24. Second Cookbook per category

Compact catalog entries for the second Cookbook in every primary category. Full prompts live in the seed files. See `docs/COOKBOOK_SOURCE_TRANSFORM_SECOND_PER_CATEGORY.md`.

### 13. Price Your Offer (`price-your-offer`)
**Category:** Start & Grow a Business · **Accent:** Sage · **1 stage / 1 recipe / ~15 min · Beginner**  
Cost floor + value proof → price you can say out loud. Source ideas: SBA/SBDC-style public pricing teaching.

### 14. Write a Campaign Brief (`write-a-campaign-brief`)
**Category:** Marketing & Growth · **Accent:** Terracotta · **3 stages / 5 recipes (`2-2-1`) / ~45 min · Intermediate**  
Audience, promise, channels, offer details, success measure. Source ideas: public AIDA / campaign-brief teaching.

### 15. Pack a Release Checklist (`pack-a-release-checklist`)
**Category:** Software & Product · **Accent:** Indigo · **3 stages / 4 recipes (`1-2-1`) / ~40 min · Intermediate**  
Frame release → must-finish work → owners/risks → go/no-go. Source ideas: public release checklist practices.

### 16. Plan a Newsletter Issue (`plan-a-newsletter-issue`)
**Category:** Content & Audience · **Accent:** Amber · **3 stages / 8 recipes (`2-3-3`) / ~35 min · Beginner**  
Reader promise through send checklist. Source ideas: public newsletter planning teaching.

### 17. Outline an Article (`outline-an-article`)
**Category:** Writing & Publishing · **Accent:** Clay · **3 stages / 5 recipes (`1-3-1`) / ~25 min · Beginner**  
Angle → ordered points → outline. Source ideas: inverted pyramid / journalism outline teaching.

### 18. Critique a Screen (`critique-a-screen`)
**Category:** Design & Brand · **Accent:** Lilac · **3 stages / 7 recipes (`2-2-3`) / ~40 min · Intermediate**  
User goal → findings → prioritized fixes. Source ideas: public usability / heuristic evaluation teaching (original wording).

### 19. Prep for an Interview (`prep-for-an-interview`)
**Category:** Career & Freelance · **Accent:** Indigo · **3 stages / 8 recipes (`3-2-3`) / ~35 min · Beginner**  
Story bank → STAR answers → rehearsal plan. Source ideas: public STAR / career-center interview prep.

### 20. Synthesize Interview Notes (`synthesize-interview-notes`)
**Category:** Research & Insights · **Accent:** Slate · **4 stages / 6 recipes (`1-2-2-1`) / ~50 min · Intermediate**  
Facts only → themes → implications memo. Source ideas: public 18F-style research synthesis. Invent no quotes.

### 21. Plan a Lesson (`plan-a-lesson`)
**Category:** Learning & Teaching · **Accent:** Pine · **3 stages / 5 recipes (`2-1-2`) / ~45 min · Beginner**  
Objective → activities → learning check. Source ideas: public lesson-plan frameworks.

### 22. Document a Simple Process (`document-a-simple-process`)
**Category:** Planning & Productivity · **Accent:** Ochre · **3 stages / 7 recipes (`3-2-2`) / ~30 min · Beginner**  
Trigger → clear steps → one-pager handoff. Source ideas: public SOP / process documentation teaching.

### 23. Design a Game Loop (`design-a-game-loop`)
**Category:** Creative Worlds · **Accent:** Plum · **3 stages / 8 recipes (`4-2-2`) / ~50 min · Intermediate**  
Fantasy → core loop → session sheet. Source ideas: public game-loop / lightweight MDA teaching.

### 24. Set a Thirty-Day Goal (`set-a-thirty-day-goal`)
**Category:** Personal Projects · **Accent:** Moss · **3 stages / 6 recipes (`1-4-1`) / ~22 min · Beginner**  
One finish line → four weeks → commitment card. Source ideas: public habit / short-horizon goal advising (distinct from Finish a Personal Project).

---

## Seed source of truth

Full prompts, checks, and sample outputs live in `database/seeds/cookbooks/*.php` (twenty-four files), loaded by `database/seeds/content.php` and inserted via `scripts/seed.php`.
