<?php
require_once __DIR__ . '/core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (!empty($_POST['idelete'])) {
    $clinic_id = Tenant::id();
    $stmt = $db->prepare('DELETE FROM pago WHERE id = ? AND clinic_id = ?');
    $stmt->bind_param('ii', $_POST['idelete'], $clinic_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: historial.php');
