<?php

declare(strict_types=1);

/**
 * Document a Simple Process - executable Cookbook for planning-productivity.
 *
 * Inspired by public SOP and process documentation teaching. Ideas only;
 * original SousMeow copy, examples, and prompts.
 */

$nameTriggerOwnerExample = <<<'MD'
## Process snapshot
Weekly Harbor Thread order packing is the Thursday routine for turning closed online orders into labeled parcels.

## Trigger
Start after online orders close at noon on Thursday and the order list is ready to print.

## Owner and backup
Mina, the shop assistant, runs the process. Lee can run it as backup if Mina is out.

## Done signal
The process is done when every paid order is packed, labeled, marked ready for pickup, and placed on the outgoing shelf.
MD;

$rawStepsExample = <<<'MD'
## Raw steps
1. Open the order list after Thursday noon.
2. Print the packing slips.
3. Pull each fabric bundle from the stock shelf.
4. Match each bundle to its packing slip.
5. Wrap the bundle in tissue.
6. Add the thank-you card.
7. Seal the mailer.
8. Print and attach the shipping label.
9. Mark the order ready for pickup.
10. Put the parcel on the outgoing shelf.

## Inputs needed
- Closed Thursday order list.
- Packing slips.
- Fabric bundles from stock shelves.
- Tissue, thank-you cards, mailers, labels.

## Tools touched
Shopify order list, printer, label printer, packing table, stock shelves, outgoing shelf.

## Gaps to confirm
- What to do if an order is missing fabric.
- Where to record a damaged item.
- Who gets told when labels fail to print.
MD;

$decisionsHandOffsExample = <<<'MD'
## Decision points
- If a packing slip does not match the shelf item, pause that order.
- If fabric is damaged, do not pack it.
- If the label printer fails, stop labeling until Lee checks it.

## Hand-offs
- Mina hands labeled parcels to the pickup shelf.
- Lee handles missing or damaged stock questions.
- The carrier takes only parcels from the outgoing shelf.

## Waiting points
- Wait for the order list to close before printing slips.
- Wait for Lee before replacing damaged fabric.
- Wait for label printer help before marking a parcel ready.

## Assumptions to verify
The current steps assume all paid Thursday orders appear in Shopify, all stock locations are correct, and the outgoing shelf is cleared before packing starts.
MD;

$clearStepsExample = <<<'MD'
## Clear procedure
1. After Thursday noon, open the closed Shopify order list.
2. Print one packing slip for each paid order.
3. For one order at a time, pull the fabric bundle named on the slip.
4. Compare the slip, fabric name, and quantity before wrapping.
5. Wrap the bundle in tissue and add one thank-you card.
6. Place the wrapped bundle in a mailer and seal it.
7. Print the shipping label for that order.
8. Attach the label to the matching mailer.
9. Mark the order ready for pickup.
10. Put the parcel on the outgoing shelf.

## Operator notes
Work one order at a time so slips and labels do not separate from parcels. Pause any order with a mismatch instead of guessing.

## Order check
The list starts only after orders close, then moves from slip to stock to wrap to label to shelf.

## Still needs a fact
The process owner should add the exact place to record damaged or missing stock.
MD;

$checksExceptionsExample = <<<'MD'
## Quality checks
- Slip, fabric name, and quantity match before wrapping.
- Mailer contains the fabric bundle and one thank-you card.
- Shipping label is attached to the matching sealed mailer.
- Order is marked ready only after the label is attached.

## Exceptions
- Missing fabric: set the slip aside and ask Lee before packing.
- Damaged fabric: do not pack; keep the item with the slip.
- Label printer problem: stop labeling and ask Lee to check the printer.

## Escalation note
Escalate only the paused order or broken tool. Continue packing other clear orders if slips, stock, and labels still match.

## Evidence to leave
At the end, the outgoing shelf holds labeled parcels and Shopify shows those orders marked ready for pickup.
MD;

$quickStartExample = <<<'MD'
## Quick start
Use this when you already know the packing table, Shopify order list, and outgoing shelf.

1. Start after Thursday orders close at noon.
2. Print packing slips.
3. Pack one order at a time from slip to shelf.
4. Pause mismatches instead of guessing.
5. Finish when all clear orders are labeled, marked ready, and on the outgoing shelf.

## Before you begin
Check that slips, tissue, cards, mailers, labels, and the label printer are ready.

## Pause and ask
Ask Lee if fabric is missing, fabric is damaged, or the label printer fails.

## Done in one sentence
Every paid Thursday order that can be packed safely is labeled, marked ready, and sitting on the outgoing shelf.
MD;

$onePagerExample = <<<'MD'
## Process one-pager
Process: Weekly Harbor Thread order packing
Owner: Mina, shop assistant
Backup: Lee
Trigger: Thursday after online orders close at noon
Done: Paid orders are packed, labeled, marked ready, and on the outgoing shelf.

Steps:
1. Open the closed Shopify order list.
2. Print packing slips.
3. Pull and check one order at a time.
4. Wrap fabric with tissue and a thank-you card.
5. Seal the mailer.
6. Print and attach the matching label.
7. Mark the order ready for pickup.
8. Place it on the outgoing shelf.

## Run checklist
- [ ] Order list is closed.
- [ ] Slip, fabric, and quantity match.
- [ ] Label matches the mailer.
- [ ] Problem orders are paused for Lee.
- [ ] Ready orders are on the outgoing shelf.

## Hand-off notes
Lee handles missing stock, damaged fabric, and label printer problems. The carrier should take only parcels from the outgoing shelf.

## Update note
Add the exact damaged-stock recording location after Lee confirms it.
MD;

return [
    'slug'                => 'document-a-simple-process',
    'title'               => 'Document a Simple Process',
    'tagline'             => 'Write a process someone else could follow without pinging you.',
    'description'         => "Simple processes fail when the steps live in someone's head. This Cookbook helps you capture the trigger, owner, raw steps, decisions, hand-offs, checks, exceptions, and one-page handoff notes. Enter only what you know about the process, tools, and done signal. Each recipe tells the assistant to invent nothing and to mark gaps instead of filling them with guesses. You leave with a compact process one-pager someone else can follow.",
    'primary_category'    => 'planning-productivity',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Anyone documenting a repeatable work, home, or team process for another person to run',
    'outcome'             => 'process snapshot, raw steps, decisions, hand-offs, clear procedure, checks, exceptions, quick start, and one-page SOP',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'ochre',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 30,
    'demo_completed_runs' => 248,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 22,
    'stages' => [
        ['title' => 'Capture', 'summary' => 'Name the process, gather raw steps, and expose decisions.'],
        ['title' => 'Clarify', 'summary' => 'Rewrite the steps and add checks, exceptions, and pause points.'],
        ['title' => 'Hand off', 'summary' => 'Create a quick start and a one-page process handoff.'],
    ],
    'fields' => [
        [
            'field_key'    => 'process_name',
            'label'        => 'Process name',
            'type'         => 'text',
            'help'         => 'Give the process a plain name someone would recognize.',
            'placeholder'  => 'e.g. Weekly order packing',
            'sample_value' => 'Weekly Harbor Thread order packing',
        ],
        [
            'field_key'    => 'who_runs_it',
            'label'        => 'Who runs it?',
            'type'         => 'text',
            'help'         => 'Name the owner and backup if known. Do not invent a backup.',
            'placeholder'  => 'e.g. Mina runs it; Lee backs up',
            'sample_value' => 'Mina, the shop assistant, runs it. Lee can back up if Mina is out.',
        ],
        [
            'field_key'    => 'trigger',
            'label'        => 'What starts the process?',
            'type'         => 'text',
            'help'         => 'The event, time, request, or signal that tells someone to begin.',
            'placeholder'  => 'e.g. Every Thursday after online orders close',
            'sample_value' => 'Every Thursday after online orders close at noon and the order list is ready to print.',
        ],
        [
            'field_key'    => 'steps_you_know',
            'label'        => 'Steps you already know',
            'type'         => 'textarea',
            'help'         => 'Messy is fine. Put one action or memory per line. Mark unsure items.',
            'placeholder'  => "Print slips\nPull items\nPack boxes\nMark ready",
            'sample_value' => "Open Shopify after orders close\nPrint packing slips\nPull fabric bundles from shelf\nMatch bundle to slip\nWrap in tissue\nAdd thank-you card\nSeal mailer\nPrint shipping label\nMark ready for pickup\nPut on outgoing shelf\nUnsure: damaged stock note location",
        ],
        [
            'field_key'    => 'tools_used',
            'label'        => 'Tools, spaces, or materials used',
            'type'         => 'text',
            'help'         => 'Apps, forms, shelves, equipment, supplies, or rooms touched during the process.',
            'placeholder'  => 'e.g. Shopify, label printer, packing table',
            'sample_value' => 'Shopify order list, printer, label printer, packing table, stock shelves, tissue, thank-you cards, mailers, outgoing shelf',
        ],
        [
            'field_key'    => 'definition_of_done',
            'label'        => 'Definition of done',
            'type'         => 'textarea',
            'help'         => 'What visible state proves the process is complete?',
            'placeholder'  => 'e.g. Every order is labeled and on the outgoing shelf',
            'sample_value' => 'Every paid order is packed, labeled, marked ready for pickup, and placed on the outgoing shelf.',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-trigger-and-owner',
            'title'            => 'Name trigger and owner',
            'summary'          => 'Create a small process snapshot with the owner, start signal, and done signal.',
            'why_it_matters'   => 'A process handoff starts failing when the runner does not know when to begin, who owns the work, or when to stop. This step locks those boundaries before step-writing begins. Common mistakes: naming a department instead of a person, inventing a backup, or turning done into a vague feeling. Need help if the AI fills gaps? Paste again and require unknowns to be marked as unknown. You are ready when the process has a clear start and finish.',
            'unlocks_text'     => 'Approving unlocks raw step capture.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are documenting a simple process. Use ONLY the facts below. Invent nothing; mark missing facts as unknown.

Process name: {{process_name}}
Who runs it: {{who_runs_it}}
Trigger: {{trigger}}
Definition of done: {{definition_of_done}}

Return Markdown with these headings:
## Process snapshot
One or two plain sentences naming the process.
## Trigger
When to start. Use only the trigger given.
## Owner and backup
Who runs it and any backup only if provided.
## Done signal
The visible finish state.

Keep the four headings in order. Under 180 words.
TXT,
            'example_response' => $nameTriggerOwnerExample,
            'output_sections' => [
                ['key' => 'process_snapshot', 'heading' => 'Process snapshot', 'required' => true],
                ['key' => 'trigger', 'heading' => 'Trigger', 'required' => true],
                ['key' => 'owner_backup', 'heading' => 'Owner and backup', 'required' => true],
                ['key' => 'done_signal', 'heading' => 'Done signal', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Start signal is clear', 'help' => 'A runner knows exactly when the process begins.',
                 'evidence_sections' => ['trigger']],
                ['label' => 'Owner is named from facts', 'help' => 'The owner or backup is not invented.',
                 'evidence_sections' => ['owner_backup']],
                ['label' => 'Done is visible', 'help' => 'The finish state can be checked without guessing.',
                 'evidence_sections' => ['done_signal']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'list-raw-steps',
            'title'            => 'List raw steps',
            'summary'          => 'Turn messy remembered actions into a first pass of steps, inputs, tools, and gaps.',
            'why_it_matters'   => 'Raw capture keeps knowledge from disappearing before it is polished. This step preserves what is known and separates it from what still needs confirmation. Common mistakes: cleaning too soon, inventing missing steps, or hiding unknowns. Need help if the AI over-organizes? Paste again and ask for a raw list only. You are ready when every known action appears somewhere.',
            'unlocks_text'     => 'Approving unlocks decisions and hand-offs.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are capturing raw process steps. Use ONLY the pantry facts and approved snapshot. Do not invent missing actions or tools.

Process name: {{process_name}}
Steps known:
{{steps_you_know}}
Tools used: {{tools_used}}

Approved snapshot:
{{artifact:name-trigger-and-owner}}

Return Markdown with these headings:
## Raw steps
Number the known actions in likely order. Keep unsure items visible.
## Inputs needed
Bullets for information, materials, or items needed before/during the process.
## Tools touched
List only the tools, spaces, or materials provided.
## Gaps to confirm
Questions or unknowns from the facts. Do not answer them.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $rawStepsExample,
            'output_sections' => [
                ['key' => 'raw_steps', 'heading' => 'Raw steps', 'required' => true],
                ['key' => 'inputs_needed', 'heading' => 'Inputs needed', 'required' => true],
                ['key' => 'tools_touched', 'heading' => 'Tools touched', 'required' => true],
                ['key' => 'gaps_to_confirm', 'heading' => 'Gaps to confirm', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Known actions are captured', 'help' => 'The raw list reflects the steps you entered.',
                 'evidence_sections' => ['raw_steps']],
                ['label' => 'Tools match the pantry', 'help' => 'No unprovided app, form, or equipment appears.',
                 'evidence_sections' => ['tools_touched']],
                ['label' => 'Unknowns stay unknown', 'help' => 'Gaps are listed as questions instead of guesses.',
                 'evidence_sections' => ['gaps_to_confirm']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'mark-decisions-and-hand-offs',
            'title'            => 'Mark decisions and hand-offs',
            'summary'          => 'Find the places where the runner must decide, pause, or pass work to someone else.',
            'why_it_matters'   => 'People get stuck in processes at decision points, not at obvious steps. This recipe makes choices and hand-offs visible without making up policies. Common mistakes: inventing approval rules, hiding wait states, or telling the runner to guess. Need help if the AI creates a policy? Paste again and ask it to phrase uncertain items as questions. You are ready when pause points are easy to spot.',
            'unlocks_text'     => 'Approving unlocks the clearer procedure rewrite.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are marking decisions and hand-offs in a process. Use ONLY the pantry facts and raw steps. Invent no policies, roles, or approvals.

Who runs it: {{who_runs_it}}
Definition of done: {{definition_of_done}}
Tools used: {{tools_used}}

Approved raw steps:
{{artifact:list-raw-steps}}

Return Markdown with these headings:
## Decision points
Bullets for moments when the runner must choose, check, pause, or ask.
## Hand-offs
Bullets for work passed to a person, shelf, system, or next actor from the facts.
## Waiting points
Where the process must wait before continuing.
## Assumptions to verify
Likely assumptions that need confirmation. Do not resolve them.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $decisionsHandOffsExample,
            'output_sections' => [
                ['key' => 'decision_points', 'heading' => 'Decision points', 'required' => true],
                ['key' => 'hand_offs', 'heading' => 'Hand-offs', 'required' => true],
                ['key' => 'waiting_points', 'heading' => 'Waiting points', 'required' => true],
                ['key' => 'assumptions_verify', 'heading' => 'Assumptions to verify', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Decisions are explicit', 'help' => 'The runner can see where judgment or a pause is needed.',
                 'evidence_sections' => ['decision_points']],
                ['label' => 'Hand-offs have recipients', 'help' => 'Each hand-off goes to a person, place, or system named by the facts.',
                 'evidence_sections' => ['hand_offs']],
                ['label' => 'Assumptions are not treated as facts', 'help' => 'Unverified details remain marked for confirmation.',
                 'evidence_sections' => ['assumptions_verify']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'rewrite-clear-steps',
            'title'            => 'Rewrite clear steps',
            'summary'          => 'Convert the raw list into a clear ordered procedure with notes and unresolved facts.',
            'why_it_matters'   => 'A handoff needs action sentences, not memories. This step rewrites the process so another person can run it in order while still seeing what is not known. Common mistakes: combining too many actions, adding new tools, or smoothing away uncertainty. Need help if steps get bloated? Paste again and ask for one action per line. You are ready when each step starts with a verb.',
            'unlocks_text'     => 'Approving unlocks quality checks and exceptions.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are rewriting a simple process into clear steps. Use ONLY approved artifacts and pantry facts. Invent nothing; keep unknowns visible.

Process name: {{process_name}}
Definition of done: {{definition_of_done}}

Approved snapshot:
{{artifact:name-trigger-and-owner}}

Approved raw steps:
{{artifact:list-raw-steps}}

Approved decisions and hand-offs:
{{artifact:mark-decisions-and-hand-offs}}

Return Markdown with these headings:
## Clear procedure
Numbered steps. One action per step. Start each with a verb where natural.
## Operator notes
Short notes that prevent common mix-ups from the approved decisions.
## Order check
One or two sentences explaining the sequence.
## Still needs a fact
Any unresolved fact the process owner must fill in.

Keep the four headings in order. Under 320 words.
TXT,
            'example_response' => $clearStepsExample,
            'output_sections' => [
                ['key' => 'clear_procedure', 'heading' => 'Clear procedure', 'required' => true],
                ['key' => 'operator_notes', 'heading' => 'Operator notes', 'required' => true],
                ['key' => 'order_check', 'heading' => 'Order check', 'required' => true],
                ['key' => 'still_needs_fact', 'heading' => 'Still needs a fact', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Steps are runnable', 'help' => 'A new runner can follow the numbered list in order.',
                 'evidence_sections' => ['clear_procedure']],
                ['label' => 'Notes prevent known mix-ups', 'help' => 'Operator notes come from decisions or hand-offs already approved.',
                 'evidence_sections' => ['operator_notes']],
                ['label' => 'Missing fact remains visible', 'help' => 'The rewrite does not pretend an unknown was solved.',
                 'evidence_sections' => ['still_needs_fact']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'add-checks-and-exceptions',
            'title'            => 'Add checks and exceptions',
            'summary'          => 'Add quality checks, exception paths, escalation notes, and evidence to leave behind.',
            'why_it_matters'   => 'A process document should tell someone how to avoid mistakes and what to do when normal steps break. This step creates guardrails from known facts instead of inventing policy. Common mistakes: writing vague "be careful" checks, solving exceptions with unknown authority, or requiring evidence that is not available. Need help if the AI invents escalation? Paste again and ask for pause-and-ask wording. You are ready when every exception has a safe next move.',
            'unlocks_text'     => 'Approving unlocks the quick-start handoff.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are adding checks and exceptions to a simple process. Use ONLY pantry facts and approved procedure. Do not invent rules, forms, or authority.

Who runs it: {{who_runs_it}}
Definition of done: {{definition_of_done}}
Tools used: {{tools_used}}

Approved clear procedure:
{{artifact:rewrite-clear-steps}}

Approved decisions and hand-offs:
{{artifact:mark-decisions-and-hand-offs}}

Return Markdown with these headings:
## Quality checks
Three to five checks the runner can verify while doing the process.
## Exceptions
Three to five if/then or pause rules for known problems.
## Escalation note
Who or what to pause for, only if provided. Otherwise say the owner must fill it in.
## Evidence to leave
Visible proof the process reached done.

Keep the four headings in order. Under 280 words.
TXT,
            'example_response' => $checksExceptionsExample,
            'output_sections' => [
                ['key' => 'quality_checks', 'heading' => 'Quality checks', 'required' => true],
                ['key' => 'exceptions', 'heading' => 'Exceptions', 'required' => true],
                ['key' => 'escalation_note', 'heading' => 'Escalation note', 'required' => true],
                ['key' => 'evidence_leave', 'heading' => 'Evidence to leave', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Checks are observable', 'help' => 'Each check can be verified during the process.',
                 'evidence_sections' => ['quality_checks']],
                ['label' => 'Exceptions avoid guessing', 'help' => 'Problem paths pause, ask, or use known hand-offs.',
                 'evidence_sections' => ['exceptions', 'escalation_note']],
                ['label' => 'Evidence matches done', 'help' => 'The proof lines up with the definition of done.',
                 'evidence_sections' => ['evidence_leave']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-quick-start',
            'title'            => 'Write quick start',
            'summary'          => 'Create a compact starter guide for someone who needs to run the process today.',
            'why_it_matters'   => 'A long SOP helps later, but a new runner first needs the fastest safe overview. This step condenses the process without dropping the trigger, pause points, or done signal. Common mistakes: omitting exceptions, adding training details not provided, or making the quick start too long. Need help if it becomes a manual? Paste again and cap it to five main moves. You are ready when a backup can start without pinging you for basics.',
            'unlocks_text'     => 'Approving unlocks the final one-page process pack.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are writing a quick start for a simple process. Use ONLY approved artifacts. Invent no new training, tools, roles, or timing.

Process name: {{process_name}}
Who runs it: {{who_runs_it}}

Approved snapshot:
{{artifact:name-trigger-and-owner}}

Approved clear procedure:
{{artifact:rewrite-clear-steps}}

Approved checks and exceptions:
{{artifact:add-checks-and-exceptions}}

Return Markdown with these headings:
## Quick start
Five to seven short numbered moves for a backup runner.
## Before you begin
Readiness checks from approved tools, inputs, or trigger.
## Pause and ask
When to stop instead of guessing.
## Done in one sentence
Restate the completion signal.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $quickStartExample,
            'output_sections' => [
                ['key' => 'quick_start', 'heading' => 'Quick start', 'required' => true],
                ['key' => 'before_begin', 'heading' => 'Before you begin', 'required' => true],
                ['key' => 'pause_ask', 'heading' => 'Pause and ask', 'required' => true],
                ['key' => 'done_sentence', 'heading' => 'Done in one sentence', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Backup can begin', 'help' => 'The quick start includes the trigger and first moves.',
                 'evidence_sections' => ['quick_start', 'before_begin']],
                ['label' => 'Pause points are preserved', 'help' => 'The guide says when not to guess.',
                 'evidence_sections' => ['pause_ask']],
                ['label' => 'Done signal is intact', 'help' => 'The quick start ends at the same finish line.',
                 'evidence_sections' => ['done_sentence']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-process-one-pager',
            'title'            => 'Pack process one-pager',
            'summary'          => 'Assemble the process snapshot, steps, checklist, hand-off notes, and update note.',
            'why_it_matters'   => 'The final handoff should be short enough to use and complete enough to prevent pings. This step packs the approved pieces into one page and keeps unresolved facts visible. Common mistakes: adding policies, burying the trigger, or hiding update needs. Need help if the AI invents detail? Paste again and ask it to use only approved artifacts. You are ready when the one-pager can live beside the work.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your process document export.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are packing a one-page process document. Use ONLY pantry facts and approved artifacts. Invent nothing; keep unresolved update notes visible.

Process name: {{process_name}}
Who runs it: {{who_runs_it}}
Trigger: {{trigger}}
Definition of done: {{definition_of_done}}

Approved quick start:
{{artifact:write-quick-start}}

Approved clear procedure:
{{artifact:rewrite-clear-steps}}

Approved checks and exceptions:
{{artifact:add-checks-and-exceptions}}

Return Markdown with these headings:
## Process one-pager
Owner, trigger, done signal, and concise steps.
## Run checklist
Five to seven checkbox items for running the process.
## Hand-off notes
Who gets what next, including pauses or exceptions.
## Update note
Facts the owner should confirm or revise later.

Keep the four headings in order. Under 360 words.
TXT,
            'example_response' => $onePagerExample,
            'output_sections' => [
                ['key' => 'process_one_pager', 'heading' => 'Process one-pager', 'required' => true],
                ['key' => 'run_checklist', 'heading' => 'Run checklist', 'required' => true],
                ['key' => 'hand_off_notes', 'heading' => 'Hand-off notes', 'required' => true],
                ['key' => 'update_note', 'heading' => 'Update note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'One-pager has boundaries', 'help' => 'Owner, trigger, and done signal are visible.',
                 'evidence_sections' => ['process_one_pager']],
                ['label' => 'Checklist supports running', 'help' => 'Checklist items are practical during the process.',
                 'evidence_sections' => ['run_checklist']],
                ['label' => 'Updates are explicit', 'help' => 'Unresolved facts are named for later correction.',
                 'evidence_sections' => ['update_note']],
            ],
        ],
    ],
];
