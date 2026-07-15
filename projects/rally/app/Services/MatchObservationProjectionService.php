<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Projects canonical observations into eligible match-day snapshots.
 * Never writes rly_match_day_results directly — always through ResultIngestionService.
 */
final class MatchObservationProjectionService
{
    /**
     * @param array<string, mixed> $observation rly_user_metric_days row
     * @return list<array{match_id: int, match_day_id: int, status: string, result?: array<string, mixed>, error?: string}>
     */
    public static function projectObservation(array $observation, bool $adminOverride = false): array
    {
        $userId = (int) ($observation['user_id'] ?? 0);
        $metricTypeId = (int) ($observation['metric_type_id'] ?? 0);
        $dataSourceId = (int) ($observation['data_source_id'] ?? 0);
        $observationDate = (string) ($observation['observation_date'] ?? '');
        $value = (int) ($observation['metric_value'] ?? 0);

        if ($userId < 1 || $metricTypeId < 1 || $dataSourceId < 1 || $observationDate === '') {
            return [];
        }

        $metric = Database::fetch('SELECT slug FROM rly_metric_types WHERE id = ?', [$metricTypeId]);
        $metricSlug = (string) ($metric['slug'] ?? '');

        // Eligible days: same user membership, metric, competition date, and declared source.
        $days = Database::fetchAll(
            'SELECT d.id AS match_day_id, d.match_id, d.status AS day_status, d.competition_date,
                    m.player_a_user_id, m.player_b_user_id,
                    m.player_a_source_id, m.player_b_source_id,
                    m.metric_type_id, m.status AS match_status, m.invitation_status
             FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             WHERE d.competition_date = ?
               AND m.metric_type_id = ?
               AND m.invitation_status = \'accepted\'
               AND (m.player_a_user_id = ? OR m.player_b_user_id = ?)',
            [$observationDate, $metricTypeId, $userId, $userId]
        );

        $results = [];
        foreach ($days as $day) {
            $isA = (int) $day['player_a_user_id'] === $userId;
            $isB = (int) $day['player_b_user_id'] === $userId;
            if (!$isA && !$isB) {
                continue;
            }

            $declaredSource = $isA
                ? (int) $day['player_a_source_id']
                : (int) ($day['player_b_source_id'] ?? 0);

            if ($declaredSource < 1 || $declaredSource !== $dataSourceId) {
                $results[] = [
                    'match_id' => (int) $day['match_id'],
                    'match_day_id' => (int) $day['match_day_id'],
                    'status' => 'source_mismatch',
                    'error' => 'Declared match source does not match observation source.',
                ];
                continue;
            }

            $dayStatus = (string) $day['day_status'];
            if (!$adminOverride && in_array($dayStatus, ['official', 'void'], true)) {
                $results[] = [
                    'match_id' => (int) $day['match_id'],
                    'match_day_id' => (int) $day['match_day_id'],
                    'status' => 'locked',
                    'error' => 'Official or void days reject ordinary projection updates.',
                ];
                continue;
            }

            try {
                $result = ResultIngestionService::ingest([
                    'user_id' => $userId,
                    'match_day_id' => (int) $day['match_day_id'],
                    'metric_type' => $metricSlug,
                    'value' => $value,
                    'data_source' => $dataSourceId,
                    'source_record_key' => $observation['source_record_key'] ?? null,
                    'is_manual' => (int) ($observation['is_manual'] ?? 0) === 1,
                    'ingested_at' => $observation['ingested_at'] ?? null,
                ], $adminOverride);
                $results[] = [
                    'match_id' => (int) $day['match_id'],
                    'match_day_id' => (int) $day['match_day_id'],
                    'status' => 'projected',
                    'result' => $result,
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'match_id' => (int) $day['match_id'],
                    'match_day_id' => (int) $day['match_day_id'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Preview which matches would receive a canonical observation.
     *
     * @return list<array<string, mixed>>
     */
    public static function previewAffectedMatches(
        int $userId,
        int $metricTypeId,
        int $dataSourceId,
        string $observationDate
    ): array {
        $days = Database::fetchAll(
            'SELECT d.id AS match_day_id, d.match_id, d.status AS day_status, d.day_number, d.competition_date,
                    m.player_a_user_id, m.player_b_user_id, m.player_a_source_id, m.player_b_source_id,
                    m.competition_type, mt.slug AS metric_slug, mt.name AS metric_name,
                    ua.name AS player_a_name, ub.name AS player_b_name
             FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_users ub ON ub.id = m.player_b_user_id
             WHERE d.competition_date = ?
               AND m.metric_type_id = ?
               AND m.invitation_status = \'accepted\'
               AND (m.player_a_user_id = ? OR m.player_b_user_id = ?)
             ORDER BY m.id ASC',
            [$observationDate, $metricTypeId, $userId, $userId]
        );

        $out = [];
        foreach ($days as $day) {
            $isA = (int) $day['player_a_user_id'] === $userId;
            $declared = $isA
                ? (int) $day['player_a_source_id']
                : (int) ($day['player_b_source_id'] ?? 0);
            $eligible = $declared === $dataSourceId
                && !in_array((string) $day['day_status'], ['official', 'void'], true);
            $out[] = [
                'match_id' => (int) $day['match_id'],
                'match_day_id' => (int) $day['match_day_id'],
                'day_number' => (int) $day['day_number'],
                'day_status' => (string) $day['day_status'],
                'competition_type' => (string) ($day['competition_type'] ?? 'classic'),
                'metric_slug' => (string) $day['metric_slug'],
                'label' => $day['player_a_name'] . ' vs ' . $day['player_b_name'],
                'source_matches' => $declared === $dataSourceId,
                'eligible' => $eligible,
            ];
        }
        return $out;
    }
}
