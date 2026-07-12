<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class PantryField
{
    public const TYPES = ['text', 'textarea', 'select', 'multiselect', 'number', 'url'];

    /** @return list<array<string, mixed>> */
    public static function forCookbook(int $cookbookId): array
    {
        return Database::fetchAll(
            'SELECT * FROM pantry_fields WHERE cookbook_id = ? ORDER BY position',
            [$cookbookId]
        );
    }

    /** @return list<string> Decoded options for select/multiselect fields. */
    public static function options(array $field): array
    {
        if (empty($field['options'])) {
            return [];
        }
        $decoded = json_decode((string) $field['options'], true);
        return is_array($decoded) ? array_values(array_map('strval', $decoded)) : [];
    }
}
