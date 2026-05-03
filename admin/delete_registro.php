<?php
require_once __DIR__ . '/core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (!empty($_POST['idelete'])) {
    $stmt = $db->prepare('DELETE FROM pago WHERE id = ?');
    $stmt->bind_param('i', $_POST['idelete']);
    $stmt->execute();
    $stmt->close();
}

header('Location: historial.php');
