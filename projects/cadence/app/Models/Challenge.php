<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

final class Challenge
{
    public const CATEGORIES = ['fitness', 'mindfulness', 'nutrition', 'learning', 'creativity', 'lifestyle'];
    public const PER_PAGE = 24;

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM challenges WHERE slug = ?', [$slug]);
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM challenges WHERE id = ?', [$id]);
    }

    /**
     * Browse with search, category and status filters, sorting, and
     * pagination. Status is computed against today's date: active,
     * upcoming, or ended.
     *
     * @return array{rows: list<array<string, mixed>>, total: int, page: int, pages: int}
     */
    public static function browse(string $q, string $category, string $status, string $sort, int $page): array
    {
        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = 'title LIKE ?';
            $params[] = '%' . addcslashes($q, '%_\\') . '%';
        }
        if (in_array($category, self::CATEGORIES, true)) {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        if ($status === 'active') {
            $where[] = 'start_date <= CURDATE() AND end_date >= CURDATE()';
        } elseif ($status === 'upcoming') {
            $where[] = 'start_date > CURDATE()';
        } elseif ($status === 'ended') {
            $where[] = 'end_date < CURDATE()';
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $orderSql = $sort === 'start'
            ? 'ORDER BY start_date DESC, id DESC'
            : 'ORDER BY is_featured DESC, participant_count DESC, id DESC';

        $total = (int) Database::fetchValue("SELECT COUNT(*) FROM challenges $whereSql", $params);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * self::PER_PAGE;

        // LIMIT/OFFSET are integers derived above, never raw input.
        $rows = Database::fetchAll(
            "SELECT * FROM challenges $whereSql $orderSql LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    /** Lifecycle label for a challenge row, computed in PHP for display. */
    public static function status(array $challenge): string
    {
        $today = date('Y-m-d');
        if ($challenge['start_date'] > $today) {
            return 'upcoming';
        }
        if ($challenge['end_date'] < $today) {
            return 'ended';
        }
        return 'active';
    }

    /** Days remaining including today, for active challenges. */
    public static function daysLeft(array $challenge): int
    {
        $end = new \DateTimeImmutable((string) $challenge['end_date']);
        $today = new \DateTimeImmutable(date('Y-m-d'));
        return max(0, (int) $today->diff($end)->format('%r%a') + 1);
    }

    /** Total days in the challenge window, inclusive. */
    public static function durationDays(array $challenge): int
    {
        $start = new \DateTimeImmutable((string) $challenge['start_date']);
        $end = new \DateTimeImmutable((string) $challenge['end_date']);
        return (int) $start->diff($end)->format('%a') + 1;
    }
}
