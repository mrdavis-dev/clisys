<?php
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
  header('Location: login.php');
  exit;

  $dni = $_SESSION['id'];
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta charset="utf-8">
  <title>administradores</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/main.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

  <div class="wrapper d-flex align-items-stretch">
    <?php
    include("menu.php");
    ?>

    <div id="content" class="p-4 p-md-5 pt-5 ">
      <div class="container">

        <h2>Agregar usuario</h2>
        <form method="POST" action="insert_user.php">

          <label for="" class="mt-3">Usuario</label>
          <input type="text" class="form-control border" name="usuario" id="">

          <label for="" class="mt-2">Nombre</label>
          <input type="text" class="form-control border" name="nombre" id="">

          <label for="" class="mt-2">Contraseña</label>
          <input type="password" class="form-control border" name="pass">

          <input class="btn btn-primary mt-3" type="submit" name="submit" value="Guardar">
        </form>

        <div class="container m-1 centrar">
          <h2 class="p-1 ">Usuarios</h2>
          <div class="container-fluid centrar">
            <?php
            // include("conexion/config.php");
            $query = "SELECT id, username, name FROM users";
            $result = mysqli_query($db, $query)
              or die('Error querying database');

            $count = mysqli_num_rows($result);
            ?>

            <td>
              <form name="form1" method="post" action="">
                <div class="container " style="overflow-y: scroll; height: 25rem; display: block;">
                  <table class="table">
                    <thead>
                      <tr>
                        <td scope="col">#</td>
                        <td scope="col"><strong>usuario</strong></td>
                        <td scope="col"><strong>nombre</strong></td>
                        <!-- <td scope="col"><strong>Nombre</strong></td>
                        <td scope="col"><strong>Asunto</strong></td> -->
                      </tr>
                      <?php

                      while ($row = mysqli_fetch_array($result)) {
                      ?>

                        <tr>
                          <td align="center"><input name="checkbox[]" type="checkbox" value="<?php echo $row['id']; ?>"></td>
                          <td><?php echo $row['username']; ?></td>
                          <td><?php echo $row['name']; ?></td>

                        </tr>

                      <?php
                      }
                      ?>

                  </table>

                </div>
                <input class="btn btn-secondary" name="delete" type="submit" value="Borrar">

                <?php

                // Check if delete button active, start this

                if (isset($_POST['delete'])) {
                  $checkbox = $_POST['checkbox'];

                  for ($i = 0; $i < count($checkbox); $i++) {

                    $del_id = $checkbox[$i];
                    $sql = "DELETE FROM users WHERE id = '$del_id'";
                    $result = mysqli_query($db, $sql);
                  }
                  // if successful redirect to delete_multiple.php
                  if ($result) {
                    echo "<meta http-equiv=\"refresh\" content=\"0;URL=registro_user.php\">";
                  }
                }

                mysqli_close($db);

                ?>

                </table>
              </form>
            </td>
            </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>