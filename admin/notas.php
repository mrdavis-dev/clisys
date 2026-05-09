<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Module.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
require_once __DIR__ . '/conexion/config.php';
Module::require('clinical_notes');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notas Clínicas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('partials/flash.php'); ?>
<?php include('partials/skip_nav.php'); ?>
<?php include('menu.php'); ?>

<div id="content" class="p-4 p-md-5 pt-5">
    <div class="animated fadeIn container centrar">
        <h1 class="page-title border-bottom pb-2">Notas Clínicas</h1>
    </div>

    <!-- Search + New note -->
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="search_cedula" class="form-control border"
                       placeholder="Buscar por cédula del paciente" autocomplete="off">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" data-toggle="modal" data-target="#modalNota">
                    <i class="fa fa-plus mr-1"></i> Nueva nota
                </button>
            </div>
        </div>

        <!-- Notes table -->
        <div id="notas_result">
            <?php
            $clinic_id = Tenant::id();
            $stmt = $db->prepare(
                'SELECT n.id, n.cedula, n.fecha, n.contenido, n.created_at,
                        COALESCE(u.name, u.username, "—") AS autor
                   FROM clinic_notes n
                   LEFT JOIN users u ON u.id = n.created_by AND u.clinic_id = n.clinic_id
                  WHERE n.clinic_id = ?
                  ORDER BY n.fecha DESC, n.created_at DESC
                  LIMIT 100'
            );
            $stmt->bind_param('i', $clinic_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows   = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            ?>
            <table class="table table-hover table-striped" id="tbl_notas">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Cédula</th>
                        <th>Resumen</th>
                        <th>Autor</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $n): ?>
                    <tr class="fila-nota" data-cedula="<?= h($n['cedula']) ?>">
                        <td><?= h($n['fecha']) ?></td>
                        <td><?= h($n['cedula']) ?></td>
                        <td><?= h(mb_strimwidth($n['contenido'], 0, 80, '…')) ?></td>
                        <td><?= h($n['autor']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-ver"
                                    data-id="<?= h((string)$n['id']) ?>"
                                    data-cedula="<?= h($n['cedula']) ?>"
                                    data-fecha="<?= h($n['fecha']) ?>"
                                    data-contenido="<?= h($n['contenido']) ?>"
                                    data-toggle="modal" data-target="#modalVerNota">
                                Ver
                            </button>
                            <form method="post" action="delete_nota.php" class="d-inline">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="nota_id" value="<?= h((string)$n['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-confirm="true" aria-label="Borrar nota">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No hay notas clínicas aún.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: nueva nota -->
<div class="modal fade" id="modalNota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Nota Clínica</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="insert_nota.php">
                <?= Csrf::field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cédula del paciente</label>
                        <input type="text" class="form-control border border-primary"
                               name="cedula" required placeholder="Número de cédula">
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control border border-primary"
                               name="fecha" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Nota / Historia clínica</label>
                        <textarea class="form-control border border-primary"
                                  name="contenido" rows="8" required
                                  placeholder="Descripción del motivo de consulta, diagnóstico, tratamiento indicado…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: ver nota completa -->
<div class="modal fade" id="modalVerNota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nota clínica — <span id="modal_cedula"></span></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-1"><strong>Fecha:</strong> <span id="modal_fecha"></span></p>
                <hr>
                <p id="modal_contenido" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Live search by cedula
$('#search_cedula').on('input', function () {
    var q = $(this).val().toLowerCase();
    $('.fila-nota').each(function () {
        var cedula = $(this).data('cedula').toLowerCase();
        $(this).toggle(q === '' || cedula.includes(q));
    });
});

// Populate "ver nota" modal
$(document).on('click', '.btn-ver', function () {
    $('#modal_cedula').text($(this).data('cedula'));
    $('#modal_fecha').text($(this).data('fecha'));
    $('#modal_contenido').text($(this).data('contenido'));
});
</script>

<script src="js/main.js"></script>
</body>
</html>
