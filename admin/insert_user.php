<?php
include_once('conexion/config.php');

$user = $_POST['usuario'];
$name = $_POST['nombre'];
$pass = $_POST['pass'];
$options = array("cost" => 4);
$hashPassword = password_hash($pass, PASSWORD_BCRYPT, $options);

$sql = "insert into users (id,username,name,password) values ('id','$user', '$name', '$hashPassword')";
mysqli_query($db,$sql);

header("location: registro_user.php?guardado")
?>
