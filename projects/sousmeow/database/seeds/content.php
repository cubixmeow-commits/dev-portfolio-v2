<?php

declare(strict_types=1);

/**
 * Seed content loader for SousMeow first-party Cookbook library.
 *
 * Five curated Cookbooks:
 * - Launch Day Kit (executable)
 * - Validate a SaaS Idea (executable)
 * - Build a Professional Portfolio (preview)
 * - Plan a YouTube Video (preview)
 * - Plan a Novel (preview)
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
];

$cookbooks = [];
foreach ($cookbookFiles as $file) {
    /** @var array<string, mixed> $book */
    $book = require __DIR__ . '/cookbooks/' . $file;
    $cookbooks[] = $book;
}

return ['cookbooks' => $cookbooks];
