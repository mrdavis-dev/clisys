<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Audit.php';
require_once __DIR__ . '/../conexion/config.php';
Auth::require();
Auth::requireRole(['admin', 'recepcion']);
Csrf::verify();

$pago_id = (int)($_POST['pago_id'] ?? 0);
$fecha   = trim($_POST['fecha']        ?? '');
$monto   = (float)($_POST['monto']    ?? 0);
$tipo    = trim($_POST['tipo_de_pago'] ?? '');
$nota    = trim($_POST['nota']         ?? '');

$allowed_tipos = ['Efectivo', 'Tarjeta', 'Cheque', 'Transferencia'];
if ($pago_id <= 0 || $fecha === '' || $monto < 0 || !in_array($tipo, $allowed_tipos, true)) {
    header('Location: ../historial.php?err=invalid');
    exit;
}

$clinic_id = Tenant::id();

// Verify ownership and fetch cedula + tratamiento
$chk = $db->prepare('SELECT cedula, tratamiento FROM pago WHERE id = ? AND clinic_id = ?');
$chk->bind_param('ii', $pago_id, $clinic_id);
$chk->execute();
$row = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$row) {
    http_response_code(403);
    exit('No autorizado.');
}

$cedula      = $row['cedula'];
$tratamiento = $row['tratamiento'];

// Fetch full chain BEFORE updating monto to preserve the saldo anchor
$chain_stmt = $db->prepare(
    'SELECT id, monto, saldo FROM pago
     WHERE clinic_id = ? AND cedula = ? AND tratamiento = ?
     ORDER BY id ASC'
);
$chain_stmt->bind_param('iss', $clinic_id, $cedula, $tratamiento);
$chain_stmt->execute();
$chain = $chain_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$chain_stmt->close();

// anchor = initial debt before the first payment in this treatment chain
$first   = $chain[0];
$anchor  = (float)$first['saldo'] + (float)$first['monto'];

// Update the edited record
$upd = $db->prepare(
    'UPDATE pago SET fecha = ?, monto = ?, tipo_de_pago = ?, nota = ? WHERE id = ? AND clinic_id = ?'
);
$upd->bind_param('sdssii', $fecha, $monto, $tipo, $nota, $pago_id, $clinic_id);
$upd->execute();
$upd->close();

// Recompute saldo chain
$running     = $anchor;
$upd_saldo   = $db->prepare('UPDATE pago SET saldo = ? WHERE id = ? AND clinic_id = ?');
foreach ($chain as $r) {
    $row_monto = ((int)$r['id'] === $pago_id) ? $monto : (float)$r['monto'];
    $running   = $running - $row_monto;
    $row_id    = (int)$r['id'];
    $upd_saldo->bind_param('dii', $running, $row_id, $clinic_id);
    $upd_saldo->execute();
}
$upd_saldo->close();

Audit::log('edit_pago', 'pago', (string)$pago_id);
header('Location: ../historial.php?ok=pago_editado');
exit;
