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
    <title>Pagos</title>
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
            <div class=" animated fadeIn container-fluid centrar p-1 mb-3 border-bottom">
                <h1 class="display-3">Pagos</h1>
            </div>
            <form action="insert_pagos_send.php" method="post">
                <div class="container">
                    <div class="row">
                        <div class="col-8">
                            <div class="container text-center mt-3 p-2">
                                <label for="">Fecha</label>
                                <input type="date" required class="form-control border" name="fecha" id="dia">

                                <?php
                                echo '
                                <label for="">Cédula del cliente</label>
                                <input type="text" required class="form-control border" autocomplete="off" value=' . $_GET["cedula"] . ' name="cedula" id="ced">

                                <label for="">Correo:</label>
                                <input type="email" readonly="readonly" required class="form-control border" value=' . $_GET["email"] . ' name="email" id="email">

                                <label for="">Nombre del cliente</label>
                                <input type="text" readonly="readonly" required class="form-control border" value=' . $_GET["nombre"] . ' name="nombre" id="names_cli">

                                <label for="">Apellido del cliente</label>
                                <input type="text" readonly="readonly" required class="form-control border" value=' . $_GET["apellido"] . ' name="apellido" id="names_apellido">

                                <label for="">Tipo de pago</label>
                                <select name="tipo_pago" required class="form-control" id="">
                                    <option value="">Seleccione alguno...</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Tarjeta">Transferencia</option>
                                </select>

                                

                                <label for="">Tratamiento</label>
                                <input type="text" required class="form-control border" autocomplete="off" value=' . $_GET["trata"] . ' id="trata" name="trata">
                                    ';

                                include("functions/funsaldo.php");


                                ?>

                                <!-- <input type="text" required class="form-control border" autocomplete="off" name="saldo"> -->

                                <label for="">Cantidad a pagar B/:</label>
                                <input type="text" required class="form-control border" autocomplete="off" name="cantidad">



                                <label for="">Nota</label><br>
                                <textarea class="w-100" name="nota" id="" cols="50" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-4" style="padding-top: 16%;">
                            <input class="btn btn-block btn-lg btn-primary align-middle" type="submit" name="enviar" value="Enviar factura">
                            <button type="reset" class="btn btn-secondary btn-lg btn-block ">Borrar campos</button>
                            <input class="btn btn-lg btn-block btn-primary" type="submit" name="print" value="Imprimir factura">
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
    <script src="js/main.js"></script>
    <!-- <script>
        $(document).ready(function() {

            load_data();

            function load_data(query) {
                $.ajax({
                    url: "get_info_pago.php",
                    method: "POST",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#result').html(data);
                    }
                });
            }
            $('#ced').keyup(function() {
                var search = $(this).val();
                if (search != '') {
                    load_data(search);
                } else {
                    load_data();
                }
            });


        });
    </script> -->
</body>

</html>