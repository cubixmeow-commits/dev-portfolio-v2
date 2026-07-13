<?php
/**
 * Original cat mascot in a few poses, used sparingly on empty states and
 * error pages. Hand-authored line-art SVG; $pose selects small variants
 * (whisker tilt, eyes, prop) so the character stays restrained.
 *
 * @var string $pose one of: searching, guarding, napping, spill,
 *                   cooking, cheering
 */
$pose = $pose ?? 'cooking';
$eyes = match ($pose) {
    'napping'  => '<path d="M25 36 Q28 38 31 36 M41 36 Q44 38 47 36" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none"/>',
    'cheering' => '<path d="M25 35 Q28 32 31 35 M41 35 Q44 32 47 35" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none"/>',
    default    => '<circle cx="28" cy="36" r="2.4" fill="currentColor"/><circle cx="44" cy="36" r="2.4" fill="currentColor"/>',
};
$prop = match ($pose) {
    'searching' => '<circle cx="59" cy="52" r="7.5" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="M64.5 57.5 L70 63" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>',
    'spill'     => '<path d="M56 60 Q61 55 66 60 L64 65 Q61 67 58 65 Z" fill="var(--mascot-accent, #E8B296)" stroke="currentColor" stroke-width="2"/><path d="M67 59 Q70 57 71 54" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/>',
    'cooking'   => '<path d="M54 56 h16 l-2 9 q-6 3 -12 0 Z" fill="var(--mascot-accent, #E8B296)" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M58 52 q1 -3 0 -5 M63 52 q1 -3 0 -5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/>',
    'cheering'  => '<path d="M12 28 L7 22 M60 28 L65 22" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="8" cy="17" r="1.6" fill="var(--mascot-accent, #E8B296)"/><circle cx="64" cy="17" r="1.6" fill="var(--mascot-accent, #E8B296)"/>',
    default     => '',
};
?><svg class="mascot mascot-<?= e($pose) ?>" width="88" height="88" viewBox="0 0 76 76" fill="none" aria-hidden="true" focusable="false">
  <ellipse cx="38" cy="66" rx="24" ry="4.5" fill="var(--mascot-shadow, rgba(0,0,0,0.07))"/>
  <path d="M20 26 C17 17 18 10 22 8.5 C25 7.4 28.5 10 30.5 13" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" fill="var(--mascot-ear, #F6E7D7)"/>
  <path d="M52 26 C55 17 54 10 50 8.5 C47 7.4 43.5 10 41.5 13" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" fill="var(--mascot-ear, #F6E7D7)"/>
  <ellipse cx="36" cy="40" rx="22" ry="19" fill="var(--mascot-face, #FDF6EC)" stroke="currentColor" stroke-width="2.4"/>
  <!-- Chef's toque: a pleated puff sitting on a band -->
  <path d="M26 16 C21.5 15.5 21.5 9 26 8.5 C24 4 31 3.5 32.5 7.5 C34 4 38 4 39.5 7.5 C41 3.5 48 4 46 8.5 C50.5 9 50.5 15.5 46 16 Z" fill="var(--mascot-hat, #FFFFFF)" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/>
  <rect x="25" y="14.5" width="22" height="7.6" rx="2.6" fill="var(--mascot-hat, #FFFFFF)" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/>
  <?= $eyes ?>
  <path d="M36 41.5 L34.6 43.2 Q36 44.4 37.4 43.2 Z" fill="currentColor"/>
  <path d="M33 46.5 Q36 49 39 46.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
  <path d="M15 41 L8 39.6 M15.5 45 L9 46.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
  <path d="M57 41 L64 39.6 M56.5 45 L63 46.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
  <?= $prop ?>
</svg>
