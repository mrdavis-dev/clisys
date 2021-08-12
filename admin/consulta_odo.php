<?php
error_reporting(0);
//fetch.php
// include("conexion/config.php");
$db = mysqli_connect("localhost", "root", "", "clinica");
$output = '';
$output2 = '';
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($db, $_POST["query"]);
 $query = "SELECT * FROM pacientes WHERE cedula LIKE '%".$search."%'";
 // $query = " SELECT cedula FROM pacientes, consulta WHERE pacientes.cedula=consulta.cedula LIKE '%".$search."%' ";
}
else
{
 // $query = "SELECT * FROM paciente";
}
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0)
{
 $output .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
    <th>Nombre</th>
    <th>cedula</th>
    <th>Apellido</th>
    <th>Edad</th>
    <th>motivo de consulta</th>
    <th>habitos higienicos</th>
    <th>Esta bajo tratamiento actualmente</th>
    <th>Hospitalizado quirurgicamente</th>
    <th>Toma algun medicamento o droga</th>
    <th>Presenta algun tipo de alergia</th>
    <th>Ha tenido una enfermedad cardiaca</th>
    <th>Es usted diabetico</th>
    <th>Tuberculosis o hepatitis</th>
    <th>Ha presentado alteraciones en el sangrado</th>
    <th>Ha tenido alguna ETS</th>
    <th>Tiene un mal habito</th>
    </tr>
 ';
 while($row = mysqli_fetch_array($result))
 {
  $output .= '
   <tr>
    <td>'.$row["nombre"].'</td>
    <td>'.$row["cedula"].'</td>
    <td>'.$row["apellido"].'</td>
    <td>'.$row["edad"].'</td>
    <td>'.$row["motivo_consuta"].'</td>
    <td>'.$row["habitos_higienicos"].'</td>
    <td>'.$row["esta_bajo_tratamiento_actualmente"].'</td>
    <td>'.$row["Ha_sido_hospitalizado_quirurgicamente"].'</td>
    <td>'.$row["esta_tomando_algun_medicamento_o_droga"].'</td>
    <td>'.$row["presenta_algun_tipo_de_alergia"].'</td>
    <td>'.$row["Ha_tenido_algun_tipo_de_enfermedad_cardiaca"].'</td>
    <td>'.$row["Es_usted_diabetico_"].'</td>
    <td>'.$row["Ha_tenido_tuberculosis_o_hepatitis"].'</td>
    <td>'.$row["Ha_presentado_alteraciones_en_el_sangrado"].'</td>
    <td>'.$row["Ha_tenido_alguna_enfermedad_de_transmision_sexual"].'</td>
    <td>'.$row["Tiene_algun_tipo_de_mal_habito"].'</td>
   </tr>
  ';

 }
 echo $output;
}
else
{
 echo 'Realizar busqúeda';
}

// consulta tabla consulta odontograma...
if(isset($_POST["query"]))
{
 $search = mysqli_real_escape_string($db, $_POST["query"]);
 $query2 = "SELECT * FROM consulta WHERE cedula LIKE '%".$search."%'";
}
else
{
 // $query = "SELECT * FROM consulta";
}
$result2 = mysqli_query($db, $query2);
if(mysqli_num_rows($result2) > 0)
{
 $output2 .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
      <th>tratamiento</th>
      <th>odontograma</th>
    </tr>
 ';
 while($row2 = mysqli_fetch_array($result2))
 {
  $output2 .= '
   <tr>
      <td>'.$row2["tratamiento"].'</td>
      <td><img height="300" width="800" src="data:image/jpeg;base64,'.base64_encode($row2["imageData"]).'"/></td>
   </tr>
  ';

 }
 echo $output2;
}
else
{
 // echo 'Realizar busqúeda';
}

?>
