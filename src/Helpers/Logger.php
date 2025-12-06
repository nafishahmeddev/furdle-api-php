<?php
declare(strict_types=1);

namespace App\Helpers;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;

/**
 * Static Logger class using Monolog for application logging.
 */
class Logger
{
    private static ?MonologLogger $instance = null;
    private static array $loggers = [];

    /**
     * Get the default logger instance.
     *
     * @return MonologLogger
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = self::createLogger('app');
        }

        return self::$instance;
    }

    /**
     * Get or create a named logger.
     *
     * @param string $name
     * @return MonologLogger
     */
    public static function getLogger(string $name = 'app'): MonologLogger
    {
        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = self::createLogger($name);
        }

        return self::$loggers[$name];
    }

    /**
     * Create a new logger instance.
     *
     * @param string $name
     * @return MonologLogger
     */
    private static function createLogger(string $name): MonologLogger
    {
        $logger = new MonologLogger($name);

        // Ensure logs directory exists
        $logDir = 'logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Add rotating file handler
        $fileHandler = new RotatingFileHandler(
            $logDir . '/' . $name . '.log',
            0, // Keep all files
            Level::Debug
        );

        // Custom line formatter
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true, // Allow inline line breaks
            true  // Ignore empty context
        );
        $fileHandler->setFormatter($formatter);

        $logger->pushHandler($fileHandler);

        // Add console handler for development
        if (php_sapi_name() === 'cli') {
            $consoleHandler = new StreamHandler('php://stdout', Level::Info);
            $consoleHandler->setFormatter($formatter);
            $logger->pushHandler($consoleHandler);
        }

        return $logger;
    }

    /**
     * Log debug message.
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     */
    public static function debug(string $message, array $context = [], string $channel = 'app'): void
    {
        self::getLogger($channel)->debug($message, $context);
    }

    /**
     * Log info message.
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     */
    public static function info(string $message, array $context = [], string $channel = 'app'): void
    {
        self::getLogger($channel)->info($message, $context);
    }

    /**
     * Log warning message.
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     */
    public static function warning(string $message, array $context = [], string $channel = 'app'): void
    {
        self::getLogger($channel)->warning($message, $context);
    }

    /**
     * Log error message.
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     */
    public static function error(string $message, array $context = [], string $channel = 'app'): void
    {
        self::getLogger($channel)->error($message, $context);
    }

    /**
     * Log critical message.
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     */
    public static function critical(string $message, array $context = [], string $channel = 'app'): void
    {
        self::getLogger($channel)->critical($message, $context);
    }

    /**
     * Log HTTP request.
     *
     * @param string $method
     * @param string $path
     * @param array $headers
     * @param array $params
     */
    public static function request(string $method, string $path, array $headers = [], array $params = []): void
    {
        self::info("HTTP Request: {$method} {$path}", [
            'headers' => $headers,
            'params' => $params
        ], 'requests');
    }

    /**
     * Log webhook event.
     *
     * @param string $event
     * @param array $data
     */
    public static function webhook(string $event, array $data = []): void
    {
        self::info("Webhook Event: {$event}", $data, 'webhooks');
    }

    /**
     * Log authentication event.
     *
     * @param string $action
     * @param string $user
     * @param array $context
     */
    public static function auth(string $action, string $user, array $context = []): void
    {
        self::info("Auth {$action}: {$user}", $context, 'auth');
    }

    /**
     * Log database operation.
     *
     * @param string $operation
     * @param string $table
     * @param array $context
     */
    public static function database(string $operation, string $table, array $context = []): void
    {
        self::debug("DB {$operation}: {$table}", $context, 'database');
    }
}