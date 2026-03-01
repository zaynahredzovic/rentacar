<?php

namespace App\Core;

final class dbConfig{
    public readonly string $driver;
    public readonly string $host;
    public readonly string $database;
    public readonly string $username;
    public readonly string $password;

    public function __construct(){
        $this->driver = $_ENV['DB_DRIVER'];
        $this->host = $_ENV['DB_HOST'];
        $this->database = $_ENV['DB_DATABASE'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

}