<?php

declare(strict_types=1);

/**
 * Prep for an Interview — executable Cookbook for career-freelance.
 *
 * Inspired by public STAR and career-center interview prep teaching. Ideas
 * only. All copy, examples, and prompts are original SousMeow work. No source
 * wording is copied.
 */

$roleNeedsExample = <<<'MD'
## Role snapshot
Junior Product Designer at Northbeam Studio: an early-career design role where the candidate needs to show clear thinking, curiosity, and follow-through on small product problems.

## Likely signals
- Can explain design choices in plain language.
- Can work from messy input without pretending everything was obvious.
- Can collaborate with non-design teammates and accept critique.
- Can show basic product judgment, not only visual polish.

## Evidence to prepare
Use the student transit app redesign, the campus food-pantry sign-up flow, and the volunteer flyer project because they show research, iteration, and communication.

## Unknowns to avoid
Do not assume Northbeam Studio uses a specific design tool, has a certain client type, or wants portfolio depth beyond what the posting and your facts say.
MD;

$storyBankExample = <<<'MD'
## Story bank
1. Transit app redesign: interviewed classmates, found confusing route labels, changed the trip-planning flow, and tested the new labels with five students.
2. Food-pantry sign-up flow: simplified an intake form after volunteers said people abandoned it.
3. Volunteer flyer project: made three versions, heard critique from the organizer, and revised the hierarchy.
4. Capstone handoff: organized Figma frames and wrote notes so a developer could understand states.

## Best raw details
- Five student test sessions for the transit app.
- Abandoned form problem on the pantry project.
- Critique led to a clearer flyer hierarchy.
- Handoff notes made the capstone easier to review.

## Gaps to fill
Add exact dates, your role on any team project, and one measurable outcome if you have it. If you do not have a metric, say what changed without numbers.

## Keep off limits
Do not claim paid client work, conversion lift, or senior leadership responsibility unless those facts are true.
MD;

$skillTagsExample = <<<'MD'
## Skill map
- User research: transit app interviews; food-pantry volunteer feedback.
- Interaction design: transit trip-planning flow; pantry sign-up flow.
- Visual hierarchy: volunteer flyer revisions.
- Collaboration: capstone handoff notes and critique response.
- Product judgment: choosing simpler labels and fewer form fields.

## Best evidence stories
Lead with the transit app for research plus design reasoning. Use the food-pantry flow when asked about simplifying a user journey. Use the flyer when asked about feedback.

## Claims to avoid
Avoid saying "I owned product strategy," "I increased sign-ups," or "I led a team" unless you can prove those details.

## Next draft inputs
Turn the transit app, pantry flow, and flyer stories into 60- to 90-second answers. Keep the capstone handoff as a shorter support example.
MD;

$starAnswersExample = <<<'MD'
## STAR answer drafts
1. Tell me about a design problem you solved.
Situation: In a class project, the student transit app made route choices hard to scan. Task: I needed to make trip planning clearer for students. Action: I interviewed classmates, noticed route labels caused hesitation, redesigned the flow, and tested new labels with five students. Result: The revised flow was easier to explain, and I learned to test wording before polishing screens.

2. Tell me about feedback you used.
Situation: A volunteer organizer said my first flyer version buried the event details. Task: I needed to make the flyer easier to act on. Action: I made three versions, asked what was still hard to find, and revised the hierarchy. Result: The final version put date, place, and sign-up action first.

## Strongest proof
The transit story shows research, interaction design, and product judgment in one answer.

## Shorten for speaking
Keep each answer under 90 seconds by naming the problem, one action, and one result. Leave side details for follow-up questions.

## Follow-up details
Be ready to explain what changed in the route labels, what you asked in interviews, and what you would test next.
MD;

$hardRepliesExample = <<<'MD'
## Hard question replies
1. Why are you ready for this role if you are junior?
I am early in my career, and my strongest pattern is learning quickly from real users and critique. In the transit app and food-pantry projects, I used feedback to make flows clearer instead of defending my first draft.

2. What is a weakness in your portfolio?
I have fewer shipped examples than an experienced designer. I can be clear about what was class, volunteer, or capstone work, and I can show how I made decisions inside those constraints.

3. Tell me about a time you disagreed with feedback.
On the flyer project, I first thought the visual style was the issue, but the organizer's feedback showed the action details were buried. I changed the hierarchy and learned to separate taste from usability.

## Bridge back to evidence
When a question feels broad, return to one real project, the choice you made, and what you learned.

## Lines not to use
Do not say you have no weaknesses, that you work best alone, or that Northbeam Studio is your dream company unless you have a real reason ready.

## Calm practice note
Pause before answering. It is fine to say, "Let me use the transit app project as the example."
MD;

$questionsAskExample = <<<'MD'
## Questions to ask
1. What kinds of design problems would a junior designer handle in the first three months?
2. How does the team give feedback on early design work?
3. What makes a junior designer successful at Northbeam Studio?
4. How do designers learn about users or clients before a project starts?

## Why these work
They focus on the role, feedback, learning, and work process without pretending you know how Northbeam Studio operates.

## Questions to skip
Skip questions about salary, promotion timelines, or remote rules until the right stage unless the interviewer opens that topic.
MD;

$rehearsalPlanExample = <<<'MD'
## Rehearsal schedule
- Round 1: Read the three main stories out loud once without timing.
- Round 2: Answer five likely questions, aiming for 60 to 90 seconds each.
- Round 3: Practice the hard-question replies with one pause before each answer.
- Final pass: Ask your four questions out loud so they sound natural.

## Out-loud drills
1. Start each story with one sentence of context.
2. Say the action in active voice: "I interviewed," "I revised," "I tested."
3. End with what changed or what you learned.

## Feedback checklist
- [ ] Answer names a real project.
- [ ] Action is clear.
- [ ] Result or learning is honest.
- [ ] No invented company facts.

## If I freeze
Say, "I can answer that with the transit app project," then use Situation, Task, Action, Result in order.
MD;

$dayChecklistExample = <<<'MD'
## Day-of checklist
- [ ] Portfolio link opens.
- [ ] Three story titles are written on one note card.
- [ ] Interview time, format, and link or address are confirmed.
- [ ] Questions to ask are copied somewhere easy to find.
- [ ] Water, charger, and quiet space are ready if remote.

## Story cheat sheet
- Transit app: research, labels, tested flow.
- Food-pantry flow: simplified form after volunteer feedback.
- Flyer project: critique, hierarchy, clearer action.

## Opening note
"I am excited to talk about the junior product designer role and how my class and volunteer projects have helped me practice research, iteration, and clear handoff."

## After-interview follow-up
Send a brief thank-you that names one real topic from the conversation and restates interest without adding new claims.
MD;

return [
    'slug'                => 'prep-for-an-interview',
    'title'               => 'Prep for an Interview',
    'tagline'             => 'Turn your real stories into clear answers you can rehearse out loud.',
    'description'         => "Interviews get easier when your real examples are already sorted. This Cookbook helps you map what the role needs, choose honest stories, turn them into clear answers, prepare hard-question replies, and rehearse out loud. Enter only facts you know about the role, company or client, your background, and the format. Every prompt is told not to invent company facts, achievements, or interview logistics.",
    'primary_category'    => 'career-freelance',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Job seekers, freelancers, and students preparing for an interview with real stories instead of memorized scripts',
    'outcome'             => 'role-needs map, story bank, skill tags, STAR answers, hard-question replies, questions to ask, rehearsal plan, and day-of checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 35,
    'demo_completed_runs' => 356,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 19,
    'stages' => [
        ['title' => 'Stories', 'summary' => 'Find role needs and choose honest examples before drafting answers.'],
        ['title' => 'Answers', 'summary' => 'Turn selected stories into clear replies for likely and hard questions.'],
        ['title' => 'Rehearse', 'summary' => 'Prepare questions, practice out loud, and pack the day-of checklist.'],
    ],
    'fields' => [
        [
            'field_key'    => 'role_title',
            'label'        => 'Role title',
            'type'         => 'text',
            'help'         => 'Use the exact role or opportunity name if you have it.',
            'placeholder'  => 'e.g. Junior product designer',
            'sample_value' => 'Junior product designer',
        ],
        [
            'field_key'    => 'company_or_client',
            'label'        => 'Company or client',
            'type'         => 'text',
            'help'         => 'Name the organization or person interviewing you. If unknown, say unknown.',
            'placeholder'  => 'e.g. Northbeam Studio',
            'sample_value' => 'Northbeam Studio',
        ],
        [
            'field_key'    => 'your_background',
            'label'        => 'Your background',
            'type'         => 'textarea',
            'help'         => 'A short honest summary of your experience, training, and constraints.',
            'placeholder'  => 'e.g. Recent bootcamp graduate with two class projects...',
            'sample_value' => 'Recent design certificate graduate with class projects in mobile flows, volunteer design work, and a capstone handoff. No full-time product design role yet.',
        ],
        [
            'field_key'    => 'stories_you_have',
            'label'        => 'Stories you can tell',
            'type'         => 'textarea',
            'help'         => 'Projects, jobs, volunteer work, class work, conflicts, saves, or lessons. One per line if possible.',
            'placeholder'  => "Project, what happened, what you did\nAnother story...",
            'sample_value' => "Student transit app redesign: interviewed classmates, changed confusing route labels, tested with five students\nFood-pantry sign-up flow: simplified intake after volunteers said people abandoned it\nVolunteer flyer project: used organizer critique to revise visual hierarchy\nCapstone handoff: organized Figma states and notes for developer review",
        ],
        [
            'field_key'    => 'skills_to_show',
            'label'        => 'Skills you want to show',
            'type'         => 'textarea',
            'help'         => 'Skills from the posting or your goal. Do not add skills you cannot support with a story.',
            'placeholder'  => "User research\nCollaboration\nClear communication",
            'sample_value' => "User research\nInteraction design\nVisual hierarchy\nCollaboration\nProduct judgment\nClear handoff",
        ],
        [
            'field_key'    => 'questions_you_fear',
            'label'        => 'Questions you are worried about',
            'type'         => 'textarea',
            'help'         => 'List the questions that make you freeze, including gaps or weaknesses.',
            'placeholder'  => "Why should we hire you?\nTell me about a weakness...",
            'sample_value' => "Why are you ready for this role if you are junior?\nWhat is a weakness in your portfolio?\nTell me about a time you disagreed with feedback",
        ],
        [
            'field_key'    => 'interview_format',
            'label'        => 'Interview format',
            'type'         => 'select',
            'help'         => 'Pick the closest known format. If details are unknown, use the closest fit and do not invent logistics.',
            'options'      => [
                'Phone screen',
                'Video interview',
                'In-person interview',
                'Panel interview',
                'Portfolio review',
                'Unknown',
            ],
            'sample_value' => 'Video interview',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'map-role-needs',
            'title'            => 'Map what the role needs',
            'summary'          => 'Translate the role, company or client, and your target skills into interview signals to prepare for.',
            'why_it_matters'   => 'Good answers start with what the role needs to hear, not with memorized slogans. This step separates known role facts from guesses. Common mistakes: inventing company values, assuming interviewer priorities, or trying to prove every possible skill. Need help if the output sounds too certain? Paste again and require "unknown" where evidence is missing. You are ready when the role needs are specific but honest.',
            'unlocks_text'     => 'Approving unlocks the story bank.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are an interview prep coach. Use ONLY the pantry facts. Do not invent company values, interviewer names, hiring criteria, pay, or team structure.

Role: {{role_title}}
Company/client: {{company_or_client}}
Background:
{{your_background}}
Skills to show:
{{skills_to_show}}
Interview format: {{interview_format}}

Produce Markdown with these exact headings:

## Role snapshot
Two sentences naming the opportunity and what the candidate must show from the facts.

## Likely signals
Four to six bullets of skills or behaviors to demonstrate. Mark uncertainty if inferred.

## Evidence to prepare
One short paragraph naming which pantry stories or background facts can support those signals.

## Unknowns to avoid
Three to five things not to claim because they were not supplied.

Keep headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $roleNeedsExample,
            'output_sections' => [
                ['key' => 'role_snapshot', 'heading' => 'Role snapshot', 'required' => true],
                ['key' => 'likely_signals', 'heading' => 'Likely signals', 'required' => true],
                ['key' => 'evidence_to_prepare', 'heading' => 'Evidence to prepare', 'required' => true],
                ['key' => 'unknowns_to_avoid', 'heading' => 'Unknowns to avoid', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Role facts stay honest', 'help' => 'The snapshot does not invent company details or interviewer priorities.',
                 'evidence_sections' => ['role_snapshot', 'unknowns_to_avoid']],
                ['label' => 'Signals connect to skills', 'help' => 'The likely signals reflect skills you entered, not random traits.',
                 'evidence_sections' => ['likely_signals']],
                ['label' => 'Evidence is prepare-able', 'help' => 'You can point to real background or story material for the signals.',
                 'evidence_sections' => ['evidence_to_prepare']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'pick-story-bank',
            'title'            => 'Pick your story bank',
            'summary'          => 'Choose the strongest real examples and note what details are still missing.',
            'why_it_matters'   => 'Interview answers feel steadier when you have a small bank of true stories. This step keeps weak or invented stories out. Common mistakes: turning every project into a heroic save, adding metrics you do not have, or ignoring small but useful examples. Need help if a story sounds inflated? Paste again and ask for plain one-line story summaries. You are ready when each story has a real action you took.',
            'unlocks_text'     => 'Approving unlocks skill tagging for each story.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are helping choose interview stories. Use ONLY the pantry facts and approved role map. Do not invent outcomes, titles, metrics, clients, or responsibilities.

Role: {{role_title}}
Company/client: {{company_or_client}}
Stories available:
{{stories_you_have}}
Background:
{{your_background}}

Approved role map:
{{artifact:map-role-needs}}

Produce Markdown with these exact headings:

## Story bank
A numbered list of 3 to 5 real stories. For each: situation, action the candidate took, and honest result or learning.

## Best raw details
Bullets of concrete details worth remembering. Use only details provided.

## Gaps to fill
Details the candidate should look up before rehearsing, such as dates or exact role. Do not fill them in.

## Keep off limits
Claims that should not be made without evidence.

Keep headings in order. Under 320 words.
TXT,
            'example_response' => $storyBankExample,
            'output_sections' => [
                ['key' => 'story_bank', 'heading' => 'Story bank', 'required' => true],
                ['key' => 'best_raw_details', 'heading' => 'Best raw details', 'required' => true],
                ['key' => 'gaps_to_fill', 'heading' => 'Gaps to fill', 'required' => true],
                ['key' => 'keep_off_limits', 'heading' => 'Keep off limits', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Stories are real', 'help' => 'Every story comes from your entered background or story list.',
                 'evidence_sections' => ['story_bank']],
                ['label' => 'Missing details stay missing', 'help' => 'The output asks you to fill gaps instead of inventing them.',
                 'evidence_sections' => ['gaps_to_fill']],
                ['label' => 'Inflated claims are blocked', 'help' => 'Off-limit claims protect you from overstating experience.',
                 'evidence_sections' => ['keep_off_limits']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'tag-stories-to-skills',
            'title'            => 'Tag stories to skills',
            'summary'          => 'Match each useful story to the skills it can honestly prove.',
            'why_it_matters'   => 'A good story can answer more than one question if you know which skill it proves. This step makes that map visible. Common mistakes: claiming a skill with no story, using one story for everything, or hiding the strongest example. Need help if the map feels thin? Add more real stories rather than letting the AI invent proof. You are ready when each target skill has at least one honest example or is marked as a gap.',
            'unlocks_text'     => 'Approving unlocks STAR answer drafts.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are mapping interview stories to skills. Use ONLY the pantry facts and approved artifacts. Do not invent achievements, team size, metrics, or tools.

Skills to show:
{{skills_to_show}}

Approved role map:
{{artifact:map-role-needs}}

Approved story bank:
{{artifact:pick-story-bank}}

Produce Markdown with these exact headings:

## Skill map
Bullets mapping each target skill to one or two story names.

## Best evidence stories
Name the top 2 or 3 stories to use most often and why.

## Claims to avoid
Skill claims that are unsupported by the stories.

## Next draft inputs
Which stories should become full answers in the next step.

Keep headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $skillTagsExample,
            'output_sections' => [
                ['key' => 'skill_map', 'heading' => 'Skill map', 'required' => true],
                ['key' => 'best_evidence_stories', 'heading' => 'Best evidence stories', 'required' => true],
                ['key' => 'claims_to_avoid', 'heading' => 'Claims to avoid', 'required' => true],
                ['key' => 'next_draft_inputs', 'heading' => 'Next draft inputs', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Skills have evidence', 'help' => 'Each skill points to a named real story or clear gap.',
                 'evidence_sections' => ['skill_map']],
                ['label' => 'Strongest stories stand out', 'help' => 'You know which examples to practice first.',
                 'evidence_sections' => ['best_evidence_stories']],
                ['label' => 'Unsupported claims are visible', 'help' => 'The map says what not to overstate.',
                 'evidence_sections' => ['claims_to_avoid']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'draft-star-answers',
            'title'            => 'Draft STAR answers',
            'summary'          => 'Turn your best stories into spoken answers with situation, task, action, and result.',
            'why_it_matters'   => 'STAR answers stop you from rambling when a question is broad. This step keeps answers short enough to say out loud while preserving real evidence. Common mistakes: spending too long on context, making the result sound bigger than it was, or writing like a cover letter. Need help if it sounds stiff? Paste again and ask for conversational wording under 90 seconds. You are ready when each answer has action and result, not just background.',
            'unlocks_text'     => 'Approving unlocks replies for hard questions.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are drafting interview answers for speaking. Use ONLY the pantry facts and approved artifacts. Do not invent metrics, job offers, praise, or company facts.

Role: {{role_title}}
Interview format: {{interview_format}}

Approved story bank:
{{artifact:pick-story-bank}}

Approved skill map:
{{artifact:tag-stories-to-skills}}

Produce Markdown with these exact headings:

## STAR answer drafts
Draft 2 or 3 answers. For each: likely question, Situation, Task, Action, Result. Keep results honest and modest if needed.

## Strongest proof
Name the answer with the best evidence and why.

## Shorten for speaking
Rules to keep each answer around 60 to 90 seconds.

## Follow-up details
Facts the candidate should be ready to explain if asked.

Keep headings in order. Under 420 words. Spoken, plain language.
TXT,
            'example_response' => $starAnswersExample,
            'output_sections' => [
                ['key' => 'star_answer_drafts', 'heading' => 'STAR answer drafts', 'required' => true],
                ['key' => 'strongest_proof', 'heading' => 'Strongest proof', 'required' => true],
                ['key' => 'shorten_for_speaking', 'heading' => 'Shorten for speaking', 'required' => true],
                ['key' => 'follow_up_details', 'heading' => 'Follow-up details', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Each answer has action', 'help' => 'The candidate did something specific in every STAR answer.',
                 'evidence_sections' => ['star_answer_drafts']],
                ['label' => 'Results are honest', 'help' => 'No fake metrics or invented praise appear in the results.',
                 'evidence_sections' => ['star_answer_drafts', 'strongest_proof']],
                ['label' => 'Ready to say aloud', 'help' => 'The shortening advice makes the answers practiceable.',
                 'evidence_sections' => ['shorten_for_speaking']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'draft-hard-question-replies',
            'title'            => 'Draft hard-question replies',
            'summary'          => 'Prepare calm, honest replies for the questions that make you freeze.',
            'why_it_matters'   => 'Hard questions are easier when you have a truthful bridge back to evidence. This step avoids defensiveness and fake confidence. Common mistakes: dodging the question, inventing a perfect weakness, or apologizing for every gap. Need help if an answer sounds too polished? Paste again and ask for simpler wording that names the real constraint. You are ready when each hard reply admits the issue and returns to proof.',
            'unlocks_text'     => 'Approving unlocks questions you can ask the interviewer.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are preparing honest replies to hard interview questions. Use ONLY the pantry facts and approved answers. Do not invent weaknesses, excuses, achievements, or interviewer concerns.

Questions the candidate fears:
{{questions_you_fear}}
Background:
{{your_background}}

Approved STAR answers:
{{artifact:draft-star-answers}}

Approved skill map:
{{artifact:tag-stories-to-skills}}

Produce Markdown with these exact headings:

## Hard question replies
Draft replies for up to 3 feared questions. Each reply should answer directly, name a real constraint if needed, and bridge to evidence.

## Bridge back to evidence
One short method for returning to a real story when a question feels broad.

## Lines not to use
Phrases or claims that would sound evasive, inflated, or unsupported.

## Calm practice note
One or two sentences for staying steady before answering.

Keep headings in order. Under 360 words. Invent nothing.
TXT,
            'example_response' => $hardRepliesExample,
            'output_sections' => [
                ['key' => 'hard_question_replies', 'heading' => 'Hard question replies', 'required' => true],
                ['key' => 'bridge_back_to_evidence', 'heading' => 'Bridge back to evidence', 'required' => true],
                ['key' => 'lines_not_to_use', 'heading' => 'Lines not to use', 'required' => true],
                ['key' => 'calm_practice_note', 'heading' => 'Calm practice note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Replies answer directly', 'help' => 'Each feared question gets an actual answer, not a dodge.',
                 'evidence_sections' => ['hard_question_replies']],
                ['label' => 'Evidence bridge is ready', 'help' => 'The candidate knows how to return to a real story.',
                 'evidence_sections' => ['bridge_back_to_evidence']],
                ['label' => 'Inflated lines are cut', 'help' => 'Unsupported or defensive claims are explicitly avoided.',
                 'evidence_sections' => ['lines_not_to_use']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-questions-to-ask',
            'title'            => 'Write questions to ask',
            'summary'          => 'Prepare thoughtful questions that fit the role without pretending you know the company inside out.',
            'why_it_matters'   => 'Questions show how you think and help you decide whether the opportunity fits. This step keeps them grounded in the role instead of fake research. Common mistakes: asking things already answered in the posting, performing flattery, or raising late-stage negotiation too early. Need help if the questions feel generic? Add real posting details, then regenerate. You are ready when each question could teach you something useful.',
            'unlocks_text'     => 'Approving unlocks the rehearsal plan.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are writing candidate questions for an interview. Use ONLY the pantry facts and approved artifacts. Do not invent company programs, interviewer roles, benefits, or culture.

Role: {{role_title}}
Company/client: {{company_or_client}}
Interview format: {{interview_format}}

Approved role map:
{{artifact:map-role-needs}}

Approved hard-question replies:
{{artifact:draft-hard-question-replies}}

Produce Markdown with these exact headings:

## Questions to ask
Four to six questions about the role, work, feedback, success, or next steps. No fake inside knowledge.

## Why these work
One short paragraph explaining what the questions help the candidate learn.

## Questions to skip
Questions to save for later or avoid because they rely on unknown facts.

Keep headings in order. Under 240 words. Invent nothing.
TXT,
            'example_response' => $questionsAskExample,
            'output_sections' => [
                ['key' => 'questions_to_ask', 'heading' => 'Questions to ask', 'required' => true],
                ['key' => 'why_these_work', 'heading' => 'Why these work', 'required' => true],
                ['key' => 'questions_to_skip', 'heading' => 'Questions to skip', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Questions fit the role', 'help' => 'They ask about real work, feedback, success, or next steps.',
                 'evidence_sections' => ['questions_to_ask']],
                ['label' => 'No fake research', 'help' => 'No question claims knowledge of programs or culture not provided.',
                 'evidence_sections' => ['questions_to_ask', 'questions_to_skip']],
                ['label' => 'Usefulness is clear', 'help' => 'The why section explains what the questions help you learn.',
                 'evidence_sections' => ['why_these_work']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'build-rehearsal-plan',
            'title'            => 'Build a rehearsal plan',
            'summary'          => 'Turn answers into out-loud practice rounds with simple feedback checks.',
            'why_it_matters'   => 'Reading answers silently is not rehearsal. This step makes practice audible, short, and repeatable. Common mistakes: memorizing every word, practicing only easy questions, or adding facts mid-rehearsal. Need help if the plan feels too big? Paste again and limit it to three rounds under 30 minutes. You are ready when you know exactly what to say out loud first.',
            'unlocks_text'     => 'Approving unlocks the day-of checklist.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are building an interview rehearsal plan. Use ONLY approved artifacts and pantry format. Do not invent interview length, interviewers, or logistics.

Interview format: {{interview_format}}

Approved STAR answers:
{{artifact:draft-star-answers}}

Approved hard replies:
{{artifact:draft-hard-question-replies}}

Approved questions to ask:
{{artifact:write-questions-to-ask}}

Produce Markdown with these exact headings:

## Rehearsal schedule
Three or four practice rounds the candidate can do out loud.

## Out-loud drills
Three drills for clearer spoken answers.

## Feedback checklist
Four to six checkbox lines for self-review or a practice partner.

## If I freeze
One short recovery script tied to the approved stories.

Keep headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $rehearsalPlanExample,
            'output_sections' => [
                ['key' => 'rehearsal_schedule', 'heading' => 'Rehearsal schedule', 'required' => true],
                ['key' => 'out_loud_drills', 'heading' => 'Out-loud drills', 'required' => true],
                ['key' => 'feedback_checklist', 'heading' => 'Feedback checklist', 'required' => true],
                ['key' => 'if_i_freeze', 'heading' => 'If I freeze', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Practice is out loud', 'help' => 'The schedule requires speaking, not only reading.',
                 'evidence_sections' => ['rehearsal_schedule', 'out_loud_drills']],
                ['label' => 'Feedback is concrete', 'help' => 'The checklist gives observable things to improve.',
                 'evidence_sections' => ['feedback_checklist']],
                ['label' => 'Freeze plan uses real stories', 'help' => 'The recovery script points back to approved evidence.',
                 'evidence_sections' => ['if_i_freeze']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-day-of-checklist',
            'title'            => 'Pack the day-of checklist',
            'summary'          => 'Finish with a practical checklist, story cheat sheet, opening note, and follow-up reminder.',
            'why_it_matters'   => 'The day of an interview is for execution, not rewriting your whole prep. This step packs only what you can use. Common mistakes: adding new claims, inventing logistics, or making the checklist too long to scan. Need help if it becomes a script? Paste again and ask for a one-card version. You are ready when the checklist fits the actual format and your stories are easy to recall.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished interview prep export.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are packing a day-of interview checklist. Use ONLY pantry facts and approved artifacts. Do not invent addresses, links, interviewer names, dress codes, or follow-up details.

Role: {{role_title}}
Company/client: {{company_or_client}}
Interview format: {{interview_format}}

Approved story map:
{{artifact:tag-stories-to-skills}}

Approved questions:
{{artifact:write-questions-to-ask}}

Approved rehearsal plan:
{{artifact:build-rehearsal-plan}}

Produce Markdown with these exact headings:

## Day-of checklist
Five to seven checkbox items practical for the stated format. Use "confirm" for unknown logistics.

## Story cheat sheet
Three to five short story reminders, each under one line.

## Opening note
One natural opening sentence the candidate can adapt.

## After-interview follow-up
One or two sentences on a brief thank-you that invents no conversation details.

Keep headings in order. Under 260 words.
TXT,
            'example_response' => $dayChecklistExample,
            'output_sections' => [
                ['key' => 'day_of_checklist', 'heading' => 'Day-of checklist', 'required' => true],
                ['key' => 'story_cheat_sheet', 'heading' => 'Story cheat sheet', 'required' => true],
                ['key' => 'opening_note', 'heading' => 'Opening note', 'required' => true],
                ['key' => 'after_interview_follow_up', 'heading' => 'After-interview follow-up', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Checklist fits the format', 'help' => 'Items match phone, video, in-person, panel, portfolio, or unknown format honestly.',
                 'evidence_sections' => ['day_of_checklist']],
                ['label' => 'Stories are easy to recall', 'help' => 'The cheat sheet is short enough to scan before speaking.',
                 'evidence_sections' => ['story_cheat_sheet']],
                ['label' => 'Follow-up invents nothing', 'help' => 'The follow-up guidance does not create conversation details before they happen.',
                 'evidence_sections' => ['after_interview_follow_up']],
            ],
        ],
    ],
];
