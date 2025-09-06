<?php
declare(strict_types=1);

// load DB constants from config.php
require_once __DIR__ . '/config.php';

final class DB {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO(
                    JK_DB_DSN,
                    JK_DB_USER,
                    JK_DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                // Fail fast with clear message (you can remove in production)
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
