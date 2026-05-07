<?php
session_start();
require_once __DIR__ . '/admin/core/env.php';
require_once __DIR__ . '/admin/core/Csrf.php';
require_once __DIR__ . '/admin/core/Database.php';
require_once __DIR__ . '/admin/core/Tenant.php';
loadEnv(__DIR__ . '/.env');
$db = Database::get();

// Load specialties for the dropdown
$specialties = [];
$res = $db->query('SELECT id, name, slug FROM specialties ORDER BY name');
if ($res) {
    $specialties = $res->fetch_all(MYSQLI_ASSOC);
}

$error = $_GET['error'] ?? '';
$errors = [
    'empty'       => 'Todos los campos son obligatorios.',
    'subdomain'   => 'El subdominio ya está en uso. Elige otro.',
    'subdomain_fmt'=> 'El subdominio solo puede tener letras, números y guiones (3-30 chars).',
    'password'    => 'Las contraseñas no coinciden.',
    'short_pwd'   => 'La contraseña debe tener al menos 8 caracteres.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar clínica — ClíSys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <style>
        body { background-color: #f0f4f8; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .btn-primary { background-color: #229b94; border-color: #229b94; }
        .btn-primary:hover { background-color: #1a7a74; border-color: #1a7a74; }
        .plan-card { cursor: pointer; border: 2px solid transparent; border-radius: 8px; transition: border-color .2s; }
        .plan-card:hover { border-color: #229b94; }
        .plan-card.selected { border-color: #229b94; background: #f0fffe; }
        .price { font-size: 1.8rem; font-weight: 700; color: #229b94; }
        .subdomain-preview { font-size: .85rem; color: #6c757d; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <p class="display-6 fw-bold" style="color:#229b94">ClíSys</p>
                <p class="text-muted">Crea la cuenta de tu clínica en segundos</p>
            </div>

            <?php if ($error && isset($errors[$error])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors[$error], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <form method="POST" action="signup_process.php" id="frmSignup">
                    <?= Csrf::field() ?>

                    <h5 class="mb-3 border-bottom pb-2">Datos de la clínica</h5>

                    <div class="mb-3">
                        <label class="form-label">Nombre de la clínica <span class="text-danger">*</span></label>
                        <input type="text" name="clinic_name" class="form-control" required
                               placeholder="Clínica Ejemplo" maxlength="120">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subdominio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="subdomain" id="inp_subdomain" class="form-control" required
                                   placeholder="mi-clinica" maxlength="30" pattern="[a-z0-9\-]{3,30}">
                            <span class="input-group-text">.clisys.com</span>
                        </div>
                        <div class="subdomain-preview mt-1" id="subdomain_preview">Solo minúsculas, números y guiones (3–30 chars).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Especialidad <span class="text-danger">*</span></label>
                        <select name="specialty_id" class="form-select" required>
                            <option value="">— Selecciona —</option>
                            <?php foreach ($specialties as $sp): ?>
                            <option value="<?= (int)$sp['id'] ?>">
                                <?= htmlspecialchars($sp['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h5 class="mt-4 mb-3 border-bottom pb-2">Plan de suscripción</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="plan-card p-3 text-center" onclick="selectPlan('free', this)">
                                <input type="radio" name="plan" value="free" class="d-none" id="plan_free">
                                <div class="price">$0</div>
                                <div class="fw-bold">Gratis</div>
                                <ul class="list-unstyled text-muted small mt-2">
                                    <li>50 pacientes</li>
                                    <li>2 usuarios</li>
                                    <li>Módulos básicos</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="plan-card p-3 text-center selected" onclick="selectPlan('basic', this)">
                                <input type="radio" name="plan" value="basic" class="d-none" id="plan_basic" checked>
                                <div class="price">$29.99</div>
                                <div class="fw-bold">Básico</div>
                                <ul class="list-unstyled text-muted small mt-2">
                                    <li>500 pacientes</li>
                                    <li>10 usuarios</li>
                                    <li>Todos los módulos</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="plan-card p-3 text-center" onclick="selectPlan('pro', this)">
                                <input type="radio" name="plan" value="pro" class="d-none" id="plan_pro">
                                <div class="price">$79.99</div>
                                <div class="fw-bold">Pro</div>
                                <ul class="list-unstyled text-muted small mt-2">
                                    <li>Ilimitado</li>
                                    <li>Ilimitado</li>
                                    <li>Todos los módulos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 border-bottom pb-2">Cuenta de administrador</h5>

                    <div class="mb-3">
                        <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control" required
                               placeholder="Tu nombre" maxlength="120">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Usuario <span class="text-danger">*</span></label>
                        <input type="text" name="admin_username" class="form-control" required
                               placeholder="admin" maxlength="60">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="admin_password" id="pwd1" class="form-control"
                                   required minlength="8" placeholder="Mínimo 8 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="admin_password2" id="pwd2" class="form-control"
                                   required placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Crear mi clínica</button>
                    </div>

                    <p class="text-center text-muted small mt-3">
                        ¿Ya tienes cuenta? <a href="admin/index.php">Inicia sesión</a>
                    </p>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
function selectPlan(val, el) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('plan_' + val).checked = true;
}

document.getElementById('inp_subdomain').addEventListener('input', function () {
    var v = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    this.value = v;
    document.getElementById('subdomain_preview').textContent =
        v ? v + '.clisys.com' : 'Solo minúsculas, números y guiones (3–30 chars).';
});

document.getElementById('frmSignup').addEventListener('submit', function (e) {
    var p1 = document.getElementById('pwd1').value;
    var p2 = document.getElementById('pwd2').value;
    if (p1 !== p2) {
        e.preventDefault();
        alert('Las contraseñas no coinciden.');
    }
});
</script>
</body>
</html>
