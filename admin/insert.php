<?php
include_once('conexion/config.php');

$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$nombre = $_POST['nombre'];
$asunto = $_POST['asunto'];
$doctor = $_POST['doctor'];

if(isset($_POST["save"])){
    $sql = "INSERT INTO citas_tabla (fecha_de_cita,hora_de_cita,nombre_paciente,asunto_de_la_cita,doctor) values ('$fecha', '$hora','$nombre','$asunto','$doctor')";
    mysqli_query($db,$sql);
    header("location: inicio.php?guardado");
}else{

}

?>
