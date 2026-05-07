<?php

require_once __DIR__ . '/Csrf.php';

class Auth {
    private const TIMEOUT = 1800; // 30 minutes

    public static function require(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['last_activity'])
            && (time() - $_SESSION['last_activity']) > self::TIMEOUT) {
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit;
        }
        if (!isset($_SESSION['loggedin'])) {
            header('Location: login.php');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
}

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
