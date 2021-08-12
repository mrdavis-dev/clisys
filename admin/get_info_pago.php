<?php
error_reporting(0);
//fetch.php
include("conexion/config.php");
$output = '';
if(isset($_POST["query"]))
{
  $search = mysqli_real_escape_string($db, $_POST["query"]);
  $query = "SELECT * FROM pacientes WHERE cedula LIKE '%".$search."%' ";
}
else
{
 // $query = "SELECT * FROM pacientes";
}
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0)
{
 $output .= '

 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '

  <label for="">Correo:</label>
  <input type="email" readonly="readonly" required class="form-control border" value='.$row["email"].' name="email" id="email">

  <label for="">Nombre del cliente</label>
  <input type="text" readonly="readonly" required class="form-control border" value='.$row["nombre"].' name="nombre" id="names_cli">

  <label for="">Apellido del cliente</label>
  <input type="text" readonly="readonly" required class="form-control border" value='.$row["apellido"].' name="apellido" id="names_apellido">
  ';
 }
 echo $output;
}
else
{
 // echo 'Data Not Found';
}
?>
