<?php
/**
 * Flash toast partial
 * Reads ?ok=<key> or ?err=<key> from the URL and renders a dismissible toast.
 * Include once per page, immediately after <body>.
 */
$flash_ok  = $_GET['ok']  ?? null;
$flash_err = $_GET['err'] ?? null;
$flash_msg  = '';
$flash_type = '';
$flash_icon = '';

if ($flash_ok !== null) {
    $flash_type = 'success';
    $flash_icon = 'check-circle';
    $flash_msg  = match ((string) $flash_ok) {
        'cita'     => 'Cita guardada correctamente.',
        'paciente' => 'Paciente registrado.',
        'pago'     => 'Pago registrado. Factura enviada.',
        'nota'     => 'Nota clínica guardada.',
        'usuario'  => 'Usuario creado correctamente.',
        default    => 'Operación completada.',
    };
} elseif ($flash_err !== null) {
    $flash_type = 'danger';
    $flash_icon = 'exclamation-triangle';
    $flash_msg  = match ((string) $flash_err) {
        'limit_patients' => 'Límite de pacientes alcanzado para este plan.',
        'csrf'           => 'Error de seguridad. Recarga la página e intenta de nuevo.',
        'delete'         => 'No se pudo eliminar el registro.',
        default          => 'Ocurrió un error. Inténtalo de nuevo.',
    };
}
?>
<?php if ($flash_msg): ?>
<div id="flash-toast" class="flash-toast flash-toast--<?= $flash_type ?>" role="alert" aria-live="polite" aria-atomic="true">
    <span class="fa fa-<?= $flash_icon ?> mr-2" aria-hidden="true"></span>
    <span class="flash-toast__msg"><?= htmlspecialchars($flash_msg, ENT_QUOTES, 'UTF-8') ?></span>
    <button class="flash-toast__close" aria-label="Cerrar notificación">&times;</button>
</div>
<?php endif; ?>
