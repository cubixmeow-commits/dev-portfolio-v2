<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Personal baseline calculation and frozen match snapshots.
 * Baselines come only from rly_user_metric_days for a single declared source.
 */
final class BaselineService
{
    public const PREFERRED_SAMPLE_DAYS = 30;
    public const MINIMUM_SAMPLE_DAYS = 7;
    public const DEFAULT_BASELINE_TIE_THRESHOLD = 1.0;

    /** Metrics that may use Baseline competitions in V1. */
    public const BASELINE_ELIGIBLE_METRICS = ['steps', 'active_minutes'];

    public static function supportsBaseline(array $metricOrMatch): bool
    {
        $slug = (string) ($metricOrMatch['metric_slug'] ?? $metricOrMatch['slug'] ?? '');
        $strategy = (string) ($metricOrMatch['scoring_strategy'] ?? '');
        if ($slug === '' || $strategy === '') {
            return false;
        }
        return in_array($slug, self::BASELINE_ELIGIBLE_METRICS, true)
            && $strategy === MetricCompetitionService::STRATEGY_DAILY_WINS;
    }

    /**
     * Estimated (not frozen) baseline summary for preview / acceptance.
     *
     * @return array<string, mixed>
     */
    public static function estimate(
        int $userId,
        int $metricTypeId,
        int $dataSourceId,
        string $matchStartDate
    ): array {
        $calc = self::calculate($userId, $metricTypeId, $dataSourceId, $matchStartDate);
        $source = Database::fetch('SELECT * FROM rly_data_sources WHERE id = ?', [$dataSourceId]);
        $metric = Database::fetch('SELECT * FROM rly_metric_types WHERE id = ?', [$metricTypeId]);

        return [
            'available' => $calc['available'],
            'reason' => $calc['reason'],
            'mean' => $calc['mean'],
            'median' => $calc['median'],
            'standard_deviation' => $calc['standard_deviation'],
            'minimum' => $calc['minimum'],
            'maximum' => $calc['maximum'],
            'sample_count' => $calc['sample_count'],
            'window_start_date' => $calc['window_start_date'],
            'window_end_date' => $calc['window_end_date'],
            'source_id' => $dataSourceId,
            'source_name' => $source['name'] ?? null,
            'source_slug' => $source['slug'] ?? null,
            'metric_slug' => $metric['slug'] ?? null,
            'typical_range' => ($calc['available'] && $calc['minimum'] !== null && $calc['maximum'] !== null)
                ? [
                    'minimum' => $calc['minimum'],
                    'maximum' => $calc['maximum'],
                    'label' => MetricFormatter::format((int) $calc['minimum'], $metric)
                        . ' – '
                        . MetricFormatter::format((int) $calc['maximum'], $metric),
                ]
                : null,
            'preferred_sample_days' => self::PREFERRED_SAMPLE_DAYS,
            'minimum_sample_days' => self::MINIMUM_SAMPLE_DAYS,
        ];
    }

    /**
     * Core math over eligible historical observations.
     *
     * @return array{
     *   available: bool,
     *   reason: ?string,
     *   mean: ?float,
     *   median: ?float,
     *   standard_deviation: ?float,
     *   minimum: ?int,
     *   maximum: ?int,
     *   sample_count: int,
     *   window_start_date: ?string,
     *   window_end_date: ?string,
     *   values: list<int>
     * }
     */
    public static function calculate(
        int $userId,
        int $metricTypeId,
        int $dataSourceId,
        string $matchStartDate
    ): array {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $matchStartDate)) {
            throw new \InvalidArgumentException('Match start date must be YYYY-MM-DD.');
        }

        $rows = Database::fetchAll(
            'SELECT observation_date, metric_value
             FROM rly_user_metric_days
             WHERE user_id = ?
               AND metric_type_id = ?
               AND data_source_id = ?
               AND observation_date < ?
               AND metric_value IS NOT NULL
             ORDER BY observation_date DESC
             LIMIT ' . self::PREFERRED_SAMPLE_DAYS,
            [$userId, $metricTypeId, $dataSourceId, $matchStartDate]
        );

        $values = [];
        $dates = [];
        foreach ($rows as $row) {
            $values[] = (int) $row['metric_value'];
            $dates[] = (string) $row['observation_date'];
        }

        $count = count($values);
        if ($count < self::MINIMUM_SAMPLE_DAYS) {
            return [
                'available' => false,
                'reason' => sprintf(
                    'Only %d eligible days were found. At least %d are required.',
                    $count,
                    self::MINIMUM_SAMPLE_DAYS
                ),
                'mean' => null,
                'median' => null,
                'standard_deviation' => null,
                'minimum' => $count > 0 ? min($values) : null,
                'maximum' => $count > 0 ? max($values) : null,
                'sample_count' => $count,
                'window_start_date' => $dates === [] ? null : min($dates),
                'window_end_date' => $dates === [] ? null : max($dates),
                'values' => $values,
            ];
        }

        $mean = array_sum($values) / $count;
        $median = self::median($values);
        $stdDev = self::sampleStandardDeviation($values, $mean);

        return [
            'available' => true,
            'reason' => null,
            'mean' => $mean,
            'median' => $median,
            'standard_deviation' => $stdDev,
            'minimum' => min($values),
            'maximum' => max($values),
            'sample_count' => $count,
            'window_start_date' => min($dates),
            'window_end_date' => max($dates),
            'values' => $values,
        ];
    }

    /**
     * Freeze both players' baselines for a match. Requires both sources known.
     * Does not overwrite an existing frozen row.
     *
     * @return array{player_a: array<string, mixed>, player_b: array<string, mixed>}
     */
    public static function freezeForMatch(int $matchId, bool $requireAvailable = false): array
    {
        $match = Database::fetch(
            'SELECT m.*, mt.slug AS metric_slug, mt.scoring_strategy
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE m.id = ?',
            [$matchId]
        );
        if ($match === null) {
            throw new \RuntimeException('Match not found.');
        }
        if ($match['player_b_source_id'] === null) {
            throw new \RuntimeException('Cannot freeze baselines until both sources are declared.');
        }

        $now = Clock::nowUtcString();
        $snapshots = [];
        foreach (
            [
                'player_a' => [
                    'user_id' => (int) $match['player_a_user_id'],
                    'source_id' => (int) $match['player_a_source_id'],
                ],
                'player_b' => [
                    'user_id' => (int) $match['player_b_user_id'],
                    'source_id' => (int) $match['player_b_source_id'],
                ],
            ] as $side => $ids
        ) {
            $existing = Database::fetch(
                'SELECT * FROM rly_match_baselines WHERE match_id = ? AND user_id = ?',
                [$matchId, $ids['user_id']]
            );
            if ($existing !== null) {
                $snapshots[$side] = self::formatSnapshot($existing);
                continue;
            }

            $calc = self::calculate(
                $ids['user_id'],
                (int) $match['metric_type_id'],
                $ids['source_id'],
                (string) $match['start_date']
            );

            if (!$calc['available']) {
                if ($requireAvailable) {
                    $who = $side === 'player_a' ? 'Player A' : 'Player B';
                    throw new \RuntimeException(
                        "Baseline unavailable for {$who}. " . (string) $calc['reason']
                    );
                }
                $snapshots[$side] = [
                    'available' => false,
                    'reason' => $calc['reason'],
                    'sample_count' => $calc['sample_count'],
                ];
                continue;
            }

            Database::run(
                'INSERT INTO rly_match_baselines
                 (match_id, user_id, metric_type_id, baseline_mean, baseline_median,
                  baseline_standard_deviation, baseline_minimum, baseline_maximum, sample_count,
                  window_start_date, window_end_date, calculated_at, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $matchId,
                    $ids['user_id'],
                    (int) $match['metric_type_id'],
                    $calc['mean'],
                    $calc['median'],
                    $calc['standard_deviation'],
                    $calc['minimum'],
                    $calc['maximum'],
                    $calc['sample_count'],
                    $calc['window_start_date'],
                    $calc['window_end_date'],
                    $now,
                    $now,
                ]
            );
            $row = Database::fetch(
                'SELECT * FROM rly_match_baselines WHERE match_id = ? AND user_id = ?',
                [$matchId, $ids['user_id']]
            );
            $snapshots[$side] = self::formatSnapshot($row ?? []);
        }

        return [
            'player_a' => $snapshots['player_a'],
            'player_b' => $snapshots['player_b'],
        ];
    }

    /**
     * @return array{player_a: array<string, mixed>, player_b: array<string, mixed>}
     */
    public static function contextForMatch(array $match): array
    {
        $matchId = (int) $match['id'];
        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];

        $rows = Database::fetchAll(
            'SELECT * FROM rly_match_baselines WHERE match_id = ?',
            [$matchId]
        );
        $byUser = [];
        foreach ($rows as $row) {
            $byUser[(int) $row['user_id']] = self::formatSnapshot($row);
        }

        return [
            'player_a' => $byUser[$playerA] ?? ['available' => false, 'reason' => 'No frozen baseline'],
            'player_b' => $byUser[$playerB] ?? ['available' => false, 'reason' => 'No frozen baseline'],
        ];
    }

    /**
     * Availability check for acceptance / create flows.
     *
     * @return array{ok: bool, player_a: array<string, mixed>, player_b: array<string, mixed>, message: ?string}
     */
    public static function availabilityForSources(
        int $metricTypeId,
        string $startDate,
        int $userA,
        int $sourceA,
        int $userB,
        int $sourceB
    ): array {
        $a = self::estimate($userA, $metricTypeId, $sourceA, $startDate);
        $b = self::estimate($userB, $metricTypeId, $sourceB, $startDate);
        $ok = !empty($a['available']) && !empty($b['available']);
        $message = null;
        if (!$ok) {
            $parts = [];
            if (empty($a['available'])) {
                $parts[] = 'Player A: ' . (string) ($a['reason'] ?? 'insufficient history');
            }
            if (empty($b['available'])) {
                $parts[] = 'Player B: ' . (string) ($b['reason'] ?? 'insufficient history');
            }
            $message = 'Baseline unavailable. ' . implode(' ', $parts);
        }
        return [
            'ok' => $ok,
            'player_a' => $a,
            'player_b' => $b,
            'message' => $message,
        ];
    }

    /** @param list<int> $values */
    public static function median(array $values): float
    {
        $n = count($values);
        if ($n === 0) {
            throw new \InvalidArgumentException('Cannot compute median of empty set.');
        }
        $sorted = $values;
        sort($sorted, SORT_NUMERIC);
        $mid = intdiv($n, 2);
        if ($n % 2 === 1) {
            return (float) $sorted[$mid];
        }
        return ((float) $sorted[$mid - 1] + (float) $sorted[$mid]) / 2.0;
    }

    /** @param list<int|float> $values */
    public static function sampleStandardDeviation(array $values, ?float $mean = null): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }
        $mean ??= array_sum($values) / $n;
        $sumSquares = 0.0;
        foreach ($values as $v) {
            $diff = ((float) $v) - $mean;
            $sumSquares += $diff * $diff;
        }
        return sqrt($sumSquares / ($n - 1));
    }

    /** @param array<string, mixed> $row */
    private static function formatSnapshot(array $row): array
    {
        if ($row === []) {
            return ['available' => false, 'reason' => 'No frozen baseline'];
        }
        return [
            'available' => true,
            'id' => (int) ($row['id'] ?? 0),
            'match_id' => (int) ($row['match_id'] ?? 0),
            'user_id' => (int) ($row['user_id'] ?? 0),
            'metric_type_id' => (int) ($row['metric_type_id'] ?? 0),
            'mean' => (float) $row['baseline_mean'],
            'median' => (float) $row['baseline_median'],
            'standard_deviation' => (float) $row['baseline_standard_deviation'],
            'minimum' => (int) $row['baseline_minimum'],
            'maximum' => (int) $row['baseline_maximum'],
            'sample_count' => (int) $row['sample_count'],
            'window_start_date' => (string) $row['window_start_date'],
            'window_end_date' => (string) $row['window_end_date'],
            'calculated_at' => (string) ($row['calculated_at'] ?? ''),
        ];
    }
}
