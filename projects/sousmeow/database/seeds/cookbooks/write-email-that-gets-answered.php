<?php

declare(strict_types=1);

/**
 * Write an Email That Gets Answered — executable Cookbook for writing-publishing.
 *
 * Inspired by public plain-language principles (Digital.gov / Federal Plain
 * Language Guidelines; Section 508 email clarity notes). All copy, examples,
 * and prompts are original SousMeow work. No guideline wording is copied.
 *
 * Structural signature: 3 stages / 3 recipes (distinct from Launch Day Kit's
 * 4/4 sequential campaign assembly).
 */

$readerAskExample = <<<'MD'
## Who this is for
Jordan Lee, building supervisor at Harbor Court Apartments. Jordan gets dozens of repair emails a week and skims on a phone between calls.

## Your relationship
I am a current tenant who pays rent on time and has reported two prior issues by email. We are polite, not friends.

## The one ask
Schedule a technician visit for the kitchen heater that has not warmed past lukewarm since Monday.

## Why it matters now
Without heat for cooking, I am using a space heater that trips the breaker. I need a visit before Friday evening.

## What they already know
They have my unit number (4B), email, and phone from the lease. They do not need the full lease history.

## What good looks like
Jordan replies with a date window or asks one clarifying question, not silence.
MD;

$fullDraftExample = <<<'MD'
## Subject line options
1. Unit 4B: kitchen heater still cold — request a visit this week
2. Need technician for kitchen heater in 4B before Friday
3. Follow-up: kitchen heater in 4B not heating

## Recommended subject
Unit 4B: kitchen heater still cold — request a visit this week

## Why this subject works
Names the unit, the problem, and the ask in one scan. No vague "Quick question."

## Email draft

Subject: Unit 4B: kitchen heater still cold — request a visit this week

Hi Jordan,

I am writing to request a technician visit for the kitchen heater in unit 4B, which has stayed lukewarm since Monday.

I emailed about this on Monday. Without heat for cooking I am using a space heater that trips the breaker, so I need a visit before Friday evening.

Could you please schedule a visit this week and reply with a date window?

Thank you,
Alex Rivera
Unit 4B | (555) 014-2291

## Structure notes
- Ask appears in the first sentence.
- One short why, then one clear ask.
- Contact details sit at the end so Jordan can act without hunting.
MD;

$reviseSendExample = <<<'MD'
## Revised email

Subject: Unit 4B: kitchen heater still cold — request a visit this week

Hi Jordan,

Please schedule a technician for the kitchen heater in unit 4B. It has stayed lukewarm since Monday.

I emailed Monday. I am using a space heater that trips the breaker, so I need a visit before Friday evening.

Can you reply with a date window this week?

Thank you,
Alex Rivera
Unit 4B | (555) 014-2291

## What changed
- First sentence leads with the verb "schedule."
- Cut a soft filler phrase ("I am writing to request").
- Kept one reason and one ask. No stack of extra requests.

## Pre-send checklist
- [ ] Subject states unit, problem, and ask
- [ ] One primary ask only
- [ ] Active voice in the ask
- [ ] No jargon Jordan would not use
- [ ] Contact line present
- [ ] I would send this under my name today
MD;

return [
    'slug'                => 'write-email-that-gets-answered',
    'title'               => 'Write an Email That Gets Answered',
    'tagline'             => 'Write a short email with one clear ask that gets answered.',
    'description'         => "Busy people delete vague emails. This Cookbook helps you name the reader, lock one ask, lead with the point, and revise until the message is short enough to answer. Enter only facts you know about the person, the ask, and the deadline. Every step is told to invent nothing about their calendar or motives. You leave with a send-ready email and a simple checklist.",
    'primary_category'    => 'writing-publishing',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Anyone who needs a clear reply from a busy person: managers, landlords, clients, or teammates',
    'outcome'             => 'reader brief, full short email with subject, revised send-ready email, and a pre-send checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'clay',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 28,
    'demo_completed_runs' => 389,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 7,
    'stages' => [
        ['title' => 'Aim', 'summary' => 'Name the reader and the single ask before any sentences.'],
        ['title' => 'Draft', 'summary' => 'Write a scannable subject and a short body in one pass.'],
        ['title' => 'Send-ready', 'summary' => 'Revise for clarity and pack a pre-send checklist.'],
    ],
    'fields' => [
        [
            'field_key'    => 'who_receives',
            'label'        => 'Who receives this email?',
            'type'         => 'text',
            'help'         => 'Name and role if you know them. One busy human, not a whole department.',
            'placeholder'  => 'e.g. Jordan Lee, building supervisor',
            'sample_value' => 'Jordan Lee, building supervisor at Harbor Court Apartments',
        ],
        [
            'field_key'    => 'your_relationship',
            'label'        => 'How do you know each other?',
            'type'         => 'textarea',
            'help'         => 'Tenant, coworker, client, stranger via form. Mention prior contact if it is real.',
            'placeholder'  => 'e.g. Current tenant; emailed once on Monday',
            'sample_value' => 'Current tenant in unit 4B. I emailed Monday about the same heater. We are polite, not friends.',
        ],
        [
            'field_key'    => 'the_ask',
            'label'        => 'What is the one thing you want them to do?',
            'type'         => 'textarea',
            'help'         => 'One action. If you have two asks, pick the one that unlocks the rest.',
            'placeholder'  => 'e.g. Schedule a technician visit this week',
            'sample_value' => 'Schedule a technician visit for the kitchen heater before Friday evening and reply with a date window.',
        ],
        [
            'field_key'    => 'why_now',
            'label'        => 'Why does this matter now?',
            'type'         => 'textarea',
            'help'         => 'A real consequence or deadline. Skip drama. Stick to facts.',
            'placeholder'  => 'e.g. No heat for cooking; space heater trips breaker',
            'sample_value' => 'Kitchen heater lukewarm since Monday. Space heater trips the breaker. Need a visit before Friday evening.',
        ],
        [
            'field_key'    => 'facts_they_need',
            'label'        => 'Facts they need to act',
            'type'         => 'textarea',
            'help'         => 'Unit numbers, dates, links, amounts. One per line. Only what is true.',
            'placeholder'  => "Unit 4B\nIssue since Monday\nPhone (555) 014-2291",
            'sample_value' => "Unit 4B\nKitchen heater lukewarm since Monday\nPrior email sent Monday\nPhone (555) 014-2291\nNeed visit before Friday evening",
        ],
        [
            'field_key'    => 'tone',
            'label'        => 'Tone',
            'type'         => 'select',
            'help'         => 'How this should sound in their inbox.',
            'options'      => [
                'Polite and direct',
                'Warm and brief',
                'Formal and careful',
                'Firm but fair',
            ],
            'sample_value' => 'Polite and direct',
        ],
        [
            'field_key'    => 'your_signoff',
            'label'        => 'Your name and sign-off details',
            'type'         => 'textarea',
            'help'         => 'Name, optional role, phone or unit. Exactly as it should appear.',
            'placeholder'  => "Alex Rivera\nUnit 4B | (555) 014-2291",
            'sample_value' => "Alex Rivera\nUnit 4B | (555) 014-2291",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-reader-and-ask',
            'title'            => 'Name the reader and the one ask',
            'summary'          => 'Lock who this is for, what you want them to do, and what they already know.',
            'why_it_matters'   => 'Vague emails hide the ask. This step forces one reader and one action before sentences appear. Common mistakes: writing to "whoever handles this," stacking three asks, inventing how busy or annoyed they feel. Need help if the AI invents motives? Paste again and ban mind-reading. You are ready when a stranger could repeat your ask in one sentence.',
            'unlocks_text'     => 'Approving unlocks the subject line and short email draft.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are a plain-language coach helping someone prepare an email brief. Use ONLY the facts below. Do not invent the reader's emotions, calendar, policies, or personal life.

Who receives it: {{who_receives}}
Relationship: {{your_relationship}}
The one ask: {{the_ask}}
Why it matters now: {{why_now}}
Facts they need:
{{facts_they_need}}
Tone: {{tone}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Who this is for
Two sentences: who they are and how they likely read email (busy/skimming is fine only if implied by role; do not invent personality).

## Your relationship
Restate the relationship in plain words. No new history.

## The one ask
One sentence. One action only.

## Why it matters now
One or two factual sentences from the provided urgency. No drama language.

## What they already know
What you can skip because it is already true from the relationship and facts. Invent nothing.

## What good looks like
One sentence describing a successful reply shape (not guaranteeing they will reply).

Keep all six headings in that order. Under 260 words. Tone of the brief: {{tone}}.
TXT,
            'example_response' => $readerAskExample,
            'output_sections' => [
                ['key' => 'who_for', 'heading' => 'Who this is for', 'required' => true],
                ['key' => 'relationship', 'heading' => 'Your relationship', 'required' => true],
                ['key' => 'one_ask', 'heading' => 'The one ask', 'required' => true],
                ['key' => 'why_now', 'heading' => 'Why it matters now', 'required' => true],
                ['key' => 'already_know', 'heading' => 'What they already know', 'required' => true],
                ['key' => 'good_looks_like', 'heading' => 'What good looks like', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Exactly one ask', 'help' => 'You could underline a single action. Nothing else sneaks in.',
                 'evidence_sections' => ['one_ask']],
                ['label' => 'Reader is named specifically', 'help' => 'Not "the team" or "whoever." A real recipient from your facts.',
                 'evidence_sections' => ['who_for']],
                ['label' => 'No invented emotions', 'help' => 'Nothing claims how they feel or what they will definitely do.',
                 'evidence_sections' => ['who_for', 'good_looks_like']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'subject-and-short-draft',
            'title'            => 'Write subject and short email',
            'summary'          => 'Produce subject options and a complete short email a busy reader can answer in one pass.',
            'why_it_matters'   => 'People decide from the subject and first lines. Common mistakes: cute subjects, burying the ask, stacking side requests, inventing ticket numbers. Need help if the body grows past a screen? Paste again and cap the body near 120 words. You are ready when skimming only the subject and first sentence still reveals the ask.',
            'unlocks_text'     => 'Approving unlocks the clarity revise and checklist.',
            'est_minutes'      => 12,
            'prompt_template'  => <<<'TXT'
You are drafting a short plain-language email with a scannable subject. Use ONLY the pantry facts and the approved brief. Do not invent policy numbers, ticket IDs, meetings, fees, legal claims, or manager names.

Who receives it: {{who_receives}}
Tone: {{tone}}
Sign-off details:
{{your_signoff}}
Facts they need:
{{facts_they_need}}

Approved reader-and-ask brief (ground truth):
{{artifact:name-reader-and-ask}}

Produce Markdown with these exact headings. Do not bold headings. Do not wrap the response in a code fence:

## Subject line options
Three subject lines under 70 characters when possible. Each must include the core ask or problem. No "Quick question" or "Following up" alone.

## Recommended subject
Pick one and say why in one sentence.

## Why this subject works
One sentence on scanability.

## Email draft
A full email including Subject line, greeting, body, and sign-off. Body should be short: ask first, brief why, single clear question or request, then sign-off using the provided details. Active voice. Short sentences. No throat-clearing ("I hope this email finds you well"). No bullet dump unless the facts list truly needs it.

## Structure notes
Three bullets explaining how the draft follows "ask first, then context, then close."

Keep these five headings in order. Body under 130 words excluding subject and sign-off. Tone: {{tone}}.
TXT,
            'example_response' => $fullDraftExample,
            'output_sections' => [
                ['key' => 'subject_options', 'heading' => 'Subject line options', 'required' => true],
                ['key' => 'recommended_subject', 'heading' => 'Recommended subject', 'required' => true],
                ['key' => 'why_subject', 'heading' => 'Why this subject works', 'required' => true],
                ['key' => 'email_draft', 'heading' => 'Email draft', 'required' => true],
                ['key' => 'structure_notes', 'heading' => 'Structure notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Subject states the point', 'help' => 'A stranger skimming the inbox knows the topic without opening.',
                 'evidence_sections' => ['subject_options', 'recommended_subject']],
                ['label' => 'Ask appears early', 'help' => 'A reader who stops after three lines still has the request.',
                 'evidence_sections' => ['email_draft']],
                ['label' => 'One primary ask only', 'help' => 'No bonus favors or stacked questions.',
                 'evidence_sections' => ['email_draft']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'revise-and-checklist',
            'title'            => 'Revise for clarity and pack a checklist',
            'summary'          => 'Tighten wording, keep the voice honest, and finish with a pre-send checklist.',
            'why_it_matters'   => 'First drafts often hedge. This step removes fog without turning rude. Common mistakes: passive voice sneaking back, new asks appearing, checklist items that need research you do not have. Need help if the revise gets sharper than your tone choice? Paste again and restore the selected tone. You are ready when you would hit send today.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens finished-file export.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are revising an email for plain clarity. Use ONLY the approved draft and pantry tone. Do not invent new asks, threats, or deadlines.

Tone: {{tone}}
Sign-off details:
{{your_signoff}}

Approved draft:
{{artifact:subject-and-short-draft}}

Approved brief:
{{artifact:name-reader-and-ask}}

Produce Markdown with these exact headings:

## Revised email
Full email (subject, greeting, body, sign-off). Prefer active verbs. Cut filler. Keep one ask. Stay in tone: {{tone}}.

## What changed
Three short bullets naming concrete edits (not vague "improved flow").

## Pre-send checklist
Six checkbox lines the sender can tick. Practical and true to this email (subject, ask, length, tone, facts, willingness to send). Do not invent legal review steps.

Keep these three headings in order. Under 300 words total for the revised email body sections plus notes. Invent nothing.
TXT,
            'example_response' => $reviseSendExample,
            'output_sections' => [
                ['key' => 'revised_email', 'heading' => 'Revised email', 'required' => true],
                ['key' => 'what_changed', 'heading' => 'What changed', 'required' => true],
                ['key' => 'pre_send_checklist', 'heading' => 'Pre-send checklist', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Still one ask', 'help' => 'Revision did not smuggle a second request.',
                 'evidence_sections' => ['revised_email']],
                ['label' => 'Changes are specific', 'help' => 'You can see what got cut or rewritten.',
                 'evidence_sections' => ['what_changed']],
                ['label' => 'Checklist is send-day practical', 'help' => 'Every item is something you can verify in one minute.',
                 'evidence_sections' => ['pre_send_checklist']],
            ],
        ],
    ],
];
