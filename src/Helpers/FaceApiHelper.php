<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Face API Helper for interacting with face recognition API.
 */
class FaceApiHelper
{
  /**
   * Get Face API URL from environment.
   *
   * @return string
   */
  private static function getApiUrl(): string
  {
    return getenv('FACE_API_URL') ?: 'https://face.nafish.me/api/rest/client-token';
  }

  /**
   * Get Face API Token from environment.
   *
   * @return string
   */
  private static function getApiToken(): string
  {
    return getenv('FACE_API_TOKEN') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoicmVzdCIsIm9yZyI6eyJjb2RlIjoiT1JHMSJ9LCJpYXQiOjE3NjQ5MTczODN9.o2HLf6_CVTGK6a4pkgcQd4rnFBoP8xfTXLj97MTuyn4';
  }

  /**
   * Generate client token from Face API.
   *
   * @param string $code
   * @param string|null $bearerToken
   * @return array|null
   */
  public static function generateToken(): ?string
  {
    $data = json_encode(['code' => 'ORG1']);

    $headers = [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data)
    ];
    $token = self::getApiToken();
    $headers[] = "Authorization: Bearer $token";

    $ch = curl_init(self::getApiUrl());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); // Not needed in PHP 8+, resources are auto-cleaned

    try {
      $data = null;
      if ($httpCode === 200 && $response) {
        $decoded = json_decode($response, true);
        if(isset($decoded['result'])) {
          if(isset($decoded['result']['token'])) {
            return $decoded['result']['token'];
          }
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return null;
  }
}
