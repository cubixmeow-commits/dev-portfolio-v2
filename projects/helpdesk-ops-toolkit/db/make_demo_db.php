<?php
/**
 * Build the local SQLite demo database (LOCAL DEMO ONLY — MySQL is the
 * canonical target). Lets the PHP app be run and screenshotted without a
 * MySQL server.
 *
 *   php db/make_demo_db.php                 # writes db/hot_demo.sqlite
 *   HOT_DB_DSN="sqlite:$(pwd)/db/hot_demo.sqlite" \
 *     php -S localhost:8000 -t web
 *
 * The canonical seed.sql uses MySQL's `\n` escape inside the Markdown article
 * bodies; SQLite treats backslash-n literally, so we translate those to real
 * newlines here. Everything else in the seed is standard SQL.
 */
declare(strict_types=1);

$dir    = __DIR__;
$dbFile = $dir . '/hot_demo.sqlite';
@unlink($dbFile);

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Split a SQL script into statements on semicolons, respecting single-quoted
 * string literals (which may themselves contain ';' or '' escapes).
 *
 * @return string[]
 */
function split_sql(string $sql): array
{
    $stmts = [];
    $buf   = '';
    $inStr = false;
    $len   = strlen($sql);
    for ($i = 0; $i < $len; $i++) {
        $ch   = $sql[$i];
        $buf .= $ch;
        if ($ch === "'") {
            if ($inStr && $i + 1 < $len && $sql[$i + 1] === "'") {
                $buf .= $sql[++$i]; // doubled '' escape — keep both, stay in string
            } else {
                $inStr = !$inStr;
            }
        } elseif ($ch === ';' && !$inStr) {
            $stmts[] = $buf;
            $buf = '';
        }
    }
    if (trim($buf) !== '') {
        $stmts[] = $buf;
    }
    return $stmts;
}

function run_sql(PDO $pdo, string $sql): void
{
    // drop full-line comments (none appear inside string literals in these files)
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    foreach (split_sql($sql) as $stmt) {
        if (trim($stmt) !== '') {
            $pdo->exec($stmt);
        }
    }
}

$schema = file_get_contents($dir . '/sqlite_schema.sql');
run_sql($pdo, $schema);

$seed = file_get_contents($dir . '/seed.sql');
// Translate MySQL-style \n escapes (used in the KB bodies) to real newlines.
$seed = str_replace('\n', "\n", $seed);
run_sql($pdo, $seed);

$counts = [];
foreach (['hot_departments', 'hot_users', 'hot_tickets', 'hot_assets', 'hot_kb_articles'] as $t) {
    $counts[$t] = (int) $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
}

echo "Built demo database: $dbFile\n";
foreach ($counts as $t => $n) {
    printf("  %-18s %d rows\n", $t, $n);
}
echo "\nRun the app with:\n";
echo "  HOT_DB_DSN=\"sqlite:$dbFile\" php -S localhost:8000 -t web\n";
