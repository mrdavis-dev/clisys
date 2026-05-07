<?php
session_start();
require_once __DIR__ . '/admin/core/env.php';
require_once __DIR__ . '/admin/core/Csrf.php';
require_once __DIR__ . '/admin/core/Database.php';
require_once __DIR__ . '/admin/core/Tenant.php';
loadEnv(__DIR__ . '/.env');
Csrf::verify();

$db = Database::get();
Tenant::load($db);   // resolves from subdomain (or falls back to clinic 1)
$clinic_id = Tenant::id();

$fecha  = $_POST['fecha']   ?? '';
$hora   = $_POST['hora']    ?? '';
$nombre = $_POST['nombre']  ?? '';
$asunto = $_POST['asunto']  ?? '';
$doctor = $_POST['doctor']  ?? '';

$stmt = $db->prepare(
    'INSERT INTO citas_tabla (clinic_id, fecha_de_cita, hora_de_cita, nombre_paciente, asunto_de_la_cita, doctor)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param('isssss', $clinic_id, $fecha, $hora, $nombre, $asunto, $doctor);
$stmt->execute();
$stmt->close();

header('Location: index.php?guardado');
