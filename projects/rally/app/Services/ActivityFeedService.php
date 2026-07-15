<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Derived competition events for the Activity feed.
 * No social posts table — events are computed from matches and days.
 */
final class ActivityFeedService
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forUser(int $userId, int $limit = 40): array
    {
        $matchIds = Database::fetchAll(
            'SELECT id FROM rly_matches
             WHERE invitation_status = \'accepted\'
               AND (player_a_user_id = ? OR player_b_user_id = ?
                    OR player_a_user_id IN (SELECT id FROM rly_users WHERE simulation = 1)
                    OR player_b_user_id IN (SELECT id FROM rly_users WHERE simulation = 1))
             ORDER BY updated_at DESC
             LIMIT 40',
            [$userId, $userId]
        );

        $events = [];
        foreach ($matchIds as $row) {
            try {
                $pack = MatchScoringService::forMatchId((int) $row['id']);
            } catch (\Throwable) {
                continue;
            }
            foreach (self::eventsFromPack($pack) as $event) {
                $events[] = $event;
            }
        }

        usort($events, static function (array $a, array $b): int {
            return strcmp((string) $b['sort_at'], (string) $a['sort_at']);
        });

        return array_slice($events, 0, $limit);
    }

    /**
     * @param array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>} $pack
     * @return list<array<string, mixed>>
     */
    public static function eventsFromPack(array $pack): array
    {
        $m = $pack['match'];
        $s = $pack['summary'];
        $events = [];
        $isAvg = MetricCompetitionService::isSeriesAverage($m);
        $metric = MetricCompetitionService::metricPayload($m);

        if ((string) $m['status'] === 'completed') {
            $events[] = [
                'type' => !empty($s['is_draw']) ? 'match_drawn' : 'match_completed',
                'sort_at' => (string) ($m['completed_at'] ?? $m['updated_at']),
                'match_id' => (int) $m['id'],
                'headline' => !empty($s['is_draw']) ? 'Match drawn' : 'Match final',
                'body' => self::finalBody($m, $s, $isAvg),
                'metric_name' => $m['metric_name'],
                'url' => '/matches/' . (int) $m['id'],
            ];
            $events[] = [
                'type' => 'match_official',
                'sort_at' => (string) ($m['completed_at'] ?? $m['updated_at']),
                'match_id' => (int) $m['id'],
                'headline' => 'Match becomes official',
                'body' => $m['player_a_name'] . ' vs ' . $m['player_b_name'] . ' · ' . $m['metric_name'],
                'metric_name' => $m['metric_name'],
                'url' => '/matches/' . (int) $m['id'],
            ];
        }

        if ((string) $m['status'] === 'settling') {
            $events[] = [
                'type' => 'match_settling',
                'sort_at' => (string) $m['updated_at'],
                'match_id' => (int) $m['id'],
                'headline' => 'Match entering settlement',
                'body' => $m['player_a_name'] . ' vs ' . $m['player_b_name'] . ' · '
                    . (int) $s['pending_days'] . ' provisional',
                'metric_name' => $m['metric_name'],
                'url' => '/matches/' . (int) $m['id'],
            ];
        }

        foreach ($pack['days'] as $day) {
            $status = (string) $day['status'];
            $outcome = MatchScoringService::dayOutcome($m, $day);
            $dayNum = (int) $day['day_number'];

            if ($status === 'live' && $dayNum === (int) $m['length_days']) {
                $events[] = [
                    'type' => 'final_day',
                    'sort_at' => (string) ($day['updated_at'] ?? $m['updated_at']),
                    'match_id' => (int) $m['id'],
                    'headline' => 'Final day begins',
                    'body' => $m['player_a_name'] . ' vs ' . $m['player_b_name'] . ' · Game ' . $dayNum,
                    'metric_name' => $m['metric_name'],
                    'url' => '/matches/' . (int) $m['id'],
                ];
            }

            if ($status !== 'official') {
                continue;
            }

            $sortAt = (string) ($day['official_at'] ?? $day['updated_at']);
            $valA = $outcome['value_a'];
            $valB = $outcome['value_b'];
            $line = MetricFormatter::format($valA, $metric) . ' vs ' . MetricFormatter::format($valB, $metric);

            if ($outcome['kind'] === 'tie') {
                $headline = $isAvg ? 'Daily comparison level' : 'Game ' . $dayNum . ' final — tie';
                $body = $line;
            } elseif ($outcome['kind'] === 'win') {
                $winner = ($outcome['winner_side'] ?? '') === 'a' ? $m['player_a_name'] : $m['player_b_name'];
                $loser = ($outcome['winner_side'] ?? '') === 'a' ? $m['player_b_name'] : $m['player_a_name'];
                $margin = (int) ($outcome['margin'] ?? 0);
                $close = $margin <= max(1, (int) $m['tie_threshold'] * 2);
                $headline = $isAvg
                    ? ('Day ' . $dayNum . ' comparison')
                    : ('Game ' . $dayNum . ' final');
                $body = $winner . ($isAvg ? ' leads ' : ' defeats ') . $loser . ' · ' . $line;
                if (!$isAvg) {
                    $series = $m['player_a_name'] . ' ' . (int) $s['player_a_wins'] . '–' . (int) $s['player_b_wins'] . ' ' . $m['player_b_name'];
                    $body .= ' · Series: ' . $series;
                } else {
                    $body .= ' · Avg '
                        . MetricFormatter::formatCompact($s['player_a_average'] ?? null, $metric)
                        . ' / '
                        . MetricFormatter::formatCompact($s['player_b_average'] ?? null, $metric);
                }
                if ($close) {
                    $events[] = [
                        'type' => 'close_result',
                        'sort_at' => $sortAt,
                        'match_id' => (int) $m['id'],
                        'headline' => 'Close result',
                        'body' => $body,
                        'metric_name' => $m['metric_name'],
                        'url' => '/matches/' . (int) $m['id'] . '/day/' . $dayNum,
                    ];
                } elseif ($margin >= max(3, (int) $m['tie_threshold'] * 5)) {
                    $events[] = [
                        'type' => 'decisive_result',
                        'sort_at' => $sortAt,
                        'match_id' => (int) $m['id'],
                        'headline' => 'Decisive result',
                        'body' => $body,
                        'metric_name' => $m['metric_name'],
                        'url' => '/matches/' . (int) $m['id'] . '/day/' . $dayNum,
                    ];
                }
            } else {
                continue;
            }

            $events[] = [
                'type' => 'daily_final',
                'sort_at' => $sortAt,
                'match_id' => (int) $m['id'],
                'headline' => $headline,
                'body' => $body ?? $line,
                'metric_name' => $m['metric_name'],
                'url' => '/matches/' . (int) $m['id'] . '/day/' . $dayNum . ($status === 'official' ? '/share' : ''),
            ];
        }

        if (!$isAvg && !empty($s['current_streak']) && (int) ($s['current_streak']['length'] ?? 0) >= 3) {
            $streakUser = (int) $s['current_streak']['user_id'];
            $name = $streakUser === (int) $m['player_a_user_id'] ? $m['player_a_name'] : $m['player_b_name'];
            $events[] = [
                'type' => 'winning_streak',
                'sort_at' => (string) $m['updated_at'],
                'match_id' => (int) $m['id'],
                'headline' => 'Winning streak',
                'body' => $name . ' · ' . (int) $s['current_streak']['length'] . ' consecutive daily wins',
                'metric_name' => $m['metric_name'],
                'url' => '/matches/' . (int) $m['id'],
            ];
        }

        return $events;
    }

    /** @param array<string, mixed> $m @param array<string, mixed> $s */
    private static function finalBody(array $m, array $s, bool $isAvg): string
    {
        if ($isAvg) {
            $metric = MetricCompetitionService::metricPayload($m);
            $line = MetricFormatter::format($s['player_a_average'] ?? null, $metric)
                . ' vs '
                . MetricFormatter::format($s['player_b_average'] ?? null, $metric);
            if (!empty($s['is_draw'])) {
                return $m['player_a_name'] . ' and ' . $m['player_b_name'] . ' draw · ' . $line;
            }
            $winner = ((int) ($s['leader_user_id'] ?? 0) === (int) $m['player_a_user_id'])
                ? $m['player_a_name'] : $m['player_b_name'];
            return $winner . ' wins · ' . $line;
        }

        $score = (int) $s['player_a_wins'] . '–' . (int) $s['player_b_wins'];
        if (!empty($s['is_draw'])) {
            return $m['player_a_name'] . ' ' . $score . ' ' . $m['player_b_name'] . ' · Draw';
        }
        $winner = ((int) ($s['leader_user_id'] ?? 0) === (int) $m['player_a_user_id'])
            ? $m['player_a_name'] : $m['player_b_name'];
        return $winner . ' · ' . $m['player_a_name'] . ' ' . $score . ' ' . $m['player_b_name'];
    }
}
