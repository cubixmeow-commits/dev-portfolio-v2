<?php

declare(strict_types=1);

namespace Rally\Models;

use Rally\Core\Database;

final class DataSource
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM rly_data_sources WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM rly_data_sources WHERE slug = ?', [$slug]);
    }

    /** @return list<array<string, mixed>> */
    public static function active(): array
    {
        return Database::fetchAll(
            'SELECT * FROM rly_data_sources WHERE is_active = 1 ORDER BY name'
        );
    }
}
