<?php
class Database {
    private static $pdo;
    public static function connect() {
        if (!self::$pdo) {
            $c = (require __DIR__ . '/../../config/config.php')['db'];
            $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$pdo;
    }
}
