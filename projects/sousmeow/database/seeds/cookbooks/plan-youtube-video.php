<?php

declare(strict_types=1);

$researchBriefExample = <<<'MD'
## Topic snapshot

One-pan weeknight pasta: a beginner-friendly video showing how to make a complete dinner in one skillet with minimal cleanup. The dish uses pantry staples and finishes in under 30 minutes.

## Search intent

Viewers are searching for quick pasta recipes, one-pan meals, and easy weeknight dinners. They want something they can make tonight without special tools or skills.

## Why this topic now

One-pan cooking content performs well in Q1 when viewers reset routines after holidays. The format maps cleanly to a short tutorial with strong visual payoff (one pan, bubbling sauce, finished plate).

## What viewers already know

Most viewers can boil pasta and brown ground meat or saute vegetables. They struggle with timing, sauce consistency, and avoiding a sticky pan.

## What this video must deliver

A repeatable method: choose protein or veg, build a sauce in the same pan, finish the pasta without a separate pot if possible. Leave them with a grocery list of five items or fewer.

## Risks to flag

- Competing with channels that use identical "one pan pasta" titles
- Viewer expectation of "no boil" vs. partial boil methods
- Need to show fail-safe moments (too dry, too wet, burned garlic)

## Angle recommendation

Position as "the one-pan method for people who hate doing dishes", not as a viral hack. Honest about tradeoffs: slightly less traditional texture, massively less cleanup.
MD;

return [
    'slug'                => 'plan-youtube-video',
    'title'               => 'Plan a YouTube Video',
    'tagline'             => 'From a rough idea to a publish-ready plan, one small Recipe at a time.',
    'description'         => "Most YouTube videos fail before anyone presses record: the idea is vague, the hook is borrowed, and the outline is a list of things to say instead of reasons to keep watching. This Cookbook moves fast through ten small outputs, so you leave with research, a hook, a retention map, B-roll shots, titles, thumbnail direction, and a publish checklist. Stock the Pantry once with your topic and channel context; every Recipe builds on the last. Beginner-friendly, no jargon, no pretending you need a studio.",
    'category'            => 'Content',
    'audience'            => 'New YouTube creators planning their next upload',
    'outcome'             => 'Research brief, hook options, outline, retention beats, shot list, titles, thumbnail notes, and a publish checklist',
    'price_cents'         => null,
    'is_executable'       => false,
    'accent'              => 'sage',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 45,
    'demo_completed_runs' => 591,
    'demo_avg_rating'     => 4.5,
    'sort_order'          => 4,
    'stages' => [
        ['title' => 'Research and Angle', 'summary' => 'Ground the idea in search intent, audience, and what competitors already cover.'],
        ['title' => 'Structure and Hook', 'summary' => 'Shape the opening, the middle, and the shots that keep people watching.'],
        ['title' => 'Package and Publish', 'summary' => 'Name it, frame it, and ship it without forgetting the small steps.'],
    ],
    'fields' => [
        [
            'field_key'    => 'video_topic',
            'label'        => 'Video topic',
            'type'         => 'text',
            'help'         => 'The working title or idea in plain language. Recipes sharpen it; they do not replace it.',
            'placeholder'  => 'e.g. One-pan weeknight pasta',
            'sample_value' => 'One-pan weeknight pasta',
        ],
        [
            'field_key'    => 'channel_niche',
            'label'        => 'Channel niche',
            'type'         => 'text',
            'help'         => 'What your channel is about in one sentence. Helps every Recipe stay on-brand.',
            'placeholder'  => 'e.g. Beginner-friendly home cooking for busy weeknights',
            'sample_value' => 'Beginner-friendly home cooking for busy weeknights',
        ],
        [
            'field_key'    => 'target_viewer',
            'label'        => 'Target viewer',
            'type'         => 'textarea',
            'help'         => 'Who is watching and what problem does this video solve for them today?',
            'placeholder'  => 'Who are they? What do they want by the end of the video?',
            'sample_value' => 'New cooks who want dinner on the table in 30 minutes without a pile of dishes. They can follow a simple recipe but feel overwhelmed by multi-pot meals.',
        ],
        [
            'field_key'    => 'video_length',
            'label'        => 'Target length',
            'type'         => 'select',
            'help'         => 'Sets pacing expectations for the outline and retention beats.',
            'options'      => ['Under 8 min', '8-15 min', '15+ min'],
            'sample_value' => 'Under 8 min',
        ],
        [
            'field_key'    => 'filming_setup',
            'label'        => 'Filming setup',
            'type'         => 'select',
            'help'         => 'What you actually have available. The shot list Recipe respects this, not an ideal studio.',
            'options'      => ['Phone on a tripod', 'Overhead phone rig', 'DSLR or mirrorless', 'Webcam at desk', 'Screen recording only'],
            'sample_value' => 'Phone on a tripod',
        ],
        [
            'field_key'    => 'publish_day',
            'label'        => 'Planned publish day',
            'type'         => 'text',
            'help'         => 'When you plan to upload. The checklist Recipe uses this for prep timing.',
            'placeholder'  => 'e.g. Tuesday',
            'sample_value' => 'Tuesday',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'research-brief',
            'title'            => 'Research Brief',
            'summary'          => 'Capture search intent, viewer expectations, and the angle that makes this video yours.',
            'why_it_matters'   => 'Recording without research is how you remake a video five bigger channels already nailed. Ten minutes here tells you what to promise, what to skip, and what would make a stranger click.',
            'unlocks_text'     => 'Approve it and the Audience Angle Recipe starts from your grounded topic, not a guess.',
            'est_minutes'      => 5,
            'prompt_template'  => null,
            'example_response' => $researchBriefExample,
            'checks' => [
                ['label' => 'Search intent is specific', 'help' => 'You can name what someone typed into YouTube before they found a video like this.'],
                ['label' => 'Risks are honest', 'help' => 'At least one real downside or competitor overlap is named, not hand-waved.'],
                ['label' => 'Angle is ownable', 'help' => 'The recommended angle could not belong to any channel in any niche.'],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'audience-angle',
            'title'            => 'Audience Angle',
            'summary'          => 'Define who this video is for and the one promise that earns their click.',
            'why_it_matters'   => '"Everyone who likes cooking" is not an audience. This Recipe turns your target viewer into a single sentence you can read before every outline decision.',
            'unlocks_text'     => 'Approve it and Competitor Video Notes will compare channels against your angle, not generic trends.',
            'est_minutes'      => 4,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'One clear viewer', 'help' => 'You can picture one person and what they need tonight, not a demographic list.'],
                ['label' => 'Promise matches length', 'help' => 'What you promise fits the target length you set in the Pantry.'],
                ['label' => 'Fits your niche', 'help' => 'A subscriber of your channel would recognize this as yours, not a trend chase.'],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'competitor-video-notes',
            'title'            => 'Competitor Video Notes',
            'summary'          => 'Study three similar videos and note what to match, skip, or beat.',
            'why_it_matters'   => 'Competitors already paid for the click test. This Recipe extracts their hooks, gaps, and comment complaints so you do not learn those lessons on your own upload.',
            'unlocks_text'     => 'Approve it and Hook Options will deliberately differ from what you documented here.',
            'est_minutes'      => 5,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Three real references', 'help' => 'Each note ties to a specific video, not a vague genre.'],
                ['label' => 'Gaps are actionable', 'help' => 'At least one competitor weakness becomes something your video will do differently.'],
                ['label' => 'No copying hooks', 'help' => 'What you borrow is structure or proof, not their opening line word for word.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'hook-options',
            'title'            => 'Hook Options',
            'summary'          => 'Write three opening hooks that earn the first 30 seconds.',
            'why_it_matters'   => 'Most drop-off happens before the intro animation finishes. Three hooks give you options to read aloud and pick the one that sounds like you, not like a template.',
            'unlocks_text'     => 'Approve one hook and the Video Outline Recipe builds the rest of the video around it.',
            'est_minutes'      => 4,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'First line earns attention', 'help' => 'Each hook opens with tension, a result, or a relatable problem, not "hey guys".'],
                ['label' => 'Distinct from competitors', 'help' => 'None of the three repeat an opening you flagged in competitor notes.'],
                ['label' => 'Readable aloud', 'help' => 'You would say at least one hook on camera without sounding like you are reading ad copy.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'video-outline',
            'title'            => 'Video Outline',
            'summary'          => 'Map the full video beat by beat from hook to outro.',
            'why_it_matters'   => 'An outline is not a script. It is the spine that stops you from rambling mid-recipe or forgetting the payoff. This Recipe gives you sections you can film out of order if you need to.',
            'unlocks_text'     => 'Approve it and Retention Beats will mark where viewers are most likely to leave.',
            'est_minutes'      => 5,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Payoff is explicit', 'help' => 'The viewer knows what they will have or know by the final frame.'],
                ['label' => 'Beats match target length', 'help' => 'Section count and depth fit the length you chose in the Pantry.'],
                ['label' => 'Hook leads the outline', 'help' => 'The approved hook is reflected in the first two beats, not bolted on after.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'retention-beats',
            'title'            => 'Retention Beats',
            'summary'          => 'Mark the moments that re-hook attention before viewers drift away.',
            'why_it_matters'   => 'YouTube rewards watch time, not good intentions. This Recipe flags three to five spots where a pattern break, preview, or mini-payoff keeps casual viewers from clicking away.',
            'unlocks_text'     => 'Approve it and the B-Roll Shot List Recipe will cover the visual changes at those beats.',
            'est_minutes'      => 4,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Drop-off points named', 'help' => 'Each beat names where attention usually fades in this type of video.'],
                ['label' => 'Fixes are concrete', 'help' => 'Every retention beat suggests a specific visual or line, not "make it engaging".'],
                ['label' => 'Spread across the runtime', 'help' => 'Beats appear in the middle and late sections, not only the opening.'],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'broll-shot-list',
            'title'            => 'B-Roll Shot List',
            'summary'          => 'List the shots to capture so editing has options beyond your talking head.',
            'why_it_matters'   => 'B-roll is what makes a cooking video feel professional on a phone budget. This Recipe gives you a checklist sized to your filming setup, not a cinema shot list you will never finish.',
            'unlocks_text'     => 'Approve it and Title Concepts will describe the video the shots actually support.',
            'est_minutes'      => 5,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Matches your setup', 'help' => 'Every shot is achievable with the filming setup you listed in the Pantry.'],
                ['label' => 'Covers retention beats', 'help' => 'At least one shot is tied to each retention beat from the prior Recipe.'],
                ['label' => 'Enough for editing', 'help' => 'You have wide, close, and action shots, not ten identical angles.'],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'title-concepts',
            'title'            => 'Title Concepts',
            'summary'          => 'Draft five title options that are clear, clickable, and honest.',
            'why_it_matters'   => 'Titles do the click test in search and suggested feeds. Five options let you pick the one that promises what the video actually delivers, which protects retention after the click.',
            'unlocks_text'     => 'Approve a title direction and Thumbnail Directions will align the visual with the words.',
            'est_minutes'      => 4,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Promise matches content', 'help' => 'No title claims a result the outline does not deliver.'],
                ['label' => 'Readable on mobile', 'help' => 'Each title is short enough to scan in a suggested feed without truncating the hook.'],
                ['label' => 'Five distinct angles', 'help' => 'Options explore different hooks (speed, ease, outcome), not the same words rearranged.'],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'thumbnail-directions',
            'title'            => 'Thumbnail Directions',
            'summary'          => 'Describe three thumbnail concepts that match your title and stand out at small size.',
            'why_it_matters'   => 'Thumbnails are half the click. This Recipe gives you composition notes, text limits, and expression cues you can hand to Canva or Photoshop without guessing.',
            'unlocks_text'     => 'Approve a direction and the Publishing Checklist covers everything left before upload day.',
            'est_minutes'      => 4,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Readable at small size', 'help' => 'Each concept uses at most three words of overlay text, or none.'],
                ['label' => 'Aligned with title', 'help' => 'The thumbnail and title promise the same outcome, not two different videos.'],
                ['label' => 'Distinct from competitors', 'help' => 'Color or composition deliberately differs from the competitor thumbnails you noted.'],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'publishing-checklist',
            'title'            => 'Publishing Checklist',
            'summary'          => 'A final pass for description, tags, cards, and upload-day timing.',
            'why_it_matters'   => 'Publish day errors are silent: wrong end screen, empty description, forgot to schedule. This Recipe is the boring list that prevents the embarrassing fix-it upload an hour later.',
            'unlocks_text'     => 'Approve it and your Project Kit export bundles every artifact from this Cookbook.',
            'est_minutes'      => 5,
            'prompt_template'  => null,
            'example_response' => null,
            'checks' => [
                ['label' => 'Timed to publish day', 'help' => 'Prep steps reference the day you set in the Pantry, with realistic lead time.'],
                ['label' => 'Description is useful', 'help' => 'The description draft includes timestamps, links, and one sentence a searcher would care about.'],
                ['label' => 'Nothing critical missing', 'help' => 'End screens, cards, playlist, and visibility setting each have a checkbox item.'],
            ],
        ],
    ],
];
