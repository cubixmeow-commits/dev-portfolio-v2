<?php

declare(strict_types=1);

/**
 * Route table. The front controller passes a fresh Router in; every
 * URL the application serves is declared here and nowhere else.
 */

use Cadence\Core\Router;
use Cadence\Controllers\AuthController;
use Cadence\Controllers\DashboardController;
use Cadence\Controllers\ProfileController;

return static function (Router $router): void {
    $router->get('/', [DashboardController::class, 'home']);
    $router->get('/dashboard', [DashboardController::class, 'index']);

    // Auth
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/register/check-handle', [AuthController::class, 'checkHandle']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->get('/verify/{token}', [AuthController::class, 'verify']);
    $router->post('/verify/resend', [AuthController::class, 'resendVerification']);
    $router->get('/forgot-password', [AuthController::class, 'showForgot']);
    $router->post('/forgot-password', [AuthController::class, 'sendReset']);
    $router->get('/reset-password/{token}', [AuthController::class, 'showReset']);
    $router->post('/reset-password/{token}', [AuthController::class, 'reset']);

    // Account settings
    $router->get('/settings', [ProfileController::class, 'settings']);
    $router->post('/settings/profile', [ProfileController::class, 'updateProfile']);
    $router->post('/settings/password', [ProfileController::class, 'updatePassword']);
    $router->post('/settings/delete', [ProfileController::class, 'deleteAccount']);
};
