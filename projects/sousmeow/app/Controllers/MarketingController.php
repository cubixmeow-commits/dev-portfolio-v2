<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Models\Recipe;
use SousMeow\Services\SiteStats;

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
            'stats'           => SiteStats::hero(),
            'heatmap'         => SiteStats::activityHeatmap(12),
            'popular'         => SiteStats::popularCookbooks(3),
            'activity'        => SiteStats::recentActivity(10),
        ]);
    }
}
