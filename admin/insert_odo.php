<?php
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Audit.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';

if (count($_FILES) > 0 && is_uploaded_file($_FILES['userImage']['tmp_name'])) {
    $clinic_id  = Tenant::id();
    $cedula     = $_POST['search'] ?? '';
    $consulta   = $_POST['consul'] ?? '';
    $tmpPath    = $_FILES['userImage']['tmp_name'];
    $imageProps = getimagesize($tmpPath);
    $mime       = $imageProps['mime'] ?? 'image/png';

    // --- Disk storage (Fase 3) ---
    $ext       = ($mime === 'image/jpeg') ? 'jpg' : 'png';
    $uploadDir = __DIR__ . '/uploads/odontograma/' . $clinic_id . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename   = uniqid('odo_', true) . '.' . $ext;
    $destPath   = $uploadDir . $filename;
    $imagenPath = 'odontograma/' . $clinic_id . '/' . $filename;

    if (!move_uploaded_file($tmpPath, $destPath)) {
        // Fallback to BLOB if disk write fails
        error_log('insert_odo: move_uploaded_file failed, falling back to BLOB');
        $imageData = file_get_contents($tmpPath);
        $null      = null;

        $stmt = $db->prepare(
            'INSERT INTO consulta (clinic_id, cedula, tratamiento, imageType, imageData) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('isssb', $clinic_id, $cedula, $consulta, $mime, $null);
        $stmt->send_long_data(4, $imageData);
        $stmt->execute();
        $new_id = (string)$db->insert_id;
        $stmt->close();
    } else {
        // Normal path: store file reference in DB (imageData left NULL)
        $stmt = $db->prepare(
            'INSERT INTO consulta (clinic_id, cedula, tratamiento, imageType, imagen_path) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('issss', $clinic_id, $cedula, $consulta, $mime, $imagenPath);
        $stmt->execute();
        $new_id = (string)$db->insert_id;
        $stmt->close();
    }

    Audit::log('insert_odontogram', 'consulta', $new_id);
    header('Location: odontograma.php');
    exit;
}
