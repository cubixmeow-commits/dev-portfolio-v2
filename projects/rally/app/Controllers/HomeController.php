<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\View;

final class HomeController
{
    public function index(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('home', [
            'title' => 'Health stats competition',
            'pageCss' => 'home',
            'bodyClass' => 'page-home',
        ]);
    }
}
