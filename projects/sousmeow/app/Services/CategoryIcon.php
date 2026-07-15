<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * Category directory marks. icon_key is an allowlisted semantic key stored
 * on sousmeow_categories; unknown or empty keys fall back to the notebook mark.
 */
final class CategoryIcon
{
    public const FALLBACK = 'notebook';

    /**
     * Inline SVG for a category mark. CurrentColor so accent CSS colors it.
     */
    public static function svg(?string $iconKey): string
    {
        $key = self::isValid((string) $iconKey) ? (string) $iconKey : self::FALLBACK;
        return match ($key) {
            'ledger' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 8h5M8 12h8M8 16h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M15.5 7.5 17 9l2.5-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'notebook' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
            default => self::svg(self::FALLBACK),
        };
    }

    public static function isValid(string $key): bool
    {
        return in_array($key, ['notebook', 'ledger'], true);
    }
}
