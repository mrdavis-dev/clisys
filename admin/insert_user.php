<?php
require_once __DIR__ . '/core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

$user = $_POST['usuario'] ?? '';
$name = $_POST['nombre']  ?? '';
$pass = $_POST['pass']    ?? '';

if ($user === '' || $name === '' || $pass === '') {
    header('Location: registro_user.php');
    exit;
}

$clinic_id    = Tenant::id();
$hashPassword = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $db->prepare('INSERT INTO users (clinic_id, username, name, password) VALUES (?, ?, ?, ?)');
$stmt->bind_param('isss', $clinic_id, $user, $name, $hashPassword);
$stmt->execute();
$stmt->close();

header('Location: registro_user.php?guardado');
