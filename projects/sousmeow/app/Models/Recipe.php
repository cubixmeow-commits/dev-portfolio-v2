<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;
use SousMeow\Services\OutputContract;

final class Recipe
{
    /** @return list<array<string, mixed>> */
    public static function forCookbook(int $cookbookId): array
    {
        return Database::fetchAll(
            'SELECT * FROM recipes WHERE cookbook_id = ? ORDER BY position',
            [$cookbookId]
        );
    }

    /** @return array<string, mixed>|null */
    public static function findByPosition(int $cookbookId, int $position): ?array
    {
        return Database::fetch(
            'SELECT * FROM recipes WHERE cookbook_id = ? AND position = ?',
            [$cookbookId, $position]
        );
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(int $cookbookId, string $slug): ?array
    {
        return Database::fetch(
            'SELECT * FROM recipes WHERE cookbook_id = ? AND slug = ?',
            [$cookbookId, $slug]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function checks(int $recipeId): array
    {
        return Database::fetchAll(
            'SELECT * FROM recipe_checks WHERE recipe_id = ? ORDER BY position',
            [$recipeId]
        );
    }

    /**
     * The recipe's decoded output contract, or null when it declares
     * none (the Runner then falls back to full-response review).
     *
     * @param array<string, mixed> $recipe
     * @return list<array{key: string, heading: string, aliases: list<string>, required: bool}>|null
     */
    public static function outputContract(array $recipe): ?array
    {
        return OutputContract::decode($recipe['output_contract'] ?? null);
    }

    /**
     * Evidence-section keys for one check row; empty = manual review.
     *
     * @param array<string, mixed> $check
     * @return list<string>
     */
    public static function evidenceKeys(array $check): array
    {
        return OutputContract::evidenceKeys($check['evidence_keys'] ?? null);
    }

    public static function countForCookbook(int $cookbookId): int
    {
        return (int) Database::fetchValue(
            'SELECT COUNT(*) FROM recipes WHERE cookbook_id = ?',
            [$cookbookId]
        );
    }
}
