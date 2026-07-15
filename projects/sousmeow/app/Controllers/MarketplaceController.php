<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Category;
use SousMeow\Models\Collection;
use SousMeow\Models\Cookbook;
use SousMeow\Models\CookbookStage;
use SousMeow\Models\PantryField;
use SousMeow\Models\Recipe;
use SousMeow\Services\CollectionResolver;

final class MarketplaceController
{
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        View::render('marketplace/index', [
            'title'     => 'Find something',
            'pageCss'   => 'marketplace',
            'query'     => $query,
            'cookbooks' => Cookbook::marketplace($query),
        ]);
    }

    public function show(string $slug): void
    {
        $cookbook = Cookbook::findBySlug($slug);
        if ($cookbook === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Cookbook not found']);
            return;
        }

        $recipes = Recipe::forCookbook((int) $cookbook['id']);
        $recipeChecks = [];
        foreach ($recipes as $recipe) {
            $checks = Recipe::checks((int) $recipe['id']);
            if ($checks !== []) {
                $recipeChecks[(int) $recipe['id']] = $checks;
            }
        }

        // Surfaced Collections this Cookbook belongs to, for cross-links.
        $collections = [];
        foreach (CollectionResolver::surfaced(Collection::allVisible()) as $entry) {
            foreach ($entry['cookbooks'] as $member) {
                if ((int) $member['id'] === (int) $cookbook['id']) {
                    $collections[] = $entry['collection'];
                    break;
                }
            }
        }

        require_once dirname(__DIR__, 2) . '/database/seeds/career_helpers.php';
        require_once dirname(__DIR__, 2) . '/database/seeds/money_helpers.php';
        $relatedCookbooks = [];
        $cookbookSlug = (string) $cookbook['slug'];
        if (in_array($cookbookSlug, sm_money_collection_slugs(), true)) {
            foreach (sm_money_related_slugs($cookbookSlug) as $relatedSlug) {
                $related = Cookbook::listingBySlug($relatedSlug);
                if ($related !== null) {
                    $relatedCookbooks[] = $related;
                }
            }
        } elseif (in_array($cookbookSlug, sm_career_collection_slugs(), true)) {
            foreach (sm_career_related_slugs($cookbookSlug) as $relatedSlug) {
                $related = Cookbook::listingBySlug($relatedSlug);
                if ($related !== null) {
                    $relatedCookbooks[] = $related;
                }
            }
        } else {
            $category = Category::forCookbook((int) $cookbook['id']);
            if ($category !== null) {
                foreach (Cookbook::inCategory((int) $category['id']) as $peer) {
                    if ((int) $peer['id'] === (int) $cookbook['id']) {
                        continue;
                    }
                    $relatedCookbooks[] = $peer;
                    if (count($relatedCookbooks) >= 5) {
                        break;
                    }
                }
            }
        }

        View::render('marketplace/show', [
            'title'            => (string) $cookbook['title'],
            'pageCss'          => 'marketplace',
            'cookbook'         => $cookbook,
            'category'         => Category::forCookbook((int) $cookbook['id']),
            'collections'      => $collections,
            'stages'           => CookbookStage::forCookbook((int) $cookbook['id']),
            'recipes'          => $recipes,
            'recipeChecks'     => $recipeChecks,
            'fields'           => PantryField::forCookbook((int) $cookbook['id']),
            'relatedCookbooks' => $relatedCookbooks,
        ]);
    }
}
