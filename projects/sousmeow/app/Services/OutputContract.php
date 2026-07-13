<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * The output contract a Recipe may declare: an ordered list of Markdown
 * sections the prompt asks the AI to produce. Stored as JSON in
 * recipes.output_contract; each Quality Check may reference section keys
 * via recipe_checks.evidence_keys. This class owns the canonical shape,
 * encoding/decoding, and validation. Contracts are validated at seed
 * time so a malformed contract can never surface as a runtime surprise.
 *
 * A contract describes structure only. It never grades content; the
 * user remains the reviewer of every Quality Check.
 */
final class OutputContract
{
    /**
     * Canonicalize a seed-defined section list for storage. Returns null
     * for "no contract" so the column stays NULL and old behavior applies.
     *
     * @param list<array<string, mixed>>|null $sections
     */
    public static function encode(?array $sections): ?string
    {
        if ($sections === null || $sections === []) {
            return null;
        }
        $canonical = [];
        foreach ($sections as $section) {
            $canonical[] = [
                'key'      => (string) ($section['key'] ?? ''),
                'heading'  => (string) ($section['heading'] ?? ''),
                'aliases'  => array_values(array_map('strval', (array) ($section['aliases'] ?? []))),
                'required' => (bool) ($section['required'] ?? true),
            ];
        }
        return json_encode($canonical, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: null;
    }

    /**
     * Decode a stored contract. Returns null when the recipe has no
     * contract or the stored value is unusable (callers then fall back
     * to full-response review, never an error page).
     *
     * @return list<array{key: string, heading: string, aliases: list<string>, required: bool}>|null
     */
    public static function decode(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return null;
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded) || $decoded === []) {
            return null;
        }
        $sections = [];
        foreach ($decoded as $section) {
            if (!is_array($section) || !isset($section['key'], $section['heading'])) {
                return null;
            }
            $sections[] = [
                'key'      => (string) $section['key'],
                'heading'  => (string) $section['heading'],
                'aliases'  => array_values(array_map('strval', (array) ($section['aliases'] ?? []))),
                'required' => (bool) ($section['required'] ?? true),
            ];
        }
        return $sections;
    }

    /**
     * Normalize a heading for matching: trim, drop surrounding emphasis
     * markers, drop trailing punctuation, collapse whitespace, casefold.
     * Both contract headings and response headings pass through here, so
     * "## **Hero:**" matches the declared heading "Hero".
     */
    public static function normalizeHeading(string $heading): string
    {
        $h = trim($heading);
        $h = trim($h, "*_`");
        $h = (string) preg_replace('/[\s\p{Z}]+/u', ' ', $h);
        $h = rtrim($h, " \t.:;!?");
        return mb_strtolower(trim($h));
    }

    /**
     * Validate one Recipe's contract together with its checks' evidence
     * references. Returns human-readable problems; empty means valid.
     * A recipe with no contract is valid, and a check with no evidence
     * mapping is valid — but evidence keys must always point at declared
     * section keys.
     *
     * @param list<array<string, mixed>>|null $sections seed or decoded contract
     * @param list<array<string, mixed>>      $checks   each may carry 'evidence_sections'
     * @return list<string>
     */
    public static function validate(?array $sections, array $checks): array
    {
        $errors = [];
        $declaredKeys = [];
        $seenHeadings = [];

        foreach ($sections ?? [] as $i => $section) {
            $label = 'section #' . ($i + 1);
            $key = (string) ($section['key'] ?? '');
            if ($key === '' || preg_match('/^[a-z0-9_]+$/', $key) !== 1) {
                $errors[] = "{$label}: key '{$key}' must be non-empty lowercase [a-z0-9_]";
            } elseif (isset($declaredKeys[$key])) {
                $errors[] = "{$label}: duplicate section key '{$key}'";
            }
            $declaredKeys[$key] = true;

            $heading = (string) ($section['heading'] ?? '');
            $names = array_merge([$heading], array_map('strval', (array) ($section['aliases'] ?? [])));
            foreach ($names as $n => $name) {
                $kind = $n === 0 ? 'heading' : 'alias';
                if (trim($name) === '') {
                    $errors[] = "{$label}: {$kind} must not be empty";
                    continue;
                }
                $normalized = self::normalizeHeading($name);
                if ($normalized === '') {
                    $errors[] = "{$label}: {$kind} '{$name}' normalizes to nothing";
                } elseif (isset($seenHeadings[$normalized])) {
                    $errors[] = "{$label}: {$kind} '{$name}' collides with {$seenHeadings[$normalized]}";
                } else {
                    $seenHeadings[$normalized] = "{$kind} '{$name}' of section '{$key}'";
                }
            }

            if (isset($section['required']) && !is_bool($section['required'])) {
                $errors[] = "{$label}: 'required' must be a boolean";
            }
        }

        foreach ($checks as $i => $check) {
            $refs = $check['evidence_sections'] ?? null;
            if ($refs === null) {
                continue;
            }
            $label = "check #" . ($i + 1) . " ('" . (string) ($check['label'] ?? '?') . "')";
            if (!is_array($refs)) {
                $errors[] = "{$label}: evidence_sections must be a list of section keys";
                continue;
            }
            if (($sections ?? []) === [] && $refs !== []) {
                $errors[] = "{$label}: references sections but the recipe declares no output contract";
                continue;
            }
            $seenRefs = [];
            foreach ($refs as $ref) {
                $ref = (string) $ref;
                if (!isset($declaredKeys[$ref])) {
                    $errors[] = "{$label}: references undeclared section key '{$ref}'";
                }
                if (isset($seenRefs[$ref])) {
                    $errors[] = "{$label}: duplicate reference to section key '{$ref}'";
                }
                $seenRefs[$ref] = true;
            }
        }

        return $errors;
    }

    /**
     * Decode a check's stored evidence keys. Empty means "no structural
     * mapping — manual review of the full response".
     *
     * @return list<string>
     */
    public static function evidenceKeys(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        return array_values(array_map('strval', array_filter($decoded, 'is_scalar')));
    }
}
