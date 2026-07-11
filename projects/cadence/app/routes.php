<?php

declare(strict_types=1);

/**
 * Route table. The front controller passes a fresh Router in; every
 * URL the application serves is declared here and nowhere else.
 */

use Cadence\Core\Router;
use Cadence\Controllers\AdminController;
use Cadence\Controllers\AdminToolsController;
use Cadence\Controllers\AuthController;
use Cadence\Controllers\ChallengeController;
use Cadence\Controllers\CheckInController;
use Cadence\Controllers\DashboardController;
use Cadence\Controllers\FeedController;
use Cadence\Controllers\LeaderboardController;
use Cadence\Controllers\ProfileController;

return static function (Router $router): void {
    $router->get('/', [DashboardController::class, 'home']);
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/notifications', [DashboardController::class, 'notifications']);

    // Challenges and check-ins
    $router->get('/challenges', [ChallengeController::class, 'index']);
    $router->get('/challenges/{slug}', [ChallengeController::class, 'show']);
    $router->post('/challenges/{slug}/join', [ChallengeController::class, 'join']);
    $router->post('/challenges/{slug}/leave', [ChallengeController::class, 'leave']);
    $router->post('/challenges/{slug}/checkin', [CheckInController::class, 'store']);

    // Community
    $router->get('/feed', [FeedController::class, 'index']);
    $router->get('/leaderboard', [LeaderboardController::class, 'index']);
    $router->get('/u/{handle}', [ProfileController::class, 'show']);

    // Auth
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/register/check-handle', [AuthController::class, 'checkHandle']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->post('/demo-login', [AuthController::class, 'demoLogin']);
    $router->get('/verify/{token}', [AuthController::class, 'verify']);
    $router->post('/verify/resend', [AuthController::class, 'resendVerification']);
    $router->get('/forgot-password', [AuthController::class, 'showForgot']);
    $router->post('/forgot-password', [AuthController::class, 'sendReset']);
    $router->get('/reset-password/{token}', [AuthController::class, 'showReset']);
    $router->post('/reset-password/{token}', [AuthController::class, 'reset']);

    // Admin
    $router->get('/admin', [AdminController::class, 'index']);
    $router->get('/admin/tools', [AdminToolsController::class, 'index']);
    $router->post('/admin/tools/run', [AdminToolsController::class, 'run']);
    $router->get('/admin/tools/status/{id}', [AdminToolsController::class, 'status']);

    // Account settings
    $router->get('/settings', [ProfileController::class, 'settings']);
    $router->post('/settings/profile', [ProfileController::class, 'updateProfile']);
    $router->post('/settings/password', [ProfileController::class, 'updatePassword']);
    $router->post('/settings/delete', [ProfileController::class, 'deleteAccount']);
};
