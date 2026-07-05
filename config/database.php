<?php
/**
 * Database Configuration
 * FSSAI License Verification System
 *
 * PDO-based database connection with error handling
 */

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Prevent direct access
defined('APP_NAME') or define('APP_NAME', 'FSSAI_Verification');

class Database {
    private static $instance = null;
    private $conn;

    // Database Configuration - PostgreSQL (Neon) from environment variables
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $ssl_mode;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'ep-proud-grass-atmdaizt-pooler.c-9.us-east-1.aws.neon.tech';
        $this->port = $_ENV['DB_PORT'] ?? '5432';
        $this->db_name = $_ENV['DB_NAME'] ?? 'neondb';
        $this->username = $_ENV['DB_USER'] ?? 'neondb_owner';
        $this->password = $_ENV['DB_PASSWORD'] ?? 'npg_g98JOczXkEus';
        $this->ssl_mode = $_ENV['DB_SSL_MODE'] ?? 'require';
        $this->connect();
    }

    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode={$this->ssl_mode}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Log error but don't expose details to user
            error_log("Database Connection Error: " . $e->getMessage());
            die(json_encode([
                'success' => false,
                'error' => 'Database connection failed. Please contact administrator.'
            ]));
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
