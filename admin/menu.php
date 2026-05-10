<?php $__current = basename($_SERVER['PHP_SELF']); ?>
<div class="wrapper d-flex align-items-stretch">
		<nav id="sidebar" aria-label="Navegación principal">
			<div class="custom-menu">
				<button type="button" id="sidebarCollapse" class="btn btn-primary" aria-label="Alternar menú">
					<i class="fa fa-bars" aria-hidden="true"></i>
					<span class="sr-only">Toggle Menu</span>
      			</button>
        	</div>
			<div class="p-4">
				<img src="" alt="">
		  		<h1 style="font-size: calc(20px + 1.1vw);" class="border-bottom"><a href="index.php" class="logo">ClíSys</a></h1>
	       	<ul class="list-unstyled components mb-5">
				<li class="<?= $__current === 'inicio.php' ? 'active' : '' ?>">
					<a href="inicio.php"><span class="fa fa-home mr-3" aria-hidden="true"></span> Inicio</a>
				</li>
				<?php if (Auth::hasRole(['admin', 'medico'])): ?>
				<li class="<?= in_array($__current, ['pacientes.php', 'edit_paciente.php']) ? 'active' : '' ?>">
					<a href="pacientes.php"><span class="fa fa-user mr-3" aria-hidden="true"></span> Pacientes</a>
				</li>
				<?php endif; ?>
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

				if (Auth::hasRole(['admin', 'medico']) && Module::enabled('odontogram')): ?>
				<li class="<?= $__current === 'odontograma.php' ? 'active' : '' ?>">
					<a href="odontograma.php"><span class="fa fa-smile-o mr-3" aria-hidden="true"></span> Odontograma</a>
				</li>
				<?php endif; ?>

				<?php if (Auth::hasRole(['admin', 'medico']) && Module::enabled('clinical_notes')): ?>
				<li class="<?= $__current === 'notas.php' ? 'active' : '' ?>">
					<a href="notas.php"><span class="fa fa-file-text-o mr-3" aria-hidden="true"></span> Notas Clínicas</a>
				</li>
				<?php endif; ?>

				<?php if (Auth::hasRole(['admin', 'recepcion']) && Module::enabled('payments')): ?>
				<li class="<?= in_array($__current, ['getinfo.php', 'pagos.php']) ? 'active' : '' ?>">
					<a href="getinfo.php"><span class="fa fa-money mr-3" aria-hidden="true"></span> Pagos</a>
				</li>
				<?php endif; ?>

				<?php if (Auth::hasRole(['admin', 'recepcion']) && Module::enabled('history')): ?>
				<li class="<?= $__current === 'historial.php' ? 'active' : '' ?>">
					<a href="historial.php"><span class="fa fa-sticky-note mr-3" aria-hidden="true"></span> Historial</a>
				</li>
				<?php endif; ?>

				<?php if (Auth::hasRole(['admin'])): ?>
				<li class="<?= $__current === 'registro_user.php' ? 'active' : '' ?>">
					<a href="registro_user.php"><span class="fa fa-users mr-3" aria-hidden="true"></span> Administradores</a>
				</li>
				<li class="<?= $__current === 'audit.php' ? 'active' : '' ?>">
					<a href="audit.php"><span class="fa fa-shield mr-3" aria-hidden="true"></span> Auditoría</a>
				</li>
				<?php endif; ?>
				<?php if (Auth::isSuperAdmin()): ?>
				<li>
					<a href="superadmin/index.php"><span class="fa fa-cog mr-3" aria-hidden="true"></span> Super Admin</a>
				</li>
				<?php endif; ?>
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
<?php include __DIR__ . '/partials/confirm_modal.php'; ?>
