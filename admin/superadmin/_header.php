<?php
if (!class_exists('Csrf')) { require_once __DIR__ . '/../core/Csrf.php'; }
if (!function_exists('h')) { require_once __DIR__ . '/../core/Auth.php'; }

if (isset($_POST['but_logout'])) {
    Csrf::verify();
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$__sa_current = basename($_SERVER['PHP_SELF']);
?>
<div class="wrapper d-flex align-items-stretch">
    <nav id="sidebar" aria-label="Navegación Super Admin">
        <div class="custom-menu">
            <button type="button" id="sidebarCollapse" class="btn btn-primary" aria-label="Alternar menú">
                <i class="fa fa-bars" aria-hidden="true"></i>
                <span class="sr-only">Toggle Menu</span>
            </button>
        </div>
        <div class="p-4">
            <h1 style="font-size: calc(16px + 0.8vw);" class="border-bottom">
                <a href="index.php" class="logo">ClíSys <small class="text-muted d-block" style="font-size:0.5em;">Super Admin</small></a>
            </h1>
            <ul class="list-unstyled components mb-5">
                <li class="<?= $__sa_current === 'index.php'   ? 'active' : '' ?>">
                    <a href="index.php"><span class="fa fa-dashboard mr-3" aria-hidden="true"></span> Dashboard</a>
                </li>
                <li class="<?= $__sa_current === 'clinics.php' ? 'active' : '' ?>">
                    <a href="clinics.php"><span class="fa fa-hospital-o mr-3" aria-hidden="true"></span> Clínicas</a>
                </li>
                <li class="<?= $__sa_current === 'modules.php' ? 'active' : '' ?>">
                    <a href="modules.php"><span class="fa fa-puzzle-piece mr-3" aria-hidden="true"></span> Módulos</a>
                </li>
                <li class="<?= $__sa_current === 'plans.php'   ? 'active' : '' ?>">
                    <a href="plans.php"><span class="fa fa-list mr-3" aria-hidden="true"></span> Planes</a>
                </li>
                <li class="<?= $__sa_current === 'clinic_users.php' ? 'active' : '' ?>">
                    <a href="clinic_users.php"><span class="fa fa-users mr-3" aria-hidden="true"></span> Usuarios</a>
                </li>
                <li>
                    <form method="post" action="">
                        <?= Csrf::field() ?>
                        <input style="margin-top:25px" class="btn btn-danger" type="submit" value="Cerrar Sesión" name="but_logout">
                    </form>
                </li>
            </ul>
        </div>
    </nav>
