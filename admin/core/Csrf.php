<?php

class Csrf {
    private const TOKEN_KEY = 'csrf_token';

    public static function generate(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function verify(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST['csrf_token'] ?? '';
        $stored = $_SESSION[self::TOKEN_KEY] ?? '';
        if ($stored === '' || !hash_equals($stored, $token)) {
            http_response_code(403);
            exit('Solicitud inválida.');
        }
    }

    public static function field(): string {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::generate(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
