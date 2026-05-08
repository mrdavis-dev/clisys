<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historial</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
	<link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/layout.css">
	<link rel="stylesheet" href="css/main.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('partials/flash.php'); ?>
<?php include('menu.php'); ?>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5 pt-5 ">
          <div class="row border-bottom mb-3">
            <div class=" col-md-7 col-sm-12 animated fadeIn container centrar  ">
                <h1 class="page-title">Historial</h1>
            </div>
            <div class="col-md-5 col-sm-12 container centrar pt-3 row">
              <div class="col-10">
                <input class="form-control border" type="text" name="search_text" id="search_text" placeholder="Buscar por cédula o nombre..." autocomplete="off">
                <span id="search-spin" class="d-none ml-2 text-muted" aria-label="Cargando..."><i class="fa fa-spinner fa-spin"></i></span>
              </div>
              <div class="col-2">
                <a href="#" class=" btn btn-primary align-middle " ><i class="fa fa-search"></i></a>
              </div>
            </div>
            <div class="">

                     <?php
             if (isset($_POST['delete'])) {
                Csrf::verify();
                $clinic_id = Tenant::id();
                $stmt = $db->prepare('DELETE FROM pago WHERE id = ? AND clinic_id = ?');
                $stmt->bind_param('ii', $_POST['emp_id'], $clinic_id);
                $stmt->execute();
                $stmt->close();
             } else {
            ?>
               <form method="post" action="historial.php">
               <?= Csrf::field() ?>
                  <table width = "400" border = "0" cellspacing = "1"
                     cellpadding = "2">

                     <tr>
                        <td width = "100">Paciente ID</td>
                        <td><input class="form-control border" name = "emp_id" type = "text" id = "emp_id"></td>
                     </tr>

                     <tr>
                        <td width = "100"> </td>
                        <td> </td>
                     </tr>

                     <tr>
                        <td width = "100"> </td>
                        <td>
                           <input class="btn btn-primary" name = "delete" type = "submit" id = "delete" value = "Delete">
                        </td>
                     </tr>

                  </table>
               </form>
            <?php } ?>

            </div>
          </div>
          <div class="container-fluid">

    			<div id="result">

    			</div>


          <script>
            $(function () {
                ajaxSearch({ url: 'viewhistorial.php', inputId: 'search_text', resultId: 'result', spinId: 'search-spin' });
            });
          </script>

            <div class="container p-1 mt-3 text-center">
              <input class="btn btn-lg btn-primary align-middle" type="submit" name="" onclick="location.reload();" value="Actualizar">
            </div>
            </form>
        </div>
    </div>
<script src="js/main.js"></script>
</body>
</html>
