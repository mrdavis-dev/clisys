<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>pacientes</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

	<link rel="stylesheet" href="css/main.css">
	
    <link rel="stylesheet" href="css/layout.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
	<?php
	include('partials/skip_nav.php'); ?>
<?php include("menu.php");
	?>

	<!-- Page Content  -->
	<div id="content" class="p-4 p-md-5 pt-5 ">
		<div class=" animated fadeIn container centrar d-flex justify-content-between align-items-center border-bottom pb-2">
			<h1 class="page-title mb-0">Pacientes</h1>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Mymodal-1">
				<i class="fa fa-user-plus mr-1"></i> Agregar paciente
			</button>
		</div>
		<div class="container-fluid">
			<div class="row  mb-3">
				<div class="col-12 container centrar pt-3 row">
					<div class="col-2">
						<span class="btn-block btn btn-secondary disabled align-middle w-100">Buscar</span>
					</div>
					<div class="col-10">
						<input class="form-control border" type="text" name="search" id="search_text" autocomplete="off" placeholder="Buscar por nombre o cédula">
						<span id="search-spin" class="d-none ml-2 text-muted" aria-label="Cargando..."><i class="fa fa-spinner fa-spin"></i></span>
					</div>
				</div>
			</div>

			<!-- Hidden delete form (submitted by inline row buttons) -->
			<form method="post" action="pacientes.php" id="delete-form" class="d-none">
				<?= Csrf::field() ?>
				<input type="hidden" name="emp_id" id="emp_id">
				<input type="hidden" name="delete" value="1">
			</form>

			<?php
			if (isset($_POST['delete'])) {
				Csrf::verify();
				$clinic_id = Tenant::id();
				$stmt = $db->prepare('DELETE FROM pacientes WHERE id = ? AND clinic_id = ?');
				$stmt->bind_param('ii', $_POST['emp_id'], $clinic_id);
				$stmt->execute();
				$stmt->close();
			}
			?>

			<script>
				function confirmDelete(id) {
					if (!confirm('¿Eliminar este paciente? Esta acción no se puede deshacer.')) return;
					document.getElementById('emp_id').value = id;
					document.getElementById('delete-form').submit();
				}
			</script>

			<div class="mt-3" id="result"></div>
		</div>
		<script>
			$(function () {
				ajaxSearch({ url: 'fetch.php', inputId: 'search_text', resultId: 'result', spinId: 'search-spin' });
			});
		</script>
		<!-- MODAL AGREGAR PACIENTE -->
		<div class="modal fade bg-dark" id="Mymodal-1" tabindex="-1" role="dialog" aria-labelledby="modalPacienteLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
			  <div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="modalPacienteLabel">Agregar paciente</h5>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<form method="POST" action="insert_paciente.php">
				<?= Csrf::field() ?>
				<input type="hidden" name="redirect_to" value="pacientes.php">
				<div class="modal-body">
				  <div class="container">
					<label>Nombre</label>
					<input type="text" class="mb-1 form-control border border-primary" name="nombre">
					<label>Apellido</label>
					<input type="text" class="mb-1 form-control border border-primary" name="apellido">
					<label>Cedula</label>
					<input type="text" class="mb-1 form-control border border-primary" name="cedula">
					<label>Direccion</label>
					<input type="text" class="mb-1 form-control border border-primary" name="direccion">
					<label>Telefono</label>
					<input type="text" class="mb-1 form-control border border-primary" name="telefono">
					<label>Email</label>
					<input type="text" class="mb-1 form-control border border-primary" name="email">
					<label>Ocupacion</label>
					<input type="text" class="mb-1 form-control border border-primary" name="ocupacion">
					<label>Edad</label>
					<input type="text" class="mb-1 form-control border border-primary" name="edad">
				  </div>
				  <div class="container">
					<label>Motivo de la visita</label>
					<textarea class="m-1 form-control border border-primary" name="motivo" cols="40" rows="5"></textarea>
					<label>Habitos higienicos</label>
					<textarea class="m-1 form-control border border-primary" name="habitos" cols="40" rows="3"></textarea>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Está bajo tratamiento médico actualmente?</h5></legend>
					<select class="form-control border" name="bajotratamiento"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Ha sido hospitalizado quirúrgicamente?</h5></legend>
					<select class="form-control border" name="quirurgicamente"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Esta tomando algún medicamento o droga?</h5></legend>
					<select class="form-control border" name="droga"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Presenta algún tipo de alergia?</h5></legend>
					<select class="form-control border" name="alergia"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Ha tenido algún tipo de enfermedad cardiaca?</h5></legend>
					<select class="form-control border" name="cardiaca"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Es usted diabético o alguno de sus familiares la padece o padeció?</h5></legend>
					<select class="form-control border" name="diabético"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Ha tenido tubérculosis o hepatitis?</h5></legend>
					<select class="form-control border" name="hepatitis"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Ha presentado alteraciones en el sangrado?</h5></legend>
					<select class="form-control border" name="sangrado"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Ha tenido algúna enfermedad de transmisión sexual?</h5></legend>
					<select class="form-control border" name="transmision"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
				  </div>
				  <div class="container m-1 p-1">
					<legend><h5>¿Tiene algún tipo de mal hábito?</h5></legend>
					<select class="form-control border" name="habito"><option value="">...</option><option value="si">Si</option><option value="no">No</option></select>
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
		<script src="js/main.js"></script>
</body>

</html>