<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Derived personal records across official match-day results.
 * Neutral labels — no medical superiority claims.
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

        // Largest / closest decisive margins (daily comparisons where user won)
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
                if ($won && $outcome['margin'] !== null) {
                    $margins[] = (int) $outcome['margin'];
                }
            }
            $streak = $pack['summary']['current_streak'] ?? null;
            if (is_array($streak) && ($streak['user_id'] ?? null) === $userId) {
                $longestStreak = max($longestStreak, (int) ($streak['length'] ?? 0));
            }
        }

        return [
            'by_metric' => $records,
            'largest_decisive_margin' => $margins === [] ? null : max($margins),
            'closest_decisive_win' => $margins === [] ? null : min($margins),
            'longest_daily_win_streak' => $longestStreak,
        ];
    }
}
