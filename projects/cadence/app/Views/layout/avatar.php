<?php
/**
 * Avatar with streak ring, the signature element. Expects:
 *   $user: row with avatar_seed, display_name
 *   $size: avatar diameter in px (ring adds 6px)
 *   $ring: float 0..1 for today's check-in completion, or null to hide
 *          the ring state (gray track only).
 */
$size = (int) ($size ?? 40);
$ring = $ring ?? null;
$outer = $size + 6;
$r = ($outer - 2) / 2;
$c = 2 * M_PI * $r;
$fraction = $ring === null ? 0.0 : max(0.0, min(1.0, (float) $ring));
[$fg, $bg] = avatar_colors((string) ($user['avatar_seed'] ?? $user['handle'] ?? 'cadence'));
$fontSize = max(10, (int) round($size * 0.38));
?>
<span class="avatar-ring" style="width:<?= $outer ?>px;height:<?= $outer ?>px"
      <?php if ($ring !== null): ?>title="<?= e(round($fraction * 100)) ?>% of today's check-ins done"<?php endif; ?>>
  <svg width="<?= $outer ?>" height="<?= $outer ?>" viewBox="0 0 <?= $outer ?> <?= $outer ?>" aria-hidden="true">
    <circle class="ring-track" cx="<?= $outer / 2 ?>" cy="<?= $outer / 2 ?>" r="<?= $r ?>"></circle>
    <?php if ($fraction > 0): ?>
      <circle class="ring-fill" cx="<?= $outer / 2 ?>" cy="<?= $outer / 2 ?>" r="<?= $r ?>"
              stroke-dasharray="<?= $c ?>" stroke-dashoffset="<?= $c * (1 - $fraction) ?>"></circle>
    <?php endif; ?>
  </svg>
  <span class="avatar" style="width:<?= $size ?>px;height:<?= $size ?>px;background:<?= e($bg) ?>;color:<?= e($fg) ?>;font-size:<?= $fontSize ?>px">
    <?= e(avatar_initials((string) ($user['display_name'] ?? '?'))) ?>
  </span>
</span>
