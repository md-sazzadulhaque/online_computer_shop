<?php
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/HomeModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/ProfileController.php';

// Try auto-login via remember me cookie
AuthController::tryRememberMe();

// Simple front controller router
$uri    = strtok($_SERVER['REQUEST_URI'], '?');
$base   = '/task1';
$path   = str_starts_with($uri, $base) ? substr($uri, strlen($base)) : $uri;
$path   = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$auth    = new AuthController();
$home    = new HomeController();
$profile = new ProfileController();

match (true) {
    $path === '/'                                 => $home->showHome(),
    $path === '/register' && $method === 'GET'    => $auth->showRegister(),
    $path === '/register' && $method === 'POST'   => $auth->handleRegister(),
    $path === '/login'    && $method === 'GET'    => $auth->showLogin(),
    $path === '/login'    && $method === 'POST'   => $auth->handleLogin(),
    $path === '/logout'   && $method === 'POST'   => $auth->handleLogout(),
    $path === '/profile'  && $method === 'GET'    => $profile->showProfile(),
    $path === '/profile'  && $method === 'POST'   => $profile->handleUpdate(),
    default => (function () {
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
    })(),
};
