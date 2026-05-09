<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
Auth::requireSuperAdmin();
require_once __DIR__ . '/../conexion/config.php';

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verify();

    if (isset($_POST['save_plan'])) {
        $pid          = (int)$_POST['plan_id'];
        $name         = trim($_POST['name'] ?? '');
        $max_patients = (int)$_POST['max_patients'];
        $max_users    = (int)$_POST['max_users'];
        $price        = (float)$_POST['price_monthly'];

        if ($name === '') $errors[] = 'El nombre es obligatorio.';

        if (empty($errors)) {
            $stmt = $db->prepare(
                'UPDATE plans SET name = ?, max_patients = ?, max_users = ?, price_monthly = ? WHERE id = ?'
            );
            $stmt->bind_param('siidi', $name, $max_patients, $max_users, $price, $pid);
            $stmt->execute();
            $stmt->close();
            $success = 'Plan actualizado.';
        }
    }

    if (isset($_POST['create_plan'])) {
        $name         = trim($_POST['name'] ?? '');
        $max_patients = (int)$_POST['max_patients'];
        $max_users    = (int)$_POST['max_users'];
        $price        = (float)$_POST['price_monthly'];

        if ($name === '') $errors[] = 'El nombre es obligatorio.';

        if (empty($errors)) {
            $stmt = $db->prepare(
                'INSERT INTO plans (name, max_patients, max_users, price_monthly) VALUES (?, ?, ?, ?)'
            );
            $stmt->bind_param('siid', $name, $max_patients, $max_users, $price);
            $stmt->execute();
            $stmt->close();
            $success = 'Plan creado.';
        }
    }
}

$plans = $db->query(
    'SELECT p.id, p.name, p.max_patients, p.max_users, p.price_monthly,
            COUNT(c.id) AS clinic_count
     FROM plans p
     LEFT JOIN clinics c ON c.plan_id = p.id
     GROUP BY p.id
     ORDER BY p.price_monthly ASC'
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Planes</title>
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
            <h2><span class="fa fa-list mr-2"></span> Planes</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createPlanModal">
                <span class="fa fa-plus mr-1"></span> Nuevo plan
            </button>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= h($success) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger" role="alert"><?= h($e) ?></div>
        <?php endforeach; ?>

        <div class="row">
        <?php foreach ($plans as $p): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><?= h($p['name']) ?></strong>
                        <span class="badge badge-info"><?= (int)$p['clinic_count'] ?> clínica(s)</span>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="plan_id" value="<?= (int)$p['id'] ?>">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control form-control-sm" name="name"
                                       value="<?= h($p['name']) ?>" required maxlength="60">
                            </div>
                            <div class="form-group">
                                <label>Máx. pacientes <small class="text-muted">(0 = ilimitado)</small></label>
                                <input type="number" class="form-control form-control-sm" name="max_patients"
                                       value="<?= (int)$p['max_patients'] ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label>Máx. usuarios <small class="text-muted">(0 = ilimitado)</small></label>
                                <input type="number" class="form-control form-control-sm" name="max_users"
                                       value="<?= (int)$p['max_users'] ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label>Precio mensual (USD)</label>
                                <input type="number" class="form-control form-control-sm" name="price_monthly"
                                       value="<?= number_format((float)$p['price_monthly'], 2, '.', '') ?>"
                                       min="0" step="0.01">
                            </div>
                            <button type="submit" name="save_plan" class="btn btn-sm btn-primary btn-block">
                                <span class="fa fa-save mr-1"></span> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div><!-- .wrapper -->

<!-- Create plan modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="">
            <?= Csrf::field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlanLabel">Nuevo plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required maxlength="60">
                    </div>
                    <div class="form-group">
                        <label>Máx. pacientes <small class="text-muted">(0 = ilimitado)</small></label>
                        <input type="number" class="form-control" name="max_patients" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Máx. usuarios <small class="text-muted">(0 = ilimitado)</small></label>
                        <input type="number" class="form-control" name="max_users" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Precio mensual (USD)</label>
                        <input type="number" class="form-control" name="price_monthly" value="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="create_plan" class="btn btn-primary">Crear plan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../js/main.js"></script>
</body>
</html>
