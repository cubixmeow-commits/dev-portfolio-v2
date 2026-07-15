<?php

declare(strict_types=1);

/**
 * Pack a Release Checklist — executable Cookbook for software-product.
 *
 * Inspired by public release/deploy checklist practices: scope, must-finish
 * work, owners, risks, and go/no-go signals. Ideas only. All copy, examples,
 * and prompts are original SousMeow work. Product Law 002 is enforced by
 * using only known release facts and inventing no engineering hours, tickets,
 * systems, owners, or readiness status.
 */

$releaseFrameExample = <<<'MD'
## Release frame
KinCalendar "Quiet Hours" v1.2 is a release planned for July 18 that adds a quiet-hours setting before notifications ship more widely.

## Product context
KinCalendar helps small teams coordinate shared calendars and notifications. This release is about reducing notification noise during chosen hours.

## Known scope
- Quiet Hours setting in preferences
- Notification mute behavior during quiet hours
- Settings copy and help text
- Basic QA on web and mobile views

## Ship-date note
Use July 18 as the planning date. Do not add engineering-hour estimates, extra platforms, or launch channels that are not in the pantry.
MD;

$mustFinishExample = <<<'MD'
## Must-finish work
| Work item | Why it matters | Done means |
|-----------|----------------|------------|
| Quiet Hours setting in preferences | Users need a place to set quiet hours | Setting can be found, saved, and changed |
| Notification mute behavior during quiet hours | The release promise depends on notifications staying quiet | Test notification does not appear during the selected window |
| Settings copy and help text | Users need to understand what the setting does | Copy is reviewed and appears in the settings view |
| Basic QA on web and mobile views | Known surfaces need a final check | Web and mobile views pass the listed checks |

## Verification notes
Verify the setting saves, the quiet window is respected, and the settings copy appears in the expected views.

## Cut or defer
Do not add new notification preferences, extra platforms, or analytics events unless they are already confirmed work.

## Missing facts
Confirm exact test accounts, release branch, and who signs off on QA if those are not already known.
MD;

$ownersRisksExample = <<<'MD'
## Owner map
| Area | Owner | Decision or handoff |
|------|-------|---------------------|
| Product sign-off | Priya | Confirms scope stays limited to Quiet Hours v1.2 |
| Engineering release | Marco | Confirms known work is merged and ready to ship |
| QA pass | Lena | Confirms web and mobile checks are complete |
| Support copy | Sam | Confirms help text and support note are ready |

## Risk list
- Notification mute behavior may be inconsistent across views.
- Settings copy may overpromise what Quiet Hours controls.
- QA sign-off could be unclear if the owner list changes.

## Escalation notes
If a risk blocks the stated go signal, pause the release decision and ask the named owner for a yes/no status.

## Retry note
If an owner or risk is missing, rerun with the real name or mark it "confirm"; do not assign work to invented people.
MD;

$goNogoExample = <<<'MD'
## Go/no-go checklist
- [ ] Quiet Hours setting can be saved and changed
- [ ] Notification mute behavior works during the selected quiet window
- [ ] Settings copy and help text are reviewed
- [ ] Web and mobile QA checks are complete
- [ ] Named owners have given yes/no status
- [ ] Known risks have an owner or a pause decision

## Go signal
Ship only when Quiet Hours can be enabled, notifications stay quiet during the selected window, and QA says web and mobile checks are complete.

## No-go signals
- The setting does not save or cannot be changed.
- Notifications still appear during quiet hours.
- QA or owner sign-off is missing.
- A named risk is unresolved and affects the go signal.

## Release packet
Release: KinCalendar "Quiet Hours" v1.2 on July 18. Scope: Quiet Hours setting, mute behavior, settings copy, and basic web/mobile QA. Owners: Priya, Marco, Lena, Sam. Decision: go only when the checklist and go signal are true.
MD;

return [
    'slug'                => 'pack-a-release-checklist',
    'title'               => 'Pack a Release Checklist',
    'tagline'             => 'Turn a ship date into a calm checklist of known work, owners, and go/no-go signals.',
    'description'         => "Release stress grows when scope, owners, and go/no-go signals live in separate threads. This Cookbook turns one planned release into a compact checklist grounded in known work. Enter only the release facts, owners, risks, and go signal you already have. Every step is told to invent nothing about engineering hours, ticket status, systems, or readiness. You leave with a release packet your team can review before saying go.",
    'primary_category'    => 'software-product',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Product managers, founders, and software teams preparing a small release or deploy decision',
    'outcome'             => 'release frame, must-finish work, owner/risk map, and go/no-go checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 40,
    'demo_completed_runs' => 156,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 15,
    'stages' => [
        ['title' => 'Scope', 'summary' => 'Frame the release and known scope.'],
        ['title' => 'Prep', 'summary' => 'List must-finish work, owners, and risks.'],
        ['title' => 'Go', 'summary' => 'Pack the go/no-go checklist and release packet.'],
    ],
    'fields' => [
        [
            'field_key'    => 'release_name',
            'label'        => 'Release name',
            'type'         => 'text',
            'help'         => 'Name the release exactly as the team uses it.',
            'placeholder'  => 'e.g. KinCalendar "Quiet Hours" v1.2',
            'sample_value' => 'KinCalendar "Quiet Hours" v1.2',
        ],
        [
            'field_key'    => 'product_context',
            'label'        => 'Product context',
            'type'         => 'text',
            'help'         => 'One sentence about the product and what this release changes.',
            'placeholder'  => 'e.g. Shared calendar app adding quiet-hours notification controls',
            'sample_value' => 'KinCalendar helps small teams coordinate shared calendars and notifications. This release reduces notification noise during chosen hours.',
        ],
        [
            'field_key'    => 'ship_date',
            'label'        => 'Ship date',
            'type'         => 'text',
            'help'         => 'The planned date or window. Use the date you know.',
            'placeholder'  => 'e.g. July 18',
            'sample_value' => 'July 18',
        ],
        [
            'field_key'    => 'known_work',
            'label'        => 'Known work',
            'type'         => 'textarea',
            'help'         => 'List real work items. No estimates or invented tickets.',
            'placeholder'  => "Quiet Hours setting\nNotification mute behavior\nSettings copy\nWeb/mobile QA",
            'sample_value' => "Quiet Hours setting in preferences\nNotification mute behavior during quiet hours\nSettings copy and help text\nBasic QA on web and mobile views",
        ],
        [
            'field_key'    => 'owners',
            'label'        => 'Owners',
            'type'         => 'textarea',
            'help'         => 'List real owners and areas. If unknown, write confirm.',
            'placeholder'  => "Product: Priya\nEngineering: Marco\nQA: Lena",
            'sample_value' => "Product sign-off: Priya\nEngineering release: Marco\nQA pass: Lena\nSupport copy: Sam",
        ],
        [
            'field_key'    => 'risks',
            'label'        => 'Risks',
            'type'         => 'text',
            'help'         => 'Known risks or worries. Keep them factual.',
            'placeholder'  => 'e.g. Notification behavior may differ between web and mobile',
            'sample_value' => 'Notification mute behavior may be inconsistent across views; settings copy may overpromise what Quiet Hours controls',
        ],
        [
            'field_key'    => 'go_signal',
            'label'        => 'Go signal',
            'type'         => 'text',
            'help'         => 'What must be true to ship.',
            'placeholder'  => 'e.g. Setting saves, notifications mute, QA complete',
            'sample_value' => 'Quiet Hours can be enabled, notifications stay quiet during the selected window, and QA says web and mobile checks are complete',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'frame-the-release',
            'title'            => 'Frame the release',
            'summary'          => 'Turn the release name, product context, known work, and ship date into one scope frame.',
            'why_it_matters'   => 'A release checklist starts by saying what is actually shipping. This step keeps scope from expanding before prep begins. Common mistakes: adding unlisted features, assuming platforms, inventing ticket status, or estimating engineering hours. Need help if the AI expands scope? Retry and tell it to use only the pantry facts. You are ready when the frame names the release without adding work.',
            'unlocks_text'     => 'Approving unlocks the must-finish work list.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are framing a software release. Follow Product Law 002: invent nothing. Use only the pantry facts below. Do not invent engineering hours, ticket IDs, deployment systems, extra platforms, owners, or readiness status.

Release: {{release_name}}
Product context: {{product_context}}
Ship date: {{ship_date}}
Known work:
{{known_work}}

Produce Markdown with these exact headings:

## Release frame
One sentence naming the release, ship date, and purpose.

## Product context
One or two sentences from the supplied product context.

## Known scope
Bullets using only the supplied known work.

## Ship-date note
One sentence explaining how to treat the ship date without inventing estimates.

Keep all four headings in order. Under 220 words.
TXT,
            'example_response' => $releaseFrameExample,
            'output_sections' => [
                ['key' => 'release_frame', 'heading' => 'Release frame', 'required' => true],
                ['key' => 'product_context', 'heading' => 'Product context', 'required' => true],
                ['key' => 'known_scope', 'heading' => 'Known scope', 'required' => true],
                ['key' => 'ship_date_note', 'heading' => 'Ship-date note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Scope uses known work', 'help' => 'The release frame does not add unlisted features.',
                 'evidence_sections' => ['known_scope']],
                ['label' => 'Ship date is stated plainly', 'help' => 'The date is used as a planning fact, not a guarantee.',
                 'evidence_sections' => ['release_frame', 'ship_date_note']],
                ['label' => 'No invented estimates', 'help' => 'The frame avoids engineering hours, ticket status, or readiness claims.',
                 'evidence_sections' => ['ship_date_note']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'list-must-finish-work',
            'title'            => 'List must-finish work',
            'summary'          => 'Convert known work into a must-finish table with done meanings and missing facts.',
            'why_it_matters'   => 'Teams ship calmly when "done" means the same thing to everyone. This step turns known work into checkable finish lines. Common mistakes: sneaking in nice-to-haves, writing vague done states, or inventing QA coverage. Need help if the AI adds tickets or hours? Retry and require work items from the approved frame only. You are ready when every must-finish item can be checked yes/no.',
            'unlocks_text'     => 'Approving unlocks owner and risk mapping.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are making a must-finish release work list. Use only known work and the approved release frame. Do not invent estimates, tickets, tests, branches, or systems.

Known work:
{{known_work}}

Approved release frame:
{{artifact:frame-the-release}}

Produce Markdown with these exact headings:

## Must-finish work
A table with columns Work item, Why it matters, Done means. Use only known work items.

## Verification notes
Two or three bullets naming checks implied by the known work. No invented tools.

## Cut or defer
One sentence naming work not to add unless already confirmed.

## Missing facts
One sentence naming facts to confirm if they are absent.

Keep all four headings in order. Under 280 words.
TXT,
            'example_response' => $mustFinishExample,
            'output_sections' => [
                ['key' => 'must_finish_work', 'heading' => 'Must-finish work', 'required' => true],
                ['key' => 'verification_notes', 'heading' => 'Verification notes', 'required' => true],
                ['key' => 'cut_or_defer', 'heading' => 'Cut or defer', 'required' => true],
                ['key' => 'missing_facts', 'heading' => 'Missing facts', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Every item has done means', 'help' => 'Must-finish work can be checked yes/no.',
                 'evidence_sections' => ['must_finish_work']],
                ['label' => 'Nice-to-haves are contained', 'help' => 'The list does not add unconfirmed work.',
                 'evidence_sections' => ['cut_or_defer']],
                ['label' => 'Unknowns are named', 'help' => 'Missing facts are marked for confirmation instead of invented.',
                 'evidence_sections' => ['missing_facts']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'assign-owners-and-risks',
            'title'            => 'Assign owners and risks',
            'summary'          => 'Map known owners to release areas and pair risks with clear escalation notes.',
            'why_it_matters'   => 'A checklist without owners becomes a wish list. This step puts real names or confirm markers beside work and risks. Common mistakes: assigning work to imaginary people, hiding risk in vague language, or treating every concern as a blocker. Need help if the AI invents owners? Retry and require only names from the owners field. You are ready when each risk has someone to ask or a clear confirm gap.',
            'unlocks_text'     => 'Approving unlocks the go/no-go checklist.',
            'est_minutes'      => 10,
            'prompt_template'  => <<<'TXT'
You are mapping owners and risks for a release. Use only supplied owners, risks, and approved work. Do not invent names, teams, escalation paths, severity labels, or readiness.

Owners:
{{owners}}

Known risks:
{{risks}}

Approved work:
{{artifact:list-must-finish-work}}

Produce Markdown with these exact headings:

## Owner map
A table with columns Area, Owner, Decision or handoff. Use supplied owners; if missing, write confirm.

## Risk list
Bullets using only supplied risks and risks directly implied by approved work.

## Escalation notes
One or two sentences explaining when to pause and ask a named owner for yes/no status.

## Retry note
One sentence explaining what to add if an owner or risk is missing.

Keep all four headings in order. Under 260 words.
TXT,
            'example_response' => $ownersRisksExample,
            'output_sections' => [
                ['key' => 'owner_map', 'heading' => 'Owner map', 'required' => true],
                ['key' => 'risk_list', 'heading' => 'Risk list', 'required' => true],
                ['key' => 'escalation_notes', 'heading' => 'Escalation notes', 'required' => true],
                ['key' => 'retry_note', 'heading' => 'Retry note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Owners are real or marked confirm', 'help' => 'The map uses supplied owner names or clear gaps.',
                 'evidence_sections' => ['owner_map']],
                ['label' => 'Risks are factual', 'help' => 'Risk language stays tied to supplied facts or approved work.',
                 'evidence_sections' => ['risk_list']],
                ['label' => 'Escalation is yes/no', 'help' => 'The notes say when to pause and ask for status.',
                 'evidence_sections' => ['escalation_notes']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-go-nogo-checklist',
            'title'            => 'Pack go/no-go checklist',
            'summary'          => 'Assemble the final release checklist, go signal, no-go signals, and release packet.',
            'why_it_matters'   => 'The final decision should not depend on memory or optimism. This step turns approved scope, work, owners, and risks into a practical go/no-go packet. Common mistakes: making the checklist too broad, inventing rollback plans, or ignoring the stated go signal. Need help if the AI adds deployment systems or hours? Retry and ban any item not present in the approved artifacts. You are ready when the checklist can be read aloud in the release meeting.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 12,
            'prompt_template'  => <<<'TXT'
You are packing a go/no-go release checklist. Use only the pantry and approved artifacts. Do not invent engineering hours, rollback systems, deploy steps, owners, tickets, or readiness status.

Release: {{release_name}}
Ship date: {{ship_date}}
Go signal: {{go_signal}}

Approved frame:
{{artifact:frame-the-release}}

Approved must-finish work:
{{artifact:list-must-finish-work}}

Approved owners and risks:
{{artifact:assign-owners-and-risks}}

Produce Markdown with these exact headings:

## Go/no-go checklist
Six to eight checkbox lines based on approved work, owners, risks, and go signal.

## Go signal
One sentence restating what must be true to ship.

## No-go signals
Three to five bullets naming conditions that should pause the release.

## Release packet
One compact paragraph with release, date, scope, owners, and decision rule.

Keep all four headings in order. Under 300 words.
TXT,
            'example_response' => $goNogoExample,
            'output_sections' => [
                ['key' => 'go_nogo_checklist', 'heading' => 'Go/no-go checklist', 'required' => true],
                ['key' => 'go_signal', 'heading' => 'Go signal', 'required' => true],
                ['key' => 'no_go_signals', 'heading' => 'No-go signals', 'required' => true],
                ['key' => 'release_packet', 'heading' => 'Release packet', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Checklist is actionable', 'help' => 'Each checkbox can be verified before the release decision.',
                 'evidence_sections' => ['go_nogo_checklist']],
                ['label' => 'Go signal matches pantry', 'help' => 'The go signal restates the supplied release condition.',
                 'evidence_sections' => ['go_signal']],
                ['label' => 'No-go signals protect the release', 'help' => 'Pause conditions are tied to known work, owners, or risks.',
                 'evidence_sections' => ['no_go_signals']],
            ],
        ],
    ],
];
