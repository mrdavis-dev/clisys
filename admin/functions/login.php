<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<title></title>
</head>
<body>
<script>
    function backbtn(){
    window.history.back();
}
</script>
</body>
</html>
<?php
session_start();
require_once("../conexion/config.php");

//VERIFICACION DE ESCRITURA DE DATOS EN EL FORM
			if ( !isset($_POST['username'], $_POST['password']) )
            {
			// Could not get the data that should have been sent.
			exit('Please fill both the username and password fields!');
			}

//  SI SE CONECTO Y SI SE ENVIARON AMBOS DATOS SE PROCEDE CON LA CONSULTA DE EXISTENCIA DEL USUARIO EVITANDO INYECCIONES SQL ?
if ($stmt = $db->prepare('SELECT id, password FROM users WHERE username = ?'))
 {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
     
     // SI EL USUARIO EXISTE EN LA TABLA SE EXTRAE Y SE APUNTA SU DNI Y SU CLAVE
     if ($stmt->num_rows > 0)
      {
		$stmt->bind_result($dni, $clave);
		$stmt->fetch();
        
			// AHORA VERIFICA SI LA CLAVE QUE SE EXTRAJO DE LA TABLA ES IGUAL A LA QUE SE ENVIA DESDE EL FORMULARIO         
        	//if ($_POST['password'] === $clave) 
          	if(password_verify( $_POST['password'],$clave))
        		{
                    // SI COINICIDEN AMBAS CONTRASEÑAS SE INICIA LA SESION Y SE LE DA LA BIENCENIDA AL USUARIO CON ECHO
					session_regenerate_id();
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['username'] = $_POST['username'];
					$_SESSION['id'] = $dni;
			        // echo 'BIENVENIDO USUARIOP : ' . $_SESSION['name'] .' CON TU DNI NUMERO : '. $_SESSION['dni'] . '!';
                    header('Location: ../inicio.php');
                   
				} 
           
       				// SI EL USUARIO EXISTE PERO EL PASSWORD NO COINCIDE IMPRIMIR EN PANTALLA PASSWORD INCORRECTO
       
                   		else { echo '
							<div class="container text-center mt-5">
							<img src="https://icons.iconarchive.com/icons/paomedia/small-n-flat/1024/sign-error-icon.png" class="img-fluid" style="width: 25%;" alt="Responsive image">
							<h3 class="text-danger">¡CONTRASEÑA INCORRECTA!</h3>
							<p class="">Tu contraseña no coincide inténtalo de nuevo</p>
							<button class="btn btn-danger" onclick="backbtn()">Regresar</button>
							</div>
							';
					}
       	}  
      
      			   // SI EL USUARIO NO EXISTE MOSTRAR USUARIO INCORRECTO
          				else { echo '
							<div class="container text-center mt-5">
							<img src="https://icons.iconarchive.com/icons/paomedia/small-n-flat/1024/sign-error-icon.png" class="img-fluid" style="width: 25%;" alt="Responsive image">
							<h3 class="text-danger">¡USUARIO INCORRECTO!</h3>
							<p class="">Tu usuario no coincide inténtalo de nuevo</p>
							<button class="btn btn-danger" onclick="backbtn()">Regresar</button>
							</div>
							'; }

	$stmt->close();
}