<?php
/**
 * CSV export of the (optionally filtered) asset list. Echoes the real
 * "equipment surplus / inventory hand-off" workflow where the list leaves
 * the system as a spreadsheet.
 */
require __DIR__ . '/lib/bootstrap.php';
require_agent();

$filters = [
    'status'     => pick((string) ($_GET['status'] ?? ''), HOT_ASSET_STATUS, ''),
    'type'       => pick((string) ($_GET['type'] ?? ''), HOT_ASSET_TYPES, ''),
    'department' => (string) ($_GET['department'] ?? ''),
];
$assets = assets_find($filters);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="assets-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
// Pass all delimiter args explicitly (incl. empty $escape) for standard CSV
// escaping and to silence the PHP 8.4 fputcsv() deprecation notice.
fputcsv($out, ['asset_tag', 'type', 'make_model', 'assigned_to', 'assigned_department', 'status', 'acquired_date'], ',', '"', '');
foreach ($assets as $a) {
    fputcsv($out, [
        $a['asset_tag'], $a['type'], $a['make_model'],
        $a['assigned_to'], $a['assigned_department'], $a['status'], $a['acquired_date'],
    ], ',', '"', '');
}
fclose($out);
