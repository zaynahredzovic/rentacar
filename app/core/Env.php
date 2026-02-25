<?php
namespace App\Core;

class Env{
    private static $loaded = false;

    public static function load($path = null){
        if(self::$loaded){
            return true;
        }

        if ($path === null) {
            $path = __DIR__ . '/../../.env';
        }

        if (!file_exists($path)) {
            die('.env file not found at: ' . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if(strpos(trim($line), '#') === 0){
                continue;
            }

            if(strpos($line, '=') !== false){
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                $value = trim($value, '"\'');

                putenv("$key=$value");
                $_ENV[$key]=$value;
                $_SERVER[$key]=$value;
            }
        }

        self::$loaded = true;
        return true;
    }

    public static function get($key, $default = null) {
        $value = getenv($key);
        if($value !== false){
            return $value;
        }

        if(isset($_ENV[$key])){
            return $_ENV[$key];
        }

        if(isset($_SERVER[$default])){
            return $_ENV[$default];
        }

        return $default;
    }

}