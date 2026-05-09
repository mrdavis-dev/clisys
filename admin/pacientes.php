<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
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
	<link rel="stylesheet" href="css/style.css">
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
		<div class=" animated fadeIn container centrar">
			<h1 class="page-title border-bottom pb-2">Pacientes</h1>
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
		<script src="js/main.js"></script>
</body>

</html>