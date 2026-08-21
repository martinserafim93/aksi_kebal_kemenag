<?php
/**
 * AKSI KEBAL - Database Class
 * 
 * Wrapper PDO untuk koneksi database menggunakan Singleton Pattern.
 * Menyediakan prepared statement untuk mencegah SQL Injection.
 */

class Database
{
    private static $instance = null;
    private $pdo;
    private $stmt;

    /**
     * Constructor - membuat koneksi PDO
     */
    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please contact administrator.');
            }
        }
    }

    /**
     * Singleton - mendapatkan instance Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prepare SQL statement
     */
    public function query(string $sql): self
    {
        $this->stmt = $this->pdo->prepare($sql);
        return $this;
    }

    /**
     * Bind parameter ke statement
     */
    public function bind($param, $value, $type = null): self
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Execute prepared statement
     */
    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    /**
     * Fetch semua hasil sebagai array of associative arrays
     */
    public function fetchAll(): array
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Fetch satu baris hasil
     */
    public function fetch()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Hitung jumlah baris yang terpengaruh
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Mendapatkan ID terakhir yang di-insert
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Mendapatkan PDOStatement yang aktif
     */
    public function getStatement(): PDOStatement
    {
        return $this->stmt;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
