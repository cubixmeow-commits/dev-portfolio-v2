<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Config;
use Rally\Core\Database;

/**
 * Canonical daily health observations independent of any single match.
 * Future HealthKit / Health Connect connectors should call ingest() once per
 * observation; projection fans that value into eligible matches.
 */
final class UserMetricDayService
{
    /**
     * @param array{
     *   user_id: int,
     *   metric_type: string|int,
     *   observation_date: string,
     *   value: int,
     *   data_source: string|int,
     *   source_record_key?: ?string,
     *   source_timezone?: ?string,
     *   is_manual?: bool,
     *   observed_at?: ?string,
     *   ingested_at?: ?string,
     *   project?: bool
     * } $payload
     * @return array{observation: array<string, mixed>, projections: list<array<string, mixed>>}
     */
    public static function ingest(array $payload, bool $adminOverride = false): array
    {
        $userId = (int) ($payload['user_id'] ?? 0);
        $value = (int) ($payload['value'] ?? -1);
        $observationDate = trim((string) ($payload['observation_date'] ?? ''));
        $isManual = (bool) ($payload['is_manual'] ?? false);
        $project = array_key_exists('project', $payload) ? (bool) $payload['project'] : true;
        $sourceRecordKey = isset($payload['source_record_key']) && $payload['source_record_key'] !== ''
            ? (string) $payload['source_record_key']
            : null;
        $sourceTimezone = isset($payload['source_timezone']) && $payload['source_timezone'] !== ''
            ? (string) $payload['source_timezone']
            : null;

        if ($userId < 1) {
            throw new \InvalidArgumentException('user_id is required.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $observationDate)) {
            throw new \InvalidArgumentException('observation_date must be YYYY-MM-DD.');
        }

        $todayUtc = Clock::now()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d');
        if ($observationDate > $todayUtc && !$adminOverride) {
            throw new \InvalidArgumentException('Future observation dates are not accepted.');
        }

        $min = Config::int('app.metric_value_min', 0);
        $max = Config::int('app.metric_value_max', 100000);
        if ($value < $min || $value > $max) {
            throw new \InvalidArgumentException("Metric value must be between {$min} and {$max}.");
        }

        $user = Database::fetch('SELECT id FROM rly_users WHERE id = ?', [$userId]);
        if ($user === null) {
            throw new \InvalidArgumentException('Unknown user.');
        }

        $metric = self::resolveMetric($payload['metric_type'] ?? null);
        $sourceId = self::resolveSourceId($payload['data_source'] ?? null);
        $source = Database::fetch('SELECT * FROM rly_data_sources WHERE id = ? AND is_active = 1', [$sourceId]);
        if ($source === null) {
            throw new \InvalidArgumentException('Unknown or inactive data source.');
        }

        $ingestedAt = self::normalizeTimestamp($payload['ingested_at'] ?? null) ?? Clock::nowUtcString();
        $observedAt = self::normalizeTimestamp($payload['observed_at'] ?? null);
        $now = Clock::nowUtcString();

        $observation = Database::transaction(static function () use (
            $userId, $metric, $sourceId, $observationDate, $value, $isManual,
            $sourceRecordKey, $sourceTimezone, $observedAt, $ingestedAt, $now
        ): array {
            $existing = null;
            if ($sourceRecordKey !== null) {
                $existing = Database::fetch(
                    'SELECT * FROM rly_user_metric_days
                     WHERE user_id = ? AND metric_type_id = ? AND data_source_id = ?
                       AND source_record_key = ?',
                    [$userId, (int) $metric['id'], $sourceId, $sourceRecordKey]
                );
            }
            if ($existing === null) {
                $existing = Database::fetch(
                    'SELECT * FROM rly_user_metric_days
                     WHERE user_id = ? AND metric_type_id = ? AND data_source_id = ? AND observation_date = ?',
                    [$userId, (int) $metric['id'], $sourceId, $observationDate]
                );
            }

            if ($existing !== null) {
                Database::run(
                    'UPDATE rly_user_metric_days
                     SET observation_date = ?, metric_value = ?, is_manual = ?, source_record_key = ?,
                         source_timezone = ?, observed_at = ?, ingested_at = ?, updated_at = ?,
                         data_source_id = ?, metric_type_id = ?
                     WHERE id = ?',
                    [
                        $observationDate,
                        $value,
                        $isManual ? 1 : 0,
                        $sourceRecordKey ?? $existing['source_record_key'],
                        $sourceTimezone ?? $existing['source_timezone'],
                        $observedAt ?? $existing['observed_at'],
                        $ingestedAt,
                        $now,
                        $sourceId,
                        (int) $metric['id'],
                        (int) $existing['id'],
                    ]
                );
                return Database::fetch('SELECT * FROM rly_user_metric_days WHERE id = ?', [(int) $existing['id']]) ?? [];
            }

            Database::run(
                'INSERT INTO rly_user_metric_days
                 (user_id, metric_type_id, data_source_id, observation_date, metric_value, is_manual,
                  source_record_key, source_timezone, observed_at, ingested_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $userId,
                    (int) $metric['id'],
                    $sourceId,
                    $observationDate,
                    $value,
                    $isManual ? 1 : 0,
                    $sourceRecordKey,
                    $sourceTimezone,
                    $observedAt,
                    $ingestedAt,
                    $now,
                    $now,
                ]
            );

            return Database::fetch(
                'SELECT * FROM rly_user_metric_days WHERE id = ?',
                [Database::lastInsertId()]
            ) ?? [];
        });

        $projections = [];
        if ($project) {
            $projections = MatchObservationProjectionService::projectObservation($observation, $adminOverride);
        }

        return [
            'observation' => $observation,
            'projections' => $projections,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function historyFor(
        int $userId,
        int $metricTypeId,
        int $dataSourceId,
        ?string $beforeDate = null,
        int $limit = 30
    ): array {
        $sql = 'SELECT * FROM rly_user_metric_days
                WHERE user_id = ? AND metric_type_id = ? AND data_source_id = ?';
        $params = [$userId, $metricTypeId, $dataSourceId];
        if ($beforeDate !== null && $beforeDate !== '') {
            $sql .= ' AND observation_date < ?';
            $params[] = $beforeDate;
        }
        $sql .= ' ORDER BY observation_date DESC LIMIT ' . max(1, $limit);
        return Database::fetchAll($sql, $params);
    }

    /** @return array<string, mixed> */
    private static function resolveMetric(mixed $metric): array
    {
        if (is_int($metric) || (is_string($metric) && ctype_digit($metric))) {
            $row = Database::fetch('SELECT * FROM rly_metric_types WHERE id = ? AND is_active = 1', [(int) $metric]);
            if ($row === null) {
                throw new \InvalidArgumentException('Unknown metric type.');
            }
            return $row;
        }
        if (!is_string($metric) || $metric === '') {
            throw new \InvalidArgumentException('metric_type is required.');
        }
        $row = Database::fetch('SELECT * FROM rly_metric_types WHERE slug = ? AND is_active = 1', [$metric]);
        if ($row === null) {
            throw new \InvalidArgumentException('Unknown metric type slug: ' . $metric);
        }
        return $row;
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

    private static function normalizeTimestamp(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        try {
            $dt = new \DateTimeImmutable($raw);
            return $dt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }
}
