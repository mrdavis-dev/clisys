<?php
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Audit.php';
Csrf::verify();
require_once __DIR__ . '/../conexion/config.php';

if (!isset($_POST['username'], $_POST['password'])) {
    exit('Por favor completa usuario y contraseña.');
}

//  SI SE CONECTO Y SI SE ENVIARON AMBOS DATOS SE PROCEDE CON LA CONSULTA DE EXISTENCIA DEL USUARIO EVITANDO INYECCIONES SQL ?
if ($stmt = $db->prepare('SELECT id, password, clinic_id, role FROM users WHERE username = ?'))
 {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();

     // SI EL USUARIO EXISTE EN LA TABLA SE EXTRAE Y SE APUNTA SU DNI Y SU CLAVE
     if ($stmt->num_rows > 0)
      {
		$stmt->bind_result($dni, $clave, $clinic_id, $role);
		$stmt->fetch();

			// AHORA VERIFICA SI LA CLAVE QUE SE EXTRAJO DE LA TABLA ES IGUAL A LA QUE SE ENVIA DESDE EL FORMULARIO
        	//if ($_POST['password'] === $clave)
          	if(password_verify( $_POST['password'],$clave))
        		{
                    // SI COINICIDEN AMBAS CONTRASEÑAS SE INICIA LA SESION Y SE LE DA LA BIENCENIDA AL USUARIO CON ECHO
					session_regenerate_id();
					$_SESSION['loggedin']   = TRUE;
					$_SESSION['username']   = $_POST['username'];
					$_SESSION['id']         = $dni;
					$_SESSION['clinic_id']  = (int)$clinic_id;
					$_SESSION['role']       = $role ?? 'admin';
					Audit::log('login', 'users', (string)$dni);
					$dest = ($role === 'superadmin') ? '../superadmin/index.php' : '../inicio.php';
                    header('Location: ' . $dest);
                   
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