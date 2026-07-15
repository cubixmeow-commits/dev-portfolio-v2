<?php

declare(strict_types=1);

use Rally\Controllers\AuthController;
use Rally\Controllers\DashboardController;
use Rally\Controllers\HomeController;
use Rally\Controllers\MatchController;
use Rally\Controllers\PlayerController;
use Rally\Controllers\SimulationController;
use Rally\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/dashboard', [DashboardController::class, 'index']);

    $router->get('/matches', [MatchController::class, 'index']);
    $router->get('/matches/create', [MatchController::class, 'create']);
    $router->post('/matches/create', [MatchController::class, 'store']);
    $router->get('/matches/{id}', [MatchController::class, 'show']);
    $router->get('/matches/{id}/accept', [MatchController::class, 'showAccept']);
    $router->post('/matches/{id}/accept', [MatchController::class, 'accept']);
    $router->post('/matches/{id}/decline', [MatchController::class, 'decline']);
    $router->get('/matches/{id}/history', [MatchController::class, 'history']);
    $router->get('/matches/{id}/day/{day}', [MatchController::class, 'day']);
    $router->get('/matches/{id}/day/{day}/share', [MatchController::class, 'share']);

    $router->get('/players/{id}', [PlayerController::class, 'show']);

    $router->get('/simulation', [SimulationController::class, 'index']);
    $router->post('/simulation', [SimulationController::class, 'update']);
};
