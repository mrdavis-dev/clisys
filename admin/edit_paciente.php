<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
$pageTitle = 'Editar Paciente — ClíSys';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body>
    <?php include('partials/skip_nav.php'); ?>
    <?php include('menu.php'); /* loads $db and Tenant */ ?>

    <?php
    $id        = (int)($_GET['id'] ?? 0);
    $cedula_q  = trim($_GET['cedula'] ?? '');
    $tab       = $_GET['tab'] ?? 'notas'; // "Atender" desde pacientes.php lleva directo a notas
    if ($tab === 'notas') { $tab = 'nueva-nota'; }
    $clinic_id = Tenant::id();

    if ($id > 0) {
        $stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ? AND clinic_id = ?');
        $stmt->bind_param('ii', $id, $clinic_id);
    } else {
        $stmt = $db->prepare('SELECT * FROM pacientes WHERE cedula = ? AND clinic_id = ?');
        $stmt->bind_param('si', $cedula_q, $clinic_id);
    }
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

        $stmt4 = $db->prepare(
            'SELECT id, tratamiento, imagen_path, imageType, imageData
               FROM consulta WHERE cedula = ? AND clinic_id = ? ORDER BY id DESC'
        );
        $stmt4->bind_param('si', $cedula, $clinic_id);
        $stmt4->execute();
        $odontogramas = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt4->close();
    }

    // Historial médico: pagos/tratamientos (con fecha real) + odontogramas (sin fecha en
    // esta instalación de BD, se listan más recientes primero por id) intercalados.
    $historial = [];
    foreach ($pagos as $p) {
        $historial[] = ['tipo' => 'pago', 'fecha' => $p['fecha'], 'data' => $p];
    }
    foreach ($odontogramas as $o) {
        $historial[] = ['tipo' => 'odontograma', 'fecha' => null, 'data' => $o];
    }
    usort($historial, function ($a, $b) {
        if ($a['fecha'] === null && $b['fecha'] === null) { return $b['data']['id'] <=> $a['data']['id']; }
        if ($a['fecha'] === null) { return 1; }
        if ($b['fecha'] === null) { return -1; }
        return strcmp($b['fecha'], $a['fecha']);
    });
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

        <div class="animated fadeIn container-fluid centrar">

            <!-- Header: datos del paciente -->
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-3"
                             style="width:72px;height:72px;font-size:1.75rem;color:#6c757d;">
                            <i class="fa fa-user"></i>
                        </div>
                        <div>
                            <h1 class="page-title mb-1"><?= h($paciente['nombre']) ?> <?= h($paciente['apellido']) ?></h1>
                            <div class="text-muted small">
                                <span><i class="fa fa-id-card-o mr-1"></i> Cédula: <?= h($paciente['cedula']) ?></span>
                                <?php if (!empty($paciente['edad'])): ?>
                                    <span class="ml-3"><i class="fa fa-birthday-cake mr-1"></i> <?= h($paciente['edad']) ?> años</span>
                                <?php endif; ?>
                                <?php if (!empty($paciente['telefono'])): ?>
                                    <span class="ml-3"><i class="fa fa-phone mr-1"></i> <?= h($paciente['telefono']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($paciente['email'])): ?>
                                    <span class="ml-3"><i class="fa fa-envelope-o mr-1"></i> <?= h($paciente['email']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($paciente['direccion'])): ?>
                                    <span class="ml-3"><i class="fa fa-map-marker mr-1"></i> <?= h($paciente['direccion']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#modalEditarDatos">
                        <i class="fa fa-pencil mr-1"></i> Editar datos
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Columna izquierda: historial médico (pagos/tratamientos) -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex align-items-center">
                            <i class="fa fa-heartbeat mr-2 text-primary"></i>
                            <strong>Historial médico</strong>
                            <span class="badge badge-secondary ml-2"><?= count($historial) ?></span>
                        </div>
                        <div class="card-body" style="max-height:520px;overflow-y:auto;">
                            <?php if (empty($historial)): ?>
                                <p class="text-muted text-center mt-4">
                                    <i class="fa fa-folder-open-o fa-2x d-block mb-2"></i>
                                    Sin historial de tratamientos ni odontogramas para este paciente.
                                </p>
                            <?php else: ?>
                                <?php foreach ($historial as $ev): ?>
                                    <?php if ($ev['tipo'] === 'pago'): $p = $ev['data']; ?>
                                        <div class="d-flex mb-3 pb-3 border-bottom">
                                            <div class="mr-3 text-primary"><i class="fa fa-file-text-o"></i></div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?= h($p['tratamiento'] ?: 'Tratamiento') ?></strong>
                                                    <small class="text-muted"><?= h($p['fecha']) ?></small>
                                                </div>
                                                <div class="small text-muted">
                                                    B/. <?= h((string)$p['monto']) ?> · <?= h($p['tipo_de_pago']) ?> · Saldo: B/. <?= h((string)$p['saldo']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: $o = $ev['data'];
                                        if (!empty($o['imagen_path'])) {
                                            $imgSrc = h('uploads/' . $o['imagen_path']);
                                        } elseif (!empty($o['imageData'])) {
                                            $mime   = h($o['imageType'] ?? 'image/jpeg');
                                            $imgSrc = 'data:' . $mime . ';base64,' . base64_encode((string)$o['imageData']);
                                        } else {
                                            $imgSrc = null;
                                        }
                                    ?>
                                        <div class="d-flex mb-3 pb-3 border-bottom">
                                            <div class="mr-3 text-info"><i class="fa fa-registered"></i></div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Odontograma<?= $o['tratamiento'] ? ' — ' . h($o['tratamiento']) : '' ?></strong>
                                                    <small class="text-muted">Registro #<?= h((string)$o['id']) ?></small>
                                                </div>
                                                <?php if ($imgSrc): ?>
                                                    <img src="<?= $imgSrc ?>" alt="Odontograma" class="img-thumbnail mt-1" style="max-height:120px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: notas médicas -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <i class="fa fa-file-text-o mr-2 text-primary"></i>
                            <strong>Notas médicas</strong>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-tabs px-3 pt-2" id="notasTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link<?= $tab !== 'nueva-nota' ? ' active' : '' ?>" data-toggle="tab" href="#tab-notas-anteriores">
                                        Notas anteriores <span class="badge badge-secondary"><?= count($notas) ?></span>
                                    </a>
                                </li>
                                <?php if (Auth::hasRole(['admin', 'medico'])): ?>
                                <li class="nav-item">
                                    <a class="nav-link<?= $tab === 'nueva-nota' ? ' active' : '' ?>" data-toggle="tab" href="#tab-nueva-nota">
                                        Nueva nota
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                            <div class="tab-content p-3" style="max-height:460px;overflow-y:auto;">

                                <div class="tab-pane fade<?= $tab !== 'nueva-nota' ? ' show active' : '' ?>" id="tab-notas-anteriores" role="tabpanel">
                                    <?php if (empty($notas)): ?>
                                        <p class="text-muted text-center mt-4">
                                            <i class="fa fa-file-o fa-2x d-block mb-2"></i>
                                            No hay notas clínicas para este paciente.
                                        </p>
                                    <?php else: ?>
                                        <?php foreach ($notas as $n): ?>
                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?= h($n['fecha']) ?></strong>
                                                    <small class="text-muted"><?= h($n['autor']) ?></small>
                                                </div>
                                                <div style="white-space: pre-wrap;"><?= h($n['contenido']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (Auth::hasRole(['admin', 'medico'])): ?>
                                <div class="tab-pane fade<?= $tab === 'nueva-nota' ? ' show active' : '' ?>" id="tab-nueva-nota" role="tabpanel">
                                    <form method="POST" action="insert_nota.php">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="redirect_to" value="edit_paciente.php?id=<?= h((string)$paciente['id']) ?>&tab=notas">
                                        <input type="hidden" name="cedula" value="<?= h($paciente['cedula']) ?>">

                                        <div class="form-group">
                                            <label>Fecha</label>
                                            <input type="date" class="form-control border" name="fecha"
                                                value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Nota</label>
                                            <textarea class="form-control border" name="contenido" rows="8" required
                                                placeholder="Escribe la nota médica aquí..."></textarea>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save mr-1"></i> Guardar nota
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->

        <!-- Modal: editar datos personales -->
        <div class="modal fade" id="modalEditarDatos" tabindex="-1" role="dialog" aria-labelledby="modalEditarDatosLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="functions/edit_paciente.php">
                        <?= Csrf::field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarDatosLabel">Editar datos del paciente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-left">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <input type="submit" name="update" class="btn btn-success" value="Actualizar">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endif; ?>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
