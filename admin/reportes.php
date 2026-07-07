<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin']);
require_once __DIR__ . '/conexion/config.php';

// Rango de fechas — default: mes actual
$desde_raw = $_GET['desde'] ?? date('Y-m-01');
$hasta_raw = $_GET['hasta'] ?? date('Y-m-d');

// Validar formato y orden
$desde = (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde_raw)) ? $desde_raw : date('Y-m-01');
$hasta = (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta_raw)) ? $hasta_raw : date('Y-m-d');
if ($desde > $hasta) { [$desde, $hasta] = [$hasta, $desde]; }

$cid = Tenant::id();

// 1. KPIs
$stmt = $db->prepare('SELECT SUM(monto), SUM(saldo), COUNT(*), COUNT(DISTINCT cedula) FROM pago WHERE clinic_id = ? AND fecha BETWEEN ? AND ?');
$stmt->bind_param('iss', $cid, $desde, $hasta);
$stmt->execute();
[$total_monto, $total_saldo, $total_tx, $total_pacientes] = $stmt->get_result()->fetch_row();
$stmt->close();
$total_monto    = (float)($total_monto    ?? 0);
$total_saldo    = (float)($total_saldo    ?? 0);
$total_tx       = (int)  ($total_tx       ?? 0);
$total_pacientes= (int)  ($total_pacientes ?? 0);

// 2. Servicios más vendidos
$stmt = $db->prepare(
    'SELECT tratamiento, COUNT(*) AS cantidad, SUM(monto) AS total
     FROM pago WHERE clinic_id = ? AND fecha BETWEEN ? AND ? AND tratamiento IS NOT NULL AND tratamiento != ""
     GROUP BY tratamiento ORDER BY cantidad DESC LIMIT 10'
);
$stmt->bind_param('iss', $cid, $desde, $hasta);
$stmt->execute();
$servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$max_servicios = !empty($servicios) ? max(array_column($servicios, 'cantidad')) : 1;

// 3. Por tipo de pago
$stmt = $db->prepare(
    'SELECT COALESCE(NULLIF(tipo_de_pago,""),"Sin especificar") AS tipo, COUNT(*) AS cantidad, SUM(monto) AS total
     FROM pago WHERE clinic_id = ? AND fecha BETWEEN ? AND ?
     GROUP BY tipo_de_pago ORDER BY total DESC'
);
$stmt->bind_param('iss', $cid, $desde, $hasta);
$stmt->execute();
$tipos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$max_tipos = !empty($tipos) ? max(array_column($tipos, 'total')) : 1;

// 4. Saldos pendientes en el rango
$stmt = $db->prepare(
    'SELECT nombre, cedula, tratamiento, monto, saldo, fecha
     FROM pago WHERE clinic_id = ? AND saldo > 0 AND fecha BETWEEN ? AND ?
     ORDER BY saldo DESC LIMIT 50'
);
$stmt->bind_param('iss', $cid, $desde, $hasta);
$stmt->execute();
$pendientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$fmt = fn(float $n): string => '$' . number_format($n, 2, '.', ',');
$pageTitle = 'Reportes — ClíSys';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
    <style>
        .kpi-card { border: none; border-radius: 10px; color: #fff; padding: 1.25rem 1.5rem; }
        .kpi-card .kpi-label { font-size: .82rem; opacity: .85; text-transform: uppercase; letter-spacing: .05em; }
        .kpi-card .kpi-value { font-size: 1.9rem; font-weight: 700; line-height: 1.15; }
        .kpi-card .kpi-icon { font-size: 2.2rem; opacity: .35; }
        .kpi-green  { background: linear-gradient(135deg, #27ae60, #1e8449); }
        .kpi-red    { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .kpi-blue   { background: linear-gradient(135deg, #2980b9, #1a6fa0); }
        .kpi-indigo { background: linear-gradient(135deg, #8e44ad, #6c3483); }

        .bar-wrap { background: #f0f0f0; border-radius: 4px; height: 10px; overflow: hidden; }
        .bar-fill  { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #2980b9, #27ae60); }
        .bar-fill-red { background: linear-gradient(90deg, #e74c3c, #e67e22); }

        .section-card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.07); margin-bottom: 1.5rem; }
        .section-card .card-header { background: #fff; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; font-weight: 600; }
        .badge-saldo { background: #e74c3c; color: #fff; font-size: .82rem; border-radius: 6px; padding: 3px 8px; }

        .date-filter-bar { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.07); padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
        .date-filter-bar label { font-size: .85rem; font-weight: 600; color: #555; margin-bottom: 2px; }
    </style>
</head>
<body>
<?php include('partials/flash.php'); ?>
<?php include('partials/skip_nav.php'); ?>
<div class="wrapper d-flex align-items-stretch">
<?php include('menu.php'); ?>
<div id="content" class="p-4 p-md-5 pt-5">
    <div class="animated fadeIn">

        <?php
        $breadcrumb = [
            ['label' => 'Inicio', 'url' => 'inicio.php'],
            ['label' => 'Reportes'],
        ];
        include 'partials/breadcrumb.php';
        ?>

        <h1 class="page-title border-bottom pb-2 mb-4">
            <span class="fa fa-bar-chart mr-2" aria-hidden="true"></span> Reportes Financieros
        </h1>

        <!-- Filtro de fechas -->
        <div class="date-filter-bar">
            <form method="GET" action="reportes.php" class="form-row align-items-end">
                <div class="col-auto">
                    <label for="inp_desde">Desde</label>
                    <input type="date" id="inp_desde" name="desde" class="form-control form-control-sm"
                           value="<?= h($desde) ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-auto">
                    <label for="inp_hasta">Hasta</label>
                    <input type="date" id="inp_hasta" name="hasta" class="form-control form-control-sm"
                           value="<?= h($hasta) ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="fa fa-search mr-1"></span> Aplicar
                    </button>
                    <a href="reportes.php" class="btn btn-outline-secondary btn-sm ml-1">Este mes</a>
                </div>
                <div class="col-auto ml-auto text-muted" style="font-size:.85rem; align-self:center">
                    Período: <strong><?= h(date('d/m/Y', strtotime($desde))) ?></strong>
                    al <strong><?= h(date('d/m/Y', strtotime($hasta))) ?></strong>
                </div>
            </form>
        </div>

        <!-- KPI cards -->
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="kpi-card kpi-green d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Total ingresado</div>
                        <div class="kpi-value"><?= h($fmt($total_monto)) ?></div>
                    </div>
                    <span class="fa fa-dollar kpi-icon" aria-hidden="true"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="kpi-card kpi-red d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Saldo pendiente</div>
                        <div class="kpi-value"><?= h($fmt($total_saldo)) ?></div>
                    </div>
                    <span class="fa fa-exclamation-circle kpi-icon" aria-hidden="true"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="kpi-card kpi-blue d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Transacciones</div>
                        <div class="kpi-value"><?= $total_tx ?></div>
                    </div>
                    <span class="fa fa-list-alt kpi-icon" aria-hidden="true"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="kpi-card kpi-indigo d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label">Pacientes atendidos</div>
                        <div class="kpi-value"><?= $total_pacientes ?></div>
                    </div>
                    <span class="fa fa-user kpi-icon" aria-hidden="true"></span>
                </div>
            </div>
        </div>

        <!-- Servicios + Tipos de pago -->
        <div class="row mb-4">
            <div class="col-lg-7 mb-3">
                <div class="card section-card">
                    <div class="card-header">
                        <span class="fa fa-stethoscope mr-2"></span> Servicios más vendidos
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($servicios)): ?>
                        <p class="text-muted p-3 mb-0">Sin registros en este período.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tratamiento</th>
                                        <th class="text-center">Veces</th>
                                        <th class="text-right">Total</th>
                                        <th style="width:120px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($servicios as $s): ?>
                                <tr>
                                    <td><?= h($s['tratamiento']) ?></td>
                                    <td class="text-center"><strong><?= (int)$s['cantidad'] ?></strong></td>
                                    <td class="text-right"><?= h($fmt((float)$s['total'])) ?></td>
                                    <td class="align-middle">
                                        <div class="bar-wrap">
                                            <div class="bar-fill" style="width:<?= round($s['cantidad'] / $max_servicios * 100) ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-3">
                <div class="card section-card">
                    <div class="card-header">
                        <span class="fa fa-credit-card mr-2"></span> Por tipo de pago
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($tipos)): ?>
                        <p class="text-muted p-3 mb-0">Sin registros en este período.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-center">#</th>
                                        <th class="text-right">Total</th>
                                        <th style="width:90px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($tipos as $t): ?>
                                <tr>
                                    <td><?= h($t['tipo']) ?></td>
                                    <td class="text-center"><?= (int)$t['cantidad'] ?></td>
                                    <td class="text-right"><strong><?= h($fmt((float)$t['total'])) ?></strong></td>
                                    <td class="align-middle">
                                        <div class="bar-wrap">
                                            <div class="bar-fill" style="width:<?= $max_tipos > 0 ? round($t['total'] / $max_tipos * 100) : 0 ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldos pendientes -->
        <div class="card section-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><span class="fa fa-clock-o mr-2"></span> Pagos con saldo pendiente</span>
                <?php if (!empty($pendientes)): ?>
                <span class="badge badge-danger"><?= count($pendientes) ?> registro<?= count($pendientes) !== 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pendientes)): ?>
                <p class="text-muted p-3 mb-0">
                    <span class="fa fa-check-circle text-success mr-1"></span>
                    Sin saldos pendientes en este período.
                </p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Cédula</th>
                                <th>Tratamiento</th>
                                <th class="text-right">Pagado</th>
                                <th class="text-right">Saldo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pendientes as $p): ?>
                        <tr>
                            <td><?= h($p['nombre']) ?></td>
                            <td><?= h($p['cedula']) ?></td>
                            <td><?= h($p['tratamiento'] ?? '—') ?></td>
                            <td class="text-right"><?= h($fmt((float)$p['monto'])) ?></td>
                            <td class="text-right">
                                <span class="badge-saldo"><?= h($fmt((float)$p['saldo'])) ?></span>
                            </td>
                            <td><?= h(date('d/m/Y', strtotime($p['fecha']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
