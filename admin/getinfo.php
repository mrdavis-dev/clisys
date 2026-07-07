<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin', 'recepcion']);
$pageTitle = 'Pagos — ClíSys';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body>
    <?php include 'partials/skip_nav.php'; ?>
    <div class="wrapper d-flex align-items-stretch">
        <?php
        include("menu.php");
        ?>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5 pt-5 ">
            <?php
            $breadcrumb = [
                ['label' => 'Inicio',       'url' => 'inicio.php'],
                ['label' => 'Registrar pago'],
            ];
            include 'partials/breadcrumb.php';
            ?>
            <div class=" animated fadeIn container-fluid centrar p-1 mb-3 border-bottom">
                <h1 class="page-title">Ingrese los datos</h1>
                <p class="text-dark">"Los datos ingresados son para calcular el ultimo saldo del paceinte en un tratamiento especifico "</p>
            </div>
            <form action="pagos.php" method="get" data-validate>
                <div class="container">
                    <div class="">

                        <div class="container text-center mt-5 p-2">

                            <label for="">Cédula del cliente</label>
                            <input type="text" required class="form-control border" autocomplete="off" name="cedula" id="ced">
                            <span id="search-spin" class="d-none ml-2 text-muted" aria-label="Buscando..."><i class="fa fa-spinner fa-spin"></i></span>

                            <div id="result"></div>

                            <!-- <label for="">Correo:</label>
                                <input type="email" required class="form-control border" name="email" id="email">

                                <label for="">Nombre y Apellido del cliente</label>
                                <input type="text" required class="form-control border" name="nombre" id="names_cli"> -->

                            <label for="">Tratamiento</label>
                            <input type="text" required class="form-control border" autocomplete="off" id="trata" name="trata">

                            <div class="mt-4">
                                <input class="btn btn-lg btn-block btn-primary mx-auto w-50" type="submit" name="next" value="Siguiente">
                            </div>
                        </div>


                    </div>
                </div>
            </form>
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <div class="container text-center mt-3 p-2">

                        </div>
                    </div>
                    <div class="col-4" style="padding-top: 16%;">

                    </div>
                </div>
            </div>

        </div>
    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script>
        $(function () {
            ajaxSearch({ url: 'get_info_pago.php', inputId: 'ced', resultId: 'result', spinId: 'search-spin', minLength: 5 });
        });
    </script>
</body>

</html>