<?php
// API routes
return function($router) {
    // API group with JSON middleware
    $router->group('/api', function($router) {
        $router->get('/status', function($req, $res) {
            $res->json(['status' => 'OK', 'timestamp' => time()]);
        });

        $router->post('/data', function($req, $res) {
            $data = $req->json();
            $res->json(['received' => $data]);
        });

        // Auth subgroup
        $router->group('/auth', function($router) {
            $router->post('/login', 'App\Controllers\AuthController@login');
            $router->post('/verify', 'App\Controllers\AuthController@verify');
            $router->post('/validate-password', 'App\Controllers\AuthController@validatePassword');
        });

        // Users subgroup
        $router->group('/users', function($router) {
            $router->post('/lookup', 'App\Controllers\UserController@lookup');
            $router->post('/register', 'App\Controllers\UserController@register');
        });

        // Events
        $router->group('/events', function($router) {
            $router->post('', 'App\Controllers\EventController@index');
            $router->post('/{id}/attend', 'App\Controllers\EventController@attend');
        });

        // Constants
        $router->get('/constants', 'App\Controllers\ConstantsController@index');
    }, ['App\Middlewares\JsonMiddleware']);
};