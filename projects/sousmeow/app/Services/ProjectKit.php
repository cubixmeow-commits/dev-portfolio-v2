<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Config;
use SousMeow\Models\Artifact;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use ZipArchive;

/**
 * Builds the exported Project Kit: a zip holding one Markdown file per
 * approved Artifact, a self-contained HTML reader, and a README manifest
 * describing the Pantry and the provenance of every file. Zips are written
 * outside the web root and only served through the authorized download route.
 */
final class ProjectKit
{
    /**
     * @param array<string, mixed> $project
     * @param array<string, mixed> $cookbook
     * @return array{filename: string, path: string, size: int, count: int}
     */
    public static function build(array $project, array $cookbook): array
    {
        $projectId = (int) $project['id'];
        $approved = Artifact::approvedByRecipe($projectId);

        $dir = rtrim(Config::string('exports.dir'), '/');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $slug = self::slugify((string) $project['title']);
        $filename = sprintf('sousmeow-kit-%s-%s.zip', $slug !== '' ? $slug : 'project', gmdate('Ymd-His'));
        $path = $dir . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create the export archive.');
        }

        $fileList = [];
        foreach (array_values($approved) as $index => $artifact) {
            $name = sprintf('%02d-%s.md', $index + 1, (string) $artifact['recipe_slug']);
            $fileList[] = ['name' => $name, 'artifact' => $artifact];
            $zip->addFromString($name, self::artifactFile($artifact));
        }
        $zip->addFromString('kit.html', self::htmlReader($project, $cookbook, $fileList));
        $zip->addFromString('README.md', self::manifest($project, $cookbook, $fileList));
        $zip->close();

        return [
            'filename' => $filename,
            'path'     => $path,
            'size'     => (int) (filesize($path) ?: 0),
            'count'    => count($fileList),
        ];
    }

    /** @param array<string, mixed> $artifact */
    private static function artifactFile(array $artifact): string
    {
        $header = sprintf(
            "# %s\n\n> Recipe %d of the %s. Approved version v%d%s.\n\n---\n\n",
            $artifact['recipe_title'],
            (int) $artifact['recipe_position'],
            'SousMeow Project Kit',
            (int) $artifact['version_no'],
            $artifact['source'] === 'example' ? ' (sample data from Demo Mode)' : ''
        );
        return $header . rtrim((string) $artifact['content']) . "\n";
    }

    /**
     * Self-contained HTML reader for offline browsing of every approved Artifact.
     *
     * @param array<string, mixed> $project
     * @param array<string, mixed> $cookbook
     * @param list<array{name: string, artifact: array<string, mixed>}> $fileList
     */
    private static function htmlReader(array $project, array $cookbook, array $fileList): string
    {
        $title = (string) $project['title'];
        $cookbookTitle = (string) $cookbook['title'];
        $exportedAt = gmdate('Y-m-d H:i') . ' UTC';
        $pantry = self::pantryEntries($project);

        $toc = '';
        $articles = '';
        foreach ($fileList as $entry) {
            $a = $entry['artifact'];
            $slug = (string) $a['recipe_slug'];
            $recipeTitle = (string) $a['recipe_title'];
            $position = (int) $a['recipe_position'];
            $version = (int) $a['version_no'];
            $sample = $a['source'] === 'example';
            $mdName = $entry['name'];

            $toc .= sprintf(
                '<li><a href="#%s">%02d. %s</a> <span class="toc-file">%s</span></li>' . "\n",
                e($slug),
                $position,
                e($recipeTitle),
                e($mdName)
            );

            $meta = sprintf('Recipe %d · v%d approved', $position, $version);
            if ($sample) {
                $meta .= ' · sample data';
            }

            $articles .= '<article class="kit-article" id="' . e($slug) . '">' . "\n";
            $articles .= '<header class="kit-article-head">' . "\n";
            $articles .= '<p class="kit-article-step">Recipe ' . $position . '</p>' . "\n";
            $articles .= '<h2>' . e($recipeTitle) . '</h2>' . "\n";
            $articles .= '<p class="kit-article-meta">' . e($meta) . ' · <code>' . e($mdName) . '</code></p>' . "\n";
            $articles .= '</header>' . "\n";
            $articles .= '<div class="kit-article-body">' . SafeText::render((string) $a['content']) . '</div>' . "\n";
            $articles .= '</article>' . "\n";
        }

        $pantryHtml = '';
        foreach ($pantry as $row) {
            $pantryHtml .= '<dt>' . e($row['label']) . '</dt><dd>' . e($row['value']) . '</dd>' . "\n";
        }

        $titleEsc = self::htmlText($title);
        $cookbookEsc = self::htmlText($cookbookTitle);
        $exportedEsc = self::htmlText($exportedAt);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$titleEsc} · Project Kit</title>
  <style>
    :root {
      --paper: #FAF2E5;
      --card: #FFFDF8;
      --ink: #3E3128;
      --ink-soft: #6E5D4E;
      --ink-faint: #98866F;
      --terracotta: #BC5B32;
      --terracotta-wash: #F7E3D7;
      --sage: #5E7E4E;
      --line: #E9DBC5;
      --font: "Segoe UI", system-ui, -apple-system, sans-serif;
      --mono: ui-monospace, "SF Mono", Menlo, monospace;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: var(--font);
      font-size: 1rem;
      line-height: 1.6;
      color: var(--ink);
      background: var(--paper);
    }
    .kit-wrap { max-width: 46rem; margin: 0 auto; padding: 1.5rem 1.25rem 3rem; }
    .kit-hero {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border-top: 6px solid var(--terracotta);
    }
    .kit-hero h1 { margin: 0 0 0.35rem; font-size: 1.5rem; line-height: 1.25; }
    .kit-hero p { margin: 0.25rem 0; color: var(--ink-soft); font-size: 0.95rem; }
    .kit-hero .kit-badge {
      display: inline-block;
      margin-top: 0.75rem;
      padding: 0.2rem 0.55rem;
      border-radius: 999px;
      background: var(--terracotta-wash);
      color: var(--terracotta);
      font-size: 0.78rem;
      font-weight: 600;
    }
    .kit-toc, .kit-pantry, .kit-article {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      margin-bottom: 1.25rem;
    }
    .kit-toc h2, .kit-pantry h2, .kit-article h2 { margin: 0 0 0.75rem; font-size: 1.1rem; }
    .kit-toc ol { margin: 0; padding-left: 1.25rem; }
    .kit-toc li { margin-bottom: 0.45rem; }
    .kit-toc a { color: var(--terracotta); font-weight: 600; text-decoration: none; }
    .kit-toc a:hover { text-decoration: underline; }
    .toc-file { color: var(--ink-faint); font-size: 0.82rem; font-weight: 400; }
    .kit-pantry dl { display: grid; grid-template-columns: minmax(7rem, 11rem) 1fr; gap: 0.35rem 1rem; margin: 0; }
    .kit-pantry dt { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ink-faint); font-weight: 700; }
    .kit-pantry dd { margin: 0; color: var(--ink-soft); overflow-wrap: anywhere; }
    .kit-article-head { border-bottom: 1px solid var(--line); padding-bottom: 0.75rem; margin-bottom: 1rem; }
    .kit-article-step { margin: 0 0 0.2rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink-faint); font-weight: 700; }
    .kit-article-meta { margin: 0.35rem 0 0; font-size: 0.85rem; color: var(--ink-faint); }
    .kit-article-meta code { font-family: var(--mono); font-size: 0.8rem; background: var(--paper); padding: 0.1em 0.35em; border-radius: 4px; }
    .kit-article-body h3, .kit-article-body h4, .kit-article-body h5 { margin: 1.25rem 0 0.5rem; line-height: 1.3; }
    .kit-article-body h3 { font-size: 1.05rem; }
    .kit-article-body p { margin: 0.65rem 0; color: var(--ink-soft); }
    .kit-article-body ul, .kit-article-body ol { margin: 0.5rem 0 0.75rem; padding-left: 1.35rem; color: var(--ink-soft); }
    .kit-article-body li { margin-bottom: 0.35rem; }
    .kit-article-body blockquote {
      margin: 0.75rem 0;
      padding: 0.5rem 0.85rem;
      border-left: 3px solid var(--terracotta);
      background: var(--terracotta-wash);
      color: var(--ink-soft);
    }
    .kit-article-body code {
      font-family: var(--mono);
      font-size: 0.88em;
      background: var(--paper);
      padding: 0.1em 0.3em;
      border-radius: 4px;
    }
    .kit-article-body pre {
      margin: 0.75rem 0;
      padding: 0.85rem 1rem;
      background: #3B2F26;
      color: #F7EFE2;
      border-radius: 8px;
      overflow-x: auto;
      font-family: var(--mono);
      font-size: 0.85rem;
      line-height: 1.5;
    }
    .kit-article-body pre code { background: none; padding: 0; color: inherit; }
    .kit-footer { font-size: 0.82rem; color: var(--ink-faint); text-align: center; margin-top: 1.5rem; }
    @media (max-width: 480px) {
      .kit-wrap { padding: 1rem 0.85rem 2rem; }
      .kit-pantry dl { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="kit-wrap">
    <header class="kit-hero">
      <h1>{$titleEsc}</h1>
      <p>Cooked with the <strong>{$cookbookEsc}</strong> Cookbook in SousMeow.</p>
      <p>Exported {$exportedEsc}. Every section below was human-reviewed and approved before export.</p>
      <span class="kit-badge">Open this file in any browser · works offline</span>
    </header>

    <nav class="kit-toc" aria-label="Table of contents">
      <h2>Contents</h2>
      <ol>
        <li><a href="#pantry">Pantry ingredients</a></li>
        {$toc}
      </ol>
    </nav>

    <section class="kit-pantry" id="pantry">
      <h2>Pantry ingredients</h2>
      <p style="margin:0 0 1rem;color:var(--ink-soft);font-size:0.92rem;">The facts every Recipe cooked from, filled once at the start of the Project.</p>
      <dl>
        {$pantryHtml}
      </dl>
    </section>

    {$articles}

    <p class="kit-footer">SousMeow Project Kit · Markdown source files are numbered in this zip alongside README.md</p>
  </div>
</body>
</html>
HTML;
    }

    /**
     * @param array<string, mixed> $project
     * @param array<string, mixed> $cookbook
     * @param list<array{name: string, artifact: array<string, mixed>}> $fileList
     */
    private static function manifest(array $project, array $cookbook, array $fileList): string
    {
        $lines = [];
        $lines[] = '# ' . $project['title'] . ': Project Kit';
        $lines[] = '';
        $lines[] = sprintf(
            'Cooked with the **%s** Cookbook in SousMeow and exported on %s UTC.',
            $cookbook['title'],
            gmdate('Y-m-d H:i')
        );
        $lines[] = '';
        $lines[] = 'Every file below was generated by an AI of the author\'s choosing, then human-reviewed';
        $lines[] = 'against the Recipe\'s Quality Checks and explicitly approved before export.';
        $lines[] = '';
        $lines[] = '## Files';
        $lines[] = '';
        $lines[] = '- `kit.html`: Self-contained HTML reader for every approved Recipe (open in any browser, works offline)';
        foreach ($fileList as $entry) {
            $a = $entry['artifact'];
            $sample = $a['source'] === 'example' ? ', sample data' : '';
            $lines[] = sprintf('- `%s`: %s (v%d approved%s)', $entry['name'], $a['recipe_title'], (int) $a['version_no'], $sample);
        }
        $lines[] = '';
        $lines[] = '## Pantry (the ingredients everything was cooked from)';
        $lines[] = '';

        foreach (self::pantryEntries($project) as $row) {
            if (str_contains($row['value'], "\n")) {
                $lines[] = '- **' . $row['label'] . ':**';
                foreach (explode("\n", $row['value']) as $part) {
                    if (trim($part) !== '') {
                        $lines[] = '  - ' . trim($part);
                    }
                }
            } else {
                $lines[] = '- **' . $row['label'] . ':** ' . ($row['value'] !== '' ? $row['value'] : '(empty)');
            }
        }
        $lines[] = '';
        return implode("\n", $lines);
    }

    /**
     * @param array<string, mixed> $project
     * @return list<array{label: string, value: string}>
     */
    private static function pantryEntries(array $project): array
    {
        $values = Project::pantryValues((int) $project['id']);
        $rows = [];
        foreach (PantryField::forCookbook((int) $project['cookbook_id']) as $field) {
            $raw = trim($values[(int) $field['id']] ?? '');
            if ($field['type'] === 'multiselect') {
                $decoded = json_decode($raw, true);
                $raw = is_array($decoded) ? implode(', ', $decoded) : $raw;
            }
            $rows[] = ['label' => (string) $field['label'], 'value' => $raw !== '' ? $raw : '(empty)'];
        }
        return $rows;
    }

    private static function htmlText(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function slugify(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim(substr($slug, 0, 40), '-');
    }
}
