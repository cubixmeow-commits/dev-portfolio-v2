<?php

declare(strict_types=1);

namespace Rally\Services;

/**
 * Central competition engine: strategy selection, Classic/Baseline presentation,
 * and structured summary helpers for daily_wins and series_average.
 */
final class MetricCompetitionService
{
    public const STRATEGY_DAILY_WINS = 'daily_wins';
    public const STRATEGY_SERIES_AVERAGE = 'series_average';

    public const TYPE_CLASSIC = 'classic';
    public const TYPE_BASELINE = 'baseline';

    public static function strategy(array $matchOrMetric): string
    {
        $s = (string) ($matchOrMetric['scoring_strategy'] ?? self::STRATEGY_DAILY_WINS);
        return $s === self::STRATEGY_SERIES_AVERAGE
            ? self::STRATEGY_SERIES_AVERAGE
            : self::STRATEGY_DAILY_WINS;
    }

    public static function competitionType(array $match): string
    {
        $t = (string) ($match['competition_type'] ?? self::TYPE_CLASSIC);
        return $t === self::TYPE_BASELINE ? self::TYPE_BASELINE : self::TYPE_CLASSIC;
    }

    public static function isBaseline(array $match): bool
    {
        return self::competitionType($match) === self::TYPE_BASELINE;
    }

    public static function isClassic(array $match): bool
    {
        return self::competitionType($match) === self::TYPE_CLASSIC;
    }

    public static function isSeriesAverage(array $matchOrMetric): bool
    {
        return self::strategy($matchOrMetric) === self::STRATEGY_SERIES_AVERAGE;
    }

    public static function isDailyWins(array $matchOrMetric): bool
    {
        return self::strategy($matchOrMetric) === self::STRATEGY_DAILY_WINS;
    }

    public static function isHealthComparisonSeries(array $matchOrMetric): bool
    {
        return self::isSeriesAverage($matchOrMetric)
            || (string) ($matchOrMetric['classification'] ?? '') === 'health_comparison';
    }

    /**
     * Validate that competition_type × metric/strategy is allowed in V1.
     *
     * @throws \InvalidArgumentException
     */
    public static function assertValidCombination(array $metric, string $competitionType): void
    {
        $type = $competitionType === self::TYPE_BASELINE ? self::TYPE_BASELINE : self::TYPE_CLASSIC;
        $slug = (string) ($metric['slug'] ?? '');
        $strategy = self::strategy($metric);

        if ($type === self::TYPE_CLASSIC) {
            return;
        }

        // Baseline
        if ($strategy !== self::STRATEGY_DAILY_WINS) {
            throw new \InvalidArgumentException(
                'Baseline competitions require a daily_wins metric. Series-average metrics support Classic only.'
            );
        }
        if (!BaselineService::supportsBaseline($metric)) {
            throw new \InvalidArgumentException(
                'Baseline is not available for this metric. Rally compares the recorded series directly.'
            );
        }
        if ($slug === '' || !in_array($slug, BaselineService::BASELINE_ELIGIBLE_METRICS, true)) {
            throw new \InvalidArgumentException(
                'Baseline is only available for Daily Steps and Active Minutes.'
            );
        }
    }

    /**
     * Daily comparison ownership for rail / live panels.
     * Classic uses raw values; Baseline uses percentage change.
     *
     * @param array{player_a?: array<string, mixed>, player_b?: array<string, mixed>}|null $baselines
     * @return array{side: ?string, is_tie: bool, margin: float|int|null, value_a: ?int, value_b: ?int, percentage_a: ?float, percentage_b: ?float}
     */
    public static function dailyComparison(
        array $match,
        ?int $valueA,
        ?int $valueB,
        ?array $baselines = null
    ): array {
        if (self::isBaseline($match) && $baselines !== null) {
            $outcome = BaselineCompetitionService::dayOutcome($match, $valueA, $valueB, $baselines);
            return [
                'side' => $outcome['winner_side'] ?? null,
                'is_tie' => ($outcome['kind'] ?? '') === 'tie',
                'margin' => $outcome['margin'] ?? null,
                'value_a' => $valueA,
                'value_b' => $valueB,
                'percentage_a' => $outcome['percentage_a'] ?? null,
                'percentage_b' => $outcome['percentage_b'] ?? null,
            ];
        }

        if ($valueA === null || $valueB === null) {
            return [
                'side' => null,
                'is_tie' => false,
                'margin' => null,
                'value_a' => $valueA,
                'value_b' => $valueB,
                'percentage_a' => null,
                'percentage_b' => null,
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
            'percentage_a' => null,
            'percentage_b' => null,
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
            'competition_type' => self::competitionType($match),
            'higher_wins' => (int) ($match['higher_wins'] ?? 1) === 1,
            'context_note' => $match['context_note'] ?? null,
            'description' => $match['description'] ?? null,
        ];
    }

    public static function competitionTypeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_BASELINE => 'Baseline',
            default => 'Classic',
        };
    }

    public static function competitionTypePromise(string $type): string
    {
        return match ($type) {
            self::TYPE_BASELINE => 'Beat your normal by more than they beat theirs.',
            default => 'Beat your opponent. Recorded values determine the result.',
        };
    }

    public static function formatSurfaceLabel(array $match): string
    {
        if (self::isHealthComparisonSeries($match)) {
            return 'Health Comparison Series';
        }
        if (self::isBaseline($match)) {
            return 'Baseline Daily Game Series';
        }
        return 'Classic Daily Game Series';
    }

    /** Human rail legend for the active strategy / competition type. */
    public static function railLegendCopy(array $match): array
    {
        if (self::isSeriesAverage($match)) {
            return [
                'title' => 'Daily comparison',
                'note' => 'Daily markers show recorded comparisons. The final official average determines the series result.',
                'a' => 'Recorded the leading value',
                'b' => 'Recorded the leading value',
                'tie' => 'Within threshold',
            ];
        }
        if (self::isBaseline($match)) {
            return [
                'title' => 'Baseline daily games',
                'note' => 'Each official day awards one series point based on percentage change from the frozen baseline. Most daily wins decide the match.',
                'a' => 'Win',
                'b' => 'Win',
                'tie' => 'Tie',
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

    public static function resultBasisLabel(array $match): string
    {
        if (self::isBaseline($match)) {
            return 'Percentage change from frozen baseline';
        }
        if (self::isSeriesAverage($match)) {
            return 'Final official series average';
        }
        return 'Recorded value';
    }
}
