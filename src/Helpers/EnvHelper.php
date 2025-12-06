<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Simple .env file parser that loads environment variables.
 */
class EnvHelper
{
    /** @var bool */
    private static $loaded = false;

    /**
     * Load environment variables from .env file.
     *
     * @param string $filePath Path to .env file (default: .env in project root)
     * @return bool True if file was loaded successfully
     */
    public static function load(string $filePath = '.env'): bool
    {
        if (self::$loaded) {
            return true;
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return false;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and empty lines
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Parse KEY=VALUE
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            // Remove surrounding quotes if present
            if (strlen($value) >= 2) {
                $firstChar = $value[0];
                $lastChar = $value[strlen($value) - 1];
                if (($firstChar === '"' && $lastChar === '"') || 
                    ($firstChar === "'" && $lastChar === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Set environment variable if not already set
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Get environment variable with optional default.
     *
     * @param string $key Environment variable name
     * @param string|null $default Default value if not found
     * @return string|null
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }

    /**
     * Check if environment variable exists.
     *
     * @param string $key Environment variable name
     * @return bool
     */
    public static function has(string $key): bool
    {
        return getenv($key) !== false;
    }
}