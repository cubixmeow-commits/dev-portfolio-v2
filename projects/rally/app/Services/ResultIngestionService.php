<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Config;
use Rally\Core\Database;

/**
 * Normalized health-result ingestion boundary.
 *
 * Future HealthKit / Health Connect connectors should call ingest() with a
 * payload shaped like:
 *
 * {
 *   "user_id": 1,
 *   "match_day_id": 12,
 *   "metric_type": "steps",
 *   "value": 11248,
 *   "data_source": "apple_watch",
 *   "source_record_key": "external-record-key",
 *   "is_manual": false,
 *   "ingested_at": "2026-07-21T08:14:00Z"
 * }
 *
 * Seed data and the simulation page use this same pathway.
 */
final class ResultIngestionService
{
    /**
     * @param array{
     *   user_id: int,
     *   match_day_id: int,
     *   metric_type?: string,
     *   value: int,
     *   data_source: string|int,
     *   source_record_key?: ?string,
     *   is_manual?: bool,
     *   ingested_at?: ?string
     * } $payload
     * @return array<string, mixed>
     */
    public static function ingest(array $payload, bool $adminOverride = false): array
    {
        $userId = (int) ($payload['user_id'] ?? 0);
        $matchDayId = (int) ($payload['match_day_id'] ?? 0);
        $value = (int) ($payload['value'] ?? -1);
        $isManual = (bool) ($payload['is_manual'] ?? false);
        $sourceRecordKey = isset($payload['source_record_key']) && $payload['source_record_key'] !== ''
            ? (string) $payload['source_record_key']
            : null;

        if ($userId < 1 || $matchDayId < 1) {
            throw new \InvalidArgumentException('user_id and match_day_id are required.');
        }

        $min = Config::int('app.metric_value_min', 0);
        $max = Config::int('app.metric_value_max', 100000);
        if ($value < $min || $value > $max) {
            throw new \InvalidArgumentException("Metric value must be between {$min} and {$max}.");
        }

        $day = Database::fetch(
            'SELECT d.*, m.player_a_user_id, m.player_b_user_id, m.player_a_source_id, m.player_b_source_id,
                    m.status AS match_status, m.metric_type_id, mt.slug AS metric_slug
             FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE d.id = ?',
            [$matchDayId]
        );
        if ($day === null) {
            throw new \InvalidArgumentException('Match day not found.');
        }

        $playerA = (int) $day['player_a_user_id'];
        $playerB = (int) $day['player_b_user_id'];
        if ($userId !== $playerA && $userId !== $playerB) {
            throw new \RuntimeException('User is not a member of this match.');
        }

        if (!$adminOverride) {
            $status = (string) $day['status'];
            if ($status === 'official' || $status === 'void') {
                throw new \RuntimeException('This day is locked. Ordinary ingestion cannot change official or void results.');
            }
            if (in_array((string) $day['match_status'], ['completed', 'cancelled'], true)) {
                throw new \RuntimeException('This match is closed to ordinary updates.');
            }
        }

        if (isset($payload['metric_type']) && $payload['metric_type'] !== ''
            && (string) $payload['metric_type'] !== (string) $day['metric_slug']) {
            throw new \InvalidArgumentException('Metric type does not match this match.');
        }

        $sourceId = self::resolveSourceId($payload['data_source'] ?? null);
        $source = Database::fetch('SELECT * FROM rly_data_sources WHERE id = ? AND is_active = 1', [$sourceId]);
        if ($source === null) {
            throw new \InvalidArgumentException('Unknown or inactive data source.');
        }

        $ingestedAt = self::normalizeIngestedAt($payload['ingested_at'] ?? null);
        $now = Clock::nowUtcString();

        return Database::transaction(static function () use (
            $matchDayId, $userId, $sourceId, $value, $isManual, $sourceRecordKey, $ingestedAt, $now
        ): array {
            $existing = Database::fetch(
                'SELECT * FROM rly_match_day_results WHERE match_day_id = ? AND user_id = ?',
                [$matchDayId, $userId]
            );

            if ($existing !== null) {
                Database::run(
                    'UPDATE rly_match_day_results
                     SET data_source_id = ?, metric_value = ?, is_manual = ?, ingested_at = ?,
                         source_record_key = ?, updated_at = ?
                     WHERE id = ?',
                    [
                        $sourceId,
                        $value,
                        $isManual ? 1 : 0,
                        $ingestedAt,
                        $sourceRecordKey ?? $existing['source_record_key'],
                        $now,
                        (int) $existing['id'],
                    ]
                );
                return Database::fetch('SELECT * FROM rly_match_day_results WHERE id = ?', [(int) $existing['id']]) ?? [];
            }

            Database::run(
                'INSERT INTO rly_match_day_results
                 (match_day_id, user_id, data_source_id, metric_value, is_manual, ingested_at, source_record_key, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $matchDayId,
                    $userId,
                    $sourceId,
                    $value,
                    $isManual ? 1 : 0,
                    $ingestedAt,
                    $sourceRecordKey,
                    $now,
                    $now,
                ]
            );

            return Database::fetch(
                'SELECT * FROM rly_match_day_results WHERE id = ?',
                [Database::lastInsertId()]
            ) ?? [];
        });
    }

    private static function resolveSourceId(mixed $source): int
    {
        if (is_int($source) || (is_string($source) && ctype_digit($source))) {
            return (int) $source;
        }
        if (!is_string($source) || $source === '') {
            throw new \InvalidArgumentException('data_source is required.');
        }
        $row = Database::fetch('SELECT id FROM rly_data_sources WHERE slug = ?', [$source]);
        if ($row === null) {
            throw new \InvalidArgumentException('Unknown data source slug: ' . $source);
        }
        return (int) $row['id'];
    }

    private static function normalizeIngestedAt(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return Clock::nowUtcString();
        }
        try {
            $dt = new \DateTimeImmutable($raw);
            return $dt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return Clock::nowUtcString();
        }
    }
}
