<?php
require_once __DIR__ . '/core/Csrf.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (count($_FILES) > 0 && is_uploaded_file($_FILES['userImage']['tmp_name'])) {
    $clinic_id = Tenant::id();
    $cedula    = $_POST['search'] ?? '';
    $consulta  = $_POST['consul'] ?? '';
    $imageData = file_get_contents($_FILES['userImage']['tmp_name']);
    $imageProps = getimagesize($_FILES['userImage']['tmp_name']);
    $mime      = $imageProps['mime'] ?? 'image/png';
    $null      = null;

    $stmt = $db->prepare(
        'INSERT INTO consulta (clinic_id, cedula, tratamiento, imageType, imageData) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('isssb', $clinic_id, $cedula, $consulta, $mime, $null);
    $stmt->send_long_data(4, $imageData);
    $stmt->execute();
    $stmt->close();
    header('Location: odontograma.php');
    exit;
}
