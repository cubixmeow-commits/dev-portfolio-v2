<?php

declare(strict_types=1);

/**
 * Write a Feature Spec - executable Cookbook for software-product.
 *
 * Inspired by public agile user-story / INVEST teaching and lightweight PRD
 * patterns (ideas only). All copy, examples, and prompts are original
 * SousMeow work. No source wording is copied.
 */

$problemExample = <<<'MD'
## Feature in one sentence
Add a "Quiet Hours" toggle to KinCalendar so a family can pause non-urgent shared-calendar alerts during a chosen time window.

## Problem today
KinCalendar sends shared event updates as they happen. A parent who works nights can be woken by routine schedule edits that do not need immediate attention.

## User impact
The affected user keeps muting the whole phone or missing useful alerts later. The family also loses trust that KinCalendar can fit different sleep schedules.

## Known constraints
The feature must respect the user's chosen quiet window and should not block urgent reminders if the product already marks them urgent. No engineering hours, legal claims, or app-store rules were provided.

## Unknowns to confirm
- Whether KinCalendar already has an urgent-reminder type.
- Whether quiet hours should apply per calendar or per person.
- Which settings screen currently holds notification controls.
MD;

$benefitsExample = <<<'MD'
## Primary user
The first user is a KinCalendar family member whose rest or focus time is interrupted by routine shared-calendar notifications.

## User situation
They still want the family calendar to work, but they need a predictable period when routine updates wait until later.

## Benefit statement
Quiet Hours lets them pause non-urgent calendar alerts during a chosen window without leaving the shared calendar.

## Secondary effects
Other family members can keep updating the calendar normally. The feature may reduce pressure to mute all notifications, which can hide useful reminders.

## Evidence limits
The spec does not claim how many users need this, how often alerts arrive, or how hard the feature is to build because those facts were not supplied.
MD;

$mustHavesExample = <<<'MD'
## Must-have list
1. User can turn Quiet Hours on or off.
2. User can choose a start time and end time for the quiet window.
3. Routine shared-calendar alerts are held or silenced during the window.
4. Urgent reminders, if KinCalendar already supports them, are not blocked by default.
5. Settings show when Quiet Hours is currently active.

## Requirement notes
The feature belongs in notification settings or another existing settings area. Copy should say "routine alerts" rather than promising silence for every possible notification.

## User control
The user controls their own quiet window. The spec does not let one family member force quiet hours onto another person.

## Edge cases to handle
- Window crosses midnight, such as 10:00 PM to 6:00 AM.
- User turns Quiet Hours off while a quiet window is active.
- User has no shared calendars yet.

## Must-not-invent boundary
Do not add pricing, analytics dashboards, device-level integrations, or legal consent steps unless the team later supplies those requirements.
MD;

$nonGoalsExample = <<<'MD'
## Non-goals
- Rebuilding the full notification system.
- Creating custom alert rules for every event type.
- Adding sleep tracking or health recommendations.
- Changing how family members create or edit calendar events.

## Parked ideas
Smart suggestions, calendar-specific quiet rules, and weekly quiet-hour templates may be useful later, but they are not needed for this first version.

## Scope boundary
This version only pauses or silences routine shared-calendar alerts during a user-chosen window. It does not decide whether an event is emotionally important or infer sleep habits.

## Why this protects the build
The boundary keeps the team focused on a small settings feature instead of a broad notification platform.
MD;

$acceptanceExample = <<<'MD'
## Acceptance checks
1. Given Quiet Hours is off, when the user opens notification settings, they can turn it on and choose a start and end time.
2. Given Quiet Hours is active, when a routine shared-calendar update happens, the user is not interrupted during the quiet window.
3. Given an urgent reminder type already exists, when it fires during Quiet Hours, it is still allowed by default.
4. Given the quiet window crosses midnight, when the start time is later than the end time, the app still treats the window as overnight.

## Happy path
A night-shift parent turns on Quiet Hours from 10:00 AM to 4:00 PM, sees that it is active, and avoids routine shared-calendar alerts during that time.

## Edge cases
- User disables Quiet Hours during an active window.
- User chooses the same start and end time.
- User has no shared calendars yet.

## Copy and settings checks
Settings copy should say what is paused, what is not paused, and how to turn Quiet Hours off. It should avoid promising that the whole phone will be silent.

## Evidence gap
The checks do not estimate engineering effort or notification-service behavior because those details were not provided.
MD;

$shipChecklistExample = <<<'MD'
## One-page spec
Quiet Hours gives each KinCalendar user a toggle and time window for pausing routine shared-calendar alerts. It helps family members protect sleep or focus without leaving the shared calendar. The first version includes the toggle, start and end times, active-state copy, and basic overnight handling. It excludes custom rules, sleep tracking, and a notification-system rebuild.

## Build checklist
- [ ] Toggle appears in the chosen settings area.
- [ ] User can set start and end times.
- [ ] Active window handles overnight ranges.
- [ ] Routine shared-calendar alerts do not interrupt during the window.
- [ ] Existing urgent reminders, if present, remain allowed by default.
- [ ] Empty shared-calendar state has clear copy.

## Review questions
- Where exactly does this live in settings?
- Does KinCalendar already mark any reminder as urgent?
- Should held routine alerts appear later or disappear silently?

## Risks and unknowns
The largest unknown is how notifications are currently categorized. If the app cannot tell routine from urgent alerts today, the first version may need a simpler label or a narrower notification rule.

## Ship-ready decision
Ready to build after the team confirms the settings location, the urgent-reminder behavior, and what happens to routine alerts after the quiet window ends.
MD;

return [
    'slug'                => 'write-a-feature-spec',
    'title'               => 'Write a Feature Spec',
    'tagline'             => 'Turn a fuzzy feature idea into a one-page spec your team could build from.',
    'description'         => "A fuzzy feature can turn into weeks of guessing once code starts. This Cookbook helps you lock the problem, the user, must-haves, non-goals, acceptance checks, and a ship-ready checklist before implementation. Enter only facts your team already knows; every prompt is told not to invent engineering hours, legal requirements, or product history.",
    'primary_category'    => 'software-product',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Solo makers and small product teams writing down a feature before building',
    'outcome'             => 'problem brief, users, must-haves, non-goals, acceptance checks, and a ship-ready checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 50,
    'demo_completed_runs' => 214,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 8,
    'stages' => [
        ['title' => 'Scope', 'summary' => 'Name the problem and who benefits.'],
        ['title' => 'Spec', 'summary' => 'Lock must-haves and non-goals.'],
        ['title' => 'Ready', 'summary' => 'Acceptance checks and ship checklist.'],
    ],
    'fields' => [
        [
            'field_key'    => 'feature_name',
            'label'        => 'Feature name',
            'type'         => 'text',
            'help'         => 'Use the working name your team already uses. No clever renaming needed.',
            'placeholder'  => 'e.g. Quiet Hours',
            'sample_value' => 'Quiet Hours',
        ],
        [
            'field_key'    => 'product_context',
            'label'        => 'Product context',
            'type'         => 'textarea',
            'help'         => 'What the product is and where this feature fits. Known facts only.',
            'placeholder'  => 'e.g. KinCalendar is a shared family calendar app...',
            'sample_value' => 'KinCalendar is a shared family calendar app where relatives create events, reminders, and schedule updates for the household.',
        ],
        [
            'field_key'    => 'user_type',
            'label'        => 'Who is this for?',
            'type'         => 'text',
            'help'         => 'Name the first user who benefits. A role or segment is enough.',
            'placeholder'  => 'e.g. Family member who works nights',
            'sample_value' => 'Family member who works nights or needs protected quiet time',
        ],
        [
            'field_key'    => 'problem_today',
            'label'        => 'What problem happens today?',
            'type'         => 'textarea',
            'help'         => 'Describe the current pain in plain language. Do not add metrics unless you have them.',
            'placeholder'  => 'e.g. Routine alerts wake them during the day',
            'sample_value' => 'Routine shared-calendar alerts arrive immediately and can wake a night-shift parent during daytime sleep.',
        ],
        [
            'field_key'    => 'must_haves',
            'label'        => 'Must-haves',
            'type'         => 'textarea',
            'help'         => 'One required behavior per line. Keep wishes and maybes out for now.',
            'placeholder'  => "Toggle on/off\nStart and end time\nActive-state message",
            'sample_value' => "Toggle Quiet Hours on or off\nChoose start and end time\nPause routine shared-calendar alerts during the quiet window\nShow when Quiet Hours is active\nDo not block urgent reminders if urgent reminders already exist",
        ],
        [
            'field_key'    => 'constraints',
            'label'        => 'Known constraints',
            'type'         => 'textarea',
            'help'         => 'Technical, product, timing, or policy facts you already know. Write unknown if you do not know.',
            'placeholder'  => 'e.g. Must fit existing notification settings',
            'sample_value' => 'Should fit inside existing notification settings. Unknown whether KinCalendar already separates routine and urgent reminders.',
        ],
        [
            'field_key'    => 'success_signal',
            'label'        => 'What signal means it worked?',
            'type'         => 'textarea',
            'help'         => 'A user behavior, support signal, or simple product outcome. Avoid fake metrics.',
            'placeholder'  => 'e.g. Users keep calendar alerts on while quieting routine updates',
            'sample_value' => 'A user can keep KinCalendar notifications enabled while avoiding routine alerts during their chosen quiet window.',
        ],
        [
            'field_key'    => 'launch_window',
            'label'        => 'Launch window',
            'type'         => 'text',
            'help'         => 'Target release moment if known. If not known, say unknown.',
            'placeholder'  => 'e.g. Next family-sharing release',
            'sample_value' => 'Next family-sharing release; exact date unknown',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-the-problem',
            'title'            => 'Name the problem',
            'summary'          => 'Turn the feature idea into a plain problem brief with constraints and unknowns.',
            'why_it_matters'   => 'Teams often start by describing the solution and skip the pain. This step anchors the feature in a real problem before anyone debates screens. Common mistakes: inventing usage metrics, adding legal or engineering estimates, or turning a small feature into a platform. Need help if the answer guesses? Retry and say "mark missing facts as unknown." You are ready when the problem can be repeated without extra context.',
            'unlocks_text'     => 'Approving unlocks the user-benefit brief.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are a product coach helping write a feature problem brief. Use ONLY the facts below. Do not invent engineering hours, legal requirements, user counts, analytics, or roadmap promises.

Feature name: {{feature_name}}
Product context: {{product_context}}
User type: {{user_type}}
Problem today: {{problem_today}}
Known constraints: {{constraints}}
Success signal: {{success_signal}}
Launch window: {{launch_window}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Feature in one sentence
One sentence naming the feature and the user-facing job it should do.

## Problem today
Two or three factual sentences about the current pain. No invented metrics.

## User impact
One or two sentences explaining why the problem matters to the stated user.

## Known constraints
Restate only constraints provided. If something is unknown, say unknown.

## Unknowns to confirm
Three to five bullets for missing facts the team should confirm before build.

Keep all five headings in order. Under 320 words. Plain language.
TXT,
            'example_response' => $problemExample,
            'output_sections' => [
                ['key' => 'feature_sentence', 'heading' => 'Feature in one sentence', 'required' => true],
                ['key' => 'problem_today', 'heading' => 'Problem today', 'required' => true],
                ['key' => 'user_impact', 'heading' => 'User impact', 'required' => true],
                ['key' => 'known_constraints', 'heading' => 'Known constraints', 'required' => true],
                ['key' => 'unknowns', 'heading' => 'Unknowns to confirm', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Problem is clear', 'help' => 'The pain is understandable without reading the solution list.',
                 'evidence_sections' => ['problem_today', 'user_impact']],
                ['label' => 'No fake certainty', 'help' => 'Missing facts are marked as unknown instead of guessed.',
                 'evidence_sections' => ['known_constraints', 'unknowns']],
                ['label' => 'Feature stays one sentence', 'help' => 'The feature name and job are visible in a single sentence.',
                 'evidence_sections' => ['feature_sentence']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-who-benefits',
            'title'            => 'Name who benefits',
            'summary'          => 'Identify the primary user, situation, benefit, and evidence limits.',
            'why_it_matters'   => 'A feature for everyone is hard to build and harder to judge. This step picks the first user so scope has a center. Common mistakes: naming every possible customer, inventing personas, or claiming research you have not done. Need help if the AI writes a fictional profile? Retry with "use role-level facts only." You are ready when the spec says who gets relief first.',
            'unlocks_text'     => 'Approving unlocks must-have requirements.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are clarifying who benefits from a feature. Use ONLY the pantry facts and the approved problem brief. Do not invent demographics, quotes, research findings, or market size.

Feature name: {{feature_name}}
Product context: {{product_context}}
User type: {{user_type}}
Problem today: {{problem_today}}
Success signal: {{success_signal}}

Approved problem brief:
{{artifact:name-the-problem}}

Produce Markdown with these exact headings:

## Primary user
One or two sentences naming the first user who benefits.

## User situation
Two sentences describing the situation when the problem appears.

## Benefit statement
One sentence connecting the feature to the relief it creates.

## Secondary effects
One or two cautious sentences about nearby effects for other users or the product. Do not claim outcomes as guaranteed.

## Evidence limits
One or two sentences naming what the team still does not know.

Keep all five headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $benefitsExample,
            'output_sections' => [
                ['key' => 'primary_user', 'heading' => 'Primary user', 'required' => true],
                ['key' => 'user_situation', 'heading' => 'User situation', 'required' => true],
                ['key' => 'benefit_statement', 'heading' => 'Benefit statement', 'required' => true],
                ['key' => 'secondary_effects', 'heading' => 'Secondary effects', 'required' => true],
                ['key' => 'evidence_limits', 'heading' => 'Evidence limits', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Primary user is specific', 'help' => 'The first beneficiary is a concrete role or segment, not "all users."',
                 'evidence_sections' => ['primary_user']],
                ['label' => 'Benefit follows the problem', 'help' => 'The benefit answers the pain named in the problem brief.',
                 'evidence_sections' => ['user_situation', 'benefit_statement']],
                ['label' => 'Research is not invented', 'help' => 'The answer does not claim interviews, metrics, or market proof you did not provide.',
                 'evidence_sections' => ['evidence_limits']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-must-haves',
            'title'            => 'Write must-haves',
            'summary'          => 'Convert the idea into required behaviors, control rules, and edge cases.',
            'why_it_matters'   => 'Must-haves are the line between a useful first version and a wishlist. This step turns requirements into buildable behavior without pretending to know the implementation. Common mistakes: mixing maybes with requirements, adding analytics or admin tools, or forgetting edge cases like empty states. Need help if the list balloons? Retry with "only first-version must-haves." You are ready when every item is needed for the stated problem.',
            'unlocks_text'     => 'Approving unlocks non-goals and parked ideas.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are writing first-version feature requirements. Use ONLY the facts and approved artifacts. Do not invent backend architecture, design files, pricing, legal steps, or engineering estimates.

Feature name: {{feature_name}}
Must-haves from pantry:
{{must_haves}}
Known constraints:
{{constraints}}

Approved problem brief:
{{artifact:name-the-problem}}

Approved user-benefit brief:
{{artifact:name-who-benefits}}

Produce Markdown with these exact headings:

## Must-have list
A numbered list of 4 to 7 required behaviors. Each item starts with a user or system action.

## Requirement notes
Short notes clarifying wording, placement, or behavior using only known facts.

## User control
Explain what the user can choose or change.

## Edge cases to handle
Three to five bullets for edge cases implied by the facts.

## Must-not-invent boundary
One or two sentences naming details the spec must not add without new facts.

Keep all five headings in order. Under 360 words.
TXT,
            'example_response' => $mustHavesExample,
            'output_sections' => [
                ['key' => 'must_have_list', 'heading' => 'Must-have list', 'required' => true],
                ['key' => 'requirement_notes', 'heading' => 'Requirement notes', 'required' => true],
                ['key' => 'user_control', 'heading' => 'User control', 'required' => true],
                ['key' => 'edge_cases', 'heading' => 'Edge cases to handle', 'required' => true],
                ['key' => 'invent_boundary', 'heading' => 'Must-not-invent boundary', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Requirements are first-version', 'help' => 'The list contains only needed behavior, not stretch ideas.',
                 'evidence_sections' => ['must_have_list']],
                ['label' => 'User control is explicit', 'help' => 'The spec says what the user can choose or change.',
                 'evidence_sections' => ['user_control']],
                ['label' => 'Boundaries stop guessing', 'help' => 'The answer blocks invented implementation, pricing, or legal details.',
                 'evidence_sections' => ['invent_boundary']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-non-goals',
            'title'            => 'Write non-goals',
            'summary'          => 'Draw the scope boundary and park tempting ideas for later.',
            'why_it_matters'   => 'Non-goals protect the build from hidden expansion. This step names what the feature will not do so the team can say no without reopening the whole idea. Common mistakes: writing vague non-goals, cutting something users actually need, or adding future features as promises. Need help if the AI sounds too harsh? Retry and ask for "firm but neutral scope language." You are ready when the boundary helps decisions.',
            'unlocks_text'     => 'Approving unlocks acceptance checks.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are writing non-goals for a lightweight feature spec. Use ONLY the pantry facts and approved artifacts. Do not invent roadmap commitments, team capacity, legal review, or technical debt.

Feature name: {{feature_name}}
Launch window: {{launch_window}}
Known constraints:
{{constraints}}

Approved problem brief:
{{artifact:name-the-problem}}

Approved must-haves:
{{artifact:write-must-haves}}

Produce Markdown with these exact headings:

## Non-goals
Four to six bullets naming what this first version will not do.

## Parked ideas
Two to four tempting ideas that may be considered later, without promising them.

## Scope boundary
Two sentences that define the edge of the first version.

## Why this protects the build
One or two sentences explaining how the boundary reduces risk or confusion.

Keep all four headings in order. Under 300 words. Plain language.
TXT,
            'example_response' => $nonGoalsExample,
            'output_sections' => [
                ['key' => 'non_goals', 'heading' => 'Non-goals', 'required' => true],
                ['key' => 'parked_ideas', 'heading' => 'Parked ideas', 'required' => true],
                ['key' => 'scope_boundary', 'heading' => 'Scope boundary', 'required' => true],
                ['key' => 'protects_build', 'heading' => 'Why this protects the build', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Non-goals are concrete', 'help' => 'Each non-goal names a real excluded behavior or area.',
                 'evidence_sections' => ['non_goals']],
                ['label' => 'Parked ideas are not promises', 'help' => 'Later ideas are framed as optional, not committed roadmap.',
                 'evidence_sections' => ['parked_ideas']],
                ['label' => 'Boundary supports decisions', 'help' => 'The scope line would help reject a new request during build.',
                 'evidence_sections' => ['scope_boundary', 'protects_build']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-acceptance-checks',
            'title'            => 'Write acceptance checks',
            'summary'          => 'Define observable checks, paths, edge cases, and evidence gaps.',
            'why_it_matters'   => 'A spec is not build-ready until the team can tell whether the feature works. This step turns requirements into observable checks without prescribing code. Common mistakes: writing checks that test implementation details, ignoring empty states, or inventing analytics events. Need help if checks are vague? Retry with "use given/when/then-style observable behavior." You are ready when a teammate could test the feature manually.',
            'unlocks_text'     => 'Approving unlocks the final one-page spec and ship checklist.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are writing acceptance checks for a first-version feature. Use ONLY the facts and approved artifacts. Do not invent database fields, API names, analytics events, QA staffing, or legal requirements.

Feature name: {{feature_name}}
Success signal: {{success_signal}}
Known constraints:
{{constraints}}

Approved must-haves:
{{artifact:write-must-haves}}

Approved non-goals:
{{artifact:write-non-goals}}

Produce Markdown with these exact headings:

## Acceptance checks
Four to seven numbered checks in observable given/when/then style or close plain language.

## Happy path
One short paragraph describing the normal successful user flow.

## Edge cases
Three to five bullets for cases testers should try.

## Copy and settings checks
Two or three checks for labels, helper copy, or settings clarity.

## Evidence gap
One or two sentences naming what cannot be checked yet because facts are missing.

Keep all five headings in order. Under 380 words.
TXT,
            'example_response' => $acceptanceExample,
            'output_sections' => [
                ['key' => 'acceptance_checks', 'heading' => 'Acceptance checks', 'required' => true],
                ['key' => 'happy_path', 'heading' => 'Happy path', 'required' => true],
                ['key' => 'edge_cases', 'heading' => 'Edge cases', 'required' => true],
                ['key' => 'copy_settings_checks', 'heading' => 'Copy and settings checks', 'required' => true],
                ['key' => 'evidence_gap', 'heading' => 'Evidence gap', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Checks are observable', 'help' => 'A tester could verify each check without reading code.',
                 'evidence_sections' => ['acceptance_checks']],
                ['label' => 'Edge cases are covered', 'help' => 'The spec includes likely unusual states from the must-haves.',
                 'evidence_sections' => ['edge_cases']],
                ['label' => 'Missing facts stay visible', 'help' => 'The answer names what cannot be known yet instead of guessing.',
                 'evidence_sections' => ['evidence_gap']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-ship-checklist',
            'title'            => 'Pack the ship checklist',
            'summary'          => 'Assemble the one-page spec, build checklist, review questions, risks, and ship decision.',
            'why_it_matters'   => 'The last step should make the feature easier to hand off, not longer to argue about. This step compresses approved work into one page and calls out remaining decisions. Common mistakes: adding new requirements at the end, hiding unknowns, or turning a checklist into a launch plan with dates nobody supplied. Need help if the summary drifts? Retry and require every line to trace to an approved artifact. You are ready when the team can decide what to build next.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished-file export.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are packing a one-page feature spec and ship checklist. Use ONLY the pantry facts and approved artifacts. Do not add new requirements, dates, legal steps, staffing, estimates, or launch claims.

Feature name: {{feature_name}}
Product context: {{product_context}}
Launch window: {{launch_window}}
Success signal: {{success_signal}}

Approved problem brief:
{{artifact:name-the-problem}}

Approved user-benefit brief:
{{artifact:name-who-benefits}}

Approved must-haves:
{{artifact:write-must-haves}}

Approved non-goals:
{{artifact:write-non-goals}}

Approved acceptance checks:
{{artifact:write-acceptance-checks}}

Produce Markdown with these exact headings:

## One-page spec
One compact paragraph covering problem, user, first-version scope, and non-goals.

## Build checklist
Six to eight checkbox items a team can verify before calling the feature ready.

## Review questions
Three to five unanswered questions to resolve before or during build.

## Risks and unknowns
One short paragraph naming the main uncertainty without inventing the answer.

## Ship-ready decision
One sentence saying what must be confirmed before build or ship.

Keep all five headings in order. Under 420 words.
TXT,
            'example_response' => $shipChecklistExample,
            'output_sections' => [
                ['key' => 'one_page_spec', 'heading' => 'One-page spec', 'required' => true],
                ['key' => 'build_checklist', 'heading' => 'Build checklist', 'required' => true],
                ['key' => 'review_questions', 'heading' => 'Review questions', 'required' => true],
                ['key' => 'risks_unknowns', 'heading' => 'Risks and unknowns', 'required' => true],
                ['key' => 'ship_ready_decision', 'heading' => 'Ship-ready decision', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Spec fits one page', 'help' => 'The summary is compact enough to hand to a teammate.',
                 'evidence_sections' => ['one_page_spec']],
                ['label' => 'Checklist is actionable', 'help' => 'Every checkbox can be verified from the approved spec.',
                 'evidence_sections' => ['build_checklist']],
                ['label' => 'Decision names blockers', 'help' => 'The final line says what must be confirmed without hiding unknowns.',
                 'evidence_sections' => ['review_questions', 'ship_ready_decision']],
            ],
        ],
    ],
];
