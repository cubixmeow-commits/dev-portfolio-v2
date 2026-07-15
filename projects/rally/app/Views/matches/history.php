<?php
/** @var array $pack */
/** @var list<array> $rows */
$m = $pack['match'];
$s = $pack['summary'];
?>
<section class="page-narrow history-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">← Match</a></p>
    <h1>Match history</h1>
    <p><?= e($m['player_a_name']) ?> <?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?> <?= e($m['player_b_name']) ?></p>
  </header>

  <div class="history-table-wrap" role="region" aria-label="Daily games" tabindex="0">
    <table class="history-table">
      <thead>
        <tr>
          <th scope="col">Game</th>
          <th scope="col">Date</th>
          <th scope="col"><?= e($m['player_a_name']) ?></th>
          <th scope="col"><?= e($m['player_b_name']) ?></th>
          <th scope="col">Result</th>
          <th scope="col">Margin</th>
          <th scope="col">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row):
          $day = $row['day'];
          $o = $row['outcome'];
          $status = (string) $day['status'];
          $resultLabel = match ($o['kind']) {
              'void' => 'Void',
              'tie' => 'Tie',
              'win' => (($o['winner_side'] ?? '') === 'a' ? $m['player_a_name'] : $m['player_b_name']) . ' win',
              'awaiting', 'partial' => 'Awaiting sync',
              default => '—',
          };
        ?>
          <tr class="status-<?= e($status) ?>">
            <td><a href="<?= e(url('/matches/' . (int) $m['id'] . '/day/' . (int) $day['day_number'])) ?>"><?= (int) $day['day_number'] ?></a></td>
            <td><?= e($day['competition_date']) ?></td>
            <td><?= $o['value_a'] !== null ? e(number_format((int) $o['value_a'])) : '—' ?></td>
            <td><?= $o['value_b'] !== null ? e(number_format((int) $o['value_b'])) : '—' ?></td>
            <td><?= e($resultLabel) ?></td>
            <td><?= $o['margin'] !== null && $o['kind'] === 'win' ? e(number_format((int) $o['margin'])) : '—' ?></td>
            <td>
              <span class="status-pill status-<?= e($status) ?>"><?= e(strtoupper($status)) ?></span>
              <?php if (!empty($day['official_at'])): ?>
                <span class="hint"><?= e($day['official_at']) ?> UTC</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
