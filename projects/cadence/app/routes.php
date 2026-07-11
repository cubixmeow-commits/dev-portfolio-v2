<?php

declare(strict_types=1);

/**
 * Route table. The front controller passes a fresh Router in; every
 * URL the application serves is declared here and nowhere else.
 */

use Cadence\Core\Router;
use Cadence\Controllers\DashboardController;

return static function (Router $router): void {
    $router->get('/', [DashboardController::class, 'home']);
};
