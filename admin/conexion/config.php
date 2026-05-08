<?php
require_once __DIR__ . '/../core/env.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Tenant.php';
loadEnv(__DIR__ . '/../../.env');
$db = Database::get();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
Tenant::load($db);
