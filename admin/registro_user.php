<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin']);
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
  
    <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/main.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

  <?php include 'partials/skip_nav.php'; ?>
    <div class="wrapper d-flex align-items-stretch">
    <?php
    include("menu.php");
    ?>

    <div id="content" class="p-4 p-md-5 pt-5 ">
      <div class="container">

        <h2>Agregar usuario</h2>
        <form method="POST" action="insert_user.php">
          <?= Csrf::field() ?>

          <label for="" class="mt-3">Usuario</label>
          <input type="text" class="form-control border" name="usuario" id="">

          <label for="" class="mt-2">Nombre</label>
          <input type="text" class="form-control border" name="nombre" id="">

          <label for="" class="mt-2">Contraseña</label>
          <input type="password" class="form-control border" name="pass">

          <label for="id_role" class="mt-2">Rol</label>
          <select name="role" id="id_role" class="form-control border">
            <option value="admin">Admin</option>
            <option value="recepcion">Recepción</option>
            <option value="medico">Médico</option>
          </select>

          <input class="btn btn-primary mt-3" type="submit" name="submit" value="Guardar">
        </form>

        <div class="container m-1 centrar">
          <h2 class="p-1 ">Usuarios</h2>
          <div class="container-fluid centrar">
            <?php
            // include("conexion/config.php");
            $clinic_id = Tenant::id();
            $stmt_users = $db->prepare("SELECT id, username, name, role FROM users WHERE clinic_id = ? AND role != 'superadmin'");
            $stmt_users->bind_param('i', $clinic_id);
            $stmt_users->execute();
            $result = $stmt_users->get_result();

            $count = mysqli_num_rows($result);
            ?>

            <td>
              <form name="form1" id="form-delete-users" method="post" action="registro_user.php">
                <?= Csrf::field() ?>
                <div class="container table-scroll">
                  <table class="table">
                    <thead>
                      <tr>
                        <td scope="col">#</td>
                        <td scope="col"><strong>usuario</strong></td>
                        <td scope="col"><strong>nombre</strong></td>
                        <td scope="col"><strong>rol</strong></td>
                      </tr>
                      <?php

                      while ($row = $result->fetch_assoc()) {
                      ?>

                        <tr>
                          <td align="center"><input name="checkbox[]" type="checkbox" value="<?= h((string)$row['id']) ?>"></td>
                          <td><?= h($row['username']) ?></td>
                          <td><?= h($row['name']) ?></td>
                          <td><?= h($row['role']) ?></td>
                        </tr>

                      <?php
                      }
                      ?>

                  </table>

                </div>
                <button type="button" class="btn btn-secondary" data-confirm="true" data-confirm-form="#form-delete-users" data-confirm-msg="¿Borrar los usuarios seleccionados?">Borrar</button>

                <?php

                if (isset($_POST['delete']) && !empty($_POST['checkbox'])) {
                  Csrf::verify();
                  $clinic_id_del = Tenant::id();
                  $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND clinic_id = ? AND role != 'superadmin'");
                  foreach ($_POST['checkbox'] as $del_id) {
                    $id_int = (int)$del_id;
                    $stmt->bind_param('ii', $id_int, $clinic_id_del);
                    $stmt->execute();
                  }
                  $stmt->close();
                  echo "<meta http-equiv=\"refresh\" content=\"0;URL=registro_user.php\">";
                }

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