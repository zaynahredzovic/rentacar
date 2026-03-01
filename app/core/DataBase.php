<?php

namespace App\Core;

use PDO;
use PDOException;

class DataBase{
    
    public static $conn;

    public function __construct(){
        try {
            $config = new dbConfig();
            $dsn = "{$config->driver}:host={$config->host};dbname={$config->database}";
            self::$conn = new PDO($dsn, $config->username, $config->password);
        } catch (PDOException $e) {
            die('Database connection error: ' . $e->getMessage());
        }
    }

}