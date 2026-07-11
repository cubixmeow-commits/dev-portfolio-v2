<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Database;
use Cadence\Core\View;
use Cadence\Models\ActivityEvent;
use Cadence\Models\Badge;
use Cadence\Models\CheckIn;
use Cadence\Models\Notification;
use Cadence\Models\Participation;

final class DashboardController
{
    /**
     * Root route: marketing page for visitors, the Today dashboard for
     * signed-in members.
     */
    public function home(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }

        // Live proof strips: the homepage is itself a demo, so these
        // numbers come from the real tables on every load.
        $weekCheckins = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM check_ins WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)'
        );
        $longestStreak = Database::fetch(
            "SELECT cp.current_streak, u.display_name, u.handle
             FROM challenge_participants cp
             JOIN challenges c ON c.id = cp.challenge_id
             JOIN users u ON u.id = cp.user_id
             WHERE c.start_date <= CURDATE() AND c.end_date >= CURDATE()
               AND u.handle NOT LIKE 'deleted\\_%'
             ORDER BY cp.current_streak DESC, cp.id ASC
             LIMIT 1"
        );
        $topChallenge = Database::fetch(
            'SELECT title, slug, participant_count FROM challenges
             WHERE start_date <= CURDATE() AND end_date >= CURDATE()
             ORDER BY participant_count DESC LIMIT 1'
        );

        $events = \Cadence\Models\ActivityEvent::feed(null, 8);
        $rings = Participation::ringMap(array_column($events, 'user_id'));

        View::render('marketing/home', [
            'title'         => '',
            'page_css'      => 'marketing',
            'weekCheckins'  => $weekCheckins,
            'longestStreak' => $longestStreak,
            'topChallenge'  => $topChallenge,
            'events'        => $events,
            'rings'         => $rings,
        ]);
    }

    public function index(): void
    {
        $user = Auth::requireUser();
        $userId = (int) $user['id'];

        // Lazy time-based notifications (no cron on shared hosting).
        Notification::sync($user);

        $today = CheckIn::todayFor((string) $user['timezone']);
        $active = Participation::activeForUser($userId);

        // 14-day points sparkline, zero-filled per day.
        $since = (new \DateTimeImmutable($today))->modify('-13 days')->format('Y-m-d');
        $byDay = [];
        foreach (Database::fetchAll(
            'SELECT checkin_date, SUM(points_awarded) AS pts FROM check_ins
             WHERE user_id = ? AND checkin_date >= ? GROUP BY checkin_date',
            [$userId, $since]
        ) as $row) {
            $byDay[(string) $row['checkin_date']] = (int) $row['pts'];
        }
        $spark = [];
        for ($d = new \DateTimeImmutable($since), $end = new \DateTimeImmutable($today); $d <= $end; $d = $d->modify('+1 day')) {
            $spark[] = ['date' => $d->format('Y-m-d'), 'points' => $byDay[$d->format('Y-m-d')] ?? 0];
        }

        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekPoints = (int) Database::fetchValue(
            'SELECT COALESCE(SUM(points_awarded), 0) FROM check_ins WHERE user_id = ? AND checkin_date >= ?',
            [$userId, $weekStart]
        );

        $bestStreak = 0;
        $todayDone = 0;
        foreach ($active as $participation) {
            $bestStreak = max($bestStreak, (int) $participation['current_streak']);
            if ($participation['last_checkin_date'] === $today) {
                $todayDone++;
            }
        }

        View::render('dashboard/index', [
            'title'       => 'Today',
            'active'      => 'dashboard',
            'page_css'    => 'dashboard',
            'user'        => $user,
            'activeChallenges' => $active,
            'today'       => $today,
            'todayDone'   => $todayDone,
            'spark'       => $spark,
            'weekPoints'  => $weekPoints,
            'bestStreak'  => $bestStreak,
            'recentBadges' => array_slice(Badge::forUser($userId), 0, 4),
            'events'      => ActivityEvent::forUser($userId, 8),
        ]);
    }

    /** Notifications page. Opening it marks everything read. */
    public function notifications(): void
    {
        $user = Auth::requireUser();
        $items = Notification::forUser((int) $user['id'], 30);
        Notification::markAllRead((int) $user['id']);

        View::render('dashboard/notifications', [
            'title'    => 'Notifications',
            'page_css' => 'dashboard',
            'items'    => $items,
        ]);
    }
}
