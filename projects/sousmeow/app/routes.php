<?php

declare(strict_types=1);

use SousMeow\Controllers\AdminController;
use SousMeow\Controllers\AuthController;
use SousMeow\Controllers\ExportController;
use SousMeow\Controllers\KitchenController;
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

    // Authentication.
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);

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
