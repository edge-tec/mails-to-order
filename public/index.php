<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables if .env exists
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Session security configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Check if installation is needed
$installedLock = __DIR__ . '/../storage/installed.lock';
if (!file_exists($installedLock) && file_exists(__DIR__ . '/install.php') && strpos($uri, '/install') === false) {
    header('Location: /install.php');
    exit;
}

// Route matching engine
$routes = require __DIR__ . '/../routes/web.php';
$routeKey = "{$method} {$uri}";

$matchedHandler = null;
$params = [];

if (isset($routes[$routeKey])) {
    $matchedHandler = $routes[$routeKey];
} else {
    // Dynamic parameter matching
    foreach ($routes as $pattern => $handler) {
        list($routeMethod, $routePath) = explode(' ', $pattern, 2);
        if ($routeMethod !== $method) continue;

        // Convert {param} to regex pattern
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $regex = "#^" . $regex . "$#";

        if (preg_match($regex, $uri, $matches)) {
            $matchedHandler = $handler;
            foreach ($matches as $key => $val) {
                if (is_string($key)) {
                    $params[$key] = $val;
                }
            }
            break;
        }
    }
}

if ($matchedHandler) {
    list($controllerClass, $methodName) = $matchedHandler;
    try {
        $controller = new $controllerClass();
        call_user_func_array([$controller, $methodName], array_values($params));
    } catch (\Throwable $e) {
        error_log("Error executing route {$routeKey}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        http_response_code(500);
        if (config('app.env') === 'local') {
            echo "<h1>Application Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo "<h1>500 — Server Error</h1><p>Something went wrong on our end. Please try again later.</p>";
        }
    }
} else {
    http_response_code(404);
    view('home.404', ['title' => '404 Page Not Found']);
}
