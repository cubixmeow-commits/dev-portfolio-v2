<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Central competition engine: strategy selection, presentation helpers,
 * and structured summary generation for both daily_wins and series_average.
 */
final class MetricCompetitionService
{
    public const STRATEGY_DAILY_WINS = 'daily_wins';
    public const STRATEGY_SERIES_AVERAGE = 'series_average';

    public static function strategy(array $matchOrMetric): string
    {
        $s = (string) ($matchOrMetric['scoring_strategy'] ?? self::STRATEGY_DAILY_WINS);
        return $s === self::STRATEGY_SERIES_AVERAGE
            ? self::STRATEGY_SERIES_AVERAGE
            : self::STRATEGY_DAILY_WINS;
    }

    public static function isSeriesAverage(array $matchOrMetric): bool
    {
        return self::strategy($matchOrMetric) === self::STRATEGY_SERIES_AVERAGE;
    }

    public static function isDailyWins(array $matchOrMetric): bool
    {
        return self::strategy($matchOrMetric) === self::STRATEGY_DAILY_WINS;
    }

    /**
     * Daily comparison ownership for rail / live panels (both strategies).
     * Uses per-day values and the match tie threshold.
     *
     * @return array{side: ?string, is_tie: bool, margin: ?int, value_a: ?int, value_b: ?int}
     */
    public static function dailyComparison(array $match, ?int $valueA, ?int $valueB): array
    {
        if ($valueA === null || $valueB === null) {
            return [
                'side' => null,
                'is_tie' => false,
                'margin' => null,
                'value_a' => $valueA,
                'value_b' => $valueB,
            ];
        }
        $cmp = MetricComparisonService::compare(
            $valueA,
            $valueB,
            (int) $match['tie_threshold'],
            (int) ($match['higher_wins'] ?? 1) === 1
        );
        return [
            'side' => $cmp['is_tie'] ? null : ($cmp['winner_side'] ?? null),
            'is_tie' => $cmp['is_tie'],
            'margin' => $cmp['margin'],
            'value_a' => $valueA,
            'value_b' => $valueB,
        ];
    }

    public static function metricPayload(array $match): array
    {
        return [
            'slug' => (string) ($match['metric_slug'] ?? $match['slug'] ?? ''),
            'name' => (string) ($match['metric_name'] ?? $match['name'] ?? ''),
            'unit' => (string) ($match['metric_unit'] ?? $match['unit'] ?? ''),
            'display_unit' => $match['display_unit'] ?? null,
            'classification' => (string) ($match['classification'] ?? 'performance'),
            'scoring_strategy' => self::strategy($match),
            'higher_wins' => (int) ($match['higher_wins'] ?? 1) === 1,
            'context_note' => $match['context_note'] ?? null,
            'description' => $match['description'] ?? null,
        ];
    }

    /** Human rail legend for the active strategy. */
    public static function railLegendCopy(array $match): array
    {
        if (self::isSeriesAverage($match)) {
            return [
                'title' => 'Daily comparison',
                'note' => 'Daily markers show comparison results. The series winner is determined by the final average.',
                'a' => 'Recorded the leading value',
                'b' => 'Recorded the leading value',
                'tie' => 'Within threshold',
            ];
        }
        return [
            'title' => 'Daily games',
            'note' => 'Each official day awards one series point. Daily wins decide the match.',
            'a' => 'Win',
            'b' => 'Win',
            'tie' => 'Tie',
        ];
    }

    /**
     * Primary scoreboard / list scoreline for either strategy.
     *
     * @return array{primary: string, aria: string, is_average: bool}
     */
    public static function scoreline(array $match, array $summary): array
    {
        $metric = self::metricPayload($match);
        if (self::isSeriesAverage($match)) {
            $a = MetricFormatter::formatCompact($summary['player_a_average'] ?? null, $metric);
            $b = MetricFormatter::formatCompact($summary['player_b_average'] ?? null, $metric);
            return [
                'primary' => $a . ' / ' . $b,
                'aria' => 'Series averages ' . $a . ' versus ' . $b,
                'is_average' => true,
            ];
        }
        $a = (int) ($summary['player_a_wins'] ?? 0);
        $b = (int) ($summary['player_b_wins'] ?? 0);
        return [
            'primary' => $a . '–' . $b,
            'aria' => 'Series score ' . $a . ' to ' . $b,
            'is_average' => false,
        ];
    }

    public static function leaderSide(array $match, array $summary): ?string
    {
        $leader = $summary['leader_user_id'] ?? null;
        if ($leader === null) {
            return null;
        }
        if ((int) $leader === (int) $match['player_a_user_id']) {
            return 'a';
        }
        if ((int) $leader === (int) $match['player_b_user_id']) {
            return 'b';
        }
        return null;
    }
}
