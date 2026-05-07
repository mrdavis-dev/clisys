<?php
require_once __DIR__ . '/../core/Auth.php';
include __DIR__ . '/../conexion/config.php';

$t = $_GET['trata']  ?? '';
$c = $_GET['cedula'] ?? '';

$stmt = $db->prepare(
    'SELECT * FROM pago WHERE id = (SELECT MAX(id) FROM pago WHERE tratamiento = ? AND cedula = ?)'
);
$stmt->bind_param('ss', $t, $c);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<label for="">Saldo</label>
        <input type="text" required class="form-control border" value="' . h((string)$row['saldo']) . '" autocomplete="off" name="saldo">';
    }
} else {
    echo '<label for="">Saldo</label>
    <input type="text" required class="form-control border" autocomplete="off" value="0" name="saldo">';
}
$stmt->close();
