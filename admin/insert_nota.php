<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Module.php';
require_once __DIR__ . '/core/Audit.php';
Auth::require();
Csrf::verify();
Module::require('clinical_notes');

require_once __DIR__ . '/conexion/config.php';

$cedula    = trim($_POST['cedula']    ?? '');
$fecha     = trim($_POST['fecha']     ?? '');
$contenido = trim($_POST['contenido'] ?? '');

if ($cedula === '' || $fecha === '' || $contenido === '') {
    header('Location: notas.php?err=empty_note');
    exit;
}

$clinic_id  = Tenant::id();
$created_by = (int)($_SESSION['id'] ?? 0);

$stmt = $db->prepare(
    'INSERT INTO clinic_notes (clinic_id, cedula, fecha, contenido, created_by)
     VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param('isssi', $clinic_id, $cedula, $fecha, $contenido, $created_by);
$stmt->execute();
$new_id = (string)$db->insert_id;
$stmt->close();

Audit::log('insert_note', 'clinic_notes', $new_id);

header('Location: notas.php?ok=nota');
exit;
