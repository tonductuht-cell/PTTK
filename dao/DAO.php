<?php
class DAO {
    protected static ?PDO $connection = null;

    protected string $host = 'localhost';
    protected string $user = 'root';
    protected string $pass = '';
    protected string $dbName = 'shopman';

    public function __construct() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4",
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
    }

    public function getConnection(): PDO {
        return self::$connection;
    }
}
