<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
Auth::requireSuperAdmin();
require_once __DIR__ . '/../conexion/config.php';

$errors  = [];
$success = '';
$clinic_id = (int)($_GET['clinic_id'] ?? 0);

// Fetch all clinics for selector
$all_clinics = $db->query('SELECT id, name FROM clinics ORDER BY name')->fetch_all(MYSQLI_ASSOC);

// ── POST handlers ──────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verify();

    $post_clinic_id = (int)($_POST['clinic_id'] ?? 0);

    // Create user
    if (isset($_POST['create_user'])) {
        $username = trim($_POST['username'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $pass     = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? '';
        if ($username === '')     $errors[] = 'Usuario es obligatorio.';
        if ($name === '')         $errors[] = 'Nombre es obligatorio.';
        if (strlen($pass) < 6)   $errors[] = 'Contraseña mínimo 6 caracteres.';
        if ($post_clinic_id <= 0) $errors[] = 'Clínica inválida.';
        $role = $_POST['role'] ?? '';

        if (empty($errors)) {
            // Check duplicate username in same clinic
            $chk = $db->prepare('SELECT id FROM users WHERE username = ? AND clinic_id = ?');
            $chk->bind_param('si', $username, $post_clinic_id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                $errors[] = 'Ya existe un usuario con ese nombre en esta clínica.';
            }
            $chk->close();
        }

        if (empty($errors)) {
            $valid_roles = ['admin', 'recepcion', 'medico'];
        if (!in_array($role, $valid_roles, true)) $errors[] = 'Rol inválido.';

            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $db->prepare('INSERT INTO users (clinic_id, username, name, password, role) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issss', $post_clinic_id, $username, $name, $hash, $role);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: clinic_users.php?clinic_id=' . $post_clinic_id . '&ok=created');
                exit;
            } else {
                $errors[] = 'Error al crear usuario: ' . h($db->error);
            }
        }
        $clinic_id = $post_clinic_id;
    }

    // Delete user
    if (isset($_POST['delete_id'])) {
        $del_id    = (int)$_POST['delete_id'];
        $del_cid   = (int)$_POST['delete_clinic_id'];
        $stmt = $db->prepare('DELETE FROM users WHERE id = ? AND clinic_id = ?');
        $stmt->bind_param('ii', $del_id, $del_cid);
        $stmt->execute();
        $stmt->close();
        header('Location: clinic_users.php?clinic_id=' . $del_cid . '&ok=deleted');
        exit;
    }
}

// ── Fetch users for selected clinic ────────────────────────────────────────

$clinic_row = null;
$users      = [];
if ($clinic_id > 0) {
    $stmt = $db->prepare('SELECT id, name FROM clinics WHERE id = ?');
    $stmt->bind_param('i', $clinic_id);
    $stmt->execute();
    $clinic_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($clinic_row) {
        $stmt = $db->prepare("SELECT id, username, name, role FROM users WHERE clinic_id = ? AND role != 'superadmin' ORDER BY id ASC");
        $stmt->bind_param('i', $clinic_id);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$flash_ok  = $_GET['ok'] ?? null;
$flash_map = [
    'created' => 'Usuario creado correctamente.',
    'deleted' => 'Usuario eliminado.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Usuarios de clínica</title>
    <?php include __DIR__ . '/_head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
    <div id="content" class="p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><span class="fa fa-users mr-2"></span> Usuarios por clínica</h2>
            <?php if ($clinic_row): ?>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                <span class="fa fa-plus mr-1"></span> Nuevo usuario
            </button>
            <?php endif; ?>
        </div>

        <!-- Clinic selector -->
        <form method="get" action="" class="form-inline mb-4">
            <label for="clinic_select" class="mr-2 font-weight-bold">Clínica:</label>
            <select name="clinic_id" id="clinic_select" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">— Seleccionar —</option>
                <?php foreach ($all_clinics as $cl): ?>
                <option value="<?= (int)$cl['id'] ?>" <?= $clinic_id === (int)$cl['id'] ? 'selected' : '' ?>>
                    <?= h($cl['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($flash_ok && isset($flash_map[$flash_ok])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= h($flash_map[$flash_ok]) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger" role="alert"><?= h($e) ?></div>
        <?php endforeach; ?>

        <?php if ($clinic_id > 0 && !$clinic_row): ?>
        <div class="alert alert-warning">Clínica no encontrada.</div>
        <?php elseif ($clinic_row): ?>
        <h5 class="mb-3">Usuarios de <strong><?= h($clinic_row['name']) ?></strong></h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Sin usuarios.</td></tr>
                <?php endif; ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= h($u['username']) ?></td>
                        <td><?= h($u['name']) ?></td>
                        <td><span class="badge badge-info"><?= h($u['role']) ?></span></td>
                        <td>
                            <form method="post" action="" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar usuario «<?= h(addslashes($u['username'])) ?>»?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="delete_id"        value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="delete_clinic_id" value="<?= (int)$clinic_id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <span class="fa fa-trash" aria-hidden="true"></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">Selecciona una clínica para ver y gestionar sus usuarios.</p>
        <?php endif; ?>
    </div>
</div><!-- .wrapper -->

<?php if ($clinic_row): ?>
<!-- Create User Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="">
            <?= Csrf::field() ?>
            <input type="hidden" name="clinic_id" value="<?= (int)$clinic_id ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Nuevo usuario — <?= h($clinic_row['name']) ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="f_username">Usuario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="f_username" name="username" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="f_name">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="f_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="f_pass">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="f_pass" name="password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="f_role">Rol <span class="text-danger">*</span></label>
                        <select name="role" id="f_role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="recepcion">Recepción</option>
                            <option value="medico">Médico</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="create_user" class="btn btn-primary">Crear usuario</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="../js/main.js"></script>
</body>
</html>
