<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\View;

final class LegalController
{
    public function terms(): void
    {
        View::render('legal/terms', [
            'title'   => 'Terms of Service',
            'pageCss' => 'account',
        ]);
    }

    public function privacy(): void
    {
        View::render('legal/privacy', [
            'title'   => 'Privacy Policy',
            'pageCss' => 'account',
        ]);
    }
}
