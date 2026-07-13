<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * Shared constants and helpers for portfolio kitchen simulation.
 */
final class Simulation
{
    public const TIMEZONE = 'America/Los_Angeles';
    public const EMAIL_DOMAIN = 'demo.local';
    public const EMAIL_PREFIX = 'kitchen+';
    public const PASSWORD = 'demo-kitchen-2026';
    public const POOL_SIZE = 772;

    public static function emailForId(int $id): string
    {
        return self::EMAIL_PREFIX . $id . '@' . self::EMAIL_DOMAIN;
    }

    public static function pacific(): \DateTimeZone
    {
        return new \DateTimeZone(self::TIMEZONE);
    }

    public static function utc(): \DateTimeZone
    {
        return new \DateTimeZone('UTC');
    }

    /** Resolve --date=yesterday|today|YYYY-MM-DD to a Pacific calendar date string. */
    public static function resolvePacificDate(string $input): string
    {
        $now = new \DateTimeImmutable('now', self::pacific());
        return match (strtolower(trim($input))) {
            'today'     => $now->format('Y-m-d'),
            'yesterday' => $now->modify('-1 day')->format('Y-m-d'),
            default     => (new \DateTimeImmutable($input, self::pacific()))->format('Y-m-d'),
        };
    }

    /** Pacific calendar day bounds as UTC datetime strings for SQL range queries. */
    public static function pacificDayUtcRange(string $pacificDate): array
    {
        $start = new \DateTimeImmutable($pacificDate . ' 00:00:00', self::pacific());
        $end = new \DateTimeImmutable($pacificDate . ' 23:59:59', self::pacific());
        return [
            $start->setTimezone(self::utc())->format('Y-m-d H:i:s'),
            $end->setTimezone(self::utc())->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Random UTC timestamp within a Pacific calendar day, weighted toward
     * US waking hours (8am–10pm PT).
     */
    public static function randomUtcInPacificDay(string $pacificDate): string
    {
        $roll = random_int(1, 100);
        if ($roll <= 70) {
            $hour = random_int(8, 22);
            $minute = random_int(0, 59);
            $second = random_int(0, 59);
            $local = new \DateTimeImmutable(
                sprintf('%s %02d:%02d:%02d', $pacificDate, $hour, $minute, $second),
                self::pacific()
            );
        } else {
            $start = new \DateTimeImmutable($pacificDate . ' 00:00:00', self::pacific());
            $offset = random_int(0, 86399);
            $local = $start->modify('+' . $offset . ' seconds');
        }
        return $local->setTimezone(self::utc())->format('Y-m-d H:i:s');
    }

    /** @return list<array<string, mixed>> */
    public static function loadPersonas(): array
    {
        $file = __DIR__ . '/../../database/simulation/personas.json';
        if (!is_file($file)) {
            throw new \RuntimeException('Missing personas.json. Run: php scripts/generate-personas.php');
        }
        $data = json_decode((string) file_get_contents($file), true);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid personas.json');
        }
        return array_slice($data, 0, self::POOL_SIZE);
    }
}
