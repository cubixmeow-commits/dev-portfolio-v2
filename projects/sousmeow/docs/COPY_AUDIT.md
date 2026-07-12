# SousMeow Copy Audit

Phase 1 and 2 of the copy pass: the voice guide everything must conform
to, then a complete map of user-facing text with a verdict per item.
Verdicts: KEEP (already on-voice), POLISH (right idea, tighten the
words), REWRITE (replace the approach). The rewrite itself is Phase 3;
results are summarized in `COPY_PASS_REPORT.md`.

A note on the brief's page names: this build has no separate Explore,
How It Works, FAQ, Pricing, or About pages. Explore is the Marketplace,
How It Works lives on the home page, pricing and FAQ live on Cookbook
detail pages, and the executable Cookbook is the Launch Day Kit. The
mascot is a single unnamed line-art cat used in a few poses. The audit
covers what exists.

## Voice guide

1. SousMeow sounds like a calm expert chef who has cooked this exact
   dish a hundred times and enjoys teaching it.
2. Warmth comes from clarity, not adjectives. Short sentences. Concrete
   nouns. Say what happens, not what could be.
3. Always answer "what do I do next" before anything else on the screen.
4. Kitchen language is seasoning, not a costume. One cooking word per
   screen is usually enough; the product terms (Kitchen, Cookbook,
   Recipe, Pantry, Ingredient, Project, Project Kit, Quality Check) do
   most of that work already.
5. Honesty is a feature. Limits are stated plainly and early. Nothing
   claims automatic quality evaluation; checks are the user's judgement,
   recorded.
6. Buttons are the verb the user is about to do: "Copy prompt",
   "Approve and lock into the kit". Never "Submit", never "OK".
7. Errors and auth messages stay flat: what went wrong, how to fix it,
   nothing else. No jokes near a failure.
8. The cat appears in art, not in prose. It never talks and it is never
   a chatbot.
9. On-voice: "Paste the whole answer, formatting and all." / "Your
   judgement, recorded." / "Ten minutes to your first kit."
10. Off-voice: "Supercharge your workflow!" / "Oops! Something went
    wrong :(" / "Harness the power of AI to elevate your content."

## Zone 1: Marketing (40% of effort)

`app/Views/marketing/home.php`

| Item | Current text | Verdict |
| --- | --- | --- |
| Hero eyebrow | "Your sous-chef for AI work" | KEEP |
| Hero H1 | "Great AI results are a recipe, not a lucky prompt." | KEEP. Passes the five-second test for "what is different". |
| Hero lede | "SousMeow walks you through proven workflows, one Recipe at a time. You copy a carefully built prompt, run it in the AI you already use, paste the answer back, and approve it against real quality checks. At the end: a finished, publish-ready Project Kit." | REWRITE. Right facts, too long; the concrete outcome should land in sentence one. |
| Hero footnote | "Bring your own AI (Claude, ChatGPT, Gemini, anything). SousMeow never calls one for you, and you can tour the whole product with built-in sample responses." | POLISH |
| Loop heading + sub | "The loop, in one glance" / "Every Cookbook runs the same honest cycle. No magic, no black box, no API key." | KEEP |
| Loop step cards (6) | "Answer a short set of questions about your project, once. These facts feed every prompt." etc. | POLISH. Trim each to its verb and outcome. |
| Featured eyebrow | "Free starter Cookbook" | KEEP |
| Featured body | "Positioning, landing page copy, channel announcements, and a FAQ, exported as one kit. For indie makers and small teams shipping a v1, in about 25 minutes." | REWRITE. Reads like a spec sheet; should read like an afternoon well spent. |
| Honesty section (3 blocks) | "It never calls an AI for you" / "It never grades your work" / "It never loses a version" | KEEP headings, POLISH bodies. |
| Final CTA | "Ten minutes to your first kit" + body | KEEP heading, POLISH body. |
| Meta description | `layout/app.php`: "SousMeow guides you through proven AI workflows, recipe by recipe..." | POLISH |
| Footer note | "SousMeow is a portfolio demonstration build. No payments are collected and no AI is called; you bring your own assistant." | KEEP. Flat on purpose; trust copy. |

## Zone 2: Recipe Runner (25%)

`app/Views/runner/step.php`, `app/Controllers/RunnerController.php`

| Item | Current text | Verdict |
| --- | --- | --- |
| Rail summary | "N of 4 Recipes approved. Approved work goes into your exported Project Kit." | KEEP |
| Step flags | "Copy this prompt" / "Run it in your own AI" / "Paste the response back" | KEEP. These are the spine of the loop. |
| Prompt explainer | "Built from your Pantry just now; the highlighted parts are your ingredients..." | POLISH |
| Run-card body | "Open the assistant you already use (Claude, ChatGPT, Gemini, anything that chats), paste the prompt, and let it cook..." | POLISH |
| Paste-card body | "Paste the whole answer, formatting and all. It is saved exactly as pasted (version 1) and you review it next; nothing goes into your kit unapproved." | KEEP |
| Demo divider + note | "no AI handy?" / "A realistic response for a fictional product (Driftlog), so you can walk the whole loop right now..." | POLISH |
| Checks lede | "SousMeow never grades text for you; you are the chef who tastes the dish..." | KEEP. This is the product's soul. |
| Approve button | "Approve and lock into the kit" | KEEP |
| Approve notes (both states) | "Confirm every check to approve..." / "Approving locks vN into your Project Kit..." | POLISH |
| Revise summaries | "Not quite right? Revise it" / "Edit this response by hand" / "Paste a different response" | KEEP |
| Old-version notice | "You are reading version N, kept exactly as it was saved..." | KEEP |
| Approved banner | "Approved and in the kit" + body | POLISH |
| Sidebar: Ingredients used | "What this Recipe's prompt is built from." | KEEP |
| Sidebar: Version history empty | "No versions yet. The first response you paste becomes v1, kept forever exactly as pasted..." | KEEP. Empty state that teaches. |
| Sidebar: Where this is going | "Approve all 4 Recipes and this Project exports as a Project Kit..." | POLISH |
| Runner flashes (paste, example, approve, revise, restore, order-lock) | see RunnerController | POLISH selectively; success moments deserve one degree more warmth, errors stay flat. |

## Zone 3: Empty states (15%)

| Item | Where | Current | Verdict |
| --- | --- | --- | --- |
| No Projects | `kitchen/index.php` | "No Projects yet, and that is the fun part" + two paragraphs | REWRITE. The heading strains; the body buries the invitation. |
| No exports | `projects/export.php` | "No exports yet" + conditional body | POLISH |
| No search results | `marketplace/index.php` | "No Cookbooks match 'q'" + suggestions | POLISH |
| No versions yet | runner sidebar | (see above) | KEEP |
| Admin empties | `admin/index.php` | "No projects yet... Quiet kitchens are honest kitchens." | KEEP |

## Zone 4: Cookbook copy (10%)

`database/seeds/content.php`, `marketplace/index.php`, `marketplace/show.php`

| Item | Verdict |
| --- | --- |
| Marketplace header ("The Cookbook shelf" + intro) | POLISH |
| Marketplace honesty footer | KEEP. Flat trust copy. |
| Launch Day Kit description | REWRITE. Should make the kit feel worth an afternoon, not list its parts. |
| Launch Day Kit tagline / audience / outcome | POLISH |
| 7 marketplace cookbook taglines + descriptions | POLISH each; two REWRITE (Blog Batch Prep, Job Hunt Mise en Place read flattest). |
| Recipe titles (all) | KEEP. The titled-dish naming is the product's charm. |
| Recipe summaries / why_it_matters / unlocks_text (4 executable) | POLISH. "Why" lines should make the user feel the cost of skipping. |
| Preview recipe summaries (30) | KEEP; spot-POLISH the weakest. |
| Pantry field labels | KEEP (labels are nouns and correct). |
| Pantry field help lines (8) | POLISH. Each should say how the ingredient is used, in fewer words. |
| Quality Check labels + help (12) | KEEP labels, POLISH help lines. |
| Detail page honest states (paid / free coming soon) | KEEP. Deliberately flat. |
| Detail facts ("Made for" / "You leave with" / "Time" / "Requires") | KEEP |

## Zone 5: Success moments (5%)

| Item | Current | Verdict |
| --- | --- | --- |
| Recipe approved flash | "{Title} approved and locked into your kit. Next Recipe unlocked." | POLISH |
| Cookbook complete flash | "Cookbook complete! Every Recipe is approved. Your Project Kit is ready to export." | POLISH |
| Kit packed flash | "Project Kit packed: N files plus a manifest. Download it below." | KEEP |
| Copy confirmation | "Copied ✓" (button swap) | KEEP |
| Export hero | "Your Project Kit is ready" | KEEP |
| Register flash | "Your kitchen is ready. Start your first Cookbook whenever you like." | POLISH |
| Sign-in flash | "Welcome back to your kitchen." | KEEP |

## Zone 6: Everything else (5%)

| Item | Where | Verdict |
| --- | --- | --- |
| Auth page asides (login/register) | `auth/*.php` | POLISH. Good teaching copy, slightly long. |
| Auth validation errors | AuthController | KEEP. Flat by design. One POLISH: the rate-limit line. |
| Pantry validation errors | ProjectController | KEEP flat; POLISH "This ingredient is required." is good, keep. |
| Pantry page lede + sample notes | `projects/pantry.php` | POLISH |
| Error pages 404/419/500/403 | `errors/*.php` | KEEP. Calm, on-voice, actionable. Headings stay. |
| CSRF failure copy | Csrf.php + 419 page | KEEP. Trust copy. |
| Delete confirmations | kitchen + runner data-confirm | KEEP |
| Admin page | `admin/index.php` | KEEP. Deliberately dry. |
| Nav and footer labels | layout | KEEP ("My Kitchen", "Marketplace", "Start cooking", "Sign out") |
| Export page (ready + progress states) | `projects/export.php` | POLISH |
| JS strings | `runner.js` approve-note update | POLISH to match new approve note. |

## Zone 7: The four Demo Mode responses

`database/seeds/content.php` (Driftlog positioning, landing copy,
announcements, FAQ). Verdict: POLISH toward "best writing in the app".
They already read as strong AI-session output; the pass tightens rhythm,
sharpens the weakest lines (landing feature block 2, two FAQ answers),
and keeps every claim consistent with the fictional Pantry.

Note for deployment: these live in the database after seeding. The live
site must re-seed (`php scripts/seed.php --fresh`, destructive) or the
old text stays in place. Flagged in the report.
