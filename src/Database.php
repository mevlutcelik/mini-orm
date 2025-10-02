<?php

namespace MiniOrm;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    self::$config['host'] ?? 'localhost',
                    self::$config['port'] ?? 3306,
                    self::$config['database'] ?? 'testdb'
                );

                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'] ?? 'root',
                    self::$config['password'] ?? 'root',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function resetConnection(): void
    {
        self::$instance = null;
    }
}