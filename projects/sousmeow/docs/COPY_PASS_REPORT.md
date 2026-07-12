# Copy Pass Report

Scope: words only. No features, routes, schema, keys, or logic changed.
Every edit conforms to the voice guide at the top of `COPY_AUDIT.md`.

## What changed, by zone

- **Marketing home**: hero lede rewritten to land the concrete outcome in
  the first sentence; all six loop cards tightened to verb plus outcome;
  featured Cookbook body rewritten from spec-sheet to invitation; honesty
  blocks and final CTA trimmed. Meta description rewritten.
- **Recipe Runner**: prompt explainer, run-card, demo note, approved
  banner, approve notes, and the "Where this is going" sidebar tightened.
  Success flashes warmed one degree ("The next Recipe is ready when you
  are."). The matching JS string in `runner.js` was kept in sync.
- **Empty states**: Kitchen empty state rewritten ("Nothing on the stove
  yet"); export and search empties tightened to teach plus invite.
- **Cookbook copy** (`database/seeds/content.php`): Launch Day Kit
  tagline, description, and outcome rewritten; all four Recipe
  "why it matters" lines sharpened to make skipping feel costly; Pantry
  field help lines shortened to how-the-ingredient-is-used; Blog Batch
  Prep and Job Hunt Mise en Place descriptions rewritten, Cold Outreach,
  Case Study, and UX Microcopy polished.
- **Demo Mode responses**: three surgical upgrades (landing feature
  block 2, the price and offline FAQ answers). The rest was already the
  strongest writing in the app and was deliberately left alone; churn
  there risked making it worse.
- **Auth and Pantry**: asides and ledes trimmed; register flash renamed
  the moment ("Your Kitchen is open.").

## Ten highest-impact strings, before and after

1. Hero lede
   Before: "SousMeow walks you through proven workflows, one Recipe at a time. You copy a carefully built prompt, run it in the AI you already use, paste the answer back, and approve it against real quality checks. At the end: a finished, publish-ready Project Kit."
   After: "SousMeow turns a big writing job into short, guided Recipes. Copy a ready-made prompt, run it in ChatGPT or Claude, paste the answer back, and approve what earns its place. By the last Recipe, you are holding a finished Project Kit, ready to publish."

2. Kitchen empty state heading
   Before: "No Projects yet, and that is the fun part"
   After: "Nothing on the stove yet"

3. Launch Day Kit description
   Before: "Launching is mostly writing: positioning, a landing page, announcement posts, and answers to the questions people will actually ask..."
   After: "Launch day is a writing day: the positioning that explains you, the landing page that sells you, the announcements that bring people in, and honest answers for the questions that follow... What you approve is what you ship: a kit of ready-to-publish files that sound like you on your best day."

4. Positioning Recipe, why it matters
   Before: "Every later Recipe quotes this positioning. Ten minutes here saves an hour of inconsistent copy later, and it is the piece most products skip."
   After: "Every later Recipe quotes this positioning word for word. Ten focused minutes here is why the whole kit will sound like one person wrote it; skipping this step is why most launch copy sounds like four."

5. FAQ Recipe, why it matters
   Before: "Launch day comments decide how your product is perceived. Answering fast and honestly beats answering perfectly..."
   After: "Launch day is mostly answering comments, and answering fast beats answering perfectly. This Recipe prepares the hard questions and your honest answers in advance, so tomorrow you paste instead of scramble."

6. Recipe approved flash
   Before: "{Title} approved and locked into your kit. Next Recipe unlocked."
   After: "{Title} is approved and locked into your kit. The next Recipe is ready when you are."

7. Cookbook complete flash
   Before: "Cookbook complete! Every Recipe is approved. Your Project Kit is ready to export."
   After: "Cookbook complete! Every Recipe approved, nothing left to cook. Pack your Project Kit below."

8. Approve gate note
   Before: "Confirm every check to approve."
   After: "The approve button wakes up when every check is confirmed."

9. Job Hunt Mise en Place tagline
   Before: "Everything prepped before the applications start."
   After: "Prep everything once. Then apply fast, everywhere."

10. Blog Batch Prep description
    Before: "Batch the thinking, then batch the drafting. A topic pantry built from questions your audience already asks..."
    After: "The blog you keep meaning to write dies in the gap between 'I should post' and 'about what?' This Cookbook closes the gap... You leave with a month of publishable posts and a backlog for the month after."

## Deliberately left flat

- All validation and error messages, the 419/500 copy, and the CSRF
  wording: failures get facts and a next step, not personality.
- Auth rate-limit and credential errors: trust copy.
- The footer disclosure and both marketplace honesty notes ("purchases
  are not open yet"): these earn credibility by being plain.
- The admin page: it is a tool, and it reads like one.
- Recipe titles and all product terminology: unchanged everywhere.

## Unsure / judgement calls

- "The approve button wakes up when every check is confirmed" is the
  most personality any instruction carries; it stayed because it explains
  a disabled button better than a flat sentence did. Easy to revert.
- "cancel whenever" was added to the fictional Driftlog FAQ answer. It
  is sample marketing copy for a fictional product, not a SousMeow claim.
- The four Demo Mode responses received only three small edits by
  design; the audit judged wholesale rewriting a net risk.

## Deployment note (important)

Seed content lives in the database after seeding. The copy in
`database/seeds/content.php` reaches a running site only after a
re-seed. On the live server:

```sh
php scripts/seed.php --fresh    # DESTRUCTIVE: drops all tables,
                                # including accounts and projects
```

Fine right after launch; do not run it once real users have projects.

## Verification

- `php -l` clean across every modified PHP file.
- `grep -rn "—"` over the project: no em dashes.
- Fresh re-seed plus the full scripted demo path (register, Pantry,
  four Recipes with Demo Mode, checks, approvals, export, download):
  all green.
- 390px browser pass over home, empty Kitchen, Pantry, and the Runner:
  no horizontal overflow, no broken buttons or cards.
