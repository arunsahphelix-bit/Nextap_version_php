<?php
require_once __DIR__ . '/../config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $database_url = getenv('DATABASE_URL');
            
            if ($database_url) {
                $db = parse_url($database_url);
                $host = $db['host'] ?? 'localhost';
                $port = $db['port'] ?? 5432;
                $dbname = ltrim($db['path'], '/');
                $user = $db['user'] ?? '';
                $pass = $db['pass'] ?? '';
                
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                if (strpos($database_url, 'sslmode=require') !== false) {
                    $dsn .= ";sslmode=require";
                }
                
                $this->conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } else {
                $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
                $this->conn = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        // Convert MySQLi-style ? placeholders to PDO $1, $2, etc for PostgreSQL
        return $this->conn->prepare($sql);
    }

    public function escape($value) {
        return $this->conn->quote($value);
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}

$db = Database::getInstance()->getConnection();
require_once __DIR__ . '/db-helper.php';
$dbHelper = new DBHelper($db);
?>
