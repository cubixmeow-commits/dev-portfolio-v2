<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

/**
 * The stable primary taxonomy. Every publicly visible Cookbook points at
 * exactly one category through cookbooks.primary_category_id.
 */
final class Category
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM sousmeow_categories WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM sousmeow_categories WHERE slug = ?', [$slug]);
    }

    /**
     * Visible categories in deterministic order, each with its live public
     * Cookbook count (all Cookbooks in the catalog are public; previews are
     * displayed alongside executable ones).
     *
     * @return list<array<string, mixed>>
     */
    public static function allVisibleWithCounts(): array
    {
        return Database::fetchAll(
            'SELECT c.*, (SELECT COUNT(*) FROM cookbooks b WHERE b.primary_category_id = c.id) AS cookbook_count
             FROM sousmeow_categories c
             WHERE c.is_visible = 1
             ORDER BY c.sort_order, c.name'
        );
    }

    /** @return array<string, mixed>|null The primary category of one Cookbook. */
    public static function forCookbook(int $cookbookId): ?array
    {
        return Database::fetch(
            'SELECT cat.* FROM sousmeow_categories cat
             JOIN cookbooks b ON b.primary_category_id = cat.id
             WHERE b.id = ?',
            [$cookbookId]
        );
    }

    /**
     * Decode the three presentation outcomes stored as JSON.
     *
     * @param array<string, mixed> $category
     * @return list<string>
     */
    public static function outcomes(array $category): array
    {
        $decoded = json_decode((string) ($category['outcomes_json'] ?? ''), true);
        if (!is_array($decoded)) {
            return [];
        }
        return array_values(array_map('strval', $decoded));
    }
}
