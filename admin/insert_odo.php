<?php
if(count($_FILES) > 0) {
if(is_uploaded_file($_FILES['userImage']['tmp_name'])) {
  include_once('conexion/config.php');

    $id = $_POST['search'];
    $consulta = $_POST['consul'];
    $imgData =addslashes(file_get_contents($_FILES['userImage']['tmp_name']));
	  $imageProperties = getimageSize($_FILES['userImage']['tmp_name']);

	$sql = "INSERT INTO consulta(cedula,tratamiento,imageType,imageData,fecha)
	VALUES('$id','$consulta','{$imageProperties['mime']}', '{$imgData}','fecha')";
	$current_id = mysqli_query($db, $sql) or die("<b>Error:</b> Problem on Image Insert<br/>" . mysqli_error($db));
	if(isset($current_id)) {
		header("Location: odontograma.php");
	}
}
}
?>
