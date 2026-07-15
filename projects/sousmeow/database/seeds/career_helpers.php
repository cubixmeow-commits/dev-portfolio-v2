<?php

declare(strict_types=1);

/**
 * Shared Career Collection helpers.
 *
 * Compatible Pantry field_key names across career Cookbooks (reuse later if
 * cross-Cookbook persistence arrives). Values are not shared at runtime yet.
 */

const SM_CAREER_FACT_RULE = 'Do not invent or infer missing career facts. Mark missing evidence clearly and ask for clarification. Never invent employers, job titles, dates, degrees, certifications, skills, responsibilities, accomplishments, revenue figures, percentages, team sizes, performance metrics, awards, clients, salary research, or competing offers. If a useful metric is missing, mark it missing and provide a qualitative alternative only when no truthful figure exists. Never turn an estimate into a fact.';

const SM_CAREER_PRIVACY_BLURB = 'Do not paste Social Security numbers, account passwords, full home addresses, private references\' contact details, confidential employer documents, proprietary customer data, or medical/disability information unless you intentionally choose to disclose it. Before using an external AI, remove phone numbers, street addresses, and other identifying details you do not need for this step.';

/** Ordered Career Collection slugs for related-Cookbook recommendations. */
function sm_career_collection_slugs(): array
{
    return [
        'tailor-resume-to-a-job',
        'prep-for-an-interview',
        'write-a-strong-cover-letter',
        'improve-your-linkedin-profile',
        'plan-a-career-change',
        'prepare-for-salary-negotiation',
    ];
}

/**
 * Related Career Cookbooks excluding the current slug.
 *
 * @return list<string>
 */
function sm_career_related_slugs(string $currentSlug): array
{
    return array_values(array_filter(
        sm_career_collection_slugs(),
        static fn(string $slug): bool => $slug !== $currentSlug
    ));
}

function sm_career_privacy_pantry_help(string $extra = ''): string
{
    $base = 'Remove phone numbers, street addresses, and account details before pasting. ' . SM_CAREER_PRIVACY_BLURB;
    return $extra === '' ? $base : $extra . ' ' . $base;
}

/**
 * Normalize a Recipe seed array with Career Collection teaching fields.
 *
 * @param array<string, mixed> $recipe
 * @return array<string, mixed>
 */
function sm_career_recipe(array $recipe): array
{
    $recipe['before_you_begin'] = (string) ($recipe['before_you_begin'] ?? '');
    $recipe['common_problems'] = (string) ($recipe['common_problems'] ?? '');
    $recipe['recovery_guidance'] = (string) ($recipe['recovery_guidance'] ?? '');
    $recipe['why_it_matters'] = (string) ($recipe['why_it_matters'] ?? '');
    $recipe['unlocks_text'] = (string) ($recipe['unlocks_text'] ?? '');
    return $recipe;
}

/**
 * Beginner detail copy shared across Career Collection descriptions.
 */
function sm_career_beginner_footer(string $kitExample, int $steps, string $minutesHint): string
{
    return "What you finish: a downloadable project kit including {$kitExample}. "
        . "Before you start: gather only truthful facts about your work and the target role. "
        . "Approximate time: {$minutesHint}. Steps: {$steps}. "
        . 'A free AI chat account is usually enough. '
        . SM_CAREER_PRIVACY_BLURB
        . ' Loop for every step: review → copy prompt → paste into your AI → review the answer → paste into SousMeow → use the confidence check → improve or continue.';
}
