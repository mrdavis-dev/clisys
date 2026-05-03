<?php
require_once __DIR__ . '/core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (isset($_POST['save'])) {
    $stmt = $db->prepare(
        'INSERT INTO citas_tabla (fecha_de_cita, hora_de_cita, nombre_paciente, asunto_de_la_cita, doctor)
         VALUES (?, ?, ?, ?, ?)'
    );
    $fecha   = $_POST['fecha']   ?? '';
    $hora    = $_POST['hora']    ?? '';
    $nombre  = $_POST['nombre']  ?? '';
    $asunto  = $_POST['asunto']  ?? '';
    $doctor  = $_POST['doctor']  ?? '';
    $stmt->bind_param('sssss', $fecha, $hora, $nombre, $asunto, $doctor);
    $stmt->execute();
    $stmt->close();
    header('Location: inicio.php?guardado');
    exit;
}
