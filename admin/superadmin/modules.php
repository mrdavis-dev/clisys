<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
Auth::requireSuperAdmin();
require_once __DIR__ . '/../conexion/config.php';

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verify();

    $clinic_id = (int)($_POST['clinic_id'] ?? 0);
    if ($clinic_id > 0) {
        $modules_all = $db->query('SELECT id FROM modules')->fetch_all(MYSQLI_ASSOC);
        foreach ($modules_all as $mod) {
            $mid     = (int)$mod['id'];
            $enabled = isset($_POST['mod_' . $mid]) ? 1 : 0;
            $stmt = $db->prepare(
                'INSERT INTO clinic_modules (clinic_id, module_id, enabled)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE enabled = ?'
            );
            $stmt->bind_param('iiii', $clinic_id, $mid, $enabled, $enabled);
            $stmt->execute();
            $stmt->close();
        }
        $success = true;
    }
}

// Fetch all clinics and modules
$clinics     = $db->query('SELECT id, name FROM clinics ORDER BY name')->fetch_all(MYSQLI_ASSOC);
$modules_all = $db->query('SELECT id, name, slug FROM modules ORDER BY name')->fetch_all(MYSQLI_ASSOC);

// Build a map: clinic_id → [module_id → enabled]
$cm_map = [];
$res = $db->query('SELECT clinic_id, module_id, enabled FROM clinic_modules');
while ($row = $res->fetch_assoc()) {
    $cm_map[(int)$row['clinic_id']][(int)$row['module_id']] = (bool)$row['enabled'];
}

$selected_clinic = (int)($_GET['clinic'] ?? ($clinics[0]['id'] ?? 0));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Módulos</title>
    <?php include __DIR__ . '/_head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
    <div id="content" class="p-4 p-md-5">
        <h2 class="mb-4"><span class="fa fa-puzzle-piece mr-2"></span> Módulos por clínica</h2>

        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Módulos actualizados correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php endif; ?>

        <!-- Clinic selector -->
        <div class="form-group row align-items-center mb-4">
            <label for="clinic_select" class="col-auto col-form-label font-weight-bold">Clínica:</label>
            <div class="col-auto">
                <select id="clinic_select" class="form-control">
                    <?php foreach ($clinics as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= (int)$c['id'] === $selected_clinic ? 'selected' : '' ?>>
                        <?= h($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php foreach ($clinics as $c): ?>
        <?php $cid = (int)$c['id']; ?>
        <form method="post" action="" id="form_clinic_<?= $cid ?>"
              class="clinic-form <?= $cid !== $selected_clinic ? 'd-none' : '' ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="clinic_id" value="<?= $cid ?>">
            <div class="card">
                <div class="card-header font-weight-bold"><?= h($c['name']) ?></div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($modules_all as $mod): ?>
                        <?php $mid = (int)$mod['id']; ?>
                        <?php $enabled = $cm_map[$cid][$mid] ?? false; ?>
                        <div class="col-sm-6 col-md-4 mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="mod_<?= $cid ?>_<?= $mid ?>"
                                       name="mod_<?= $mid ?>"
                                       <?= $enabled ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="mod_<?= $cid ?>_<?= $mid ?>">
                                    <?= h($mod['name']) ?>
                                    <small class="text-muted d-block"><code><?= h($mod['slug']) ?></code></small>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <span class="fa fa-save mr-1"></span> Guardar módulos
                    </button>
                </div>
            </div>
        </form>
        <?php endforeach; ?>
    </div>
</div><!-- .wrapper -->

<script>
document.getElementById('clinic_select').addEventListener('change', function () {
    document.querySelectorAll('.clinic-form').forEach(function (f) { f.classList.add('d-none'); });
    var target = document.getElementById('form_clinic_' + this.value);
    if (target) target.classList.remove('d-none');
});
</script>
<script src="../js/main.js"></script>
</body>
</html>
