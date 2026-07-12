<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Models\PantryField;
use SousMeow\Models\Recipe;

final class MarketplaceController
{
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        View::render('marketplace/index', [
            'title'     => 'Marketplace',
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

        View::render('marketplace/show', [
            'title'    => (string) $cookbook['title'],
            'pageCss'  => 'marketplace',
            'cookbook' => $cookbook,
            'recipes'  => Recipe::forCookbook((int) $cookbook['id']),
            'fields'   => PantryField::forCookbook((int) $cookbook['id']),
        ]);
    }
}
