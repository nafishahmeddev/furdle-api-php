<?php
// API routes
return function ($router) {
    // API group with JSON middleware
    $router->group('/api', function ($router) {
        $router->get('/status', function ($req, $res) {
            $res->json(['status' => 'OK', 'timestamp' => time()]);
        });

        $router->post('/data', function ($req, $res) {
            $data = $req->json();
            $res->json(['received' => $data]);
        });

        // Auth subgroup
        $router->group('/auth', function ($router) {
            $router->post('/login', 'App\Controllers\AuthController@login');
            $router->post('/token','App\Controllers\AuthController@token');
            
            // add auth middleware 
            $router->get('/verify', 'App\Controllers\AuthController@verify', ["App\Middlewares\AuthMiddleware"]);
            $router->post('/validate-password', 'App\Controllers\AuthController@validatePassword', ['App\Middlewares\AuthMiddleware']);
        });

        // Users subgroup
        $router->group('/users', function ($router) {
            $router->post('/types', 'App\Controllers\UserController@types');
            $router->post('/lookup', 'App\Controllers\UserController@lookup');
            $router->post('/register', 'App\Controllers\UserController@register');
        }, ['App\Middlewares\AuthMiddleware']);

        // Events
        $router->group('/events', function ($router) {
            $router->post('', 'App\Controllers\EventController@index');
            $router->post('/attend', 'App\Controllers\EventController@attend');
        }, ['App\Middlewares\AuthMiddleware']);

        // Constants
        $router->get('/constants', 'App\Controllers\ConstantsController@index', ['App\Middlewares\AuthMiddleware']);

        // Webhooks (no auth required for external services)
        $router->group('/webhooks', function ($router) {
            $router->post('/event', 'App\Controllers\WebhookController@event');
        });
    }, ['App\Middlewares\JsonMiddleware', 'App\Middlewares\LoggingMiddleware']);
};
