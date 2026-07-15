<?php

declare(strict_types=1);

/**
 * Critique a Screen - executable Cookbook for design-brand.
 *
 * Inspired by public usability and heuristic evaluation teaching. Ideas only.
 * All copy, examples, and prompts are original SousMeow work. No company's
 * heuristic-list wording is copied.
 */

$userGoalExample = <<<'MD'
## Screen frame
KinCalendar settings screen for the Quiet Hours toggle, inside notification settings.

## User goal
The user wants to pause routine shared-calendar alerts during a chosen time window without missing alerts that still matter.

## Success in plain words
They can tell whether Quiet Hours is on, understand what it affects, and know how to set or edit the quiet window.

## Evidence boundary
The critique can use the stated screen contents and known friction only. It cannot assume analytics, user quotes, or how the backend handles alerts.
MD;

$describeScreenExample = <<<'MD'
## What is visible
The screen shows notification settings with a Quiet Hours row, an off toggle, disabled start and end time fields, and helper text that says routine alerts can be paused during the selected window.

## Stated context
KinCalendar is a shared family calendar. Quiet Hours belongs in settings and should help users manage routine calendar alerts.

## Not observed
No screenshots, error states, analytics, accessibility audit, or confirmation flow were provided.

## Critique lens
Review whether the visible copy and controls make the user's next step clear without inventing missing screen behavior.
MD;

$clarityFindingsExample = <<<'MD'
## Clarity findings
1. The off toggle is clear, but the disabled time fields may not explain when they become editable.
2. "Routine alerts" may need a short example so users know what Quiet Hours will pause.
3. The screen does not yet say whether urgent reminders still arrive.

## Evidence from screen
The observed screen includes an off toggle, disabled start and end time fields, and helper text about pausing routine alerts.

## Risk to goal
Users may hesitate to turn Quiet Hours on if they cannot tell what will change or whether important reminders are protected.

## Questions to confirm
- Does the time picker enable immediately after the toggle turns on?
- Does KinCalendar already define urgent reminders?
- Is there room for one line of explanatory copy?
MD;

$frictionFindingsExample = <<<'MD'
## Friction findings
1. Disabled time fields may look unavailable rather than waiting for the toggle.
2. The user may need to understand the quiet window before deciding to turn it on.
3. Known friction says users are unsure whether urgent reminders still arrive.

## Evidence from screen
The screen shows the fields disabled while the toggle is off, and the known friction mentions uncertainty about urgent reminders.

## Why it matters
If the control feels risky, users may leave all notifications on or mute the whole phone outside the product.

## Not assumed
This critique does not assume error rates, tap counts, technical limits, or user intent beyond the supplied goal and friction.
MD;

$priorityFixesExample = <<<'MD'
## Priority fixes
1. Add a short line explaining that turning on Quiet Hours enables the time window fields.
2. Clarify what "routine alerts" means and whether urgent reminders still come through.
3. Consider showing a disabled-state hint directly beside the start and end fields.

## Reason for order
The first two fixes address trust and comprehension before any layout polish. The third supports the same goal if space allows.

## Tradeoffs
More copy can crowd a settings screen, so the wording should be short and tied to the controls already visible.
MD;

$fixNotesExample = <<<'MD'
## Fix notes
- Under the toggle: "Turn on Quiet Hours to choose when routine calendar alerts wait."
- Near the helper text: "Urgent reminders still come through if KinCalendar marks them urgent."
- Near disabled times: "Available after Quiet Hours is on."

## Copy or layout notes
Place the explanation close to the control it explains. Keep the current settings structure and avoid adding a separate education screen.

## Validation notes
Ask a tester what they think will happen after turning on the toggle and whether urgent reminders are still allowed.

## Constraints respected
The notes keep the existing settings screen, avoid a full notification redesign, and do not add analytics or backend behavior.
MD;

$memoExample = <<<'MD'
## Critique memo
The KinCalendar Quiet Hours settings screen has a useful core: a named toggle, time fields, and helper text. The main issue is not the feature idea but the confidence gap around what turns on, what becomes editable, and which alerts still arrive.

## Findings summary
- The disabled start and end fields need a clearer reason.
- "Routine alerts" needs a plain example or boundary.
- Urgent-reminder behavior should be stated if that behavior already exists.

## Recommended fixes
1. Add a short enablement note below the toggle.
2. Define routine alerts in one line.
3. Add a compact note about urgent reminders only if the product already supports them.

## Next check
Review the revised screen with one user goal: "Set Quiet Hours without worrying that important reminders disappear."
MD;

return [
    'slug'                => 'critique-a-screen',
    'title'               => 'Critique a Screen',
    'tagline'             => 'Review one real screen with plain findings and next fixes — invent no user data.',
    'description'         => "A useful screen critique stays close to what is actually visible. This Cookbook helps you frame the user goal, describe the screen, list clarity and friction findings, prioritize fixes, write fix notes, and package a plain critique memo. Prompts separate observations from assumptions so the critique does not invent user data.",
    'primary_category'    => 'design-brand',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Designers, founders, and product teams reviewing one screen before changing it',
    'outcome'             => 'screen frame, visible description, clarity findings, friction findings, prioritized fixes, fix notes, and critique memo',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'lilac',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 40,
    'demo_completed_runs' => 143,
    'demo_avg_rating'     => 4.6,
    'sort_order'          => 18,
    'stages' => [
        ['title' => 'Frame', 'summary' => 'Name the user goal and describe only what is visible.'],
        ['title' => 'Findings', 'summary' => 'List clarity and friction findings grounded in evidence.'],
        ['title' => 'Fixes', 'summary' => 'Prioritize fixes, write notes, and package the memo.'],
    ],
    'fields' => [
        [
            'field_key'    => 'screen_name',
            'label'        => 'Screen name',
            'type'         => 'text',
            'help'         => 'Name the exact screen being reviewed.',
            'placeholder'  => 'e.g. Quiet Hours settings',
            'sample_value' => 'KinCalendar Quiet Hours settings screen',
        ],
        [
            'field_key'    => 'product_context',
            'label'        => 'Product context',
            'type'         => 'textarea',
            'help'         => 'What product is this in, and what part of the product does the screen support?',
            'placeholder'  => 'e.g. Shared family calendar app notification settings',
            'sample_value' => 'KinCalendar is a shared family calendar app. This screen sits inside notification settings and controls Quiet Hours for routine shared-calendar alerts.',
        ],
        [
            'field_key'    => 'user_goal',
            'label'        => 'User goal',
            'type'         => 'textarea',
            'help'         => 'What the user is trying to do on this screen. Known goal only.',
            'placeholder'  => 'e.g. Pause routine alerts during a chosen time window',
            'sample_value' => 'Pause routine shared-calendar alerts during a chosen time window without missing reminders that still matter.',
        ],
        [
            'field_key'    => 'what_you_see',
            'label'        => 'What you see',
            'type'         => 'textarea',
            'help'         => 'Describe visible elements, labels, controls, and copy. Avoid interpretation.',
            'placeholder'  => "One observed element per line",
            'sample_value' => "Notification settings screen\nQuiet Hours row with toggle set to off\nStart time and end time fields shown but disabled\nHelper text says routine alerts can be paused during the selected window\nNo screenshot of confirmation or error states",
        ],
        [
            'field_key'    => 'known_friction',
            'label'        => 'Known friction',
            'type'         => 'textarea',
            'help'         => 'Known concerns, feedback, or suspected problems. If unknown, say unknown.',
            'placeholder'  => 'e.g. Users are unsure whether urgent reminders still arrive',
            'sample_value' => 'Users may be unsure whether urgent reminders still arrive during Quiet Hours. Disabled time fields may look unavailable instead of waiting for the toggle.',
        ],
        [
            'field_key'    => 'constraints',
            'label'        => 'Constraints',
            'type'         => 'textarea',
            'help'         => 'Design, product, engineering, or content constraints already known.',
            'placeholder'  => 'e.g. Must keep existing settings structure',
            'sample_value' => 'Keep the existing settings structure. Do not redesign the full notification system. Do not add analytics, new alert categories, or backend behavior in the critique.',
        ],
        [
            'field_key'    => 'success_looks_like',
            'label'        => 'Success looks like',
            'type'         => 'textarea',
            'help'         => 'What a better screen helps the user understand or do.',
            'placeholder'  => 'e.g. User knows what Quiet Hours pauses and how to set it',
            'sample_value' => 'A user can tell what Quiet Hours pauses, whether important reminders still arrive, and how to set a quiet window with confidence.',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-the-user-goal',
            'title'            => 'Name the user goal',
            'summary'          => 'Frame the screen around the real user goal and success condition.',
            'why_it_matters'   => 'A critique without a goal becomes personal taste. The user goal makes findings testable.',
            'unlocks_text'     => 'Approving unlocks the screen description.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Frame a screen critique using only supplied facts. Invent no user research, metrics, personas, or hidden product behavior.

Screen: {{screen_name}}
Product context: {{product_context}}
User goal: {{user_goal}}
Success looks like: {{success_looks_like}}
Constraints:
{{constraints}}

Return Markdown with exactly these headings:
## Screen frame
One sentence.
## User goal
One or two sentences.
## Success in plain words
One sentence.
## Evidence boundary
What the critique may and may not use.
Under 220 words.
TXT,
            'example_response' => $userGoalExample,
            'output_sections' => [
                ['key' => 'screen_frame', 'heading' => 'Screen frame', 'required' => true],
                ['key' => 'user_goal', 'heading' => 'User goal', 'required' => true],
                ['key' => 'success_in_plain_words', 'heading' => 'Success in plain words', 'required' => true],
                ['key' => 'evidence_boundary', 'heading' => 'Evidence boundary', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Goal is user-centered', 'help' => 'The goal says what the user is trying to do.',
                 'evidence_sections' => ['user_goal']],
                ['label' => 'Success is plain', 'help' => 'Success is observable in the screen experience.',
                 'evidence_sections' => ['success_in_plain_words']],
                ['label' => 'Evidence boundary is explicit', 'help' => 'The critique will not invent user data.',
                 'evidence_sections' => ['evidence_boundary']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'describe-the-screen',
            'title'            => 'Describe the screen',
            'summary'          => 'Write a neutral description of what is visible and what is not known.',
            'why_it_matters'   => 'Observation comes before judgment. This step keeps later findings anchored to the real screen.',
            'unlocks_text'     => 'Approving unlocks clarity findings.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
Describe the screen neutrally. Use only visible details and approved frame. Do not infer emotions, analytics, or behavior not shown.

What you see:
{{what_you_see}}
Known friction:
{{known_friction}}
Approved frame:
{{artifact:name-the-user-goal}}

Return Markdown with exactly these headings:
## What is visible
Plain observation paragraph.
## Stated context
Context from supplied facts.
## Not observed
Missing evidence or states.
## Critique lens
One sentence for how findings will be judged.
Under 220 words.
TXT,
            'example_response' => $describeScreenExample,
            'output_sections' => [
                ['key' => 'what_is_visible', 'heading' => 'What is visible', 'required' => true],
                ['key' => 'stated_context', 'heading' => 'Stated context', 'required' => true],
                ['key' => 'not_observed', 'heading' => 'Not observed', 'required' => true],
                ['key' => 'critique_lens', 'heading' => 'Critique lens', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Description stays neutral', 'help' => 'Visible elements are described before judgment.',
                 'evidence_sections' => ['what_is_visible']],
                ['label' => 'Missing evidence is named', 'help' => 'Unknown states are not assumed.',
                 'evidence_sections' => ['not_observed']],
                ['label' => 'Lens ties to goal', 'help' => 'The critique lens matches the approved user goal.',
                 'evidence_sections' => ['critique_lens']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'list-clarity-findings',
            'title'            => 'List clarity findings',
            'summary'          => 'Identify where copy, labels, or states may be hard to understand.',
            'why_it_matters'   => 'Clarity findings show what the screen needs to explain so users can act with confidence.',
            'unlocks_text'     => 'Approving unlocks friction findings.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
List clarity findings grounded only in the screen description and approved goal. Do not invent test results or user quotes.

Known friction:
{{known_friction}}
Goal:
{{artifact:name-the-user-goal}}
Description:
{{artifact:describe-the-screen}}

Return Markdown with exactly these headings:
## Clarity findings
Numbered findings about meaning, labels, or state.
## Evidence from screen
Visible details supporting the findings.
## Risk to goal
How clarity issues affect the user goal.
## Questions to confirm
Facts needed before design changes.
Under 260 words.
TXT,
            'example_response' => $clarityFindingsExample,
            'output_sections' => [
                ['key' => 'clarity_findings', 'heading' => 'Clarity findings', 'required' => true],
                ['key' => 'evidence_from_screen', 'heading' => 'Evidence from screen', 'required' => true],
                ['key' => 'risk_to_goal', 'heading' => 'Risk to goal', 'required' => true],
                ['key' => 'questions_to_confirm', 'heading' => 'Questions to confirm', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Findings are about clarity', 'help' => 'Each finding concerns understanding, labels, or state.',
                 'evidence_sections' => ['clarity_findings']],
                ['label' => 'Evidence is visible', 'help' => 'The support comes from the screen description.',
                 'evidence_sections' => ['evidence_from_screen']],
                ['label' => 'Questions do not masquerade as facts', 'help' => 'Unknowns remain questions.',
                 'evidence_sections' => ['questions_to_confirm']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'list-friction-findings',
            'title'            => 'List friction findings',
            'summary'          => 'Identify where the screen may slow, worry, or block the user.',
            'why_it_matters'   => "Friction findings connect the visible screen to the user's next action without claiming unseen behavior.",
            'unlocks_text'     => 'Approving unlocks fix prioritization.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
List friction findings from supplied evidence only. Do not invent behavior, emotions, analytics, or support tickets.

Known friction:
{{known_friction}}
Constraints:
{{constraints}}
Goal:
{{artifact:name-the-user-goal}}
Description:
{{artifact:describe-the-screen}}
Clarity findings:
{{artifact:list-clarity-findings}}

Return Markdown with exactly these headings:
## Friction findings
Numbered findings about effort, hesitation, or blockage.
## Evidence from screen
Visible or supplied evidence.
## Why it matters
Effect on the user goal.
## Not assumed
What the critique is not claiming.
Under 260 words.
TXT,
            'example_response' => $frictionFindingsExample,
            'output_sections' => [
                ['key' => 'friction_findings', 'heading' => 'Friction findings', 'required' => true],
                ['key' => 'evidence_from_screen', 'heading' => 'Evidence from screen', 'required' => true],
                ['key' => 'why_it_matters', 'heading' => 'Why it matters', 'required' => true],
                ['key' => 'not_assumed', 'heading' => 'Not assumed', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Findings name friction', 'help' => 'Each finding addresses possible effort, worry, or blockage.',
                 'evidence_sections' => ['friction_findings']],
                ['label' => 'Evidence stays supplied', 'help' => 'Findings cite visible or provided friction only.',
                 'evidence_sections' => ['evidence_from_screen']],
                ['label' => 'Assumptions are blocked', 'help' => 'Unseen behavior is named as not assumed.',
                 'evidence_sections' => ['not_assumed']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'prioritize-fixes',
            'title'            => 'Prioritize fixes',
            'summary'          => 'Choose the first fixes based on goal impact and constraints.',
            'why_it_matters'   => 'Prioritizing prevents the critique from becoming an unranked list of possible changes.',
            'unlocks_text'     => 'Approving unlocks fix notes.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
Prioritize fixes using only approved findings and constraints. Do not invent new features, metrics, or engineering scope.

Constraints:
{{constraints}}
Success looks like: {{success_looks_like}}
Clarity findings:
{{artifact:list-clarity-findings}}
Friction findings:
{{artifact:list-friction-findings}}

Return Markdown with exactly these headings:
## Priority fixes
Numbered fixes in recommended order.
## Reason for order
One paragraph.
## Tradeoffs
Constraints or costs to watch.
Under 220 words.
TXT,
            'example_response' => $priorityFixesExample,
            'output_sections' => [
                ['key' => 'priority_fixes', 'heading' => 'Priority fixes', 'required' => true],
                ['key' => 'reason_for_order', 'heading' => 'Reason for order', 'required' => true],
                ['key' => 'tradeoffs', 'heading' => 'Tradeoffs', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Fixes are ranked', 'help' => 'The recommendations have an order.',
                 'evidence_sections' => ['priority_fixes']],
                ['label' => 'Order follows goal impact', 'help' => 'The rationale connects fixes to the user goal.',
                 'evidence_sections' => ['reason_for_order']],
                ['label' => 'Constraints are respected', 'help' => 'Tradeoffs acknowledge limits instead of ignoring them.',
                 'evidence_sections' => ['tradeoffs']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-fix-notes',
            'title'            => 'Write fix notes',
            'summary'          => 'Turn priorities into specific copy, layout, and validation notes.',
            'why_it_matters'   => 'Fix notes make the critique actionable while staying inside known constraints.',
            'unlocks_text'     => 'Approving unlocks the critique memo.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
Write fix notes from approved priorities. Use only supplied constraints and findings. Do not invent UI not requested by the fixes.

Screen: {{screen_name}}
Constraints:
{{constraints}}
Priority fixes:
{{artifact:prioritize-fixes}}

Return Markdown with exactly these headings:
## Fix notes
Specific copy or interaction notes.
## Copy or layout notes
Where changes should live.
## Validation notes
What to check with a reviewer or tester.
## Constraints respected
How the notes stay within limits.
Under 260 words.
TXT,
            'example_response' => $fixNotesExample,
            'output_sections' => [
                ['key' => 'fix_notes', 'heading' => 'Fix notes', 'required' => true],
                ['key' => 'copy_or_layout_notes', 'heading' => 'Copy or layout notes', 'required' => true],
                ['key' => 'validation_notes', 'heading' => 'Validation notes', 'required' => true],
                ['key' => 'constraints_respected', 'heading' => 'Constraints respected', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Notes are concrete', 'help' => 'The fixes include specific wording or placement guidance.',
                 'evidence_sections' => ['fix_notes', 'copy_or_layout_notes']],
                ['label' => 'Validation is goal-based', 'help' => 'The check asks whether the user goal is clearer.',
                 'evidence_sections' => ['validation_notes']],
                ['label' => 'Constraints remain intact', 'help' => 'The fix does not expand scope silently.',
                 'evidence_sections' => ['constraints_respected']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-critique-memo',
            'title'            => 'Pack critique memo',
            'summary'          => 'Assemble the screen critique into a plain memo with findings and next fixes.',
            'why_it_matters'   => 'The memo gives teams a shared record of what was observed, what matters, and what to try next.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
Package a screen critique memo. Use only approved artifacts. Do not invent user data, screenshots, analytics, or product behavior.

Screen: {{screen_name}}
Goal:
{{artifact:name-the-user-goal}}
Description:
{{artifact:describe-the-screen}}
Clarity:
{{artifact:list-clarity-findings}}
Friction:
{{artifact:list-friction-findings}}
Priority fixes:
{{artifact:prioritize-fixes}}
Fix notes:
{{artifact:write-fix-notes}}

Return Markdown with exactly these headings:
## Critique memo
One paragraph summary.
## Findings summary
Bullets combining clarity and friction findings.
## Recommended fixes
Numbered next fixes.
## Next check
One focused follow-up check.
Under 320 words.
TXT,
            'example_response' => $memoExample,
            'output_sections' => [
                ['key' => 'critique_memo', 'heading' => 'Critique memo', 'required' => true],
                ['key' => 'findings_summary', 'heading' => 'Findings summary', 'required' => true],
                ['key' => 'recommended_fixes', 'heading' => 'Recommended fixes', 'required' => true],
                ['key' => 'next_check', 'heading' => 'Next check', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Memo stays evidence-based', 'help' => 'The summary does not add user data or unseen behavior.',
                 'evidence_sections' => ['critique_memo']],
                ['label' => 'Findings are summarized', 'help' => 'Clarity and friction issues are easy to scan.',
                 'evidence_sections' => ['findings_summary']],
                ['label' => 'Next check is actionable', 'help' => 'The follow-up can be used to review the revised screen.',
                 'evidence_sections' => ['next_check']],
            ],
        ],
    ],
];
