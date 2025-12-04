<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Token Helper for generating and validating tokens.
 */
class TokenHelper
{
  /**
   * Generate access and refresh tokens.
   *
   * @return array
   */
  public static function generate(array $payload): array
  {
    $token_id = rand(1111111111, 9999999999);
    $accessToken = EncryptionHelper::encryptJson([
      'id' => $token_id,
      "type" => "access",
      'payload' => $payload,
      'exp' => time() + 3600 // 1 hour expiration
    ]);
    $refreshToken = EncryptionHelper::encryptJson([
      "access_token_id" => $accessToken,
      "type" => "refresh",
      'exp' => time() + 604800 // 1 week expiration
    ]);

    return [
      'access' => $accessToken,
      'refresh' => $refreshToken
    ];
  }

  /**
   * Validate a token.
   *
   * @param string $token
   * @return bool
   */
  public static function validate(string $token): bool
  {
    try {
      $token = EncryptionHelper::decryptJson($token);
      if (!$token || !isset($token['exp']) || $token['exp'] < time()) {
        return false;
      }
      return true;
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Decode a token to get user data.
   *
   * @param string $token
   * @return array|null
   */
  public static function decode(string $token): ?array
  {
    if (!self::validate($token)) {
      return null;
    }

    $token = EncryptionHelper::decryptJson($token);
    return $token['user'] ?? null;
  }
}
