<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/index.css">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <title>Crear Usuario</title>
</head>

<body class="fondo" style="font-family: 'Montserrat', sans-serif;">
  <div class="container" style=" scroll-behavior: smooth;">
    <!-- logo -->
    <div class="text-center text-light">
      <img src="../img/PPS_LOGO_WHITE_.png" class="img-fluid" style="width: 40%;" alt="Responsive image">
    </div>

    <!-- register form -->
    <div class="text-center mt-3 text-white w-75 mx-auto">
      <h5 class="mb-4 font-weight-bold">Crear nueva cuenta</h5>

      <form action="functions/register.php" method="POST">

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

        <input style="margin-bottom: 80px; background-color: #FF9900; color:#fff" name="submit" class="btn" type="submit" value="Registrarme">

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
      document.getElementById('message').style.color = '#ff9900';
      document.getElementById('message').innerHTML = 'La contraseña no coincide';
    }
  }
</script>