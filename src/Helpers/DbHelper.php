<?php
declare(strict_types=1);

namespace App\Helpers;
use \PDO;

/**
 * Database Helper for MySQL operations using PDO.
 */
class DbHelper
{
    private static ?PDO $pdo = null;

    /**
     * Get PDO instance (singleton).
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'test';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASS') ?: '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            self::$pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    /**
     * Execute a raw query.
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     */
    public static function query(string $query, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Select multiple rows.
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public static function select(string $query, array $params = []): array
    {
        $stmt = self::query($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Select single row.
     *
     * @param string $query
     * @param array $params
     * @return array|null
     */
    public static function selectOne(string $query, array $params = []): ?array
    {
        $stmt = self::query($query, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Insert a record.
     *
     * @param string $table
     * @param array $data
     * @return int Last insert ID
     */
    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        self::query($query, array_values($data));

        return (int) self::getConnection()->lastInsertId();
    }

    /**
     * Update records.
     *
     * @param string $table
     * @param array $data
     * @param string $where
     * @param array $params
     * @return int Affected rows
     */
    public static function update(string $table, array $data, string $where, array $params = []): int
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $query = "UPDATE $table SET $set WHERE $where";

        $stmt = self::query($query, array_merge(array_values($data), $params));
        return $stmt->rowCount();
    }

    /**
     * Delete records.
     *
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int Affected rows
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $query = "DELETE FROM $table WHERE $where";
        $stmt = self::query($query, $params);
        return $stmt->rowCount();
    }
}