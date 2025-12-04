<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Encryption Helper for encrypting and decrypting data.
 */
class EncryptionHelper
{
    private const CIPHER = 'aes-256-cbc';
    private const KEY = 'your-secret-key-here-32-chars'; // 32 chars for AES-256

    /**
     * Encrypt a string.
     *
     * @param string $data
     * @return string
     */
    public static function encryptString(string $data): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER));
        $encrypted = openssl_encrypt($data, self::CIPHER, self::KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt a string.
     *
     * @param string $data
     * @return string
     */
    public static function decryptString(string $data): string
    {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, self::CIPHER, self::KEY, 0, $iv);
    }

    /**
     * Encrypt JSON data.
     *
     * @param mixed $data
     * @return string
     */
    public static function encryptJson(mixed $data): string
    {
        $json = json_encode($data);
        return self::encryptString($json);
    }

    /**
     * Decrypt JSON data.
     *
     * @param string $data
     * @return mixed
     */
    public static function decryptJson(string $data): mixed
    {
        $json = self::decryptString($data);
        return json_decode($json, true);
    }
}