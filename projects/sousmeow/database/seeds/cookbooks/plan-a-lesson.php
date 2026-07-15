<?php

declare(strict_types=1);

/**
 * Plan a Lesson — executable Cookbook for learning-teaching.
 *
 * Inspired by public lesson-plan framework teaching (objective -> activity ->
 * check). Ideas only. All copy, examples, and prompts are original SousMeow
 * work. No source wording is copied.
 */

$objectiveExample = <<<'MD'
## Lesson objective
By the end of this 45-minute lesson, adult community garden learners will be able to sow basil seeds in a container at the correct shallow depth and explain how they will keep the soil moist while seeds germinate.

## Learners
The learners are adults in a community garden class. They may have mixed gardening experience, so the lesson should use plain words and visible demonstration.

## Success evidence
- Learners place basil seeds shallowly in a prepared container.
- Learners lightly cover and water the seeds.
- Learners can say one care step for the first week.

## Scope boundary
This lesson does not need to cover transplanting, pruning, pest management, or every herb. It focuses on sowing basil today.
MD;

$prerequisitesExample = <<<'MD'
## Need to know
- What a seed is and that it needs moisture to start growing.
- How to handle a small container gently.
- Where the basil container will sit after class.

## Materials ready
- Basil seeds.
- Containers with drainage.
- Seed-starting mix or potting mix.
- Watering can or spray bottle.
- Labels or tape and marker.

## Possible gaps
If containers are not already filled, add setup time or make filling the first activity. If water is not nearby, assign a refill plan before class starts.

## Setup notes
Put one demo container where everyone can see it. Pre-open seed packets if hands will be busy or wet.
MD;

$activitiesExample = <<<'MD'
## Lesson flow
1. 0-5 min: Welcome, show basil seeds, and name today's objective.
2. 5-10 min: Demonstrate container, soil surface, shallow sowing, and gentle watering.
3. 10-25 min: Learners sow their own basil seeds while the teacher circulates.
4. 25-35 min: Pair check: learners explain depth, water, and first-week care to a partner.
5. 35-42 min: Whole-group troubleshooting for common questions.
6. 42-45 min: Exit check and label containers.

## Activity instructions
Show first, then have learners repeat the same steps: fill or smooth soil, place seeds on the surface, cover lightly, water gently, label, and choose a care spot.

## Timing guardrails
Keep the demo under five minutes. If setup runs long, shorten troubleshooting before cutting the hands-on sowing time.

## Teacher notes
Use plain language such as "light cover" instead of measurements learners cannot see. Watch for seeds buried too deeply or washed to one side.
MD;

$checkExample = <<<'MD'
## Learning check
Each learner shows their labeled container and answers: "How deep did you cover the basil seeds, and what will you do to keep them moist this week?"

## What to look for
- Seeds are covered only lightly.
- Soil is damp, not flooded.
- Container has a label.
- Learner can name one first-week care action.

## If learners miss it
If seeds are buried too deeply, gently uncover and re-cover one example together. If watering is too heavy, demonstrate a lighter pour or spray.

## Keep it fair
Check the skill taught today, not prior gardening vocabulary. Let learners answer in everyday language.
MD;

$lessonCardExample = <<<'MD'
## Lesson card
Intro to sowing basil: a 45-minute community garden lesson where adult learners sow basil seeds in a container and explain first-week care.

## Materials and setup
Basil seeds, containers with drainage, soil mix, water, labels, and marker. Put a demo container where all learners can see it and keep water within reach.

## Flow summary
Open with the objective, demonstrate shallow sowing, give learners hands-on sowing time, run a pair explanation, troubleshoot common issues, and close with the exit check.

## Check and close
Learners show a labeled container and explain seed depth plus one moisture-care step. Close by reminding them where the container should sit and when to check moisture next.
MD;

return [
    'slug'                => 'plan-a-lesson',
    'title'               => 'Plan a Lesson',
    'tagline'             => 'Build one lesson with a clear objective, activities, and a way to check learning.',
    'description'         => "A lesson works best when the objective, activities, and check all point at the same skill. This Cookbook helps you write a learning objective, name prerequisites, sequence activities, design a fair check, and pack a lesson card. Enter only facts you know about the learners, time, materials, and topic. Every prompt is told not to invent supplies, learner background, or standards you did not provide.",
    'primary_category'    => 'learning-teaching',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Teachers, facilitators, trainers, and community instructors planning one focused lesson',
    'outcome'             => 'learning objective, prerequisite list, activity sequence, learning check, and a compact lesson card',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'pine',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 45,
    'demo_completed_runs' => 201,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 21,
    'stages' => [
        ['title' => 'Aim', 'summary' => 'Name the objective and what learners need before the lesson begins.'],
        ['title' => 'Flow', 'summary' => 'Sequence activities inside the time you actually have.'],
        ['title' => 'Check', 'summary' => 'Design a fair check and pack a lesson card.'],
    ],
    'fields' => [
        [
            'field_key'    => 'lesson_title',
            'label'        => 'Lesson title',
            'type'         => 'text',
            'help'         => 'Name the one lesson you are planning.',
            'placeholder'  => 'e.g. Intro to sowing basil',
            'sample_value' => 'Intro to sowing basil',
        ],
        [
            'field_key'    => 'learners',
            'label'        => 'Learners',
            'type'         => 'textarea',
            'help'         => 'Who is in the room and what you know about them. Do not invent prior knowledge.',
            'placeholder'  => 'e.g. Adult beginners in a community garden class',
            'sample_value' => 'Adults in a community garden class with mixed gardening experience.',
        ],
        [
            'field_key'    => 'objective',
            'label'        => 'Objective',
            'type'         => 'textarea',
            'help'         => 'What learners should be able to do by the end. Use a real action.',
            'placeholder'  => 'e.g. Sow basil seeds in a container and explain first-week care',
            'sample_value' => 'Learners can sow basil seeds in a container at a shallow depth and explain how to keep the soil moist while seeds germinate.',
        ],
        [
            'field_key'    => 'time_minutes',
            'label'        => 'Time available in minutes',
            'type'         => 'number',
            'help'         => 'Total lesson time, not prep time.',
            'placeholder'  => '45',
            'sample_value' => '45',
        ],
        [
            'field_key'    => 'materials_ready',
            'label'        => 'Materials ready',
            'type'         => 'textarea',
            'help'         => 'List only materials you actually have or can confirm.',
            'placeholder'  => "Seeds\nContainers\nSoil\nWater",
            'sample_value' => "Basil seeds\nContainers with drainage\nSeed-starting mix or potting mix\nWatering can or spray bottle\nLabels or tape and marker",
        ],
        [
            'field_key'    => 'activities_ideas',
            'label'        => 'Activity ideas',
            'type'         => 'textarea',
            'help'         => 'Demonstrations, practice, discussion, examples, or checks you are considering.',
            'placeholder'  => "Demo sowing\nLearners plant their own\nPair explanation",
            'sample_value' => "Short demonstration of shallow sowing\nLearners sow basil in their own containers\nPair explanation of care steps\nExit question before leaving",
        ],
        [
            'field_key'    => 'how_you_will_check',
            'label'        => 'How you will check learning',
            'type'         => 'textarea',
            'help'         => 'What learners will show, say, write, solve, or make so you know they learned it.',
            'placeholder'  => 'e.g. Learners show container and explain watering plan',
            'sample_value' => 'Learners show their labeled basil container and explain how deep they covered the seeds and how they will keep soil moist this week.',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'write-learning-objective',
            'title'            => 'Write the learning objective',
            'summary'          => 'Turn the topic into one teachable objective with visible success evidence.',
            'why_it_matters'   => 'A lesson objective keeps every activity from drifting. This step names what learners will do, who they are, and how success will show. Common mistakes: writing an objective that says "understand," adding standards not provided, or making the lesson too broad. Need help if it is too vague? Paste again and require one observable verb. You are ready when the objective can be checked in class.',
            'unlocks_text'     => 'Approving unlocks prerequisites and materials.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are helping plan one lesson. Use ONLY the pantry fields. Do not invent standards, learner background, materials, or outcomes.

Lesson title: {{lesson_title}}
Learners:
{{learners}}
Objective:
{{objective}}
Time: {{time_minutes}} minutes

Produce Markdown with these exact headings:

## Lesson objective
One sentence beginning "By the end..." with an observable action and the time context.

## Learners
Two sentences summarizing who they are and what is known. Mark unknowns by not claiming them.

## Success evidence
Three to five bullets showing what learners will do, say, make, or show.

## Scope boundary
What this one lesson will not cover.

Keep headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $objectiveExample,
            'output_sections' => [
                ['key' => 'lesson_objective', 'heading' => 'Lesson objective', 'required' => true],
                ['key' => 'learners', 'heading' => 'Learners', 'required' => true],
                ['key' => 'success_evidence', 'heading' => 'Success evidence', 'required' => true],
                ['key' => 'scope_boundary', 'heading' => 'Scope boundary', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Objective is observable', 'help' => 'Learners have to do, say, make, or show something visible.',
                 'evidence_sections' => ['lesson_objective', 'success_evidence']],
                ['label' => 'Learner facts are honest', 'help' => 'The learner section does not invent prior knowledge or needs.',
                 'evidence_sections' => ['learners']],
                ['label' => 'Scope is contained', 'help' => 'The boundary keeps the lesson from trying to teach too much.',
                 'evidence_sections' => ['scope_boundary']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'list-prerequisites',
            'title'            => 'List prerequisites and materials',
            'summary'          => 'Name what learners need to know and what materials must be ready.',
            'why_it_matters'   => 'Missing prerequisites and supplies can break a lesson even when the objective is clear. This step keeps setup practical and honest. Common mistakes: assuming learners know vocabulary, inventing materials, or hiding setup time. Need help if materials are uncertain? Paste again and ask it to separate ready items from possible gaps. You are ready when setup can be checked before class.',
            'unlocks_text'     => 'Approving unlocks the activity sequence.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are listing prerequisites and materials for one lesson. Use ONLY pantry fields and the approved objective. Do not invent supplies, room features, technology, or learner knowledge.

Materials ready:
{{materials_ready}}
Learners:
{{learners}}
Activities being considered:
{{activities_ideas}}

Approved objective:
{{artifact:write-learning-objective}}

Produce Markdown with these exact headings:

## Need to know
Three to five prerequisite ideas or skills, phrased plainly. If unknown, keep them minimal.

## Materials ready
Bullets using only listed materials.

## Possible gaps
Things to confirm or adjust before class. Do not solve gaps with invented supplies.

## Setup notes
Short setup advice using only available materials and known time.

Keep headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $prerequisitesExample,
            'output_sections' => [
                ['key' => 'need_to_know', 'heading' => 'Need to know', 'required' => true],
                ['key' => 'materials_ready', 'heading' => 'Materials ready', 'required' => true],
                ['key' => 'possible_gaps', 'heading' => 'Possible gaps', 'required' => true],
                ['key' => 'setup_notes', 'heading' => 'Setup notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Prerequisites are minimal', 'help' => 'The list does not assume advanced prior knowledge unless supplied.',
                 'evidence_sections' => ['need_to_know']],
                ['label' => 'Materials are from pantry', 'help' => 'No extra supplies appear as if they are ready.',
                 'evidence_sections' => ['materials_ready']],
                ['label' => 'Gaps are flagged', 'help' => 'Uncertain setup items are named instead of invented.',
                 'evidence_sections' => ['possible_gaps']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'sequence-activities',
            'title'            => 'Sequence the activities',
            'summary'          => 'Build a timed flow that moves from demonstration to practice to reflection.',
            'why_it_matters'   => 'A lesson flow should spend time where learning happens, not only where explaining happens. This step turns ideas into a timed sequence. Common mistakes: overpacking the time, skipping practice, or adding materials not ready. Need help if the flow exceeds time? Paste again and require total minutes to match the pantry time. You are ready when every activity supports the objective.',
            'unlocks_text'     => 'Approving unlocks the learning check.',
            'est_minutes'      => 12,
            'prompt_template'  => <<<'TXT'
You are sequencing one lesson. Use ONLY pantry fields and approved artifacts. Do not invent materials, room setup, standards, or learner background.

Time available: {{time_minutes}} minutes
Activity ideas:
{{activities_ideas}}
Materials ready:
{{materials_ready}}

Approved objective:
{{artifact:write-learning-objective}}

Approved prerequisites:
{{artifact:list-prerequisites}}

Produce Markdown with these exact headings:

## Lesson flow
A timed sequence whose minutes add up to {{time_minutes}}. Include opening, teacher input, learner practice, and closing.

## Activity instructions
Plain instructions for the main learner activity.

## Timing guardrails
What to shorten if time runs tight, while protecting the main practice.

## Teacher notes
Short facilitation notes tied to the objective and materials.

Keep headings in order. Under 360 words. Invent nothing.
TXT,
            'example_response' => $activitiesExample,
            'output_sections' => [
                ['key' => 'lesson_flow', 'heading' => 'Lesson flow', 'required' => true],
                ['key' => 'activity_instructions', 'heading' => 'Activity instructions', 'required' => true],
                ['key' => 'timing_guardrails', 'heading' => 'Timing guardrails', 'required' => true],
                ['key' => 'teacher_notes', 'heading' => 'Teacher notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Flow fits the time', 'help' => 'The sequence adds up to the minutes you entered.',
                 'evidence_sections' => ['lesson_flow']],
                ['label' => 'Learners practice', 'help' => 'The flow gives learners time to do the objective, not only listen.',
                 'evidence_sections' => ['lesson_flow', 'activity_instructions']],
                ['label' => 'Materials stay honest', 'help' => 'Activities use materials that were listed or flag gaps.',
                 'evidence_sections' => ['activity_instructions', 'teacher_notes']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'design-the-check',
            'title'            => 'Design the check',
            'summary'          => 'Create a fair way to see whether learners met the objective.',
            'why_it_matters'   => 'A lesson check should measure the thing taught, not trivia or confidence. This step makes success visible and gives a recovery path if learners miss it. Common mistakes: checking recall when the objective is performance, grading prior knowledge, or making the check too big for the time. Need help if the check feels unfair? Paste again and ask it to test only the approved objective. You are ready when the check can happen inside the lesson.',
            'unlocks_text'     => 'Approving unlocks the final lesson card.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are designing a learning check. Use ONLY pantry fields and approved artifacts. Do not invent standards, grades, rubrics, supplies, or learner traits.

How you will check:
{{how_you_will_check}}

Approved objective:
{{artifact:write-learning-objective}}

Approved lesson flow:
{{artifact:sequence-activities}}

Produce Markdown with these exact headings:

## Learning check
One check learners complete before the lesson ends.

## What to look for
Three to five observable signs that meet the objective.

## If learners miss it
One or two quick reteach or correction moves that fit the lesson.

## Keep it fair
How the check avoids testing unstated prior knowledge or extra materials.

Keep headings in order. Under 280 words. Invent nothing.
TXT,
            'example_response' => $checkExample,
            'output_sections' => [
                ['key' => 'learning_check', 'heading' => 'Learning check', 'required' => true],
                ['key' => 'what_to_look_for', 'heading' => 'What to look for', 'required' => true],
                ['key' => 'if_learners_miss_it', 'heading' => 'If learners miss it', 'required' => true],
                ['key' => 'keep_it_fair', 'heading' => 'Keep it fair', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Check matches objective', 'help' => 'The check measures the skill named in the objective.',
                 'evidence_sections' => ['learning_check', 'what_to_look_for']],
                ['label' => 'Reteach move is practical', 'help' => 'If learners miss it, the response can happen during the lesson.',
                 'evidence_sections' => ['if_learners_miss_it']],
                ['label' => 'Fairness is explicit', 'help' => 'The check does not depend on unprovided background or materials.',
                 'evidence_sections' => ['keep_it_fair']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-lesson-card',
            'title'            => 'Pack the lesson card',
            'summary'          => 'Assemble the objective, materials, flow, and check into one compact teaching card.',
            'why_it_matters'   => 'A lesson card keeps the facilitator focused when class starts. This final step compresses the plan without adding new pieces. Common mistakes: rewriting the objective, adding new supplies, or losing the check. Need help if it is too long? Paste again and ask for a one-page card. You are ready when you could teach from the card without opening every prior artifact.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished lesson plan export.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are packing a compact lesson card. Use ONLY pantry fields and approved artifacts. Do not invent materials, standards, learner background, timings, or checks.

Lesson title: {{lesson_title}}
Time: {{time_minutes}} minutes
Learners:
{{learners}}

Approved objective:
{{artifact:write-learning-objective}}

Approved prerequisites:
{{artifact:list-prerequisites}}

Approved flow:
{{artifact:sequence-activities}}

Approved check:
{{artifact:design-the-check}}

Produce Markdown with these exact headings:

## Lesson card
One paragraph naming the lesson, learners, time, and objective.

## Materials and setup
Bullets or a short paragraph using only approved materials and setup notes.

## Flow summary
Condensed teaching sequence from the approved flow.

## Check and close
The learning check and closing reminder.

Keep headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $lessonCardExample,
            'output_sections' => [
                ['key' => 'lesson_card', 'heading' => 'Lesson card', 'required' => true],
                ['key' => 'materials_and_setup', 'heading' => 'Materials and setup', 'required' => true],
                ['key' => 'flow_summary', 'heading' => 'Flow summary', 'required' => true],
                ['key' => 'check_and_close', 'heading' => 'Check and close', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Card preserves the objective', 'help' => 'The objective is still visible and unchanged in meaning.',
                 'evidence_sections' => ['lesson_card']],
                ['label' => 'No new supplies appear', 'help' => 'Materials and setup come from approved artifacts.',
                 'evidence_sections' => ['materials_and_setup']],
                ['label' => 'Check is included', 'help' => 'The final card keeps the learning check and close.',
                 'evidence_sections' => ['check_and_close']],
            ],
        ],
    ],
];
