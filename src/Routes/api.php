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
            $router->post('/token', 'App\Controllers\AuthController@token');

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
        $router->group('/third-party', function ($router) {
            $router->post('', 'App\Controllers\ThirdPartyController@index');
            // Debug endpoint to test external API
            $router->get('/debug/{form_no}/{session}', function($req, $res) {
                $form_no = $req->param('form_no');
                $session = $req->param('session');
                
                $client = new \App\Core\HttpClient();
                $client->setVerifySSL(false);
                $client->setHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Al-Ameen-Face/1.0)',
                    'Accept' => 'application/json, text/plain, */*'
                ]);
                
                try {
                    $response = $client->get(
                        'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
                        [
                            'action' => 'get_students_by_form_no',
                            'controll_session' => $session,
                            'form_no' => $form_no
                        ]
                    );
                    
                    $res->json([
                        'status' => $response['status'],
                        'headers' => $response['headers'],
                        'body_preview' => substr($response['body'], 0, 1000),
                        'body_length' => strlen($response['body']),
                        'is_json' => json_decode($response['body'], true) !== null
                    ]);
                } catch (\Exception $e) {
                    $res->json(['error' => $e->getMessage()]);
                }
            });
        });
    }, ['App\Middlewares\JsonMiddleware', 'App\Middlewares\LoggingMiddleware', 'App\Middlewares\CorsMiddleware']);
};
