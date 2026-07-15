<?php

declare(strict_types=1);

/**
 * Write a Campaign Brief — executable Cookbook for marketing-growth.
 *
 * Inspired by public AIDA / campaign-brief teaching: audience, promise,
 * channel plan, offer details, and one measure. Ideas only. All copy,
 * examples, and prompts are original SousMeow work. Product Law 002 is
 * enforced by treating the pantry as known campaign facts, never invented
 * audience research, performance benchmarks, or ad claims.
 */

$campaignAudienceExample = <<<'MD'
## Campaign frame
BatchBowl free trial launch push introduces the meal-prep app to people who want an easier way to plan weekly meals.

## Audience
Busy professionals who want meal planning to feel less like a Sunday spreadsheet project.

## Audience facts
- They are considering a free trial, not a paid annual plan yet.
- The known problem is planning meals, not grocery delivery or dieting.
- The brief should speak to time and planning friction.

## Fit boundary
Do not widen this to everyone who eats. Do not assume family size, income, diet goals, or current apps unless those facts are supplied.
MD;

$campaignPromiseExample = <<<'MD'
## Core promise
BatchBowl helps busy professionals plan a week of meals faster, starting with a free trial.

## Reason to believe
The offer is a meal-prep app with a free trial, so the proof can lean on trying the planning flow before paying. The known pain is spreadsheet-style planning work.

## Message boundaries
- Do not promise weight loss, grocery savings, or nutrition outcomes.
- Do not claim the app replaces every food decision.
- Keep the promise about easier weekly meal planning.

## Draft line
"Plan your week of meals without turning Sunday into a spreadsheet."
MD;

$channelPlanExample = <<<'MD'
## Channel plan
| Channel | Role | Why it fits known facts |
|---------|------|-------------------------|
| Instagram Reels | Show the before/after of messy planning versus BatchBowl flow | Visual format can show planning friction quickly |
| Email to waitlist | Convert people who already asked for updates | They are already closer to trying the free trial |
| Partner newsletter | Reach a related audience through one trusted mention | Useful if the partner audience is already known |

## Channel jobs
Instagram gets attention, email asks for the trial start, and the partner newsletter borrows context from a trusted source.

## Budget guardrails
Keep spend within the stated launch notes. Do not invent cost-per-click, influencer rates, or paid media targets.

## What not to assume
No channel is guaranteed to win. The plan should be adjusted after real trial-start data appears.
MD;

$offerDetailsExample = <<<'MD'
## Offer details
BatchBowl is offering a free trial for new users during the launch push.

## Required terms
- Say what the free trial includes if the team knows it.
- Say who qualifies: new users.
- State any end date, payment requirement, or cancellation rule only if those facts are confirmed.

## Friction to remove
Make the trial-start action obvious: one button, one short explanation, and no extra claims about savings or diet outcomes.

## Asset notes
Each channel needs the same offer line, one call to action, and any confirmed terms. Leave unknown legal or billing terms as blanks to confirm.
MD;

$successMeasureExample = <<<'MD'
## Success measure
The campaign succeeds if it drives 200 free-trial starts within two weeks of the launch push.

## Counting plan
Count free-trial starts from the launch campaign links or signup source tags. Do not count impressions, likes, or email opens as the main result.

## Review checkpoint
Review results two weeks after launch. Compare trial starts by channel and note which promise line was used.

## Final brief
- Campaign: BatchBowl free trial launch push
- Audience: busy professionals who want meal planning without spreadsheet work
- Promise: plan a week of meals faster with a free trial
- Channels: Instagram Reels, waitlist email, partner newsletter
- Offer: free trial for new users, with unknown terms to confirm
- Measure: 200 free-trial starts within two weeks
MD;

return [
    'slug'                => 'write-a-campaign-brief',
    'title'               => 'Write a Campaign Brief',
    'tagline'             => 'Lock audience, promise, channels, and one success measure before you make ads.',
    'description'         => "Campaign work gets expensive when the audience, promise, offer, and measure are still fuzzy. This Cookbook turns one real campaign into a brief that can guide copy, channel choices, and review. Enter only campaign facts you already know. Every step is told to invent nothing about audience psychology, benchmarks, budgets, or performance. You leave with a campaign brief that names who it is for, what it promises, where it will run, what the offer says, and how success will be counted.",
    'primary_category'    => 'marketing-growth',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Founders, marketers, and creators who need a compact campaign brief before making ads or launch assets',
    'outcome'             => 'audience frame, campaign promise, channel plan, offer details, and one success measure',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'terracotta',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 45,
    'demo_completed_runs' => 188,
    'demo_avg_rating'     => 4.6,
    'sort_order'          => 14,
    'stages' => [
        ['title' => 'Aim', 'summary' => 'Name the audience and promise before choosing assets.'],
        ['title' => 'Plan', 'summary' => 'Choose channels and spell out the offer details.'],
        ['title' => 'Measure', 'summary' => 'Choose one success measure and finish the brief.'],
    ],
    'fields' => [
        [
            'field_key'    => 'campaign_name',
            'label'        => 'Campaign name',
            'type'         => 'text',
            'help'         => 'Name the campaign in plain words.',
            'placeholder'  => 'e.g. BatchBowl free trial launch push',
            'sample_value' => 'BatchBowl free trial launch push',
        ],
        [
            'field_key'    => 'product_offer',
            'label'        => 'Product or offer',
            'type'         => 'text',
            'help'         => 'What are you promoting? Include the offer if known.',
            'placeholder'  => 'e.g. Meal-prep app free trial for new users',
            'sample_value' => 'BatchBowl meal-prep app free trial for new users',
        ],
        [
            'field_key'    => 'audience',
            'label'        => 'Audience',
            'type'         => 'text',
            'help'         => 'Who this campaign is for. Keep it specific and known.',
            'placeholder'  => 'e.g. Busy professionals who meal prep',
            'sample_value' => 'Busy professionals who want meal planning to feel less like a Sunday spreadsheet project',
        ],
        [
            'field_key'    => 'promise',
            'label'        => 'Promise',
            'type'         => 'text',
            'help'         => 'The main benefit you can honestly claim from known facts.',
            'placeholder'  => 'e.g. Plan your week of meals faster',
            'sample_value' => 'Plan a week of meals faster without turning Sunday into a spreadsheet',
        ],
        [
            'field_key'    => 'channels',
            'label'        => 'Channels',
            'type'         => 'textarea',
            'help'         => 'List channels you are considering. One per line. Do not add channels you cannot use.',
            'placeholder'  => "Instagram Reels\nEmail to waitlist\nPartner newsletter",
            'sample_value' => "Instagram Reels\nEmail to waitlist\nPartner newsletter",
        ],
        [
            'field_key'    => 'budget_notes',
            'label'        => 'Budget notes',
            'type'         => 'text',
            'help'         => 'Known spend, limits, or production constraints. If none, say none.',
            'placeholder'  => 'e.g. Small launch budget; reuse founder-shot demo clips',
            'sample_value' => 'Small launch budget; reuse founder-shot demo clips and existing waitlist email tool',
        ],
        [
            'field_key'    => 'success_measure',
            'label'        => 'Success measure',
            'type'         => 'text',
            'help'         => 'One number or observable result you will review.',
            'placeholder'  => 'e.g. 200 free-trial starts within two weeks',
            'sample_value' => '200 free-trial starts within two weeks of the launch push',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-campaign-audience',
            'title'            => 'Name campaign audience',
            'summary'          => 'Frame the campaign and draw a useful boundary around the audience.',
            'why_it_matters'   => 'A campaign aimed at everyone gives the team nothing to choose against. This step names the campaign, buyer, and boundaries before copy starts. Common mistakes: writing for a vague persona, adding demographic facts you do not know, or mixing users and buyers without saying so. Need help if the AI invents research? Retry and tell it the pantry is the only source. You are ready when the audience is narrow enough to reject a bad idea.',
            'unlocks_text'     => 'Approving unlocks the campaign promise.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing the audience section of a campaign brief. Follow Product Law 002: invent nothing. Use only the pantry facts below; do not invent audience research, demographics, objections, budgets, or performance data.

Campaign: {{campaign_name}}
Product/offer: {{product_offer}}
Audience: {{audience}}
Promise draft: {{promise}}
Channels under consideration:
{{channels}}

Produce Markdown with these exact headings:

## Campaign frame
One sentence naming the campaign and what it promotes.

## Audience
One clear audience sentence using the supplied audience.

## Audience facts
Three bullets using only supplied facts or direct implications of the offer.

## Fit boundary
One sentence saying who not to assume this is for.

Keep all four headings in order. Under 220 words.
TXT,
            'example_response' => $campaignAudienceExample,
            'output_sections' => [
                ['key' => 'campaign_frame', 'heading' => 'Campaign frame', 'required' => true],
                ['key' => 'audience', 'heading' => 'Audience', 'required' => true],
                ['key' => 'audience_facts', 'heading' => 'Audience facts', 'required' => true],
                ['key' => 'fit_boundary', 'heading' => 'Fit boundary', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Audience is specific', 'help' => 'The brief names a concrete audience, not everyone.',
                 'evidence_sections' => ['audience']],
                ['label' => 'Facts stay in the pantry', 'help' => 'Audience facts come from supplied campaign information.',
                 'evidence_sections' => ['audience_facts']],
                ['label' => 'Boundary prevents drift', 'help' => 'The campaign says what not to assume.',
                 'evidence_sections' => ['fit_boundary']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'lock-the-promise',
            'title'            => 'Lock the promise',
            'summary'          => 'Turn the known offer and audience pain into one honest campaign promise.',
            'why_it_matters'   => 'The promise is the spine of the campaign. If it overclaims, every asset inherits the risk. Common mistakes: promising outcomes the product has not proven, stuffing in three benefits, or copying category cliches. Need help if the AI sounds too salesy? Retry and require one claim grounded in the supplied offer. You are ready when the promise is clear enough to become a headline.',
            'unlocks_text'     => 'Approving unlocks channel planning.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing the promise section of a campaign brief. Use only the pantry and approved audience frame. Do not invent proof, results, testimonials, or guarantees.

Campaign: {{campaign_name}}
Product/offer: {{product_offer}}
Promise draft: {{promise}}

Approved audience frame:
{{artifact:name-campaign-audience}}

Produce Markdown with these exact headings:

## Core promise
One sentence with the campaign promise.

## Reason to believe
Two sentences explaining why the promise is supportable from known facts.

## Message boundaries
Three bullets naming claims not to make.

## Draft line
One short campaign line in quotes.

Keep all four headings in order. Under 220 words.
TXT,
            'example_response' => $campaignPromiseExample,
            'output_sections' => [
                ['key' => 'core_promise', 'heading' => 'Core promise', 'required' => true],
                ['key' => 'reason_to_believe', 'heading' => 'Reason to believe', 'required' => true],
                ['key' => 'message_boundaries', 'heading' => 'Message boundaries', 'required' => true],
                ['key' => 'draft_line', 'heading' => 'Draft line', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Promise is single-minded', 'help' => 'The core promise says one main thing.',
                 'evidence_sections' => ['core_promise']],
                ['label' => 'Proof is known', 'help' => 'The reason to believe does not invent results or testimonials.',
                 'evidence_sections' => ['reason_to_believe']],
                ['label' => 'Claims have boundaries', 'help' => 'Unsupported claims are named as off-limits.',
                 'evidence_sections' => ['message_boundaries']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'pick-channels',
            'title'            => 'Pick channels',
            'summary'          => 'Give each chosen channel a job without inventing performance forecasts.',
            'why_it_matters'   => 'Channels should earn their place before assets multiply. This step assigns roles using the known audience, promise, and budget notes. Common mistakes: adding trendy channels, pretending reach equals conversion, or inventing CPMs and click rates. Need help if the AI forecasts results? Retry and ban benchmarks unless supplied. You are ready when every channel has a job and a limit.',
            'unlocks_text'     => 'Approving unlocks offer detail writing.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are planning campaign channels. Use only supplied channels, budget notes, and approved artifacts. Do not invent channel performance, audience size, paid media costs, or creator fees.

Channels:
{{channels}}
Budget notes: {{budget_notes}}

Approved audience:
{{artifact:name-campaign-audience}}

Approved promise:
{{artifact:lock-the-promise}}

Produce Markdown with these exact headings:

## Channel plan
A table with columns Channel, Role, Why it fits known facts. Use only listed channels.

## Channel jobs
One short paragraph assigning each channel a job.

## Budget guardrails
One or two sentences using the supplied budget notes.

## What not to assume
One sentence naming performance or spend assumptions to avoid.

Keep all four headings in order. Under 260 words.
TXT,
            'example_response' => $channelPlanExample,
            'output_sections' => [
                ['key' => 'channel_plan', 'heading' => 'Channel plan', 'required' => true],
                ['key' => 'channel_jobs', 'heading' => 'Channel jobs', 'required' => true],
                ['key' => 'budget_guardrails', 'heading' => 'Budget guardrails', 'required' => true],
                ['key' => 'what_not_to_assume', 'heading' => 'What not to assume', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Only listed channels appear', 'help' => 'No surprise channels were added.',
                 'evidence_sections' => ['channel_plan']],
                ['label' => 'Each channel has a job', 'help' => 'The plan explains what each channel is for.',
                 'evidence_sections' => ['channel_jobs']],
                ['label' => 'No performance forecast', 'help' => 'The plan avoids made-up reach, spend, or conversion claims.',
                 'evidence_sections' => ['what_not_to_assume']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'spell-offer-details',
            'title'            => 'Spell offer details',
            'summary'          => 'Write the offer terms and asset notes that keep campaign messages consistent.',
            'why_it_matters'   => 'A strong promise still fails if the offer details are unclear. This step separates confirmed terms from blanks to confirm. Common mistakes: hiding eligibility, inventing trial rules, or giving each channel a different offer. Need help if the AI fills legal or billing gaps? Retry and ask it to mark unknowns as blanks. You are ready when every asset can repeat the same offer line.',
            'unlocks_text'     => 'Approving unlocks the success measure and final brief.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are writing the offer-details section of a campaign brief. Use only the pantry and approved artifacts. Do not invent trial length, discount amount, legal terms, billing rules, or eligibility.

Product/offer: {{product_offer}}
Budget notes: {{budget_notes}}

Approved promise:
{{artifact:lock-the-promise}}

Approved channels:
{{artifact:pick-channels}}

Produce Markdown with these exact headings:

## Offer details
One short paragraph explaining the offer from known facts.

## Required terms
Three to five bullets: confirmed terms first, unknown terms marked "confirm".

## Friction to remove
One or two sentences about what the user should make clear so people can act.

## Asset notes
Three bullets for what every channel asset should include.

Keep all four headings in order. Under 260 words.
TXT,
            'example_response' => $offerDetailsExample,
            'output_sections' => [
                ['key' => 'offer_details', 'heading' => 'Offer details', 'required' => true],
                ['key' => 'required_terms', 'heading' => 'Required terms', 'required' => true],
                ['key' => 'friction_to_remove', 'heading' => 'Friction to remove', 'required' => true],
                ['key' => 'asset_notes', 'heading' => 'Asset notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Offer is consistent', 'help' => 'The same offer can be repeated across channels.',
                 'evidence_sections' => ['offer_details', 'asset_notes']],
                ['label' => 'Unknown terms stay unknown', 'help' => 'Missing trial, billing, or legal details are marked for confirmation.',
                 'evidence_sections' => ['required_terms']],
                ['label' => 'Action is clear', 'help' => 'The brief says what to make obvious so people can act.',
                 'evidence_sections' => ['friction_to_remove']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-success-measure',
            'title'            => 'Write success measure',
            'summary'          => 'Choose one success measure and assemble the final campaign brief.',
            'why_it_matters'   => 'A campaign needs one main measure before results start arriving. This step keeps the team from judging success by whichever number looks best later. Common mistakes: mixing awareness and conversion goals, counting vanity metrics as the main result, or inventing tracking tools. Need help if the AI adds a dashboard you do not have? Retry and require only the supplied measure and known links or tags. You are ready when the brief says what will be counted and when.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are finishing a campaign brief. Use only the pantry and approved artifacts. Do not invent analytics tools, attribution models, benchmarks, or success thresholds.

Campaign: {{campaign_name}}
Success measure: {{success_measure}}

Approved audience:
{{artifact:name-campaign-audience}}

Approved promise:
{{artifact:lock-the-promise}}

Approved channels:
{{artifact:pick-channels}}

Approved offer details:
{{artifact:spell-offer-details}}

Produce Markdown with these exact headings:

## Success measure
One sentence naming the single main measure.

## Counting plan
Two sentences explaining how to count it using only known or generic campaign links/tags.

## Review checkpoint
One sentence naming when to review results.

## Final brief
Six bullets: Campaign, Audience, Promise, Channels, Offer, Measure.

Keep all four headings in order. Under 280 words.
TXT,
            'example_response' => $successMeasureExample,
            'output_sections' => [
                ['key' => 'success_measure', 'heading' => 'Success measure', 'required' => true],
                ['key' => 'counting_plan', 'heading' => 'Counting plan', 'required' => true],
                ['key' => 'review_checkpoint', 'heading' => 'Review checkpoint', 'required' => true],
                ['key' => 'final_brief', 'heading' => 'Final brief', 'required' => true],
            ],
            'checks' => [
                ['label' => 'One main measure', 'help' => 'The campaign is judged by a single primary result.',
                 'evidence_sections' => ['success_measure']],
                ['label' => 'Counting plan is practical', 'help' => 'The measure can be counted without invented tools.',
                 'evidence_sections' => ['counting_plan']],
                ['label' => 'Final brief is complete', 'help' => 'The final brief includes audience, promise, channels, offer, and measure.',
                 'evidence_sections' => ['final_brief']],
            ],
        ],
    ],
];
