<?php
error_reporting(0);
//fetch.php
// include("conexion/config.php");
$connect = mysqli_connect("localhost", "root", "", "anguizola");
$output = '';
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($connect, $_POST["query"]);
 $query = "SELECT * FROM pago WHERE cedula LIKE '%".$search."%' OR nombre LIKE '%".$search."%'";
}
else
{
 $query = "SELECT * FROM pago";
}
$result = mysqli_query($connect, $query);
if(mysqli_num_rows($result) > 0)
{
 $output .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
    <td>Id</td>
    <th>Fecha</th>
    <th>Nombre</th>
    <th>Cedula</th>
    <th>Cantidad pagada</th>
    <th>Tipo de pago</th>
    <th>Saldo</th>
    <th>Tratamiento</th>
    <th>Nota</th>
    </tr>
 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '
   <tr>
    <td>'.$row["id"].'</td>
    <td>'.$row["fecha"].'</td>
    <td>'.$row["nombre"].' '.$row["apellido"].'</td>
    <td>'.$row["cedula"].'</td>
    <td>B/. '.$row["monto"].'</td>
    <td>'.$row["tipo_de_pago"].'</td>
    <td>B/. '.$row["saldo"].'</td>
    <td>'.$row["tratamiento"].'</td>
    <td>'.$row["nota"].'</td>
   </tr>
  ';
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

?>
