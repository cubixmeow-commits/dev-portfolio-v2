<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;

/**
 * Public product-philosophy documents.
 * Mirrors docs/*.md for in-app linking from the homepage and authoring notes.
 */
final class PhilosophyController
{
    public function productLaw002(): void
    {
        View::render('philosophy/product-law-002', [
            'title'   => 'Product Law 002 — Remove Cognitive Load',
            'pageCss' => 'account',
        ]);
    }
}
