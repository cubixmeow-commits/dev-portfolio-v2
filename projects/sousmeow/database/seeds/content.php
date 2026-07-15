<?php

declare(strict_types=1);

/**
 * Seed content loader for SousMeow first-party Cookbook library.
 *
 * Thirty-three curated Cookbooks (thirty-one executable, two preview):
 * - Launch Day Kit (executable)
 * - Validate a SaaS Idea (executable)
 * - Build a Professional Portfolio (preview)
 * - Plan a YouTube Video (executable)
 * - Plan a Novel (preview)
 * - Build a Study Plan (executable)
 * - Write an Email That Gets Answered (executable)
 * - Write a Feature Spec (executable)
 * - Name Your Brand Voice (executable)
 * - Compare Three Competitors (executable)
 * - Make a Criteria Decision (executable)
 * - Finish a Personal Project (executable)
 * - Price Your Offer (executable)
 * - Write a Campaign Brief (executable)
 * - Pack a Release Checklist (executable)
 * - Plan a Newsletter Issue (executable)
 * - Outline an Article (executable)
 * - Critique a Screen (executable)
 * - Tailor Your Resume to a Job (executable)
 * - Prepare for an Interview (executable)
 * - Write a Strong Cover Letter (executable)
 * - Improve Your LinkedIn Profile (executable)
 * - Plan a Career Change (executable)
 * - Prepare for a Salary Negotiation (executable)
 * - Build a Budget and Debt Payoff Plan (executable)
 * - Prepare a Bill Dispute (executable)
 * - Audit a Medical Bill (executable)
 * - Navigate a First Home Purchase (executable)
 * - Synthesize Interview Notes (executable)
 * - Plan a Lesson (executable)
 * - Document a Simple Process (executable)
 * - Design a Game Loop (executable)
 * - Set a Thirty-Day Goal (executable)
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
    'write-a-feature-spec.php',
    'name-your-brand-voice.php',
    'compare-three-competitors.php',
    'make-a-criteria-decision.php',
    'finish-a-personal-project.php',
    'price-your-offer.php',
    'write-a-campaign-brief.php',
    'pack-a-release-checklist.php',
    'plan-a-newsletter-issue.php',
    'outline-an-article.php',
    'critique-a-screen.php',
    'tailor-resume-to-a-job.php',
    'prep-for-an-interview.php',
    'write-a-strong-cover-letter.php',
    'improve-your-linkedin-profile.php',
    'plan-a-career-change.php',
    'prepare-for-salary-negotiation.php',
    'build-budget-and-debt-plan.php',
    'prepare-bill-dispute.php',
    'audit-medical-bill.php',
    'navigate-first-home-purchase.php',
    'synthesize-interview-notes.php',
    'plan-a-lesson.php',
    'document-a-simple-process.php',
    'design-a-game-loop.php',
    'set-a-thirty-day-goal.php',
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
    'cookbooks'   => $cookbooks,
    'categories'  => $categories,
    'collections' => $collections,
];
