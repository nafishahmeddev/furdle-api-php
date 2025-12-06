<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Logger;

/**
 * Logging middleware to log requests using Monolog.
 */
class LoggingMiddleware implements Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        // Log the request using our static Logger helper with pretty formatted headers
        Logger::info("HTTP Request: {$req->method} {$req->path}", [
            'headers' => json_encode($req->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ], 'requests');
        
        $next();
    }
}