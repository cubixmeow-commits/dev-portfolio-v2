<?php
/**
 * Reporting dashboard (agent only). The same underlying query logic that the
 * Java TicketReportGenerator uses, delivered here as on-screen charts.
 * Charts are hand-rendered inline SVG — no charting library, no CDN.
 */
require __DIR__ . '/lib/bootstrap.php';
require_agent();

$s = ticket_stats();

/** Horizontal labelled bar chart. $data = [label => value]. */
function svg_hbars(array $data, string $accent = '#005ea2'): string
{
    $max = max(array_merge([1], array_map('intval', array_values($data ?: [0]))));
    $rowH = 34; $pad = 8; $labelW = 132; $barMax = 300; $valW = 44;
    $w = $labelW + $barMax + $valW; $h = count($data) * $rowH + $pad * 2;
    $svg = "<svg viewBox=\"0 0 $w $h\" role=\"img\" class=\"chart\" preserveAspectRatio=\"xMinYMin meet\">";
    $y = $pad;
    foreach ($data as $label => $val) {
        $val = (int) $val;
        $bw  = $max > 0 ? (int) round($val / $max * $barMax) : 0;
        $cy  = $y + $rowH / 2;
        $svg .= "<text x=\"0\" y=\"" . ($cy + 4) . "\" class=\"c-label\">" . e(label((string) $label)) . "</text>";
        $svg .= "<rect x=\"$labelW\" y=\"" . ($y + 6) . "\" width=\"" . max($bw, 2) . "\" height=\"" . ($rowH - 14) . "\" rx=\"3\" fill=\"$accent\"/>";
        $svg .= "<text x=\"" . ($labelW + max($bw, 2) + 8) . "\" y=\"" . ($cy + 4) . "\" class=\"c-val\">$val</text>";
        $y += $rowH;
    }
    return $svg . '</svg>';
}

/** Vertical bar chart for weekly volume. $data = [weekKey => value]. */
function svg_vbars(array $data, string $accent = '#005ea2'): string
{
    if (!$data) {
        return '<p class="muted">No dated tickets to chart yet.</p>';
    }
    $max = max(array_merge([1], array_map('intval', array_values($data))));
    $n = count($data); $barW = 46; $gap = 14; $chartH = 150; $top = 10; $labelH = 40;
    $w = $n * ($barW + $gap) + $gap; $h = $chartH + $top + $labelH;
    $svg = "<svg viewBox=\"0 0 $w $h\" role=\"img\" class=\"chart\" preserveAspectRatio=\"xMinYMin meet\">";
    $x = $gap;
    foreach ($data as $week => $val) {
        $val = (int) $val;
        $bh  = (int) round($val / $max * $chartH);
        $y   = $top + $chartH - $bh;
        $short = preg_replace('/^\d{4}-/', '', (string) $week); // WNN
        $svg .= "<rect x=\"$x\" y=\"$y\" width=\"$barW\" height=\"" . max($bh, 2) . "\" rx=\"3\" fill=\"$accent\"/>";
        $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"" . ($y - 5) . "\" class=\"c-val\" text-anchor=\"middle\">$val</text>";
        $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"" . ($top + $chartH + 18) . "\" class=\"c-axis\" text-anchor=\"middle\">" . e($short) . "</text>";
        $x += $barW + $gap;
    }
    $svg .= "<text x=\"$gap\" y=\"" . ($top + $chartH + 34) . "\" class=\"c-axis-note\">ISO week</text>";
    return $svg . '</svg>';
}

$pct = $s['total'] > 0 ? round($s['done'] / $s['total'] * 100) : 0;

hot_header('Reports', 'reports');
?>
<div class="page-head">
  <h1>Ticket reports</h1>
  <p class="lead">Operational summary across all <?= (int) $s['total'] ?> tickets. This is the on-screen counterpart to the weekly <code>TicketReportGenerator</code> Java tool.</p>
</div>

<div class="stat-row">
  <div class="stat"><span class="stat-n"><?= (int) $s['total'] ?></span><span class="stat-l">Total tickets</span></div>
  <div class="stat"><span class="stat-n"><?= (int) $s['open'] ?></span><span class="stat-l">Open / in progress</span></div>
  <div class="stat"><span class="stat-n"><?= (int) $s['done'] ?></span><span class="stat-l">Resolved / closed</span></div>
  <div class="stat"><span class="stat-n"><?= e(fmt_hours($s['avg_hours'])) ?></span><span class="stat-l">Avg. resolution time</span></div>
</div>

<div class="cols">
  <section class="card">
    <h2>Ticket volume by week</h2>
    <div class="chart-wrap"><?= svg_vbars($s['by_week']) ?></div>
  </section>
  <section class="card">
    <h2>Open vs. closed</h2>
    <div class="progress" role="img" aria-label="<?= (int) $pct ?>% of tickets resolved or closed">
      <div class="progress-bar" style="width: <?= (int) $pct ?>%"></div>
    </div>
    <p class="progress-note"><strong><?= (int) $pct ?>%</strong> resolved or closed — <?= (int) $s['open'] ?> still active, <?= (int) $s['done'] ?> done.</p>
    <h2 class="mt">By priority</h2>
    <div class="chart-wrap"><?= svg_hbars($s['by_priority'], '#c2410c') ?></div>
  </section>
</div>

<section class="card">
  <h2>Tickets by category</h2>
  <div class="chart-wrap"><?= svg_hbars($s['by_category']) ?></div>
</section>

<section class="card">
  <h2>Tickets by status</h2>
  <div class="chart-wrap"><?= svg_hbars($s['by_status'], '#2e7d5b') ?></div>
</section>
<?php
hot_footer();
