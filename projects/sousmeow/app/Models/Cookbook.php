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

    /**
     * Marketplace listing with recipe counts, optionally filtered by a
     * search query across title, tagline, and category.
     *
     * @return list<array<string, mixed>>
     */
    public static function marketplace(string $query = ''): array
    {
        $sql = 'SELECT c.*, (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count
                FROM cookbooks c';
        $params = [];
        $query = trim($query);
        if ($query !== '') {
            $sql .= ' WHERE (c.title LIKE ? OR c.tagline LIKE ? OR c.category LIKE ?)';
            $like = '%' . $query . '%';
            $params = [$like, $like, $like];
        }
        $sql .= ' ORDER BY c.sort_order, c.title';
        return Database::fetchAll($sql, $params);
    }

    /** @return array<string, mixed>|null The single executable sample cookbook. */
    public static function featured(): ?array
    {
        return Database::fetch(
            'SELECT c.*, (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count
             FROM cookbooks c WHERE c.is_executable = 1 ORDER BY c.sort_order LIMIT 1'
        );
    }
}
