<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Audit.php';
require_once __DIR__ . '/core/Plan.php';
Auth::require();
Auth::requireRole(['admin', 'medico']);
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (isset($_POST['submit'])) {
    // Enforce plan patient limit before inserting
    if (!Plan::withinLimit('patients')) {
        header('Location: inicio.php?err=limit_patients');
        exit;
    }

    $clinic_id  = Tenant::id();
    $nombre     = $_POST['nombre']          ?? '';
    $apellido   = $_POST['apellido']        ?? '';
    $cedula     = $_POST['cedula']          ?? '';
    $direccion  = $_POST['direccion']       ?? '';
    $telefono   = $_POST['telefono']        ?? '';
    $email      = $_POST['email']           ?? '';
    $ocupacion  = $_POST['ocupacion']       ?? '';
    $edad       = $_POST['edad']            ?? '';
    $motivo     = $_POST['motivo']          ?? '';
    $habitos    = $_POST['habitos']         ?? '';
    $trat       = $_POST['bajotratamiento'] ?? '';
    $hosp       = $_POST['quirurgicamente'] ?? '';
    $droga      = $_POST['droga']           ?? '';
    $alergia    = $_POST['alergia']         ?? '';
    $cardiaca   = $_POST['cardiaca']        ?? '';
    $diabetico  = $_POST['diabético']       ?? '';
    $hepatitis  = $_POST['hepatitis']       ?? '';
    $sangrado   = $_POST['sangrado']        ?? '';
    $transmision = $_POST['transmision']    ?? '';
    $habito     = $_POST['habito']          ?? '';

    $stmt = $db->prepare(
        'INSERT INTO pacientes
         (clinic_id, nombre, apellido, cedula, direccion, telefono, email, ocupacion, edad,
          motivo_consulta, habitos_higienicos, esta_bajo_tratamiento_actualmente,
          Ha_sido_hospitalizado_quirurgicamente, esta_tomando_algun_medicamento_o_droga,
          presenta_algun_tipo_de_alergia, Ha_tenido_algun_tipo_de_enfermedad_cardiaca,
          Es_usted_diabetico_, Ha_tenido_tuberculosis_o_hepatitis,
          Ha_presentado_alteraciones_en_el_sangrado,
          Ha_tenido_alguna_enfermedad_de_transmision_sexual,
          Tiene_algun_tipo_de_mal_habito)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->bind_param(
        'issssssssssssssssssss',
        $clinic_id,
        $nombre, $apellido, $cedula, $direccion, $telefono, $email, $ocupacion, $edad,
        $motivo, $habitos, $trat, $hosp, $droga, $alergia, $cardiaca,
        $diabetico, $hepatitis, $sangrado, $transmision, $habito
    );
    $stmt->execute();
    $new_id = (string)$db->insert_id;
    $stmt->close();
    Audit::log('insert_patient', 'pacientes', $new_id);
    $redirect  = trim($_POST['redirect_to'] ?? '');
    $allowed   = ['inicio.php', 'pacientes.php'];
    $safe      = false;
    foreach ($allowed as $a) {
        if (strncmp($redirect, $a, strlen($a)) === 0) { $safe = true; break; }
    }
    $location = $safe ? $redirect . '?ok=paciente' : 'inicio.php?ok=paciente';
    header('Location: ' . $location);
    exit;
}
