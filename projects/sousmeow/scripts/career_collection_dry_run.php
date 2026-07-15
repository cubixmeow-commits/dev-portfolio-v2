<?php

declare(strict_types=1);

/**
 * Fixture dry-run for Career Collection Cookbooks.
 *
 * Interpolates Pantry sample_value fields and earlier example_response
 * artifacts into each Recipe prompt. Reports unresolved placeholders,
 * missing teaching fields, and contract/check mismatches.
 *
 * Usage: php scripts/career_collection_dry_run.php
 */

require_once __DIR__ . '/../database/seeds/career_helpers.php';
require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Services\OutputContract;

$slugs = sm_career_collection_slugs();
$issues = [];
$matrix = [];

foreach ($slugs as $slug) {
    $path = __DIR__ . '/../database/seeds/cookbooks/' . $slug . '.php';
    if (!is_file($path)) {
        $issues[] = "{$slug}: seed file missing";
        continue;
    }
    /** @var array<string, mixed> $book */
    $book = require $path;

    $pantry = [];
    foreach ($book['fields'] as $field) {
        $pantry[(string) $field['field_key']] = (string) ($field['sample_value'] ?? '');
    }

    $artifacts = [];
    $row = [
        'cookbook' => (string) $book['title'],
        'slug' => $slug,
        'pantry_fixture' => implode(', ', array_keys($pantry)),
        'recipes_tested' => [],
        'artifacts_generated' => [],
        'interpolation_issues' => [],
        'ux_issues' => [],
        'factual_integrity_issues' => [],
        'status' => 'pass',
    ];

    $shapeCounts = [];
    foreach ($book['recipes'] as $recipe) {
        $sp = (int) ($recipe['stage_position'] ?? 0);
        $shapeCounts[$sp] = ($shapeCounts[$sp] ?? 0) + 1;
    }
    ksort($shapeCounts);
    $row['stage_shape'] = implode('-', $shapeCounts);

    foreach ($book['recipes'] as $recipe) {
        $rslug = (string) $recipe['slug'];
        $row['recipes_tested'][] = $rslug;

        foreach (['before_you_begin', 'common_problems', 'recovery_guidance', 'why_it_matters', 'summary'] as $teach) {
            if (trim((string) ($recipe[$teach] ?? '')) === '') {
                $row['ux_issues'][] = "{$rslug}: empty {$teach}";
            }
        }

        $template = (string) ($recipe['prompt_template'] ?? '');
        if ($template === '') {
            $row['interpolation_issues'][] = "{$rslug}: empty prompt";
            continue;
        }

        if (!str_contains($template, 'Do not invent') && !str_contains($template, SM_CAREER_FACT_RULE)) {
            // Fact rule is interpolated via {$factRule} which expands SM_CAREER_FACT_RULE
            if (!str_contains($template, 'Fact rule:') && !str_contains($template, 'invent')) {
                $row['factual_integrity_issues'][] = "{$rslug}: prompt may lack invent-nothing guidance";
            }
        }

        $banned = ['75 percent', '75%', 'deserve a salary', 'you deserve'];
        $hay = strtolower($template . ' ' . ($recipe['example_response'] ?? ''));
        foreach ($banned as $b) {
            if (str_contains($hay, strtolower($b))) {
                $row['factual_integrity_issues'][] = "{$rslug}: banned claim fragment '{$b}'";
            }
        }

        $built = preg_replace_callback(
            '/\{\{([a-z0-9_:\-]+)\}\}/i',
            static function (array $m) use ($pantry, $artifacts, $rslug, &$row): string {
                $key = strtolower($m[1]);
                if (str_starts_with($key, 'artifact:')) {
                    $aslug = substr($key, 9);
                    if (!isset($artifacts[$aslug]) || $artifacts[$aslug] === '') {
                        $row['interpolation_issues'][] = "{$rslug}: missing prior artifact {$aslug}";
                        return '[missing: ' . $key . ']';
                    }
                    return $artifacts[$aslug];
                }
                if (!array_key_exists($key, $pantry) || $pantry[$key] === '') {
                    $row['interpolation_issues'][] = "{$rslug}: unresolved pantry {{$key}}";
                    return '[missing: ' . $key . ']';
                }
                return $pantry[$key];
            },
            $template
        ) ?? $template;

        if (str_contains($built, '[missing:')) {
            // Already recorded
        }
        if (preg_match('/\{\{[a-z0-9_:\-]+\}\}/i', $built)) {
            $row['interpolation_issues'][] = "{$rslug}: leftover placeholder after interpolation";
        }

        $contractErrors = OutputContract::validate(
            $recipe['output_sections'] ?? null,
            $recipe['checks'] ?? []
        );
        foreach ($contractErrors as $err) {
            $row['interpolation_issues'][] = "{$rslug}: contract {$err}";
        }

        $example = (string) ($recipe['example_response'] ?? '');
        if ($example === '') {
            $row['ux_issues'][] = "{$rslug}: missing example_response";
        } else {
            foreach (($recipe['output_sections'] ?? []) as $section) {
                $heading = (string) ($section['heading'] ?? '');
                if ($heading !== '' && !str_contains($example, '## ' . $heading) && !str_contains($example, '# ' . $heading)) {
                    // Soft warning: heading may use slight variation
                    $row['ux_issues'][] = "{$rslug}: example may miss heading '{$heading}'";
                }
            }
            $artifacts[$rslug] = $example;
            $row['artifacts_generated'][] = $rslug;
        }
    }

    if (
        $row['interpolation_issues'] !== []
        || $row['factual_integrity_issues'] !== []
        || count(array_filter($row['ux_issues'], static fn(string $u): bool => str_contains($u, 'empty '))) > 0
    ) {
        $row['status'] = 'fail';
        $issues[] = $slug;
    }

    $matrix[] = $row;
}

$reportPath = __DIR__ . '/../research/CAREER_COLLECTION_QA.md';
$lines = [];
$lines[] = '# Career Collection QA';
$lines[] = '';
$lines[] = 'Fixture dry-run generated by `scripts/career_collection_dry_run.php`.';
$lines[] = '';
$lines[] = 'Date: ' . gmdate('Y-m-d');
$lines[] = '';

foreach ($matrix as $row) {
    $lines[] = '## ' . $row['cookbook'];
    $lines[] = '';
    $lines[] = '- **Slug:** `' . $row['slug'] . '`';
    $lines[] = '- **Stage shape:** `' . ($row['stage_shape'] ?? '') . '`';
    $lines[] = '- **Pantry fixture keys:** ' . $row['pantry_fixture'];
    $lines[] = '- **Recipes tested:** ' . implode(', ', $row['recipes_tested']);
    $lines[] = '- **Artifacts generated (via sample responses):** ' . implode(', ', $row['artifacts_generated']);
    $lines[] = '- **Interpolation issues:** ' . ($row['interpolation_issues'] === [] ? 'none' : implode('; ', $row['interpolation_issues']));
    $lines[] = '- **UX issues:** ' . ($row['ux_issues'] === [] ? 'none' : implode('; ', $row['ux_issues']));
    $lines[] = '- **Factual-integrity issues:** ' . ($row['factual_integrity_issues'] === [] ? 'none' : implode('; ', $row['factual_integrity_issues']));
    $lines[] = '- **Final status:** **' . strtoupper($row['status']) . '**';
    $lines[] = '';
}

$lines[] = '## Seed status';
$lines[] = '';
$lines[] = 'Run `php scripts/seed.php --status` separately. Catalog health must report OK with 27 executable + 2 preview.';
$lines[] = '';

file_put_contents($reportPath, implode("\n", $lines));

echo implode("\n", array_map(
    static fn(array $r): string => sprintf(
        '[%s] %s (%s) issues=%d',
        strtoupper($r['status']),
        $r['slug'],
        $r['stage_shape'] ?? '',
        count($r['interpolation_issues']) + count($r['factual_integrity_issues'])
    ),
    $matrix
)) . "\n";
echo "Wrote {$reportPath}\n";

exit($issues === [] ? 0 : 1);
