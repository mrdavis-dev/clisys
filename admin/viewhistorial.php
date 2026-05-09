<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output    = '';
$clinic_id = Tenant::id();

if (isset($_POST['query'])) {
    $search = '%' . $_POST['query'] . '%';
    $stmt = $db->prepare(
        'SELECT * FROM pago WHERE clinic_id = ? AND (cedula LIKE ? OR nombre LIKE ?)'
    );
    $stmt->bind_param('iss', $clinic_id, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $db->prepare('SELECT * FROM pago WHERE clinic_id = ? ORDER BY fecha DESC');
    $stmt->bind_param('i', $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows > 0) {
    $output .= '<div class="table-responsive">
   <table class="table table-bordered">
    <tr>
    <td>Id</td><th>Fecha</th><th>Nombre</th><th>Cedula</th>
    <th>Cantidad pagada</th><th>Tipo de pago</th><th>Saldo</th>
    <th>Tratamiento</th><th>Nota</th><th>Acciones</th>
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
    <td><button type="button" class="btn btn-sm btn-warning"
      data-toggle="modal" data-target="#modalEditPago"
      data-id="'          . h((string)$row['id'])       . '"
      data-fecha="'        . h($row['fecha'])            . '"
      data-monto="'        . h((string)$row['monto'])    . '"
      data-tipo="'         . h($row['tipo_de_pago'])     . '"
      data-tratamiento="'  . h($row['tratamiento'])      . '"
      data-nota="'         . h($row['nota'])             . '"
    ><i class="fa fa-pencil"></i> Editar</button></td>
   </tr>';
    }
    $output .= '</table></div>';
    echo $output;
} else {
    echo '<div class="empty-state"><i class="fa fa-folder-open-o fa-2x mb-3 d-block" aria-hidden="true"></i><p>No hay registros de pago para mostrar.</p></div>';
}
