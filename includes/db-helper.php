<?php
// PDO Helper Functions for MySQLi-style code compatibility

class DBHelper {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Execute query with parameters (MySQLi-style compatibility)
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get single row
    public function fetchOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }
    
    // Get all rows
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    
    // Get row count
    public function rowCount($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }
    
    // Get last insert ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>
