<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Development simulation controls. Primary path edits canonical observations
 * via UserMetricDayService; a legacy match-day editor remains for debugging.
 */
final class SimulationService
{
    public static function advanceTo(\DateTimeImmutable $instant): void
    {
        Clock::setOverride($instant->setTimezone(new \DateTimeZone('UTC')));
        SettlementService::refreshAllOpenMatches();
    }

    public static function advanceDays(int $days): \DateTimeImmutable
    {
        $current = Clock::now();
        $next = $current->modify(($days >= 0 ? '+' : '') . $days . ' days');
        self::advanceTo($next);
        return $next;
    }

    public static function clearClock(): void
    {
        Clock::clearOverride();
        SettlementService::refreshAllOpenMatches();
    }

    /**
     * Canonical observation update — preferred simulation path.
     *
     * @param array{
     *   user_id: int,
     *   metric_type: string|int,
     *   data_source: string|int,
     *   observation_date: string,
     *   value: int,
     *   project?: bool
     * } $input
     * @return array{observation: array<string, mixed>, projections: list<array<string, mixed>>, preview: list<array<string, mixed>>}
     */
    public static function ingestCanonical(array $input): array
    {
        $result = UserMetricDayService::ingest([
            'user_id' => (int) $input['user_id'],
            'metric_type' => $input['metric_type'],
            'data_source' => $input['data_source'],
            'observation_date' => (string) $input['observation_date'],
            'value' => (int) $input['value'],
            'source_record_key' => 'sim-canonical-' . (int) $input['user_id'] . '-' . $input['metric_type'] . '-' . $input['observation_date'],
            'is_manual' => true,
            'project' => array_key_exists('project', $input) ? (bool) $input['project'] : true,
        ], true);

        $metric = is_numeric($input['metric_type'])
            ? Database::fetch('SELECT id FROM rly_metric_types WHERE id = ?', [(int) $input['metric_type']])
            : Database::fetch('SELECT id FROM rly_metric_types WHERE slug = ?', [(string) $input['metric_type']]);
        $sourceId = is_numeric($input['data_source'])
            ? (int) $input['data_source']
            : (int) (Database::fetchValue('SELECT id FROM rly_data_sources WHERE slug = ?', [(string) $input['data_source']]) ?? 0);

        $preview = MatchObservationProjectionService::previewAffectedMatches(
            (int) $input['user_id'],
            (int) ($metric['id'] ?? 0),
            $sourceId,
            (string) $input['observation_date']
        );

        return [
            'observation' => $result['observation'],
            'projections' => $result['projections'],
            'preview' => $preview,
        ];
    }

    /**
     * Legacy match-day editor — labeled for debugging only.
     *
     * @param array{
     *   match_day_id: int,
     *   value_a?: ?int,
     *   value_b?: ?int,
     *   source_a?: string|int|null,
     *   source_b?: string|int|null,
     *   settle?: bool,
     *   day_status?: ?string
     * } $input
     */
    public static function updateDay(array $input): void
    {
        $matchDayId = (int) $input['match_day_id'];
        $day = Database::fetch(
            'SELECT d.*, m.player_a_user_id, m.player_b_user_id, m.player_a_source_id, m.player_b_source_id,
                    m.id AS match_id, mt.slug AS metric_slug
             FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE d.id = ?',
            [$matchDayId]
        );
        if ($day === null) {
            throw new \RuntimeException('Match day not found.');
        }

        $sourceA = $input['source_a'] ?? (int) $day['player_a_source_id'];
        $sourceB = $input['source_b'] ?? (int) ($day['player_b_source_id'] ?? $day['player_a_source_id']);

        if (array_key_exists('value_a', $input) && $input['value_a'] !== null && $input['value_a'] !== '') {
            UserMetricDayService::ingest([
                'user_id' => (int) $day['player_a_user_id'],
                'metric_type' => (string) $day['metric_slug'],
                'observation_date' => (string) $day['competition_date'],
                'value' => (int) $input['value_a'],
                'data_source' => $sourceA,
                'source_record_key' => 'sim-a-' . $matchDayId,
                'is_manual' => true,
            ], true);
        }

        if (array_key_exists('value_b', $input) && $input['value_b'] !== null && $input['value_b'] !== '') {
            UserMetricDayService::ingest([
                'user_id' => (int) $day['player_b_user_id'],
                'metric_type' => (string) $day['metric_slug'],
                'observation_date' => (string) $day['competition_date'],
                'value' => (int) $input['value_b'],
                'data_source' => $sourceB,
                'source_record_key' => 'sim-b-' . $matchDayId,
                'is_manual' => true,
            ], true);
        }

        if (!empty($input['day_status'])) {
            $allowed = ['scheduled', 'live', 'pending', 'official', 'void'];
            $status = (string) $input['day_status'];
            if (!in_array($status, $allowed, true)) {
                throw new \InvalidArgumentException('Invalid day status.');
            }
            $now = Clock::nowUtcString();
            $officialAt = in_array($status, ['official', 'void'], true) ? $now : null;
            Database::run(
                'UPDATE rly_match_days SET status = ?, official_at = ?, updated_at = ? WHERE id = ?',
                [$status, $officialAt, $now, $matchDayId]
            );
        }

        if (!empty($input['settle'])) {
            SettlementService::settleDayNow($matchDayId);
        } else {
            SettlementService::refreshMatch((int) $day['match_id']);
        }
    }

    /** @return list<array<string, mixed>> */
    public static function listMatches(): array
    {
        return Database::fetchAll(
            'SELECT m.*, ua.name AS player_a_name, ub.name AS player_b_name, mt.name AS metric_name
             FROM rly_matches m
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_users ub ON ub.id = m.player_b_user_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             ORDER BY m.id ASC'
        );
    }

    /** @return list<array<string, mixed>> */
    public static function listUsers(): array
    {
        return Database::fetchAll('SELECT id, name, username FROM rly_users WHERE status = \'active\' ORDER BY name');
    }

    /** @return list<array<string, mixed>> */
    public static function listMetrics(): array
    {
        return Database::fetchAll('SELECT * FROM rly_metric_types WHERE is_active = 1 ORDER BY name');
    }
}
