<?php
/**
 * Database Configuration
 * Handles database connection using PDO
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Database configuration
    private $driver;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $charset = 'utf8mb4';
    private $sslmode;
    
    private function __construct() {
        // Check if running in Docker environment
        if (getenv('DB_HOST')) {
            $this->driver = getenv('DB_DRIVER') ?: 'mysql';
            $this->host = getenv('DB_HOST');
            $this->port = getenv('DB_PORT') ?: ($this->driver === 'pgsql' ? '5432' : '3306');
            $this->dbname = getenv('DB_NAME');
            $this->username = getenv('DB_USER');
            $this->password = getenv('DB_PASSWORD');
            $this->sslmode = getenv('DB_SSLMODE') ?: null;
        } else {
            // Local XAMPP configuration
            $this->driver = 'mysql';
            $this->host = 'localhost';
            $this->port = '3306';
            $this->dbname = 'online_bookstore';
            $this->username = 'root';
            $this->password = '';
            $this->sslmode = null;
        }
        
        $this->connect();
    }
    
    private function connect() {
        try {
            if ($this->driver === 'pgsql') {
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
                if ($this->sslmode) {
                    $dsn .= ";sslmode={$this->sslmode}";
                }
            } else {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            }

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
