<?php
include("../conexion/config.php");
if (isset($_POST["update"])) {

    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $cedula = $_POST["cedula"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $ocupacion = $_POST["ocupacion"];
    $edad = $_POST["edad"];
    $idpost = $_POST["id"];
    
    $update = "UPDATE pacientes
    SET nombre = IF('$nombre' = '', nombre, '$nombre'),
    apellido = IF('$apellido' = '', apellido, '$apellido'),
    cedula = IF('$cedula' = '', cedula, '$cedula'),
    direccion = IF('$direccion' = '', direccion, '$direccion'),
    telefono = IF('$telefono' = '', telefono, '$telefono'),
    email = IF('$email' = '', email, '$email'),
    ocuapacion = IF('$ocupacion' = '', ocuapacion, '$ocupacion'),
    edad = IF('$edad' = '', edad, '$edad')
    WHERE id = $idpost
    ";
    
    if (mysqli_query($db, $update)) {
        echo "New record has been added successfully !";
        header("location: ../pacientes.php?guardado");
    } else {
        echo "Error: " . $update . ":-" . mysqli_error($db);
    }
    }
    
    $db->close();
?>