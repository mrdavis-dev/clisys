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
                    // error_reporting(0);
                    $id = $_GET["id"];
                    $select = "SELECT * from pacientes WHERE id = $id";
                    $result = $db->query($select);
                    
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo '

                            <form method="post" action="functions/edit_paciente.php">
                            <label for="id_nombre">Nombre</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="nombre" value="' . $row["nombre"] . '">
    
                            <label for="id_apellido">Apellido</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="apellido" value="' . $row["apellido"] . '">
    
                            <label for="id_cedula">Cedula</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="cedula" value="' . $row["cedula"] . '">
    
                            <label for="id_direccion">Direccion</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="direccion" value="' . $row["direccion"] . '">
    
                            <label for="id_telefono">Telefono</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="telefono" value="' . $row["telefono"] . '">
    
                            <label for="id_email">Email</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="email" value="' . $row["email"] . '">
    
                            <label for="id_ocupacion">Ocupacion</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="ocupacion" value="' . $row["ocuapacion"] . '">
    
                            <label for="id_edad">Edad</label>
                            <input type="text" class="mb-1 form-control border border-primary" name="edad" value="' . $row["edad"] . '">
                           
                            <input type="text" class="mb-1 form-control border border-primary" style="display:none" name="id" value="' . $row["id"] . '">
                            
                            <div class="text-center mt-4">
                                <input type="submit" name="update" class="btn btn-success" value="actualizar">
                            </div>
                            </form>
                            ';
                        }
                    } else {
                        echo "0 results";
                    }

                    ?>
                </div>
            </div>
        </div>

</body>

</html>
<script src="js/main.js"></script>