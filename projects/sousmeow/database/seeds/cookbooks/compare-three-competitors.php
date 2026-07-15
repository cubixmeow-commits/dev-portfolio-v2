<?php

declare(strict_types=1);

/**
 * Compare Three Competitors — executable Cookbook for research-insights.
 *
 * Inspired by public university entrepreneurship competitive-analysis teaching
 * (ideas only; invent no proprietary frameworks). Built to pass Product Law 002:
 * ask for known facts, hide analysis complexity, and force unknowns to stay
 * unknown. All copy, examples, and prompts are original SousMeow work.
 */

$frameQuestionExample = <<<'MD'
## Research question
How do three lunch-prep apps compare against BatchBowl for busy home cooks who want to prep weekday lunches on Sunday without wasting food?

## Decision this supports
Decide whether BatchBowl should position around pantry-first batch planning or broaden into general meal planning before the September landing-page rewrite.

## Your offer baseline
BatchBowl is a meal-prep planner for people who cook once or twice a week and want lunch portions, shopping lists, and leftover tracking in one place.

## Facts to protect
- Target audience: busy professionals and parents who want weekday lunches ready before Monday.
- Known BatchBowl angle: batch cooking, lunch portions, shopping list, leftover tracking.
- Deadline: September 15 positioning decision.

## Unknowns to keep visible
- Exact competitor pricing is not fully supplied.
- Competitor feature depth beyond public positioning is unknown.
- No funding, revenue, user count, or growth claims were provided.
MD;

$nameCompetitorsExample = <<<'MD'
## Competitor set
| Slot | Competitor | Why included | Public fact supplied |
|------|------------|--------------|----------------------|
| A | PrepPal | Direct lunch-prep planning alternative | User says it offers weekly meal plans and grocery lists |
| B | LunchLoop | Lunch-specific app with recurring menus | User says it focuses on work lunches and reminders |
| C | PlatePlan | Broader meal-planning app | User says it has recipes, shopping lists, and family servings |

## Comparison boundaries
Compare only offer, audience, pricing signals, and visible gaps. Do not rank product quality, traction, reviews, funding, or revenue unless the user supplied evidence.

## Missing competitor facts
- PrepPal pricing tier names are unknown.
- LunchLoop feature list beyond lunch reminders is unknown.
- PlatePlan's exact target segment is unknown.

## Research hygiene note
Treat this as a fast positioning read, not a full market map. Unknown means unknown.
MD;

$compareOffersExample = <<<'MD'
## Offer comparison table
| Product | Core promise | Main workflow | Overlap with BatchBowl | Unknowns |
|---------|--------------|---------------|------------------------|----------|
| BatchBowl | Plan weekday lunches from batch cooking | Choose prep session, portions, shopping list, leftover tracking | Baseline | None from supplied facts |
| PrepPal | Weekly meal plans and grocery lists | Pick plan, shop, cook | Meal plans and grocery lists overlap | Whether it tracks leftovers |
| LunchLoop | Work-lunch planning with reminders | Set recurring lunches and reminders | Lunch focus overlaps | Whether it builds batch prep or shopping lists |
| PlatePlan | General recipes and family servings | Browse recipes, size meals, shop | Shopping and portions overlap | Whether it focuses on lunches |

## Most visible difference
BatchBowl's clearest visible difference is pantry-first batch lunch planning with leftover tracking. PrepPal and PlatePlan sound broader; LunchLoop sounds lunch-specific but lighter on prep operations from the supplied facts.

## Unknowns to verify
- Does PrepPal support leftover inventory?
- Does LunchLoop generate grocery lists?
- Does PlatePlan offer batch-cooking workflows?
MD;

$compareAudiencesExample = <<<'MD'
## Audience comparison table
| Product | Likely audience from supplied facts | Audience overlap | Audience difference |
|---------|-------------------------------------|------------------|---------------------|
| BatchBowl | Busy professionals and parents prepping weekday lunches | Baseline | Wants Sunday prep and less food waste |
| PrepPal | People who want weekly meal plans | Medium | May include dinners and general planning |
| LunchLoop | People planning work lunches | High | May skew toward office routines and reminders |
| PlatePlan | Families sizing recipes and shopping | Medium | Broader household meal planning |

## Audience overlap
LunchLoop overlaps most directly on lunch planning. PrepPal and PlatePlan overlap when their users need shopping lists and portions, but their supplied facts point beyond weekday lunch prep.

## Audience gaps
No competitor is confirmed to own the specific "Sunday batch prep for weekday lunches with leftover tracking" audience. That may be a useful wedge if verified on public pages and user interviews.
MD;

$pricingSignalsExample = <<<'MD'
## Pricing signal table
| Product | Pricing signal from supplied facts | What it may imply | Unknowns |
|---------|------------------------------------|-------------------|----------|
| BatchBowl | Unknown | Pricing story is not set | Willingness to pay |
| PrepPal | Unknown | Cannot compare price | Free tier, trial, subscription |
| LunchLoop | Unknown | Cannot compare price | Free tier, trial, subscription |
| PlatePlan | Unknown | Cannot compare price | Free tier, trial, subscription |

## Pricing caveats
No exact prices were supplied, so this comparison cannot claim cheaper, premium, freemium, or enterprise positioning. Pricing should be treated as a research task, not a conclusion.

## Evidence gaps
- Public pricing pages for all three competitors.
- Whether prices are monthly, annual, or one-time.
- Whether core lunch-prep features sit behind paid plans.
MD;

$gapMapExample = <<<'MD'
## Gap map
| Gap | Evidence from comparison | Opportunity if true | Verification needed |
|-----|--------------------------|---------------------|---------------------|
| Leftover-aware planning | No competitor was supplied with leftover tracking | Position BatchBowl around less waste after prep day | Check feature pages and onboarding |
| Sunday batch workflow | Competitors sound weekly, lunch, or recipe-led | Own the specific prep-session workflow | Review public copy and screenshots |
| Lunch portions over dinner variety | PlatePlan and PrepPal sound broader | Emphasize repeatable weekday lunches | Interview target users |

## Possible white spaces
BatchBowl may have room to be the practical lunch-prep planner for people who repeat meals, shop once, and hate wasted ingredients.

## Risks of the gaps
The gaps may disappear after public research. A competitor could already offer leftovers or batch prep but not have that fact in the Pantry.
MD;

$opportunityNotesExample = <<<'MD'
## Opportunity notes
1. Lead with "turn Sunday cooking into weekday lunches" if public research confirms competitors stay broader.
2. Show leftover tracking early because it is the most concrete difference in the supplied facts.
3. Avoid claiming BatchBowl is cheaper, faster, or more popular until evidence exists.

## Strongest opening
BatchBowl can test a positioning line around "plan one prep session, portion every weekday lunch, and use what is already in your kitchen."

## What not to claim
- Do not claim competitors lack features until verified on public pages.
- Do not claim market leadership, funding, user counts, or revenue.
- Do not imply BatchBowl replaces all meal planning; keep the wedge lunch-prep specific.
MD;

$decisionMemoExample = <<<'MD'
## Decision memo
BatchBowl should test a lunch-prep wedge instead of broad meal-planning copy for the September 15 positioning decision. The supplied facts show the clearest possible difference around batch cooking, lunch portions, shopping lists, and leftover tracking.

## Recommended next move
Run a one-week evidence pass: capture each competitor's public offer, audience language, and pricing page in a simple table. Then draft two landing-page hero options: one pantry-first and one Sunday batch-prep focused.

## Evidence to collect next
1. Public pricing and free-trial details for PrepPal, LunchLoop, and PlatePlan.
2. Screenshots or notes from competitor onboarding that prove whether leftovers and batch prep are present.
3. Five target-audience interviews asking what makes lunch prep fail after Sunday.
MD;

return [
    'slug'                => 'compare-three-competitors',
    'title'               => 'Compare Three Competitors',
    'tagline'             => 'Put three real alternatives side by side and leave with a clear opportunity note.',
    'description'         => "Competitive research gets sloppy when teams fill gaps with guesses. This Cookbook frames one positioning question, compares three named alternatives against your offer, and forces every unknown to stay visible. Enter only facts you already know or can verify from public sources. Every step is told to invent nothing about funding, revenue, feature depth, or market share. You leave with a research frame, competitor cards, comparison tables, gaps, opportunity notes, and a short decision memo.",
    'primary_category'    => 'research-insights',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Founders and PMs who need a fast, honest competitor read before positioning',
    'outcome'             => 'research frame, competitor cards, offer/audience/pricing comparison, gaps, and a short decision memo',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'slate',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 55,
    'demo_completed_runs' => 198,
    'demo_avg_rating'     => 4.6,
    'sort_order'          => 10,
    'stages' => [
        ['title' => 'Field', 'summary' => 'Frame the question and name the three competitors.'],
        ['title' => 'Compare', 'summary' => 'Compare offers, audiences, pricing signals, and visible gaps.'],
        ['title' => 'Conclude', 'summary' => 'Turn the comparison into opportunity notes and a decision memo.'],
    ],
    'fields' => [
        [
            'field_key'    => 'research_goal',
            'label'        => 'What decision should this competitor read support?',
            'type'         => 'textarea',
            'help'         => 'Name the positioning, product, pricing, or audience question. No vague "understand the market."',
            'placeholder'  => 'e.g. Decide whether to position around pantry-first lunch prep or broad meal planning',
            'sample_value' => 'Decide whether BatchBowl should position around pantry-first batch planning or broaden into general meal planning before the September landing-page rewrite.',
        ],
        [
            'field_key'    => 'your_offer',
            'label'        => 'Your offer',
            'type'         => 'textarea',
            'help'         => 'Describe what you sell or plan to sell. Stick to current facts and intended audience.',
            'placeholder'  => 'e.g. BatchBowl helps people plan weekday lunches from one Sunday prep session',
            'sample_value' => 'BatchBowl is a meal-prep planner for people who cook once or twice a week and want lunch portions, shopping lists, and leftover tracking in one place.',
        ],
        [
            'field_key'    => 'competitor_a',
            'label'        => 'Competitor A',
            'type'         => 'text',
            'help'         => 'Name a real alternative. Add a public URL if you have it.',
            'placeholder'  => 'e.g. PrepPal — weekly meal plans and grocery lists',
            'sample_value' => 'PrepPal — offers weekly meal plans and grocery lists',
        ],
        [
            'field_key'    => 'competitor_b',
            'label'        => 'Competitor B',
            'type'         => 'text',
            'help'         => 'Name a second real alternative. Direct, indirect, or status quo is fine if real.',
            'placeholder'  => 'e.g. LunchLoop — work lunch planning and reminders',
            'sample_value' => 'LunchLoop — focuses on work lunches and reminders',
        ],
        [
            'field_key'    => 'competitor_c',
            'label'        => 'Competitor C',
            'type'         => 'text',
            'help'         => 'Name a third real alternative. Do not invent a competitor to fill the slot.',
            'placeholder'  => 'e.g. PlatePlan — recipes, shopping lists, family servings',
            'sample_value' => 'PlatePlan — broader meal-planning app with recipes, shopping lists, and family servings',
        ],
        [
            'field_key'    => 'known_facts',
            'label'        => 'Known competitor facts',
            'type'         => 'textarea',
            'help'         => 'Paste only facts you have from public pages, screenshots, notes, or firsthand use. If you do not know pricing, say unknown.',
            'placeholder'  => "PrepPal: weekly plans, grocery lists, pricing unknown\nLunchLoop: lunch reminders, pricing unknown",
            'sample_value' => "PrepPal: weekly meal plans and grocery lists; pricing unknown\nLunchLoop: work-lunch planning and reminders; pricing unknown\nPlatePlan: recipes, shopping lists, family servings; pricing unknown\nNo funding, revenue, user count, or market-share claims collected",
        ],
        [
            'field_key'    => 'target_audience',
            'label'        => 'Target audience',
            'type'         => 'textarea',
            'help'         => 'Who you most want to win. Use your own current audience facts, not imagined competitor segments.',
            'placeholder'  => 'e.g. Busy professionals prepping weekday lunches on Sunday',
            'sample_value' => 'Busy professionals and parents who want weekday lunches ready before Monday without wasting food.',
        ],
        [
            'field_key'    => 'decision_deadline',
            'label'        => 'Decision deadline',
            'type'         => 'text',
            'help'         => 'When this comparison must inform a choice.',
            'placeholder'  => 'e.g. September 15',
            'sample_value' => 'September 15 positioning decision',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'frame-the-question',
            'title'            => 'Frame the competitor question',
            'summary'          => 'Turn the research goal into one answerable comparison question with facts and unknowns separated.',
            'why_it_matters'   => 'Competitor work fails when the team starts collecting screenshots before naming the decision. This step narrows the question, anchors your offer, and makes unknowns explicit. Common mistakes: asking for a total market map, inventing what competitors are good at, or hiding the deadline. Need help if the AI sounds too confident? Paste again and require "unknown" for unsupported facts. You are ready when the frame could guide a one-hour public research pass.',
            'unlocks_text'     => 'Approving unlocks the competitor set and comparison boundaries.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are a careful competitive-research assistant. Follow Product Law 002: reduce cognitive load and use ONLY the facts below. Do not invent funding, revenue, user counts, market share, feature depth, pricing, reviews, or motives. If a fact is not supplied, write unknown.

Research goal:
{{research_goal}}

Your offer:
{{your_offer}}

Target audience:
{{target_audience}}

Decision deadline: {{decision_deadline}}

Known facts:
{{known_facts}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Research question
One answerable question this comparison should answer.

## Decision this supports
One or two sentences naming the choice and deadline.

## Your offer baseline
Plain-language baseline for the user's offer, using only provided facts.

## Facts to protect
Three to five bullets that must remain true through the comparison.

## Unknowns to keep visible
Three to five bullets naming missing facts. Do not fill them in.

Keep all five headings in order. Under 280 words. Invent nothing.
TXT,
            'example_response' => $frameQuestionExample,
            'output_sections' => [
                ['key' => 'research_question', 'heading' => 'Research question', 'required' => true],
                ['key' => 'decision_supported', 'heading' => 'Decision this supports', 'required' => true],
                ['key' => 'offer_baseline', 'heading' => 'Your offer baseline', 'required' => true],
                ['key' => 'facts_to_protect', 'heading' => 'Facts to protect', 'required' => true],
                ['key' => 'unknowns_visible', 'heading' => 'Unknowns to keep visible', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Question supports a real decision', 'help' => 'The question points to the entered decision and deadline, not a generic market scan.',
                 'evidence_sections' => ['research_question', 'decision_supported']],
                ['label' => 'Offer baseline uses only supplied facts', 'help' => 'No extra features, promises, traction, or buyer claims appear.',
                 'evidence_sections' => ['offer_baseline', 'facts_to_protect']],
                ['label' => 'Unknowns stay visible', 'help' => 'Missing competitor facts are named instead of guessed.',
                 'evidence_sections' => ['unknowns_visible']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-three-competitors',
            'title'            => 'Name the three real competitors',
            'summary'          => 'Create competitor cards and honest comparison boundaries before analysis begins.',
            'why_it_matters'   => 'A useful comparison starts with real alternatives and clear limits. This step prevents made-up competitors, bloated categories, and fake certainty. Common mistakes: adding a fourth company, claiming one is the leader without evidence, or treating missing data as a weakness. Need help if the AI invents a market map? Paste again and say exactly three named alternatives only. You are ready when every competitor row ties back to the Pantry.',
            'unlocks_text'     => 'Approving unlocks offer, audience, pricing, and gap comparisons.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are building a tight competitor set. Use ONLY the three named competitors and known facts below plus the approved frame. Do not add, replace, or rename competitors. Do not invent public facts.

Competitor A: {{competitor_a}}
Competitor B: {{competitor_b}}
Competitor C: {{competitor_c}}

Known facts:
{{known_facts}}

Approved research frame:
{{artifact:frame-the-question}}

Produce Markdown with these exact headings:

## Competitor set
A table with exactly three competitor rows: Slot, Competitor, Why included, Public fact supplied.

## Comparison boundaries
What this read will compare and what it will not claim.

## Missing competitor facts
Bullets naming missing facts for each competitor. If none were supplied for a category, say unknown.

## Research hygiene note
One short note reminding the user that unknown means unknown.

Keep all four headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $nameCompetitorsExample,
            'output_sections' => [
                ['key' => 'competitor_set', 'heading' => 'Competitor set', 'required' => true],
                ['key' => 'comparison_boundaries', 'heading' => 'Comparison boundaries', 'required' => true],
                ['key' => 'missing_competitor_facts', 'heading' => 'Missing competitor facts', 'required' => true],
                ['key' => 'research_hygiene_note', 'heading' => 'Research hygiene note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Exactly three competitors', 'help' => 'The table contains only the three named alternatives from the Pantry.',
                 'evidence_sections' => ['competitor_set']],
                ['label' => 'Boundaries prevent overclaiming', 'help' => 'The scope excludes unsupported rankings, traction, revenue, or feature claims.',
                 'evidence_sections' => ['comparison_boundaries']],
                ['label' => 'Missing facts are explicit', 'help' => 'Unknown details are listed instead of quietly inferred.',
                 'evidence_sections' => ['missing_competitor_facts', 'research_hygiene_note']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'compare-offers',
            'title'            => 'Compare the offers',
            'summary'          => 'Put your offer and the three alternatives side by side by promise, workflow, overlap, and unknowns.',
            'why_it_matters'   => 'Offer comparison shows what a buyer thinks each option does. This step keeps the view practical and public-fact based. Common mistakes: scoring quality without trying the product, adding hidden features, or turning a missing fact into a weakness. Need help if the AI guesses? Paste again and require every unsupported cell to say unknown. You are ready when the table could be checked against public pages.',
            'unlocks_text'     => 'Approving unlocks audience and pricing comparisons.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are comparing offers using public-source style thinking and only supplied facts. Do not invent features, integrations, quality claims, funding, revenue, or customer counts. Unknown is allowed and preferred over guessing.

Your offer:
{{your_offer}}

Known facts:
{{known_facts}}

Approved frame:
{{artifact:frame-the-question}}

Approved competitor set:
{{artifact:name-three-competitors}}

Produce Markdown with these exact headings:

## Offer comparison table
A table with rows for the user's offer plus all three competitors. Columns: Product, Core promise, Main workflow, Overlap with the user's offer, Unknowns.

## Most visible difference
One paragraph naming the clearest difference that is supported by facts.

## Unknowns to verify
Three to five bullets of public facts to verify next.

Keep all three headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $compareOffersExample,
            'output_sections' => [
                ['key' => 'offer_comparison_table', 'heading' => 'Offer comparison table', 'required' => true],
                ['key' => 'most_visible_difference', 'heading' => 'Most visible difference', 'required' => true],
                ['key' => 'unknowns_to_verify', 'heading' => 'Unknowns to verify', 'required' => true],
            ],
            'checks' => [
                ['label' => 'All four offers appear', 'help' => 'Your offer and exactly three competitors have rows.',
                 'evidence_sections' => ['offer_comparison_table']],
                ['label' => 'Difference is evidence-based', 'help' => 'The visible difference is traceable to supplied facts or marked as tentative.',
                 'evidence_sections' => ['most_visible_difference']],
                ['label' => 'Unsupported facts become research tasks', 'help' => 'Missing feature details are listed for verification.',
                 'evidence_sections' => ['unknowns_to_verify']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'compare-audiences',
            'title'            => 'Compare the audiences',
            'summary'          => 'Map likely audience overlap without inventing personas or buyer motives.',
            'why_it_matters'   => 'Competitors may overlap on product but serve different buyers. This step separates audience facts from assumptions. Common mistakes: inventing job titles, claiming a competitor targets enterprise, or assuming all alternatives fight for the same buyer. Need help if the AI creates personas? Paste again and require "from supplied facts" in every audience row. You are ready when overlap and gaps are both clearly caveated.',
            'unlocks_text'     => 'Approving unlocks pricing signals and gap mapping.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are comparing audiences. Use ONLY the target audience, known facts, and approved artifacts. Do not invent personas, demographics, willingness to pay, or buyer psychology.

Target audience:
{{target_audience}}

Known facts:
{{known_facts}}

Approved frame:
{{artifact:frame-the-question}}

Approved competitor set:
{{artifact:name-three-competitors}}

Approved offer comparison:
{{artifact:compare-offers}}

Produce Markdown with these exact headings:

## Audience comparison table
A table with rows for the user's offer plus all three competitors. Columns: Product, Likely audience from supplied facts, Audience overlap, Audience difference.

## Audience overlap
One paragraph naming where audiences appear to overlap, with caveats.

## Audience gaps
One paragraph naming possible audience gaps and what must be verified.

Keep all three headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $compareAudiencesExample,
            'output_sections' => [
                ['key' => 'audience_comparison_table', 'heading' => 'Audience comparison table', 'required' => true],
                ['key' => 'audience_overlap', 'heading' => 'Audience overlap', 'required' => true],
                ['key' => 'audience_gaps', 'heading' => 'Audience gaps', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Audience claims are sourced to supplied facts', 'help' => 'Rows do not invent buyer roles, demographics, or motives.',
                 'evidence_sections' => ['audience_comparison_table']],
                ['label' => 'Overlap is not overstated', 'help' => 'The overlap paragraph uses cautious language where evidence is thin.',
                 'evidence_sections' => ['audience_overlap']],
                ['label' => 'Gaps require verification', 'help' => 'Possible gaps are framed as possible, not proven.',
                 'evidence_sections' => ['audience_gaps']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'compare-pricing-signals',
            'title'            => 'Compare pricing signals',
            'summary'          => 'Separate known pricing facts from missing pricing research.',
            'why_it_matters'   => 'Pricing is easy to fake and hard to unwind. This step refuses to claim cheap, premium, or freemium unless the Pantry supports it. Common mistakes: guessing monthly prices, assuming a free trial, or treating unknown price as expensive. Need help if the AI creates numbers? Paste again and forbid any dollar amount not in your known facts. You are ready when pricing confidence is proportional to evidence.',
            'unlocks_text'     => 'Approving unlocks the gap map.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are comparing pricing signals. Use ONLY pricing details supplied in the known facts and approved artifacts. Do not invent dollar amounts, tiers, trials, discounts, or packaging.

Known facts:
{{known_facts}}

Approved frame:
{{artifact:frame-the-question}}

Approved competitor set:
{{artifact:name-three-competitors}}

Approved offer comparison:
{{artifact:compare-offers}}

Produce Markdown with these exact headings:

## Pricing signal table
A table with rows for the user's offer plus all three competitors. Columns: Product, Pricing signal from supplied facts, What it may imply, Unknowns.

## Pricing caveats
One paragraph explaining what cannot be concluded yet.

## Evidence gaps
Three to five bullets naming pricing facts to collect from public sources.

Keep all three headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $pricingSignalsExample,
            'output_sections' => [
                ['key' => 'pricing_signal_table', 'heading' => 'Pricing signal table', 'required' => true],
                ['key' => 'pricing_caveats', 'heading' => 'Pricing caveats', 'required' => true],
                ['key' => 'evidence_gaps', 'heading' => 'Evidence gaps', 'required' => true],
            ],
            'checks' => [
                ['label' => 'No invented prices', 'help' => 'Dollar amounts or tier names appear only if supplied.',
                 'evidence_sections' => ['pricing_signal_table']],
                ['label' => 'Caveats block false conclusions', 'help' => 'The response says what cannot be claimed about price yet.',
                 'evidence_sections' => ['pricing_caveats']],
                ['label' => 'Pricing research tasks are concrete', 'help' => 'Evidence gaps name public pricing facts to collect.',
                 'evidence_sections' => ['evidence_gaps']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'map-gaps',
            'title'            => 'Map visible gaps',
            'summary'          => 'Turn the comparisons into possible white spaces without pretending they are proven.',
            'why_it_matters'   => 'Gaps are hypotheses until verified. This step makes opportunity visible while keeping evidence honest. Common mistakes: declaring competitors do not have features, mistaking unclear copy for absence, or framing every gap as a moat. Need help if the AI overclaims? Paste again and require "if true" language. You are ready when the gap map gives useful research targets.',
            'unlocks_text'     => 'Approving unlocks opportunity notes.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are mapping possible gaps from the approved comparison. Use ONLY supplied facts and approved artifacts. Do not claim a competitor lacks something unless the fact was supplied. Use "possible" and "if true" where evidence is incomplete.

Approved frame:
{{artifact:frame-the-question}}

Approved competitor set:
{{artifact:name-three-competitors}}

Approved offer comparison:
{{artifact:compare-offers}}

Approved audience comparison:
{{artifact:compare-audiences}}

Approved pricing signals:
{{artifact:compare-pricing-signals}}

Produce Markdown with these exact headings:

## Gap map
A table with 3 to 5 possible gaps. Columns: Gap, Evidence from comparison, Opportunity if true, Verification needed.

## Possible white spaces
One short paragraph naming the most promising white space in cautious language.

## Risks of the gaps
Two or three sentences explaining how the gaps could be wrong after public research.

Keep all three headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $gapMapExample,
            'output_sections' => [
                ['key' => 'gap_map', 'heading' => 'Gap map', 'required' => true],
                ['key' => 'possible_white_spaces', 'heading' => 'Possible white spaces', 'required' => true],
                ['key' => 'risks_of_gaps', 'heading' => 'Risks of the gaps', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Gaps are framed as possible', 'help' => 'The map does not treat missing evidence as proof.',
                 'evidence_sections' => ['gap_map']],
                ['label' => 'White space is useful and cautious', 'help' => 'The opportunity is specific but not claimed as proven.',
                 'evidence_sections' => ['possible_white_spaces']],
                ['label' => 'Risks are visible', 'help' => 'The response explains how public research could invalidate the gaps.',
                 'evidence_sections' => ['risks_of_gaps']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-opportunity-notes',
            'title'            => 'Write opportunity notes',
            'summary'          => 'Translate the gap map into positioning notes and claims to avoid.',
            'why_it_matters'   => 'The value of competitor work is the next message or product bet it sharpens. This step creates usable opportunity notes while protecting against false claims. Common mistakes: using superlatives, attacking competitors, or forgetting what is still unverified. Need help if the AI writes marketing hype? Paste again and ban "best," "only," and unsupported claims. You are ready when the notes could guide a landing-page test without legal or research regret.',
            'unlocks_text'     => 'Approving unlocks the final decision memo.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing opportunity notes from a cautious competitor read. Use ONLY approved artifacts. Do not invent proof, market size, competitor weakness, funding, revenue, feature claims, or customer behavior.

Approved frame:
{{artifact:frame-the-question}}

Approved gap map:
{{artifact:map-gaps}}

Approved audience comparison:
{{artifact:compare-audiences}}

Approved pricing signals:
{{artifact:compare-pricing-signals}}

Produce Markdown with these exact headings:

## Opportunity notes
Three to five numbered notes the user can act on. Each must stay inside the evidence.

## Strongest opening
One short positioning or product opening to test, phrased as a hypothesis if evidence is incomplete.

## What not to claim
Three to five bullets banning unsupported claims.

Keep all three headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $opportunityNotesExample,
            'output_sections' => [
                ['key' => 'opportunity_notes', 'heading' => 'Opportunity notes', 'required' => true],
                ['key' => 'strongest_opening', 'heading' => 'Strongest opening', 'required' => true],
                ['key' => 'what_not_to_claim', 'heading' => 'What not to claim', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Notes are actionable', 'help' => 'Each note can guide copy, research, or product focus.',
                 'evidence_sections' => ['opportunity_notes']],
                ['label' => 'Opening is not overclaimed', 'help' => 'The strongest opening stays evidence-based or explicitly tentative.',
                 'evidence_sections' => ['strongest_opening']],
                ['label' => 'Unsupported claims are blocked', 'help' => 'The banned claims list includes traction, feature, and pricing overreach where relevant.',
                 'evidence_sections' => ['what_not_to_claim']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-decision-memo',
            'title'            => 'Pack the decision memo',
            'summary'          => 'Finish with a short memo, recommended next move, and evidence to collect next.',
            'why_it_matters'   => 'Research should end in a decision-ready artifact, not a pile of notes. This step names the recommended move and the evidence still needed. Common mistakes: pretending uncertainty is gone, recommending a build without validation, or burying the next action. Need help if the memo is too long? Paste again and cap it at three sections. You are ready when the memo can be pasted into a team update.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing a short decision memo from approved competitor research. Use ONLY approved artifacts and the original decision deadline. Do not invent new facts or certainty. Be clear about what is recommendation versus evidence to collect.

Decision deadline: {{decision_deadline}}

Approved frame:
{{artifact:frame-the-question}}

Approved competitor set:
{{artifact:name-three-competitors}}

Approved gap map:
{{artifact:map-gaps}}

Approved opportunity notes:
{{artifact:write-opportunity-notes}}

Produce Markdown with these exact headings:

## Decision memo
One concise paragraph summarizing the comparison and what it suggests for the decision.

## Recommended next move
One practical next move before the deadline. Keep it feasible.

## Evidence to collect next
Three to five numbered evidence items that would reduce uncertainty. Public-source checks and user interviews are allowed. Invent nothing.

Keep all three headings in order. Under 260 words.
TXT,
            'example_response' => $decisionMemoExample,
            'output_sections' => [
                ['key' => 'decision_memo', 'heading' => 'Decision memo', 'required' => true],
                ['key' => 'recommended_next_move', 'heading' => 'Recommended next move', 'required' => true],
                ['key' => 'evidence_to_collect_next', 'heading' => 'Evidence to collect next', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Memo reflects approved research', 'help' => 'The summary does not add new competitor facts.',
                 'evidence_sections' => ['decision_memo']],
                ['label' => 'Next move fits the deadline', 'help' => 'The recommendation is practical before the entered decision deadline.',
                 'evidence_sections' => ['recommended_next_move']],
                ['label' => 'Evidence list reduces uncertainty', 'help' => 'The next evidence items target unknowns surfaced earlier.',
                 'evidence_sections' => ['evidence_to_collect_next']],
            ],
        ],
    ],
];
