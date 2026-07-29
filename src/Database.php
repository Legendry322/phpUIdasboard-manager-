<?php
/**
 * Database connection wrapper. It will attempt to load config.php (gitignored) if present,
 * otherwise it will fall back to ../dbconnect.php if that file exists in repo (you mentioned dbconnect.php exists).
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Load config or legacy dbconnect
        if (file_exists(__DIR__ . '/../config.php')) {
            require_once __DIR__ . '/../config.php';
        } elseif (file_exists(__DIR__ . '/../dbconnect.php')) {
            require_once __DIR__ . '/../dbconnect.php';
        } else {
            throw new RuntimeException('No DB configuration found. Copy config.example.php to config.php and edit it.');
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
        ];

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        try {
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            return self::$instance;
        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
