<?php

declare(strict_types=1);

namespace App\Middlewares;


use App\Core\Middleware;
use App\Helpers\TokenHelper;
use App\Core\Request;
use App\Core\Response;

/**
 * Authentication middleware.
 */
class AuthMiddleware implements Middleware
{
  public function handle(Request $req, Response $res, callable $next): void
  {
    $authHeader = $req->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      $res->status(401)->send('Unauthorized');
      return;
    }

    $token = substr($authHeader, 7); // Remove 'Bearer '

    if (!TokenHelper::validate($token)) {
      $res->status(401)->send('Invalid token');
      return;
    }

    // Set auth data (dummy for now, in real app decode token to get user data)
    $req->auth = [
      'user_id' => '123',
      'name' => 'John Doe',
      'email' => 'john.doe@example.com'
    ];

    $next();
  }
}
