<?php

declare(strict_types=1);

/**
 * Shared Money & Major Decisions helpers.
 *
 * Guidance is organizational — never financial, legal, medical, or tax advice.
 */

const SM_MONEY_FACT_RULE = 'Do not invent or infer missing financial figures, interest rates, laws, regulations, medical advice, mortgage approvals, credit decisions, insurance coverage, consumer rights, or salary estimates. Mark missing information clearly and ask for clarification. Never turn an estimate into a verified fact. Do not promise financial outcomes.';

const SM_MONEY_PRIVACY_BLURB = 'Do not paste Social Security numbers, bank account numbers, credit card numbers, insurance IDs, medical record numbers, passwords, or personal account credentials. Before using ChatGPT, Claude, Gemini, Grok, Copilot, DeepSeek, or any external AI, replace sensitive values with placeholders such as [BANK-LAST4], [CLAIM-ID], or [MEMBER-ID].';

const SM_MONEY_DISCLAIMER = 'This Cookbook helps you organize information and prepare questions or documents. It is not financial, legal, medical, tax, or lending advice. Verify important decisions with qualified professionals and current official sources when needed.';

/** Ordered Money & Major Decisions Cookbook slugs. */
function sm_money_collection_slugs(): array
{
    return [
        'build-budget-and-debt-plan',
        'prepare-bill-dispute',
        'audit-medical-bill',
        'navigate-first-home-purchase',
    ];
}

/** @return list<string> */
function sm_money_related_slugs(string $currentSlug): array
{
    return array_values(array_filter(
        sm_money_collection_slugs(),
        static fn(string $slug): bool => $slug !== $currentSlug
    ));
}

function sm_money_privacy_pantry_help(string $extra = ''): string
{
    $base = SM_MONEY_PRIVACY_BLURB;
    return $extra === '' ? $base : $extra . ' ' . $base;
}

/**
 * @param array<string, mixed> $recipe
 * @return array<string, mixed>
 */
function sm_money_recipe(array $recipe): array
{
    $recipe['before_you_begin'] = (string) ($recipe['before_you_begin'] ?? '');
    $recipe['common_problems'] = (string) ($recipe['common_problems'] ?? '');
    $recipe['recovery_guidance'] = (string) ($recipe['recovery_guidance'] ?? '');
    $recipe['why_it_matters'] = (string) ($recipe['why_it_matters'] ?? '');
    $recipe['unlocks_text'] = (string) ($recipe['unlocks_text'] ?? '');
    return $recipe;
}

function sm_money_beginner_footer(string $kitExample, int $steps, string $minutesHint): string
{
    return SM_MONEY_DISCLAIMER . ' '
        . "What you finish: a downloadable project kit including {$kitExample}. "
        . "Approximate time: {$minutesHint}. Steps: {$steps}. "
        . 'A free AI chat account is usually enough. '
        . SM_MONEY_PRIVACY_BLURB
        . ' Loop for every step: review → copy prompt → paste into your AI → review the answer → paste into SousMeow → use the confidence check → improve or continue.';
}
