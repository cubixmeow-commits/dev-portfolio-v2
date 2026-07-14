<?php

declare(strict_types=1);

/**
 * First-party category definitions: the stable primary taxonomy every
 * publicly visible Cookbook belongs to. Slugs are the canonical vocabulary
 * the Cookbook pipeline references in `primary_category`; they never change
 * casually. `accent` is an allowlisted key (Services\Accent), never a hex.
 * `outcomes` holds exactly three, stored as JSON. sort_order follows array
 * order. Voice: plain, workshop, no hype.
 */

return [
    [
        'slug'        => 'start-grow-business',
        'name'        => 'Start & Grow a Business',
        'short_name'  => 'Business',
        'tagline'     => 'Shape a rough idea into something real.',
        'description' => 'Test whether an idea holds up before you build it. These Cookbooks help you check demand, learn who you are serving, and price the work so it can support itself.',
        'outcomes'    => ['Validate an idea', 'Understand a customer', 'Plan pricing'],
        'accent'      => 'sage',
    ],
    [
        'slug'        => 'marketing-growth',
        'name'        => 'Marketing & Growth',
        'short_name'  => 'Marketing',
        'tagline'     => 'Turn a real offer into attention.',
        'description' => 'Put a finished offer in front of the people who want it. Work through positioning, launch plans, and campaigns that say plainly what the product does and who it is for.',
        'outcomes'    => ['Position a product', 'Plan a launch', 'Build a campaign'],
        'accent'      => 'terracotta',
    ],
    [
        'slug'        => 'software-product',
        'name'        => 'Software & Product',
        'short_name'  => 'Software',
        'tagline'     => 'Plan and ship what you build.',
        'description' => 'Move a feature from idea to release with the thinking written down. These Cookbooks cover specs, system design, and the steps that make a launch calm instead of frantic.',
        'outcomes'    => ['Write a spec', 'Design a system', 'Prepare a release'],
        'accent'      => 'teal',
    ],
    [
        'slug'        => 'content-audience',
        'name'        => 'Content & Audience',
        'short_name'  => 'Content',
        'tagline'     => 'Build something worth following.',
        'description' => 'Make content on purpose rather than in bursts. Plan individual pieces and the system that keeps an audience growing between them.',
        'outcomes'    => ['Plan a video', 'Grow a newsletter', 'Build a content system'],
        'accent'      => 'amber',
    ],
    [
        'slug'        => 'writing-publishing',
        'name'        => 'Writing & Publishing',
        'short_name'  => 'Writing',
        'tagline'     => 'Move from draft to finished work.',
        'description' => 'Get words into shape and out the door. From a single article to a full manuscript, these Cookbooks help you draft, structure, and edit with a clear next step.',
        'outcomes'    => ['Draft an article', 'Structure a book', 'Edit a manuscript'],
        'accent'      => 'clay',
    ],
    [
        'slug'        => 'design-brand',
        'name'        => 'Design & Brand',
        'short_name'  => 'Design',
        'tagline'     => 'Give the work a coherent look.',
        'description' => 'Decide how the work should look and feel before you commit to it. Build a brand foundation, plan an interface, and hold a design up to honest critique.',
        'outcomes'    => ['Build a brand foundation', 'Plan an interface', 'Critique a design'],
        'accent'      => 'lilac',
    ],
    [
        'slug'        => 'career-freelance',
        'name'        => 'Career & Freelance',
        'short_name'  => 'Career',
        'tagline'     => 'Prepare for the next move.',
        'description' => 'Get ready for the thing that comes next, whether that is a role or a client. Assemble the materials, rehearse the conversation, and make the case for your work.',
        'outcomes'    => ['Build a resume', 'Prepare for an interview', 'Win a client'],
        'accent'      => 'indigo',
    ],
    [
        'slug'        => 'research-insights',
        'name'        => 'Research & Insights',
        'short_name'  => 'Research',
        'tagline'     => 'Make sense of the evidence.',
        'description' => 'Turn scattered inputs into a conclusion you can act on. Analyze a market, read back a stack of interviews, and see how the competition really compares.',
        'outcomes'    => ['Analyze a market', 'Review interviews', 'Compare competitors'],
        'accent'      => 'slate',
    ],
    [
        'slug'        => 'learning-teaching',
        'name'        => 'Learning & Teaching',
        'short_name'  => 'Learning',
        'tagline'     => 'Turn a subject into a system.',
        'description' => 'Organize a subject so it can be learned or taught. Build a study plan for yourself, or design a course and lessons for other people.',
        'outcomes'    => ['Build a study plan', 'Design a course', 'Plan a lesson'],
        'accent'      => 'pine',
    ],
    [
        'slug'        => 'planning-productivity',
        'name'        => 'Planning & Productivity',
        'short_name'  => 'Planning',
        'tagline'     => 'Bring order to the work.',
        'description' => 'Sort out what to do and in what order. These Cookbooks help you make a hard decision, organize a project, and write down a process so it can run without you.',
        'outcomes'    => ['Make a decision', 'Organize a project', 'Document a process'],
        'accent'      => 'ochre',
    ],
    [
        'slug'        => 'creative-worlds',
        'name'        => 'Creative Worlds',
        'short_name'  => 'Creative',
        'tagline'     => 'Build a world worth returning to.',
        'description' => 'Plan the shape of a story or a game before you build it. Work out the world, its rules, and the arc that keeps people coming back.',
        'outcomes'    => ['Plan a story', 'Build a world', 'Design a game'],
        'accent'      => 'plum',
    ],
    [
        'slug'        => 'personal-projects',
        'name'        => 'Personal Projects',
        'short_name'  => 'Personal',
        'tagline'     => 'Finish something that matters to you.',
        'description' => 'Give a personal project the same structure you would give work. Set a goal, make the decisions it needs, and carry it through to done.',
        'outcomes'    => ['Plan a goal', 'Organize a decision', 'Finish a project'],
        'accent'      => 'moss',
    ],
];
