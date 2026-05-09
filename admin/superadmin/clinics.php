<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
Auth::requireSuperAdmin();
require_once __DIR__ . '/../conexion/config.php';

$errors  = [];
$success = '';
$action  = $_GET['action'] ?? '';
$edit_id = (int)($_GET['id'] ?? 0);

// Fetch lookups
$specialties = $db->query('SELECT id, name FROM specialties ORDER BY name')->fetch_all(MYSQLI_ASSOC);
$plans       = $db->query('SELECT id, name FROM plans ORDER BY name')->fetch_all(MYSQLI_ASSOC);
$modules_all = $db->query('SELECT id, name, slug FROM modules ORDER BY name')->fetch_all(MYSQLI_ASSOC);

// ── POST handlers ──────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verify();

    // Deactivate / activate
    if (isset($_POST['toggle_id'])) {
        $tid  = (int)$_POST['toggle_id'];
        $tval = (int)$_POST['toggle_val'];
        $stmt = $db->prepare('UPDATE clinics SET active = ? WHERE id = ?');
        $stmt->bind_param('ii', $tval, $tid);
        $stmt->execute();
        $stmt->close();
        header('Location: clinics.php?ok=toggle');
        exit;
    }

    // Delete (soft: set active=0 only if no children — show warning instead)
    if (isset($_POST['delete_id'])) {
        $did = (int)$_POST['delete_id'];
        // Count patients
        $chk = $db->prepare('SELECT COUNT(*) FROM pacientes WHERE clinic_id = ?');
        $chk->bind_param('i', $did);
        $chk->execute();
        $chk->bind_result($cnt);
        $chk->fetch();
        $chk->close();
        if ($cnt > 0) {
            $errors[] = 'No se puede eliminar: la clínica tiene ' . $cnt . ' paciente(s). Desactívala en su lugar.';
        } else {
            $stmt = $db->prepare('DELETE FROM clinics WHERE id = ?');
            $stmt->bind_param('i', $did);
            $stmt->execute();
            $stmt->close();
            header('Location: clinics.php?ok=deleted');
            exit;
        }
    }

    // Create
    if (isset($_POST['create_clinic'])) {
        $name     = trim($_POST['name'] ?? '');
        $subdomain = trim($_POST['subdomain'] ?? '');
        $spec_id  = (int)($_POST['specialty_id'] ?? 0) ?: null;
        $plan_id  = (int)($_POST['plan_id'] ?? 0) ?: null;
        $expires  = trim($_POST['plan_expires_at'] ?? '') ?: null;
        $active   = isset($_POST['active']) ? 1 : 0;

        if ($name === '')     $errors[] = 'El nombre es obligatorio.';
        if ($subdomain === '') $errors[] = 'El subdominio es obligatorio.';
        if (!preg_match('/^[a-z0-9\-]+$/', $subdomain)) $errors[] = 'Subdominio solo puede contener letras minúsculas, números y guiones.';

        if (empty($errors)) {
            $stmt = $db->prepare(
                'INSERT INTO clinics (name, subdomain, specialty_id, plan_id, plan_expires_at, active)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssiisi', $name, $subdomain, $spec_id, $plan_id, $expires, $active);
            if ($stmt->execute()) {
                $new_cid = $stmt->insert_id;
                $stmt->close();
                // Enable all modules for new clinic by default
                foreach ($modules_all as $mod) {
                    $ins = $db->prepare('INSERT IGNORE INTO clinic_modules (clinic_id, module_id, enabled) VALUES (?, ?, 1)');
                    $ins->bind_param('ii', $new_cid, $mod['id']);
                    $ins->execute();
                    $ins->close();
                }
                header('Location: clinics.php?ok=created');
                exit;
            } else {
                $errors[] = 'Error al crear la clínica: ' . h($db->error);
            }
        }
    }

    // Edit
    if (isset($_POST['edit_clinic'])) {
        $eid      = (int)$_POST['edit_id'];
        $name     = trim($_POST['name'] ?? '');
        $subdomain = trim($_POST['subdomain'] ?? '');
        $spec_id  = (int)($_POST['specialty_id'] ?? 0) ?: null;
        $plan_id  = (int)($_POST['plan_id'] ?? 0) ?: null;
        $expires  = trim($_POST['plan_expires_at'] ?? '') ?: null;
        $active   = isset($_POST['active']) ? 1 : 0;

        if ($name === '')     $errors[] = 'El nombre es obligatorio.';
        if ($subdomain === '') $errors[] = 'El subdominio es obligatorio.';
        if (!preg_match('/^[a-z0-9\-]+$/', $subdomain)) $errors[] = 'Subdominio solo puede contener letras minúsculas, números y guiones.';

        if (empty($errors)) {
            $stmt = $db->prepare(
                'UPDATE clinics SET name=?, subdomain=?, specialty_id=?, plan_id=?, plan_expires_at=?, active=?
                 WHERE id=?'
            );
            $stmt->bind_param('ssiisii', $name, $subdomain, $spec_id, $plan_id, $expires, $active, $eid);
            $stmt->execute();
            $stmt->close();
            header('Location: clinics.php?ok=updated');
            exit;
        }
        $edit_id = $eid;
        $action  = 'edit';
    }
}

// ── Fetch clinics list ─────────────────────────────────────────────────────

$clinics = $db->query(
    'SELECT c.id, c.name, c.subdomain, c.active, c.plan_expires_at, c.created_at,
            s.name AS specialty_name, p.name AS plan_name
     FROM clinics c
     LEFT JOIN specialties s ON s.id = c.specialty_id
     LEFT JOIN plans p ON p.id = c.plan_id
     ORDER BY c.id ASC'
)->fetch_all(MYSQLI_ASSOC);

// Fetch clinic for edit
$edit_row = null;
if ($action === 'edit' && $edit_id > 0) {
    $stmt = $db->prepare('SELECT * FROM clinics WHERE id = ?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$flash_ok = $_GET['ok'] ?? null;
$flash_map = [
    'created' => 'Clínica creada correctamente.',
    'updated' => 'Clínica actualizada.',
    'deleted' => 'Clínica eliminada.',
    'toggle'  => 'Estado actualizado.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Clínicas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
    <div id="content" class="p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><span class="fa fa-hospital-o mr-2"></span> Clínicas</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                <span class="fa fa-plus mr-1"></span> Nueva clínica
            </button>
        </div>

        <?php if ($flash_ok && isset($flash_map[$flash_ok])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= h($flash_map[$flash_ok]) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger" role="alert"><?= h($e) ?></div>
        <?php endforeach; ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Subdominio</th>
                        <th>Especialidad</th>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($clinics as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= h($c['name']) ?></td>
                        <td><code><?= h($c['subdomain']) ?></code></td>
                        <td><?= h($c['specialty_name'] ?? '—') ?></td>
                        <td><?= h($c['plan_name'] ?? '—') ?></td>
                        <td><?= $c['plan_expires_at'] ? h($c['plan_expires_at']) : '—' ?></td>
                        <td>
                            <?php if ($c['active']): ?>
                                <span class="badge badge-success">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <a href="clinics.php?action=edit&id=<?= (int)$c['id'] ?>"
                               class="btn btn-sm btn-outline-primary mr-1" title="Editar">
                                <span class="fa fa-pencil" aria-hidden="true"></span>
                            </a>
                            <form method="post" action="" class="d-inline">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="toggle_id"  value="<?= (int)$c['id'] ?>">
                                <input type="hidden" name="toggle_val" value="<?= $c['active'] ? 0 : 1 ?>">
                                <button type="submit" class="btn btn-sm <?= $c['active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                        title="<?= $c['active'] ? 'Desactivar' : 'Activar' ?>">
                                    <span class="fa fa-<?= $c['active'] ? 'ban' : 'check' ?>" aria-hidden="true"></span>
                                </button>
                            </form>
                            <form method="post" action="" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar la clínica «<?= h(addslashes($c['name'])) ?>»? Esta acción es irreversible.')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="delete_id" value="<?= (int)$c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger ml-1" title="Eliminar">
                                    <span class="fa fa-trash" aria-hidden="true"></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><!-- .wrapper -->

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="">
            <?= Csrf::field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Nueva clínica</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php include __DIR__ . '/_clinic_fields.php'; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="create_clinic" class="btn btn-primary">Crear clínica</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_row): ?>
<!-- Edit form (inline, shown when action=edit) -->
<div class="modal fade show d-block" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
    <div class="modal-dialog">
        <form method="post" action="">
            <?= Csrf::field() ?>
            <input type="hidden" name="edit_id" value="<?= (int)$edit_row['id'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar clínica — <?= h($edit_row['name']) ?></h5>
                    <a href="clinics.php" class="close" aria-label="Cerrar"><span aria-hidden="true">&times;</span></a>
                </div>
                <div class="modal-body">
                    <?php include __DIR__ . '/_clinic_fields.php'; ?>
                </div>
                <div class="modal-footer">
                    <a href="clinics.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="edit_clinic" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<script src="../js/main.js"></script>
</body>
</html>
