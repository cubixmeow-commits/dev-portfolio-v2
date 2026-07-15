<?php

declare(strict_types=1);

/**
 * Make a Criteria Decision — executable Cookbook for planning-productivity.
 *
 * Inspired by public university advising / structured decision-making
 * worksheets (options, criteria, weigh, choose). Ideas only. Built to pass
 * Product Law 002 by asking for known options and trusted criteria, then
 * keeping unsupported certainty out of the choice. All copy, examples, and
 * prompts are original SousMeow work.
 */

$frameOptionsExample = <<<'MD'
## Decision frame
Choose the next 90-day work path: stay at the current job, accept Offer B, or freelance for 90 days.

## Options on the table
| Option | What it means | Known cautions |
|--------|---------------|----------------|
| A | Stay at current job | Predictable, but growth has slowed |
| B | Accept Offer B | New team and higher pay, but unknown manager fit |
| C | Freelance for 90 days | More autonomy, but income uncertainty |

## Criteria to use
1. Income stability
2. Learning speed
3. Schedule control
4. Health and stress
5. Long-term career fit

## Non-negotiables
Must keep health stable and cover baseline monthly expenses. Any option that breaks either rule should not win on score alone.
MD;

$scoreChooseExample = <<<'MD'
## Scored table
| Option | Income stability | Learning speed | Schedule control | Health and stress | Long-term career fit | Total |
|--------|------------------|----------------|------------------|-------------------|----------------------|-------|
| A: Stay at current job | 4 | 2 | 3 | 3 | 2 | 14 |
| B: Accept Offer B | 4 | 4 | 2 | 3 | 4 | 17 |
| C: Freelance for 90 days | 2 | 4 | 5 | 2 | 3 | 16 |

## Choice
Choose Offer B, unless a final manager conversation reveals a non-negotiable health or schedule risk. It scores highest while still protecting baseline income.

## Why this choice fits the criteria
Offer B balances income stability, learning speed, and long-term career fit better than staying put. Freelancing wins on control but carries the largest income and stress uncertainty for this 90-day window.

## First next step
Schedule one clarifying call with Offer B's manager and ask about weekly expectations, decision authority, and how urgent work is handled after hours.
MD;

return [
    'slug'                => 'make-a-criteria-decision',
    'title'               => 'Make a Criteria Decision',
    'tagline'             => 'Choose between real options using criteria you already trust.',
    'description'         => "Hard choices get heavier when every option stays in your head. This Cookbook turns two or three real options into a small criteria table, checks them against non-negotiables, and names the first next step. Enter only options and criteria you already trust. Every prompt is told to invent nothing about hidden outcomes, motives, or future guarantees. You leave with framed options, a scored table, a choice, and one next action.",
    'primary_category'    => 'planning-productivity',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Anyone stuck between two or three real options and delaying a call',
    'outcome'             => 'framed options and criteria, scored table, choice, and first next step',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'ochre',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 20,
    'demo_completed_runs' => 441,
    'demo_avg_rating'     => 4.9,
    'sort_order'          => 11,
    'stages' => [
        ['title' => 'Frame', 'summary' => 'Name the options and criteria before scoring anything.'],
        ['title' => 'Decide', 'summary' => 'Score the options, choose, and name the first next step.'],
    ],
    'fields' => [
        [
            'field_key'    => 'decision_title',
            'label'        => 'Decision title',
            'type'         => 'text',
            'help'         => 'Name the real choice in one line.',
            'placeholder'  => 'e.g. Choose my next 90-day work path',
            'sample_value' => 'Choose my next 90-day work path',
        ],
        [
            'field_key'    => 'option_a',
            'label'        => 'Option A',
            'type'         => 'textarea',
            'help'         => 'One real option. Include known pros or cautions if you have them.',
            'placeholder'  => 'e.g. Stay at my current job',
            'sample_value' => 'Stay at my current job. Predictable, but growth has slowed.',
        ],
        [
            'field_key'    => 'option_b',
            'label'        => 'Option B',
            'type'         => 'textarea',
            'help'         => 'Second real option. Do not include imaginary outcomes.',
            'placeholder'  => 'e.g. Accept Offer B',
            'sample_value' => 'Accept Offer B. New team and higher pay, but manager fit is still unknown.',
        ],
        [
            'field_key'    => 'option_c',
            'label'        => 'Option C (optional)',
            'type'         => 'textarea',
            'help'         => 'Optional third option. Leave blank if this is a two-option decision.',
            'placeholder'  => 'e.g. Freelance for 90 days',
            'sample_value' => 'Freelance for 90 days. More autonomy, but income is uncertain.',
        ],
        [
            'field_key'    => 'criteria_list',
            'label'        => 'Criteria',
            'type'         => 'textarea',
            'help'         => 'List the criteria you already trust. One per line. Keep it to three to six.',
            'placeholder'  => "Income stability\nLearning speed\nSchedule control",
            'sample_value' => "Income stability\nLearning speed\nSchedule control\nHealth and stress\nLong-term career fit",
        ],
        [
            'field_key'    => 'non_negotiables',
            'label'        => 'Non-negotiables',
            'type'         => 'textarea',
            'help'         => 'Rules an option must not break. If none, say none.',
            'placeholder'  => 'e.g. Must cover rent and protect sleep',
            'sample_value' => 'Must keep health stable and cover baseline monthly expenses.',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'frame-options-and-criteria',
            'title'            => 'Frame options and criteria',
            'summary'          => 'Put the real options, trusted criteria, and non-negotiables into one clean frame.',
            'why_it_matters'   => 'Decisions feel bigger when options and criteria blur together. This step separates what is on the table from how you will judge it. Common mistakes: adding a fantasy option, inventing future outcomes, or treating a non-negotiable as just another preference. Need help if the AI adds details? Paste again and require only the words you supplied. You are ready when the frame names the choice without pretending the future is known.',
            'unlocks_text'     => 'Approving unlocks the scored table and recommendation.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are a structured decision coach. Follow Product Law 002: reduce cognitive load and use ONLY the user's options, criteria, and non-negotiables. Do not invent outcomes, salaries, motives, risks, timelines, or hidden information. If Option C is blank, make a two-option frame.

Decision title: {{decision_title}}

Option A:
{{option_a}}

Option B:
{{option_b}}

Option C, if any:
{{option_c}}

Criteria:
{{criteria_list}}

Non-negotiables:
{{non_negotiables}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Decision frame
One sentence naming the choice.

## Options on the table
A table with one row per real option. Columns: Option, What it means, Known cautions. Use only supplied facts.

## Criteria to use
A numbered list using the criteria supplied by the user. Do not add criteria.

## Non-negotiables
Restate the user's non-negotiables and explain that any option breaking them should not win on score alone.

Keep all four headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $frameOptionsExample,
            'output_sections' => [
                ['key' => 'decision_frame', 'heading' => 'Decision frame', 'required' => true],
                ['key' => 'options_on_table', 'heading' => 'Options on the table', 'required' => true],
                ['key' => 'criteria_to_use', 'heading' => 'Criteria to use', 'required' => true],
                ['key' => 'non_negotiables', 'heading' => 'Non-negotiables', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Only real options appear', 'help' => 'The frame uses Option A, Option B, and Option C only if supplied.',
                 'evidence_sections' => ['options_on_table']],
                ['label' => 'Criteria are user supplied', 'help' => 'No surprise criteria were added.',
                 'evidence_sections' => ['criteria_to_use']],
                ['label' => 'Non-negotiables are protected', 'help' => 'Hard rules are not reduced to ordinary preferences.',
                 'evidence_sections' => ['non_negotiables']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'score-and-choose',
            'title'            => 'Score and choose',
            'summary'          => 'Score the approved options against criteria, choose cautiously, and name one first step.',
            'why_it_matters'   => 'A criteria table makes tradeoffs visible without pretending the score is destiny. This step turns delay into a provisional choice and a next action. Common mistakes: treating math as certainty, ignoring non-negotiables, or inventing evidence to justify the preferred option. Need help if the AI sounds absolute? Paste again and ask for a choice with caveats. You are ready when you can take one next step today.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 12,
            'prompt_template'  => <<<'TXT'
You are helping the user make a criteria decision. Use ONLY the approved frame below. Do not invent outcomes, facts, motives, or guarantees. Score each option 1 to 5 for each criterion based on the supplied facts; if evidence is thin, use a cautious middle score and say why in the choice explanation.

Approved options and criteria:
{{artifact:frame-options-and-criteria}}

Produce Markdown with these exact headings:

## Scored table
A table with one row per option and one column per criterion, plus Total. Use 1 = weak fit, 3 = mixed or unknown, 5 = strong fit.

## Choice
Name the recommended option in one or two sentences. Respect non-negotiables before total score.

## Why this choice fits the criteria
One short paragraph explaining the key tradeoffs. Do not pretend the choice guarantees an outcome.

## First next step
One concrete next action the user can take next. No multi-week plan.

Keep all four headings in order. Under 280 words. Invent nothing.
TXT,
            'example_response' => $scoreChooseExample,
            'output_sections' => [
                ['key' => 'scored_table', 'heading' => 'Scored table', 'required' => true],
                ['key' => 'choice', 'heading' => 'Choice', 'required' => true],
                ['key' => 'why_choice_fits', 'heading' => 'Why this choice fits the criteria', 'required' => true],
                ['key' => 'first_next_step', 'heading' => 'First next step', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Scores cover every option and criterion', 'help' => 'The table includes each approved option and each approved criterion.',
                 'evidence_sections' => ['scored_table']],
                ['label' => 'Choice respects non-negotiables', 'help' => 'The recommendation does not let a high score override a hard rule.',
                 'evidence_sections' => ['choice']],
                ['label' => 'Next step is immediate', 'help' => 'The first step is a single concrete action, not a vague plan.',
                 'evidence_sections' => ['first_next_step']],
            ],
        ],
    ],
];
