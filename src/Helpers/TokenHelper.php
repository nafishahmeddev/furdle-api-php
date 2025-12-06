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
  /**
   * Get JWT secret key from environment.
   *
   * @return string
   */
  private static function getSecretKey(): string
  {
    return getenv('JWT_SECRET') ?: 'your-secret-key-here-change-in-production';
  }

  /**
   * Get JWT algorithm from environment.
   *
   * @return string
   */
  private static function getAlgorithm(): string
  {
    return getenv('JWT_ALGORITHM') ?: 'HS256';
  }

  /**
   * Get JWT expiry time from environment.
   *
   * @return int
   */
  private static function getExpiry(): int
  {
    return (int)(getenv('JWT_EXPIRY') ?: 3600);
  }

  /**
   * Generate access and refresh tokens.
   *
   * @param array $payload
   * @return array
   */
  public static function generate(array $payload): array
  {
    $expiry = self::getExpiry();
    $accessPayload = [
      'iss' => 'your-app',
      'aud' => 'your-app-users',
      'iat' => time(),
      'exp' => time() + $expiry, // From environment
      'type' => 'access',
      'user' => $payload
    ];

    $accessToken = JWT::encode($accessPayload, self::getSecretKey(), self::getAlgorithm());

    $refreshPayload = [
      'iss' => 'your-app',
      'aud' => 'your-app-users',
      'iat' => time(),
      'exp' => time() + ($expiry * 7), // 7x access token expiry
      'type' => 'refresh',
      "accessToken" => $accessToken,
      "user"=> $payload
    ];


    $refreshToken = JWT::encode($refreshPayload, self::getSecretKey(), self::getAlgorithm());

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
    return JWT::decode($token, new Key(self::getSecretKey(), self::getAlgorithm()));
  }
}
