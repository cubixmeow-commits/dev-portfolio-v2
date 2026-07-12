<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class CookbookStage
{
    /** @return list<array<string, mixed>> */
    public static function forCookbook(int $cookbookId): array
    {
        return Database::fetchAll(
            'SELECT * FROM cookbook_stages WHERE cookbook_id = ? ORDER BY position',
            [$cookbookId]
        );
    }
}
