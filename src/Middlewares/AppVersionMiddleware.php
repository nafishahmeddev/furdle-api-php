<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

/**
 * App Version Check middleware.
 * Validates X-Device-Type and X-App-Build-Number headers.
 */
class AppVersionMiddleware implements Middleware
{
    private const VALID_DEVICE_TYPES = ['ios', 'android', 'linux', 'macos', 'windows'];
    private const MIN_BUILD_NUMBERS = [
        'ios' => 2,
        'android' => 2,
        'linux' => 1,
        'macos' => 1,
        'windows' => 1,
    ]; // Minimum required build numbers by platform

    public function handle(Request $req, Response $res, callable $next): void
    {
        $deviceType = $req->header('X-Device-Type');
        $buildNumber = $req->header('X-App-Build-Number');

        // Check if device type is provided and valid
        if (!$deviceType || !in_array(strtolower($deviceType), self::VALID_DEVICE_TYPES, true)) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Invalid or missing X-Device-Type header. Must be one of: ' . implode(', ', self::VALID_DEVICE_TYPES)
            ]);
            return;
        }

        // Check if build number is provided and valid
        $deviceTypeLower = strtolower($deviceType);
        $minBuild = self::MIN_BUILD_NUMBERS[$deviceTypeLower] ?? 1;
        if (!$buildNumber || !is_numeric($buildNumber) || (int)$buildNumber < $minBuild) {
            $res->status(403)->json([
                'code' => 'FORCE_UPDATE_APP',
                'message' => 'App version too old. Please update to the latest version. Minimum build number for ' . $deviceType . ' is ' . $minBuild
            ]);
            return;
        }

        // Headers are valid, proceed
        $next();
    }
}