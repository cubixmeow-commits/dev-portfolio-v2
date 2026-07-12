<?php

declare(strict_types=1);

/**
 * CLI smoke test: Plan a YouTube Video end-to-end without a browser.
 * Usage: php scripts/verify-youtube-video.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;
use SousMeow\Models\Artifact;
use SousMeow\Models\Cookbook;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;
use SousMeow\Services\ProjectKit;

$cookbook = Cookbook::findBySlug('plan-youtube-video');
if ($cookbook === null || (int) $cookbook['is_executable'] !== 1) {
    fwrite(STDERR, "FAIL: Plan a YouTube Video not executable\n");
    exit(1);
}

$userId = (int) Database::fetchValue('SELECT id FROM users LIMIT 1');
$projectId = Project::create($userId, (int) $cookbook['id'], 'One-pan pasta video test');
$fields = PantryField::forCookbook((int) $cookbook['id']);
$values = [];
foreach ($fields as $field) {
    $values[(int) $field['id']] = (string) $field['sample_value'];
}
Project::savePantry($projectId, $values);
Project::markPantrySaved($projectId);

$recipes = Recipe::forCookbook((int) $cookbook['id']);
foreach ($recipes as $recipe) {
    $recipeId = (int) $recipe['id'];
    $content = (string) $recipe['example_response'];
    if ($content === '') {
        fwrite(STDERR, "FAIL: missing example for {$recipe['slug']}\n");
        exit(1);
    }
    if ($recipe['prompt_template'] === null || $recipe['prompt_template'] === '') {
        fwrite(STDERR, "FAIL: missing prompt for {$recipe['slug']}\n");
        exit(1);
    }
    $artifactId = Artifact::addVersion($projectId, $recipeId, $content, 'example');
    $checks = Recipe::checks($recipeId);
    $version = Artifact::latestVersion($artifactId);
    if ($version === null) {
        fwrite(STDERR, "FAIL: no version for {$recipe['slug']}\n");
        exit(1);
    }
    $versionId = (int) $version['id'];
    foreach ($checks as $check) {
        Artifact::setCheck($versionId, (int) $check['id'], true);
    }
    Artifact::approve($artifactId, $versionId);
    echo "Approved: {$recipe['slug']}\n";
}

if (!Project::markCompleteIfDone($projectId)) {
    fwrite(STDERR, "FAIL: project not marked complete\n");
    exit(1);
}

$project = Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]);
$result = ProjectKit::build($project, $cookbook);
$zipPath = $result['path'];
if (!is_file($zipPath)) {
    fwrite(STDERR, "FAIL: export zip not created\n");
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($zipPath) !== true) {
    fwrite(STDERR, "FAIL: cannot open zip\n");
    exit(1);
}
$expected = count($recipes) + 2;
if ($zip->numFiles < $expected) {
    fwrite(STDERR, "FAIL: zip has {$zip->numFiles} files, expected at least {$expected}\n");
    exit(1);
}
if ($zip->locateName('kit.html') === false) {
    fwrite(STDERR, "FAIL: kit.html missing from zip\n");
    exit(1);
}
$html = $zip->getFromName('kit.html');
$zip->close();

if ($html === false || !str_contains($html, '<!DOCTYPE html>') || !str_contains($html, 'Research Brief')) {
    fwrite(STDERR, "FAIL: kit.html is invalid or missing recipe content\n");
    exit(1);
}

echo "OK: YouTube Video kit export at {$zipPath}\n";
exit(0);
