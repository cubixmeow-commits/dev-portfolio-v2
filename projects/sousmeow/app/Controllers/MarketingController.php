<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Models\CookbookStage;
use SousMeow\Models\Recipe;

final class MarketingController
{
    public function home(): void
    {
        $cookbooks = Cookbook::marketplace();

        // The featured Cookbook is shown as a full artifact: cover facts,
        // its stages, and every Recipe with its estimated time.
        $featured = Cookbook::featured();
        $featuredStages = [];
        $featuredRecipes = [];
        if ($featured !== null) {
            $featuredStages = CookbookStage::forCookbook((int) $featured['id']);
            $featuredRecipes = Recipe::forCookbook((int) $featured['id']);
        }

        View::render('marketing/home', [
            'title'           => '',
            'pageCss'         => 'marketing',
            'pageJs'          => 'marketing',
            'featured'        => $featured,
            'featuredStages'  => $featuredStages,
            'featuredRecipes' => $featuredRecipes,
            'cookbooks'       => $cookbooks,
        ]);
    }
}
