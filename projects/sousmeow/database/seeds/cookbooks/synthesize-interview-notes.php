<?php

declare(strict_types=1);

/**
 * Synthesize Interview Notes — executable Cookbook for research-insights.
 *
 * Inspired by public 18F-style research synthesis teaching. Ideas only. All
 * copy, examples, and prompts are original SousMeow work. No source wording is
 * copied.
 */

$frameGoalExample = <<<'MD'
## Research question
What would help neighbors trust and actually use ShareShed, a tool-library app for borrowing household tools nearby?

## Decision at stake
The synthesis should help decide which first-version features and trust supports matter before building more of the app.

## Evidence boundary
The evidence is limited to five neighbor interviews and the notes provided. It can use repeated concerns and surprises, but it cannot claim citywide demand or invent who the neighbors are.

## Deadline
Create a concise memo before Friday so the team can choose a smaller first version.
MD;

$factsOnlyExample = <<<'MD'
## Fact list
1. Several neighbors liked borrowing occasional-use tools instead of buying them.
2. People worried about tools not coming back or coming back damaged.
3. Some notes mention not knowing which neighbor owns which tool.
4. Two notes mention preferring pickup windows over open-ended messaging.
5. At least one neighbor said they already lend tools informally.

## Repeated observations
Trust, return condition, and simple pickup coordination appeared more than once.

## Surprises
The strongest hesitation was not app navigation; it was whether borrowing would stay neighborly and low-friction.

## Evidence gaps
The notes do not prove exact feature priority, willingness to pay, neighborhood demographics, or how many people would list tools.
MD;

$themesExample = <<<'MD'
## Theme clusters
1. Trust before inventory: people want confidence that tools will be returned and treated well.
2. Coordination must be simple: pickup windows may matter more than a long chat thread.
3. Existing informal lending is a starting behavior: some neighbors already lend, but it is hard to see what is available.

## Supporting notes
- Trust cluster: repeated worry about returns and damage.
- Coordination cluster: pickup windows appeared in multiple notes.
- Existing behavior cluster: informal lending and not knowing who owns what both appeared in the notes.

## Singletons
Any one-off feature requests should stay separate until more notes support them.

## Not findings yet
Do not call these statistically representative findings. They are themes from five conversations.
MD;

$factInferenceExample = <<<'MD'
## Facts
- Five neighbor interviews were reviewed.
- Trust, tool condition, and return worries repeated in the notes.
- Pickup windows appeared in more than one note.
- Informal tool lending already happens for at least one participant.

## Inferences
- A first version may need trust and return expectations before a large catalog.
- Scheduling support may reduce friction more than extra browsing features.
- ShareShed should make existing lending easier rather than replace neighbor relationships.

## Confidence notes
Confidence is strongest where a concern repeated across notes. Confidence is lower for one-off requests and any market-size claim.

## Do not claim
Do not claim demographics, exact demand, conversion, safety guarantees, or that all neighbors want the same features.
MD;

$implicationsExample = <<<'MD'
## Implications
1. Prioritize visible borrowing expectations: return date, condition note, and simple accountability.
2. Include pickup-window coordination early because it came up repeatedly.
3. Keep the first inventory experience small and understandable instead of adding many browse filters.

## Product questions
- What is the lightest way to show return expectations without making the app feel formal?
- Should pickup windows be required before a borrow request is sent?
- What information should a tool listing include to support trust?

## Risks to watch
Overbuilding features that do not address trust could leave the main hesitation untouched.

## What not to decide
Do not decide pricing, verification rules, demographic targeting, or full marketplace policy from these notes alone.
MD;

$memoExample = <<<'MD'
## Research memo
Five neighbor interviews about ShareShed suggest the first version should focus less on a huge tool catalog and more on trust, return expectations, and simple pickup coordination.

## Themes and evidence
1. Trust before inventory: repeated notes mention worry about tools not being returned or coming back damaged.
2. Coordination must be simple: pickup windows appeared in multiple notes as a way to reduce back-and-forth.
3. Existing lending is real but hidden: at least one neighbor already lends tools, and some notes mention not knowing who has what.

## Recommended next moves
- Prototype a borrow request with return date, condition note, and pickup window.
- Test whether those trust supports make people more comfortable listing or borrowing.
- Keep optional one-off feature requests parked until more interviews support them.

## Limits and open questions
These notes do not prove citywide demand, demographics, willingness to pay, or safety outcomes. The next round should ask what minimum trust details make a neighbor comfortable trying one borrow.
MD;

return [
    'slug'                => 'synthesize-interview-notes',
    'title'               => 'Synthesize Interview Notes',
    'tagline'             => 'Turn raw notes from real conversations into themes you can act on — invent no quotes.',
    'description'         => "Raw interview notes can turn into confident-sounding fiction if you summarize too quickly. This Cookbook helps you frame the synthesis, extract only supported facts, cluster themes, separate fact from inference, and package a short memo. Enter the real notes and repeated observations you already have. Every prompt forbids invented quotes, demographics, and findings not present in the notes or approved artifacts.",
    'primary_category'    => 'research-insights',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Researchers, founders, product teams, and community organizers turning interview notes into careful decisions',
    'outcome'             => 'synthesis frame, facts-only extract, theme clusters, fact/inference split, implications, and a research memo',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'slate',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 50,
    'demo_completed_runs' => 129,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 20,
    'stages' => [
        ['title' => 'Frame', 'summary' => 'Name the decision and the evidence boundary before summarizing.'],
        ['title' => 'Evidence', 'summary' => 'Extract facts from notes and cluster only what is supported.'],
        ['title' => 'Themes', 'summary' => 'Separate facts from inferences and write careful implications.'],
        ['title' => 'Act', 'summary' => 'Package a memo with limits and next moves.'],
    ],
    'fields' => [
        [
            'field_key'    => 'research_question',
            'label'        => 'Research question',
            'type'         => 'textarea',
            'help'         => 'What you hoped to learn from the interviews. Keep it close to the real study.',
            'placeholder'  => 'e.g. What would make neighbors willing to borrow tools from each other?',
            'sample_value' => 'What would help neighbors trust and actually use ShareShed, a tool-library app for borrowing household tools nearby?',
        ],
        [
            'field_key'    => 'interview_count',
            'label'        => 'Number of interviews',
            'type'         => 'number',
            'help'         => 'Use the real count. This helps keep claims proportional.',
            'placeholder'  => '5',
            'sample_value' => '5',
        ],
        [
            'field_key'    => 'notes_dump',
            'label'        => 'Raw notes dump',
            'type'         => 'textarea',
            'help'         => 'Paste notes, bullets, transcript excerpts, or facilitator notes. Do not clean them up too much.',
            'placeholder'  => "Paste notes here. Keep speaker labels if you have them.",
            'sample_value' => "Neighbor 1: likes borrowing tools for one-off projects; worries people will forget to return them.\nNeighbor 2: has a drill and ladder, lends to friends now; wants pickup times clear.\nNeighbor 3: interested if tool condition is shown; nervous about damage.\nNeighbor 4: does not know who nearby owns which tools; dislikes too much messaging.\nNeighbor 5: would try it for gardening tools; wants a return date and simple reminder.",
        ],
        [
            'field_key'    => 'what_you_heard_repeated',
            'label'        => 'What you heard repeatedly',
            'type'         => 'textarea',
            'help'         => 'Patterns you noticed before synthesis. Use your own words and do not add new facts.',
            'placeholder'  => "Trust\nPickup timing\nTool condition",
            'sample_value' => "Trust and returns came up repeatedly.\nPeople wanted simple pickup timing.\nTool condition and damage worries appeared more than once.",
        ],
        [
            'field_key'    => 'surprises',
            'label'        => 'Surprises',
            'type'         => 'textarea',
            'help'         => 'Anything that challenged your assumption. Leave blank if nothing surprised you.',
            'placeholder'  => 'e.g. People cared less about browsing and more about trust.',
            'sample_value' => 'People seemed less worried about app navigation than about keeping borrowing neighborly and low-friction.',
        ],
        [
            'field_key'    => 'decision_this_informs',
            'label'        => 'Decision this informs',
            'type'         => 'textarea',
            'help'         => 'The product, service, policy, or research decision that will use the synthesis.',
            'placeholder'  => 'e.g. Decide which first-version features ShareShed needs.',
            'sample_value' => 'Decide which first-version ShareShed features matter before building more of the app.',
        ],
        [
            'field_key'    => 'deadline',
            'label'        => 'Deadline',
            'type'         => 'text',
            'help'         => 'When the memo or decision is needed.',
            'placeholder'  => 'e.g. Friday',
            'sample_value' => 'Friday',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'frame-synthesis-goal',
            'title'            => 'Frame the synthesis goal',
            'summary'          => 'Name the question, decision, evidence boundary, and deadline before touching themes.',
            'why_it_matters'   => 'A synthesis without a frame can become a pile of attractive claims. This step says what the notes can and cannot decide. Common mistakes: turning five conversations into a market claim, inventing participant demographics, or skipping the decision context. Need help if the frame sounds too broad? Paste again and ask for a narrower decision. You are ready when the boundary is explicit.',
            'unlocks_text'     => 'Approving unlocks the facts-only extraction.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are framing a research synthesis. Use ONLY the pantry fields. Do not invent quotes, demographics, participant traits, or findings not in {{notes_dump}}.

Research question:
{{research_question}}
Interview count: {{interview_count}}
Decision this informs:
{{decision_this_informs}}
Deadline: {{deadline}}
Notes:
{{notes_dump}}

Produce Markdown with these exact headings:

## Research question
Restate the study question in one or two careful sentences.

## Decision at stake
Name the decision the synthesis can inform.

## Evidence boundary
State what the notes can support and what they cannot support. Mention the interview count.

## Deadline
Name the timing and what must be ready.

Keep headings in order. Under 240 words. Invent nothing.
TXT,
            'example_response' => $frameGoalExample,
            'output_sections' => [
                ['key' => 'research_question', 'heading' => 'Research question', 'required' => true],
                ['key' => 'decision_at_stake', 'heading' => 'Decision at stake', 'required' => true],
                ['key' => 'evidence_boundary', 'heading' => 'Evidence boundary', 'required' => true],
                ['key' => 'deadline', 'heading' => 'Deadline', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Decision is named', 'help' => 'The synthesis has a concrete decision to inform.',
                 'evidence_sections' => ['decision_at_stake']],
                ['label' => 'Evidence boundary is honest', 'help' => 'The frame limits claims to the notes and interview count.',
                 'evidence_sections' => ['evidence_boundary']],
                ['label' => 'No invented participant facts', 'help' => 'No demographics or traits appear unless they were in the notes.',
                 'evidence_sections' => ['research_question', 'evidence_boundary']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'extract-facts-only',
            'title'            => 'Extract facts only',
            'summary'          => 'Pull supported observations from the raw notes before naming themes.',
            'why_it_matters'   => 'Themes should grow from evidence, not from what you hoped to hear. This step keeps raw observations separate from interpretation. Common mistakes: inventing quotes, rounding vague notes into exact counts, or adding demographics from memory. Need help if a bullet sounds like a conclusion? Paste again and ask for facts only. You are ready when each item can be traced to notes or pantry.',
            'unlocks_text'     => 'Approving unlocks theme clustering.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are extracting supported facts from interview notes. Use ONLY {{notes_dump}}, {{what_you_heard_repeated}}, {{surprises}}, and approved frame. Do not invent quotes, demographics, findings, exact counts, or participant attributes. Do not use quotation marks unless copying exact text from notes.

Interview count: {{interview_count}}
What repeated:
{{what_you_heard_repeated}}
Surprises:
{{surprises}}
Raw notes:
{{notes_dump}}

Approved frame:
{{artifact:frame-synthesis-goal}}

Produce Markdown with these exact headings:

## Fact list
Numbered observations supported by the notes. Use "some", "several", or exact counts only when the notes support them.

## Repeated observations
Patterns that appear more than once, stated without overclaiming.

## Surprises
Surprises supported by pantry or notes.

## Evidence gaps
What the notes do not prove.

Keep headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $factsOnlyExample,
            'output_sections' => [
                ['key' => 'fact_list', 'heading' => 'Fact list', 'required' => true],
                ['key' => 'repeated_observations', 'heading' => 'Repeated observations', 'required' => true],
                ['key' => 'surprises', 'heading' => 'Surprises', 'required' => true],
                ['key' => 'evidence_gaps', 'heading' => 'Evidence gaps', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Facts trace to notes', 'help' => 'Each observation can be found in raw notes or pantry fields.',
                 'evidence_sections' => ['fact_list']],
                ['label' => 'No invented quotes or demographics', 'help' => 'The extract does not create quote text or participant traits.',
                 'evidence_sections' => ['fact_list', 'repeated_observations']],
                ['label' => 'Gaps are explicit', 'help' => 'The output names what the notes do not prove.',
                 'evidence_sections' => ['evidence_gaps']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'cluster-into-themes',
            'title'            => 'Cluster into themes',
            'summary'          => 'Group supported observations into careful themes and keep one-offs separate.',
            'why_it_matters'   => 'Clustering turns evidence into usable structure, but it can also hide weak support. This step asks for themes, supporting notes, singletons, and a reminder that clusters are not universal findings. Common mistakes: naming a theme from one comment, inventing quotes, or treating interviews as a survey. Need help if themes sound too polished? Paste again and ask for plainer cluster names. You are ready when every theme has support.',
            'unlocks_text'     => 'Approving unlocks fact and inference separation.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are clustering interview evidence. Use ONLY approved facts and pantry fields. Do not invent quotes, demographics, or findings not in {{notes_dump}} or approved artifacts.

Research question:
{{research_question}}
Interview count: {{interview_count}}

Approved facts:
{{artifact:extract-facts-only}}

Approved frame:
{{artifact:frame-synthesis-goal}}

Produce Markdown with these exact headings:

## Theme clusters
Three to five theme names with one sentence each. A theme needs more than one supporting note unless clearly marked tentative.

## Supporting notes
Bullets tying each theme to supported observations. Paraphrase; do not invent quotes.

## Singletons
One-off observations or feature requests that should not become themes yet.

## Not findings yet
One or two sentences limiting what these clusters can claim.

Keep headings in order. Under 360 words.
TXT,
            'example_response' => $themesExample,
            'output_sections' => [
                ['key' => 'theme_clusters', 'heading' => 'Theme clusters', 'required' => true],
                ['key' => 'supporting_notes', 'heading' => 'Supporting notes', 'required' => true],
                ['key' => 'singletons', 'heading' => 'Singletons', 'required' => true],
                ['key' => 'not_findings_yet', 'heading' => 'Not findings yet', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Themes have support', 'help' => 'Each cluster points back to supported notes.',
                 'evidence_sections' => ['theme_clusters', 'supporting_notes']],
                ['label' => 'One-offs are separated', 'help' => 'Singletons do not get promoted into findings.',
                 'evidence_sections' => ['singletons']],
                ['label' => 'Claims stay proportional', 'help' => 'The output avoids representative or survey-like claims.',
                 'evidence_sections' => ['not_findings_yet']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'separate-fact-from-inference',
            'title'            => 'Separate fact from inference',
            'summary'          => 'Label what the notes show, what you infer, and what you still cannot claim.',
            'why_it_matters'   => 'Teams often act on inferences while calling them facts. This step makes the distinction visible so decisions are safer. Common mistakes: inventing participant profiles, turning concern into demand, or hiding low confidence. Need help if inference language sounds certain? Paste again and require "may" or "could" where support is thin. You are ready when facts and interpretations are separate.',
            'unlocks_text'     => 'Approving unlocks implications.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are separating facts from inferences. Use ONLY pantry notes and approved artifacts. Do not invent quotes, demographics, or findings not in {{notes_dump}} or the approved evidence.

Decision this informs:
{{decision_this_informs}}

Approved facts:
{{artifact:extract-facts-only}}

Approved themes:
{{artifact:cluster-into-themes}}

Produce Markdown with these exact headings:

## Facts
Bullets the notes directly support.

## Inferences
Bullets that interpret the facts. Use careful language such as may, could, or suggests.

## Confidence notes
Which claims are stronger because they repeat, and which are weak because they are one-off or missing.

## Do not claim
Claims to avoid, including any invented quotes, demographics, outcomes, or unsupported findings.

Keep headings in order. Under 320 words.
TXT,
            'example_response' => $factInferenceExample,
            'output_sections' => [
                ['key' => 'facts', 'heading' => 'Facts', 'required' => true],
                ['key' => 'inferences', 'heading' => 'Inferences', 'required' => true],
                ['key' => 'confidence_notes', 'heading' => 'Confidence notes', 'required' => true],
                ['key' => 'do_not_claim', 'heading' => 'Do not claim', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Facts and inferences are separate', 'help' => 'Direct evidence and interpretation appear in different sections.',
                 'evidence_sections' => ['facts', 'inferences']],
                ['label' => 'Confidence is qualified', 'help' => 'The notes explain where support is stronger or weaker.',
                 'evidence_sections' => ['confidence_notes']],
                ['label' => 'Unsupported claims are blocked', 'help' => 'The do-not-claim list includes invented quotes, demographics, and findings.',
                 'evidence_sections' => ['do_not_claim']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-implications',
            'title'            => 'Write implications',
            'summary'          => 'Turn themes into careful action implications without pretending they are final proof.',
            'why_it_matters'   => 'Research becomes useful when it informs a choice, but implications must stay tied to evidence. This step says what to consider next and what not to decide yet. Common mistakes: treating themes as requirements, inventing participant quotes, or deciding strategy from thin notes. Need help if an implication jumps too far? Paste again and ask for a smaller next move. You are ready when every implication has a cautious evidence trail.',
            'unlocks_text'     => 'Approving unlocks the research memo.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing research implications. Use ONLY pantry fields and approved artifacts. Do not invent quotes, demographics, findings, user segments, metrics, or decisions not supported by {{notes_dump}}.

Decision this informs:
{{decision_this_informs}}
Deadline: {{deadline}}

Approved fact/inference split:
{{artifact:separate-fact-from-inference}}

Approved themes:
{{artifact:cluster-into-themes}}

Produce Markdown with these exact headings:

## Implications
Three to five careful implications for the decision. Use "prioritize", "test", or "consider"; do not declare certainty.

## Product questions
Open questions the team should answer next.

## Risks to watch
Risks if the team ignores the evidence or overreads it.

## What not to decide
Choices that require more evidence before deciding.

Keep headings in order. Under 340 words.
TXT,
            'example_response' => $implicationsExample,
            'output_sections' => [
                ['key' => 'implications', 'heading' => 'Implications', 'required' => true],
                ['key' => 'product_questions', 'heading' => 'Product questions', 'required' => true],
                ['key' => 'risks_to_watch', 'heading' => 'Risks to watch', 'required' => true],
                ['key' => 'what_not_to_decide', 'heading' => 'What not to decide', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Implications trace to themes', 'help' => 'Action ideas come from approved evidence, not new claims.',
                 'evidence_sections' => ['implications']],
                ['label' => 'Open questions remain open', 'help' => 'The output does not pretend unanswered questions are settled.',
                 'evidence_sections' => ['product_questions', 'what_not_to_decide']],
                ['label' => 'Overreach is named', 'help' => 'Risks include overreading or ignoring evidence.',
                 'evidence_sections' => ['risks_to_watch']],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'pack-research-memo',
            'title'            => 'Pack the research memo',
            'summary'          => 'Assemble a concise memo with themes, evidence, next moves, and limits.',
            'why_it_matters'   => 'A memo should help people act while remembering what the research can and cannot prove. This final step keeps the summary short and caveated. Common mistakes: adding invented quotes for color, hiding sample size, or writing findings as universal truth. Need help if the memo sounds too confident? Paste again and ask for stronger limits. You are ready when a decision-maker can see both the signal and the limits.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished research memo export.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are writing a concise research memo. Use ONLY pantry fields and approved artifacts. Do not invent quotes, demographics, findings, participant names, metrics, or conclusions not in {{notes_dump}} or approved evidence.

Research question:
{{research_question}}
Interview count: {{interview_count}}
Decision:
{{decision_this_informs}}
Deadline: {{deadline}}

Approved facts:
{{artifact:extract-facts-only}}

Approved themes:
{{artifact:cluster-into-themes}}

Approved implications:
{{artifact:write-implications}}

Produce Markdown with these exact headings:

## Research memo
One concise paragraph answering the research question within the evidence boundary.

## Themes and evidence
Three to five numbered themes with paraphrased evidence. No invented quotes.

## Recommended next moves
Three to five actions or tests tied to the implications.

## Limits and open questions
Limits from sample size, notes, missing data, and undecided questions.

Keep headings in order. Under 430 words. Invent nothing.
TXT,
            'example_response' => $memoExample,
            'output_sections' => [
                ['key' => 'research_memo', 'heading' => 'Research memo', 'required' => true],
                ['key' => 'themes_and_evidence', 'heading' => 'Themes and evidence', 'required' => true],
                ['key' => 'recommended_next_moves', 'heading' => 'Recommended next moves', 'required' => true],
                ['key' => 'limits_and_open_questions', 'heading' => 'Limits and open questions', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Memo stays within evidence', 'help' => 'The summary does not claim more than notes and approved artifacts support.',
                 'evidence_sections' => ['research_memo', 'limits_and_open_questions']],
                ['label' => 'No invented quotes', 'help' => 'Themes use paraphrased evidence or exact note text only.',
                 'evidence_sections' => ['themes_and_evidence']],
                ['label' => 'Next moves are actionable', 'help' => 'Recommendations can guide the decision without pretending unknowns are solved.',
                 'evidence_sections' => ['recommended_next_moves']],
            ],
        ],
    ],
];
