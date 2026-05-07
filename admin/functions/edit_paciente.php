<?php
require_once __DIR__ . '/../core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/../conexion/config.php';

if (isset($_POST['update'])) {
    $nombre    = $_POST['nombre']    ?? '';
    $apellido  = $_POST['apellido']  ?? '';
    $cedula    = $_POST['cedula']    ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono  = $_POST['telefono']  ?? '';
    $email     = $_POST['email']     ?? '';
    $ocupacion = $_POST['ocupacion'] ?? '';
    $edad      = $_POST['edad']      ?? '';
    $idpost    = (int)($_POST['id']  ?? 0);
    $clinic_id = Tenant::id();

    $stmt = $db->prepare(
        'UPDATE pacientes
         SET nombre = ?, apellido = ?, cedula = ?, direccion = ?,
             telefono = ?, email = ?, ocupacion = ?, edad = ?
         WHERE id = ? AND clinic_id = ?'
    );
    $stmt->bind_param(
        'ssssssssii',
        $nombre, $apellido, $cedula, $direccion,
        $telefono, $email, $ocupacion, $edad, $idpost, $clinic_id
    );
    $stmt->execute();
    $stmt->close();
    header('Location: ../pacientes.php?guardado');
    exit;
}
