<?php

declare(strict_types=1);

/**
 * Finish a Personal Project — executable Cookbook for personal-projects.
 *
 * Inspired by public SMART-goal academic advising and simple work-breakdown /
 * project planning teaching. Ideas only; original copy.
 */

$defineDoneExample = <<<'MD'
## Finished means
Desk Garden is finished when the window herb garden is set up, at least three herbs are growing in labeled pots, and I have cooked with the herbs once a week for four weeks.

## Visible proof
- Photo of the planted window setup with labels.
- Four short cooking notes naming what I used from the herbs.
- A final note saying what I would keep, change, or stop.

## Not part of this finish line
I am not redesigning the whole kitchen, building shelves, or becoming an expert gardener. The project is a small window herb garden that gets used in real meals.

## Done-definition test
A friend could look at the photos and four cooking notes and tell whether this version of Desk Garden is complete.
MD;

$whyNowExample = <<<'MD'
## Why this matters now
Desk Garden matters now because I keep saying I want fresh herbs at home, but the idea stays vague. A small finished version would make cooking feel easier and give me a low-risk project I can actually close.

## Personal payoff
- I will have fresh herbs within reach.
- I will practice finishing a small project instead of endlessly improving the plan.
- I will learn whether a window garden fits my real kitchen habits.

## Cost of waiting
If I wait, the project will keep living as a nice idea and I will keep buying herbs only when I remember them.

## Keep-going sentence
I am finishing Desk Garden now because a small, used garden is better than a perfect one I never start.
MD;

$constraintsExample = <<<'MD'
## Time budget
I have 3 focused hours per week for Desk Garden. Planning should stay small enough that most of the time goes to buying, planting, cooking, and checking the herbs.

## Materials and readiness
Ready now: sunny kitchen window, three small pots, saucers, potting mix, basil seeds, parsley seeds, scissors, notebook. Still needed: one mint starter plant and plant labels.

## Blockers to respect
- Forgetting to water the herbs.
- Buying extra supplies that turn the small project into kitchen redesign.
- Not choosing recipes, so the herbs grow without being used.

## Scope rules
Keep the first version to three herbs, the existing window, and four weeks of use. Do not add shelves, grow lights, or a full apartment garden unless this version finishes first.
MD;

$chunksExample = <<<'MD'
## Project chunks
1. Confirm the finish line and keep the scope to three herbs.
2. Gather missing supplies: mint starter and labels.
3. Set up the window area and plant the pots.
4. Make a simple watering and checking routine.
5. Pick four easy meals that can use the herbs.
6. Cook with herbs once each week and write a short note.
7. Review the four-week result and decide what to keep.

## Chunk size check
Each chunk can be done in one sitting or repeated as a small weekly action. None requires redesigning the kitchen.

## Dependencies
Supplies must come before planting. Planting and recipe choices must happen before the four weekly cooking notes can be completed.

## Parked ideas
Shelves, grow lights, more herb varieties, and a full recipe blog stay parked until after this version is finished.
MD;

$weeksExample = <<<'MD'
## Week sequence
1. Week 1: Buy the mint starter and labels. Plant basil, parsley, and mint. Label the pots and choose four herb-friendly meals.
2. Week 2: Check watering routine. Cook one meal using the herbs and write note 1.
3. Week 3: Adjust watering if needed. Cook one herb meal and write note 2.
4. Week 4: Cook one herb meal and write note 3. Remove anything that is clearly not working.
5. Week 5: Cook the fourth herb meal, take final photos, and write the closing note.

## Weekly focus
The first week builds the setup. The middle weeks prove the garden is being used. The final week closes the project instead of expanding it.

## Time fit
The plan stays within 3 focused hours per week by keeping setup small and using one cooking note each week.

## If a week slips
Move the missed cooking note to the next week and keep the same finish line. Do not add new herbs to make up for slipping.
MD;

$risksExample = <<<'MD'
## Risk list
1. Forgetting to water: herbs dry out before the four weeks are done.
2. Scope creep: adding shelves, lights, or more varieties delays the finish.
3. No meal plan: herbs grow, but I do not cook with them.
4. Weak light: the window may not support all three herbs equally.

## Prevention moves
- Put watering check next to an existing kitchen habit.
- Keep a "later" list for shelves and extra plants.
- Choose four simple meals in Week 1.
- Take a quick weekly photo so problems are visible early.

## Recovery moves
If one herb struggles, keep caring for it but do not restart the whole project. If one cooking week slips, cook with the herbs twice the next week only if that fits the 3-hour budget.

## Watch points
Check leaf droop, dry soil, unused herbs, and any urge to buy supplies that were not in the small finish line.
MD;

$checkinTemplateExample = <<<'MD'
## Weekly check-in questions
1. What got done for Desk Garden this week?
2. Did I stay within the three-herb, existing-window scope?
3. Did I cook with the herbs or move that action to the next open week?
4. What is the next smallest move?

## Progress evidence
- Current photo of the pots.
- One cooking note when a herb meal happens.
- One sentence about watering, light, or plant condition.

## Decision rule
If the project is moving, keep the plan. If the same blocker appears twice, choose one small fix instead of redesigning the project.

## Reset line
This week counts if I make one real move that keeps Desk Garden closer to the four cooking notes.
MD;

$firstWeekExample = <<<'MD'
## First-week actions
1. Re-read the finish line and parked ideas.
2. Buy one mint starter and plant labels.
3. Clear only the existing window space.
4. Plant basil, parsley, and mint in the three pots.
5. Label each pot.
6. Pick four easy meals that can use the herbs.
7. Set the first weekly check-in time.

## Calendar placement
Use the 3 focused hours this week for supply pickup, planting, labeling, and meal picking. Do not schedule work beyond the hours provided.

## Materials to touch
Pots, saucers, potting mix, basil seeds, parsley seeds, mint starter, labels, scissors, notebook, and the chosen window.

## End-of-week proof
The week is complete when the herbs are planted and labeled, four meal ideas are listed, and the first check-in is ready.
MD;

$finishChecklistExample = <<<'MD'
## Finish checklist
- [ ] Three herb pots are planted and labeled.
- [ ] The garden stayed in the existing window setup.
- [ ] Four cooking notes name meals that used the herbs.
- [ ] Final photo is saved.
- [ ] Closing note says what to keep, change, or stop.
- [ ] Parked ideas are reviewed only after the four-week finish is complete.

## Definition-of-done match
This checklist matches the Desk Garden finish line: a small window herb garden, used in cooking once a week for four weeks, with visible proof.

## Closeout note
Desk Garden is complete because I planted the small setup, used the herbs in four meals, and wrote down what happened. The next decision is whether to maintain, expand, or stop.

## Next-version parking lot
- Try one new herb variety.
- Consider a shelf only if the existing window setup proves useful.
- Save favorite herb meals for a repeat version.
MD;

return [
    'slug'                => 'finish-a-personal-project',
    'title'               => 'Finish a Personal Project',
    'tagline'             => 'Give a personal project a finish line, a calendar, and a first week of real moves.',
    'description'         => "Personal projects slip when they stay vague, too big, or always almost ready. This Cookbook helps you name what done means, why now matters, what constraints are real, and which chunks belong in the first finished version. Enter only pantry facts about the project, deadline, hours, materials, and blockers. Every prompt tells the assistant to invent nothing about your schedule, budget, tools, or motivation beyond those facts. You leave with a first week of real moves and a finish checklist.",
    'primary_category'    => 'personal-projects',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'People with a meaningful personal project that keeps slipping',
    'outcome'             => 'done definition, why-now, constraints, chunk list, week sequence, risks, check-in template, first-week actions, finish checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'moss',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 70,
    'demo_completed_runs' => 312,
    'demo_avg_rating'     => 4.7,
    'sort_order'          => 12,
    'stages' => [
        ['title' => 'Aim', 'summary' => 'Define done, why, and constraints before planning the work.'],
        ['title' => 'Plan', 'summary' => 'Break the project into chunks, sequence the weeks, and name risks.'],
        ['title' => 'Finish', 'summary' => 'Set check-ins, choose first-week moves, and pack the closeout checklist.'],
    ],
    'fields' => [
        [
            'field_key'    => 'project_name',
            'label'        => 'Project name',
            'type'         => 'text',
            'help'         => 'Give the personal project a short name. It can be plain or playful.',
            'placeholder'  => 'e.g. Desk Garden',
            'sample_value' => 'Desk Garden',
        ],
        [
            'field_key'    => 'done_definition',
            'label'        => 'What would count as done?',
            'type'         => 'textarea',
            'help'         => 'Describe the finished version someone could actually verify. Keep it smaller than your dream version.',
            'placeholder'  => 'e.g. Set up a window herb garden and cook with it for four weeks',
            'sample_value' => 'Set up a window herb garden with three labeled herbs and cook with it once a week for four weeks.',
        ],
        [
            'field_key'    => 'why_now',
            'label'        => 'Why now?',
            'type'         => 'textarea',
            'help'         => 'A real reason this matters now. Use facts or personal meaning, not pressure you do not believe.',
            'placeholder'  => 'e.g. I keep postponing it and want fresh herbs for simple meals',
            'sample_value' => 'I keep saying I want fresh herbs at home, but the project stays vague. I want a small finished project that improves simple meals.',
        ],
        [
            'field_key'    => 'hours_per_week',
            'label'        => 'Focused hours per week',
            'type'         => 'number',
            'help'         => 'Hours you can actually protect for this project. The plan will not invent extra time.',
            'placeholder'  => '3',
            'sample_value' => '3',
        ],
        [
            'field_key'    => 'deadline',
            'label'        => 'Deadline',
            'type'         => 'text',
            'help'         => 'The date you want this version finished. Use a real calendar date or a clear week.',
            'placeholder'  => 'e.g. May 31, 2026',
            'sample_value' => 'May 31, 2026',
        ],
        [
            'field_key'    => 'materials_ready',
            'label'        => 'Materials ready',
            'type'         => 'textarea',
            'help'         => 'Tools, supplies, notes, space, or people already available. Include missing items only if you clearly mark them.',
            'placeholder'  => "Pots\nPotting mix\nSunny window\nStill need labels",
            'sample_value' => "Sunny kitchen window\nThree small pots and saucers\nPotting mix\nBasil and parsley seeds\nScissors and notebook\nStill need: mint starter plant and plant labels",
        ],
        [
            'field_key'    => 'known_blockers',
            'label'        => 'Known blockers',
            'type'         => 'textarea',
            'help'         => 'What usually makes this project slip: time, money, supplies, decisions, confidence, space, or energy.',
            'placeholder'  => 'e.g. I overbuy supplies, forget maintenance, and avoid picking recipes',
            'sample_value' => "Forgetting to water\nBuying extra supplies instead of starting\nNot choosing meals that use the herbs\nTurning it into a bigger kitchen project",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'define-done',
            'title'            => 'Define done',
            'summary'          => 'Turn the project from a fuzzy wish into a finish line with visible proof.',
            'why_it_matters'   => 'A personal project keeps slipping when "done" means "better someday." This step makes completion observable before planning starts. Common mistakes: describing a dream version, adding tools not listed, or making success depend on someone else approving it. Need help if the AI expands the project? Paste again and require it to use only your stated done definition and materials. You are ready when a friend could tell whether the project is finished.',
            'unlocks_text'     => 'Approving unlocks the why-now statement, so the project has a reason to move now.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are helping someone define a finish line for a personal project. Use ONLY the pantry facts below. Do not invent their schedule, budget, tools, helpers, taste, or motivation.

Project name: {{project_name}}
Stated done definition: {{done_definition}}
Deadline: {{deadline}}
Focused hours per week: {{hours_per_week}}
Materials ready:
{{materials_ready}}
Known blockers:
{{known_blockers}}

Produce Markdown with these exact headings as plain ATX headings. Do not bold headings. Do not wrap the response in a code fence:

## Finished means
One or two sentences that make the done definition observable. Include the project name and deadline if useful.

## Visible proof
Three bullets showing evidence the person could collect without inventing tools or outside approval.

## Not part of this finish line
Two or three sentences cutting scope that would make this version too big. Use only the stated project and blockers.

## Done-definition test
One sentence explaining how a friend could verify completion.

Keep all four headings in order. Under 260 words. Plain language. Invent nothing.
TXT,
            'example_response' => $defineDoneExample,
            'output_sections' => [
                ['key' => 'finished_means', 'heading' => 'Finished means', 'required' => true],
                ['key' => 'visible_proof', 'heading' => 'Visible proof', 'required' => true],
                ['key' => 'not_part', 'heading' => 'Not part of this finish line', 'required' => true],
                ['key' => 'done_test', 'heading' => 'Done-definition test', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Done is observable', 'help' => 'The finish line can be verified from visible proof, not a mood.',
                 'evidence_sections' => ['finished_means', 'visible_proof']],
                ['label' => 'Scope is smaller than the dream', 'help' => 'The non-goals clearly cut tempting extras.',
                 'evidence_sections' => ['not_part']],
                ['label' => 'No invented requirements', 'help' => 'The test does not add tools, approvals, or success metrics you did not provide.',
                 'evidence_sections' => ['done_test']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-why-now',
            'title'            => 'Name why now',
            'summary'          => 'Write the honest reason this project deserves attention in this season.',
            'why_it_matters'   => 'Motivation fades when the reason is generic. This step ties the finish line to a real personal payoff and the cost of waiting. Common mistakes: inventing urgency, turning the project into self-criticism, or promising life changes the facts do not support. Need help if the AI gets dramatic? Paste again and ask for calm language using only your why-now field and approved done definition. You are ready when the reason feels true enough to reread during a stuck week.',
            'unlocks_text'     => 'Approving unlocks constraints, so the plan can respect time, materials, and blockers.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are helping someone write a grounded why-now statement. Use ONLY the pantry facts and approved done definition. Do not invent emotional backstory, external deadlines, money pressure, or other people's expectations.

Project name: {{project_name}}
Why now: {{why_now}}
Deadline: {{deadline}}
Known blockers:
{{known_blockers}}

Approved done definition:
{{artifact:define-done}}

Produce Markdown with these exact headings:

## Why this matters now
Two or three calm sentences connecting the stated reason to the approved finish line.

## Personal payoff
Three bullets naming practical or meaningful benefits that follow from the facts. No hype.

## Cost of waiting
One or two sentences about what likely continues if the project stays unfinished. Do not shame the person.

## Keep-going sentence
One sentence the person can reread when momentum drops.

Keep these four headings in order. Under 260 words. Invent nothing.
TXT,
            'example_response' => $whyNowExample,
            'output_sections' => [
                ['key' => 'why_matters_now', 'heading' => 'Why this matters now', 'required' => true],
                ['key' => 'personal_payoff', 'heading' => 'Personal payoff', 'required' => true],
                ['key' => 'cost_of_waiting', 'heading' => 'Cost of waiting', 'required' => true],
                ['key' => 'keep_going', 'heading' => 'Keep-going sentence', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Reason comes from your facts', 'help' => 'The why-now statement matches what you entered and approved.',
                 'evidence_sections' => ['why_matters_now']],
                ['label' => 'Payoff is believable', 'help' => 'Benefits are practical or meaningful without promising a transformed life.',
                 'evidence_sections' => ['personal_payoff']],
                ['label' => 'Tone stays kind', 'help' => 'The cost of waiting and keep-going line do not shame you.',
                 'evidence_sections' => ['cost_of_waiting', 'keep_going']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-constraints',
            'title'            => 'Name constraints',
            'summary'          => 'Make the real limits visible so the plan fits the life and supplies already described.',
            'why_it_matters'   => 'Constraints are not excuses; they are design rules. This step prevents a project plan from pretending there are extra hours, supplies, or space. Common mistakes: ignoring the hours-per-week number, treating missing materials as ready, or making every blocker a fatal flaw. Need help if the AI overbooks you? Paste again and require the weekly work to stay inside the entered hours and materials. You are ready when the plan has honest boundaries.',
            'unlocks_text'     => 'Approving unlocks chunking, where the project is broken into small pieces that fit these limits.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are helping someone state project constraints. Use ONLY the pantry facts and approved Aim artifacts. Do not invent free time, money, rooms, tools, helpers, or shopping trips.

Project name: {{project_name}}
Focused hours per week: {{hours_per_week}}
Deadline: {{deadline}}
Materials ready:
{{materials_ready}}
Known blockers:
{{known_blockers}}

Approved done definition:
{{artifact:define-done}}

Approved why-now:
{{artifact:name-why-now}}

Produce Markdown with these exact headings:

## Time budget
Restate the weekly hours as a planning limit. Do not place exact days or times unless provided.

## Materials and readiness
List what is ready and what is still missing, based only on the materials field.

## Blockers to respect
Three to five bullets naming blockers as planning constraints, not personal flaws.

## Scope rules
Three rules that keep this version finishable inside the done definition.

Keep these four headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $constraintsExample,
            'output_sections' => [
                ['key' => 'time_budget', 'heading' => 'Time budget', 'required' => true],
                ['key' => 'materials_readiness', 'heading' => 'Materials and readiness', 'required' => true],
                ['key' => 'blockers_respect', 'heading' => 'Blockers to respect', 'required' => true],
                ['key' => 'scope_rules', 'heading' => 'Scope rules', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Hours are treated as a limit', 'help' => 'The time budget does not invent extra availability.',
                 'evidence_sections' => ['time_budget']],
                ['label' => 'Materials are honest', 'help' => 'Ready and missing items match what you entered.',
                 'evidence_sections' => ['materials_readiness']],
                ['label' => 'Scope rules protect completion', 'help' => 'Rules stop obvious creep before planning begins.',
                 'evidence_sections' => ['scope_rules']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'break-into-chunks',
            'title'            => 'Break into chunks',
            'summary'          => 'Split the finish line into small pieces with dependencies and parked ideas.',
            'why_it_matters'   => 'A project stays stuck when every next step feels like the whole project. This step turns the finish line into manageable chunks. Common mistakes: making chunks too large, adding research detours, or hiding dependencies until late. Need help if the chunks feel vague? Paste again and ask for each chunk to be completable in one sitting or one weekly repeat. You are ready when every chunk has a clear verb.',
            'unlocks_text'     => 'Approving unlocks the week sequence, where these chunks get placed in order.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are breaking a personal project into work chunks. Use ONLY the pantry facts and approved Aim artifacts. Do not invent new features, supplies, helpers, or research tasks.

Project name: {{project_name}}
Deadline: {{deadline}}
Focused hours per week: {{hours_per_week}}
Materials ready:
{{materials_ready}}
Known blockers:
{{known_blockers}}

Approved done definition:
{{artifact:define-done}}

Approved constraints:
{{artifact:name-constraints}}

Produce Markdown with these exact headings:

## Project chunks
A numbered list of 5 to 8 chunks. Start each chunk with a verb. Keep chunks inside the approved finish line.

## Chunk size check
Two or three sentences checking that chunks are small enough for the stated weekly hours.

## Dependencies
Name which chunks must happen before others and which can repeat weekly.

## Parked ideas
Bullets for tempting extras that should wait until after this version is finished.

Keep these four headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $chunksExample,
            'output_sections' => [
                ['key' => 'project_chunks', 'heading' => 'Project chunks', 'required' => true],
                ['key' => 'chunk_size_check', 'heading' => 'Chunk size check', 'required' => true],
                ['key' => 'dependencies', 'heading' => 'Dependencies', 'required' => true],
                ['key' => 'parked_ideas', 'heading' => 'Parked ideas', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Chunks have verbs', 'help' => 'Each chunk says what action happens, not just a topic name.',
                 'evidence_sections' => ['project_chunks']],
                ['label' => 'Chunks fit your hours', 'help' => 'The size check respects the weekly time budget.',
                 'evidence_sections' => ['chunk_size_check']],
                ['label' => 'Dependencies are visible', 'help' => 'The plan shows what must happen before later work.',
                 'evidence_sections' => ['dependencies']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'sequence-the-weeks',
            'title'            => 'Sequence the weeks',
            'summary'          => 'Turn project chunks into a simple week-by-week path toward the deadline.',
            'why_it_matters'   => 'Chunks need order or they become another list to avoid. This step builds a realistic sequence that respects weekly hours and the deadline. Common mistakes: placing everything in week one, assuming exact free days not provided, or expanding the finish line mid-plan. Need help if the AI invents a calendar? Paste again and tell it to use week labels only unless you gave exact days. You are ready when the sequence shows what happens first, middle, and last.',
            'unlocks_text'     => 'Approving unlocks risk planning, so the sequence can survive predictable snags.',
            'est_minutes'      => 9,
            'prompt_template'  => <<<'TXT'
You are sequencing a personal project by week. Use ONLY the deadline, weekly hours, approved chunks, and approved constraints. Do not invent exact weekdays, time slots, vacations, shipping dates, or helpers.

Project name: {{project_name}}
Deadline: {{deadline}}
Focused hours per week: {{hours_per_week}}

Approved done definition:
{{artifact:define-done}}

Approved constraints:
{{artifact:name-constraints}}

Approved chunks:
{{artifact:break-into-chunks}}

Produce Markdown with these exact headings:

## Week sequence
A numbered week-by-week list from start to finish. Each week should have a main focus and stay within the stated weekly hours.

## Weekly focus
Two or three sentences explaining the arc from setup to proof to closeout.

## Time fit
One or two sentences showing how the plan respects the weekly hours.

## If a week slips
Kind, practical rules for moving work without expanding the project or inventing extra time.

Keep these four headings in order. Under 320 words. Invent nothing.
TXT,
            'example_response' => $weeksExample,
            'output_sections' => [
                ['key' => 'week_sequence', 'heading' => 'Week sequence', 'required' => true],
                ['key' => 'weekly_focus', 'heading' => 'Weekly focus', 'required' => true],
                ['key' => 'time_fit', 'heading' => 'Time fit', 'required' => true],
                ['key' => 'if_week_slips', 'heading' => 'If a week slips', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Sequence reaches the finish line', 'help' => 'The final week closes the approved done definition.',
                 'evidence_sections' => ['week_sequence']],
                ['label' => 'Hours stay realistic', 'help' => 'The time-fit note respects the hours you entered.',
                 'evidence_sections' => ['time_fit']],
                ['label' => 'Slip rule avoids heroic catch-up', 'help' => 'The fallback moves work without pretending extra time appears.',
                 'evidence_sections' => ['if_week_slips']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'list-risks',
            'title'            => 'List risks',
            'summary'          => 'Name the likely ways the project could stall, plus prevention and recovery moves.',
            'why_it_matters'   => 'Risks are easiest to handle before they happen. This step turns known blockers into concrete watch points and small recovery actions. Common mistakes: catastrophizing, ignoring the blockers already named, or solving every risk with more money or time. Need help if the AI gets dramatic? Paste again and ask for small prevention moves using only approved constraints. You are ready when each risk has one prevention move and one recovery move.',
            'unlocks_text'     => 'Approving unlocks the weekly check-in template used to keep the project moving.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are helping someone plan for project risks. Use ONLY pantry facts and approved artifacts. Do not invent disasters, diagnoses, purchases, helpers, or extra time.

Project name: {{project_name}}
Known blockers:
{{known_blockers}}
Materials ready:
{{materials_ready}}

Approved done definition:
{{artifact:define-done}}

Approved constraints:
{{artifact:name-constraints}}

Approved week sequence:
{{artifact:sequence-the-weeks}}

Produce Markdown with these exact headings:

## Risk list
A numbered list of 3 to 5 likely risks based on known blockers and the approved plan.

## Prevention moves
One small prevention move per risk. Stay inside the materials and time budget.

## Recovery moves
What to do if a risk happens. Prefer reducing scope inside the finish line over restarting.

## Watch points
Short list of signs to check during weekly check-ins.

Keep these four headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $risksExample,
            'output_sections' => [
                ['key' => 'risk_list', 'heading' => 'Risk list', 'required' => true],
                ['key' => 'prevention_moves', 'heading' => 'Prevention moves', 'required' => true],
                ['key' => 'recovery_moves', 'heading' => 'Recovery moves', 'required' => true],
                ['key' => 'watch_points', 'heading' => 'Watch points', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Risks come from known blockers', 'help' => 'The list reflects what you entered and approved, not imagined disasters.',
                 'evidence_sections' => ['risk_list']],
                ['label' => 'Prevention is small', 'help' => 'Moves can happen inside the current time and materials.',
                 'evidence_sections' => ['prevention_moves']],
                ['label' => 'Recovery avoids restarting', 'help' => 'The plan repairs or narrows instead of throwing everything away.',
                 'evidence_sections' => ['recovery_moves']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-checkin-template',
            'title'            => 'Write check-in template',
            'summary'          => 'Create a short weekly review that uses evidence, decisions, and one next move.',
            'why_it_matters'   => 'Personal projects drift when nobody checks the plan against reality. This step gives the project a lightweight review without turning it into admin work. Common mistakes: writing a long journal template, measuring feelings only, or scheduling exact check-in times not provided. Need help if the AI invents a calendar slot? Paste again and ask for questions only, with no assumed day or time. You are ready when the template can be answered in ten minutes.',
            'unlocks_text'     => 'Approving unlocks first-week planning, where the first real moves are chosen.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are writing a lightweight weekly check-in template for a personal project. Use ONLY the pantry facts and approved plan. Do not invent exact meeting times, accountability partners, apps, or metrics.

Project name: {{project_name}}
Focused hours per week: {{hours_per_week}}

Approved done definition:
{{artifact:define-done}}

Approved week sequence:
{{artifact:sequence-the-weeks}}

Approved risk list:
{{artifact:list-risks}}

Produce Markdown with these exact headings:

## Weekly check-in questions
Four to six questions that check progress, scope, blockers, and the next smallest move.

## Progress evidence
Three to five evidence items the person can collect from the approved done definition.

## Decision rule
One or two sentences explaining when to keep going, adjust, or reduce scope.

## Reset line
One kind sentence for restarting after a messy week without expanding the project.

Keep these four headings in order. Under 280 words. Invent nothing.
TXT,
            'example_response' => $checkinTemplateExample,
            'output_sections' => [
                ['key' => 'checkin_questions', 'heading' => 'Weekly check-in questions', 'required' => true],
                ['key' => 'progress_evidence', 'heading' => 'Progress evidence', 'required' => true],
                ['key' => 'decision_rule', 'heading' => 'Decision rule', 'required' => true],
                ['key' => 'reset_line', 'heading' => 'Reset line', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Template is short enough to use', 'help' => 'The check-in could be answered quickly during a normal week.',
                 'evidence_sections' => ['checkin_questions']],
                ['label' => 'Evidence matches done', 'help' => 'Progress evidence comes from the approved finish line.',
                 'evidence_sections' => ['progress_evidence']],
                ['label' => 'Decision rule is practical', 'help' => 'The rule tells you when to continue, adjust, or reduce scope.',
                 'evidence_sections' => ['decision_rule']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'plan-first-week',
            'title'            => 'Plan first week',
            'summary'          => 'Choose the first week of real moves that starts the project without overbuilding it.',
            'why_it_matters'   => 'A plan becomes real in the first week. This step turns the sequence into concrete actions while respecting the hours and materials already named. Common mistakes: using the first week for more planning only, buying extras, or placing tasks on exact days not provided. Need help if the AI over-schedules? Paste again and ask it to keep actions within the stated weekly hours and avoid exact days. You are ready when the first week has visible proof.',
            'unlocks_text'     => 'Approving unlocks the finish checklist, which closes the project cleanly.',
            'est_minutes'      => 8,
            'prompt_template'  => <<<'TXT'
You are planning the first week of a personal project. Use ONLY the pantry facts and approved artifacts. Do not invent exact weekdays, appointments, store hours, purchases beyond missing materials, or help from others.

Project name: {{project_name}}
Focused hours per week: {{hours_per_week}}
Materials ready:
{{materials_ready}}
Known blockers:
{{known_blockers}}

Approved constraints:
{{artifact:name-constraints}}

Approved chunks:
{{artifact:break-into-chunks}}

Approved week sequence:
{{artifact:sequence-the-weeks}}

Approved check-in template:
{{artifact:write-checkin-template}}

Produce Markdown with these exact headings:

## First-week actions
A numbered list of 5 to 8 concrete actions. Each should start with a verb and fit the first week of the approved sequence.

## Calendar placement
Describe how to fit the actions within the stated weekly hours without inventing exact days or times.

## Materials to touch
List the supplies, notes, spaces, or files used this week. Mark missing items only if already stated.

## End-of-week proof
One sentence naming the visible evidence that Week 1 happened.

Keep these four headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $firstWeekExample,
            'output_sections' => [
                ['key' => 'first_week_actions', 'heading' => 'First-week actions', 'required' => true],
                ['key' => 'calendar_placement', 'heading' => 'Calendar placement', 'required' => true],
                ['key' => 'materials_to_touch', 'heading' => 'Materials to touch', 'required' => true],
                ['key' => 'end_week_proof', 'heading' => 'End-of-week proof', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Actions are concrete', 'help' => 'Every first-week item starts with a real action.',
                 'evidence_sections' => ['first_week_actions']],
                ['label' => 'Calendar stays honest', 'help' => 'Placement uses the weekly hours without inventing exact availability.',
                 'evidence_sections' => ['calendar_placement']],
                ['label' => 'Proof is visible', 'help' => 'You can tell whether the first week happened.',
                 'evidence_sections' => ['end_week_proof']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-finish-checklist',
            'title'            => 'Pack finish checklist',
            'summary'          => 'Build the final checklist, closeout note, and parking lot for any next version.',
            'why_it_matters'   => 'Finishing needs a stop signal. This step prevents the project from turning into endless improvements by matching the checklist to the approved done definition. Common mistakes: adding a new version before closing this one, making the checklist vague, or requiring proof the person cannot collect. Need help if the AI expands the finish? Paste again and tell it to match only the approved done definition and artifacts. You are ready when every checklist item can be ticked or left unticked.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished project plan export.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are packing a finish checklist for a personal project. Use ONLY the pantry facts and approved artifacts. Do not invent new goals, purchases, public sharing, metrics, or next projects.

Project name: {{project_name}}
Deadline: {{deadline}}

Approved done definition:
{{artifact:define-done}}

Approved why-now:
{{artifact:name-why-now}}

Approved week sequence:
{{artifact:sequence-the-weeks}}

Approved first-week plan:
{{artifact:plan-first-week}}

Approved check-in template:
{{artifact:write-checkin-template}}

Produce Markdown with these exact headings:

## Finish checklist
Five to seven checkbox items that match the approved done definition and visible proof.

## Definition-of-done match
Two or three sentences explaining how the checklist proves this version is complete.

## Closeout note
A short note the person can write when the checklist is done. It should close this version without starting a new one.

## Next-version parking lot
Three bullets for ideas that can wait until after completion, using only already mentioned parked ideas or scope cuts.

Keep these four headings in order. Under 300 words. Invent nothing.
TXT,
            'example_response' => $finishChecklistExample,
            'output_sections' => [
                ['key' => 'finish_checklist', 'heading' => 'Finish checklist', 'required' => true],
                ['key' => 'dod_match', 'heading' => 'Definition-of-done match', 'required' => true],
                ['key' => 'closeout_note', 'heading' => 'Closeout note', 'required' => true],
                ['key' => 'next_version_parking', 'heading' => 'Next-version parking lot', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Checklist matches done', 'help' => 'Items prove the approved finish line instead of a bigger project.',
                 'evidence_sections' => ['finish_checklist', 'dod_match']],
                ['label' => 'Closeout stops the loop', 'help' => 'The note closes this version before any next version begins.',
                 'evidence_sections' => ['closeout_note']],
                ['label' => 'Future ideas are parked', 'help' => 'Next-version ideas wait until after the checklist is complete.',
                 'evidence_sections' => ['next_version_parking']],
            ],
        ],
    ],
];
