# Homepage Reposition Report — Three Core Advantages

## 1. Files changed

| File | Change |
|------|--------|
| `app/Views/marketing/home.php` | Full homepage restructure around three pillars |
| `app/Controllers/MarketingController.php` | Passes `Cookbook::marketplace()` for workflow cards |
| `public/assets/css/pages/marketing.css` | Pillars, comparison, subscription flow, trust, workflow grid |
| `app/Views/layout/app.php` | Updated meta description |

---

## 2. Previous homepage hierarchy

1. Hero (product + terminology callout)
2. How it works (4 steps)
3. Featured workflow (Launch Day Kit detail)
4. Marketplace teaser (single CTA block)
5. Community activity dashboard
6. Why SousMeow is different (3 honesty items)
7. Final CTA

---

## 3. New homepage hierarchy

1. **Hero** — product promise + BYO-AI advantage + terminology bridge
2. **Three product pillars** — guided workflows / your subscription / reusable processes
3. **How it works** — 4-step loop (`#how-it-works`)
4. **Comparison** — prompts vs workflows
5. **Subscription advantage** — dedicated flow diagram section
6. **Trust** — workflows with a definition of done
7. **Explore guided workflows** — all real Cookbooks with outcome metadata
8. **Community activity** — simulated dashboard (atmosphere only)
9. **Final CTA**

---

## 4. Hero copy changes

| Element | Before | After |
|---------|--------|-------|
| Headline | Build complete projects using the AI subscriptions you already have | **Turn complex projects into guided AI workflows** |
| Body | Single paragraph mixing product + AI names | Two paragraphs: process value, then subscription advantage |
| Primary CTA | Start free / Open my projects | **Explore workflows** / **Continue my project** |
| Secondary CTA | Explore workflows (to marketplace) | **See how it works** (anchor) |
| Terminology | Full Cookbook/Recipe/Pantry/Kit callout | Compact: Cookbooks + Recipes only |

---

## 5. Three product pillars

Visual three-card section immediately after hero:

1. **Guided workflows** — complexity → clear steps; themed line: “From idea to finished project”
2. **Your AI, your subscription** — ChatGPT/Claude/Gemini; themed line: “Bring your own AI”
3. **Reusable processes** — structured inputs, checks, exports; themed line: “Marketplace direction”

Headings are plain product language. Restaurant lines are italic subtitles only.

---

## 6. Existing-subscription advantage

Communicated in **three places**:

1. Hero second paragraph (no API, no token bill)
2. Pillar 2 card
3. Dedicated **“The workflow layer for the AI tools you already use”** section with vertical flow diagram and bullet points (no API, no markup, model freedom, data transparency)

Explicit loop stated: prepare → run in your AI → paste back → review.

---

## 7. Marketplace direction (honest)

- **Explore guided workflows** section lists all 5 real Cookbooks from DB
- Each card shows: category, availability badge, title, tagline, steps, time, audience, “You receive: {outcome}”
- Footer honesty line: 3 runnable, 2 previews, no purchases or creator payouts
- Trust section mentions marketplace as **future direction**, not current feature
- No invented creators, sales, reviews, or transactions

---

## 8. Restaurant terminology retained

| Term | Where |
|------|-------|
| Cookbook | Terminology bridge, loop step, workflow cards, themed subtitles |
| Recipe | Terminology bridge, step counts |
| Pantry | Loop step 2 explanation |
| Project Kit | Trust section export item |
| Kitchen | Dashboard themed subtitles only (“Fresh from the kitchen”, “kitchen pulse”) |
| Cookbook (final CTA) | Optional themed line only |

---

## 9. Claims deliberately avoided

- Fully automated AI execution
- SousMeow runs prompts for you
- Automatic quality grading
- Creator earnings / marketplace transactions
- Selling workflows today
- Fake community scale beyond disclosed simulation
- “Revolutionary” / hype language
- External AI brand logos

---

## 10. Mobile validation (375px)

- Hero value prop and BYO-AI line appear before pillars
- Pillars stack single column
- Comparison stacks (no cramped table)
- Subscription flow diagram stacks vertically
- Workflow cards single column with outcome-first copy
- Dashboard remains after product story
- CTAs use plain labels (Explore workflows, See how it works)

---

## 11. Accessibility

- `aria-labelledby` on all major sections
- `visually-hidden` heading for pillars group
- `aria-label` on flow diagram list
- Semantic `article`, `section`, `ol`, `ul` structure
- Existing `prefers-reduced-motion` on live dot preserved
- Anchor link `#how-it-works` for secondary CTA
- No information conveyed by color alone on workflow availability (text badges)

---

## 12. Remaining ambiguity

| Area | Note |
|------|------|
| “Creators” metric | Portfolio simulation label; disclosed as simulated |
| Demo rating stars on trending | Catalog metadata, not user reviews |
| “Marketplace direction” in pillar 3 | Intentionally forward-looking |
| Runner still uses Recipe in UI | Taught after homepage; separate from this mission |

---

*Implemented on branch `cursor/theme-refinement-665b`.*
