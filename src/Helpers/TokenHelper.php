<?php

declare(strict_types=1);

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

/**
 * Token Helper for generating and validating JWT tokens.
 */
class TokenHelper
{
  private const SECRET_KEY = 'your-secret-key-here-change-in-production';
  private const ALGORITHM = 'HS256';

  /**
   * Generate access and refresh tokens.
   *
   * @param array $payload
   * @return array
   */
  public static function generate(array $payload): array
  {
    $accessPayload = [
      'iss' => 'your-app',
      'aud' => 'your-app-users',
      'iat' => time(),
      'exp' => time() + 86400, // 24 hour
      'type' => 'access',
      'user' => $payload
    ];

    $accessToken = JWT::encode($accessPayload, self::SECRET_KEY, self::ALGORITHM);

    $refreshPayload = [
      'iss' => 'your-app',
      'aud' => 'your-app-users',
      'iat' => time(),
      'exp' => time() + 604800, // 1 week
      'type' => 'refresh',
      "accessToken" => $accessToken
    ];


    $refreshToken = JWT::encode($refreshPayload, self::SECRET_KEY, self::ALGORITHM);

    return [
      'access' => $accessToken,
      'refresh' => $refreshToken
    ];
  }

  /**
   * Validate a token and return payload if valid.
   *
   * @param string $token
   * @return bool
   */
  public static function decode(string $token): stdClass
  {
    return JWT::decode($token, new Key(self::SECRET_KEY, self::ALGORITHM));
  }
}
