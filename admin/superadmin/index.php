<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
Auth::requireSuperAdmin();
require_once __DIR__ . '/../conexion/config.php';

// Dashboard counts — no Tenant scoping
$total_clinics  = $db->query('SELECT COUNT(*) FROM clinics')->fetch_row()[0];
$active_clinics = $db->query('SELECT COUNT(*) FROM clinics WHERE active = 1')->fetch_row()[0];
$total_users    = $db->query('SELECT COUNT(*) FROM users')->fetch_row()[0];
$total_patients = $db->query('SELECT COUNT(*) FROM pacientes')->fetch_row()[0];

$recent = $db->query(
    'SELECT c.name, c.subdomain, c.active, p.name AS plan_name, c.created_at
     FROM clinics c
     LEFT JOIN plans p ON p.id = c.plan_id
     ORDER BY c.created_at DESC
     LIMIT 5'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Dashboard</title>
    <?php include __DIR__ . '/_head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
    <div id="content" class="p-4 p-md-5">
        <h2 class="mb-4"><span class="fa fa-dashboard mr-2"></span> Dashboard</h2>

        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card sa-card sa-card-blue-dk h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="fa fa-hospital-o fa-2x mr-3" aria-hidden="true"></span>
                        <div>
                            <div style="font-size:2rem; font-weight:700;"><?= (int)$total_clinics ?></div>
                            <div>Clínicas totales</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card sa-card sa-card-blue h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="fa fa-check-circle fa-2x mr-3" aria-hidden="true"></span>
                        <div>
                            <div style="font-size:2rem; font-weight:700;"><?= (int)$active_clinics ?></div>
                            <div>Clínicas activas</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card sa-card sa-card-blue-lt h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="fa fa-users fa-2x mr-3" aria-hidden="true"></span>
                        <div>
                            <div style="font-size:2rem; font-weight:700;"><?= (int)$total_users ?></div>
                            <div>Usuarios totales</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card sa-card sa-card-slate h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="fa fa-user fa-2x mr-3" aria-hidden="true"></span>
                        <div>
                            <div style="font-size:2rem; font-weight:700;"><?= (int)$total_patients ?></div>
                            <div>Pacientes totales</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3">Clínicas recientes</h4>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Subdominio</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Creada</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= h($row['name']) ?></td>
                        <td><code><?= h($row['subdomain']) ?></code></td>
                        <td><?= h($row['plan_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($row['active']): ?>
                                <span class="badge badge-success">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td><?= h($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="clinics.php" class="btn btn-outline-primary btn-sm">
            <span class="fa fa-arrow-right mr-1"></span> Ver todas las clínicas
        </a>
    </div>
</div><!-- .wrapper -->
<script src="../js/main.js"></script>
</body>
</html>
