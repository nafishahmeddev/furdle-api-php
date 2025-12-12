<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * CacheHelper for file-based caching with expiration.
 */
class CacheHelper
{
  /**
   * Summary of cacheDir
   * @var string
   */
  private static $cacheDir = __DIR__ . '/../../writable/cache/';

  /**
   * Get cached value by key.
   *
   * @param string $key
   * @return mixed|null
   */
  public static function get(string $key)
  {
    $pattern = self::$cacheDir . self::sanitizeKey($key) . '_*.json';
    $files = glob($pattern);
    if (empty($files)) {
      return null;
    }

    // Find the valid file with the latest expiration
    $validFile = null;
    $latestExpires = 0;
    foreach ($files as $file) {
      $filename = basename($file, '.json');
      $parts = explode('_', $filename);
      $expires = (int) end($parts);
      if ($expires > time() && $expires > $latestExpires) {
        $latestExpires = $expires;
        $validFile = $file;
      }
    }

    if ($validFile === null) {
      return null;
    }

    $data = json_decode(file_get_contents($validFile), true);
    if ($data === null || !isset($data['value'])) {
      unlink($validFile);
      return null;
    }

    return $data['value'];
  }

  /**
   * Set cached value with timeout.
   *
   * @param string $key
   * @param mixed $value
   * @param int $timeoutSeconds
   * @return void
   */
  public static function set(string $key, $value, int $timeoutSeconds): void
  {
    $expires = time() + $timeoutSeconds;
    $sanitizedKey = self::sanitizeKey($key);

    // Delete any existing files for this key
    $pattern = self::$cacheDir . $sanitizedKey . '_*.json';
    $existingFiles = glob($pattern);
    foreach ($existingFiles as $file) {
      unlink($file);
    }

    $filePath = self::$cacheDir . $sanitizedKey . '_' . $expires . '.json';
    $data = [
      'value' => $value,
    ];
    file_put_contents($filePath, json_encode($data));
  }

  /**
   * Clear cache by key.
   *
   * @param string $key
   * @return void
   */
  public static function clear(string $key): void
  {
    $pattern = self::$cacheDir . self::sanitizeKey($key) . '_*.json';
    $files = glob($pattern);
    foreach ($files as $file) {
      unlink($file);
    }
  }

  /**
   * Clear all cache files.
   *
   * @return void
   */
  public static function clearAll(): void
  {
    $files = glob(self::$cacheDir . '*.json');
    foreach ($files as $file) {
      unlink($file);
    }
  }

  /**
   * Garbage collect expired cache files.
   *
   * @return void
   */
  public static function gc(): void
  {
    $files = glob(self::$cacheDir . '*.json');
    $currentTime = time();
    foreach ($files as $file) {
      $filename = basename($file, '.json');
      $parts = explode('_', $filename);
      if (count($parts) < 2) {
        continue; // Invalid filename, skip
      }
      $expires = (int) end($parts);
      if ($expires < $currentTime) {
        unlink($file);
      }
    }
  }

  /**
   * Sanitize key for filename.
   *
   * @param string $key
   * @return string
   */
  private static function sanitizeKey(string $key): string
  {
    return preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
  }
}
