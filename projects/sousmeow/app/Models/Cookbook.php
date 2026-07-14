<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class Cookbook
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM cookbooks WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM cookbooks WHERE slug = ?', [$slug]);
    }

    /** Shared projection: Cookbook plus recipe_count and category display fields. */
    private const LISTING_SELECT =
        'SELECT c.*, cat.name AS category_name, cat.slug AS category_slug, cat.accent AS category_accent,
                (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count
         FROM cookbooks c
         LEFT JOIN categories cat ON cat.id = c.primary_category_id';

    /**
     * Marketplace listing with recipe counts, optionally filtered by a
     * search query. Search spans Cookbook title and tagline, the primary
     * category name, and editorial Collection names. EXISTS keeps the join
     * from ever duplicating a Cookbook, and the legacy category string is
     * not referenced.
     *
     * @return list<array<string, mixed>>
     */
    public static function marketplace(string $query = ''): array
    {
        $sql = self::LISTING_SELECT;
        $params = [];
        $query = trim($query);
        if ($query !== '') {
            $like = '%' . $query . '%';
            $sql .= '
                WHERE (
                    c.title LIKE ?
                    OR c.tagline LIKE ?
                    OR EXISTS (SELECT 1 FROM categories cx
                               WHERE cx.id = c.primary_category_id AND cx.name LIKE ?)
                    OR EXISTS (SELECT 1 FROM cookbook_collections ccx
                               JOIN collections colx ON colx.id = ccx.collection_id
                               WHERE ccx.cookbook_id = c.id
                                 AND colx.collection_type = ?
                                 AND colx.name LIKE ?)
                )';
            $params = [$like, $like, $like, 'editorial', $like];
        }
        $sql .= ' ORDER BY c.sort_order, c.title';
        return Database::fetchAll($sql, $params);
    }

    /** @return array<string, mixed>|null The single executable sample cookbook. */
    public static function featured(): ?array
    {
        return Database::fetch(
            self::LISTING_SELECT . ' WHERE c.is_executable = 1 ORDER BY c.sort_order LIMIT 1'
        );
    }
}
