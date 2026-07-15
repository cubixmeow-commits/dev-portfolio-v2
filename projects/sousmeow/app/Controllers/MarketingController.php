<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Collection;
use SousMeow\Models\Cookbook;
use SousMeow\Models\CookbookStage;
use SousMeow\Models\Recipe;
use SousMeow\Services\CollectionResolver;

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

        $moneyShelf = null;
        $moneyCollection = Collection::findBySlug('money-major-decisions');
        if ($moneyCollection !== null) {
            $moneyBooks = CollectionResolver::cookbooksFor($moneyCollection);
            if (count($moneyBooks) >= (int) ($moneyCollection['min_display_count'] ?? 1)) {
                $moneyShelf = [
                    'collection' => $moneyCollection,
                    'cookbooks'  => $moneyBooks,
                ];
            }
        }

        View::render('marketing/home', [
            'title'           => '',
            'pageCss'         => 'marketing',
            'pageJs'          => 'marketing',
            'featured'        => $featured,
            'featuredStages'  => $featuredStages,
            'featuredRecipes' => $featuredRecipes,
            'cookbooks'       => $cookbooks,
            'moneyShelf'      => $moneyShelf,
        ]);
    }
}
