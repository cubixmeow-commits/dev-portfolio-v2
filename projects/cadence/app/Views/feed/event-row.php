<?php
/**
 * One feed row. Expects $event (joined row from ActivityEvent::feed)
 * and $rings (user id => fraction map).
 */
use Cadence\Models\ActivityEvent;
?>
<li class="feed-row">
  <a href="<?= e(url('/u/' . $event['handle'])) ?>" aria-hidden="true" tabindex="-1">
    <?php Cadence\Core\View::partial('layout/avatar', [
        'user' => $event,
        'size' => 36,
        'ring' => $rings[(int) $event['user_id']] ?? null,
    ]); ?>
  </a>
  <div class="feed-row-body">
    <p><?= ActivityEvent::sentence($event) ?></p>
    <?php
      $note = null;
      if (!empty($event['meta'])) {
          $meta = json_decode((string) $event['meta'], true);
          if (is_array($meta) && !empty($meta['note'])) {
              $note = (string) $meta['note'];
          }
      }
    ?>
    <?php if ($note !== null): ?>
      <p class="muted small feed-note">"<?= e($note) ?>"</p>
    <?php endif; ?>
    <span class="muted small"><?= e(time_ago((string) $event['created_at'])) ?></span>
  </div>
</li>
