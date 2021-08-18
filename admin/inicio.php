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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inicio</title>
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

        <!-- Page Content  -->
      <div id="content" class="p-4 p-md-5 pt-5 ">
			<div class="container">
						<div class="container centrado">
							<div class="centrar animated fadeIn slow container border-bottom  m-2">

								<p class="display-4"><img src="img/logo-color.png" style="width: 50px"> Clínica Anguizola </p>

							</div>
							<div class="centrar">
								<button type="button" class="sombra m-3 btn btn-primary btn-lg" data-toggle="modal" data-target="#Mymodal-1">
									Agregar paciente
								</button>
								<button type="button" class="sombra m-3 btn btn-primary btn-lg" data-toggle="modal" data-target="#Mymodal-2">
									Agregar cita
								</button>
							</div>
						</div>
						<div class="container m-1 centrar">
							<h1 class="p-1 ">Próximas citas</h1>
							<div class="container-fluid centrar" >
							<?php
							$dbc = mysqli_connect("172.17.0.7", "root", "#dbcontainer.com", "anguizola")
							or die('Error connecting to MySQL server');
							$query = "select * from citas_tabla";
							$result = mysqli_query($dbc,$query)
							or die('Error querying database');

							$count=mysqli_num_rows($result);
							?>

              <td><form name="form1" method="post" action="">
              <div class="container " style="overflow-y: scroll; height: 25rem; display: block;">
                  <table class="table" >
                      <thead>
                        <tr>
                          <td scope="col"></td>
            							<td scope="col"><strong>Fecha</strong></td>
            							<td scope="col"><strong>Hora</strong></td>
            							<td scope="col"><strong>Nombre</strong></td>
            							<td scope="col"><strong>Asunto</strong></td>
                          <td scope="col"><strong>Doctor</strong></td>
                        </tr>
                        <?php

          							while ($row=mysqli_fetch_array($result)) {
          							?>

          							<tr>
          							<td align="center" ><input name="checkbox[]" type="checkbox" value="<?php echo $row['id']; ?>"></td>
          							<td ><?php echo $row['fecha_de_cita']; ?></td>
          							<td ><?php echo $row['hora_de_cita']; ?></td>
          							<td ><?php echo $row['nombre_paciente']; ?></td>
          							<td ><?php echo $row['asunto_de_la_cita']; ?></td>
                        <td ><?php echo $row['doctor']; ?></td>
          							</tr>

          							<?php
          							}
          							?>

                    </table>

              </div>
                <input class="btn btn-secondary" name="delete" type="submit" value="Borrar">

							<?php

							// Check if delete button active, start this

							if(isset($_POST['delete']))
							{
								$checkbox = $_POST['checkbox'];

							for($i=0;$i<count($checkbox);$i++){

							$del_id = $checkbox[$i];
							$sql = "DELETE FROM citas_tabla WHERE id='$del_id'";
							$result = mysqli_query($db,$sql);
							}
							// if successful redirect to delete_multiple.php
							if($result){
							echo "<meta http-equiv=\"refresh\" content=\"0;URL=inicio.php\">";
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

			<!-- CONTENIDO DEL MODAL PACIENTES -->
			<div class="modal fade bg-dark" id="Mymodal-1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				  <div class="modal-content">
					<div class="modal-header">
					  <h5 class="modal-title" id="exampleModalLabel">Agregar paciente</h5>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					</div>
					<form method="POST" action="insert_paciente.php">
					<div class="modal-body ">
					  <div class="container">
						<label for="id_nombre">Nombre</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="Nombre" name="nombre">

						<label for="id_apellido">Apellido</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="apellido">

						<label for="id_cedula">Cedula</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="cedula">

						<label for="id_direccion">Direccion</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="direccion">

						<label for="id_telefono">Telefono</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="telefono">

						<label for="id_email">Email</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="email">

						<label for="id_ocupacion">Ocupacion</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="ocupacion">

						<label for="id_edad">Edad</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="edad">
					  </div>

					  <div class="container">
						<label for="id_motivo">Motivo de la visita</label>
						<textarea class="m-1 form-control border border-primary" name="motivo" cols="40" rows="10"></textarea>

						<label for="id_habitos">Habitos higienicos</label>
						<textarea class="m-1 form-control border border-primary" name="habitos" cols="40" rows="10"></textarea>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Está bajo tratamiento médico actualmente?</h5></legend>
						<select id="q-1" class="form-control border" name="bajotratamiento">
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Ha sido hospitalizado quirúrgicamente?</h5></legend>
						<select id="q-2" class="form-control border" name="quirurgicamente">
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Esta tomando algún medicamento o droga?</h5></legend>
						<select id="q-3" class="form-control border" name="droga" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Presenta algún tipo de alergia?</h5></legend>
						<select id="q-4" class="form-control border" name="alergia" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Ha tenido algún tipo de enfermedad cardiaca?</h5></legend>
						<select id="q-5" class="form-control border" name="cardiaca" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Es usted diabético o alguno de sus familiares la padece o padeció?</h5></legend>
						<select id="q-6" class="form-control border" name="diabético" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Ha tenido tubérculosis o hepatitis?</h5></legend>
						<select id="q-7" class="form-control border" name="hepatitis" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Ha presentado alteraciones en el sangrado?</h5></legend>
						<select id="q-8" class="form-control border" name="sangrado" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Ha tenido algúna enfermedad de transmisión sexual?</h5></legend>
						<select id="q-9" class="form-control border" name="transmision" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					  <div class="container m-1 p-1">
						<legend><h5>¿Tiene algún tipo de mal hábito?</h5></legend>
						<select id="q-10" class="form-control border" name="habito" >
							<option value="">...</option>
							<option value="si">Si</option>
							<option value="no">No</option>
						</select>
					  </div>

					</div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					  <button type="reset" class="btn" style="background-color:var(--color-3)">Borrar</button>
					  <input type="submit" class="btn btn-primary" name="submit" value="Guardar">
					</div>
					</form>
				  </div>
				</div>
			  </div>



			  <!-- MODAL DE CITAS -->
			  <div class="modal fade bg-dark" id="Mymodal-2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				  <div class="modal-content">
					<div class="modal-header">
					  <h5 class="modal-title" id="exampleModalLabel">Agregar cita</h5>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					</div>

					<form action="insert.php" method="POST">
					<div class="modal-body ">
					  <div class="container">
						<label for="fecha">Fecha de cita</label>
						<input type="date" class="mb-1 form-control border border-primary" placeholder="" name="fecha">

						<label for="hora">Hora de cita</label>
						<input type="time" class="mb-1 form-control border border-primary" placeholder="" name="hora">

						<label for="nombre">Nombre de paciente</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="nombre">

						<label for="asunto">Asunto de la cita</label>
						<input type="text" class="mb-1 form-control border border-primary" placeholder="" name="asunto">

            <label for="">Doctor de preferencia</label>
            <select name="doctor" required class="form-control" id="">
                <option value="">Seleccione un Doctor...</option>
                <option value="Dr. Júlio Anguizola Vial">Dr. Júlio Anguizola Vial</option>
                <option value="Dr. Miguel Anguizola Severino">Dr. Miguel Anguizola Severino</option>
                <option value="Dr. Amira Martínez de Anguizola">Dr. Amira Martínez de Anguizola</option>
            </select>
					  </div>
					</div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					  <button type="reset" class="btn" style="background-color:var(--color-3)">Borrar</button>
            			<input type="submit" class="btn btn-primary" name="save" value="Guardar">
					</div>
					</form>
				  </div>
				</div>
			  </div>
      </div>
	</div>
	<script src="js/main.js"></script>
</body>
</html>
