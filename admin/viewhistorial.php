<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output = '';

if (isset($_POST['query'])) {
    $search = '%' . $_POST['query'] . '%';
    $stmt = $db->prepare(
        'SELECT * FROM pago WHERE cedula LIKE ? OR nombre LIKE ?'
    );
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $db->query('SELECT * FROM pago ORDER BY fecha DESC');
}

if ($result->num_rows > 0) {
    $output .= '<div class="table-responsive">
   <table class="table table-bordered">
    <tr>
    <td>Id</td><th>Fecha</th><th>Nombre</th><th>Cedula</th>
    <th>Cantidad pagada</th><th>Tipo de pago</th><th>Saldo</th>
    <th>Tratamiento</th><th>Nota</th>
    </tr>';
    while ($row = $result->fetch_assoc()) {
        $output .= '<tr>
    <td>' . h((string)$row['id']) . '</td>
    <td>' . h($row['fecha']) . '</td>
    <td>' . h($row['nombre']) . '</td>
    <td>' . h($row['cedula']) . '</td>
    <td>B/. ' . h((string)$row['monto']) . '</td>
    <td>' . h($row['tipo_de_pago']) . '</td>
    <td>B/. ' . h((string)$row['saldo']) . '</td>
    <td>' . h($row['tratamiento']) . '</td>
    <td>' . h($row['nota']) . '</td>
   </tr>';
    }
    $output .= '</table></div>';
    echo $output;
} else {
    echo 'Data Not Found';
}
