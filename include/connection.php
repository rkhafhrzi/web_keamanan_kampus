<?php

require_once __DIR__ . '/env.php';

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
        global $env;

        $host    = $env['DB_HOST'] ?? 'localhost';
        $db      = $env['DB_NAME'] ?? 'geosafe';
        $user    = $env['DB_USER'] ?? 'root';
        $pass    = $env['DB_PASS'] ?? 'RootMysql123!';
        $charset = $env['DB_CHARSET'] ?? 'utf8mb4';

        if (!$db || !$user) {
            die('Konfigurasi database di .env belum lengkap');
        }

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . $e->getMessage());
        }
    }

    /**
     * Ambil instance PDO (Singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            new self();
        }
        return self::$instance;
    }

    private function __clone() {}
    public function __wakeup() {}
}
