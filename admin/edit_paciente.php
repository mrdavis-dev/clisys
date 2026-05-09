<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Paciente</title>
</head>

<body>
    <?php include('partials/skip_nav.php'); ?>
    <?php include('menu.php'); /* loads $db and Tenant */ ?>

    <?php
    $id        = (int)($_GET['id'] ?? 0);
    $clinic_id = Tenant::id();

    $stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ? AND clinic_id = ?');
    $stmt->bind_param('ii', $id, $clinic_id);
    $stmt->execute();
    $paciente = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $pagos = [];
    $notas = [];

    if ($paciente) {
        $cedula = $paciente['cedula'];

        $stmt2 = $db->prepare(
            'SELECT fecha, monto, tipo_de_pago, saldo, tratamiento, nota
               FROM pago WHERE cedula = ? AND clinic_id = ? ORDER BY fecha DESC'
        );
        $stmt2->bind_param('si', $cedula, $clinic_id);
        $stmt2->execute();
        $pagos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();

        $stmt3 = $db->prepare(
            'SELECT n.fecha, n.contenido, n.created_at,
                    COALESCE(u.name, u.username, "—") AS autor
               FROM clinic_notes n
               LEFT JOIN users u ON u.id = n.created_by AND u.clinic_id = n.clinic_id
              WHERE n.cedula = ? AND n.clinic_id = ?
              ORDER BY n.fecha DESC, n.created_at DESC'
        );
        $stmt3->bind_param('si', $cedula, $clinic_id);
        $stmt3->execute();
        $notas = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt3->close();
    }
    ?>

    <div id="content" class="p-4 p-md-5 pt-5">
        <?php
        $breadcrumb = [
            ['label' => 'Inicio',    'url' => 'inicio.php'],
            ['label' => 'Pacientes', 'url' => 'pacientes.php'],
            ['label' => 'Detalle de paciente'],
        ];
        include 'partials/breadcrumb.php';
        ?>

        <?php if (!$paciente): ?>
            <div class="container centrar">
                <p class="text-muted">Paciente no encontrado.</p>
                <a href="pacientes.php" class="btn btn-secondary">Volver</a>
            </div>
        <?php else: ?>

        <div class="animated fadeIn container centrar">
            <h1 class="page-title border-bottom pb-2">
                <?= h($paciente['nombre']) ?> <?= h($paciente['apellido']) ?>
                <small class="text-muted" style="font-size: 0.5em;">Cédula: <?= h($paciente['cedula']) ?></small>
            </h1>

            <ul class="nav nav-tabs mt-4" id="pacienteTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-datos">
                        <i class="fa fa-user mr-1"></i> Datos personales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-pagos">
                        <i class="fa fa-money mr-1"></i> Historial de pagos
                        <span class="badge badge-secondary"><?= count($pagos) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-notas">
                        <i class="fa fa-file-text-o mr-1"></i> Notas clínicas
                        <span class="badge badge-secondary"><?= count($notas) ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-4" id="pacienteTabsContent">

                <!-- Tab: Datos personales -->
                <div class="tab-pane fade show active text-left" id="tab-datos" role="tabpanel">
                    <form method="post" action="functions/edit_paciente.php">
                        <?= Csrf::field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <label>Nombre</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="nombre" value="<?= h($paciente['nombre']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Apellido</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="apellido" value="<?= h($paciente['apellido']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Cédula</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="cedula" value="<?= h($paciente['cedula']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Edad</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="edad" value="<?= h($paciente['edad']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Teléfono</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="telefono" value="<?= h($paciente['telefono']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="email" value="<?= h($paciente['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Ocupación</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="ocupacion" value="<?= h($paciente['ocupacion']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label>Dirección</label>
                                <input type="text" class="mb-2 form-control border border-primary" name="direccion" value="<?= h($paciente['direccion']) ?>">
                            </div>
                        </div>

                        <input type="hidden" name="id" value="<?= h((string)$paciente['id']) ?>">

                        <div class="text-center mt-4 mb-4">
                            <input type="submit" name="update" class="btn btn-success px-5" value="Actualizar">
                        </div>
                    </form>
                </div>

                <!-- Tab: Historial de pagos -->
                <div class="tab-pane fade" id="tab-pagos" role="tabpanel">
                    <?php if (empty($pagos)): ?>
                        <p class="text-muted text-center mt-4">
                            <i class="fa fa-folder-open-o fa-2x d-block mb-2"></i>
                            No hay registros de pago para este paciente.
                        </p>
                    <?php else: ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Tipo de pago</th>
                                        <th>Saldo</th>
                                        <th>Tratamiento</th>
                                        <th>Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pagos as $p): ?>
                                    <tr>
                                        <td><?= h($p['fecha']) ?></td>
                                        <td>B/. <?= h((string)$p['monto']) ?></td>
                                        <td><?= h($p['tipo_de_pago']) ?></td>
                                        <td>B/. <?= h((string)$p['saldo']) ?></td>
                                        <td><?= h($p['tratamiento']) ?></td>
                                        <td><?= h($p['nota']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab: Notas clínicas -->
                <div class="tab-pane fade" id="tab-notas" role="tabpanel">
                    <?php if (empty($notas)): ?>
                        <p class="text-muted text-center mt-4">
                            <i class="fa fa-file-o fa-2x d-block mb-2"></i>
                            No hay notas clínicas para este paciente.
                        </p>
                    <?php else: ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Nota</th>
                                        <th>Autor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($notas as $n): ?>
                                    <tr>
                                        <td class="text-nowrap"><?= h($n['fecha']) ?></td>
                                        <td style="white-space: pre-wrap;"><?= h($n['contenido']) ?></td>
                                        <td class="text-nowrap"><?= h($n['autor']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.tab-content -->
        </div><!-- /.container -->

        <?php endif; ?>
    </div><!-- #content -->

    <script src="js/main.js"></script>
</body>
</html>
