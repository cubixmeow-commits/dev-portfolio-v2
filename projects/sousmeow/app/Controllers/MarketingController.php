<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Models\Recipe;
use SousMeow\Services\HomepageActivityPresenter;

final class MarketingController
{
    public function home(): void
    {
        $featured = Cookbook::featured();
        $featuredRecipes = $featured !== null ? Recipe::forCookbook((int) $featured['id']) : [];

        View::render('marketing/home', [
            'title'           => '',
            'pageCss'         => 'marketing',
            'featured'        => $featured,
            'featuredRecipes' => $featuredRecipes,
            'cookbooks'       => Cookbook::marketplace(),
            'activityBoard'   => HomepageActivityPresenter::bundle(),
        ]);
    }
}
