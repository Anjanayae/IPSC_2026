<?php
/**
 * Database Connection Class
 * IPSC 2026 Registration System
 */

require_once 'config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please contact the administrator.");
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
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO " . $table . " (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($values);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            error_log("Insert error: " . $e->getMessage());
            throw new Exception("Failed to insert record");
        }
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach (array_keys($data) as $field) {
            $setParts[] = "$field = ?";
        }
        
        $sql = "UPDATE " . $table . " SET " . implode(', ', $setParts) . " WHERE " . $where;
        
        try {
            $stmt = $this->connection->prepare($sql);
            $params = array_merge(array_values($data), $whereParams);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            throw new Exception("Failed to update record");
        }
    }
    
    public function select($table, $conditions = [], $fields = '*') {
        $sql = "SELECT $fields FROM $table";
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach (array_keys($conditions) as $field) {
                $whereParts[] = "$field = ?";
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($conditions));
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Select error: " . $e->getMessage());
            throw new Exception("Failed to retrieve records");
        }
    }
    
    public function logActivity($action, $tableName = null, $recordId = null, $userId = null, $details = null) {
        $data = [
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'details' => $details
        ];
        
        try {
           // $this->insert('activity_log', $data);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>