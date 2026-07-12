<?php

declare(strict_types=1);

/**
 * CLI smoke test: Launch Day Kit end-to-end without a browser.
 * Usage: php scripts/verify-launch-day-kit.php
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

$cookbook = Cookbook::findBySlug('launch-day-kit');
if ($cookbook === null || (int) $cookbook['is_executable'] !== 1) {
    fwrite(STDERR, "FAIL: Launch Day Kit not executable\n");
    exit(1);
}

$userId = (int) Database::fetchValue('SELECT id FROM users LIMIT 1');
$projectId = Project::create($userId, (int) $cookbook['id'], 'Driftlog launch test');
$fields = PantryField::forCookbook((int) $cookbook['id']);
$values = [];
foreach ($fields as $field) {
    $sample = (string) $field['sample_value'];
    if ($field['type'] === 'multiselect') {
        $picked = array_map('trim', explode(',', $sample));
        $values[(int) $field['id']] = json_encode($picked);
    } else {
        $values[(int) $field['id']] = $sample;
    }
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
$expected = count($recipes) + 1;
if ($zip->numFiles < $expected) {
    fwrite(STDERR, "FAIL: zip has {$zip->numFiles} files, expected at least {$expected}\n");
    exit(1);
}
$zip->close();

echo "OK: Launch Day Kit export at {$zipPath}\n";
exit(0);
