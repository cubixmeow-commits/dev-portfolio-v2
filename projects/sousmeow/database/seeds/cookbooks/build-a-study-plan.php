<?php

declare(strict_types=1);

/**
 * Build a Study Plan — executable Cookbook for learning-teaching.
 *
 * Inspired by public university learning-center concepts (Cornell LSC
 * study / note strategies, five-day exam plans, spaced and retrieval
 * practice). All copy, examples, and prompts are original SousMeow work.
 * No source wording is reproduced.
 */

$successExample = <<<'MD'
## Finish line
Pass the midterm with a score of at least 75%, able to solve standard probability and confidence-interval problems without the textbook open.

## What success looks like in practice
- Explain the difference between mean, median, and mode in one plain paragraph.
- Compute a confidence interval from a small sample with the formula sheet only.
- Spot when a problem asks for probability versus a p-value.

## Non-goals
I am not trying to finish the whole textbook. I am not building a data-science portfolio. This plan covers only Unit 2 and Unit 3 for the midterm.

## Constraints I must respect
I have eight focused hours total before March 20. Evenings after 9pm are too noisy to count as study time.
MD;

$topicMapExample = <<<'MD'
## Topic map
1. Descriptive stats refresh — review, medium weight
2. Probability rules — practice-heavy, high weight (weak spot)
3. Sampling distributions — practice-heavy, high weight
4. Confidence intervals — practice-heavy, high weight (weak spot)
5. Hypothesis testing intro — light skim if time, medium weight
6. Worded exam problems — practice-heavy, high weight

## Priority order
1. Probability rules
2. Confidence intervals
3. Sampling distributions
4. Worded exam problems
5. Descriptive stats refresh
6. Hypothesis testing intro

## Weak spot callouts
Probability rules and confidence intervals trip me when the problem mixes words and symbols. I slow down and guess the formula.

## Out of scope for now
Regression chapters and software-lab demos stay off this plan until after the midterm.
MD;

$weeklyRhythmExample = <<<'MD'
## Weekly rhythm
- Monday 45 min: Probability drills (blank-page recall, then check)
- Tuesday 45 min: Confidence intervals (worked examples, then similar new ones)
- Wednesday 30 min: Sampling distributions (explain out loud from notes)
- Thursday 45 min: Mixed worded problems from the study guide
- Friday 20 min: Quick review of the week's miss list
- Saturday 60 min: Mini practice exam (timer on)
- Sunday: Rest or 20-minute light skim only if a Friday item slipped

## Session template
1. 2 minutes: reopen yesterday's miss list
2. Main block: active practice on one priority topic (no rereading first)
3. 5 minutes: write three cues or questions for next time
4. Stop on time even if unfinished; park leftovers on the miss list

## Guardrails
If a session slips, move it to the next open slot of equal length. Do not double a day. Stop after 8 weekly hours.
MD;

$retrievalKitExample = <<<'MD'
## Cue questions
1. When do I use addition versus multiplication in probability?
2. What does a 95% confidence interval actually claim?
3. Why does a larger sample shrink the interval width?
4. How do I tell a parameter from a statistic in a word problem?
5. What are the steps for a two-sided z-test at a glance?

## Blank-page challenges
1. From memory, write the probability addition rule and one example.
2. Blank paper: set up a confidence interval for a mean with n=30.
3. Explain sampling distribution to a friend in under two minutes without notes.

## Self-check keys
1. Addition for mutually exclusive events; multiplication for independent events.
2. Across many samples, about 95% of such intervals would contain the true mean; it is not the chance for this single interval.
3. Larger n reduces standard error, so the margin of error shrinks.

## How to use this kit
Cover answers. Attempt every cue and blank-page challenge first. Mark misses. Restudy only the missed items, then retry the same cue later the same week.
MD;

$finalWeekExample = <<<'MD'
## Day-by-day plan
- Day 5: Probability rules only. End with five cue questions from the kit.
- Day 4: Confidence intervals + one blank-page challenge.
- Day 3: Mixed sampling and worded problems. 40-minute timed set.
- Day 2: Full short practice exam. Rebuild the miss list from scratch.
- Day 1: Light review of the miss list only. Sleep on time. Pack formula sheet and pencils.

## Day-before checklist
- [ ] Formula sheet printed or written once by hand
- [ ] Miss list fits on one page
- [ ] Sleep target set for tonight
- [ ] Calculator batteries or backup checked
- [ ] Know the start time and room

## If I fall behind
Drop hypothesis-testing skim first. Keep probability and confidence intervals. Never skip sleep to cram a new chapter the night before.
MD;

return [
    'slug'                => 'build-a-study-plan',
    'title'               => 'Build a Study Plan',
    'tagline'             => 'Turn a looming exam or skill goal into a calm, realistic practice schedule.',
    'description'         => "Most people restudy notes and hope it sticks. This Cookbook helps you set a clear finish line, break the subject into chunks, place real practice on real days, and build simple self-tests so you know what still needs work. Enter only facts you already know about your subject, deadline, and available time. Every step is told to invent nothing about your schedule or score. You leave with a plan you can follow this week.",
    'primary_category'    => 'learning-teaching',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Students and self-learners who need a calm plan before an exam, certification, or skill checkpoint',
    'outcome'             => 'success criteria, topic map, weekly study rhythm, retrieval practice kit, and a final-week plan with a day-before checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'pine',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 40,
    'demo_completed_runs' => 214,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 6,
    'stages' => [
        ['title' => 'Aim', 'summary' => 'Name what finished looks like before you schedule any hours.'],
        ['title' => 'Map', 'summary' => 'Break the subject into chunks and mark what already feels hard.'],
        ['title' => 'Schedule', 'summary' => 'Place practice on days you actually have.'],
        ['title' => 'Practice kit', 'summary' => 'Build self-tests and a calm final-week plan.'],
    ],
    'fields' => [
        [
            'field_key'    => 'subject_name',
            'label'        => 'What are you studying?',
            'type'         => 'text',
            'help'         => 'The class, skill, or book section. Keep it specific enough that someone else would know what you mean.',
            'placeholder'  => 'e.g. Introductory Statistics midterm',
            'sample_value' => 'Introductory Statistics midterm (Units 2 and 3)',
        ],
        [
            'field_key'    => 'success_goal',
            'label'        => 'What does success look like?',
            'type'         => 'textarea',
            'help'         => 'A grade target, a skill you need to demonstrate, or a certification step. One or two honest sentences.',
            'placeholder'  => 'e.g. Score at least 75% and solve CI problems without the textbook',
            'sample_value' => 'Score at least 75% on the midterm and solve confidence-interval problems with the formula sheet only.',
        ],
        [
            'field_key'    => 'deadline',
            'label'        => 'Deadline',
            'type'         => 'text',
            'help'         => 'Exam date, project due date, or the day you want to be ready. Use a real calendar date.',
            'placeholder'  => 'e.g. March 20, 2026',
            'sample_value' => 'March 20, 2026',
        ],
        [
            'field_key'    => 'hours_per_week',
            'label'        => 'Focused hours per week',
            'type'         => 'number',
            'help'         => 'Hours you can actually protect for study. Be realistic. Quiet evening time only.',
            'placeholder'  => '6',
            'sample_value' => '8',
        ],
        [
            'field_key'    => 'starting_point',
            'label'        => 'Where are you starting?',
            'type'         => 'select',
            'help'         => 'Pick the closest match. The plan will not invent a higher starting level.',
            'options'      => [
                'Brand new to this subject',
                'I attended class but feel shaky',
                'I understand most of it, need practice',
                'Almost ready, need a focused review',
            ],
            'sample_value' => 'I attended class but feel shaky',
        ],
        [
            'field_key'    => 'weak_spots',
            'label'        => 'What already feels hard?',
            'type'         => 'textarea',
            'help'         => 'Topics, problem types, or habits that trip you. Leave blank only if you truly do not know yet.',
            'placeholder'  => 'e.g. Word problems that mix symbols and stories',
            'sample_value' => "Probability word problems\nConfidence intervals when the wording is murky\nRemembering which formula to reach for under time pressure",
        ],
        [
            'field_key'    => 'materials',
            'label'        => 'What materials do you have?',
            'type'         => 'textarea',
            'help'         => 'Textbook chapters, lecture notes, practice exams, videos, tutors. One item per line. Do not list things you do not have.',
            'placeholder'  => "Textbook chapters 4-6\nLecture slides\nLast year's practice quiz",
            'sample_value' => "Textbook chapters 4-6\nLecture slides from weeks 4-7\nInstructor practice quiz PDF\nFormula sheet handout",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'define-success-criteria',
            'title'            => 'Define what finished looks like',
            'summary'          => 'Turn your goal into a clear finish line, practical proofs of readiness, and honest non-goals.',
            'why_it_matters'   => 'Without a finish line, study hours turn into endless rereading. This step locks what "ready" means so later steps schedule practice toward it. Common mistakes: vague goals like "do well," inventing grade histories you did not share, or adding topics you never listed. Need help if the AI widens the goal? Paste again and tell it to use only your stated success target and deadline. You are ready when a stranger could tell what you are aiming for and what you are not.',
            'unlocks_text'     => 'Approving unlocks the topic map, which breaks the subject into study chunks.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are a calm study coach helping a learner write success criteria. Use ONLY the facts below. Do not invent prior grades, hours already studied, course policies, or extra goals.

Subject: {{subject_name}}
Success goal: {{success_goal}}
Deadline: {{deadline}}
Focused hours available per week: {{hours_per_week}}
Starting point: {{starting_point}}
Materials available:
{{materials}}

Produce Markdown with these exact headings as plain ATX headings on their own lines. Do not bold headings. Do not wrap the response in a code fence:

## Finish line
One or two sentences restating the success goal in plain language with the deadline visible.

## What success looks like in practice
Three concrete proofs of readiness the learner could demonstrate (explain, solve, or teach something). Tie them to the subject. Do not invent assessments that were not described.

## Non-goals
Two or three things this plan will deliberately ignore so focus stays tight.

## Constraints I must respect
Restate the weekly hours and any clear limits implied by the starting point and materials. Invent nothing beyond the facts above.

Keep all four headings in that order. Under 280 words. Plain language. No hype.
TXT,
            'example_response' => $successExample,
            'output_sections' => [
                ['key' => 'finish_line', 'heading' => 'Finish line', 'required' => true],
                ['key' => 'success_in_practice', 'heading' => 'What success looks like in practice', 'required' => true],
                ['key' => 'non_goals', 'heading' => 'Non-goals', 'required' => true],
                ['key' => 'constraints', 'heading' => 'Constraints I must respect', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Finish line matches your goal', 'help' => 'The target and deadline match what you entered. Nothing new was invented.',
                 'evidence_sections' => ['finish_line']],
                ['label' => 'Proofs are specific', 'help' => 'You could demonstrate each "success in practice" item without guessing what it means.',
                 'evidence_sections' => ['success_in_practice']],
                ['label' => 'Non-goals protect focus', 'help' => 'At least one non-goal honestly cuts scope you would otherwise overreach.',
                 'evidence_sections' => ['non_goals']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'topic-map',
            'title'            => 'Map the subject into study chunks',
            'summary'          => 'Break the work into topics, rank them, and mark weak spots from your own experience.',
            'why_it_matters'   => 'A schedule without a topic map just fills hours. This step decides what deserves practice time. Common mistakes: inventing topics not in your materials, treating everything as highest priority, or ignoring weak spots you already named. Need help if the list feels endless? Paste again and ask for at most eight topics using only your materials and weak spots. You are ready when the map is short enough to schedule and your hard topics are clearly marked.',
            'unlocks_text'     => 'Approving unlocks your weekly rhythm, which places these topics on real days.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are helping a learner map study topics. Use ONLY the facts and the approved success criteria. Do not invent chapters, tools, or weak spots they did not name.

Subject: {{subject_name}}
Starting point: {{starting_point}}
Weak spots they already feel:
{{weak_spots}}
Materials available:
{{materials}}

Approved success criteria (ground truth):
{{artifact:define-success-criteria}}

Produce Markdown with these exact headings:

## Topic map
A numbered list of 5 to 8 study chunks. For each: short name, study mode (review / practice-heavy / light skim), and weight (high / medium / low). Stay inside the materials listed. Do not add topics that are not implied by the materials or success criteria.

## Priority order
The same topics ranked for practice time, highest impact first. Bias toward stated weak spots and high-weight items that prove the finish line.

## Weak spot callouts
Two or three sentences naming which topics need more active practice and why, using only the learner's stated weak spots.

## Out of scope for now
Topics or chapters intentionally parked until after the deadline or finish line.

Keep these four headings in order. Under 320 words. Plain language. Invent nothing.
TXT,
            'example_response' => $topicMapExample,
            'output_sections' => [
                ['key' => 'topic_map', 'heading' => 'Topic map', 'required' => true],
                ['key' => 'priority_order', 'heading' => 'Priority order', 'required' => true],
                ['key' => 'weak_spot_callouts', 'heading' => 'Weak spot callouts', 'required' => true],
                ['key' => 'out_of_scope', 'heading' => 'Out of scope for now', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Topics fit your materials', 'help' => 'Nothing appears that you did not have notes, chapters, or tools for.',
                 'evidence_sections' => ['topic_map']],
                ['label' => 'Weak spots are visible', 'help' => 'Your hard areas show up in priority or callouts, not buried.',
                 'evidence_sections' => ['priority_order', 'weak_spot_callouts']],
                ['label' => 'Scope is cut on purpose', 'help' => 'Out-of-scope items keep the plan smaller than "the whole subject."',
                 'evidence_sections' => ['out_of_scope']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'weekly-rhythm',
            'title'            => 'Place practice on real days',
            'summary'          => 'Turn hours-per-week into a simple weekly rhythm with a session template and guardrails.',
            'why_it_matters'   => 'Ambition dies on unscheduled weeks. This step fits practice into the hours you said you have. Common mistakes: packing more hours than you allowed, scheduling only passive rereading, or stacking make-up days until burnout. Need help if the AI overbooks you? Paste again and insist total weekly minutes stay within your entered hours. You are ready when the week is honest and every session does active practice.',
            'unlocks_text'     => 'Approving unlocks the retrieval kit used inside these sessions.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are scheduling study sessions for a busy learner. Use ONLY their hours and the approved topic map. Do not invent free mornings they did not describe.

Subject: {{subject_name}}
Deadline: {{deadline}}
Focused hours per week: {{hours_per_week}}

Approved success criteria:
{{artifact:define-success-criteria}}

Approved topic map:
{{artifact:topic-map}}

Produce Markdown with these exact headings:

## Weekly rhythm
A day-by-day list for one repeating week. Each day: length in minutes and the single priority topic or task. Total time must not exceed the weekly hours above. Prefer shorter sessions most weekdays plus one longer practice block. Include one lighter day.

## Session template
Four short steps the learner repeats every session (start, main practice, capture misses, stop). Emphasize active practice before rereading.

## Guardrails
Rules for missed days, overwork, and stopping on time. Stay realistic for the stated hours.

Keep these three headings in order. Under 300 words. Do not invent calendar conflicts or class times not provided.
TXT,
            'example_response' => $weeklyRhythmExample,
            'output_sections' => [
                ['key' => 'weekly_rhythm', 'heading' => 'Weekly rhythm', 'required' => true],
                ['key' => 'session_template', 'heading' => 'Session template', 'required' => true],
                ['key' => 'guardrails', 'heading' => 'Guardrails', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Hours match your budget', 'help' => 'Adding the session lengths stays within the hours you entered.',
                 'evidence_sections' => ['weekly_rhythm']],
                ['label' => 'Sessions do active practice', 'help' => 'The template starts with practice or recall, not long rereading.',
                 'evidence_sections' => ['session_template']],
                ['label' => 'Missed-day rules are kind and firm', 'help' => 'Guardrails prevent stacking endless make-up sessions.',
                 'evidence_sections' => ['guardrails']],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'retrieval-practice-kit',
            'title'            => 'Build your self-test kit',
            'summary'          => 'Create cue questions, blank-page challenges, and short answer keys so you can practice without only rereading.',
            'why_it_matters'   => 'Rereading feels safe and teaches little. Self-tests show what you can actually recall. Common mistakes: questions that only ask for definitions you can copy, keys that invent facts beyond your materials, or no instructions for how to use the kit. Need help if answers feel made up? Paste again and require every key to stay inside your materials and prior approved docs. You are ready when you could run a 20-minute self-test tonight with this kit alone.',
            'unlocks_text'     => 'Approving unlocks the final-week plan that uses this kit before the deadline.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are building a retrieval-practice kit. Use ONLY the subject facts and approved artifacts. Do not invent textbook page numbers, quiz banks, or answer keys with facts not supported by the materials list.

Subject: {{subject_name}}
Materials available:
{{materials}}
Weak spots:
{{weak_spots}}

Approved success criteria:
{{artifact:define-success-criteria}}

Approved topic map:
{{artifact:topic-map}}

Produce Markdown with these exact headings:

## Cue questions
Five short questions the learner can answer aloud with notes covered. Bias toward weak spots and high-priority topics.

## Blank-page challenges
Three prompts that ask the learner to write or explain from memory (no multiple choice).

## Self-check keys
Brief expected answers for the cue questions and enough guidance to judge the blank-page challenges. Stay inside the materials and subject. Mark uncertainty rather than inventing.

## How to use this kit
Four to six sentences: cover, attempt, mark misses, restudy misses only, retry later.

Keep these four headings in order. Under 380 words. Plain language.
TXT,
            'example_response' => $retrievalKitExample,
            'output_sections' => [
                ['key' => 'cue_questions', 'heading' => 'Cue questions', 'required' => true],
                ['key' => 'blank_page_challenges', 'heading' => 'Blank-page challenges', 'required' => true],
                ['key' => 'self_check_keys', 'heading' => 'Self-check keys', 'required' => true],
                ['key' => 'how_to_use', 'heading' => 'How to use this kit', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Questions need real recall', 'help' => 'You cannot answer them by skimming a heading. They force memory.',
                 'evidence_sections' => ['cue_questions', 'blank_page_challenges']],
                ['label' => 'Keys stay honest', 'help' => 'Nothing in the keys invents formulas or facts beyond your materials.',
                 'evidence_sections' => ['self_check_keys']],
                ['label' => 'Usage steps are clear', 'help' => 'You know how to run a short session with this kit tonight.',
                 'evidence_sections' => ['how_to_use']],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'final-week-plan',
            'title'            => 'Plan the final week',
            'summary'          => 'Map the last five days before the deadline and pack a calm day-before checklist.',
            'why_it_matters'   => 'The week before a deadline is when panic overwrites the plan. This step keeps practice narrow and protects sleep. Common mistakes: inventing a brand-new topic on day one, skipping the checklist, or advising all-nighters. Need help if the AI packs heroic hours? Paste again and keep each day within a normal share of your weekly hours. You are ready when the plan feels calmer than cramming and the checklist fits on one small list.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished-file export.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing a calm final-week plan. Use ONLY the deadline, hours, and approved artifacts. Do not invent exam logistics, room numbers, or school rules not provided.

Subject: {{subject_name}}
Deadline: {{deadline}}
Focused hours per week: {{hours_per_week}}

Approved success criteria:
{{artifact:define-success-criteria}}

Approved topic map:
{{artifact:topic-map}}

Approved weekly rhythm:
{{artifact:weekly-rhythm}}

Approved retrieval kit:
{{artifact:retrieval-practice-kit}}

Produce Markdown with these exact headings:

## Day-by-day plan
Five days labeled Day 5 through Day 1 before the deadline. Each day: focus topic or task and approximate length. Lean on the retrieval kit. Reduce new learning as the deadline nears. Protect sleep on Day 1.

## Day-before checklist
Five concrete packing or prep items relevant to this subject. Do not invent school-specific rules. Stick to portable prep (materials, rest, timing you already know).

## If I fall behind
What to drop first and what must remain. Stay kind and firm. No all-nighters.

Keep these three headings in order. Under 320 words. Invent nothing beyond the facts and approved artifacts.
TXT,
            'example_response' => $finalWeekExample,
            'output_sections' => [
                ['key' => 'day_by_day', 'heading' => 'Day-by-day plan', 'required' => true],
                ['key' => 'day_before_checklist', 'heading' => 'Day-before checklist', 'required' => true],
                ['key' => 'if_behind', 'heading' => 'If I fall behind', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Days get calmer near the deadline', 'help' => 'Day 1 is light review and logistics, not a new chapter.',
                 'evidence_sections' => ['day_by_day']],
                ['label' => 'Checklist is doable', 'help' => 'Each item is something you can pack or confirm without new research.',
                 'evidence_sections' => ['day_before_checklist']],
                ['label' => 'Behind plan cuts scope, not sleep', 'help' => 'Fall-behind advice drops topics before it suggests staying up all night.',
                 'evidence_sections' => ['if_behind']],
            ],
        ],
    ],
];
