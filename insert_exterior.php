<?php
include_once("admin/conexion/config.php");
// include_once('Main/html/conexion/config.php');

$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$nombre = $_POST['nombre'];
$asunto = $_POST['asunto'];
$doctor = $_POST['doctor'];

$sql = "insert into citas_tabla (fecha_de_cita,hora_de_cita,nombre_paciente,asunto_de_la_cita,doctor) values ('$fecha', '$hora','$nombre','$asunto','$doctor')";
mysqli_query($db,$sql);

header("location: index.php?guardado")
?>
