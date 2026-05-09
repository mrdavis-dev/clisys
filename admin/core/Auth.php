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

    public static function requireSuperAdmin(): void
    {
        self::require();
        if (($_SESSION['role'] ?? '') !== 'superadmin') {
            http_response_code(403);
            include __DIR__ . '/../partials/403.php';
            exit;
        }
    }

    public static function isSuperAdmin(): bool
    {
        return ($_SESSION['role'] ?? '') === 'superadmin';
    }

    public static function hasRole(array $roles): bool
    {
        $role = $_SESSION['role'] ?? '';
        if ($role === 'superadmin') { return true; }
        return in_array($role, $roles, true);
    }

    public static function requireRole(array $roles): void
    {
        if (!self::hasRole($roles)) {
            http_response_code(403);
            include __DIR__ . '/../partials/403.php';
            exit;
        }
    }
}

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
