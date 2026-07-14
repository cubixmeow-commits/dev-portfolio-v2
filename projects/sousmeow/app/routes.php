<?php

declare(strict_types=1);

use SousMeow\Controllers\AccountController;
use SousMeow\Controllers\AdminController;
use SousMeow\Controllers\AuthController;
use SousMeow\Controllers\CategoryController;
use SousMeow\Controllers\ExportController;
use SousMeow\Controllers\KitchenController;
use SousMeow\Controllers\LegalController;
use SousMeow\Controllers\MarketingController;
use SousMeow\Controllers\MarketplaceController;
use SousMeow\Controllers\ProjectController;
use SousMeow\Controllers\RunnerController;
use SousMeow\Core\Router;

return static function (Router $router): void {
    // Public marketing site.
    $router->get('/', [MarketingController::class, 'home']);
    $router->get('/marketplace', [MarketplaceController::class, 'index']);
    $router->get('/cookbooks/{slug}', [MarketplaceController::class, 'show']);

    // Discovery taxonomy: one shared index and one shared category route
    // serve all categories (no per-category controllers or templates).
    $router->get('/categories', [CategoryController::class, 'index']);
    $router->get('/categories/{slug}', [CategoryController::class, 'show']);

    // Legal pages.
    $router->get('/terms', [LegalController::class, 'terms']);
    $router->get('/privacy', [LegalController::class, 'privacy']);

    // Authentication.
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
    $router->get('/reset-password/{token}', [AuthController::class, 'showResetPassword']);
    $router->post('/reset-password/{token}', [AuthController::class, 'resetPassword']);
    $router->get('/verify-email/pending', [AuthController::class, 'showVerifyPending']);
    $router->get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
    $router->post('/verify-email/resend', [AuthController::class, 'resendVerification']);

    // Legacy password route redirects to account security.
    $router->get('/account/password', [AuthController::class, 'showChangePassword']);
    $router->post('/account/password', [AuthController::class, 'changePassword']);

    // Account settings.
    $router->get('/account', [AccountController::class, 'index']);
    $router->get('/account/profile', [AccountController::class, 'profile']);
    $router->post('/account/profile', [AccountController::class, 'updateProfile']);
    $router->get('/account/preferences', [AccountController::class, 'preferences']);
    $router->post('/account/preferences', [AccountController::class, 'updatePreferences']);
    $router->get('/account/security', [AccountController::class, 'security']);
    $router->post('/account/security/password', [AccountController::class, 'updatePassword']);
    $router->post('/account/security/email', [AccountController::class, 'requestEmailChange']);
    $router->get('/account/email/confirm/{token}', [AccountController::class, 'confirmEmailChange']);
    $router->get('/account/data', [AccountController::class, 'data']);
    $router->post('/account/data/export', [AccountController::class, 'exportData']);
    $router->post('/account/delete', [AccountController::class, 'deleteAccount']);

    // First-run onboarding.
    $router->get('/onboarding', [AccountController::class, 'showOnboarding']);
    $router->post('/onboarding', [AccountController::class, 'completeOnboarding']);

    // Kitchen (the signed-in home).
    $router->get('/kitchen', [KitchenController::class, 'index']);

    // Projects and the Pantry.
    $router->post('/projects', [ProjectController::class, 'create']);
    $router->get('/projects/{id}', [ProjectController::class, 'show']);
    $router->get('/projects/{id}/pantry', [ProjectController::class, 'pantry']);
    $router->post('/projects/{id}/pantry', [ProjectController::class, 'savePantry']);
    $router->post('/projects/{id}/delete', [ProjectController::class, 'delete']);

    // The Recipe Runner.
    $router->get('/projects/{id}/run/{position}', [RunnerController::class, 'step']);
    $router->post('/projects/{id}/run/{position}/paste', [RunnerController::class, 'paste']);
    $router->post('/projects/{id}/run/{position}/example', [RunnerController::class, 'pasteExample']);
    $router->post('/projects/{id}/run/{position}/checks', [RunnerController::class, 'toggleCheck']);
    $router->post('/projects/{id}/run/{position}/approve', [RunnerController::class, 'approve']);
    $router->post('/projects/{id}/run/{position}/reopen', [RunnerController::class, 'reopen']);
    $router->post('/projects/{id}/run/{position}/edit', [RunnerController::class, 'saveEdit']);
    $router->post('/projects/{id}/run/{position}/restore', [RunnerController::class, 'restore']);

    // Project Kit exports.
    $router->get('/projects/{id}/export', [ExportController::class, 'show']);
    $router->post('/projects/{id}/export', [ExportController::class, 'create']);
    $router->get('/exports/{id}/download', [ExportController::class, 'download']);

    // Admin overview (admin accounts exist only via scripts/seed.php).
    $router->get('/admin', [AdminController::class, 'index']);
};
