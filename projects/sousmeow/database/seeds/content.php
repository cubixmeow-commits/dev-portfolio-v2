<?php

declare(strict_types=1);

/**
 * Seed content loader for SousMeow first-party Cookbook library.
 *
 * Seven curated Cookbooks (five executable, two preview):
 * - Launch Day Kit (executable)
 * - Validate a SaaS Idea (executable)
 * - Build a Professional Portfolio (preview)
 * - Plan a YouTube Video (executable)
 * - Plan a Novel (preview)
 * - Build a Study Plan (executable)
 * - Write an Email That Gets Answered (executable)
 *
 * Each Cookbook is defined in database/seeds/cookbooks/{slug}.php.
 * Prompt templates use {{field_key}} for Pantry values and
 * {{artifact:recipe-slug}} for approved earlier Artifacts.
 */

$cookbookFiles = [
    'launch-day-kit.php',
    'validate-saas-idea.php',
    'professional-portfolio.php',
    'plan-youtube-video.php',
    'plan-a-novel.php',
    'build-a-study-plan.php',
    'write-email-that-gets-answered.php',
];

$cookbooks = [];
foreach ($cookbookFiles as $file) {
    /** @var array<string, mixed> $book */
    $book = require __DIR__ . '/cookbooks/' . $file;
    $cookbooks[] = $book;
}

/** @var list<array<string, mixed>> $categories */
$categories = require __DIR__ . '/categories.php';
/** @var list<array<string, mixed>> $collections */
$collections = require __DIR__ . '/collections.php';

return [
    'categories'  => $categories,
    'collections' => $collections,
    'cookbooks'   => $cookbooks,
];
