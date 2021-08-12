<?php
include_once('conexion/config.php');

if(isset($_POST['submit']))
{
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $ocupacion = $_POST['ocupacion'];
    $edad = $_POST['edad'];
    $motivo_consulta = $_POST['motivo'];
    $habitos_higienicos = $_POST['habitos'];
    $esta_bajo_tratamiento_actualmente = $_POST['bajotratamiento'];
    $hospitalizado = $_POST['quirurgicamente'];
    $medicamento_o_droga = $_POST['droga'];
    $tipo_de_alergia = $_POST['alergia'];
    $ha_tenido_algún_tipo_de_enfermedad_cardiaca = $_POST['cardiaca'];
    $es_usted_diabético_ = $_POST['diabético'];
    $ha_tenido_tubérculosis_o_hepatitis = $_POST['hepatitis'];
    $ha_presentado_alteraciones_en_el_sangrado = $_POST['sangrado'];
    $ha_tenido_algúna_enfermedad_de_transmisión_sexual = $_POST['transmision'];
    $tiene_algún_tipo_de_mal_habito = $_POST['habito'];
// $fecha_de_pago = $_POST[''];
// $cantidad_pagada = $_POST[''];
// $tipo_de_pago = $_POST[''];
// $saldo_restante = $_POST[''];
// $odontograma = $_POST[''];

    $sql = "INSERT into pacientes (nombre,apellido,cedula,direccion,telefono,email,ocuapacion,edad,motivo_consuta,
    habitos_higienicos,esta_bajo_tratamiento_actualmente,Ha_sido_hospitalizado_quirurgicamente,esta_tomando_algun_medicamento_o_droga,
    presenta_algun_tipo_de_alergia,Ha_tenido_algun_tipo_de_enfermedad_cardiaca,Es_usted_diabetico_,Ha_tenido_tuberculosis_o_hepatitis,
    Ha_presentado_alteraciones_en_el_sangrado,Ha_tenido_alguna_enfermedad_de_transmision_sexual,Tiene_algun_tipo_de_mal_habito) values ('$nombre','$apellido','$cedula','$direccion','$telefono','$email','$ocupacion','$edad','$motivo_consulta','$habitos_higienicos','$esta_bajo_tratamiento_actualmente','$hospitalizado','$medicamento_o_droga','$tipo_de_alergia','$ha_tenido_algún_tipo_de_enfermedad_cardiaca','$es_usted_diabético_','$ha_tenido_tubérculosis_o_hepatitis','$ha_presentado_alteraciones_en_el_sangrado','$ha_tenido_algúna_enfermedad_de_transmisión_sexual','$tiene_algún_tipo_de_mal_habito')";

    if (mysqli_query($db, $sql)) {
        echo "New record has been added successfully !";
        header("location: inicio.php?guardado");
     } else {
        echo "Error: " . $sql . ":-" . mysqli_error($db);
     }
     mysqli_close($db);
}

?>
