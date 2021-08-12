<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
  <div class="container animated fadeIn">
    <div class=" animated fadeIn container-fluid centrar p-1 mb-3 mt-4 border-bottom">
      <h1 class="display-4">Inicio de sesion</h1>
    </div>
  </div>
  <div class="container animated fadeIn delay-1s">
    <form method="POST" action="functions/login.php">
      <div id="div_login row">
        <div class="container shadow p-3 col-7">
          <legend class="m-2">Usuario</legend>
          <input type="text" class="form-control border" id="txt_uname" name="username">
          <legend class="m-2">Contraseña</legend>
          <input type="password" class="form-control border" id="txt_uname" name="password">
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
