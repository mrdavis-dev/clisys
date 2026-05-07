<?php

class Database {
    private static ?mysqli $instance = null;

    public static function get(): mysqli {
        if (self::$instance === null) {
            self::$instance = new mysqli(
                $_ENV['DB_SERVER'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
                $_ENV['DB_DATABASE']
            );
            if (self::$instance->connect_error) {
                error_log('DB connection failed: ' . self::$instance->connect_error);
                http_response_code(503);
                exit('Servicio temporalmente no disponible.');
            }
            self::$instance->set_charset('utf8mb4');
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}
