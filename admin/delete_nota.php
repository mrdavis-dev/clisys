<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Module.php';
require_once __DIR__ . '/core/Audit.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
Csrf::verify();
Module::require('clinical_notes');

require_once __DIR__ . '/conexion/config.php';

$nota_id   = (int)($_POST['nota_id'] ?? 0);
$clinic_id = Tenant::id();

if ($nota_id > 0) {
    $stmt = $db->prepare('DELETE FROM clinic_notes WHERE id = ? AND clinic_id = ?');
    $stmt->bind_param('ii', $nota_id, $clinic_id);
    $stmt->execute();
    $stmt->close();
    Audit::log('delete_note', 'clinic_notes', (string)$nota_id);
}

header('Location: notas.php');
exit;
