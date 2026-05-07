<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/style.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Detalles</title>
</head>

<body>
    <?php
    include("menu.php");
    ?>

    <div id="content" class="p-4 p-md-5 pt-5 ">
        <div class=" animated fadeIn container centrar">
            <h2 class="display-3 border-bottom">Información de paciente</h2>

            <div class="container mt-5">
                <div class="text-start">
                    <?php
                    $id        = (int)($_GET['id'] ?? 0);
                    $clinic_id = Tenant::id();
                    $stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ? AND clinic_id = ?');
                    $stmt->bind_param('ii', $id, $clinic_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                            <form method="post" action="functions/edit_paciente.php">
                            <?= Csrf::field() ?>
                            <label for="id_nombre">Nombre</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="nombre" value="<?= h($row['nombre']) ?>">

                            <label for="id_apellido">Apellido</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="apellido" value="<?= h($row['apellido']) ?>">

                            <label for="id_cedula">Cedula</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="cedula" value="<?= h($row['cedula']) ?>">

                            <label for="id_direccion">Direccion</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="direccion" value="<?= h($row['direccion']) ?>">

                            <label for="id_telefono">Telefono</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="telefono" value="<?= h($row['telefono']) ?>">

                            <label for="id_email">Email</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="email" value="<?= h($row['email']) ?>">

                            <label for="id_ocupacion">Ocupacion</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="ocupacion" value="<?= h($row['ocupacion']) ?>">

                            <label for="id_edad">Edad</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="edad" value="<?= h($row['edad']) ?>">

                            <input type="hidden" name="id" value="<?= h((string)$row['id']) ?>">

                            <div class="text-center mt-4">
                                <input type="submit" name="update" class="btn btn-success" value="actualizar">
                            </div>
                            </form>
                        <?php }
                    } else {
                        echo '<p>Paciente no encontrado.</p>';
                    }
                    $stmt->close();
                    ?>
                </div>
            </div>
        </div>

</body>

</html>
<script src="js/main.js"></script>