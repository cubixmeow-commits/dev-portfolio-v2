<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Services\HomepageActivityPresenter;

final class MarketingController
{
    public function home(): void
    {
        $allCookbooks = Cookbook::marketplace();
        $featuredCookbooks = array_values(array_filter(
            $allCookbooks,
            static fn(array $c): bool => (int) $c['is_executable'] === 1
        ));
        $featuredCookbooks = array_slice($featuredCookbooks, 0, 3);

        View::render('marketing/home', [
            'title'             => '',
            'pageCss'           => 'marketing',
            'featuredCookbooks' => $featuredCookbooks,
            'activityBoard'     => HomepageActivityPresenter::bundle(),
        ]);
    }
}
