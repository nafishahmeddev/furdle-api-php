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

    $decoded = TokenHelper::decode($token);
    if (!is_object($decoded) || !property_exists($decoded, 'user')) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid token'
      ]);
      return;
    }
    $req->auth = $decoded->user;

    $next();
  }
}
