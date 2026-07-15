<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Derived personal records across official match-day results and canonical history.
 * Separates Classic and Baseline records. Neutral labels — no medical claims.
 */
final class PersonalRecordsService
{
    /**
     * @return array<string, mixed>
     */
    public static function forUser(int $userId): array
    {
        $rows = Database::fetchAll(
            'SELECT r.metric_value, r.user_id, d.status AS day_status, d.day_number,
                    m.id AS match_id, m.player_a_user_id, m.player_b_user_id, m.tie_threshold,
                    m.competition_type,
                    mt.slug, mt.name, mt.unit, mt.display_unit, mt.higher_wins, mt.scoring_strategy
             FROM rly_match_day_results r
             JOIN rly_match_days d ON d.id = r.match_day_id
             JOIN rly_matches m ON m.id = d.match_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE r.user_id = ?
               AND d.status = \'official\'
               AND m.invitation_status = \'accepted\'',
            [$userId]
        );

        $bySlug = [];
        foreach ($rows as $row) {
            $slug = (string) $row['slug'];
            $val = (int) $row['metric_value'];
            if (!isset($bySlug[$slug])) {
                $bySlug[$slug] = [
                    'slug' => $slug,
                    'name' => $row['name'],
                    'unit' => $row['unit'],
                    'display_unit' => $row['display_unit'],
                    'higher_wins' => (int) $row['higher_wins'] === 1,
                    'values' => [],
                ];
            }
            $bySlug[$slug]['values'][] = $val;
        }

        $records = [];
        $defs = [
            'steps' => ['label' => 'Highest daily steps', 'pick' => 'max'],
            'active_minutes' => ['label' => 'Most active minutes in one day', 'pick' => 'max'],
            'hrv' => ['label' => 'Highest recorded HRV', 'pick' => 'max'],
            'resting_heart_rate' => ['label' => 'Lowest recorded resting heart rate', 'pick' => 'min'],
            'sleep_duration' => ['label' => 'Longest recorded sleep duration', 'pick' => 'max'],
        ];

        foreach ($defs as $slug => $meta) {
            if (!isset($bySlug[$slug]) || $bySlug[$slug]['values'] === []) {
                $records[$slug] = [
                    'label' => $meta['label'],
                    'value' => null,
                    'formatted' => '—',
                    'metric' => null,
                ];
                continue;
            }
            $vals = $bySlug[$slug]['values'];
            $picked = $meta['pick'] === 'min' ? min($vals) : max($vals);
            $metric = $bySlug[$slug];
            $records[$slug] = [
                'label' => $meta['label'],
                'value' => $picked,
                'formatted' => MetricFormatter::format($picked, $metric),
                'metric' => $metric,
            ];
        }

        $competitionRecords = self::competitionRecords($userId);
        $baselines = self::recentBaselines($userId);

        $margins = [];
        $allMatches = Database::fetchAll(
            'SELECT id FROM rly_matches
             WHERE (player_a_user_id = ? OR player_b_user_id = ?)
               AND invitation_status = \'accepted\'',
            [$userId, $userId]
        );
        $longestStreak = 0;
        foreach ($allMatches as $mrow) {
            try {
                $pack = MatchScoringService::forMatchId((int) $mrow['id']);
            } catch (\Throwable) {
                continue;
            }
            $match = $pack['match'];
            $isA = (int) $match['player_a_user_id'] === $userId;
            foreach ($pack['days'] as $day) {
                if ((string) $day['status'] !== 'official') {
                    continue;
                }
                $outcome = MatchScoringService::dayOutcome($match, $day);
                if ($outcome['kind'] !== 'win') {
                    continue;
                }
                $won = ($isA && ($outcome['winner_side'] ?? '') === 'a')
                    || (!$isA && ($outcome['winner_side'] ?? '') === 'b');
                if ($won && $outcome['margin'] !== null && is_numeric($outcome['margin'])) {
                    $margins[] = (float) $outcome['margin'];
                }
            }
            $streak = $pack['summary']['current_streak'] ?? null;
            if (is_array($streak) && ($streak['user_id'] ?? null) === $userId) {
                $longestStreak = max($longestStreak, (int) ($streak['length'] ?? 0));
            }
        }

        return [
            'by_metric' => $records,
            'competition_records' => $competitionRecords,
            'recent_baselines' => $baselines,
            'largest_decisive_margin' => $margins === [] ? null : max($margins),
            'closest_decisive_win' => $margins === [] ? null : min($margins),
            'longest_daily_win_streak' => $longestStreak,
        ];
    }

    /**
     * Separate Classic and Baseline W–L–D plus per-metric breakdowns.
     *
     * @return array<string, mixed>
     */
    public static function competitionRecords(int $userId): array
    {
        $matches = Database::fetchAll(
            'SELECT m.*, mt.slug AS metric_slug, mt.name AS metric_name, mt.scoring_strategy
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE (m.player_a_user_id = ? OR m.player_b_user_id = ?)
               AND m.invitation_status = \'accepted\'
               AND m.status = \'completed\'',
            [$userId, $userId]
        );

        $classic = ['wins' => 0, 'losses' => 0, 'draws' => 0, 'by_metric' => []];
        $baseline = ['wins' => 0, 'losses' => 0, 'draws' => 0, 'by_metric' => []];

        foreach ($matches as $match) {
            try {
                $pack = MatchScoringService::forMatchId((int) $match['id']);
            } catch (\Throwable) {
                continue;
            }
            $summary = $pack['summary'];
            $type = MetricCompetitionService::competitionType($match);
            $key = $type === MetricCompetitionService::TYPE_BASELINE ? 'baseline' : 'classic';
            $slug = (string) $match['metric_slug'];
            if ($key === 'baseline') {
                if (!isset($baseline['by_metric'][$slug])) {
                    $baseline['by_metric'][$slug] = [
                        'slug' => $slug,
                        'name' => $match['metric_name'],
                        'wins' => 0,
                        'losses' => 0,
                        'draws' => 0,
                    ];
                }
                if (!empty($summary['is_draw'])) {
                    $baseline['draws']++;
                    $baseline['by_metric'][$slug]['draws']++;
                } elseif (($summary['leader_user_id'] ?? null) === $userId) {
                    $baseline['wins']++;
                    $baseline['by_metric'][$slug]['wins']++;
                } else {
                    $baseline['losses']++;
                    $baseline['by_metric'][$slug]['losses']++;
                }
            } else {
                if (!isset($classic['by_metric'][$slug])) {
                    $classic['by_metric'][$slug] = [
                        'slug' => $slug,
                        'name' => $match['metric_name'],
                        'wins' => 0,
                        'losses' => 0,
                        'draws' => 0,
                    ];
                }
                if (!empty($summary['is_draw'])) {
                    $classic['draws']++;
                    $classic['by_metric'][$slug]['draws']++;
                } elseif (($summary['leader_user_id'] ?? null) === $userId) {
                    $classic['wins']++;
                    $classic['by_metric'][$slug]['wins']++;
                } else {
                    $classic['losses']++;
                    $classic['by_metric'][$slug]['losses']++;
                }
            }
        }

        return [
            'classic' => $classic,
            'baseline' => $baseline,
            'classic_pool_note' => 'Five supported metrics',
            'baseline_pool_note' => 'Steps and Active Minutes',
        ];
    }

    /**
     * Estimated recent baselines per metric × source from canonical history.
     *
     * @return list<array<string, mixed>>
     */
    public static function recentBaselines(int $userId): array
    {
        $sources = Database::fetchAll(
            'SELECT DISTINCT umd.metric_type_id, umd.data_source_id, mt.slug, mt.name AS metric_name,
                    ds.name AS source_name, ds.slug AS source_slug
             FROM rly_user_metric_days umd
             JOIN rly_metric_types mt ON mt.id = umd.metric_type_id
             JOIN rly_data_sources ds ON ds.id = umd.data_source_id
             WHERE umd.user_id = ?
             ORDER BY mt.slug, ds.name',
            [$userId]
        );

        $asOf = Clock::now()->setTimezone(new \DateTimeZone('UTC'))->modify('+1 day')->format('Y-m-d');
        $out = [];
        foreach ($sources as $row) {
            $est = BaselineService::estimate(
                $userId,
                (int) $row['metric_type_id'],
                (int) $row['data_source_id'],
                $asOf
            );
            $out[] = [
                'metric_slug' => $row['slug'],
                'metric_name' => $row['metric_name'],
                'source_name' => $row['source_name'],
                'source_slug' => $row['source_slug'],
                'available' => $est['available'],
                'mean' => $est['mean'],
                'sample_count' => $est['sample_count'],
                'typical_range' => $est['typical_range'],
                'window_start_date' => $est['window_start_date'],
                'window_end_date' => $est['window_end_date'],
                'formatted_mean' => $est['available']
                    ? MetricFormatter::format((int) round((float) $est['mean']), [
                        'slug' => $row['slug'],
                        'name' => $row['metric_name'],
                    ])
                    : '—',
            ];
        }
        return $out;
    }
}
