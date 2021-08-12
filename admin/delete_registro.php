<?php
// error_reporting(0);
include_once('conexion/config.php');

// $delete_pacient = $_POST['iddelete'];
//
// if (isset($_POST["delte"])) {
// $querydelete = "DELETE FROM `pacientes` WHERE id = '$delete_pacient'";
// $result = mysqli_query($db, $querydelete);
// }
// if($result){
//   header('pacientes.php');
// }


if(!empty($_POST['idelete'])){
$id=$_POST['idelete'];
}
$query = "DELETE FROM `pago` WHERE id = '".$id."'";
$result = mysqli_query($db, $query);
if ( !$result ) {
    trigger_error('query failed', E_USER_ERROR);
}

header('historial.php');
?>
