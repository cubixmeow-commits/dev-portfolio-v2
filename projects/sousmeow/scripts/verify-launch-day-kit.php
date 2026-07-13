<?php

declare(strict_types=1);

/**
 * CLI smoke test: Launch Day Kit end-to-end without a browser, covering
 * the output-contract pilot. Recipe 1 exercises the full review
 * mechanics (unstructured fallback, confirmation clearing on edit,
 * restore, approval gate); the remaining recipes run the structured
 * happy path; the export is checked last.
 *
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
use SousMeow\Services\ResponseParser;

function fail(string $message): never
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

$cookbook = Cookbook::findBySlug('launch-day-kit');
if ($cookbook === null || (int) $cookbook['is_executable'] !== 1) {
    fail('Launch Day Kit not executable');
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

// --- Recipe 1: full review mechanics -------------------------------

$first = $recipes[0];
$firstId = (int) $first['id'];
$contract = Recipe::outputContract($first);
if ($contract === null) {
    fail("pilot recipe {$first['slug']} has no output contract");
}
$checks = Recipe::checks($firstId);

// 1) Unstructured response: parsing degrades to missing statuses but
//    never blocks saving, confirming, or approving.
$unstructured = 'A plain paragraph response without any Markdown headings, long enough to be stored and reviewed.';
$artifactId = Artifact::addVersion($projectId, $firstId, $unstructured, 'pasted');
$v1 = Artifact::latestVersion($artifactId);
$parsed = ResponseParser::parse((string) $v1['content'], $contract);
if ($parsed['missing_required'] === []) {
    fail('unstructured response should be missing required sections');
}
foreach ($checks as $check) {
    $keys = Recipe::evidenceKeys($check);
    $status = ResponseParser::checkStatus($keys, $parsed);
    $wanted = $keys === [] ? ResponseParser::STATUS_MANUAL : ResponseParser::STATUS_MISSING;
    if ($status !== $wanted) {
        fail("unstructured status for '{$check['label']}' is {$status}, expected {$wanted}");
    }
}
echo "OK: unstructured response degrades to missing/manual, never an error\n";

// 2) Confirmations tie to the exact version and clear on edit.
Artifact::setCheck((int) $v1['id'], (int) $checks[0]['id'], true);
if (Artifact::confirmedCheckIds((int) $v1['id']) !== [(int) $checks[0]['id']]) {
    fail('confirmation was not recorded against v1');
}
Artifact::addVersion($projectId, $firstId, $unstructured . ' Edited by hand.', 'edited');
$v2 = Artifact::latestVersion($artifactId);
if ((int) $v2['version_no'] !== 2) {
    fail('edit did not create version 2');
}
if (Artifact::confirmedCheckIds((int) $v2['id']) !== []) {
    fail('new version must start with no confirmations');
}
$artifactRow = Artifact::find($projectId, $firstId);
if ($artifactRow['status'] !== 'review' || $artifactRow['approved_version_id'] !== null) {
    fail('artifact should be back in review after an edit');
}
echo "OK: editing creates a new version and clears confirmations\n";

// 3) Restore keeps history intact and appends a new version.
Artifact::addVersion($projectId, $firstId, (string) $v1['content'], 'restored');
$v3 = Artifact::latestVersion($artifactId);
if ((int) $v3['version_no'] !== 3 || $v3['content'] !== $v1['content']) {
    fail('restore should append v3 with v1 content');
}
if (count(Artifact::versions($artifactId)) !== 3) {
    fail('restore must not rewrite history');
}
echo "OK: restore appends a new version, history intact\n";

// 4) Structured example response: replacement creates v4, evidence is
//    located, all checks confirmed, approval succeeds.
Artifact::addVersion($projectId, $firstId, (string) $first['example_response'], 'example');
$v4 = Artifact::latestVersion($artifactId);
if ((int) $v4['version_no'] !== 4) {
    fail('example paste should be v4');
}
$parsed = ResponseParser::parse((string) $v4['content'], $contract);
if ($parsed['missing_required'] !== [] || $parsed['duplicates'] !== []) {
    fail('structured example should locate every required section exactly once');
}
foreach ($checks as $check) {
    $keys = Recipe::evidenceKeys($check);
    $status = ResponseParser::checkStatus($keys, $parsed);
    $wanted = $keys === [] ? ResponseParser::STATUS_MANUAL : ResponseParser::STATUS_LOCATED;
    if ($status !== $wanted) {
        fail("structured status for '{$check['label']}' is {$status}, expected {$wanted}");
    }
    Artifact::setCheck((int) $v4['id'], (int) $check['id'], true);
}
Artifact::approve($artifactId, (int) $v4['id']);
echo "OK: structured example located, confirmed, approved ({$first['slug']})\n";

// --- Remaining recipes: structured happy path -----------------------

foreach (array_slice($recipes, 1) as $recipe) {
    $recipeId = (int) $recipe['id'];
    $content = (string) $recipe['example_response'];
    if ($content === '') {
        fail("missing example for {$recipe['slug']}");
    }
    $recipeContract = Recipe::outputContract($recipe);
    if ($recipeContract === null) {
        fail("pilot recipe {$recipe['slug']} has no output contract");
    }
    $parsed = ResponseParser::parse($content, $recipeContract);
    if ($parsed['missing_required'] !== []) {
        fail("{$recipe['slug']} example missing required sections: " . implode(', ', $parsed['missing_required']));
    }
    $artifactId = Artifact::addVersion($projectId, $recipeId, $content, 'example');
    $version = Artifact::latestVersion($artifactId);
    if ($version === null) {
        fail("no version for {$recipe['slug']}");
    }
    $versionId = (int) $version['id'];
    foreach (Recipe::checks($recipeId) as $check) {
        Artifact::setCheck($versionId, (int) $check['id'], true);
    }
    Artifact::approve($artifactId, $versionId);
    echo "Approved: {$recipe['slug']}\n";
}

if (!Project::markCompleteIfDone($projectId)) {
    fail('project not marked complete');
}

// --- Export ---------------------------------------------------------

$project = Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]);
$result = ProjectKit::build($project, $cookbook);
$zipPath = $result['path'];
if (!is_file($zipPath)) {
    fail('export zip not created');
}

$zip = new ZipArchive();
if ($zip->open($zipPath) !== true) {
    fail('cannot open zip');
}
$expected = count($recipes) + 2; // README.md + kit.html
if ($zip->numFiles < $expected) {
    fail("zip has {$zip->numFiles} files, expected at least {$expected}");
}
if ($zip->locateName('kit.html') === false) {
    fail('kit.html missing from zip');
}
// The approved v4 content (the structured example) is what ships.
$firstFile = (string) $zip->getFromName('01-' . $first['slug'] . '.md');
if (!str_contains($firstFile, '## Positioning statement')) {
    fail('exported recipe 1 does not contain the approved structured content');
}
$zip->close();

echo "OK: Launch Day Kit export at {$zipPath}\n";
exit(0);
