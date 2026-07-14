# Mission 001 — Copy Simplification Pass

Editorial pass applying Product Law 002 to public-facing marketing copy.
Layouts, tokens, spacing, and motion were not redesigned. Prefer delete over rewrite.

## Inspection

| Page | Route | File | Status |
| --- | --- | --- | --- |
| Homepage | `/` | `app/Views/marketing/home.php` | Simplified |
| Marketplace | `/marketplace` | `app/Views/marketplace/index.php` | Simplified |
| Cookbook detail | `/cookbooks/{slug}` | `app/Views/marketplace/show.php` | Simplified |
| Categories | `/categories` | `app/Views/categories/index.php` | Simplified |
| Category detail | `/categories/{slug}` | `app/Views/categories/show.php` | Simplified |
| Collections | `/collections/{slug}` | `app/Views/collections/show.php` | Breadcrumb only |
| Layout / nav | sitewide | `app/Views/layout/app.php` | Simplified |
| Discovery card | partial | `app/Views/partials/discovery-card.php` | Meta labels |

**Missing pages (not invented):** About, standalone How It Works, Pricing, other landing pages.

**Ignored (per brief):** Admin, Kitchen, Runner, legal, Product Law doc content.

**Nav before → after (guest):** Categories / Explore workflows → How it works / Find something / Sign in / Start free.

---

## Homepage

### Audit
- Cognitive Load: High (Class-1 terms taught before value)
- Clarity: Medium
- Redundancy: High (hero lede + audience repeated product law)
- Terminology: Cookbook/Recipe/Pantry/Runner/Project Kit before marketplace
- Audience Fit: Medium — talked at product system, not visitor frustration

**Removed:** duplicated audience paragraph; early Cookbook bridge; Class-1 section titles (Pantry/Runner/Project Kit); "Browse the Cookbooks" in first viewport.

**Delayed:** word "Cookbook" until explore shelf (“We call these guides Cookbooks.”)

### Rewrite notes
Arc: problem → relief (your AI) → how it works (plain) → proof (Launch Day demo) → explore (Cookbook intro) → CTA. Real demos kept; surrounding labels plain.

---

## Marketplace

### Audit
- Cognitive Load: Medium
- Terminology: "workflows" before earning Cookbook; honesty line mentioned Runner
- Audience Fit: OK once introduced

### Rewrite notes
Open with earned definition of Cookbook. Meta uses "steps" not "Recipe". Honesty line trimmed; still states portfolio / no checkout / own AI.

---

## Cookbook detail

### Audit
- Class-1: Pantry, Project Kit, Runner, Kitchen, ingredient, provenance
- Long preview / paid disclaimers

### Rewrite notes
Plain "Your information" / "Finished files". CTA: Start this Cookbook. Breadcrumb: Find something. Honesty preserved without machinery jargon.

---

## Categories / collections / cards

### Rewrite notes
"Browse by topic"; lede without Project Kit / ingredients; discovery meta uses "steps".

---

## Cookbook seed descriptions (public marketing ledes)

Simplified `description` (and one `tagline`) on five seed files so detail/marketplace copy no longer teaches Pantry/Recipe before the visitor has chosen a guide. Prompt internals and Runner screens left alone.