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
   * @return string|null
   */
  public static function generateToken(): ?string
  {
    try {
      $client = new \App\Core\HttpClient();
      $client->setHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . self::getApiToken()
      ]);

      $response = $client->post(self::getApiUrl(), ['code' => 'ORG1']);
      $decoded = $client->decodeJson($response);

      if ($response['status'] === 200) {
        if (isset($decoded['result']['token'])) {
          return $decoded['result']['token'];
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return null;
  }
}
