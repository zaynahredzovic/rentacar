<?php

namespace App\Core;

final class dbConfig{
    public readonly string $driver;
    public readonly string $host;
    public readonly string $database;
    public readonly string $username;
    public readonly string $password;

    public function __construct(){
        // Load env if not already loaded
        Env::load();
        
        // Get values from environment
        $this->driver = Env::get('DB_DRIVER', 'mysql');
        $this->host = Env::get('DB_HOST', 'localhost');
        $this->database = Env::get('DB_NAME', 'rentacar');
        $this->username = Env::get('DB_USER', 'root');
        $this->password = Env::get('DB_PASSWORD', '');
        
        // Debug logging
        error_log("dbConfig loaded - Database: " . $this->database);
    }
}