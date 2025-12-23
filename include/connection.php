<?php

class Database
{
    private static ?PDO $instance = null;

    private string $host;
    private string $db;
    private string $user;
    private string $pass;
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $this->host = 'localhost';
        $this->db   = 'geosafe';
        $this->user = 'root';
        $this->pass = '';

        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$instance = new PDO($dsn, $this->user, $this->pass, $options);
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

    // Cegah clone & unserialize
    private function __clone() {}
    public function __wakeup() {}
}
