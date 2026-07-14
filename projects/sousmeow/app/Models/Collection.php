<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

/**
 * A discovery Collection. Metadata only; membership resolution (editorial,
 * dynamic, attribute) lives in Services\CollectionResolver, and honesty
 * gating (min_display_count) is applied there too.
 */
final class Collection
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM sousmeow_collections WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM sousmeow_collections WHERE slug = ?', [$slug]);
    }

    /**
     * Visible collections in deterministic order. Not yet filtered by
     * min_display_count; callers pass these through CollectionResolver to
     * decide which actually surface.
     *
     * @return list<array<string, mixed>>
     */
    public static function allVisible(): array
    {
        return Database::fetchAll(
            'SELECT * FROM sousmeow_collections WHERE is_visible = 1 ORDER BY sort_order, name'
        );
    }
}
