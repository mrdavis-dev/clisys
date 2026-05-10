<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
require_once __DIR__ . '/conexion/config.php';

$clinic_id = Tenant::id();

// Upcoming appointments
$stmt_citas = $db->prepare(
    'SELECT * FROM citas_tabla WHERE clinic_id = ? ORDER BY fecha_de_cita ASC, hora_de_cita ASC'
);
$stmt_citas->bind_param('i', $clinic_id);
$stmt_citas->execute();
$result_citas = $stmt_citas->get_result();
$citas = $result_citas->fetch_all(MYSQLI_ASSOC);
$stmt_citas->close();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && !empty($_POST['checkbox'])) {
    Csrf::verify();
    $stmt_del = $db->prepare('DELETE FROM citas_tabla WHERE id = ? AND clinic_id = ?');
    foreach ($_POST['checkbox'] as $del_id) {
        $id_int = (int)$del_id;
        $stmt_del->bind_param('ii', $id_int, $clinic_id);
        $stmt_del->execute();
    }
    $stmt_del->close();
    header('Location: inicio.php?deleted=1');
    exit;
}

// Quick stats
$today = date('Y-m-d');
$stmt_today = $db->prepare('SELECT COUNT(*) FROM citas_tabla WHERE clinic_id = ? AND fecha_de_cita = ?');
$stmt_today->bind_param('is', $clinic_id, $today);
$stmt_today->execute();
$stmt_today->bind_result($citas_hoy);
$stmt_today->fetch();
$stmt_today->close();

$stmt_patients = $db->prepare('SELECT COUNT(*) FROM pacientes WHERE clinic_id = ?');
$stmt_patients->bind_param('i', $clinic_id);
$stmt_patients->execute();
$stmt_patients->bind_result($total_patients);
$stmt_patients->fetch();
$stmt_patients->close();

$stmt_week = $db->prepare(
    'SELECT COUNT(*) FROM citas_tabla WHERE clinic_id = ? AND fecha_de_cita BETWEEN ? AND DATE_ADD(?, INTERVAL 6 DAY)'
);
$stmt_week->bind_param('iss', $clinic_id, $today, $today);
$stmt_week->execute();
$stmt_week->bind_result($citas_semana);
$stmt_week->fetch();
$stmt_week->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio — ClíSys</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            color: #fff;
            box-shadow: 0 2px 10px rgba(30,58,138,.13);
            transition: transform .15s, box-shadow .15s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(30,58,138,.18); }
        .stat-card .stat-value { font-size: 2.2rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: .8rem; opacity: .85; margin-top: .25rem; text-transform: uppercase; letter-spacing: .05em; }
        .stat-card .stat-icon  { font-size: 2rem; opacity: .30; }
        .stat-blue   { background: linear-gradient(135deg, #1E3A8A, #2563EB); }
        .stat-indigo { background: linear-gradient(135deg, #3730a3, #4f46e5); }
        .stat-slate  { background: linear-gradient(135deg, #334155, #475569); }

        .appointments-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(30,58,138,.08);
        }
        .appointments-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        .appointments-card .card-header h5 {
            font-weight: 700;
            color: #1e3a8a;
            margin: 0;
        }
        .table thead th {
            background: #f1f5f9;
            color: #475569;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
        }
        .table tbody tr { vertical-align: middle; }
        .table tbody tr:hover { background: rgba(37,99,235,.04); }
        .table td { border-color: #f1f5f9; color: #334155; font-size: .9rem; }
        .badge-date {
            background: rgba(37,99,235,.10);
            color: #2563EB;
            font-weight: 600;
            font-size: .78rem;
            padding: .3em .7em;
            border-radius: 6px;
        }
        .badge-time {
            background: rgba(71,85,105,.10);
            color: #475569;
            font-weight: 600;
            font-size: .78rem;
            padding: .3em .7em;
            border-radius: 6px;
        }
        .action-bar {
            background: #fff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 8px rgba(30,58,138,.07);
            margin-bottom: 1.5rem;
        }
        .empty-appointments {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
        }
        .empty-appointments .fa { font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1; }
        .empty-appointments p { font-size: .95rem; }
        .btn-action {
            border-radius: 8px;
            font-weight: 500;
            font-size: .875rem;
            padding: .45rem 1rem;
        }
        .modal-header { border-radius: 8px 8px 0 0; }
        .form-section-title {
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin: 1.25rem 0 .5rem;
            padding-bottom: .35rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .page-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-header-bar h2 { color: #1e3a8a; font-weight: 700; margin: 0; }
        .page-header-bar .page-sub { color: #64748b; font-size: .875rem; margin-top: .2rem; }
    </style>
</head>
<body>
<?php include 'partials/flash.php'; ?>
<?php include 'partials/skip_nav.php'; ?>

<div class="wrapper d-flex align-items-stretch">
<?php include 'menu.php'; ?>

<div id="content" class="p-4 p-md-5">

    <!-- Page header -->
    <div class="page-header-bar">
        <div>
            <h2><span class="fa fa-home mr-2" aria-hidden="true"></span>Inicio</h2>
            <div class="page-sub"><?= date('l, d \d\e F \d\e Y') ?></div>
        </div>
        <div class="d-flex gap-2">
            <?php if (Auth::hasRole(['admin', 'medico'])): ?>
            <button class="btn btn-primary btn-action mr-2" data-toggle="modal" data-target="#modal-paciente">
                <span class="fa fa-user-plus mr-1"></span> Agregar paciente
            </button>
            <?php endif; ?>
            <button class="btn btn-outline-primary btn-action" data-toggle="modal" data-target="#modal-cita">
                <span class="fa fa-calendar-plus-o mr-1"></span> Agregar cita
            </button>
        </div>
    </div>

    <!-- Stats row -->
    <div class="row mb-4">
        <div class="col-sm-4 mb-3">
            <div class="stat-card stat-blue d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?= $citas_hoy ?></div>
                    <div class="stat-label">Citas hoy</div>
                </div>
                <div class="stat-icon"><span class="fa fa-calendar-check-o"></span></div>
            </div>
        </div>
        <div class="col-sm-4 mb-3">
            <div class="stat-card stat-indigo d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?= $citas_semana ?></div>
                    <div class="stat-label">Esta semana</div>
                </div>
                <div class="stat-icon"><span class="fa fa-calendar"></span></div>
            </div>
        </div>
        <div class="col-sm-4 mb-3">
            <div class="stat-card stat-slate d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?= $total_patients ?></div>
                    <div class="stat-label">Pacientes registrados</div>
                </div>
                <div class="stat-icon"><span class="fa fa-users"></span></div>
            </div>
        </div>
    </div>

    <!-- Appointments table -->
    <div class="card appointments-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5><span class="fa fa-clock-o mr-2"></span>Próximas citas</h5>
            <span class="badge badge-primary"><?= count($citas) ?> total</span>
        </div>
        <div class="card-body p-0">
            <form method="POST" action="" id="form-delete-citas">
                <?= Csrf::field() ?>
                <?php if (empty($citas)): ?>
                <div class="empty-appointments">
                    <div class="fa fa-calendar-o"></div>
                    <p>No hay citas programadas.</p>
                    <button type="button" class="btn btn-primary btn-action mt-2" data-toggle="modal" data-target="#modal-cita">
                        <span class="fa fa-plus mr-1"></span> Agregar primera cita
                    </button>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px"></th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Asunto</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($citas as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="checkbox[]" value="<?= (int)$row['id'] ?>"></td>
                                <td><span class="badge-date"><?= h($row['fecha_de_cita']) ?></span></td>
                                <td><span class="badge-time"><?= h($row['hora_de_cita']) ?></span></td>
                                <td><strong><?= h($row['nombre_paciente']) ?></strong></td>
                                <td><?= h($row['asunto_de_la_cita']) ?></td>
                                <td><?= h($row['doctor']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top d-flex align-items-center" style="background:#f8fafc; border-radius:0 0 12px 12px;">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-action"
                        data-confirm="true"
                        data-confirm-form="#form-delete-citas"
                        data-confirm-msg="¿Borrar las citas seleccionadas? Esta acción no se puede deshacer.">
                        <span class="fa fa-trash mr-1"></span> Borrar seleccionadas
                    </button>
                    <input type="hidden" name="delete" value="1">
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

</div><!-- #content -->
</div><!-- .wrapper -->

<!-- ── Modal: Agregar paciente ─────────────────────────────── -->
<div class="modal fade" id="modal-paciente" tabindex="-1" role="dialog" aria-labelledby="modal-paciente-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-paciente-title">
                    <span class="fa fa-user-plus mr-2"></span>Agregar paciente
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="insert_paciente.php">
                <?= Csrf::field() ?>
                <div class="modal-body">

                    <div class="form-section-title">Datos personales</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cédula</label>
                                <input type="text" class="form-control" name="cedula" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Edad</label>
                                <input type="text" class="form-control" name="edad">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ocupación</label>
                                <input type="text" class="form-control" name="ocupacion">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" class="form-control" name="direccion">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title">Motivo de consulta</div>
                    <div class="form-group">
                        <label>Motivo de la visita</label>
                        <textarea class="form-control" name="motivo" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Hábitos higiénicos</label>
                        <textarea class="form-control" name="habitos" rows="2"></textarea>
                    </div>

                    <div class="form-section-title">Antecedentes médicos</div>
                    <div class="row">
                        <?php
                        $preguntas = [
                            'bajotratamiento' => '¿Está bajo tratamiento médico actualmente?',
                            'quirurgicamente' => '¿Ha sido hospitalizado quirúrgicamente?',
                            'droga'           => '¿Está tomando algún medicamento o droga?',
                            'alergia'         => '¿Presenta algún tipo de alergia?',
                            'cardiaca'        => '¿Ha tenido algún tipo de enfermedad cardiaca?',
                            'diabético'       => '¿Es usted diabético o algún familiar lo padece?',
                            'hepatitis'       => '¿Ha tenido tuberculosis o hepatitis?',
                            'sangrado'        => '¿Ha presentado alteraciones en el sangrado?',
                            'transmision'     => '¿Ha tenido alguna enfermedad de transmisión sexual?',
                            'habito'          => '¿Tiene algún tipo de mal hábito?',
                        ];
                        foreach ($preguntas as $name => $label): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:.85rem"><?= h($label) ?></label>
                                <select class="form-control" name="<?= h($name) ?>">
                                    <option value="">—</option>
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="reset" class="btn btn-light">Limpiar</button>
                    <button type="submit" class="btn btn-primary" name="submit">
                        <span class="fa fa-save mr-1"></span> Guardar paciente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal: Agregar cita ─────────────────────────────────── -->
<div class="modal fade" id="modal-cita" tabindex="-1" role="dialog" aria-labelledby="modal-cita-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cita-title">
                    <span class="fa fa-calendar-plus-o mr-2"></span>Agregar cita
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="insert.php" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha de cita</label>
                                <input type="date" class="form-control" name="fecha" required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de cita</label>
                                <input type="time" class="form-control" name="hora" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre del paciente</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Asunto de la cita</label>
                        <input type="text" class="form-control" name="asunto">
                    </div>
                    <div class="form-group">
                        <label>Doctor de preferencia</label>
                        <select name="doctor" required class="form-control">
                            <option value="">Seleccione un doctor…</option>
                            <?php
                            $cid_doc = Tenant::id();
                            $stmt_doc = $db->prepare('SELECT name FROM staff WHERE clinic_id = ? AND active = 1 ORDER BY name');
                            $stmt_doc->bind_param('i', $cid_doc);
                            $stmt_doc->execute();
                            $res_doc = $stmt_doc->get_result();
                            while ($doc = $res_doc->fetch_assoc()) {
                                echo '<option value="' . h($doc['name']) . '">' . h($doc['name']) . '</option>';
                            }
                            $stmt_doc->close();
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="reset" class="btn btn-light">Limpiar</button>
                    <button type="submit" class="btn btn-primary" name="save">
                        <span class="fa fa-save mr-1"></span> Guardar cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/confirm_modal.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
