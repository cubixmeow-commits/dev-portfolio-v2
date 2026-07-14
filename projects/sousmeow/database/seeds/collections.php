<?php

declare(strict_types=1);

/**
 * First-party Collection definitions: flexible discovery views over the
 * catalog. `collection_type` tells CollectionResolver how membership
 * resolves:
 *
 *   editorial  explicit membership; Cookbooks list the slug in their seed.
 *   dynamic    computed at query time (recently-added = created_at desc).
 *   attribute  derived from Cookbook fields (deep-workflows = recipes >= 6;
 *              under-30-minutes = est_minutes <= 30).
 *
 * min_display_count is honesty: a Collection is surfaced only once it has
 * at least that many qualifying Cookbooks, so a sparse strip stays hidden
 * rather than lying about the shelf. Only editorial Collections carry join
 * rows. No most-completed / trending Collection ships until real usage data
 * exists. sort_order follows array order. Voice: plain, no hype.
 */

return [
    [
        'slug'              => 'start-here',
        'name'              => 'Start Here',
        'tagline'           => 'Guided Cookbooks that produce something useful in one sitting.',
        'description'       => 'A short shelf of guided Cookbooks you can start now and finish soon, each ending in something you can actually use.',
        'accent'            => 'sage',
        'collection_type'   => 'editorial',
        'min_display_count' => 3,
    ],
    [
        'slug'              => 'selected-by-sousmeow',
        'name'              => 'Selected by SousMeow',
        'tagline'           => 'Cookbooks we think are worth your time.',
        'description'       => 'A small, hand-picked set of the Cookbooks we would point a friend to first.',
        'accent'            => 'amber',
        'collection_type'   => 'editorial',
        'min_display_count' => 3,
    ],
    [
        'slug'              => 'recently-added',
        'name'              => 'Recently Added',
        'tagline'           => 'The newest additions to the library.',
        'description'       => 'The Cookbooks that joined the library most recently, newest first.',
        'accent'            => 'teal',
        'collection_type'   => 'dynamic',
        'min_display_count' => 3,
    ],
    [
        'slug'              => 'launch-something',
        'name'              => 'Launch Something',
        'tagline'           => 'Take a product from ready to released.',
        'description'       => 'Cookbooks for the stretch between a finished product and a real launch.',
        'accent'            => 'terracotta',
        'collection_type'   => 'editorial',
        'min_display_count' => 3,
    ],
    [
        'slug'              => 'under-30-minutes',
        'name'              => 'Under 30 Minutes',
        'tagline'           => 'Complete something before you lose the thread.',
        'description'       => 'Shorter Cookbooks you can finish in one focused sitting.',
        'accent'            => 'ochre',
        'collection_type'   => 'attribute',
        'min_display_count' => 4,
    ],
    [
        'slug'              => 'deep-workflows',
        'name'              => 'Deep Workflows',
        'tagline'           => 'Longer Cookbooks for work that deserves the time.',
        'description'       => 'Longer, multi-stage Cookbooks for work that rewards patience.',
        'accent'            => 'indigo',
        'collection_type'   => 'attribute',
        'min_display_count' => 3,
    ],
];
