<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Audit.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (!empty($_POST['idelete'])) {
    $clinic_id  = Tenant::id();
    $del_id     = (int)$_POST['idelete'];
    $stmt = $db->prepare('DELETE FROM pacientes WHERE id = ? AND clinic_id = ?');
    $stmt->bind_param('ii', $del_id, $clinic_id);
    $stmt->execute();
    $stmt->close();
    Audit::log('delete_patient', 'pacientes', (string)$del_id);
}
