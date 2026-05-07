<?php
require_once __DIR__ . '/../core/env.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Tenant.php';
loadEnv(__DIR__ . '/../../.env');
$db = Database::get();
// Tenant::load() requires an active session; pages that call Auth::require() first
// will have a session already. Pages that don't (e.g. public inserts) start their own.
if (session_status() === PHP_SESSION_ACTIVE) {
    Tenant::load($db);
}
