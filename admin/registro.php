<?php
require_once __DIR__ . '/core/Csrf.php';
$pageTitle = 'Crear Usuario — ClíSys';
?>
<!DOCTYPE html>
<html lang="es">

<head>
<?php include __DIR__ . '/partials/auth_head.php'; ?>
<link rel="stylesheet" href="../style/index.css">
</head>

<body class="fondo">
  <div class="container" style=" scroll-behavior: smooth;">

    <!-- register form -->
    <div class="text-center mt-3 text-white w-75 mx-auto">
      <h5 class="mb-4 font-weight-bold">Crear nueva cuenta</h5>

      <form action="functions/register.php" method="POST">
        <?= Csrf::field() ?>

        <div class="mb-4 ">
          <label style="float:left" for="">Nombre:</label>
          <input type="text" class="form-control text-center" autocomplete="" name="nombre" placeholder="Nombre" required>
        </div>

        <div class="mb-4 ">
          <label style="float:left" for="">Usuario:</label>
          <input type="text" class="form-control text-center" autocomplete="" name="user" placeholder="usuario" required>
        </div>

        <div class="mb-4 ">
          <label style="float:left" for="">Contraseña:</label>
          <input type="password" class="form-control text-center" autocomplete="of" id="pass_new" onkeyup="check();" name="password" placeholder="Contraseña" required>
        </div>

        <div class="form-group">
          <label style="float:left" for="">Repetir Contraseña:</label>
          <input class="form-control text-center" type="password" name="pass_rep" id="pass_rep" onkeyup="check();" placeholder="Repetir Contraseña">
          <span class="font-weight-bold" id="message"></span><br>
        </div>

        <input style="margin-bottom: 80px;" name="submit" class="btn btn-primary" type="submit" value="Registrarme">

      </form>

    </div>




  </div>
 

</body>

</html>
<script>
  function check(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
    if (tecla == 8) {
      return true;
    }
    // Patron de entrada, en este caso solo acepta numeros y letras
    patron = /[A-Za-z0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  }

  function checknum(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
    if (tecla == 8) {
      return true;
    }
    // Patron de entrada, en este caso solo acepta numeros y letras
    patron = /[A-Za-z0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  }

  // verificar contraseña
  var check = function() {
    if (document.getElementById('pass_new').value ==
      document.getElementById('pass_rep').value) {
      document.getElementById('message').style.color = 'green';
      document.getElementById('message').innerHTML = 'coincidencia de 100%';
    } else {
      document.getElementById('message').style.color = '#dc2626';
      document.getElementById('message').innerHTML = 'La contraseña no coincide';
    }
  }
</script>