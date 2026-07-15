<?php

declare(strict_types=1);

/**
 * Price Your Offer — executable Cookbook for start-grow-business.
 *
 * Inspired by SBA/SBDC-style public pricing teaching: cost floor plus value.
 * Ideas only. All copy, examples, and prompts are original SousMeow work.
 * Built for Product Law 002 by treating the pantry as known costs, known
 * value proof, and stated constraints; no buyer reactions or market data are
 * invented.
 */

$priceMemoExample = <<<'MD'
## Price frame
Fix Your Focus Friday is a weekend workshop for freelancers who want a short reset for focus habits.

## Cost floor
Known costs are $300 for the room, $120 for worksheets and supplies, and $80 for checkout fees and snacks. The cash floor is $500 before paying the owner for their time. At 10 seats, that is $50 per seat before profit.

## Target price
Charge $149 per seat as the target price. It clears the known cost floor at the expected 10 seats and fits the stated value proof: freelancers already pay for tools and coaching to protect billable time.

## How to say it
"Fix Your Focus Friday is $149 for a small-group workshop that helps freelancers reset their week, leave with a focus plan, and stop losing billable hours to scattered work."

## Retry note
If this price feels hard to say out loud, rerun with tighter constraints or add real competitor/context facts. Do not lower it because of guessed buyer reactions.
MD;

return [
    'slug'                => 'price-your-offer',
    'title'               => 'Price Your Offer',
    'tagline'             => 'Set a price you can say out loud using costs and value you already know.',
    'description'         => "Pricing gets noisy when guessed buyer reactions replace known facts. This Cookbook turns one real offer into a short price decision memo: cost floor, target price, and the words you can use to say it. Enter only costs, value proof, and constraints you know. Every prompt is told to invent nothing about demand, competitors, or willingness to pay. You leave with a price you can explain without pretending the market has spoken.",
    'primary_category'    => 'start-grow-business',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Solopreneurs pricing a real offer for the first time or a refresh',
    'outcome'             => 'a price decision memo with floor, target, and how to say it',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'sage',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 15,
    'demo_completed_runs' => 305,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 13,
    'stages' => [
        ['title' => 'Decide', 'summary' => 'Name costs, value, and the price you will charge.'],
    ],
    'fields' => [
        [
            'field_key'    => 'offer_name',
            'label'        => 'Offer name',
            'type'         => 'text',
            'help'         => 'Name the real thing someone can buy.',
            'placeholder'  => 'e.g. Fix Your Focus Friday',
            'sample_value' => 'Fix Your Focus Friday',
        ],
        [
            'field_key'    => 'who_pays',
            'label'        => 'Who pays?',
            'type'         => 'text',
            'help'         => 'Describe the buyer you are actually serving. No imagined segment research.',
            'placeholder'  => 'e.g. Freelancers who need a focus reset',
            'sample_value' => 'Freelancers who want a short reset for focus habits',
        ],
        [
            'field_key'    => 'cost_floor',
            'label'        => 'Known cost floor',
            'type'         => 'textarea',
            'help'         => 'List real costs, minimum seats, fees, materials, or time boundaries. Use numbers you already know.',
            'placeholder'  => "Room: $300\nMaterials: $120\nPayment fees/snacks: $80\nExpected seats: 10",
            'sample_value' => "Room: $300\nWorksheets and supplies: $120\nCheckout fees and snacks: $80\nExpected minimum: 10 seats",
        ],
        [
            'field_key'    => 'value_proof',
            'label'        => 'Known value proof',
            'type'         => 'textarea',
            'help'         => 'Facts that support value: past results, buyer pain, comparable spending, or direct quotes. Do not guess demand.',
            'placeholder'  => 'e.g. Freelancers already pay for tools and coaching to protect billable time',
            'sample_value' => 'Freelancers already pay for tools and coaching to protect billable time. Past coaching clients say scattered work costs them billable hours.',
        ],
        [
            'field_key'    => 'constraints',
            'label'        => 'Pricing constraints',
            'type'         => 'text',
            'help'         => 'Any real boundary: must be easy to say, limited seats, launch discount, minimum margin, or none.',
            'placeholder'  => 'e.g. Small group; price must be simple to say out loud',
            'sample_value' => 'Small group; price must be simple to say out loud. No discounts for the first run.',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'decide-the-price',
            'title'            => 'Decide the price',
            'summary'          => 'Turn known costs, value proof, and constraints into a floor, target price, and spoken price line.',
            'why_it_matters'   => 'A price should clear the floor before it tries to feel brave. This step keeps known costs and known value proof visible while you choose. Common mistakes: ignoring fees, pricing from fear, copying a competitor you have not researched, or inventing what buyers will pay. Need help if the AI guesses demand? Retry and tell it to use only the pantry facts. You are ready when you can say the price without adding imaginary evidence.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 15,
            'prompt_template'  => <<<'TXT'
You are a pricing coach. Follow Product Law 002: invent nothing. Treat the pantry as the user's known costs, known value proof, buyer description, and constraints. Do not invent demand, competitors, conversion rates, margins, or buyer reactions.

Offer: {{offer_name}}
Who pays: {{who_pays}}
Known cost floor:
{{cost_floor}}
Known value proof:
{{value_proof}}
Constraints: {{constraints}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Price frame
One sentence naming the offer and buyer.

## Cost floor
Restate the known costs and any per-unit floor you can calculate from supplied numbers. If a number is missing, say what is missing instead of inventing it.

## Target price
Name one target price or a narrow range, using only the cost floor, value proof, and constraints. Explain the tradeoff in two sentences.

## How to say it
One quote the owner can say to a buyer. Include the price and value in plain language.

## Retry note
One sentence explaining what to add or tighten if the price feels unsupported. No guessed market claims.

Keep all five headings in order. Under 300 words.
TXT,
            'example_response' => $priceMemoExample,
            'output_sections' => [
                ['key' => 'price_frame', 'heading' => 'Price frame', 'required' => true],
                ['key' => 'cost_floor', 'heading' => 'Cost floor', 'required' => true],
                ['key' => 'target_price', 'heading' => 'Target price', 'required' => true],
                ['key' => 'how_to_say_it', 'heading' => 'How to say it', 'required' => true],
                ['key' => 'retry_note', 'heading' => 'Retry note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Floor uses known costs', 'help' => 'The floor comes from supplied costs, not guessed overhead.',
                 'evidence_sections' => ['cost_floor']],
                ['label' => 'Target is explainable', 'help' => 'The target price connects to the floor, value proof, and constraints.',
                 'evidence_sections' => ['target_price']],
                ['label' => 'No invented market claims', 'help' => 'The memo does not claim buyers will pay, convert, or react unless supplied.',
                 'evidence_sections' => ['target_price', 'retry_note']],
            ],
        ],
    ],
];
