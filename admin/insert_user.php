<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Audit.php';
require_once __DIR__ . '/core/Plan.php';
Auth::require();
Auth::requireRole(['admin']);
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

$user = $_POST['usuario'] ?? '';
$name = $_POST['nombre']  ?? '';
$pass = $_POST['pass']    ?? '';

if ($user === '' || $name === '' || $pass === '') {
    header('Location: registro_user.php');
    exit;
}

// Enforce plan user limit
if (!Plan::withinLimit('users')) {
    header('Location: registro_user.php?limit=users');
    exit;
}

$role_input     = $_POST['role'] ?? 'admin';
$allowed_roles  = ['admin', 'recepcion', 'medico'];
$role           = in_array($role_input, $allowed_roles, true) ? $role_input : 'admin';

$clinic_id    = Tenant::id();
$hashPassword = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $db->prepare('INSERT INTO users (clinic_id, username, name, password, role) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('issss', $clinic_id, $user, $name, $hashPassword, $role);
$stmt->execute();
$new_id = (string)$db->insert_id;
$stmt->close();
Audit::log('insert_user', 'users', $new_id);

header('Location: registro_user.php?guardado');
