<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\View;
use Cadence\Models\ActivityEvent;
use Cadence\Models\Participation;

final class FeedController
{
    private const PAGE_SIZE = 20;

    public function index(): void
    {
        $filter = ($_GET['filter'] ?? '') === 'mine' && Auth::check() ? 'mine' : 'everyone';
        $before = isset($_GET['before']) ? max(0, (int) $_GET['before']) : null;

        $events = ActivityEvent::feed(
            $before ?: null,
            self::PAGE_SIZE + 1,
            $filter === 'mine' ? Auth::id() : null
        );

        $hasMore = count($events) > self::PAGE_SIZE;
        $events = array_slice($events, 0, self::PAGE_SIZE);
        $nextCursor = $hasMore && $events !== [] ? (int) end($events)['id'] : null;

        $rings = Participation::ringMap(array_column($events, 'user_id'));

        // fetch() asks for the fragment: just the rows plus the next
        // cursor, for the Load more button. Full page render otherwise.
        if (($_GET['fragment'] ?? '') === '1') {
            $html = '';
            foreach ($events as $event) {
                $html .= View::capture('feed/event-row', ['event' => $event, 'rings' => $rings], false);
            }
            json_response(['html' => $html, 'nextCursor' => $nextCursor]);
        }

        View::render('feed/index', [
            'title'      => 'Feed',
            'active'     => 'feed',
            'page_css'   => 'community',
            'events'     => $events,
            'rings'      => $rings,
            'filter'     => $filter,
            'nextCursor' => $nextCursor,
        ]);
    }
}
