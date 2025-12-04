<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Face API Helper for interacting with face recognition API.
 */
class FaceApiHelper
{
  private const API_URL = 'https://face.nafish.me/api/rest/client-token';

  /**
   * Generate client token from Face API.
   *
   * @param string $code
   * @param string|null $bearerToken
   * @return array|null
   */
  public static function generateToken(): string|null
  {
    $data = json_encode(['code' => 'ORG1']);

    $headers = [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data)
    ];
    $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoicmVzdCIsIm9yZyI6eyJjb2RlIjoiT1JHMSJ9LCJpYXQiOjE3NjQ4NTM4MjgsImV4cCI6MTc2NDg1NzQyOH0.3b3OKeHOHg6qlbAR6j2Vdx1UhQ2PJ1pXU3E2i6DDwaA";
    $headers[] = "Authorization: Bearer $token";

    $ch = curl_init(self::API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close($ch); // Not needed in PHP 8+, resources are auto-cleaned

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
      return null;
    }
    return null;
  }
}
