<?php

declare(strict_types=1);

/**
 * StreamHive front controller (entry point).
 *
 * Every request that is not an existing file is routed here by Nginx. This file
 * starts the session, registers the routes and dispatches the current request
 * to the matching controller action.
 */

session_start();

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
