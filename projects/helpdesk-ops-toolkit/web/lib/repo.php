<?php
/**
 * Data access and reporting. Every query uses a prepared statement.
 * Aggregation for the reports page is done in PHP rather than with
 * database-specific date functions, so the same code runs against MySQL
 * (production) and the SQLite demo unchanged.
 */
declare(strict_types=1);

/* ===================================================================
 * TICKETS
 * =================================================================== */

/**
 * @param array{status?:string,category?:string,priority?:string,department?:string,q?:string} $f
 * @return array<int,array<string,mixed>>
 */
function tickets_find(array $f = []): array
{
    $where  = [];
    $params = [];
    foreach (['status', 'category', 'priority'] as $col) {
        if (!empty($f[$col])) {
            $where[]        = "$col = :$col";
            $params[":$col"] = $f[$col];
        }
    }
    if (!empty($f['department'])) {
        $where[]                 = 'requester_department = :dept';
        $params[':dept']         = $f['department'];
    }
    if (!empty($f['q'])) {
        $where[]           = '(title LIKE :q OR description LIKE :q OR requester_name LIKE :q)';
        $params[':q']      = '%' . $f['q'] . '%';
    }
    $sql = 'SELECT * FROM hot_tickets';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= " ORDER BY CASE status
                        WHEN 'open' THEN 0 WHEN 'in_progress' THEN 1
                        WHEN 'resolved' THEN 2 ELSE 3 END,
                      CASE priority
                        WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END,
                      created_at DESC";
    $st = hot_db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function ticket_get(int $id): ?array
{
    $st = hot_db()->prepare('SELECT * FROM hot_tickets WHERE id = :id');
    $st->execute([':id' => $id]);
    $row = $st->fetch();
    return $row ?: null;
}

/** Public ticket submission. Returns the new ticket id. */
function ticket_create(array $d): int
{
    $st = hot_db()->prepare(
        'INSERT INTO hot_tickets
           (title, description, category, priority, status,
            requester_name, requester_department, assigned_to, created_at, resolved_at)
         VALUES
           (:title, :description, :category, :priority, :status,
            :rname, :rdept, NULL, :created, NULL)'
    );
    $st->execute([
        ':title'       => $d['title'],
        ':description' => $d['description'],
        ':category'    => pick($d['category'] ?? '', HOT_CATEGORIES, 'other'),
        ':priority'    => pick($d['priority'] ?? '', HOT_PRIORITIES, 'medium'),
        ':status'      => 'open',
        ':rname'       => $d['requester_name'],
        ':rdept'       => $d['requester_department'],
        ':created'     => date('Y-m-d H:i:s'),
    ]);
    return (int) hot_db()->lastInsertId();
}

/**
 * Agent update: change status/assignee and optionally append a resolution
 * note to the description (schema v1 keeps the audit inline in the body).
 * resolved_at is set when moving into a resolved/closed state and cleared
 * when reopening.
 */
function ticket_update(int $id, string $status, ?string $assignee, string $note): bool
{
    $t = ticket_get($id);
    if (!$t) {
        return false;
    }
    $status = pick($status, HOT_STATUSES, $t['status']);

    $description = $t['description'];
    if (trim($note) !== '') {
        $stamp = date('Y-m-d H:i');
        $who   = current_agent() ?? 'agent';
        $description .= "\n\n--- Resolution note ({$who}, {$stamp}) ---\n" . trim($note);
    }

    $resolvedAt = $t['resolved_at'];
    if (in_array($status, ['resolved', 'closed'], true) && empty($resolvedAt)) {
        $resolvedAt = date('Y-m-d H:i:s');
    } elseif (in_array($status, ['open', 'in_progress'], true)) {
        $resolvedAt = null;
    }

    $st = hot_db()->prepare(
        'UPDATE hot_tickets
            SET status = :status, assigned_to = :assignee,
                description = :description, resolved_at = :resolved
          WHERE id = :id'
    );
    return $st->execute([
        ':status'      => $status,
        ':assignee'    => ($assignee !== null && $assignee !== '') ? $assignee : null,
        ':description' => $description,
        ':resolved'    => $resolvedAt,
        ':id'          => $id,
    ]);
}

/* ===================================================================
 * REPORTING (computed in PHP for portability)
 * =================================================================== */

function ticket_stats(): array
{
    $rows = hot_db()->query('SELECT category, priority, status, created_at, resolved_at FROM hot_tickets')
                    ->fetchAll();

    $byStatus   = array_fill_keys(HOT_STATUSES, 0);
    $byCategory = array_fill_keys(HOT_CATEGORIES, 0);
    $byPriority = array_fill_keys(HOT_PRIORITIES, 0);
    $byWeek     = []; // 'Y-\WW' => count
    $resHours   = [];

    foreach ($rows as $r) {
        $byStatus[$r['status']]     = ($byStatus[$r['status']] ?? 0) + 1;
        $byCategory[$r['category']] = ($byCategory[$r['category']] ?? 0) + 1;
        $byPriority[$r['priority']] = ($byPriority[$r['priority']] ?? 0) + 1;

        $cts = strtotime((string) $r['created_at']);
        if ($cts) {
            $key           = date('o-\WW', $cts); // ISO year-week
            $byWeek[$key]  = ($byWeek[$key] ?? 0) + 1;
        }
        if (!empty($r['resolved_at'])) {
            $rts = strtotime((string) $r['resolved_at']);
            if ($cts && $rts && $rts >= $cts) {
                $resHours[] = ($rts - $cts) / 3600;
            }
        }
    }

    ksort($byWeek);
    $byWeek = array_slice($byWeek, -8, null, true); // last 8 weeks

    $total    = count($rows);
    $openish  = ($byStatus['open'] ?? 0) + ($byStatus['in_progress'] ?? 0);
    $doneish  = ($byStatus['resolved'] ?? 0) + ($byStatus['closed'] ?? 0);
    $avgHours = $resHours ? array_sum($resHours) / count($resHours) : 0.0;

    return [
        'total'      => $total,
        'by_status'  => $byStatus,
        'by_category'=> $byCategory,
        'by_priority'=> $byPriority,
        'by_week'    => $byWeek,
        'open'       => $openish,
        'done'       => $doneish,
        'avg_hours'  => $avgHours,
        'resolved_count' => count($resHours),
    ];
}

/* ===================================================================
 * ASSETS
 * =================================================================== */

function assets_find(array $f = []): array
{
    $where  = [];
    $params = [];
    if (!empty($f['status'])) {
        $where[]           = 'status = :status';
        $params[':status'] = $f['status'];
    }
    if (!empty($f['type'])) {
        $where[]         = 'type = :type';
        $params[':type'] = $f['type'];
    }
    if (!empty($f['department'])) {
        $where[]         = 'assigned_department = :dept';
        $params[':dept'] = $f['department'];
    }
    $sql = 'SELECT * FROM hot_assets';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY asset_tag';
    $st = hot_db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function asset_get(int $id): ?array
{
    $st = hot_db()->prepare('SELECT * FROM hot_assets WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function asset_save(array $d, ?int $id = null): int
{
    $fields = [
        ':tag'    => $d['asset_tag'],
        ':type'   => pick($d['type'] ?? '', HOT_ASSET_TYPES, 'laptop'),
        ':model'  => $d['make_model'],
        ':auser'  => ($d['assigned_to'] ?? '') !== '' ? $d['assigned_to'] : null,
        ':adept'  => ($d['assigned_department'] ?? '') !== '' ? $d['assigned_department'] : null,
        ':status' => pick($d['status'] ?? '', HOT_ASSET_STATUS, 'in_use'),
        ':acq'    => ($d['acquired_date'] ?? '') !== '' ? $d['acquired_date'] : null,
    ];
    if ($id) {
        $fields[':id'] = $id;
        $st = hot_db()->prepare(
            'UPDATE hot_assets SET asset_tag=:tag, type=:type, make_model=:model,
                assigned_to=:auser, assigned_department=:adept, status=:status, acquired_date=:acq
             WHERE id=:id'
        );
        $st->execute($fields);
        return $id;
    }
    $st = hot_db()->prepare(
        'INSERT INTO hot_assets
           (asset_tag, type, make_model, assigned_to, assigned_department, status, acquired_date)
         VALUES (:tag, :type, :model, :auser, :adept, :status, :acq)'
    );
    $st->execute($fields);
    return (int) hot_db()->lastInsertId();
}

/** Quick status transition (e.g. mark for surplus / retire). */
function asset_set_status(int $id, string $status): bool
{
    $status = pick($status, HOT_ASSET_STATUS, 'in_use');
    $st = hot_db()->prepare('UPDATE hot_assets SET status = :s WHERE id = :id');
    return $st->execute([':s' => $status, ':id' => $id]);
}

/* ===================================================================
 * KNOWLEDGE BASE
 * =================================================================== */

function kb_find(array $f = []): array
{
    $where  = [];
    $params = [];
    if (!empty($f['audience'])) {
        $where[]             = 'audience = :aud';
        $params[':aud']      = $f['audience'];
    }
    if (!empty($f['category'])) {
        $where[]             = 'category = :cat';
        $params[':cat']      = $f['category'];
    }
    $sql = 'SELECT id, title, category, audience, updated_at FROM hot_kb_articles';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY audience, category, title';
    $st = hot_db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function kb_get(int $id): ?array
{
    $st = hot_db()->prepare('SELECT * FROM hot_kb_articles WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

/* ===================================================================
 * LOOKUPS
 * =================================================================== */

function departments_all(): array
{
    return hot_db()->query('SELECT name FROM hot_departments ORDER BY name')
                   ->fetchAll(PDO::FETCH_COLUMN);
}

function agents_all(): array
{
    $st = hot_db()->query(
        "SELECT name FROM hot_users WHERE role IN ('support_agent','admin') ORDER BY name"
    );
    return $st->fetchAll(PDO::FETCH_COLUMN);
}
