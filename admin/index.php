<?php
require_once __DIR__ . '/core/Csrf.php';
Csrf::generate();
$pageTitle = 'Clisys — Iniciar sesión';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/partials/auth_head.php'; ?>
</head>
<body>
  <div class="container animated fadeIn text-center mt-5 mb-2">
    <h1 class="display-4 fw-bold auth-brand">Clisys</h1>
    <p class="text-muted">Sistema de gestión clínica</p>
  </div>
  <?php if (isset($_GET['expired'])): ?>
  <div class="container animated fadeIn" style="max-width:460px">
    <div class="alert alert-warning text-center" role="alert">
      <span class="fa fa-clock-o mr-2"></span>
      Tu sesión expiró por inactividad. Inicia sesión nuevamente.
    </div>
  </div>
  <?php endif; ?>
  <div class="container animated fadeIn delay-1s">
    <form method="POST" action="functions/login.php" data-validate>
      <?= Csrf::field() ?>
      <div id="div_login row">
        <div class="container shadow p-3 col-7">
          <label for="txt_uname" class="m-2">Usuario</label>
          <input type="text" class="form-control border" id="txt_uname" name="username">
          <label for="txt_password" class="m-2">Contraseña</label>
          <input type="password" class="form-control border" id="txt_password" name="password">
          <div class="container row justify-content-center">
            <input type="submit" value="Ingresar" class="btn btn-primary m-4 col-5" name="but_submit" id="but_submit">
            <input type="reset" value="Borrar" class="btn btn-secondary m-4 col-5">
          </div>
        </div>
      </div>
    </form>
  </div>
</body>
</html>
