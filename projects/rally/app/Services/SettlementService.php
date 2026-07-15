<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Config;
use Rally\Core\Database;

/**
 * Match-day lifecycle and settlement using the application clock and the
 * match's authoritative timezone.
 *
 * Default rule: Day N becomes official at 06:00 on Day N+2 in the match
 * timezone (configurable via app.settlement_hour / settlement_lag_days).
 *
 * Missing-data policy at settlement:
 * - One or both players missing → day is void (no free wins).
 * - Void days award no wins and are counted separately from ties.
 */
final class SettlementService
{
    /**
     * Refresh day statuses and match status for one match against Clock::now().
     *
     * @return array{days_updated: int, match_status: string}
     */
    public static function refreshMatch(int $matchId): array
    {
        $match = Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$matchId]);
        if ($match === null) {
            throw new \RuntimeException('Match not found.');
        }

        if (in_array((string) $match['status'], ['cancelled', 'invited'], true)) {
            return ['days_updated' => 0, 'match_status' => (string) $match['status']];
        }
        if ((string) $match['invitation_status'] !== 'accepted'
            && (string) $match['status'] !== 'completed') {
            // Still pending invitation acceptance — leave alone unless already active statuses.
            if ((string) $match['status'] === 'scheduled' || (string) $match['status'] === 'invited') {
                return ['days_updated' => 0, 'match_status' => (string) $match['status']];
            }
        }

        $tz = new \DateTimeZone((string) $match['timezone']);
        $now = Clock::now()->setTimezone($tz);
        $nowUtc = Clock::nowUtcString();
        $todayLocal = $now->format('Y-m-d');

        $days = Database::fetchAll(
            'SELECT * FROM rly_match_days WHERE match_id = ? ORDER BY day_number ASC',
            [$matchId]
        );

        $updated = 0;
        foreach ($days as $day) {
            $status = (string) $day['status'];
            if ($status === 'official' || $status === 'void') {
                continue;
            }

            $compDate = (string) $day['competition_date'];
            $settlesAt = new \DateTimeImmutable((string) $day['settles_at'], new \DateTimeZone('UTC'));
            $settlesAtLocal = $settlesAt->setTimezone($tz);

            $newStatus = $status;

            if ($compDate > $todayLocal) {
                $newStatus = 'scheduled';
            } elseif ($compDate === $todayLocal) {
                $newStatus = 'live';
            } elseif ($now >= $settlesAtLocal) {
                $newStatus = self::settleDay((int) $day['id'], $match, $nowUtc);
            } else {
                $newStatus = 'pending';
            }

            if ($newStatus !== $status && $newStatus !== 'official' && $newStatus !== 'void') {
                Database::run(
                    'UPDATE rly_match_days SET status = ?, updated_at = ? WHERE id = ?',
                    [$newStatus, $nowUtc, (int) $day['id']]
                );
                $updated++;
            } elseif (in_array($newStatus, ['official', 'void'], true) && $status !== $newStatus) {
                $updated++;
            }
        }

        $matchStatus = self::deriveMatchStatus($match, $matchId, $todayLocal, $nowUtc);

        return ['days_updated' => $updated, 'match_status' => $matchStatus];
    }

    /**
     * Force-settle a single day (simulation / admin).
     */
    public static function settleDayNow(int $matchDayId): string
    {
        $day = Database::fetch(
            'SELECT d.*, m.* FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             WHERE d.id = ?',
            [$matchDayId]
        );
        if ($day === null) {
            throw new \RuntimeException('Match day not found.');
        }
        $match = Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [(int) $day['match_id']]);
        if ($match === null) {
            throw new \RuntimeException('Match not found.');
        }
        $result = self::settleDay($matchDayId, $match, Clock::nowUtcString());
        self::refreshMatch((int) $match['id']);
        return $result;
    }

    /**
     * Settle one day: official if both values present, else void.
     *
     * @param array<string, mixed> $match
     */
    private static function settleDay(int $matchDayId, array $match, string $nowUtc): string
    {
        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];

        $results = Database::fetchAll(
            'SELECT * FROM rly_match_day_results WHERE match_day_id = ?',
            [$matchDayId]
        );
        $hasA = false;
        $hasB = false;
        foreach ($results as $row) {
            if ((int) $row['user_id'] === $playerA) {
                $hasA = true;
            }
            if ((int) $row['user_id'] === $playerB) {
                $hasB = true;
            }
        }

        $status = ($hasA && $hasB) ? 'official' : 'void';

        Database::run(
            'UPDATE rly_match_days SET status = ?, official_at = ?, updated_at = ? WHERE id = ?',
            [$status, $nowUtc, $nowUtc, $matchDayId]
        );

        return $status;
    }

    /**
     * @param array<string, mixed> $match
     */
    private static function deriveMatchStatus(array $match, int $matchId, string $todayLocal, string $nowUtc): string
    {
        $current = (string) $match['status'];
        if ($current === 'cancelled') {
            return 'cancelled';
        }
        if ($current === 'invited') {
            return 'invited';
        }

        $days = Database::fetchAll(
            'SELECT status, competition_date FROM rly_match_days WHERE match_id = ? ORDER BY day_number',
            [$matchId]
        );

        if ($days === []) {
            return $current;
        }

        $allTerminal = true;
        $anyLiveOrPending = false;
        $anyFuture = false;
        $lastDate = (string) $days[count($days) - 1]['competition_date'];

        foreach ($days as $day) {
            $st = (string) $day['status'];
            if ($st !== 'official' && $st !== 'void') {
                $allTerminal = false;
            }
            if ($st === 'live' || $st === 'pending') {
                $anyLiveOrPending = true;
            }
            if ($st === 'scheduled') {
                $anyFuture = true;
            }
        }

        $newStatus = $current;

        if ($allTerminal) {
            $newStatus = 'completed';
            Database::run(
                'UPDATE rly_matches SET status = ?, completed_at = COALESCE(completed_at, ?), updated_at = ? WHERE id = ?',
                ['completed', $nowUtc, $nowUtc, $matchId]
            );
            return $newStatus;
        }

        if ($lastDate < $todayLocal && $anyLiveOrPending) {
            $newStatus = 'settling';
        } elseif ($anyLiveOrPending || ($lastDate >= $todayLocal && !$anyFuture && $anyLiveOrPending)) {
            $newStatus = 'active';
        } elseif ($anyFuture || $lastDate >= $todayLocal) {
            // Has live day today or future days.
            $hasLive = false;
            foreach ($days as $day) {
                if ((string) $day['status'] === 'live') {
                    $hasLive = true;
                    break;
                }
            }
            $newStatus = $hasLive || $anyLiveOrPending ? 'active' : (
                ((string) $match['start_date'] <= $todayLocal) ? 'active' : 'scheduled'
            );
            if ((string) $match['start_date'] > $todayLocal && !$hasLive && !$anyLiveOrPending) {
                $newStatus = 'scheduled';
            } elseif ($hasLive || ((string) $match['start_date'] <= $todayLocal && !$allTerminal)) {
                if ($lastDate < $todayLocal) {
                    $newStatus = 'settling';
                } else {
                    $newStatus = 'active';
                }
            }
        }

        // Cleaner recompute:
        if (!$allTerminal) {
            if ($lastDate < $todayLocal) {
                $newStatus = 'settling';
            } elseif ((string) $match['start_date'] > $todayLocal) {
                $newStatus = 'scheduled';
            } else {
                $newStatus = 'active';
            }
        }

        if ($newStatus !== $current) {
            Database::run(
                'UPDATE rly_matches SET status = ?, updated_at = ?, completed_at = NULL WHERE id = ?',
                [$newStatus, $nowUtc, $matchId]
            );
        }

        return $newStatus;
    }

    /**
     * Compute settles_at UTC string for a competition date in match timezone.
     */
    public static function computeSettlesAt(string $competitionDate, string $timezone): string
    {
        $lagDays = Config::int('app.settlement_lag_days', 2);
        $hour = Config::int('app.settlement_hour', 6);
        $tz = new \DateTimeZone($timezone);
        $local = new \DateTimeImmutable($competitionDate . ' 00:00:00', $tz);
        $settlesLocal = $local->modify("+{$lagDays} days")->setTime($hour, 0, 0);
        return $settlesLocal->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    /** Refresh all non-terminal matches (used by simulation advance). */
    public static function refreshAllOpenMatches(): int
    {
        $rows = Database::fetchAll(
            "SELECT id FROM rly_matches WHERE status NOT IN ('cancelled','completed','invited')"
        );
        $count = 0;
        foreach ($rows as $row) {
            self::refreshMatch((int) $row['id']);
            $count++;
        }
        // Also refresh invited? no. But scheduled/active that might need invite accepted already.
        $more = Database::fetchAll(
            "SELECT id FROM rly_matches WHERE invitation_status = 'accepted' AND status IN ('scheduled','active','settling')"
        );
        foreach ($more as $row) {
            // already counted above for most; refresh is idempotent
            self::refreshMatch((int) $row['id']);
        }
        return $count;
    }
}
