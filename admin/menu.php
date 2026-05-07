<div class="wrapper d-flex align-items-stretch">
		<nav id="sidebar" class="">
			<div class="custom-menu">
				<button type="button" id="sidebarCollapse" class="btn btn-primary">
					<i class="fa fa-bars"></i>
					<span class="sr-only">Toggle Menu</span>
      			</button>
        	</div>
			<div class="p-4">
				<img src="" alt="">
		  		<h1 style="font-size: calc(20px + 1.1vw);" class="border-bottom"><a href="index.php" class="logo">Clínica Anguizola</a></h1>
	       	<ul class="list-unstyled components mb-5">
				<li>
					<a href="inicio.php"><span class="fa fa-home mr-3"></span> Inicio</a>
				</li>
				<li>
					<a href="pacientes.php"><span class="fa fa-user mr-3"></span> Pacientes</a>
				</li>
				<?php
				// Load module system if not already loaded
				if (!class_exists('Module')) {
					require_once __DIR__ . '/core/Module.php';
				}
				if (!class_exists('Database')) {
					require_once __DIR__ . '/core/Database.php';
				}
				if (!class_exists('Tenant')) {
					require_once __DIR__ . '/core/Tenant.php';
				}
				// Ensure conexion/config loaded (provides $db + Tenant::load)
				if (!class_exists('Database') || !Tenant::resolved()) {
					require_once __DIR__ . '/conexion/config.php';
				}

				if (Module::enabled('odontogram')): ?>
				<li>
					<a href="odontograma.php"><span class="fa fa-smile-o mr-3"></span> Odontograma</a>
				</li>
				<?php endif; ?>

				<?php if (Module::enabled('clinical_notes')): ?>
				<li>
					<a href="notas.php"><span class="fa fa-file-text-o mr-3"></span> Notas Clínicas</a>
				</li>
				<?php endif; ?>

				<?php if (Module::enabled('payments')): ?>
				<li>
					<a href="getinfo.php"><span class="fa fa-money mr-3"></span> Pagos</a>
				</li>
				<?php endif; ?>

				<?php if (Module::enabled('history')): ?>
				<li>
					<a href="historial.php"><span class="fa fa-sticky-note mr-3"></span> Historial</a>
				</li>
				<?php endif; ?>

				<li>
					<a href="registro_user.php"><span class="fa fa-users mr-3"></span> Administradores</a>
				</li>
				<li>
					<a href="audit.php"><span class="fa fa-shield mr-3"></span> Auditoría</a>
				</li>
				<li>
				<?php
				if (!class_exists('Csrf')) { require_once __DIR__ . '/core/Csrf.php'; }

				if (isset($_POST['but_logout'])) {
					session_unset();
					session_destroy();
					header('Location: index.php');
					exit;
				}
				?>
				<form method='post' action="">
					<?= Csrf::field() ?>
					<input style="margin-top:25px" class="btn btn-danger" type="submit" value="Cerrar Sesion" name="but_logout">
				</form>
				</li>
	        </ul>
	      </div>
    	</nav>
