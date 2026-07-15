<?php

declare(strict_types=1);

/**
 * Outline an Article - executable Cookbook for writing-publishing.
 *
 * Inspired by inverted pyramid and public journalism outline teaching. Ideas
 * only. All copy, examples, and prompts are original SousMeow work. No source
 * wording is copied.
 */

$angleExample = <<<'MD'
## Article angle
Show Harbor Thread readers how to notice a worn wool coat lining and decide what to ask before bringing it in.

## Reader and takeaway
The reader owns a wool coat with a lining that feels loose, torn, or uncomfortable. They should leave knowing the signs to check and what details help a repair shop assess the next step.

## Boundaries
Do not promise that every lining can be mended. Do not add prices, turnaround times, fabric science, or dry-cleaning advice unless those facts are supplied.

## Working nut graf
A damaged lining can make a good wool coat feel finished before it really is. This article explains the lining problems worth checking, what they can affect, and what to note before asking Harbor Thread about a repair.
MD;

$orderExample = <<<'MD'
## Ordered points
1. Start with the reader's problem: the coat is still useful, but the lining is torn, sagging, or catching.
2. Explain what a lining does in plain words: comfort, structure, and protection for the inside of the coat.
3. List the signs to check: seam splits, pocket tears, hem detaching, fabric shredding, or sleeves pulling.
4. Tell readers what to photograph or describe before asking for help.
5. Close with a cautious next step: ask Harbor Thread whether the coat is a good repair candidate.

## Why this order works
It begins with the practical concern, gives enough context to understand the repair, then moves into inspection and action.

## Parked points
- Exact repair prices.
- Promised turnaround time.
- Claims about every wool coat being worth repair.
MD;

$sectionHeadsExample = <<<'MD'
## Section heads
1. When a wool coat lining starts causing problems
2. What the lining does
3. Signs worth checking before you bring it in
4. What to note or photograph
5. How to ask about the repair

## Scan path
A busy reader can scan the heads and understand the article: notice the problem, understand the part, inspect the coat, gather details, then ask for help.

## Draft notes
Keep each section short. Use concrete coat parts. Avoid technical sewing terms unless they are explained immediately.
MD;

$evidenceGapsExample = <<<'MD'
## Evidence gaps
- Whether Harbor Thread replaces full linings or only repairs smaller lining damage.
- Whether photos are accepted before an appointment.
- Whether any coat materials are excluded.

## Evidence to use
- The known article topic: mending a wool coat lining.
- Reader need: a practical way to inspect and ask about repair.
- Known points about lining tears, loose seams, pockets, hems, and sleeves.

## Claims to avoid
Do not claim a repair will be cheap, fast, invisible, or possible for every coat.
MD;

$packOutlineExample = <<<'MD'
## Article outline
Title idea: How to mend a wool coat lining

Angle: Help readers inspect a worn lining and ask a repair shop the right questions before replacing the coat.

Sections:
1. When a wool coat lining starts causing problems
2. What the lining does
3. Signs worth checking before you bring it in
4. What to note or photograph
5. How to ask about the repair

## Drafting brief
Write for Harbor Thread blog readers who want plain repair guidance. Start with the reader's coat problem, then give a practical inspection path and a careful CTA.

## Final checks
- [ ] The takeaway appears in the opening.
- [ ] Every section serves the scan path.
- [ ] Evidence gaps are not filled in.
- [ ] The article avoids prices, timelines, and guaranteed repair outcomes.

## Open questions
Confirm which lining repairs Harbor Thread offers, whether photo review is available, and what CTA link should be used.
MD;

return [
    'slug'                => 'outline-an-article',
    'title'               => 'Outline an Article',
    'tagline'             => 'Move from a topic to a sharp outline a busy reader can scan.',
    'description'         => "A useful article outline protects the reader's time. This Cookbook helps you lock the angle, order the points, write scan-friendly section heads, note evidence gaps, and package a draft-ready outline. Every prompt uses only the topic, reader, takeaway, and points you provide.",
    'primary_category'    => 'writing-publishing',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Writers turning a topic into a clean article plan before drafting',
    'outcome'             => 'article angle, ordered point spine, section heads, evidence gaps, and a draft-ready outline',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'clay',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 25,
    'demo_completed_runs' => 274,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 17,
    'stages' => [
        ['title' => 'Angle', 'summary' => 'Lock the reader, takeaway, and article angle.'],
        ['title' => 'Spine', 'summary' => 'Order points, write section heads, and note evidence gaps.'],
        ['title' => 'Ready', 'summary' => 'Package a draft-ready article outline.'],
    ],
    'fields' => [
        [
            'field_key'    => 'article_topic',
            'label'        => 'Article topic',
            'type'         => 'text',
            'help'         => 'Name the article in plain words.',
            'placeholder'  => 'e.g. How to mend a wool coat lining',
            'sample_value' => 'How to mend a wool coat lining',
        ],
        [
            'field_key'    => 'reader',
            'label'        => 'Reader',
            'type'         => 'text',
            'help'         => 'Who needs this article?',
            'placeholder'  => 'e.g. Harbor Thread blog readers with a worn wool coat',
            'sample_value' => 'Harbor Thread blog readers who own a wool coat with a worn or torn lining',
        ],
        [
            'field_key'    => 'takeaway',
            'label'        => 'Takeaway',
            'type'         => 'textarea',
            'help'         => 'What should the reader understand or do after reading?',
            'placeholder'  => 'e.g. Know what lining issues to check before asking for repair help',
            'sample_value' => 'Readers should know what lining issues to check, what details to note, and how to ask whether a wool coat lining can be repaired.',
        ],
        [
            'field_key'    => 'known_points',
            'label'        => 'Known points',
            'type'         => 'textarea',
            'help'         => 'Facts, points, and examples you already know. One per line.',
            'placeholder'  => "One point per line",
            'sample_value' => "Lining tears can catch on clothing\nLoose lining can make a coat feel uncomfortable\nCheck seams, pockets, sleeve lining, and bottom hem\nPhotos of the damage may help explain the issue\nAsk Harbor Thread what repair path fits the coat",
        ],
        [
            'field_key'    => 'must_avoid',
            'label'        => 'Must avoid',
            'type'         => 'textarea',
            'help'         => 'Claims, topics, or wording that should stay out.',
            'placeholder'  => 'e.g. No prices or guaranteed repair outcomes',
            'sample_value' => 'Do not promise prices, turnaround times, invisible repairs, or that every wool coat lining can be mended.',
        ],
        [
            'field_key'    => 'length_target',
            'label'        => 'Length target',
            'type'         => 'text',
            'help'         => 'Approximate target length or format.',
            'placeholder'  => 'e.g. 900-word blog post',
            'sample_value' => '900-word Harbor Thread blog post',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'lock-the-angle',
            'title'            => 'Lock the angle',
            'summary'          => 'Define the article angle, reader, takeaway, and boundaries.',
            'why_it_matters'   => 'The angle decides what the article is allowed to do and what it should leave out.',
            'unlocks_text'     => 'Approving unlocks point ordering.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Lock an article angle using only these facts. Do not invent research, quotes, metrics, services, or outcomes.

Topic: {{article_topic}}
Reader: {{reader}}
Takeaway: {{takeaway}}
Known points:
{{known_points}}
Must avoid:
{{must_avoid}}
Length target: {{length_target}}

Return Markdown with exactly these headings:
## Article angle
One sentence.
## Reader and takeaway
Two or three sentences.
## Boundaries
What the article must not claim.
## Working nut graf
One short paragraph setting the article's promise.
Under 240 words.
TXT,
            'example_response' => $angleExample,
            'output_sections' => [
                ['key' => 'article_angle', 'heading' => 'Article angle', 'required' => true],
                ['key' => 'reader_and_takeaway', 'heading' => 'Reader and takeaway', 'required' => true],
                ['key' => 'boundaries', 'heading' => 'Boundaries', 'required' => true],
                ['key' => 'working_nut_graf', 'heading' => 'Working nut graf', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Angle is specific', 'help' => 'The angle names the reader problem and article job.',
                 'evidence_sections' => ['article_angle']],
                ['label' => 'Takeaway is visible', 'help' => 'The reader payoff is stated plainly.',
                 'evidence_sections' => ['reader_and_takeaway']],
                ['label' => 'Boundaries prevent invention', 'help' => 'Unsupported claims are named before outlining.',
                 'evidence_sections' => ['boundaries']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'order-the-points',
            'title'            => 'Order the points',
            'summary'          => 'Arrange known points into a reader-friendly spine.',
            'why_it_matters'   => 'A strong article spine helps the reader move from problem to understanding to next step.',
            'unlocks_text'     => 'Approving unlocks section heads.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Order the article points. Use only the known points and approved angle. Do not add facts, examples, quotes, or sources.

Known points:
{{known_points}}
Must avoid:
{{must_avoid}}
Approved angle:
{{artifact:lock-the-angle}}

Return Markdown with exactly these headings:
## Ordered points
Numbered points in reader-first order.
## Why this order works
One paragraph.
## Parked points
Bullets for points to leave out or confirm later.
Under 220 words.
TXT,
            'example_response' => $orderExample,
            'output_sections' => [
                ['key' => 'ordered_points', 'heading' => 'Ordered points', 'required' => true],
                ['key' => 'why_this_order_works', 'heading' => 'Why this order works', 'required' => true],
                ['key' => 'parked_points', 'heading' => 'Parked points', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Points are ordered', 'help' => 'The spine is numbered and sequenced.',
                 'evidence_sections' => ['ordered_points']],
                ['label' => 'Order has a reason', 'help' => 'The sequence is tied to reader understanding.',
                 'evidence_sections' => ['why_this_order_works']],
                ['label' => 'Unsupported ideas are parked', 'help' => 'Avoided claims stay out of the spine.',
                 'evidence_sections' => ['parked_points']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-section-heads',
            'title'            => 'Write section heads',
            'summary'          => 'Turn the ordered points into headings a busy reader can scan.',
            'why_it_matters'   => 'Section heads are the article outline the reader sees first.',
            'unlocks_text'     => 'Approving unlocks evidence gap notes.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Write section heads from the approved point order. Use plain language. Do not invent new points.

Article topic: {{article_topic}}
Length target: {{length_target}}
Approved order:
{{artifact:order-the-points}}

Return Markdown with exactly these headings:
## Section heads
Numbered section heads.
## Scan path
One paragraph explaining what a skimmer learns.
## Draft notes
Bullets for how to draft under the heads.
Under 200 words.
TXT,
            'example_response' => $sectionHeadsExample,
            'output_sections' => [
                ['key' => 'section_heads', 'heading' => 'Section heads', 'required' => true],
                ['key' => 'scan_path', 'heading' => 'Scan path', 'required' => true],
                ['key' => 'draft_notes', 'heading' => 'Draft notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Heads are scannable', 'help' => 'A reader can understand the outline from headings alone.',
                 'evidence_sections' => ['section_heads', 'scan_path']],
                ['label' => 'Heads follow the approved order', 'help' => 'The sequence still matches the spine.',
                 'evidence_sections' => ['section_heads']],
                ['label' => 'Draft notes are practical', 'help' => 'The notes guide writing without adding new facts.',
                 'evidence_sections' => ['draft_notes']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'note-evidence-gaps',
            'title'            => 'Note evidence gaps',
            'summary'          => 'Separate usable evidence from facts that still need confirmation.',
            'why_it_matters'   => 'Evidence gaps keep the draft honest and prevent polished guesses from becoming claims.',
            'unlocks_text'     => 'Approving unlocks the final outline package.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Identify evidence gaps for this article. Use only pantry facts and approved outline. Do not add sources, quotes, or statistics.

Known points:
{{known_points}}
Must avoid:
{{must_avoid}}
Approved heads:
{{artifact:write-section-heads}}

Return Markdown with exactly these headings:
## Evidence gaps
Facts to confirm before drafting or publishing.
## Evidence to use
Supplied facts safe to use.
## Claims to avoid
Unsupported claims that must stay out.
Under 200 words.
TXT,
            'example_response' => $evidenceGapsExample,
            'output_sections' => [
                ['key' => 'evidence_gaps', 'heading' => 'Evidence gaps', 'required' => true],
                ['key' => 'evidence_to_use', 'heading' => 'Evidence to use', 'required' => true],
                ['key' => 'claims_to_avoid', 'heading' => 'Claims to avoid', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Gaps are explicit', 'help' => 'Unknowns are listed before drafting.',
                 'evidence_sections' => ['evidence_gaps']],
                ['label' => 'Usable evidence is supplied', 'help' => 'The safe evidence comes from the pantry or approved artifacts.',
                 'evidence_sections' => ['evidence_to_use']],
                ['label' => 'Risky claims are blocked', 'help' => 'Unsupported promises are named.',
                 'evidence_sections' => ['claims_to_avoid']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-outline',
            'title'            => 'Pack the outline',
            'summary'          => 'Assemble the angle, spine, heads, and evidence notes into one draft-ready outline.',
            'why_it_matters'   => 'The packed outline gives drafting a clear path and makes remaining unknowns visible.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Package a draft-ready article outline. Use only approved artifacts and pantry facts. Do not invent links, sources, quotes, or claims.

Topic: {{article_topic}}
Reader: {{reader}}
Length target: {{length_target}}
Angle:
{{artifact:lock-the-angle}}
Point order:
{{artifact:order-the-points}}
Section heads:
{{artifact:write-section-heads}}
Evidence notes:
{{artifact:note-evidence-gaps}}

Return Markdown with exactly these headings:
## Article outline
Title idea, angle, and numbered sections.
## Drafting brief
Who to write for and how to start.
## Final checks
Four checkbox items.
## Open questions
Facts to confirm.
Under 300 words.
TXT,
            'example_response' => $packOutlineExample,
            'output_sections' => [
                ['key' => 'article_outline', 'heading' => 'Article outline', 'required' => true],
                ['key' => 'drafting_brief', 'heading' => 'Drafting brief', 'required' => true],
                ['key' => 'final_checks', 'heading' => 'Final checks', 'required' => true],
                ['key' => 'open_questions', 'heading' => 'Open questions', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Outline is draft-ready', 'help' => 'The article has a title idea, angle, and section sequence.',
                 'evidence_sections' => ['article_outline']],
                ['label' => 'Brief names reader and approach', 'help' => 'The writer knows who the draft serves.',
                 'evidence_sections' => ['drafting_brief']],
                ['label' => 'Open questions remain visible', 'help' => 'Unconfirmed facts are not treated as true.',
                 'evidence_sections' => ['open_questions']],
            ],
        ],
    ],
];
