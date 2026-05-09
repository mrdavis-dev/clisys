<?php
require_once __DIR__ . '/core/Auth.php';
Auth::require();
Auth::requireRole(['admin']);
require_once __DIR__ . '/conexion/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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

<script src="js/main.js"></script>
</body>
</html>
