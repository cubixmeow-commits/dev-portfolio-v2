<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;

/**
 * The one place Collection membership is resolved. Controllers never branch
 * on collection_type; they ask this service for a Collection's Cookbooks
 * and whether it should surface.
 *
 * Three resolution strategies:
 *   editorial  explicit join rows in cookbook_collections.
 *   dynamic    computed at query time (recently-added = created_at desc).
 *   attribute  derived from Cookbook fields (deep-workflows = recipe count
 *              >= 6; under-30-minutes = est_minutes <= 30).
 *
 * All strategies read the same public catalog the marketplace shows
 * (executable and preview Cookbooks), except start-here, which is executable
 * only. Every query yields at most one row per Cookbook, so results never
 * duplicate.
 */
final class CollectionResolver
{
    /** Shared projection: Cookbook plus recipe_count and category display fields. */
    private const SELECT =
        'SELECT c.*, cat.name AS category_name, cat.slug AS category_slug, cat.accent AS category_accent,
                (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count
         FROM cookbooks c
         LEFT JOIN categories cat ON cat.id = c.primary_category_id';

    /** Attribute thresholds, named so the rule reads at the call site. */
    private const DEEP_WORKFLOW_RECIPES = 6;
    private const UNDER_30_MINUTES = 30;

    /**
     * The Cookbooks in a Collection, ordered for display.
     *
     * @param array<string, mixed> $collection
     * @return list<array<string, mixed>>
     */
    public static function cookbooksFor(array $collection): array
    {
        $slug = (string) ($collection['slug'] ?? '');
        return match ((string) ($collection['collection_type'] ?? 'editorial')) {
            'dynamic'   => self::dynamic($slug),
            'attribute' => self::attribute($slug),
            default     => self::editorial((int) ($collection['id'] ?? 0), $slug),
        };
    }

    /**
     * Whether a Collection should appear in navigation and strips: visible,
     * and at or above its min_display_count of qualifying Cookbooks.
     *
     * @param array<string, mixed> $collection
     */
    public static function isSurfaced(array $collection): bool
    {
        if ((int) ($collection['is_visible'] ?? 0) !== 1) {
            return false;
        }
        return count(self::cookbooksFor($collection)) >= (int) ($collection['min_display_count'] ?? 1);
    }

    /**
     * Resolve a set of Collections to just those that surface, each paired
     * with its Cookbooks. One resolve per Collection.
     *
     * @param list<array<string, mixed>> $collections
     * @return list<array{collection: array<string, mixed>, cookbooks: list<array<string, mixed>>}>
     */
    public static function surfaced(array $collections): array
    {
        $out = [];
        foreach ($collections as $collection) {
            if ((int) ($collection['is_visible'] ?? 0) !== 1) {
                continue;
            }
            $cookbooks = self::cookbooksFor($collection);
            if (count($cookbooks) >= (int) ($collection['min_display_count'] ?? 1)) {
                $out[] = ['collection' => $collection, 'cookbooks' => $cookbooks];
            }
        }
        return $out;
    }

    /** @return list<array<string, mixed>> */
    private static function editorial(int $collectionId, string $slug): array
    {
        // start-here must contain executable Cookbooks only, enforced here
        // regardless of what curation put in the join table.
        $executableOnly = $slug === 'start-here' ? ' AND c.is_executable = 1' : '';
        return Database::fetchAll(
            self::SELECT . '
             JOIN cookbook_collections cc ON cc.cookbook_id = c.id
             WHERE cc.collection_id = ?' . $executableOnly . '
             ORDER BY cc.position, c.title',
            [$collectionId]
        );
    }

    /** @return list<array<string, mixed>> */
    private static function dynamic(string $slug): array
    {
        return match ($slug) {
            'recently-added' => Database::fetchAll(
                self::SELECT . ' ORDER BY c.created_at DESC, c.id DESC'
            ),
            default => [],
        };
    }

    /** @return list<array<string, mixed>> */
    private static function attribute(string $slug): array
    {
        // Thresholds are inlined (cast to int) rather than bound. A bound
        // parameter carries text affinity, and SQLite ranks every integer
        // below every text value, so `COUNT(*) >= '6'` would be false for
        // all rows. These are internal integer constants, never user input.
        $deep = (int) self::DEEP_WORKFLOW_RECIPES;
        $under = (int) self::UNDER_30_MINUTES;
        return match ($slug) {
            'deep-workflows' => Database::fetchAll(
                self::SELECT . "
                 WHERE (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) >= {$deep}
                 ORDER BY c.sort_order, c.title"
            ),
            'under-30-minutes' => Database::fetchAll(
                self::SELECT . "
                 WHERE c.est_minutes <= {$under}
                 ORDER BY c.sort_order, c.title"
            ),
            // Unknown attribute slug (or one whose signal does not yet exist):
            // resolve to nothing so the Collection stays hidden.
            default => [],
        };
    }
}
