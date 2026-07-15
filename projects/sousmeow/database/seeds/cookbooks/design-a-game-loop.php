<?php

declare(strict_types=1);

/**
 * Design a Game Loop - executable Cookbook for creative-worlds.
 *
 * Inspired by public game-loop and lightweight MDA teaching. Ideas only;
 * original SousMeow copy, examples, and prompts. No engine tech claims.
 */

$playerFantasyExample = <<<'MD'
## Player fantasy
In Windowgarden, the player feels like a gentle caretaker arranging herbs on a rainy windowsill so each small plant can thrive.

## Player role
The player is the person tending the window: choosing where herbs sit, noticing what each plant needs, and making small adjustments.

## Feel promise
A session should feel calm, observant, and satisfying, with tiny changes that make the window look more alive.

## Design boundaries
No combat, no pressure timers, and no copied features from reference games. The design should stay focused on herbs, rain, and quiet puzzle choices.
MD;

$coreVerbExample = <<<'MD'
## Core verb
Arrange.

## Verb meaning
The player arranges herbs, pots, light, and small window objects so each plant has a better place to grow.

## Verb test
If a player says "I arranged the windowsill and the herbs responded," the loop is doing its job.

## Not the verb
The core verb is not fighting, farming a huge field, decorating a whole house, or managing a shop.
MD;

$coreLoopStepsExample = <<<'MD'
## Core loop
1. Look at the rainy windowsill.
2. Notice what one herb needs.
3. Arrange a pot or nearby object.
4. Watch the plant respond.
5. Collect a small growth reward.
6. Use the reward to unlock one new seed or ornament.
7. Return to the window with a new placement choice.

## Player choices
The main choices are which herb to help first, where to place it, and whether to spend a reward on a seed or a window object.

## Loop reason
The loop feels good because observation leads to a small action, the action changes the window, and the reward creates another gentle choice.

## Fragile spots
The loop may feel flat if plant needs are unclear, rewards arrive with no choice, or arranging objects does not visibly change anything.
MD;

$rewardRhythmExample = <<<'MD'
## Reward rhythm
Windowgarden rewards the player after each clear plant improvement, then offers a slightly larger reward at the end of the session.

## Moment feedback
- A leaf perks up after a good arrangement.
- Rain sound softens when the plant is comfortable.
- A small growth token appears after the response.

## Progression tie
Growth tokens unlock new herb seeds or small window ornaments, which create more arrangement choices without changing the quiet fantasy.

## Rhythm warning
Do not reward every tap. The reward should follow observation and a meaningful arrangement choice.
MD;

$oneSessionExample = <<<'MD'
## Session map
For a 15-minute Windowgarden session:
1. Arrive at the rainy window and see the current herbs.
2. Choose one herb that needs attention.
3. Try one or two arrangements.
4. Watch the herb respond.
5. Spend a small reward on a seed or ornament.
6. Place the new item.
7. End with the window a little more alive.

## Session end
The session ends when the player has improved at least one herb and placed or saved one reward.

## Return hook
The player returns because the next herb, seed, or window object suggests a new arrangement puzzle.

## Time fit
The loop stays small enough for 15 minutes by focusing on one or two herbs instead of the whole garden.
MD;

$failStatesExample = <<<'MD'
## Fail states
1. The player cannot tell what a herb needs.
2. Moving objects feels random.
3. Rewards unlock items that do not change choices.
4. The session has no clear stopping point.

## Soft recovery
Use gentle hints, visible plant reactions, and a simple "save the reward for later" option instead of punishing the player.

## Design safeguards
- Keep each plant need readable.
- Make every arrangement change visible.
- Tie new seeds and ornaments back to the arrange verb.
- End the session with a visible before/after difference.

## Avoided claims
This plan does not assume an engine, platform, network feature, or production budget.
MD;

$onboardingBeatExample = <<<'MD'
## First minute
The player opens Windowgarden to one small basil pot on a rainy windowsill. A leaf droops away from the light.

## Teach by doing
The player drags the pot closer to the bright patch, watches the leaf lift, and receives one growth token.

## First success
The first success is a visible plant response: the basil looks healthier and the windowsill feels warmer.

## What waits
New seeds, ornaments, multiple herbs, and longer puzzle combinations wait until after the player understands arrange -> response -> reward.
MD;

$loopSheetExample = <<<'MD'
## Loop sheet
Game: Windowgarden
Player fantasy: gentle herb caretaker on a rainy windowsill
Core verb: arrange
Session length: 15 minutes

Core loop:
Notice a plant need -> arrange a pot or object -> watch the plant respond -> earn a small reward -> unlock a seed or ornament -> return to the window with a new choice.

## Reward and progression
Small plant responses give growth tokens. Tokens unlock herbs and window ornaments that create more arrangement puzzles while keeping the calm caretaker fantasy.

## Session shape
A session should help one or two herbs, place or save one reward, and end with the window visibly improved.

## Open design questions
- Which plant needs are easiest to read at a glance?
- Which rewards create real arrangement choices?
- How many herbs can stay cozy inside 15 minutes?
MD;

return [
    'slug'                => 'design-a-game-loop',
    'title'               => 'Design a Game Loop',
    'tagline'             => 'Define what the player does, why it feels good, and how one session ends.',
    'description'         => "A game idea becomes easier to design when the fantasy, core verb, reward rhythm, and session end are named. This Cookbook helps you describe what the player wants to feel, what they do again and again, why the loop rewards them, and how one session closes. Enter only concept facts and feel references. Every prompt tells the assistant not to copy features or invent engine, platform, budget, or technology claims. You leave with a compact loop sheet for a small playable idea.",
    'primary_category'    => 'creative-worlds',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Game designers, writers, students, and hobbyists shaping a small game idea before feature lists',
    'outcome'             => 'player fantasy, core verb, core loop, reward rhythm, session map, fail states, onboarding beat, and loop sheet',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'plum',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 50,
    'demo_completed_runs' => 117,
    'demo_avg_rating'     => 4.6,
    'sort_order'          => 23,
    'stages' => [
        ['title' => 'Fantasy', 'summary' => 'Name the fantasy, action, loop, and reward feeling.'],
        ['title' => 'Loop', 'summary' => 'Map one session and the ways the loop can fail.'],
        ['title' => 'Session', 'summary' => 'Teach the first beat and pack a loop sheet.'],
    ],
    'fields' => [
        [
            'field_key'    => 'game_name',
            'label'        => 'Game name',
            'type'         => 'text',
            'help'         => 'Working title is fine.',
            'placeholder'  => 'e.g. Windowgarden',
            'sample_value' => 'Windowgarden',
        ],
        [
            'field_key'    => 'player_fantasy',
            'label'        => 'Player fantasy',
            'type'         => 'textarea',
            'help'         => 'What the player wants to feel like they are doing or becoming.',
            'placeholder'  => 'e.g. A gentle caretaker growing herbs on a rainy windowsill',
            'sample_value' => 'A gentle caretaker growing herbs on a rainy windowsill and solving quiet placement puzzles.',
        ],
        [
            'field_key'    => 'core_verb',
            'label'        => 'Core verb',
            'type'         => 'text',
            'help'         => 'One main action the player repeats. Use a verb.',
            'placeholder'  => 'e.g. arrange',
            'sample_value' => 'arrange',
        ],
        [
            'field_key'    => 'session_length',
            'label'        => 'Session length',
            'type'         => 'text',
            'help'         => 'How long one satisfying play session should take.',
            'placeholder'  => 'e.g. 15 minutes',
            'sample_value' => '15 minutes',
        ],
        [
            'field_key'    => 'progression_idea',
            'label'        => 'Progression idea',
            'type'         => 'textarea',
            'help'         => 'How the player sees change over time. Keep it lightweight.',
            'placeholder'  => 'e.g. Unlock new seeds and small window objects',
            'sample_value' => 'Earn growth tokens that unlock new herb seeds and small window ornaments.',
        ],
        [
            'field_key'    => 'known_constraints',
            'label'        => 'Known constraints',
            'type'         => 'textarea',
            'help'         => 'Tone, scope, audience, time, complexity, or content limits. Do not list engine claims unless they are real facts.',
            'placeholder'  => "No combat\nShort sessions\nSmall solo scope",
            'sample_value' => "Cozy tone\nNo combat\nNo pressure timers\nSmall herb set\nShort sessions",
        ],
        [
            'field_key'    => 'reference_feel',
            'label'        => 'Reference feel',
            'type'         => 'textarea',
            'help'         => 'Games you like the feel of, not features to copy.',
            'placeholder'  => 'e.g. Quiet puzzle games, gardening toys, rainy ambience',
            'sample_value' => "Quiet puzzle games\nGentle gardening toys\nRainy ambience\nSmall cozy rooms",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'lock-player-fantasy',
            'title'            => 'Lock player fantasy',
            'summary'          => 'State what the player should feel like they are doing and what is out of bounds.',
            'why_it_matters'   => 'A loop feels scattered when the fantasy is only a theme. This step names the role, promise, and boundaries before mechanics multiply. Common mistakes: copying reference features, adding technology claims, or describing the developer fantasy instead of the player fantasy. Need help if the AI gets broad? Paste again and require one player role. You are ready when the fantasy can guide yes/no decisions.',
            'unlocks_text'     => 'Approving unlocks the core verb.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are helping define a game concept. Use ONLY the facts below. Do not copy reference features or invent engine, platform, budget, or tech claims.

Game name: {{game_name}}
Player fantasy: {{player_fantasy}}
Known constraints:
{{known_constraints}}
Reference feel, not features to copy:
{{reference_feel}}

Return Markdown with these headings:
## Player fantasy
One or two sentences naming what the player feels like they are doing.
## Player role
Who the player is in the fantasy.
## Feel promise
Three adjectives or a short sentence about the intended feel.
## Design boundaries
What this game should not become, using only constraints and reference guidance.

Keep the four headings in order. Under 220 words.
TXT,
            'example_response' => $playerFantasyExample,
            'output_sections' => [
                ['key' => 'player_fantasy', 'heading' => 'Player fantasy', 'required' => true],
                ['key' => 'player_role', 'heading' => 'Player role', 'required' => true],
                ['key' => 'feel_promise', 'heading' => 'Feel promise', 'required' => true],
                ['key' => 'design_boundaries', 'heading' => 'Design boundaries', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Fantasy is player-centered', 'help' => 'The statement describes what the player feels or does.',
                 'evidence_sections' => ['player_fantasy', 'player_role']],
                ['label' => 'References are not copied', 'help' => 'Reference games shape feel only, not borrowed features.',
                 'evidence_sections' => ['design_boundaries']],
                ['label' => 'No tech claims appear', 'help' => 'The output does not assume engine, platform, or production details.',
                 'evidence_sections' => ['design_boundaries']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-core-verb',
            'title'            => 'Name core verb',
            'summary'          => 'Turn the fantasy into one repeatable player action.',
            'why_it_matters'   => 'The core verb keeps the loop from becoming a pile of features. This step names the repeated action and tests whether it supports the fantasy. Common mistakes: choosing a noun, adding several verbs, or sneaking in a bigger genre. Need help if the verb is fuzzy? Paste again and ask for one plain verb. You are ready when most future mechanics can be judged by that action.',
            'unlocks_text'     => 'Approving unlocks core loop steps.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are naming one core verb for a game loop. Use ONLY pantry facts and approved fantasy. Do not invent features, controls, engine tech, or platform.

Game name: {{game_name}}
Core verb candidate: {{core_verb}}
Progression idea: {{progression_idea}}

Approved fantasy:
{{artifact:lock-player-fantasy}}

Return Markdown with these headings:
## Core verb
One verb only, or the closest plain verb from the candidate.
## Verb meaning
Two sentences explaining what the player does with that verb.
## Verb test
One sentence a player could say after a good loop.
## Not the verb
Actions or genres this loop is not centered on.

Keep the four headings in order. Under 200 words.
TXT,
            'example_response' => $coreVerbExample,
            'output_sections' => [
                ['key' => 'core_verb', 'heading' => 'Core verb', 'required' => true],
                ['key' => 'verb_meaning', 'heading' => 'Verb meaning', 'required' => true],
                ['key' => 'verb_test', 'heading' => 'Verb test', 'required' => true],
                ['key' => 'not_the_verb', 'heading' => 'Not the verb', 'required' => true],
            ],
            'checks' => [
                ['label' => 'One verb leads', 'help' => 'The core action is not a list of competing verbs.',
                 'evidence_sections' => ['core_verb']],
                ['label' => 'Verb serves fantasy', 'help' => 'The meaning links back to the approved player fantasy.',
                 'evidence_sections' => ['verb_meaning']],
                ['label' => 'Non-core actions are excluded', 'help' => 'The loop has clear boundaries.',
                 'evidence_sections' => ['not_the_verb']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'list-core-loop-steps',
            'title'            => 'List core loop steps',
            'summary'          => 'Map the repeated sequence from noticing to action, feedback, reward, and return.',
            'why_it_matters'   => 'A loop needs a reason to repeat. This step shows what the player observes, does, receives, and wants next. Common mistakes: writing a story summary, skipping feedback, or making rewards unrelated to the verb. Need help if the loop is too long? Paste again and ask for six or fewer repeated beats. You are ready when the last beat naturally points back to the first.',
            'unlocks_text'     => 'Approving unlocks reward rhythm.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are mapping a core game loop. Use ONLY pantry facts and approved fantasy/verb. Do not invent engine tech, platform, monetization, or copied features.

Game name: {{game_name}}
Session length: {{session_length}}
Progression idea: {{progression_idea}}
Known constraints:
{{known_constraints}}

Approved fantasy:
{{artifact:lock-player-fantasy}}

Approved core verb:
{{artifact:name-core-verb}}

Return Markdown with these headings:
## Core loop
Numbered repeatable beats: observe, act, feedback, reward, return.
## Player choices
Two to four choices inside the loop.
## Loop reason
Why the loop should feel satisfying.
## Fragile spots
Where the loop could become confusing, flat, or too broad.

Keep the four headings in order. Under 300 words.
TXT,
            'example_response' => $coreLoopStepsExample,
            'output_sections' => [
                ['key' => 'core_loop', 'heading' => 'Core loop', 'required' => true],
                ['key' => 'player_choices', 'heading' => 'Player choices', 'required' => true],
                ['key' => 'loop_reason', 'heading' => 'Loop reason', 'required' => true],
                ['key' => 'fragile_spots', 'heading' => 'Fragile spots', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Loop can repeat', 'help' => 'The final beat creates a reason to return to the first.',
                 'evidence_sections' => ['core_loop']],
                ['label' => 'Choices are real', 'help' => 'The player has decisions, not only instructions.',
                 'evidence_sections' => ['player_choices']],
                ['label' => 'Fragile spots are named', 'help' => 'Likely weak points are visible before adding features.',
                 'evidence_sections' => ['fragile_spots']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-reward-rhythm',
            'title'            => 'Name reward rhythm',
            'summary'          => 'Define when rewards arrive, how feedback feels, and how progression supports the loop.',
            'why_it_matters'   => 'Rewards should reinforce the core action instead of distracting from it. This step names small feedback moments and a progression tie. Common mistakes: rewarding random taps, adding currencies with no choices, or promising content scale not in the facts. Need help if rewards feel generic? Paste again and tie each reward to the core verb. You are ready when feedback, reward, and progression point in the same direction.',
            'unlocks_text'     => 'Approving unlocks session mapping.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are designing reward rhythm for a small game loop. Use ONLY pantry facts and approved loop. Do not invent production scale, engine tech, platforms, or copied mechanics.

Game name: {{game_name}}
Progression idea: {{progression_idea}}
Reference feel:
{{reference_feel}}

Approved core verb:
{{artifact:name-core-verb}}

Approved core loop:
{{artifact:list-core-loop-steps}}

Return Markdown with these headings:
## Reward rhythm
When small and session-level rewards happen.
## Moment feedback
Three to five sensory or visible feedback moments that fit the feel.
## Progression tie
How rewards connect to progression without leaving the core verb.
## Rhythm warning
One warning that prevents reward spam or scope creep.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $rewardRhythmExample,
            'output_sections' => [
                ['key' => 'reward_rhythm', 'heading' => 'Reward rhythm', 'required' => true],
                ['key' => 'moment_feedback', 'heading' => 'Moment feedback', 'required' => true],
                ['key' => 'progression_tie', 'heading' => 'Progression tie', 'required' => true],
                ['key' => 'rhythm_warning', 'heading' => 'Rhythm warning', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Rewards follow action', 'help' => 'Reward timing reinforces the approved loop.',
                 'evidence_sections' => ['reward_rhythm']],
                ['label' => 'Feedback fits the feel', 'help' => 'Moment feedback supports the reference feel without copying features.',
                 'evidence_sections' => ['moment_feedback']],
                ['label' => 'Progression stays on-verb', 'help' => 'Unlocks create more of the core action, not a separate game.',
                 'evidence_sections' => ['progression_tie']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'map-one-session',
            'title'            => 'Map one session',
            'summary'          => 'Shape a single play session with a beginning, middle, ending, and return hook.',
            'why_it_matters'   => 'Loops live inside sessions. This step makes one sitting feel complete instead of endless or abruptly cut off. Common mistakes: ignoring session length, ending only when content runs out, or adding calendar and platform claims. Need help if the session sprawls? Paste again and ask for one satisfying sitting. You are ready when the player knows why the session ended.',
            'unlocks_text'     => 'Approving unlocks fail-state planning.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are mapping one session for a game concept. Use ONLY pantry facts and approved loop. Do not invent engine tech, platform, analytics, dates, or budget.

Game name: {{game_name}}
Session length: {{session_length}}
Known constraints:
{{known_constraints}}

Approved core loop:
{{artifact:list-core-loop-steps}}

Approved reward rhythm:
{{artifact:name-reward-rhythm}}

Return Markdown with these headings:
## Session map
Numbered beats for one session from arrival to close.
## Session end
What makes the session feel complete.
## Return hook
Why the player might want another session.
## Time fit
How the map respects the stated session length.

Keep the four headings in order. Under 300 words.
TXT,
            'example_response' => $oneSessionExample,
            'output_sections' => [
                ['key' => 'session_map', 'heading' => 'Session map', 'required' => true],
                ['key' => 'session_end', 'heading' => 'Session end', 'required' => true],
                ['key' => 'return_hook', 'heading' => 'Return hook', 'required' => true],
                ['key' => 'time_fit', 'heading' => 'Time fit', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Session has an ending', 'help' => 'The player can tell when one sitting is complete.',
                 'evidence_sections' => ['session_end']],
                ['label' => 'Return hook follows the loop', 'help' => 'The next-session reason comes from the approved loop or rewards.',
                 'evidence_sections' => ['return_hook']],
                ['label' => 'Length is respected', 'help' => 'The session map fits the stated session length.',
                 'evidence_sections' => ['time_fit']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'list-fail-states',
            'title'            => 'List fail states',
            'summary'          => 'Name ways the loop can break and gentle safeguards that keep the session readable.',
            'why_it_matters'   => 'A loop can fail even when its idea is appealing. This step names confusion, randomness, missing feedback, and unclear endings before more features are added. Common mistakes: treating fail states as player blame, inventing punishments, or assuming technical solutions. Need help if the AI gets harsh? Paste again and ask for soft recovery. You are ready when each failure has a design safeguard.',
            'unlocks_text'     => 'Approving unlocks the first onboarding beat.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are listing design fail states for a game loop. Use ONLY approved artifacts and pantry constraints. Do not invent engine limitations, platform issues, monetization, or punishment systems.

Game name: {{game_name}}
Known constraints:
{{known_constraints}}

Approved fantasy:
{{artifact:lock-player-fantasy}}

Approved loop:
{{artifact:list-core-loop-steps}}

Approved session map:
{{artifact:map-one-session}}

Return Markdown with these headings:
## Fail states
Three to five ways the loop or session could stop feeling clear or good.
## Soft recovery
How to help the player recover without breaking the feel.
## Design safeguards
Bullets that protect the loop from those failures.
## Avoided claims
State what this plan does not assume about technology or production.

Keep the four headings in order. Under 280 words.
TXT,
            'example_response' => $failStatesExample,
            'output_sections' => [
                ['key' => 'fail_states', 'heading' => 'Fail states', 'required' => true],
                ['key' => 'soft_recovery', 'heading' => 'Soft recovery', 'required' => true],
                ['key' => 'design_safeguards', 'heading' => 'Design safeguards', 'required' => true],
                ['key' => 'avoided_claims', 'heading' => 'Avoided claims', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Failures are about design clarity', 'help' => 'The fail states do not blame the player.',
                 'evidence_sections' => ['fail_states']],
                ['label' => 'Recovery fits the feel', 'help' => 'Recovery actions respect the approved fantasy and constraints.',
                 'evidence_sections' => ['soft_recovery']],
                ['label' => 'No tech assumptions slipped in', 'help' => 'The output avoids engine, platform, and production claims.',
                 'evidence_sections' => ['avoided_claims']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'write-onboarding-beat',
            'title'            => 'Write onboarding beat',
            'summary'          => 'Create the first minute that teaches the loop through one small success.',
            'why_it_matters'   => 'The first minute should let the player feel the loop, not read a feature list. This step teaches the core verb with one tiny win and postpones everything else. Common mistakes: explaining every system, copying a tutorial pattern, or introducing progression too early. Need help if the beat is crowded? Paste again and ask for one verb, one response, one reward. You are ready when the player can learn by doing.',
            'unlocks_text'     => 'Approving unlocks the final loop sheet.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are writing the first onboarding beat for a game loop. Use ONLY approved artifacts. Do not invent engine tech, controls, platform, UI, or copied tutorial features.

Game name: {{game_name}}

Approved fantasy:
{{artifact:lock-player-fantasy}}

Approved core verb:
{{artifact:name-core-verb}}

Approved loop:
{{artifact:list-core-loop-steps}}

Approved fail states:
{{artifact:list-fail-states}}

Return Markdown with these headings:
## First minute
What the player sees and understands first.
## Teach by doing
One small action using the core verb and one immediate response.
## First success
What success looks or feels like.
## What waits
Systems, rewards, or choices to introduce later.

Keep the four headings in order. Under 260 words.
TXT,
            'example_response' => $onboardingBeatExample,
            'output_sections' => [
                ['key' => 'first_minute', 'heading' => 'First minute', 'required' => true],
                ['key' => 'teach_by_doing', 'heading' => 'Teach by doing', 'required' => true],
                ['key' => 'first_success', 'heading' => 'First success', 'required' => true],
                ['key' => 'what_waits', 'heading' => 'What waits', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Onboarding starts with the fantasy', 'help' => 'The first minute quickly shows what kind of experience this is.',
                 'evidence_sections' => ['first_minute']],
                ['label' => 'Core verb is taught by action', 'help' => 'The player learns by doing one approved verb.',
                 'evidence_sections' => ['teach_by_doing']],
                ['label' => 'Complexity waits', 'help' => 'Later systems do not crowd the first beat.',
                 'evidence_sections' => ['what_waits']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-loop-sheet',
            'title'            => 'Pack loop sheet',
            'summary'          => 'Assemble the fantasy, verb, loop, rewards, session shape, and open questions.',
            'why_it_matters'   => 'A compact loop sheet keeps the idea testable. This step gathers the approved decisions without turning them into a production plan. Common mistakes: adding features at the end, claiming engine feasibility, or losing the session end. Need help if the sheet grows? Paste again and ask for one page only. You are ready when the next design conversation can start from the same loop.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your loop sheet export.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are packing a concise game loop sheet. Use ONLY pantry facts and approved artifacts. Do not invent engine, platform, budget, team, release, or copied features.

Game name: {{game_name}}
Session length: {{session_length}}

Approved fantasy:
{{artifact:lock-player-fantasy}}

Approved core verb:
{{artifact:name-core-verb}}

Approved core loop:
{{artifact:list-core-loop-steps}}

Approved rewards:
{{artifact:name-reward-rhythm}}

Approved session:
{{artifact:map-one-session}}

Return Markdown with these headings:
## Loop sheet
Game name, fantasy, core verb, session length, and core loop.
## Reward and progression
How feedback and progression support the core loop.
## Session shape
How one session starts, ends, and invites return.
## Open design questions
Three to five questions to answer next without inventing production claims.

Keep the four headings in order. Under 360 words.
TXT,
            'example_response' => $loopSheetExample,
            'output_sections' => [
                ['key' => 'loop_sheet', 'heading' => 'Loop sheet', 'required' => true],
                ['key' => 'reward_progression', 'heading' => 'Reward and progression', 'required' => true],
                ['key' => 'session_shape', 'heading' => 'Session shape', 'required' => true],
                ['key' => 'open_design_questions', 'heading' => 'Open design questions', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Sheet preserves the core loop', 'help' => 'Fantasy, verb, and loop are all visible together.',
                 'evidence_sections' => ['loop_sheet']],
                ['label' => 'Rewards support progression', 'help' => 'Reward and progression do not become a separate game.',
                 'evidence_sections' => ['reward_progression']],
                ['label' => 'Questions stay design-focused', 'help' => 'Open questions do not invent technology or production claims.',
                 'evidence_sections' => ['open_design_questions']],
            ],
        ],
    ],
];
