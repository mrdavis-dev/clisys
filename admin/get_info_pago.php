<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output    = '';
$clinic_id = Tenant::id();

if (isset($_POST['query']) && trim($_POST['query']) !== '') {
    $search = '%' . trim($_POST['query']) . '%';
    $stmt = $db->prepare(
        'SELECT * FROM pacientes WHERE clinic_id = ? AND cedula LIKE ?'
    );
    $stmt->bind_param('is', $clinic_id, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= '
  <label for="">Correo:</label>
  <input type="email" readonly required class="form-control border" value="' . h($row['email']) . '" name="email" id="email">

  <label for="">Nombre del cliente</label>
  <input type="text" readonly required class="form-control border" value="' . h($row['nombre']) . '" name="nombre" id="names_cli">

  <label for="">Apellido del cliente</label>
  <input type="text" readonly required class="form-control border" value="' . h($row['apellido']) . '" name="apellido" id="names_apellido">
  ';
        }
        echo $output;
    }
    $stmt->close();
}
