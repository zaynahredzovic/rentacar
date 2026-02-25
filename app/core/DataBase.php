<?php

namespace App\Core;

use PDO;
use PDOException;

class Database{
    
    private static $conn = null;

    public function __construct(){
        try {
            if (self::$conn === null) {
                $config = new dbConfig();
                
                // Debug: Check config values
                error_log("Connecting with:");
                error_log("Driver: " . $config->driver);
                error_log("Host: " . $config->host);
                error_log("Database: " . $config->database);
                
                $dsn = "{$config->driver}:host={$config->host};dbname={$config->database}";
                
                // Create PDO connection
                self::$conn = new PDO(
                    $dsn, 
                    $config->username, 
                    $config->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                error_log("✅ PDO Database connected successfully");
            }
        } catch (PDOException $e) {
            error_log("PDO Database connection error: " . $e->getMessage());
            error_log("DSN: " . ($dsn ?? 'not set'));
            die('Database connection error: ' . $e->getMessage());
        }
    }

    public static function getConnection() {
        if (self::$conn === null) {
            new self();
        }
        return self::$conn;
    }
}