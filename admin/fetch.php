<?php
error_reporting(0);
//fetch.php
include("conexion/config.php");
$output = '';
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($db, $_POST["query"]);
 $query = "SELECT * FROM pacientes WHERE cedula LIKE '%".$search."%' OR nombre LIKE '%".$search."%'
 ";
}
else
{
 $query = "SELECT * FROM pacientes";
}
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0)
{
 $output .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
     <th>id</th>
     <th>Nombre</th>
     <th>Apellidos</th>
     <th>Cedula</th>
     <th>Direccion</th>
     <th>Teléfono</th>
     <th>Email</th>
     <th>Ocupación</th>
    </tr>

 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '
   <tr>
    <td>'.$row["id"].'</td>
    <td>'.$row["nombre"].'</td>
    <td>'.$row["apellido"].'</td>
    <td>'.$row["cedula"].'</td>
    <td>'.$row["direccion"].'</td>
    <td>'.$row["telefono"].'</td>
    <td>'.$row["email"].'</td>
    <td>'.$row["ocuapacion"].'</td>
   </tr>


  ';
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

// if(isset($_POST['delete']))
// {
//   $checkbox = $_POST['checkbox'];

// for($i=0;$i<count($checkbox);$i++){

// $del_id = $checkbox[$i];
// $sql = "DELETE FROM pacientes WHERE id='$del_id'";
// $result = mysqli_query($db,$sql);
// }
// // if successful redirect to delete_multiple.php
// if($result){
// echo "<meta http-equiv=\"refresh\" content=\"0;URL=pacientes.php\">";
// }
// }

mysqli_close($db);

?>
