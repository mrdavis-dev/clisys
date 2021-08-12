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
		  		<h1 style="font-size: calc(20px + 1.1vw);" class="border-bottom"><a href="index.php" class="logo">Clisys</a></h1>
	       	<ul class="list-unstyled components mb-5">
				<li >
					<a href="inicio.php"><span class="fa fa-home mr-3"></span> Inicio</a>
				</li>
				<li >
					<a href="pacientes.php"><span class="fa fa-user mr-3"></span> Pacientes</a>
				</li>
				<li class="">
				<a href="odontograma.php"><span class="fa fa-smile-o mr-3"></span> Odontograma</a>
				</li>
				<li>
				<a href="getinfo.php"><span class="fa fa-money mr-3"></span> Pagos</a>
				</li>
				<li>
          <a href="historial.php"><span class="fa fa-sticky-note mr-3"></span> Historial</a>
        </li>
        <li>
          <a href="registro_user.php"><span class="fa fa-users mr-3"></span>administradores</a>
        </li>
        <li>
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