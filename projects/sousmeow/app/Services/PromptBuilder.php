<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Models\Artifact;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;

/**
 * Turns a Recipe's prompt template into a ready-to-run prompt by
 * substituting {{field_key}} placeholders with the project's Pantry
 * values and {{artifact:recipe-slug}} with earlier approved Artifacts.
 *
 * build() returns the plain text the user copies. buildHtml() returns
 * an escaped HTML rendering where every substituted ingredient is
 * wrapped in a highlight span, so the Runner can show exactly which
 * information was used. Both walk the same substitution map, so what
 * you see highlighted is exactly what gets copied.
 */
final class PromptBuilder
{
    /**
     * @param array<string, mixed> $recipe
     * @param array<string, mixed> $project
     * @return array{text: string, html: string, missing: list<string>}
     */
    public static function build(array $recipe, array $project): array
    {
        $template = (string) ($recipe['prompt_template'] ?? '');
        $substitutions = self::substitutions($project);

        $missing = [];
        $text = preg_replace_callback(
            '/\{\{([a-z0-9_:\-]+)\}\}/i',
            static function (array $m) use ($substitutions, &$missing): string {
                $key = strtolower($m[1]);
                if (array_key_exists($key, $substitutions) && $substitutions[$key] !== '') {
                    return $substitutions[$key];
                }
                $missing[] = $key;
                return '[missing: ' . $key . ']';
            },
            $template
        ) ?? $template;

        $html = preg_replace_callback(
            '/\{\{([a-z0-9_:\-]+)\}\}/i',
            static function (array $m) use ($substitutions): string {
                $key = strtolower($m[1]);
                $value = $substitutions[$key] ?? '';
                if ($value === '') {
                    return '<span class="prompt-ingredient">[missing: ' . e($key) . ']</span>';
                }
                return '<span class="prompt-ingredient">' . e($value) . '</span>';
            },
            e($template)
        ) ?? e($template);

        return ['text' => $text, 'html' => $html, 'missing' => array_values(array_unique($missing))];
    }

    /**
     * The full substitution map for a project: pantry field keys plus
     * artifact:slug entries for every approved artifact.
     *
     * @param array<string, mixed> $project
     * @return array<string, string>
     */
    private static function substitutions(array $project): array
    {
        $projectId = (int) $project['id'];
        $cookbookId = (int) $project['cookbook_id'];

        $map = [];
        $values = Project::pantryValues($projectId);
        foreach (PantryField::forCookbook($cookbookId) as $field) {
            $raw = trim($values[(int) $field['id']] ?? '');
            $map[strtolower((string) $field['field_key'])] = self::presentValue($field, $raw);
        }

        foreach (Artifact::approvedByRecipe($projectId) as $artifact) {
            $map['artifact:' . strtolower((string) $artifact['recipe_slug'])] = (string) $artifact['content'];
        }

        return $map;
    }

    /**
     * Present a stored value for prompt use. Multiselects are stored as
     * JSON arrays and join into a readable list; everything else passes
     * through as entered.
     *
     * @param array<string, mixed> $field
     */
    private static function presentValue(array $field, string $raw): string
    {
        if ($raw === '') {
            return '';
        }
        if ($field['type'] === 'multiselect') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return implode(', ', array_map('strval', $decoded));
            }
        }
        return $raw;
    }
}
