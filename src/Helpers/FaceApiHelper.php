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
    public static function generateTokens(string $code = 'ORG1', ?string $bearerToken = null): ?array
    {
        $data = json_encode(['code' => $code]);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ];

        if ($bearerToken) {
            $headers[] = 'Authorization: Bearer ' . $bearerToken;
        }

        $ch = curl_init(self::API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }

        return null;
    }
}