<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin', 'recepcion']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagos</title>
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
    </div>
    <script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
    <script>
        $(function () {
            ajaxSearch({ url: 'get_info_pago.php', inputId: 'ced', resultId: 'result', spinId: 'search-spin', minLength: 5 });
        });
    </script>
</body>

</html>