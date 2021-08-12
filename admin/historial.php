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
    <title>Historial</title>
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
	<div class="wrapper d-flex align-items-stretch" >
		<nav id="sidebar" class="">
			<div class="custom-menu">
				<button type="button" id="sidebarCollapse" class="btn btn-primary">
					<i class="fa fa-bars"></i>
					<span class="sr-only">Toggle Menu</span>
	      		</button>
        	</div>
			<div class="p-4">

		  		<h1 style="font-size: calc(20px + 1.1vw); " class="border-bottom"><a href="index.php" class="logo">Clisys</a></h1>
	       	<ul class="list-unstyled components mb-5">
            <li >
              <a href="inicio.php"><span class="fa fa-home mr-3"></span> Inicio</a>
            </li>
            <li >
              <a href="pacientes.php"><span class="fa fa-user mr-3"></span> Pacientes</a>
            </li>
            <li>
              <a href="odontograma.php"><span class="fa fa-smile-o mr-3"></span> Odontograma</a>
            </li>
            <li >
              <a href="getinfo.php"><span class="fa fa-money mr-3"></span> Pagos</a>
            </li>
            <li class="active">
              <a href="historial.php"><span class="fa fa-sticky-note mr-3"></span> Historial</a>
			</li>
			<li>
				<a href="registro_user.php"><span class="fa fa-users mr-3"></span>administradores</a>
			</li>
			<?php
          include "conexion/config.php";

          // Check user login or not
          if(!isset($_SESSION['uname'])){
              // header('Location: index.php');
          }

          // logout
          if(isset($_POST['but_logout'])){
              session_destroy();
              header('Location: index.php');
          }
          ?>
          <form method='post' action="">
            <input style="margin-top:25px" class="btn btn-danger" type="submit" value="Cerrar Sesion" name="but_logout">
        </form>
        </li>
	        </ul>
	      </div>
    	</nav>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5 pt-5 ">
          <div class="row border-bottom mb-3">
            <div class=" col-md-7 col-sm-12 animated fadeIn container centrar  ">
                <span><h1 class="display-3">Historial</h1></span>
            </div>
            <div class="col-md-5 col-sm-12 container centrar pt-3 row">
              <div class="col-10">
                <input class=" form-control border" type="text" name="search_text" id="search_text">
              </div>
              <div class="col-2">
                <a href="#" class=" btn btn-primary align-middle " ><i class="fa fa-search"></i></a>
              </div>
            </div>
            <div class="">

                <?php
             if(isset($_POST['delete'])) {

                // if(! $db ) {
                //    die('Could not connect: ' . mysqli_error($db));
                // }

                $emp_id = $_POST['emp_id'];

                $sql = "DELETE FROM pago WHERE id = $emp_id" ;
                // mysqli_select_db($conn,'anguizola');
                $retval = mysqli_query( $db,$sql );

                if(! $retval ) {
                   die('Could not delete data: ' . mysqli_error($db));
                }

                // echo "Deleted data successfully\n";

                mysqli_close($db);
             }else
            ?>
               <form method = "post" action = "<?php $_PHP_SELF ?>">
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


            </div>
          </div>
          <div class="container-fluid">

    			<div id="result">

    			</div>


          <script>
        		$(document).ready(function(){

        		load_data();

        		function load_data(query)
        		{
        		$.ajax({
        		url:"viewhistorial.php",
        		method:"POST",
        		data:{query:query},
        		success:function(data)
        		{
        			$('#result').html(data);
        		}
        		});
        		}
        		$('#search_text').keyup(function(){
        		var search = $(this).val();
        		if(search != '')
        		{
        		load_data(search);
        		}
        		else
        		{
        		load_data();
        		}
        		});
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
