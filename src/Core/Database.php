<?php
/**
 * Procurement ERP - Database Class
 * 
 * Provides database connection and query functionality
 */

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . \App\Config\Database::$host . ";dbname=" . \App\Config\Database::$dbName . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, \App\Config\Database::$username, \App\Config\Database::$password, $options);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Initialize the database connection (singleton)
     */
    public static function initialize() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get the database connection
     */
    public static function getConnection() {
        if (self::$instance === null) {
            self::initialize();
        }
        return self::$instance->connection;
    }
    
    /**
     * Execute a query with parameters
     */
    public static function query($sql, $params = []) {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch a single row
     */
    public static function fetch($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch all rows
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert a record and return the last insert ID
     */
    public static function insert($table, $data) {
        $db = self::getConnection();
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $db->lastInsertId();
    }
    
    /**
     * Update a record
     */
    public static function update($table, $data, $where, $whereParams = []) {
        $db = self::getConnection();
        
        $setParts = [];
        foreach (array_keys($data) as $field) {
            $setParts[] = "{$field} = :{$field}";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";
        
        $stmt = $db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    /**
     * Delete a record
     */
    public static function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Begin a transaction
     */
    public static function beginTransaction() {
        return self::getConnection()->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public static function commit() {
        return self::getConnection()->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public static function rollback() {
        return self::getConnection()->rollBack();
    }
}