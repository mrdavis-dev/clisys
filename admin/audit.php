<?php
require_once __DIR__ . '/core/Auth.php';
Auth::require();
Auth::requireRole(['admin']);
require_once __DIR__ . '/conexion/config.php';
$pageTitle = 'Auditoría — ClíSys';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>
<body>
<?php include('partials/skip_nav.php'); ?>
<?php include('menu.php'); ?>

<div id="content" class="p-4 p-md-5 pt-5">
    <div class="animated fadeIn container centrar">
        <h1 class="page-title border-bottom pb-2">Registro de Auditoría</h1>
        <p class="text-muted">Últimas 500 acciones de esta clínica.</p>
    </div>

    <div class="container-fluid mt-3">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="filtro_accion" class="form-control border"
                       placeholder="Filtrar por acción…" autocomplete="off">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover" id="tbl_audit">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Fecha / Hora</th>
                        <th>Usuario ID</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>ID Afectado</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $clinic_id = Tenant::id();
                $stmt = $db->prepare(
                    'SELECT id, created_at, user_id, action, entity, entity_id, ip
                       FROM audit_log
                      WHERE clinic_id = ?
                      ORDER BY created_at DESC
                      LIMIT 500'
                );
                $stmt->bind_param('i', $clinic_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr class="fila-audit" data-accion="<?= h($row['action']) ?>">
                        <td><?= h((string)$row['id']) ?></td>
                        <td><?= h($row['created_at']) ?></td>
                        <td><?= h((string)$row['user_id']) ?></td>
                        <td><code><?= h($row['action']) ?></code></td>
                        <td><?= h($row['entity']) ?></td>
                        <td><?= h($row['entity_id']) ?></td>
                        <td><?= h($row['ip']) ?></td>
                    </tr>
                <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#filtro_accion').on('input', function () {
    var q = $(this).val().toLowerCase();
    $('.fila-audit').each(function () {
        var accion = $(this).data('accion').toLowerCase();
        $(this).toggle(q === '' || accion.includes(q));
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
