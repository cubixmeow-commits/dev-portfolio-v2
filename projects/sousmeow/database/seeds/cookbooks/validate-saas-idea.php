<?php

declare(strict_types=1);

$problemValidationBriefExample = <<<'MD'
## Problem statement (sharpened)

Shopify merchants who sell through wholesale and marketplace channels receive supplier invoices that rarely line up with fulfilled orders and payout records on the first pass. The mismatch shows up at month-end close, when the ops lead exports orders, downloads PDF invoices from three supplier portals, and spends four to six hours in Google Sheets matching line items by SKU and ship date. Errors are caught late: duplicate charges, quantity drift, and freight billed twice. The pain is not "accounting is hard," it is "we only find supplier billing mistakes after the money is gone."

## Who feels it first

The founder-operator or operations manager at a Shopify store doing roughly $500K to $3M in annual revenue, with 50 or more SKUs and at least one wholesale or Amazon channel. They own inventory, supplier relationships, and month-end close hygiene. The bookkeeper receives cleaned-up exports; they do not live inside supplier portals daily.

## Current workaround and its cost

Today the workaround is manual reconciliation in Google Sheets: Shopify order export, supplier invoice PDFs, VLOOKUP by SKU, color coding for exceptions, Slack messages to the bookkeeper for anything ambiguous. It works until volume grows. Cost is measured in hours every month-end plus delayed detection of overcharges. Founder insight from a prior DTC brand: $14K in duplicate supplier invoices went unnoticed for one quarter because reconciliation happened only at tax prep.

## What would change if the problem vanished

Invoices would auto-match to fulfilled orders and payout lines within 48 hours of receipt. Exceptions would surface as a short queue with suggested fixes, not a Friday spreadsheet marathon. Month-end close would shrink from a half-day reconciliation sprint to a 20-minute exception review.

## Validation questions to answer next

1. How often does reconciliation happen today (weekly, month-end only, quarterly)?
2. What is the typical exception rate per 100 invoices (matched first pass vs manual)?
3. Would the buyer pay $79/month if it reliably caught one duplicate invoice per quarter?
MD;

$painFrequencySnapshotExample = <<<'MD'
## Pain frequency map

| Trigger moment | How often | Time cost | Business risk |
|----------------|-----------|-----------|---------------|
| Month-end close reconciliation | Every month | 4-6 hours | Late discovery of supplier overcharges |
| New supplier onboarding | 2-4 times per year | 2 hours setup per supplier | Wrong cost basis on new SKUs |
| Wholesale order spike (seasonal) | 2-3 peaks per year | +8 hours that week | Quantity mismatch on high-volume POs |
| Bookkeeper handoff | Every month | 1 hour cleanup | Exceptions buried in Slack threads |
| Tax prep / audit pull | Once per year | 12+ hours reconstruction | Missed credits from prior quarters |

## Severity scorecard

- **Frequency:** High for core reconciliation (monthly, non-optional). Medium for exception handling (weekly during growth periods).
- **Intensity:** 7/10. Not a daily fire, but month-end is reliably stressful and errors have direct dollar impact.
- **Switching urgency:** Moderate. Workaround exists; pain spikes when SKU count or channel count grows.

## Evidence gaps (do not invent numbers)

The Pantry does not state current exception rate or exact hours logged. Treat any quantified claim above as a hypothesis until 5 buyer interviews confirm:
- Hours spent per close cycle
- Number of supplier invoices per month
- Dollar value of mismatches found in the last 90 days

## Interview prompts to fill gaps

1. "Walk me through the last month-end close. Where did reconciliation sit on the calendar?"
2. "Of your last 20 supplier invoices, how many matched on the first pass without manual edits?"
3. "Tell me about the last billing mistake you caught. How long had it been sitting?"
MD;

$idealCustomerProfileExample = <<<'MD'
## ICP snapshot

**Title:** Operations Manager (sometimes Founder wearing ops hat)
**Company:** Shopify-native DTC or hybrid wholesale brand
**Revenue band:** $500K-$3M annual GMV
**Catalog:** 50+ active SKUs, 3+ core suppliers
**Channels:** Shopify storefront plus at least one of wholesale portal, Amazon, or Faire

## Firmographics

- 5-25 employees; finance is outsourced or part-time bookkeeper
- Uses Shopify Plus or advanced Shopify with inventory apps
- Suppliers invoice via email PDF and/or portal download (not EDI)
- Already pays for at least one finance adjacent tool (A2X, Bookkeep, or Gusto + QBO stack)

## Psychographics

- Prides themselves on "knowing our unit economics" but hates spreadsheet heroics
- Skeptical of "AI accounting" pitches; trusts tools that show their work
- Will trial software that saves a visible month-end hour block before asking finance to approve

## Disqualifiers (who this is not for)

- Pre-revenue stores with fewer than 20 SKUs (pain exists but frequency is too low)
- Brands on fully integrated EDI with suppliers (problem is already solved upstream)
- Merchants who reconcile only at year-end via bookkeeper (buyer is not the user)

## Day-in-the-life trigger

Tuesday after month-end: ops lead has 14 unmatched invoice lines flagged in Slack, bookkeeper asks for cleaned CSV, and a wholesale PO from last week still lacks freight allocation. That is the moment they search for "Shopify invoice reconciliation."

## ICP litmus test

Would this person recognize themselves in the sharpened problem statement and feel the monthly close row in the pain frequency map? If yes, they are in-segment.
MD;

$buyerAccessPlanExample = <<<'MD'
## Primary access path: Shopify Community and forums

**Channel chosen in Pantry:** Shopify Community and forums

### Where to show up
- Shopify Community > Shopify Discussion > Retail and wholesale threads
- Search existing threads for "reconciliation," "supplier invoice," "month end," "COGS"
- Post one new diagnostic question per week in relevant subforums

### Approach
Ask workflow questions, not product pitches. Example opener: "How are you matching supplier invoices to fulfilled wholesale orders today?" Follow up with commenters who describe spreadsheet workflows matching your ICP.

### Weekly targets (qualitative)
- 3 substantive thread replies per week for 4 weeks
- 2 new DM conversations with merchants who match ICP disqualifier rules
- Book 1 workflow teardown call per week from Community interactions

### Conversion signals
- DMs requesting a template or asking what tools others use
- Thread replies describing 50+ SKU stores with wholesale channels
- Accepted calendar invite for a 20-minute workflow screen-share

## Secondary paths (if primary stalls)

- LinkedIn outreach to ops managers at Shopify brands (10 connection requests per week)
- E-commerce operator Slack groups: ask for workflow shares, no pitch
- Warm intro via bookkeepers who serve Shopify merchants: offer to document reconciliation SOP in exchange for one client intro

## Who to avoid

- Generic "Shopify entrepreneurs" groups dominated by beginners (wrong revenue band)
- Accounting firms as first buyer (they are a partnership channel later, not interview source)

## 2-week outreach calendar

| Week | Mon-Tue | Wed-Thu | Fri |
|------|---------|---------|-----|
| 1 | Post 1 Community question | Reply to 3 threads + follow DMs | Log responses, book 2 calls |
| 2 | Post follow-up thread summary | Reply to 3 threads + 1 LinkedIn batch | Synthesize patterns doc |

## Success metric for access (from Pantry)

**Success signal:** 5 paid pilots from Shopify merchants who complete two reconciliation cycles without churning. Access plan goal before building: 15 qualified conversations, 8 workflow teardown calls, 5 willing to pilot when MVP exists.
MD;

$competitorLandscapeExample = <<<'MD'
## Competitive set (from Pantry, expanded honestly)

| Name | What they optimize | Overlap with LedgerLoop | Gap |
|------|-------------------|-------------------------|-----|
| Bookkeep.com | E-commerce bookkeeping automation, categorization, COGS sync | Same buyer, same Shopify stack | Focuses on books, not supplier invoice-to-order line matching |
| A2X | Amazon/Shopify payout to accounting mapping | Same merchant, month-end pain | Payout settlement, not supplier AP reconciliation |
| Ramp | Corporate cards + receipt matching | "Match documents" mental model | Employee spend, not supplier wholesale invoices |
| Bookkeeper retainer | Human reconciliation | Solves problem today | Does not scale, no exception queue software |

## Category placement

LedgerLoop sits between inventory-aware ops tools and accounting automation: **supplier invoice reconciliation for multi-channel Shopify merchants.** It is not a general ledger, not a card program, not full outsourced bookkeeping.

## Switching behavior

- **Do nothing (Sheets):** Default incumbent. Zero monthly fee, high labor cost.
- **Bookkeep + bookkeeper:** Common at $500K-$3M band. LedgerLoop must complement, not replace, QBO sync.
- **Hire more ops help:** Alternative "solution" when pain spikes; software must beat half a day per month.

## Competitive risks

1. Bookkeep adds supplier invoice ingestion (platform expansion risk)
2. Shopify ships native AP features (platform risk, low near-term for wholesale edge cases)
3. Merchants downgrade pain and keep Sheets (status quo risk)

## Unknowns (do not invent market share)

Pantry lists competitors by name only. Market share, pricing tiers, and feature depth beyond public positioning are **unverified** until checked on each vendor's site and 3 user interviews.
MD;

$differentiationHypothesisExample = <<<'MD'
## Differentiation thesis

LedgerLoop wins if it matches **supplier invoice line items to fulfilled Shopify orders and wholesale PO lines** with an auditable exception queue, faster and more accurately than a Google Sheet, without requiring merchants to change accounting systems.

## Wedge (first believable claim)

"See every supplier invoice line matched or flagged within 48 hours of upload, with a clickable trail from invoice to order to shipment."

## Why incumbents miss this wedge

- **Bookkeep** automates categorization and COGS from Shopify data; it does not foreground supplier PDF ingestion and SKU-level exception resolution for ops.
- **A2X** owns payout reconciliation for marketplaces; supplier AP is a different document flow.
- **Ramp** solves employee spend; supplier invoices arrive from email and portals, not cards.

## Founder insight as moat seed

Prior ops lead saw $14K duplicate supplier invoices missed because reconciliation was quarterly. LedgerLoop's UX should optimize for **catching duplicates and quantity drift before close**, not just exporting journal entries.

## Differentiation tests ( falsifiable )

1. In 5 workflow teardown calls, buyers say invoice-to-order matching is a distinct step from "bookkeeping."
2. Pilot users resolve 80%+ of lines automatically on first upload (hypothesis, not fact yet).
3. At $79/month, one caught duplicate invoice per quarter covers annual cost (buyer math, verify in interviews).

## What we will not claim

- Replacing QuickBooks or Bookkeep
- Full AP automation for EDI-heavy suppliers
- Guaranteed fraud detection or tax advice
MD;

$riskiestAssumptionsRegisterExample = <<<'MD'
## Assumption stack (ranked by risk)

| Rank | Assumption | Why it is risky | Category |
|------|------------|-----------------|----------|
| 1 | Merchants receive enough supplier PDF invoices monthly to justify $79/month | Low volume stores may reconcile quarterly | Demand |
| 2 | Shopify order and fulfillment data contains fields needed to match invoice lines | Wholesale and 3PL flows may lack stable identifiers | Feasibility |
| 3 | Ops manager is buyer and daily user; bookkeeper is influencer only | Finance may block tools that touch AP | Buyer |
| 4 | 48-hour auto-match is achievable without custom ML per merchant | SKU aliases and pack sizes break naive matching | Feasibility |
| 5 | Bookkeep/A2X users will add LedgerLoop instead of waiting for vendor roadmap | Incumbent expansion risk | Competition |
| 6 | Merchants will upload supplier invoices to a third-party tool | Security and habit friction | Channel |
| 7 | One duplicate caught per quarter justifies price | ROI story may be too weak for some bands | Pricing |
| 8 | LinkedIn + Shopify Community yields 15 qualified conversations in 4 weeks | Reach channel may be slow | Growth |

## Top 3 to test first

1. **Invoice volume and pain frequency** (Assumption 1) - kills idea if median merchant has <10 invoices/month and reconciles quarterly without dollar impact.
2. **Match feasibility on real documents** (Assumptions 2 and 4) - kills build if pilot merchants need >50% manual mapping rules upfront.
3. **Buyer vs bookkeeper authority** (Assumption 3) - kills GTM if every deal requires bookkeeper firm approval before trial.

## Assumptions treated as facts today (flagged)

- $14K duplicate invoice story is founder anecdote, not LedgerLoop customer data
- $79/month price is hypothesis only
- Competitor feature gaps are from public positioning, not product teardowns
MD;

$assumptionEvidenceMatrixExample = <<<'MD'
## Evidence matrix

| Assumption | Evidence we have | Evidence we need | Test method | Pass threshold | Fail threshold |
|------------|------------------|------------------|-------------|----------------|----------------|
| Monthly invoice volume justifies subscription | Pantry workaround describes monthly close pain | Median invoices/month from 8 interviews | Workflow teardown calls | >=20 invoices/month median | <8 invoices/month and quarterly close OK |
| Shopify data supports line matching | Problem brief cites order export workflow | Screen-share of 3 real invoice + order pairs | Pilot doc upload test | 70% lines match with default rules | <40% auto-match after 2 hours config |
| Ops manager is economic buyer | ICP names ops role | Ask "who signs $79/month tools?" on every call | Interview script Q5 | 5/8 calls say ops/founder decides | Finance gate on every deal |
| Upload habit sticks | None | Pilot retention on 2 cycles | Concierge MVP | 5/5 pilots upload 2nd batch | <3/5 return after week 1 |
| ROI at $79/month | Price hypothesis only | Ask "what duplicate cost last year?" | Interview + pilot survey | 3/5 cite >=$300/year leakage | Majority say Sheets is fine |
| Reach channel works | Buyer access plan drafted | Log outreach metrics 4 weeks | Outreach sprint | 15 qualified conversations | <6 qualified conversations |
| Bookkeep coexistence | Differentiation thesis | Ask current stack on calls | Interviews | Majority use QBO + Bookkeep/A2X, open to add-on | "One tool only" preference |
| 48-hour match SLA | None | Measure pilot processing time | Concierge MVP | 80% batches under 48h manual-assisted | Cannot meet with human ops at scale |

## Current evidence grade

- **Strong:** Problem definition and workaround description (Pantry + sharpened brief)
- **Weak:** Quantitative pain, auto-match rate, willingness to pay
- **Missing:** Any LedgerLoop pilot data (product does not exist yet)

## Next 10 days: evidence collection priorities

1. Complete 8 workflow teardown calls (fills volume, buyer, ROI columns)
2. Run manual match test on 30 invoice lines from 2 merchants (feasibility)
3. Start outreach sprint per buyer access plan (channel)
MD;

$killCriteriaExample = <<<'MD'
## Stop rules (idea dies if...)

1. **Volume fail:** Median interviewed merchant processes fewer than 8 supplier invoices per month AND reports less than 2 hours/month reconciliation pain.
2. **Feasibility fail:** Manual match test achieves under 40% line auto-match on two real merchants after reasonable SKU mapping, without heroic per-client engineering.
3. **Buyer fail:** In 8 interviews, fewer than 3 merchants say ops or founder can approve $79/month software without bookkeeper firm sign-off.
4. **WTP fail:** Zero of 5 pilot-ready merchants agree to $79/month after seeing concierge results (or equivalent LOI for pilot).
5. **Channel fail:** After 4 weeks of documented outreach, fewer than 6 qualified conversations with in-segment ICP.

## Revise rules (pivot scope if...)

1. **Segment revise:** Pain is real but concentrated in Amazon-only sellers, not wholesale Shopify - narrow ICP to FBA hybrid merchants.
2. **Product revise:** Matching works but upload friction dominates - pivot to email-forward ingestion only, drop portal integrations from MVP.
3. **Price revise:** Merchants will pay but not at $79 - revise to $39 lightweight tier or per-invoice pricing if 5+ buyers say subscription is blocker.
4. **Position revise:** Buyers treat this as bookkeeper feature - sell through accounting firm partners instead of direct ops GTM.

## Continue rules (green light to build MVP if...)

1. At least 5 of 8 interviews confirm >=20 invoices/month and >=3 hours/month reconciliation.
2. Manual match test hits >=70% auto-match on two merchants' sample sets.
3. At least 5 merchants commit to paid pilot at $79/month (or signed LOI) before code complete.
4. Success signal from Pantry remains achievable by decision deadline: 5 pilots completing two cycles.

## Decision hygiene

Evaluate kill/revise/continue only after evidence matrix cells have **real interview or test data**, not AI-invented percentages. If decision deadline (March 15, 2026) arrives with empty cells, default decision is **revise** (extend discovery), not **go**.
MD;

$mvpScopeRecommendationExample = <<<'MD'
## MVP definition (concierge-first)

**Goal:** Prove auto-match rate and upload habit for wholesale-hybrid Shopify merchants, not build a full AP platform.

### In scope (v1)

1. **Invoice ingest:** Email forward to unique address + manual PDF upload UI
2. **Data sources:** Shopify order export CSV + fulfilled order API read (orders, line items, SKUs)
3. **Matching engine:** Rule-based SKU + quantity + ship date window (configurable per supplier)
4. **Exception queue:** Unmatched lines with side-by-side invoice snippet and candidate orders
5. **Export:** CSV of matched pairs for bookkeeper import to QBO (no direct GL posting)

### Out of scope (explicitly deferred)

- Supplier portal scraping or EDI
- Bookkeep/A2X bi-directional sync
- Multi-entity consolidation
- Automated payment execution
- Mobile app

## Build vs concierge split

| Component | MVP approach | Why |
|-----------|--------------|-----|
| Matching | Semi-automated script + human review behind curtain | De-risk Assumption 4 before scaling |
| Onboarding | Founder-led 45-min setup call | Learn mapping rules per supplier |
| Auth | Magic link, single workspace | Speed over SSO |

## Effort estimate

- 3-week eng slice for ingest + queue + export
- Parallel 4-week discovery sprint (calls + outreach)
- Do not commit eng beyond exception queue until manual match test passes kill criteria

## MVP success metrics (tie to Pantry)

- 5 paid pilots at $79/month
- Each pilot completes **two reconciliation cycles** (upload batch, resolve exceptions, export)
- Target >=70% lines auto-matched by cycle 2 (measure, do not assume)
MD;

$validationExperimentPlanExample = <<<'MD'
## Experiment backlog (ordered)

### Experiment 1: Workflow teardown interviews
- **Hypothesis:** In-segment merchants spend >=3 hours/month on supplier invoice matching.
- **Method:** 8 calls, screen-share current Sheet/process (script from pain-frequency snapshot).
- **Duration:** Weeks 1-2
- **Owner:** Founder
- **Success:** 5/8 confirm hours + invoice volume thresholds from kill criteria
- **Output:** Updated evidence matrix column "Evidence we have"

### Experiment 2: Manual match feasibility
- **Hypothesis:** 70%+ invoice lines can auto-match with rule-based SKU mapping.
- **Method:** Collect 30 invoice lines each from 2 merchants; run spreadsheet/script matching; log exceptions.
- **Duration:** Week 2
- **Success:** Pass/feasibility threshold in kill criteria
- **Output:** Match rate report attached to differentiation hypothesis

### Experiment 3: Outreach sprint
- **Hypothesis:** Shopify Community + LinkedIn yields 15 qualified conversations in 4 weeks.
- **Method:** Execute buyer access plan calendar; log every reply and ICP fit.
- **Duration:** Weeks 1-4
- **Success:** >=15 qualified, >=8 teardown calls booked
- **Output:** Pipeline table with source channel

### Experiment 4: Concierge paid pilot
- **Hypothesis:** Merchants will pay $79/month before full product exists if concierge delivers matched export within 48 hours.
- **Method:** Offer "LedgerLoop Concierge" - forward invoices, receive exception queue + CSV within 48h; charge via Stripe.
- **Duration:** Weeks 3-6
- **Success:** 5 pilots, 2 completed cycles each (Pantry success signal)
- **Output:** Retention log and match rate per cycle

## Timeline to decision deadline

**Decision deadline from Pantry:** March 15, 2026

| Date window | Milestone |
|-------------|-----------|
| By Feb 28 | Interviews + manual match test complete |
| By Mar 7 | Outreach metrics evaluated against kill criteria |
| By Mar 14 | 5 pilot commitments or documented fail |
| Mar 15 | Go / revise / stop memo signed |

## Resources

- Founder time: ~15 hours/week (calls, concierge, outreach)
- Budget: $0 ads; optional $500 bookkeeper intro thank-you gift cards
- Tools: Calendly, Stripe payment link, shared Google Drive for invoice samples (merchant consent required)
MD;

$goReviseStopMemoExample = <<<'MD'
## Executive summary

LedgerLoop targets Shopify merchants ($500K-$3M GMV) who reconcile supplier invoices to fulfilled orders manually at month-end. Discovery is incomplete until interview and match-test data replace hypotheses. This memo records the decision framework; **final call requires evidence matrix cells filled with real results**, not projected pass rates.

## Evidence snapshot (as of draft)

| Area | Status | Notes |
|------|--------|-------|
| Problem clarity | Green | Sharpened brief and pain map align with Pantry |
| Buyer access plan | Yellow | Plan exists; outreach not yet executed |
| Competitive wedge | Yellow | Thesis plausible; needs user language validation |
| Feasibility | Red | No manual match test results yet |
| Willingness to pay | Red | No pilots billed |
| Channel | Red | No conversation log |

## Decision options

### GO (build MVP)
**Criteria met:** Kill criteria continue rules satisfied with documented data; 5 pilot commitments at $79/month; manual match >=70%; outreach >=15 qualified conversations.

**If GO:** Build scoped MVP (ingest, rule match, exception queue, CSV export); start with concierge-assisted matching; target two reconciliation cycles per pilot before scaling eng.

### REVISE (recommended if deadline hits with yellow/red cells)
**Trigger:** Partial validation - pain confirmed but match rate or WTP weak.
**Revision paths:** Narrow to Amazon-hybrid segment; lower price tier; partner-led GTM through bookkeepers; email-only ingest MVP.

**If REVISE:** Extend discovery 4 weeks with one explicit pivot from kill criteria revise rules; do not write production code until feasibility cell turns green.

### STOP (kill idea)
**Trigger:** Any stop rule in kill criteria fires with verified data (low volume, <40% match, finance gate on all deals, channel fail, zero WTP).

**If STOP:** Archive learnings; do not sunk-cost into AP features; optionally productize interview insights as a reconciliation SOP template for community credibility.

## Preliminary recommendation

**REVISE (pending evidence)** - Problem framing is coherent and differentiated on paper, but feasibility and willingness to pay are unproven. Default action before March 15, 2026: run Experiments 1-3 immediately; only upgrade to GO if Experiment 4 secures 5 paid pilots completing two cycles.

## Signed decision

- [ ] GO  - Build MVP per scope recommendation
- [x] REVISE - Extend validation sprint (specify pivot): ___________________
- [ ] STOP - Archive project

**Decision owner:** Founder
**Review date:** March 15, 2026 (Pantry decision deadline)
MD;

return [
    'slug'                => 'validate-saas-idea',
    'title'               => 'Validate a SaaS Idea',
    'tagline'             => 'Turn a fuzzy B2B hunch into evidence, experiments, and a go/revise/stop call.',
    'description'         => "Most SaaS ideas fail quietly because founders build before they test. This Cookbook runs a structured validation sprint: sharpen the problem, map the buyer, stress the market, rank your riskiest assumptions, and design experiments that produce real evidence. Stock the Pantry once with what you actually know today. Every Recipe prompt is told not to invent facts, market sizes, or interview quotes. What you approve chains forward until the final memo forces an honest go, revise, or stop decision before you write production code.",
    'category'            => 'Research',
    'audience'            => 'Founders and small teams validating a B2B SaaS idea before building',
    'outcome'             => 'validation brief, ICP, competitor map, assumption stress test, MVP scope, experiment plan, and go/revise/stop memo',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'teal',
    'difficulty'          => 'Advanced',
    'est_minutes'         => 180,
    'demo_completed_runs' => 312,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 2,
    'stages' => [
        ['title' => 'Frame the Bet', 'summary' => 'Sharpen the problem and map how often it hurts.'],
        ['title' => 'Know the Buyer', 'summary' => 'Define who buys and how you will reach them.'],
        ['title' => 'Map the Market', 'summary' => 'Place competitors and articulate your wedge.'],
        ['title' => 'Stress the Assumptions', 'summary' => 'Rank risks, gather evidence rules, and set kill criteria.'],
        ['title' => 'Decide and Test', 'summary' => 'Scope the MVP, plan experiments, and record the decision.'],
    ],
    'fields' => [
        [
            'field_key'    => 'idea_name',
            'label'        => 'Idea name',
            'type'         => 'text',
            'help'         => 'Working name for the product concept. Recipes use it as a label, not a claim it exists in market.',
            'placeholder'  => 'e.g. LedgerLoop',
            'sample_value' => 'LedgerLoop',
        ],
        [
            'field_key'    => 'problem_statement',
            'label'        => 'Problem statement',
            'type'         => 'textarea',
            'help'         => 'What goes wrong today, for whom, and what it costs them in time or money. Two to four honest sentences.',
            'placeholder'  => 'Describe the pain in plain language.',
            'sample_value' => 'Shopify merchants with wholesale and marketplace orders struggle to match incoming supplier invoices to fulfilled orders and payout records. Mismatches surface at month-end close, costing hours in spreadsheet work and sometimes missed overcharges.',
        ],
        [
            'field_key'    => 'target_buyer',
            'label'        => 'Target buyer',
            'type'         => 'textarea',
            'help'         => 'Role, company type, and revenue band if known. Say what you are guessing.',
            'placeholder'  => 'Who feels this pain first and would pay to fix it?',
            'sample_value' => 'Operations manager or founder-operator at a Shopify store doing $500K-$3M annually with 50+ SKUs and at least one wholesale or Amazon channel.',
        ],
        [
            'field_key'    => 'current_workaround',
            'label'        => 'Current workaround',
            'type'         => 'textarea',
            'help'         => 'What they do today instead of using your product. Include tools and manual steps.',
            'placeholder'  => 'Spreadsheets, agencies, hacks, etc.',
            'sample_value' => 'Export orders from Shopify, download invoices from supplier portals, match manually in Google Sheets with VLOOKUP and color coding, escalate edge cases to the bookkeeper.',
        ],
        [
            'field_key'    => 'founder_insight',
            'label'        => 'Founder insight',
            'type'         => 'textarea',
            'help'         => 'Why you believe this problem is real. Personal experience, customer conversations, or domain exposure. Label anecdotes as yours, not market facts.',
            'placeholder'  => 'What do you know that outsiders might miss?',
            'sample_value' => 'Previously ran ops at a DTC brand that lost $14K to duplicate supplier invoices in one quarter because nobody reconciled until tax prep.',
        ],
        [
            'field_key'    => 'known_competitors',
            'label'        => 'Known competitors',
            'type'         => 'textarea',
            'help'         => 'One name per line. Include do-nothing and manual processes if relevant. Recipes will not invent competitors beyond this list.',
            'placeholder'  => "One competitor or alternative per line",
            'sample_value' => "Bookkeep.com (e-commerce bookkeeping automation)\nA2X (Amazon/Shopify accounting sync)\nRamp (corporate cards with receipt matching)\nManual bookkeeper retainers",
        ],
        [
            'field_key'    => 'price_hypothesis',
            'label'        => 'Price hypothesis (USD/month)',
            'type'         => 'number',
            'help'         => 'What you would charge if the product existed. Recipes treat this as a hypothesis to test, not validated WTP.',
            'placeholder'  => '79',
            'sample_value' => '79',
        ],
        [
            'field_key'    => 'reach_channel',
            'label'        => 'Primary reach channel',
            'type'         => 'select',
            'help'         => 'Where you will find buyers for validation interviews and pilots. The access plan Recipe builds from this choice.',
            'options'      => [
                'Shopify Community and forums',
                'LinkedIn outreach',
                'Cold email to ops managers',
                'E-commerce operator Slack or Discord',
                'Accounting firm partnerships',
            ],
            'sample_value' => 'Shopify Community and forums',
        ],
        [
            'field_key'    => 'success_signal',
            'label'        => 'Success signal',
            'type'         => 'text',
            'help'         => 'One measurable outcome that would prove validation succeeded. Be specific enough to falsify.',
            'placeholder'  => 'e.g. 5 paid pilots completing two onboarding cycles',
            'sample_value' => '5 paid pilots from Shopify merchants who complete two reconciliation cycles without churning',
        ],
        [
            'field_key'    => 'decision_deadline',
            'label'        => 'Decision deadline',
            'type'         => 'text',
            'help'         => 'When you will force a go/revise/stop call. Recipes anchor experiment timelines to this date.',
            'placeholder'  => 'e.g. March 15, 2026',
            'sample_value' => 'March 15, 2026',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'problem-validation-brief',
            'title'            => 'Problem Validation Brief',
            'summary'          => 'Sharpen the problem, buyer pain, and the validation questions worth answering.',
            'why_it_matters'   => 'Every later Recipe quotes this brief as ground truth. A vague problem statement is why founders run twelve interviews and still cannot explain what they are testing.',
            'unlocks_text'     => 'Approve it and the Pain Frequency Snapshot Recipe starts with a shared definition of the bet.',
            'est_minutes'      => 14,
            'prompt_template'  => <<<'TXT'
You are a SaaS validation coach who writes analytically. Using only the Pantry facts below, produce a problem validation brief. Do not invent market sizes, customer counts, survey results, or competitor features.

Idea name: {{idea_name}}
Problem statement: {{problem_statement}}
Target buyer: {{target_buyer}}
Current workaround: {{current_workaround}}
Founder insight (anecdote, not market data): {{founder_insight}}

Produce Markdown with these exact section headings:

## Problem statement (sharpened)
Rewrite the problem in plain language: trigger moment, who feels it, cost in time or money. No hype adjectives.

## Who feels it first
Two or three sentences on the buyer role and company context. Mark anything inferred as inference.

## Current workaround and its cost
Describe the workaround chain and why it breaks under growth. Quote founder insight only as founder anecdote.

## What would change if the problem vanished
Concrete outcome state, not feature list.

## Validation questions to answer next
Three falsifiable questions the next Recipes should help answer. No invented answers.

Under 400 words. If a fact is missing, say "unknown" rather than guessing.
TXT,
            'example_response' => $problemValidationBriefExample,
            'checks' => [
                ['label' => 'Problem is specific to a moment', 'help' => 'You can picture the Tuesday when reconciliation breaks, not a generic "inefficiency" claim.'],
                ['label' => 'Founder insight stays anecdotal', 'help' => 'Personal stories are labeled as yours, not presented as industry statistics.'],
                ['label' => 'Questions are falsifiable', 'help' => 'Each validation question could fail based on interview answers.'],
                ['label' => 'No invented TAM or metrics', 'help' => 'Nothing claims market size, churn rates, or survey percentages not in your Pantry.'],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'pain-frequency-snapshot',
            'title'            => 'Pain Frequency Snapshot',
            'summary'          => 'Map when the pain fires, how often, and what evidence you still need.',
            'why_it_matters'   => 'A painful problem that fires once a year is a different business than one that fires every month-end. This snapshot stops you from building for imaginary urgency.',
            'unlocks_text'     => 'Approve it and Stage 2 (Know the Buyer) uses your frequency map to shape the ICP.',
            'est_minutes'      => 16,
            'prompt_template'  => <<<'TXT'
You are a SaaS validation coach. Using the Pantry facts and approved brief below, map pain frequency. Do not invent statistics, hours saved, or dollar amounts. Mark hypotheses explicitly.

Idea name: {{idea_name}}
Target buyer: {{target_buyer}}
Current workaround: {{current_workaround}}

Approved problem validation brief (ground truth):
{{artifact:problem-validation-brief}}

Produce Markdown with these exact section headings:

## Pain frequency map
A table with columns: Trigger moment, How often, Time cost, Business risk. Use qualitative frequency (daily, weekly, monthly) unless Pantry provides numbers.

## Severity scorecard
Short subsections: Frequency, Intensity (1-10 with one-line rationale), Switching urgency. Justify scores from the brief only.

## Evidence gaps (do not invent numbers)
List what you still need to learn from buyers. Call out any placeholder metrics that must not be treated as facts.

## Interview prompts to fill gaps
Three questions a founder could ask on a teardown call to replace hypotheses with evidence.

Under 450 words. Never fabricate interview quotes or survey results.
TXT,
            'example_response' => $painFrequencySnapshotExample,
            'checks' => [
                ['label' => 'Month-end close appears if relevant', 'help' => 'For finance/ops pains, the snapshot names when in the calendar the pain spikes.'],
                ['label' => 'Gaps are named honestly', 'help' => 'Missing invoice counts or hours are listed as gaps, not filled with plausible numbers.'],
                ['label' => 'Table rows match your workaround', 'help' => 'Trigger moments trace to steps in your Pantry workaround, not generic SaaS pains.'],
                ['label' => 'Interview prompts are usable verbatim', 'help' => 'You could paste each prompt into a Calendly call agenda without editing.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'ideal-customer-profile',
            'title'            => 'Ideal Customer Profile',
            'summary'          => 'Define who is in-segment, who is out, and how they recognize the problem.',
            'why_it_matters'   => 'Broad ICPs produce noisy interviews. Tight disqualifiers save weeks of conversations with merchants who will never pay for reconciliation software.',
            'unlocks_text'     => 'Approve it and the Buyer Access Plan Recipe targets this ICP on your chosen channel.',
            'est_minutes'      => 15,
            'prompt_template'  => <<<'TXT'
You are a B2B SaaS segmentation analyst. Build an ICP using Pantry facts and approved artifacts. Do not invent firmographic statistics or technographic install rates.

Idea name: {{idea_name}}
Target buyer: {{target_buyer}}
Current workaround: {{current_workaround}}
Known competitors (names only):
{{known_competitors}}

Approved problem validation brief:
{{artifact:problem-validation-brief}}

Approved pain frequency snapshot:
{{artifact:pain-frequency-snapshot}}

Produce Markdown with these exact section headings:

## ICP snapshot
Title, company type, revenue band, catalog/channel traits as bullet lines. Mark guesses.

## Firmographics
Operational traits that predict the pain (stack, team size, supplier model). No invented percentages.

## Psychographics
How they think about the problem and tools. Ground in brief and workaround.

## Disqualifiers (who this is not for)
At least three out-of-segment profiles with why they fail.

## Day-in-the-life trigger
One short scene when they would search for a solution.

## ICP litmus test
One paragraph: how to score a lead in 60 seconds using prior artifacts.

Under 450 words. No fabricated case studies.
TXT,
            'example_response' => $idealCustomerProfileExample,
            'checks' => [
                ['label' => 'Revenue band matches Pantry', 'help' => 'ICP honors the buyer range you entered, not a broader "all Shopify stores" definition.'],
                ['label' => 'Disqualifiers are concrete', 'help' => 'At least one disqualifier names a segment you might wrongly chase (e.g., pre-revenue, EDI-heavy).'],
                ['label' => 'Trigger scene is recognizable', 'help' => 'Ops/finance lead would say "that is my month-end" after reading the day-in-the-life section.'],
                ['label' => 'Competitors inform stack context', 'help' => 'ICP mentions realistic adjacent tools from your competitor list, not invented logos.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'buyer-access-plan',
            'title'            => 'Buyer Access Plan',
            'summary'          => 'Plan how to reach in-segment buyers for interviews and pilots on your primary channel.',
            'why_it_matters'   => 'The best validation brief is useless if every conversation is with friends or wrong-fit founders. This plan turns your reach channel into a calendar.',
            'unlocks_text'     => 'Approve it and Stage 3 market Recipes proceed knowing how you will reality-check competitor claims with buyers.',
            'est_minutes'      => 15,
            'prompt_template'  => <<<'TXT'
You are a founder-led GTM strategist for early validation (not scale). Using Pantry facts and approved ICP, write a buyer access plan. Do not invent response rates, conversion percentages, or names of community members.

Idea name: {{idea_name}}
Primary reach channel: {{reach_channel}}
Success signal: {{success_signal}}
Decision deadline: {{decision_deadline}}

Approved ideal customer profile:
{{artifact:ideal-customer-profile}}

Produce Markdown with these exact section headings:

## Primary access path: [channel name]
Where to show up, approach tone (diagnostic not pitch), weekly activity targets, conversion signals. Use qualitative targets unless Pantry provides numbers.

## Secondary paths (if primary stalls)
Two backup channels compatible with the ICP.

## Who to avoid
Segments and venues that waste time given disqualifiers.

## 2-week outreach calendar
Simple table of activities by week.

## Success metric for access (from Pantry)
Restate success signal and define a precursor metric (conversations, calls) achievable before product exists.

Under 450 words. Do not claim prior outreach results.
TXT,
            'example_response' => $buyerAccessPlanExample,
            'checks' => [
                ['label' => 'Channel matches Pantry selection', 'help' => 'Primary path uses the reach channel you picked, not a random growth hack.'],
                ['label' => 'Tone is diagnostic', 'help' => 'Outreach asks about workflows, not "book a demo" spam.'],
                ['label' => 'Calendar fits decision deadline', 'help' => 'Two-week sprint plus follow-ups can repeat before your decision date.'],
                ['label' => 'Success signal is restated measurably', 'help' => 'Precursor metrics tie to 5 paid pilots or your exact Pantry success signal.'],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'competitor-landscape',
            'title'            => 'Competitor Landscape',
            'summary'          => 'Map alternatives from your list and place where the idea sits in the stack.',
            'why_it_matters'   => 'Founders often discover they are building a feature of an incumbent, not a company. Honest placement now prevents a six-month misfire.',
            'unlocks_text'     => 'Approve it and the Differentiation Hypothesis Recipe articulates the wedge against this map.',
            'est_minutes'      => 16,
            'prompt_template'  => <<<'TXT'
You are a competitive analyst for early-stage SaaS. Using Pantry competitors and approved brief, map the landscape. Do not invent funding rounds, customer counts, pricing tiers, or feature lists not stated in Pantry.

Idea name: {{idea_name}}
Known competitors (complete list, do not add names):
{{known_competitors}}
Current workaround: {{current_workaround}}

Approved problem validation brief:
{{artifact:problem-validation-brief}}

Produce Markdown with these exact section headings:

## Competitive set (from Pantry, expanded honestly)
Table: Name, What they optimize, Overlap with {{idea_name}}, Gap. One row per Pantry line.

## Category placement
One paragraph naming the category you are entering and what you are not.

## Switching behavior
Bullets for do-nothing incumbent and top alternatives. No invented switching cost dollars.

## Competitive risks
Three risks (incumbent expansion, platform, status quo).

## Unknowns (do not invent market share)
What must be verified on vendor sites or in buyer calls.

Under 450 words. Label all feature claims as public positioning unless sourced in Pantry.
TXT,
            'example_response' => $competitorLandscapeExample,
            'checks' => [
                ['label' => 'Every Pantry competitor appears', 'help' => 'Each line from known_competitors has a row, including manual bookkeeper/workaround.'],
                ['label' => 'Overlap vs gap is honest', 'help' => 'Bookkeep/A2X-style tools are credited for what they actually do before stating the gap.'],
                ['label' => 'Unknowns section exists', 'help' => 'Market share and pricing are flagged unverified, not stated as facts.'],
                ['label' => 'Category name fits Shopify ops pain', 'help' => 'Placement reads as supplier invoice reconciliation, not generic "fintech."'],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'differentiation-hypothesis',
            'title'            => 'Differentiation Hypothesis',
            'summary'          => 'State the wedge, why incumbents miss it, and how you will falsify the claim.',
            'why_it_matters'   => '"Better UX" is not differentiation. A falsifiable wedge lets you test whether buyers see invoice-to-order matching as a separate job from bookkeeping.',
            'unlocks_text'     => 'Approve it and Stage 4 assumption Recipes stress-test whether the wedge survives contact with reality.',
            'est_minutes'      => 16,
            'prompt_template'  => <<<'TXT'
You are a strategy advisor for pre-product SaaS. Write a differentiation hypothesis using Pantry facts and approved artifacts. Do not invent proprietary technology, patents, or customer testimonials.

Idea name: {{idea_name}}
Founder insight: {{founder_insight}}
Price hypothesis: {{price_hypothesis}} USD/month (hypothesis only)

Approved problem validation brief:
{{artifact:problem-validation-brief}}

Approved competitor landscape:
{{artifact:competitor-landscape}}

Produce Markdown with these exact section headings:

## Differentiation thesis
One paragraph: job to be done, buyer outcome, why not incumbent.

## Wedge (first believable claim)
Single sentence a buyer could repeat. Must be testable in interviews.

## Why incumbents miss this wedge
One bullet per named competitor from the landscape, tied to gap column.

## Founder insight as moat seed
How personal insight shapes product bias. Keep as anecdote.

## Differentiation tests (falsifiable)
Three tests that could prove you wrong.

## What we will not claim
Honest limits (compliance, scope, integrations).

Under 450 words. No fabricated pilot results.
TXT,
            'example_response' => $differentiationHypothesisExample,
            'checks' => [
                ['label' => 'Wedge mentions invoice-to-order matching', 'help' => 'Core claim reflects supplier reconciliation, not generic automation.'],
                ['label' => 'Tests could fail', 'help' => 'Each falsification test has a clear fail condition, not a vanity metric.'],
                ['label' => 'Price ROI is framed as hypothesis', 'help' => '$79/month (or your price) is tied to buyer math to verify, not asserted as validated WTP.'],
                ['label' => 'Incumbent bullets cite landscape gaps', 'help' => 'Why Bookkeep/A2X/Ramp miss the wedge references the competitor table, not strawmen.'],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'riskiest-assumptions-register',
            'title'            => 'Riskiest Assumptions Register',
            'summary'          => 'Rank the assumptions that would kill the idea if wrong.',
            'why_it_matters'   => 'Building tests the riskiest assumption first is cheaper than building the whole MVP. This register decides what evidence matters most.',
            'unlocks_text'     => 'Approve it and the Assumption Evidence Matrix Recipe assigns tests to each row.',
            'est_minutes'      => 15,
            'prompt_template'  => <<<'TXT'
You are a lean startup advisor. From Pantry facts and all approved Stage 1-3 artifacts, build an assumptions register. Do not invent validation results or confidence percentages.

Idea name: {{idea_name}}
Price hypothesis: {{price_hypothesis}} USD/month
Success signal: {{success_signal}}
Reach channel: {{reach_channel}}

Approved problem validation brief:
{{artifact:problem-validation-brief}}

Approved pain frequency snapshot:
{{artifact:pain-frequency-snapshot}}

Approved ideal customer profile:
{{artifact:ideal-customer-profile}}

Approved competitor landscape:
{{artifact:competitor-landscape}}

Approved differentiation hypothesis:
{{artifact:differentiation-hypothesis}}

Produce Markdown with these exact section headings:

## Assumption stack (ranked by risk)
Table: Rank, Assumption, Why it is risky, Category (Demand/Feasibility/Buyer/Competition/Pricing/Channel). At least 8 rows.

## Top 3 to test first
Which assumptions to test before writing code, with kill implication.

## Assumptions treated as facts today (flagged)
List anything in prior artifacts still anecdote or hypothesis.

Under 500 words. No invented experiment outcomes.
TXT,
            'example_response' => $riskiestAssumptionsRegisterExample,
            'checks' => [
                ['label' => 'Feasibility assumptions included', 'help' => 'At least one row covers invoice matching or data feasibility, not only "people want this."'],
                ['label' => 'Top 3 map to kill scenarios', 'help' => 'Each priority assumption says what happens if it fails (kill, revise, or narrow).'],
                ['label' => 'Founder anecdote flagged', 'help' => '$14K duplicate story or similar is listed as unverified anecdote, not customer proof.'],
                ['label' => 'Channel assumption present', 'help' => 'Reach channel from Pantry appears as testable row with risk rationale.'],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'assumption-evidence-matrix',
            'title'            => 'Assumption Evidence Matrix',
            'summary'          => 'Pair each assumption with evidence you have, need, and pass/fail thresholds.',
            'why_it_matters'   => 'Assumptions without pass/fail thresholds drift forever. This matrix is the scoreboard for your validation sprint.',
            'unlocks_text'     => 'Approve it and Kill Criteria Recipe turns fail thresholds into stop/revise rules.',
            'est_minutes'      => 15,
            'prompt_template'  => <<<'TXT'
You are a validation program manager. Build an evidence matrix from the assumptions register and prior artifacts. Do not invent completed experiments or interview counts.

Decision deadline: {{decision_deadline}}
Success signal: {{success_signal}}

Approved riskiest assumptions register:
{{artifact:riskiest-assumptions-register}}

Approved buyer access plan:
{{artifact:buyer-access-plan}}

Produce Markdown with these exact section headings:

## Evidence matrix
Table columns: Assumption, Evidence we have, Evidence we need, Test method, Pass threshold, Fail threshold. One row per ranked assumption.

## Current evidence grade
Short bullets: Strong / Weak / Missing areas. Honest about empty cells.

## Next 10 days: evidence collection priorities
Ordered list of three actions tied to buyer access plan and matrix gaps.

Under 500 words. Pass/fail thresholds must be numeric or countable where possible, but do not fabricate current results.
TXT,
            'example_response' => $assumptionEvidenceMatrixExample,
            'checks' => [
                ['label' => 'Every ranked assumption has a row', 'help' => 'Matrix covers the full register, not just top 3.'],
                ['label' => 'Pass and fail thresholds differ', 'help' => 'Each row has distinct pass vs fail, not vague "learn more."'],
                ['label' => 'Evidence we have cites Pantry only', 'help' => 'Current evidence column does not claim interviews you have not run.'],
                ['label' => 'Priorities link to access plan', 'help' => '10-day actions mention outreach or teardown calls from your buyer access plan.'],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'kill-criteria',
            'title'            => 'Kill Criteria',
            'summary'          => 'Define stop, revise, and continue rules before optimism bias sets in.',
            'why_it_matters'   => 'Kill criteria written after failed experiments are rationalizations. Writing them now protects months of runway.',
            'unlocks_text'     => 'Approve it and Stage 5 scopes an MVP only if continue rules are still plausible.',
            'est_minutes'      => 14,
            'prompt_template'  => <<<'TXT'
You are a disciplined startup advisor. Convert the evidence matrix into decision rules. Do not invent current test results.

Decision deadline: {{decision_deadline}}
Price hypothesis: {{price_hypothesis}} USD/month
Success signal: {{success_signal}}

Approved assumption evidence matrix:
{{artifact:assumption-evidence-matrix}}

Approved riskiest assumptions register:
{{artifact:riskiest-assumptions-register}}

Produce Markdown with these exact section headings:

## Stop rules (idea dies if...)
At least five numbered rules derived from fail thresholds. Specific and measurable.

## Revise rules (pivot scope if...)
At least four pivot triggers (segment, product, price, GTM).

## Continue rules (green light to build MVP if...)
Rules tied to success signal and pass thresholds. Must be countable.

## Decision hygiene
How to decide on deadline if evidence is incomplete. Default to revise, not go.

Under 450 words. Do not state that any rule has already passed or failed unless Pantry provides that data (it does not).
TXT,
            'example_response' => $killCriteriaExample,
            'checks' => [
                ['label' => 'Stop rules cite matrix fails', 'help' => 'At least three stop rules mirror fail thresholds (volume, match rate, WTP).'],
                ['label' => 'Revise paths are actionable', 'help' => 'Each revise rule names a concrete pivot (segment, price, channel), not "keep learning."'],
                ['label' => 'Continue rules reference success signal', 'help' => 'Green light includes your Pantry success signal verbatim or equivalent.'],
                ['label' => 'Deadline hygiene is explicit', 'help' => 'Decision deadline triggers revise if cells are empty, not automatic go.'],
            ],
        ],
        [
            'stage_position'   => 5,
            'slug'             => 'mvp-scope-recommendation',
            'title'            => 'MVP Scope Recommendation',
            'summary'          => 'Recommend the smallest build that tests feasibility and willingness to pay.',
            'why_it_matters'   => 'Over-scoped MVPs validate nothing except your ability to ship late. This scope ties every feature to a ranked assumption.',
            'unlocks_text'     => 'Approve it and the Validation Experiment Plan Recipe sequences build and discovery work.',
            'est_minutes'      => 16,
            'prompt_template'  => <<<'TXT'
You are a product strategist for pre-PMF SaaS. Recommend MVP scope using approved artifacts. Do not invent engineering estimates from industry averages or promise integrations not justified by assumptions.

Idea name: {{idea_name}}
Price hypothesis: {{price_hypothesis}} USD/month
Success signal: {{success_signal}}

Approved differentiation hypothesis:
{{artifact:differentiation-hypothesis}}

Approved riskiest assumptions register:
{{artifact:riskiest-assumptions-register}}

Approved kill criteria:
{{artifact:kill-criteria}}

Produce Markdown with these exact section headings:

## MVP definition (concierge-first)
Goal statement plus In scope (numbered capabilities) and Out of scope (deferred items).

## Build vs concierge split
Table: Component, MVP approach, Why. Bias toward learning speed.

## Effort estimate
Qualitative slice (weeks), not fake story points. Gate eng on kill criteria.

## MVP success metrics (tie to Pantry)
Restate success signal and measurable match/retention metrics as hypotheses.

Under 450 words. No feature creep beyond testing top assumptions.
TXT,
            'example_response' => $mvpScopeRecommendationExample,
            'checks' => [
                ['label' => 'In scope tests match feasibility', 'help' => 'Core MVP proves invoice ingest and line matching, not full AP automation.'],
                ['label' => 'Out of scope lists tempting traps', 'help' => 'Bookkeep sync, EDI, and payments are explicitly deferred if not in v1.'],
                ['label' => 'Concierge bias is explicit', 'help' => 'Plan allows human-in-loop matching before automated scale.'],
                ['label' => 'Eng gated on kill criteria', 'help' => 'Build commitment waits on manual match test passing continue rules.'],
            ],
        ],
        [
            'stage_position'   => 5,
            'slug'             => 'validation-experiment-plan',
            'title'            => 'Validation Experiment Plan',
            'summary'          => 'Sequence interviews, tests, and pilots against your decision deadline.',
            'why_it_matters'   => 'Unscheduled validation slips until "after this feature." A dated experiment plan is how advanced founders treat discovery like delivery.',
            'unlocks_text'     => 'Approve it and the final Go/Revise/Stop Memo Recipe records outcomes against this plan.',
            'est_minutes'      => 14,
            'prompt_template'  => <<<'TXT'
You are a validation sprint coach. Write an experiment plan chaining prior artifacts. Do not invent completed experiments, calendar bookings, or conversion rates.

Idea name: {{idea_name}}
Reach channel: {{reach_channel}}
Price hypothesis: {{price_hypothesis}} USD/month
Success signal: {{success_signal}}
Decision deadline: {{decision_deadline}}

Approved buyer access plan:
{{artifact:buyer-access-plan}}

Approved kill criteria:
{{artifact:kill-criteria}}

Approved MVP scope recommendation:
{{artifact:mvp-scope-recommendation}}

Approved assumption evidence matrix:
{{artifact:assumption-evidence-matrix}}

Produce Markdown with these exact section headings:

## Experiment backlog (ordered)
At least four experiments. Each: Hypothesis, Method, Duration, Owner, Success metric, Output artifact.

## Timeline to decision deadline
Table mapping date windows to milestones ending on decision deadline.

## Resources
Founder time, budget, tools. Keep realistic for solo founder.

Under 500 words. Label all metrics as targets, not achieved results.
TXT,
            'example_response' => $validationExperimentPlanExample,
            'checks' => [
                ['label' => 'Four experiments cover demand and feasibility', 'help' => 'Plan includes interviews, manual match test, outreach, and paid concierge pilot.'],
                ['label' => 'Timeline ends on decision deadline', 'help' => 'Milestones anchor to your Pantry decision date, not vague "soon."'],
                ['label' => 'Outreach experiment cites access plan', 'help' => 'Channel tactics match buyer access plan, not new channels.'],
                ['label' => 'Success metrics tie to kill criteria', 'help' => 'Experiment pass/fail uses thresholds from kill criteria and matrix.'],
            ],
        ],
        [
            'stage_position'   => 5,
            'slug'             => 'go-revise-stop-memo',
            'title'            => 'Go / Revise / Stop Memo',
            'summary'          => 'Synthesize evidence and force an honest go, revise, or stop decision.',
            'why_it_matters'   => 'The memo is the deliverable investors and co-founders actually want: a decision with criteria, not another brainstorm doc.',
            'unlocks_text'     => 'Approving this completes the Cookbook and opens your Project Kit export.',
            'est_minutes'      => 14,
            'prompt_template'  => <<<'TXT'
You are a board-advisor style reviewer. Draft a go/revise/stop memo using all approved artifacts. Do not invent experiment results, revenue, or pilot counts. Use Red/Yellow/Green for evidence status based on whether real data exists (most cells start Red/Yellow).

Idea name: {{idea_name}}
Decision deadline: {{decision_deadline}}
Success signal: {{success_signal}}
Price hypothesis: {{price_hypothesis}} USD/month

Approved problem validation brief:
{{artifact:problem-validation-brief}}

Approved assumption evidence matrix:
{{artifact:assumption-evidence-matrix}}

Approved kill criteria:
{{artifact:kill-criteria}}

Approved MVP scope recommendation:
{{artifact:mvp-scope-recommendation}}

Approved validation experiment plan:
{{artifact:validation-experiment-plan}}

Produce Markdown with these exact section headings:

## Executive summary
Three sentences: problem, current evidence state, decision framework.

## Evidence snapshot (as of draft)
Table: Area, Status (Red/Yellow/Green), Notes. Honest if experiments not run yet.

## Decision options
Subsections ### GO (build MVP), ### REVISE, ### STOP. Each: criteria met/trigger, recommended actions.

## Preliminary recommendation
One of GO, REVISE, or STOP with rationale. If evidence missing, default REVISE and state what data upgrades the call.

## Signed decision
Checkbox lines for GO / REVISE / STOP, decision owner, review date from Pantry.

Under 500 words. Never claim pilots or match rates exist unless user pasted real results (assume they do not).
TXT,
            'example_response' => $goReviseStopMemoExample,
            'checks' => [
                ['label' => 'Recommendation matches evidence honesty', 'help' => 'If experiments are unrun, memo defaults to REVISE, not false GO.'],
                ['label' => 'All three decision paths defined', 'help' => 'GO, REVISE, and STOP each have triggers tied to kill criteria.'],
                ['label' => 'Success signal appears in GO path', 'help' => 'Continue option references 5 paid pilots or your exact Pantry success signal.'],
                ['label' => 'Signed decision block is present', 'help' => 'Memo ends with checkboxes, owner, and decision deadline date.'],
            ],
        ],
    ],
];
