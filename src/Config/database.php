<?php
/**
 * Procurement ERP - Database Configuration
 */

namespace App\Config;

class Database {
    public static $host;
    public static $dbName;
    public static $username;
    public static $password;
    
    public static function init() {
        self::$host = $_ENV['DB_HOST'] ?? 'localhost';
        self::$dbName = $_ENV['DB_NAME'] ?? 'procurement_erp';
        self::$username = $_ENV['DB_USER'] ?? 'root';
        self::$password = $_ENV['DB_PASSWORD'] ?? '';
    }
}

// Initialize database configuration
Database::init();