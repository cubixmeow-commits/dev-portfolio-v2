<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * The accent allowlist and the single presentation-layer mapping from an
 * approved accent key to its CSS class.
 *
 * `accent` is always stored as one of these semantic keys, never a hex
 * value or raw CSS. Seed content is validated against KEYS at sync time
 * (an unknown key is a fatal seed error), and every template that needs
 * an accent goes through cssClass() so the key-to-token mapping lives in
 * exactly one place. The matching CSS custom properties live in
 * tokens.css; the .accent-* classes live in components.css.
 */
final class Accent
{
    /** Approved semantic accent keys. The only values `accent` may hold. */
    public const KEYS = [
        'terracotta', 'amber', 'sage', 'teal', 'lilac', 'clay',
        'indigo', 'slate', 'pine', 'ochre', 'plum', 'moss',
    ];

    public const FALLBACK = 'terracotta';

    public static function isValid(string $key): bool
    {
        return in_array($key, self::KEYS, true);
    }

    /**
     * Map an accent key to its CSS class. Unknown keys fall back to the
     * default rather than emitting an unstyled or caller-controlled class.
     */
    public static function cssClass(string $key): string
    {
        return 'accent-' . (self::isValid($key) ? $key : self::FALLBACK);
    }
}
