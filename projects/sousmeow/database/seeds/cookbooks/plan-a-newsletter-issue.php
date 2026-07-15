<?php

declare(strict_types=1);

/**
 * Plan a Newsletter Issue - executable Cookbook for content-audience.
 *
 * Inspired by public newsletter planning teaching. Ideas only. All copy,
 * examples, and prompts are original SousMeow work. No source wording is copied.
 */

$readerPromiseExample = <<<'MD'
## Reader snapshot
Harbor Thread Notes is for people who want practical clothing care without sewing jargon. This issue serves readers who own winter coats and want to know whether a worn coat is worth repairing.

## Issue promise
By the end, the reader will know three simple winter coat repairs to check before replacing the coat.

## Must-use facts
- Focus on winter coat repairs.
- Include what owners can inspect at home.
- Mention that Harbor Thread can assess hems, lining tears, loose buttons, and worn pockets.

## Success signal
A reader replies with a coat question, books a repair consult, or saves the checklist for the weekend.
MD;

$angleExample = <<<'MD'
## Issue angle
Make the issue a calm "repair before replace" guide for winter coats.

## Why this angle fits
It matches the reader promise because the topic is seasonal, practical, and easy to scan. It also gives Harbor Thread a natural way to invite repair questions without sounding salesy.

## Keep out
Do not add coat-buying advice, fabric claims, turnaround promises, or prices unless Harbor Thread supplies those facts.
MD;

$sectionsExample = <<<'MD'
## Section outline
1. Opening note: why winter coats deserve one repair check before replacement.
2. Quick inspection: lining, buttons, pockets, cuffs, and hems.
3. Repair paths: which issues are often worth asking a tailor about.
4. When to bring it in: what photos or notes help Harbor Thread assess the coat.
5. CTA: reply with a photo or book a repair consult.

## Section purpose
The outline moves from reader concern to practical inspection to a clear next step.

## Drafting notes
Use plain clothing-care language. Keep the tone useful and reassuring. Do not shame readers for delayed repairs.

## Unknowns to confirm
- Whether Harbor Thread accepts coat photos by reply.
- Whether consults require appointments.
- Any seasonal cutoff date for winter repairs.
MD;

$subjectOptionsExample = <<<'MD'
## Subject options
1. Before you replace a winter coat, check these repairs
2. Winter coat looking tired? Start with these fixes
3. Three coat repairs worth checking this week

## Recommended subject
Before you replace a winter coat, check these repairs

## Why it fits
It names the reader's decision, the seasonal topic, and the practical promise without adding unsupported discounts or urgency.
MD;

$openingExample = <<<'MD'
## Opening draft
Before you give up on a winter coat, check the parts that take the hardest wear: lining seams, cuffs, buttons, pockets, and hems. A small repair can sometimes keep a favorite coat useful for another season.

## Reader hook
The hook starts from a choice the reader may already be considering: repair or replace.

## Section bridge
Below is a quick inspection path you can use at home before deciding whether to bring the coat in.
MD;

$linksCtaExample = <<<'MD'
## Links to include
- Coat repair booking page, if Harbor Thread has one.
- A general contact page if there is no booking page.
- Optional: one photo guide only if it already exists.

## CTA wording
Reply with a photo of the worn area, or book a repair consult and tell us what feels loose, torn, or uncomfortable.

## Placement notes
Put the CTA once after the inspection guide and once in the closing line. Do not interrupt every section with a booking prompt.

## Missing assets
Confirm the exact booking link, whether photo replies are accepted, and any required appointment language.
MD;

$previewBlurbExample = <<<'MD'
## Preview text
A quick winter coat check: lining, buttons, pockets, cuffs, and hems before you decide to replace it.

## Social blurb
Winter coats work hard. This week's Harbor Thread Notes walks through the repairs worth checking before you shop for a new one.

## Why it earns the open
The preview repeats the practical promise and lists the specific parts the reader can inspect.
MD;

$sendChecklistExample = <<<'MD'
## Send-ready outline
Subject: Before you replace a winter coat, check these repairs
Preview: A quick winter coat check: lining, buttons, pockets, cuffs, and hems before you decide to replace it.

Opening: Before you give up on a winter coat, check the parts that take the hardest wear.

Sections:
1. Why winter coats deserve one repair check.
2. Inspect lining, buttons, pockets, cuffs, and hems.
3. Decide what to ask Harbor Thread about.
4. Reply with a photo or book a repair consult.

## Final package
The issue promises one practical outcome: help readers decide whether a winter coat repair is worth asking about before replacing the coat.

## Pre-send checklist
- [ ] Subject matches the reader promise.
- [ ] Preview text names the practical payoff.
- [ ] Every section supports winter coat repair decisions.
- [ ] CTA uses only confirmed contact or booking details.
- [ ] No prices, timelines, or services were invented.

## Remaining unknowns
Confirm the booking link, photo-reply policy, and whether appointments are required.
MD;

return [
    'slug'                => 'plan-a-newsletter-issue',
    'title'               => 'Plan a Newsletter Issue',
    'tagline'             => 'Plan one newsletter from reader promise to send-ready outline.',
    'description'         => "A strong newsletter issue starts with one useful promise. This Cookbook helps you name the reader, choose an angle, outline sections, package the subject and preview text, and finish with a send checklist. Every step works only from the facts you enter and the artifacts you approve.",
    'primary_category'    => 'content-audience',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Newsletter writers planning one useful issue at a time',
    'outcome'             => 'reader promise, issue angle, section outline, subject options, opening, CTA plan, preview blurb, and send checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'amber',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 35,
    'demo_completed_runs' => 221,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 16,
    'stages' => [
        ['title' => 'Reader', 'summary' => 'Name the reader promise and the issue angle.'],
        ['title' => 'Draft bones', 'summary' => 'Outline sections, subject options, and the opening.'],
        ['title' => 'Package', 'summary' => 'Plan links, preview copy, and the send checklist.'],
    ],
    'fields' => [
        [
            'field_key'    => 'newsletter_name',
            'label'        => 'Newsletter name',
            'type'         => 'text',
            'help'         => 'Use the exact name readers see.',
            'placeholder'  => 'e.g. Harbor Thread Notes',
            'sample_value' => 'Harbor Thread Notes',
        ],
        [
            'field_key'    => 'issue_topic',
            'label'        => 'Issue topic',
            'type'         => 'text',
            'help'         => 'Name the issue in plain words.',
            'placeholder'  => 'e.g. Winter coat repairs',
            'sample_value' => 'Winter coat repairs',
        ],
        [
            'field_key'    => 'reader_promise',
            'label'        => 'Reader promise',
            'type'         => 'textarea',
            'help'         => 'What will a reader be able to do, decide, or understand after reading?',
            'placeholder'  => 'e.g. Know which coat repairs are worth checking before replacing a coat',
            'sample_value' => 'Readers will know three simple winter coat repairs to check before replacing a coat.',
        ],
        [
            'field_key'    => 'must_include',
            'label'        => 'Must include',
            'type'         => 'textarea',
            'help'         => 'Facts, links, sections, or reminders that must appear. Known facts only.',
            'placeholder'  => "One item per line",
            'sample_value' => "Check lining seams, loose buttons, worn pockets, cuffs, and hems\nInvite readers to reply with a coat question\nMention Harbor Thread can assess repair options\nNo prices or turnaround promises yet",
        ],
        [
            'field_key'    => 'call_to_action',
            'label'        => 'Call to action',
            'type'         => 'text',
            'help'         => 'The one action this issue should invite.',
            'placeholder'  => 'e.g. Reply with a coat photo or book a repair consult',
            'sample_value' => 'Reply with a photo of the worn area or book a repair consult.',
        ],
        [
            'field_key'    => 'send_day',
            'label'        => 'Send day',
            'type'         => 'text',
            'help'         => 'The planned send day if known.',
            'placeholder'  => 'e.g. Thursday',
            'sample_value' => 'Thursday',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-the-reader-promise',
            'title'            => 'Name the reader promise',
            'summary'          => 'Turn the issue facts into one clear promise for the reader.',
            'why_it_matters'   => 'A newsletter issue becomes easier to plan when every section must earn its place under one reader promise.',
            'unlocks_text'     => 'Approving unlocks the issue angle.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Plan a newsletter issue using only these facts. Invent no audience data, services, links, metrics, or deadlines.

Newsletter: {{newsletter_name}}
Topic: {{issue_topic}}
Reader promise: {{reader_promise}}
Must include:
{{must_include}}
CTA: {{call_to_action}}
Send day: {{send_day}}

Return Markdown with exactly these headings:
## Reader snapshot
Who this issue is for, using only the facts.
## Issue promise
One clear reader payoff.
## Must-use facts
Bullets from the supplied facts only.
## Success signal
One observable reader action or decision.
Under 220 words.
TXT,
            'example_response' => $readerPromiseExample,
            'output_sections' => [
                ['key' => 'reader_snapshot', 'heading' => 'Reader snapshot', 'required' => true],
                ['key' => 'issue_promise', 'heading' => 'Issue promise', 'required' => true],
                ['key' => 'must_use_facts', 'heading' => 'Must-use facts', 'required' => true],
                ['key' => 'success_signal', 'heading' => 'Success signal', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Promise is reader-centered', 'help' => 'The payoff says what the reader can do or decide.',
                 'evidence_sections' => ['issue_promise']],
                ['label' => 'Facts stay supplied', 'help' => 'No audience research, links, or offers were invented.',
                 'evidence_sections' => ['must_use_facts']],
                ['label' => 'Success is observable', 'help' => 'The signal is an action or decision, not a vague feeling.',
                 'evidence_sections' => ['success_signal']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'pick-issue-angle',
            'title'            => 'Pick the issue angle',
            'summary'          => 'Choose the practical angle the issue will hold from opening to CTA.',
            'why_it_matters'   => 'A tight angle keeps the issue from becoming a loose pile of useful but disconnected notes.',
            'unlocks_text'     => 'Approving unlocks the section outline.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
Choose one newsletter issue angle. Use only the pantry facts and approved promise. Do not add reader segments, offers, or claims.

Topic: {{issue_topic}}
CTA: {{call_to_action}}
Approved promise:
{{artifact:name-the-reader-promise}}

Return Markdown with exactly these headings:
## Issue angle
One sentence naming the angle.
## Why this angle fits
Two or three sentences connecting it to the promise and CTA.
## Keep out
Bullets for facts or topics not supplied.
Under 180 words.
TXT,
            'example_response' => $angleExample,
            'output_sections' => [
                ['key' => 'issue_angle', 'heading' => 'Issue angle', 'required' => true],
                ['key' => 'why_angle_fits', 'heading' => 'Why this angle fits', 'required' => true],
                ['key' => 'keep_out', 'heading' => 'Keep out', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Angle is singular', 'help' => 'The issue has one center, not three competing themes.',
                 'evidence_sections' => ['issue_angle']],
                ['label' => 'Fit is explained', 'help' => 'The angle connects to the approved promise.',
                 'evidence_sections' => ['why_angle_fits']],
                ['label' => 'Boundaries are clear', 'help' => 'Unsupported topics are explicitly kept out.',
                 'evidence_sections' => ['keep_out']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'outline-sections',
            'title'            => 'Outline the sections',
            'summary'          => 'Build a scan-friendly issue spine from opening through CTA.',
            'why_it_matters'   => 'A section outline lets you see whether every part supports the promise before drafting full copy.',
            'unlocks_text'     => 'Approving unlocks subject line options.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
Make a newsletter section outline. Use only approved artifacts and pantry facts. Do not invent interviews, links, prices, or service details.

Newsletter: {{newsletter_name}}
Must include:
{{must_include}}
Approved promise:
{{artifact:name-the-reader-promise}}
Approved angle:
{{artifact:pick-issue-angle}}

Return Markdown with exactly these headings:
## Section outline
Numbered sections from opening to CTA.
## Section purpose
One paragraph explaining the flow.
## Drafting notes
Bullets for tone and drafting constraints.
## Unknowns to confirm
Bullets for missing facts only.
Under 260 words.
TXT,
            'example_response' => $sectionsExample,
            'output_sections' => [
                ['key' => 'section_outline', 'heading' => 'Section outline', 'required' => true],
                ['key' => 'section_purpose', 'heading' => 'Section purpose', 'required' => true],
                ['key' => 'drafting_notes', 'heading' => 'Drafting notes', 'required' => true],
                ['key' => 'unknowns_to_confirm', 'heading' => 'Unknowns to confirm', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Outline has a full arc', 'help' => 'It moves from opening to CTA.',
                 'evidence_sections' => ['section_outline']],
                ['label' => 'Each section supports the promise', 'help' => 'The flow explanation ties back to the reader payoff.',
                 'evidence_sections' => ['section_purpose']],
                ['label' => 'Unknowns are not filled in', 'help' => 'Missing facts are named instead of invented.',
                 'evidence_sections' => ['unknowns_to_confirm']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-subject-options',
            'title'            => 'Write subject options',
            'summary'          => 'Draft subject lines that promise the issue without overclaiming.',
            'why_it_matters'   => 'The subject line should tell the right reader why this issue is worth opening.',
            'unlocks_text'     => 'Approving unlocks the opening draft.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
Write subject options for this newsletter issue. Use only approved artifacts. Do not invent discounts, urgency, or results.

Newsletter: {{newsletter_name}}
Send day: {{send_day}}
Approved angle:
{{artifact:pick-issue-angle}}
Approved outline:
{{artifact:outline-sections}}

Return Markdown with exactly these headings:
## Subject options
Three subject lines.
## Recommended subject
Pick one subject.
## Why it fits
One or two sentences.
Under 160 words.
TXT,
            'example_response' => $subjectOptionsExample,
            'output_sections' => [
                ['key' => 'subject_options', 'heading' => 'Subject options', 'required' => true],
                ['key' => 'recommended_subject', 'heading' => 'Recommended subject', 'required' => true],
                ['key' => 'why_it_fits', 'heading' => 'Why it fits', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Three options exist', 'help' => 'There are exactly three subject lines to compare.',
                 'evidence_sections' => ['subject_options']],
                ['label' => 'Recommendation is explicit', 'help' => 'One subject is chosen for drafting.',
                 'evidence_sections' => ['recommended_subject']],
                ['label' => 'No unsupported urgency', 'help' => 'The fit explanation does not add deadlines or promotions.',
                 'evidence_sections' => ['why_it_fits']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'draft-opening',
            'title'            => 'Draft the opening',
            'summary'          => 'Write the first lines that set up the promise and lead into the outline.',
            'why_it_matters'   => 'A useful opening earns attention quickly and tells the reader what the issue will help them do.',
            'unlocks_text'     => 'Approving unlocks links and CTA planning.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Draft a newsletter opening. Use only approved artifacts and pantry facts. Do not invent anecdotes, customer stories, or seasonal claims.

Newsletter: {{newsletter_name}}
Topic: {{issue_topic}}
Approved promise:
{{artifact:name-the-reader-promise}}
Approved outline:
{{artifact:outline-sections}}

Return Markdown with exactly these headings:
## Opening draft
One short opening paragraph.
## Reader hook
Explain the hook in one sentence.
## Section bridge
One sentence leading into the outline.
Under 180 words.
TXT,
            'example_response' => $openingExample,
            'output_sections' => [
                ['key' => 'opening_draft', 'heading' => 'Opening draft', 'required' => true],
                ['key' => 'reader_hook', 'heading' => 'Reader hook', 'required' => true],
                ['key' => 'section_bridge', 'heading' => 'Section bridge', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Opening names the topic', 'help' => 'The first paragraph clearly matches the issue.',
                 'evidence_sections' => ['opening_draft']],
                ['label' => 'Hook serves the reader', 'help' => 'The hook ties to the reader promise.',
                 'evidence_sections' => ['reader_hook']],
                ['label' => 'Bridge points forward', 'help' => 'The reader knows what comes next.',
                 'evidence_sections' => ['section_bridge']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'plan-links-and-cta',
            'title'            => 'Plan links and CTA',
            'summary'          => 'Place the call to action and name any missing link facts.',
            'why_it_matters'   => 'A good issue can lose momentum if the next step is vague or unsupported by real link details.',
            'unlocks_text'     => 'Approving unlocks preview and social blurb copy.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
Plan links and CTA for this issue. Use only supplied and approved facts. If a link is missing, name it as missing.

CTA: {{call_to_action}}
Must include:
{{must_include}}
Approved outline:
{{artifact:outline-sections}}

Return Markdown with exactly these headings:
## Links to include
Bullets for real or needed links.
## CTA wording
One concise CTA.
## Placement notes
Where the CTA should appear.
## Missing assets
Facts or URLs to confirm.
Under 220 words.
TXT,
            'example_response' => $linksCtaExample,
            'output_sections' => [
                ['key' => 'links_to_include', 'heading' => 'Links to include', 'required' => true],
                ['key' => 'cta_wording', 'heading' => 'CTA wording', 'required' => true],
                ['key' => 'placement_notes', 'heading' => 'Placement notes', 'required' => true],
                ['key' => 'missing_assets', 'heading' => 'Missing assets', 'required' => true],
            ],
            'checks' => [
                ['label' => 'CTA is single', 'help' => 'The issue asks for one next action.',
                 'evidence_sections' => ['cta_wording']],
                ['label' => 'Placement is planned', 'help' => 'The CTA has a home in the issue.',
                 'evidence_sections' => ['placement_notes']],
                ['label' => 'Missing links stay missing', 'help' => 'Unknown URLs are not invented.',
                 'evidence_sections' => ['missing_assets']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-preview-blurb',
            'title'            => 'Write preview blurb',
            'summary'          => 'Package the issue promise into inbox preview and sharing copy.',
            'why_it_matters'   => 'Preview copy should support the subject line and make the issue easy to recognize at a glance.',
            'unlocks_text'     => 'Approving unlocks the final send checklist.',
            'est_minutes'      => 3,
            'prompt_template'  => <<<'TXT'
Write preview and sharing copy using only approved artifacts. Do not add claims, discounts, or links.

Newsletter: {{newsletter_name}}
Recommended subject:
{{artifact:write-subject-options}}
Approved opening:
{{artifact:draft-opening}}

Return Markdown with exactly these headings:
## Preview text
One inbox preview line.
## Social blurb
One short sharing blurb.
## Why it earns the open
One sentence tying it to the promise.
Under 150 words.
TXT,
            'example_response' => $previewBlurbExample,
            'output_sections' => [
                ['key' => 'preview_text', 'heading' => 'Preview text', 'required' => true],
                ['key' => 'social_blurb', 'heading' => 'Social blurb', 'required' => true],
                ['key' => 'why_it_earns_open', 'heading' => 'Why it earns the open', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Preview is concise', 'help' => 'The preview line is short enough for an inbox.',
                 'evidence_sections' => ['preview_text']],
                ['label' => 'Blurb matches the issue', 'help' => 'The sharing copy reflects the approved topic.',
                 'evidence_sections' => ['social_blurb']],
                ['label' => 'Open reason is grounded', 'help' => 'The reason points to the reader promise.',
                 'evidence_sections' => ['why_it_earns_open']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-send-checklist',
            'title'            => 'Pack the send checklist',
            'summary'          => 'Assemble the approved parts into a send-ready outline and checklist.',
            'why_it_matters'   => 'The final package catches mismatches before the issue moves into drafting or scheduling.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 3,
            'prompt_template'  => <<<'TXT'
Assemble a send-ready newsletter issue plan. Use only approved artifacts. Do not invent final links, dates, prices, or service promises.

Newsletter: {{newsletter_name}}
Send day: {{send_day}}
Promise:
{{artifact:name-the-reader-promise}}
Outline:
{{artifact:outline-sections}}
Subject:
{{artifact:write-subject-options}}
Opening:
{{artifact:draft-opening}}
CTA plan:
{{artifact:plan-links-and-cta}}
Preview:
{{artifact:write-preview-blurb}}

Return Markdown with exactly these headings:
## Send-ready outline
Subject, preview, opening note, sections, and CTA.
## Final package
One paragraph describing the finished issue.
## Pre-send checklist
Five checkbox items.
## Remaining unknowns
Facts to confirm before sending.
Under 300 words.
TXT,
            'example_response' => $sendChecklistExample,
            'output_sections' => [
                ['key' => 'send_ready_outline', 'heading' => 'Send-ready outline', 'required' => true],
                ['key' => 'final_package', 'heading' => 'Final package', 'required' => true],
                ['key' => 'pre_send_checklist', 'heading' => 'Pre-send checklist', 'required' => true],
                ['key' => 'remaining_unknowns', 'heading' => 'Remaining unknowns', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Package includes key parts', 'help' => 'Subject, preview, outline, and CTA are present.',
                 'evidence_sections' => ['send_ready_outline']],
                ['label' => 'Checklist catches risky gaps', 'help' => 'The checklist includes facts, links, and promise alignment.',
                 'evidence_sections' => ['pre_send_checklist']],
                ['label' => 'Unknowns remain visible', 'help' => 'Missing facts are not silently resolved.',
                 'evidence_sections' => ['remaining_unknowns']],
            ],
        ],
    ],
];
