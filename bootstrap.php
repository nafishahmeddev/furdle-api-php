<?php
declare(strict_types=1);

/**
 * Application Bootstrap
 *
 * Initializes the auto-router, loads routes, and dispatches requests.
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
use App\Helpers\EnvHelper;
EnvHelper::load(__DIR__ . '/.env');

use App\Core\AutoRouter;

$router = new AutoRouter();

// Manually load specified route files
$routeFiles = [
    __DIR__ . '/src/Routes/api.php',
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        $returned = require $file;
        if (is_callable($returned)) {
            $returned($router);
        } elseif (is_array($returned)) {
            foreach ($returned as $route) {
                if (isset($route['method'], $route['path'], $route['handler'])) {
                    $middleware = $route['middleware'] ?? [];
                    $router->add($route['method'], $route['path'], $route['handler'], $middleware);
                }
            }
        }
    }
}

try {
    $router->dispatch();
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'Internal Server Error: ' . $e->getMessage();
}