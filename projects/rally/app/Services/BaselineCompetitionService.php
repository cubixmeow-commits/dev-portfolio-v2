<?php

declare(strict_types=1);

namespace Rally\Services;

/**
 * Baseline competition math: favorable-direction percentage change vs frozen mean.
 * Presentation-ready deltas; MatchScoringService remains the series scorer.
 */
final class BaselineCompetitionService
{
    /**
     * Favorable-direction percentage change from a frozen baseline mean.
     * Higher-wins metrics: (current - mean) / mean * 100
     * Lower-wins metrics: (mean - current) / mean * 100  (more favorable when lower)
     *
     * V1 only exposes Baseline for higher-wins daily_wins metrics, but the
     * formula stays general for correctness.
     */
    public static function percentageChange(int $currentValue, float $baselineMean, bool $higherWins = true): float
    {
        if ($baselineMean == 0.0) {
            throw new \InvalidArgumentException('Baseline mean cannot be zero.');
        }
        if ($higherWins) {
            return (($currentValue - $baselineMean) / $baselineMean) * 100.0;
        }
        return (($baselineMean - $currentValue) / $baselineMean) * 100.0;
    }

    /**
     * Compare two percentage changes with a percentage-point tie threshold.
     * Absolute difference < threshold → tie; equal to threshold is decisive.
     *
     * @return array{
     *   outcome: string,
     *   is_tie: bool,
     *   winner_side: ?string,
     *   margin: float,
     *   percentage_a: float,
     *   percentage_b: float,
     *   percentage_difference: float
     * }
     */
    public static function comparePercentages(
        float $percentageA,
        float $percentageB,
        float $baselineTieThreshold = 1.0
    ): array {
        $diff = $percentageA - $percentageB;
        $abs = abs($diff);
        if ($abs < $baselineTieThreshold) {
            return [
                'outcome' => MetricComparisonService::OUTCOME_TIE,
                'is_tie' => true,
                'winner_side' => null,
                'margin' => $abs,
                'percentage_a' => $percentageA,
                'percentage_b' => $percentageB,
                'percentage_difference' => $abs,
            ];
        }
        $winner = $diff > 0 ? 'a' : 'b';
        return [
            'outcome' => $winner === 'a'
                ? MetricComparisonService::OUTCOME_A
                : MetricComparisonService::OUTCOME_B,
            'is_tie' => false,
            'winner_side' => $winner,
            'margin' => $abs,
            'percentage_a' => $percentageA,
            'percentage_b' => $percentageB,
            'percentage_difference' => $abs,
        ];
    }

    /**
     * Daily Baseline outcome for a single day.
     *
     * @param array<string, mixed> $match
     * @param array{player_a: array<string, mixed>, player_b: array<string, mixed>} $baselines
     * @return array<string, mixed>
     */
    public static function dayOutcome(
        array $match,
        ?int $valueA,
        ?int $valueB,
        array $baselines
    ): array {
        $baseA = $baselines['player_a'] ?? [];
        $baseB = $baselines['player_b'] ?? [];
        $higherWins = (int) ($match['higher_wins'] ?? 1) === 1;
        $threshold = self::thresholdFromMatch($match);

        if (empty($baseA['available']) || empty($baseB['available'])) {
            return [
                'kind' => 'unavailable',
                'label' => 'Baseline unavailable',
                'winner_user_id' => null,
                'winner_side' => null,
                'percentage_a' => null,
                'percentage_b' => null,
                'value_a' => $valueA,
                'value_b' => $valueB,
                'baseline_a' => $baseA['mean'] ?? null,
                'baseline_b' => $baseB['mean'] ?? null,
                'margin' => null,
            ];
        }

        if ($valueA === null && $valueB === null) {
            return [
                'kind' => 'awaiting',
                'label' => 'Awaiting sync',
                'winner_user_id' => null,
                'winner_side' => null,
                'percentage_a' => null,
                'percentage_b' => null,
                'value_a' => null,
                'value_b' => null,
                'baseline_a' => (float) $baseA['mean'],
                'baseline_b' => (float) $baseB['mean'],
                'margin' => null,
            ];
        }
        if ($valueA === null || $valueB === null) {
            return [
                'kind' => 'partial',
                'label' => 'Awaiting sync',
                'winner_user_id' => null,
                'winner_side' => null,
                'percentage_a' => $valueA !== null
                    ? self::percentageChange($valueA, (float) $baseA['mean'], $higherWins)
                    : null,
                'percentage_b' => $valueB !== null
                    ? self::percentageChange($valueB, (float) $baseB['mean'], $higherWins)
                    : null,
                'value_a' => $valueA,
                'value_b' => $valueB,
                'baseline_a' => (float) $baseA['mean'],
                'baseline_b' => (float) $baseB['mean'],
                'margin' => null,
            ];
        }

        $pctA = self::percentageChange($valueA, (float) $baseA['mean'], $higherWins);
        $pctB = self::percentageChange($valueB, (float) $baseB['mean'], $higherWins);
        $cmp = self::comparePercentages($pctA, $pctB, $threshold);

        if ($cmp['is_tie']) {
            return [
                'kind' => 'tie',
                'label' => 'Tie',
                'winner_user_id' => null,
                'winner_side' => null,
                'percentage_a' => $pctA,
                'percentage_b' => $pctB,
                'value_a' => $valueA,
                'value_b' => $valueB,
                'baseline_a' => (float) $baseA['mean'],
                'baseline_b' => (float) $baseB['mean'],
                'margin' => $cmp['margin'],
                'raw_would_win' => self::rawWinnerSide($valueA, $valueB, $match),
            ];
        }

        $winnerSide = (string) $cmp['winner_side'];
        $winnerId = $winnerSide === 'a'
            ? (int) $match['player_a_user_id']
            : (int) $match['player_b_user_id'];

        return [
            'kind' => 'win',
            'label' => 'Win',
            'winner_user_id' => $winnerId,
            'winner_side' => $winnerSide,
            'percentage_a' => $pctA,
            'percentage_b' => $pctB,
            'value_a' => $valueA,
            'value_b' => $valueB,
            'baseline_a' => (float) $baseA['mean'],
            'baseline_b' => (float) $baseB['mean'],
            'margin' => $cmp['margin'],
            'raw_would_win' => self::rawWinnerSide($valueA, $valueB, $match),
        ];
    }

    /**
     * Presentation helpers for deltas against a baseline.
     *
     * @return array{raw_delta: ?float, percentage: ?float, label: string, signed_percentage: string}
     */
    public static function deltaPresentation(
        ?int $currentValue,
        ?float $baselineMean,
        bool $higherWins,
        ?array $metric = null
    ): array {
        if ($currentValue === null || $baselineMean === null || $baselineMean == 0.0) {
            return [
                'raw_delta' => null,
                'percentage' => null,
                'label' => '—',
                'signed_percentage' => '—',
            ];
        }
        $rawDelta = $currentValue - $baselineMean;
        $pct = self::percentageChange($currentValue, $baselineMean, $higherWins);
        $sign = $pct >= 0 ? '+' : '';
        $direction = $rawDelta >= 0 ? 'above' : 'below';
        $absDelta = abs($rawDelta);
        $metricLabel = MetricFormatter::format((int) round($absDelta), $metric);

        return [
            'raw_delta' => $rawDelta,
            'percentage' => $pct,
            'label' => sprintf(
                '%s %s recent baseline · %s%s%%',
                $metricLabel,
                $direction,
                $sign,
                number_format($pct, 1)
            ),
            'signed_percentage' => $sign . number_format($pct, 1) . '%',
        ];
    }

    public static function formatPercentage(?float $pct): string
    {
        if ($pct === null) {
            return '—';
        }
        $sign = $pct >= 0 ? '+' : '';
        return $sign . number_format($pct, 1) . '%';
    }

    public static function thresholdFromMatch(array $match): float
    {
        if (isset($match['baseline_tie_threshold']) && $match['baseline_tie_threshold'] !== null && $match['baseline_tie_threshold'] !== '') {
            return (float) $match['baseline_tie_threshold'];
        }
        return BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD;
    }

    private static function rawWinnerSide(int $valueA, int $valueB, array $match): ?string
    {
        $cmp = MetricComparisonService::compare(
            $valueA,
            $valueB,
            (int) $match['tie_threshold'],
            (int) ($match['higher_wins'] ?? 1) === 1
        );
        return $cmp['is_tie'] ? null : ($cmp['winner_side'] ?? null);
    }
}
