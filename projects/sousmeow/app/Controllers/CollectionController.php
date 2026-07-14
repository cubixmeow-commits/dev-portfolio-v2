<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Collection;
use SousMeow\Services\CollectionResolver;

/**
 * One shared route serves every Collection detail page. A Collection that
 * is hidden (is_visible = 0) or below its min_display_count returns the
 * existing 404 view, exactly as it is absent from navigation and strips.
 */
final class CollectionController
{
    public function show(string $slug): void
    {
        $collection = Collection::findBySlug($slug);
        if ($collection === null || !CollectionResolver::isSurfaced($collection)) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Collection not found']);
            return;
        }

        View::render('collections/show', [
            'title'      => (string) $collection['name'],
            'pageCss'    => ['marketplace', 'categories'],
            'collection' => $collection,
            'cookbooks'  => CollectionResolver::cookbooksFor($collection),
        ]);
    }
}
