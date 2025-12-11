<?php

declare(strict_types=1);

namespace App\Helpers;

use GuzzleHttp\Client;

/**
 * Face API Helper for interacting with face recognition API.
 */
class FaceApiHelper
{
  /**
   * get client
   */
  public static function getClient(): Client
  {
    $url = getenv('FACE_API_URL');
    $token = getenv('FACE_API_TOKEN');

    return new Client([
      "base_uri" => $url,
      "headers" => [
        "Authorization" => "Bearer $token",
        "Content-Type" => "application/json"
      ]
    ]);
  }

  public static function generateToken(): ?string
  {
    try {
      $client = self::getClient();
      $response = $client->post("/api/rest/client-token", [
        'json' => ['code' => 'ORG1']
      ]);


      $data = json_decode($response->getBody()->getContents(), true);
      return $data['result']['token'] ?? null;
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
