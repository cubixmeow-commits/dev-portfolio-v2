# SousMeow Theme Refinement Report

**Mission:** Preserve the restaurant-inspired identity while making the product understandable to a first-time visitor in under 10 seconds.

**Principle applied:** Plain language first. Restaurant language second.

---

## 1. Files modified

| File | Changes |
|------|---------|
| `app/Views/marketing/home.php` | Full homepage restructure: product-first hero, 4-step loop, section reorder, dashboard relabeling |
| `app/Views/layout/app.php` | Meta description, default title, navigation labels, footer |
| `app/Views/kitchen/index.php` | Page subtitle, empty state, status labels, button copy |
| `app/Views/marketplace/index.php` | Header, search, empty state |
| `app/Views/marketplace/show.php` | CTA, section headings |
| `app/Views/projects/pantry.php` | Headings, buttons, helper copy |
| `app/Views/projects/export.php` | Export CTAs and not-ready state |
| `app/Views/runner/step.php` | Approved state, approve button, continue/export CTAs |
| `app/Views/auth/login.php` | Aside copy |
| `app/Views/auth/register.php` | Aside copy, name field help |
| `app/Views/errors/403.php` | Plain-language access denied |
| `app/Views/errors/404.php` | Plain-language not found |
| `app/Views/errors/419.php` | Plain-language session expired |
| `app/Views/errors/500.php` | Plain-language server error |
| `app/Views/admin/index.php` | Table headers, simulation labels |
| `app/Services/SiteStats.php` | Activity feed message copy |
| `app/Core/Auth.php` | Sign-in redirect flash |
| `app/Controllers/AuthController.php` | Welcome/registration flashes |
| `app/Controllers/ProjectController.php` | Project lifecycle flashes |
| `app/Controllers/RunnerController.php` | Workflow progress flashes |
| `app/Controllers/ExportController.php` | Export flashes |
| `public/assets/css/pages/marketing.css` | New sections: terms callout, marketplace teaser, community, theme lines |

**Not modified (by design):** Database schema, models, controllers (routing), URLs, seed Cookbook/Recipe content, validation error strings (already plain-language).

---

## 2. Terminology changes (before → after)

### Category A — Kept (taught on first use)

| Term | Treatment |
|------|-----------|
| Cookbook | Kept; introduced in hero callout as “workflows are called Cookbooks” |
| Recipe | Kept; introduced as “each step is a Recipe” |
| Pantry | Kept; introduced as “project details live in the Pantry” |
| Project Kit | Kept; introduced as “finished files export as a Project Kit” |
| Kitchen | Kept as page name (“My Kitchen”) with plain subtitle |
| SousMeow | Unchanged |

### Category B — Kept with clarification

| Before | After |
|--------|-------|
| The stove is busy today | Workflow activity today |
| Today in the kitchen | Today |
| kits packed | projects completed |
| chefs cooking | creators active |
| Chefs | Creators |
| All-time kits | All-time projects |
| Kitchen rhythm | Workflow activity |
| Kitchen pulse | Recent activity (+ themed subtitle “The kitchen pulse.”) |
| Trending today | Popular workflows (+ “Trending Cookbooks today.”) |
| Kit ready / Cooking / Pantry stocked / New chef | Project complete / In progress / Details added / New member |
| `{name} is cooking` | `{name} is working on` |
| joined the kitchen | joined SousMeow |

### Category C — Replaced (too metaphorical as primary label)

| Before | After |
|--------|-------|
| Great AI results are a recipe, not a lucky prompt | Build complete projects using the AI subscriptions you already have |
| Your sous-chef for AI work | Guided AI workflows · no API required |
| Start cooking free | Start free |
| Browse Cookbooks (nav) | Explore workflows |
| My Kitchen (nav) | My projects |
| Open my Kitchen | Open my projects |
| Nothing on the stove yet | No projects yet |
| Stocking the Pantry (status) | Needs project details |
| Cooking · Recipe N of M (status) | In progress · Step N of M |
| Continue cooking | Continue project |
| Stock the Pantry and start cooking | Save and start first step |
| Back to cooking | Back to project |
| The Cookbook shelf | Explore workflows |
| Start cooking (marketplace CTA) | Start this workflow |
| The Recipes | Workflow steps |
| What the Pantry asks for | Project details required |
| Approve and lock into the kit | Approve response |
| Continue to Recipe N | Continue to step N |
| Pack a fresh kit | Export project (.zip) |
| This shelf is empty (404) | Page not found |
| That pantry is not yours (403) | Access denied |
| We dropped a pan (500) | Something went wrong |
| Welcome back to your kitchen | Welcome back |
| Stock your Pantry first | Add your project details first |
| Cookbook complete! | Workflow complete! |

---

## 3. Homepage hierarchy changes

**Before:**
1. Hero (metaphor-first) + embedded activity dashboard
2. Hero CTAs
3. 6-step loop
4. Featured Cookbook
5. Honesty section
6. Final CTA

**After:**
1. **Hero** — product-first headline, AI subscription value prop, CTAs, terminology callout
2. **How it works** — 4 plain-language stages (metaphor in subtitles only)
3. **Featured workflow** — example Cookbook with step list
4. **Explore workflows** — Marketplace teaser
5. **Community activity** — simulated dashboard (trust signal, not product explanation)
6. **Why SousMeow is different** — honesty / differentiation
7. **Final CTA**

---

## 4. Navigation improvements

| Location | Before | After |
|----------|--------|-------|
| Auth nav primary | My Kitchen | My projects |
| Auth nav secondary | Marketplace | Explore workflows |
| Guest nav CTA | Start cooking | Start free |
| Guest nav link | Marketplace | Explore workflows |
| Footer | Marketplace | Explore workflows |
| Kitchen page title | My Kitchen (no explanation) | My Kitchen + “Your workflows, projects, and exports” |

URLs unchanged (`/kitchen`, `/marketplace`).

---

## 5. Dashboard improvements

Public community dashboard (homepage):
- Primary KPI: **Projects completed** (not “kits packed”)
- Secondary: **Steps approved**, **Creators active**
- KPI cards: Creators, All-time projects, Satisfaction, Workflows
- Heatmap: **Workflow activity**
- Feed: **Recent activity** with plain event copy
- Trending: **Popular workflows**
- Themed subtitles retained as decoration (“Fresh from the kitchen.”, “The kitchen pulse.”)

Kitchen dashboard (signed-in):
- Empty state explains workflow in plain language first
- Status badges use plain labels; Pantry/Cookbook referenced in body copy
- Buttons: “Add project details”, “Continue project”, “Export project”

---

## 6. Microcopy improvements

**Flash messages** restructured to: what happened → what’s next → (personality removed from primary line)

Examples:
- “Project started. First step: add your project details.”
- “Project details saved. The first step is ready.”
- “Workflow complete! Every step approved. Export your Project Kit below.”
- “Account created. Choose a workflow to start your first project.”

**Empty states:**
- Kitchen: “No projects yet” + instructions + optional “The kitchen is ready when you are.”

**Buttons:**
- Copy prompt, Save response and review it, Approve response, Export project, Explore workflows, Start free

**Validation errors:** Unchanged — already plain-language per `COPY_AUDIT.md`.

---

## 7. Mobile review (375px)

Verified structurally:
- Hero headline uses `clamp()` for readable scaling
- 4-step loop uses `auto-fit` grid with 13rem minimum — stacks to single column on narrow screens
- Community dashboard retains horizontal scroll heatmap
- KPI grid stays 2×2 on mobile
- Activity cards use flex layout with avatars — left-aligned, no orphaned bullets
- Marketplace teaser stacks CTA below copy on narrow widths
- Navigation collapses to hamburger (existing behavior)

No important explanatory copy hidden behind mobile-only breakpoints.

---

## 8. Accessibility review

Preserved:
- Semantic HTML (`section`, `header`, `nav`, `main`, `ol`/`ul`)
- Heading hierarchy (h1 → h2 → h3; dashboard h3/h4 nested appropriately)
- `aria-labelledby` on major sections
- `role="progressbar"` on project progress
- `aria-label` on heatmap
- Skip link, focus states (unchanged in components.css)
- Color contrast on dark insights panel (unchanged tokens)
- `prefers-reduced-motion` on live dot animation

Improved:
- Error pages now lead with plain headings (“Page not found” vs metaphor)
- Activity feed badges paired with descriptive sentence copy

---

## 9. Remaining cognitive load (intentional / future)

| Area | Status | Recommendation |
|------|--------|----------------|
| Seed Cookbook recipe titles (“Plate the Landing Page”) | **Updated** | Launch Day Kit steps now use plain titles in seed data |
| Runner rail still says “Recipe” on step labels | Kept | Branded term; user has been taught by this point |
| Breadcrumb “My Kitchen” | Kept | Matches page title; subtitle explains |
| Admin “Workflow” column still shows Cookbook slug data | Kept | Admin audience |
| `docs/COPY_AUDIT.md` references old nav labels | Stale doc | Update separately if desired |
| Marketplace URL still `/marketplace` | By constraint | Label says “Explore workflows” |

---

## First-visitor validation

| Question | Answerable in <10s? |
|----------|---------------------|
| What does this product do? | Yes — guided AI workflows, BYO assistant |
| Who is it for? | Yes — people with AI subscriptions doing complex projects |
| Why would I use it? | Yes — structure, review discipline, finished export |
| How does it work? | Yes — 4 steps on homepage |
| Do I need an API? | Yes — explicitly no |
| Can I use ChatGPT / Claude? | Yes — named in hero |
| What is a Cookbook / Recipe / Pantry / Project Kit? | Yes — terminology callout in hero |
| What do I receive when I finish? | Yes — Project Kit / finished files |

---

*Report generated as part of the SousMeow Theme Refinement mission.*
