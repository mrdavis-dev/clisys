<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>Registrado</title>
</head>
<body class="container">
<?php
require_once __DIR__ . '/../core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/../conexion/config.php';

if (!isset($_POST['submit'])) {
    header('Location: ../registro.php');
    exit;
}

$username = trim($_POST['user'] ?? '');
$password = $_POST['password'] ?? '';
$nombre   = trim($_POST['nombre'] ?? '');

if ($username === '' || $password === '' || $nombre === '') {
    echo '<p class="text-danger">Todos los campos son requeridos.</p>';
    echo '<button class="btn btn-danger" onclick="window.history.back()">Regresar</button>';
    exit;
}

// Verificar duplicado con prepared statement (por clínica)
$clinic_id = Tenant::id();
$check = $db->prepare('SELECT id FROM users WHERE clinic_id = ? AND username = ?');
$check->bind_param('is', $clinic_id, $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    echo '<h2>Este usuario ya está registrado</h2>';
    echo '<button class="btn btn-danger" onclick="window.history.back()">Regresar</button>';
    exit;
}
$check->close();

$hashPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
// $clinic_id already set above for duplicate check
$stmt = $db->prepare('INSERT INTO users (clinic_id, username, password, name) VALUES (?,?,?,?)');
$stmt->bind_param('isss', $clinic_id, $username, $hashPassword, $nombre);

if ($stmt->execute()) {
    $stmt->close();
    echo '<script>alert("Registrado..."); window.location.href = "../index.php";</script>';
} else {
    error_log('Error al registrar usuario: ' . $db->error);
    echo '<p class="text-danger">Error al registrar. Intenta de nuevo.</p>';
    echo '<button class="btn btn-danger" onclick="window.history.back()">Regresar</button>';
}
?>
</body>
</html>
