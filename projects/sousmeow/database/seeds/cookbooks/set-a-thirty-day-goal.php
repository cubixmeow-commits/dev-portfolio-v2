<?php

declare(strict_types=1);

/**
 * Set a Thirty-Day Goal - executable Cookbook for personal-projects.
 *
 * Inspired by public habit and short-horizon goal advising, distinct from
 * SMART project-finish Cookbooks. Ideas only; original SousMeow copy,
 * examples, and prompts.
 */

$oneGoalExample = <<<'MD'
## One goal
Desk Garden is a thirty-day goal to sprout basil on the desk ledge and harvest enough leaves for one simple lunch.

## Why thirty days
Thirty days is short enough to keep the basil visible every workday and long enough to practice a small care rhythm.

## Done definition
Done means basil has sprouted, stayed alive on the desk ledge, and provided a small harvest for one lunch by the end of the thirty days.

## Not this month
This month is not about building a full herb garden, buying extra planters, or learning every basil variety.
MD;

$fourWeeksExample = <<<'MD'
## Four-week path
1. Week 1: Set up the pot, plant the basil seeds, and start the water/light check.
2. Week 2: Watch for sprouts and keep the soil lightly moist.
3. Week 3: Thin or adjust the strongest sprouts if needed and keep the desk ledge routine steady.
4. Week 4: Keep caring for the basil and harvest a small amount if the leaves are ready.

## Week purpose
Week 1 starts the goal. Weeks 2 and 3 protect the rhythm. Week 4 checks whether the harvest-ready version of done has arrived.

## Evidence by week
- Week 1: planted pot on the desk ledge.
- Week 2: sprout photo or note.
- Week 3: care note after several checks.
- Week 4: harvest note or final growth photo.

## Scope guard
Stay with one basil pot on the desk ledge for this thirty-day goal.
MD;

$weeklyActionsExample = <<<'MD'
## Weekly actions
- Plant and label the basil pot in Week 1.
- Check water and light on available days.
- Take one quick photo or note each week.
- Remove only obvious weak sprouts if the pot gets crowded.
- Harvest a small amount in Week 4 if the leaves are ready.

## Days-available fit
With 20 available days, the goal needs short checks rather than long sessions. Most actions should take a few minutes on days the desk is available.

## Support to use
Use the sunny desk ledge, the pot and seeds already on hand, and the Friday reminder from Sam to notice whether the routine is slipping.

## Minimum viable week
A week still counts if the basil is checked, watered if needed, and one note or photo records what changed.
MD;

$obstaclesExample = <<<'MD'
## If-then obstacles
- If I forget two checks in a row, then I put the watering cup beside the keyboard.
- If the soil dries out, then I water lightly and return to the next available-day check.
- If I want to buy more planters, then I write the idea on a later list and keep one pot.
- If the basil is not ready to harvest by Day 30, then I record the growth honestly instead of expanding the goal.

## Prevention moves
Keep the pot visible, pair checks with starting work, and use the Friday reminder before the weekend.

## Recovery moves
Restart the next available-day check without doubling the plan. Do not add a second plant to make up for a missed day.

## Support ask
Ask Sam to send one Friday question: "Did Desk Garden get checked this week?"
MD;

$weeklyCheckExample = <<<'MD'
## Weekly check
1. Did I check water and light on the days available this week?
2. What changed in the basil pot?
3. What obstacle showed up?
4. Which if-then rule will I use next week?
5. What is the smallest next action?

## Evidence to record
- One photo or note of the pot.
- Any sprout, leaf, soil, or watering observation.
- Whether the week stayed with one basil pot.

## Adjustment rule
If the goal is moving, keep the same rhythm. If the same obstacle appears twice, use one prevention move before adding any new task.

## Next-week sentence
Next week, Desk Garden continues with one pot, short checks, and the smallest action that keeps the basil alive.
MD;

$goalCardExample = <<<'MD'
## Thirty-day goal card
Goal: Desk Garden
Finish line: sprout basil on the desk ledge and harvest enough leaves for one simple lunch by the end of thirty days.
Why: a short, visible goal can turn "someday herbs" into a daily care rhythm.
Available days: 20

## Weekly rhythm
Week 1 plant and label. Week 2 protect sprouts. Week 3 keep care steady. Week 4 harvest if ready and record the final state.

## Obstacle plan
Forgotten checks get paired with the keyboard. Dry soil gets a light watering and a return to the rhythm. Extra planter ideas go on a later list.

## Commitment line
For the next thirty days, I will keep one basil pot visible, check it on available days, and close the goal with honest evidence instead of expanding it.
MD;

return [
    'slug'                => 'set-a-thirty-day-goal',
    'title'               => 'Set a Thirty-Day Goal',
    'tagline'             => 'Pick one personal finish line for the next thirty days and the weekly rhythm to reach it.',
    'description'         => "Thirty days is long enough to see progress and short enough to stay honest. This Cookbook helps you choose one goal, define done, split the month into four weeks, place small actions on available days, prepare if-then obstacle moves, and pack a compact goal card. Enter only the goal, reason, available days, obstacles, and support you truly have. Every prompt tells the assistant to invent nothing about your calendar, motivation, tools, or helpers. You leave with a thirty-day card you can reread each week.",
    'primary_category'    => 'personal-projects',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'People choosing one personal goal for the next thirty days without turning it into a full project plan',
    'outcome'             => 'one-goal statement, four-week path, weekly actions, if-then obstacle plan, weekly check, and thirty-day goal card',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'moss',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 22,
    'demo_completed_runs' => 389,
    'demo_avg_rating'     => 4.9,
    'sort_order'          => 24,
    'stages' => [
        ['title' => 'Choose', 'summary' => 'Lock one thirty-day finish line before planning actions.'],
        ['title' => 'Plan', 'summary' => 'Split the month, place actions, plan obstacles, and write a weekly check.'],
        ['title' => 'Commit', 'summary' => 'Pack the goal into a short card for the next thirty days.'],
    ],
    'fields' => [
        [
            'field_key'    => 'goal_title',
            'label'        => 'Goal title',
            'type'         => 'text',
            'help'         => 'Give the thirty-day goal a short name.',
            'placeholder'  => 'e.g. Desk Garden',
            'sample_value' => 'Desk Garden',
        ],
        [
            'field_key'    => 'why_thirty_days',
            'label'        => 'Why thirty days?',
            'type'         => 'textarea',
            'help'         => 'Why this goal belongs in the next thirty days. Keep it honest and specific.',
            'placeholder'  => 'e.g. I want a small visible care habit before the month ends',
            'sample_value' => 'I want a small visible care habit at my desk and I keep postponing growing herbs because the idea gets too big.',
        ],
        [
            'field_key'    => 'done_definition',
            'label'        => 'What counts as done?',
            'type'         => 'textarea',
            'help'         => 'The personal finish line for Day 30. Make it observable.',
            'placeholder'  => 'e.g. Sprout basil and harvest enough leaves for one lunch',
            'sample_value' => 'Sprout basil on the desk ledge and harvest enough leaves for one simple lunch by the end of thirty days.',
        ],
        [
            'field_key'    => 'days_available',
            'label'        => 'Days available in the next 30',
            'type'         => 'number',
            'help'         => 'How many days you can realistically touch this goal. The plan will not invent more.',
            'placeholder'  => '20',
            'sample_value' => '20',
        ],
        [
            'field_key'    => 'obstacles',
            'label'        => 'Likely obstacles',
            'type'         => 'textarea',
            'help'         => 'What could interrupt the thirty-day rhythm?',
            'placeholder'  => "Forgetting checks\nOverbuying supplies\nTravel days",
            'sample_value' => "Forgetting to check water\nSoil drying out over the weekend\nBuying extra planters instead of keeping one pot\nBasil may not be ready to harvest exactly on Day 30",
        ],
        [
            'field_key'    => 'support_you_have',
            'label'        => 'Support you already have',
            'type'         => 'textarea',
            'help'         => 'People, places, tools, reminders, supplies, or routines already available.',
            'placeholder'  => "Sunny ledge\nSeeds\nFriend reminder",
            'sample_value' => "Sunny desk ledge\nSmall pot\nBasil seeds\nWatering cup\nSam can ask on Fridays whether I checked the plant",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'lock-one-goal',
            'title'            => 'Lock one goal',
            'summary'          => 'Turn the idea into one thirty-day goal with a clear finish line and scope cut.',
            'why_it_matters'   => 'A thirty-day goal works best when it is one finish line, not a basket of improvements. This step names the goal, the reason thirty days matters, and what is not included. Common mistakes: adding a second goal, inventing motivation, or making done depend on perfect conditions. Need help if the AI expands it? Paste again and ask for one goal only. You are ready when the Day 30 proof is visible.',
            'unlocks_text'     => 'Approving unlocks the four-week path.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are helping choose one thirty-day goal. Use ONLY the facts below. Invent nothing about schedule, motivation, supplies, helpers, or outcomes.

Goal title: {{goal_title}}
Why thirty days: {{why_thirty_days}}
Done definition: {{done_definition}}
Days available: {{days_available}}
Obstacles:
{{obstacles}}

Return Markdown with these headings:
## One goal
One sentence naming the single thirty-day goal.
## Why thirty days
One or two sentences using the stated reason.
## Done definition
An observable Day 30 finish line.
## Not this month
What is outside scope for these thirty days.

Keep the four headings in order. Under 220 words.
TXT,
            'example_response' => $oneGoalExample,
            'output_sections' => [
                ['key' => 'one_goal', 'heading' => 'One goal', 'required' => true],
                ['key' => 'why_thirty_days', 'heading' => 'Why thirty days', 'required' => true],
                ['key' => 'done_definition', 'heading' => 'Done definition', 'required' => true],
                ['key' => 'not_this_month', 'heading' => 'Not this month', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Only one goal', 'help' => 'The statement does not combine several personal goals.',
                 'evidence_sections' => ['one_goal']],
                ['label' => 'Done is observable', 'help' => 'Day 30 completion can be checked from evidence.',
                 'evidence_sections' => ['done_definition']],
                ['label' => 'Scope is cut', 'help' => 'The plan protects the month from extra ambitions.',
                 'evidence_sections' => ['not_this_month']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'split-into-four-weeks',
            'title'            => 'Split into four weeks',
            'summary'          => 'Create a four-week path from start to proof without adding a bigger project plan.',
            'why_it_matters'   => 'Thirty days is easier to hold when each week has a purpose. This step creates a simple month arc while keeping the goal smaller than a project roadmap. Common mistakes: planning exact dates not provided, overloading Week 1, or expanding the finish line. Need help if the AI makes a calendar? Paste again and ask for week labels only. You are ready when each week has one job.',
            'unlocks_text'     => 'Approving unlocks weekly action placement.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are splitting a thirty-day goal into four weeks. Use ONLY pantry facts and the approved one-goal artifact. Do not invent dates, exact weekdays, supplies, helpers, or a larger project.

Goal title: {{goal_title}}
Days available: {{days_available}}
Support:
{{support_you_have}}

Approved goal:
{{artifact:lock-one-goal}}

Return Markdown with these headings:
## Four-week path
Numbered Week 1 to Week 4 path toward the approved done definition.
## Week purpose
One or two sentences explaining the arc of the month.
## Evidence by week
One evidence item per week.
## Scope guard
One sentence keeping the goal inside thirty days.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $fourWeeksExample,
            'output_sections' => [
                ['key' => 'four_week_path', 'heading' => 'Four-week path', 'required' => true],
                ['key' => 'week_purpose', 'heading' => 'Week purpose', 'required' => true],
                ['key' => 'evidence_by_week', 'heading' => 'Evidence by week', 'required' => true],
                ['key' => 'scope_guard', 'heading' => 'Scope guard', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Four weeks are present', 'help' => 'The path uses Week 1 through Week 4.',
                 'evidence_sections' => ['four_week_path']],
                ['label' => 'Evidence is weekly', 'help' => 'Each week has a simple proof item.',
                 'evidence_sections' => ['evidence_by_week']],
                ['label' => 'Goal stays small', 'help' => 'The scope guard prevents a full project plan.',
                 'evidence_sections' => ['scope_guard']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'place-weekly-actions',
            'title'            => 'Place weekly actions',
            'summary'          => 'Choose small actions that fit the available days and support already present.',
            'why_it_matters'   => 'A month plan fails when actions assume more days, energy, or help than exist. This step turns the four-week path into repeatable actions that respect available days. Common mistakes: inventing exact schedules, adding supplies, or treating a missed day as failure. Need help if it overbooks you? Paste again and require the days-available number as a limit. You are ready when the week can survive a normal life.',
            'unlocks_text'     => 'Approving unlocks if-then obstacle planning.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are placing weekly actions for a thirty-day goal. Use ONLY pantry facts and approved artifacts. Do not invent exact dates, extra days, new supplies, or new helpers.

Goal title: {{goal_title}}
Days available: {{days_available}}
Support:
{{support_you_have}}
Obstacles:
{{obstacles}}

Approved goal:
{{artifact:lock-one-goal}}

Approved four-week path:
{{artifact:split-into-four-weeks}}

Return Markdown with these headings:
## Weekly actions
Five to seven small actions for the month.
## Days-available fit
How the actions respect the available-day number.
## Support to use
Support from the pantry that makes the actions easier.
## Minimum viable week
What still counts in a rough week.

Keep the four headings in order. Under 280 words.
TXT,
            'example_response' => $weeklyActionsExample,
            'output_sections' => [
                ['key' => 'weekly_actions', 'heading' => 'Weekly actions', 'required' => true],
                ['key' => 'days_available_fit', 'heading' => 'Days-available fit', 'required' => true],
                ['key' => 'support_to_use', 'heading' => 'Support to use', 'required' => true],
                ['key' => 'minimum_viable_week', 'heading' => 'Minimum viable week', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Actions are small', 'help' => 'Weekly actions can happen inside normal available days.',
                 'evidence_sections' => ['weekly_actions']],
                ['label' => 'Available days are respected', 'help' => 'The plan does not invent more days.',
                 'evidence_sections' => ['days_available_fit']],
                ['label' => 'Support comes from facts', 'help' => 'No new helper, app, or supply appears.',
                 'evidence_sections' => ['support_to_use']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'name-if-then-obstacles',
            'title'            => 'Name if-then obstacles',
            'summary'          => 'Prepare simple if-then moves for the obstacles most likely to interrupt the month.',
            'why_it_matters'   => 'Obstacles are easier to handle before they arrive. This step turns likely interruptions into small if-then choices, not self-criticism. Common mistakes: inventing dramatic problems, adding punishment, or solving everything with more time. Need help if the AI gets harsh? Paste again and ask for kind recovery language. You are ready when every obstacle has a next move.',
            'unlocks_text'     => 'Approving unlocks the weekly check.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are writing if-then obstacle moves for a thirty-day goal. Use ONLY pantry facts and approved actions. Do not invent diagnoses, extra time, purchases, or helpers.

Goal title: {{goal_title}}
Obstacles:
{{obstacles}}
Support:
{{support_you_have}}

Approved weekly actions:
{{artifact:place-weekly-actions}}

Return Markdown with these headings:
## If-then obstacles
Three to five if-then rules for likely obstacles.
## Prevention moves
Small setup moves that reduce obstacle odds.
## Recovery moves
How to restart without expanding the goal.
## Support ask
One ask using only support already provided, or say none was provided.

Keep the four headings in order. Under 280 words.
TXT,
            'example_response' => $obstaclesExample,
            'output_sections' => [
                ['key' => 'if_then_obstacles', 'heading' => 'If-then obstacles', 'required' => true],
                ['key' => 'prevention_moves', 'heading' => 'Prevention moves', 'required' => true],
                ['key' => 'recovery_moves', 'heading' => 'Recovery moves', 'required' => true],
                ['key' => 'support_ask', 'heading' => 'Support ask', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Obstacles match pantry facts', 'help' => 'If-then rules come from obstacles you entered.',
                 'evidence_sections' => ['if_then_obstacles']],
                ['label' => 'Recovery is kind and small', 'help' => 'Restart moves do not punish or expand the goal.',
                 'evidence_sections' => ['recovery_moves']],
                ['label' => 'Support is not invented', 'help' => 'The ask uses only support already named.',
                 'evidence_sections' => ['support_ask']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-weekly-check',
            'title'            => 'Write weekly check',
            'summary'          => 'Create a short weekly review that uses evidence, adjustment, and one next action.',
            'why_it_matters'   => 'A thirty-day goal needs a light check, not a giant tracking system. This step creates a weekly review that keeps the month honest and adjustable. Common mistakes: measuring only mood, inventing metrics, or making the check longer than the action. Need help if it becomes admin? Paste again and ask for five questions maximum. You are ready when the check takes a few minutes.',
            'unlocks_text'     => 'Approving unlocks the final thirty-day goal card.',
            'est_minutes'      => 3,
            'prompt_template'  => <<<'TXT'
You are writing a weekly check for a thirty-day goal. Use ONLY pantry facts and approved artifacts. Do not invent apps, dates, metrics, or accountability systems.

Goal title: {{goal_title}}
Days available: {{days_available}}

Approved goal:
{{artifact:lock-one-goal}}

Approved weekly actions:
{{artifact:place-weekly-actions}}

Approved obstacle plan:
{{artifact:name-if-then-obstacles}}

Return Markdown with these headings:
## Weekly check
Four to six questions for a quick weekly review.
## Evidence to record
Three to five evidence items tied to the done definition.
## Adjustment rule
When to keep going, simplify, or use an if-then rule.
## Next-week sentence
One sentence to start the next week.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $weeklyCheckExample,
            'output_sections' => [
                ['key' => 'weekly_check', 'heading' => 'Weekly check', 'required' => true],
                ['key' => 'evidence_to_record', 'heading' => 'Evidence to record', 'required' => true],
                ['key' => 'adjustment_rule', 'heading' => 'Adjustment rule', 'required' => true],
                ['key' => 'next_week_sentence', 'heading' => 'Next-week sentence', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Check is lightweight', 'help' => 'Questions can be answered quickly each week.',
                 'evidence_sections' => ['weekly_check']],
                ['label' => 'Evidence matches done', 'help' => 'Recorded evidence proves progress toward the finish line.',
                 'evidence_sections' => ['evidence_to_record']],
                ['label' => 'Adjustment is practical', 'help' => 'The rule says when to keep, simplify, or use if-then moves.',
                 'evidence_sections' => ['adjustment_rule']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-thirty-day-card',
            'title'            => 'Pack thirty-day card',
            'summary'          => 'Assemble the goal, reason, weekly rhythm, obstacle plan, and commitment line.',
            'why_it_matters'   => 'A goal card is short enough to reread when the month gets messy. This step packs the approved pieces into one personal card without turning it into a full project plan. Common mistakes: adding new goals, promising perfect streaks, or hiding obstacles. Need help if the card grows? Paste again and ask for one screen. You are ready when the card can guide the next thirty days.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your thirty-day goal card export.',
            'est_minutes'      => 3,
            'prompt_template'  => <<<'TXT'
You are packing a thirty-day goal card. Use ONLY pantry facts and approved artifacts. Do not invent new goals, dates, helpers, supplies, or metrics.

Goal title: {{goal_title}}
Days available: {{days_available}}

Approved goal:
{{artifact:lock-one-goal}}

Approved four-week path:
{{artifact:split-into-four-weeks}}

Approved obstacle plan:
{{artifact:name-if-then-obstacles}}

Approved weekly check:
{{artifact:write-weekly-check}}

Return Markdown with these headings:
## Thirty-day goal card
Goal, finish line, why, and available days.
## Weekly rhythm
One compact sentence or list for Weeks 1-4.
## Obstacle plan
The most important if-then moves.
## Commitment line
One honest line the person can reread during the month.

Keep the four headings in order. Under 280 words.
TXT,
            'example_response' => $goalCardExample,
            'output_sections' => [
                ['key' => 'thirty_day_goal_card', 'heading' => 'Thirty-day goal card', 'required' => true],
                ['key' => 'weekly_rhythm', 'heading' => 'Weekly rhythm', 'required' => true],
                ['key' => 'obstacle_plan', 'heading' => 'Obstacle plan', 'required' => true],
                ['key' => 'commitment_line', 'heading' => 'Commitment line', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Card includes the finish line', 'help' => 'The single goal and done definition are easy to find.',
                 'evidence_sections' => ['thirty_day_goal_card']],
                ['label' => 'Weekly rhythm is compact', 'help' => 'The card can be reread quickly during the month.',
                 'evidence_sections' => ['weekly_rhythm']],
                ['label' => 'Commitment stays honest', 'help' => 'The final line does not promise perfect streaks or expanded scope.',
                 'evidence_sections' => ['commitment_line']],
            ],
        ],
    ],
];
