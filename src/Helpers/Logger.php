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

        // Add rotating file handler for combined log
        $fileHandler = new RotatingFileHandler(
            $logDir . '/app.log', // All logs go to app.log
            0, // Keep all files
            Level::Debug
        );

        // Custom line formatter
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message%\n%context%\n%extra%\n",
            'Y-m-d H:i:s',
            true, // Allow inline line breaks
            false  // Don't ignore empty context to show pretty formatting
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
}