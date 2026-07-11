<div class="container feed-wrap">
  <div class="page-head">
    <div>
      <h1>Activity</h1>
      <p>What the community is building, as it happens.</p>
    </div>
    <?php if ($auth !== null): ?>
      <nav class="tab-row" aria-label="Feed filter">
        <a class="tab<?= $filter === 'everyone' ? ' tab-active' : '' ?>" href="<?= e(url('/feed')) ?>">Everyone</a>
        <a class="tab<?= $filter === 'mine' ? ' tab-active' : '' ?>" href="<?= e(url('/feed?filter=mine')) ?>">Challenges I'm in</a>
      </nav>
    <?php endif; ?>
  </div>

  <?php if ($events === []): ?>
    <div class="card">
      <div class="empty">
        <h3>Nothing here yet</h3>
        <?php if ($filter === 'mine'): ?>
          <p>Join a challenge and this feed fills with your people.</p>
          <p style="margin-top: var(--sp-4)"><a class="btn btn-primary" href="<?= e(url('/challenges')) ?>">Explore challenges</a></p>
        <?php else: ?>
          <p>Activity shows up as members join challenges and check in.</p>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <ul class="feed-list card" id="feed-list">
      <?php foreach ($events as $event): ?>
        <?php Cadence\Core\View::partial('feed/event-row', ['event' => $event, 'rings' => $rings]); ?>
      <?php endforeach; ?>
    </ul>

    <?php if ($nextCursor !== null): ?>
      <div class="feed-more">
        <a class="btn btn-secondary" id="feed-more-button"
           href="<?= e(url('/feed?' . http_build_query(array_filter(['filter' => $filter === 'mine' ? 'mine' : '', 'before' => $nextCursor])))) ?>"
           data-fragment-url="<?= e(url('/feed?' . http_build_query(array_filter(['filter' => $filter === 'mine' ? 'mine' : '', 'fragment' => 1])))) ?>"
           data-cursor="<?= e((string) $nextCursor) ?>">Load more</a>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<script src="<?= e(url('/assets/js/feed.js')) ?>" defer></script>
