<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output    = '';
$clinic_id = Tenant::id();

if (isset($_POST['query'])) {
    $search = '%' . $_POST['query'] . '%';
    $stmt = $db->prepare(
        'SELECT * FROM pacientes WHERE clinic_id = ? AND (cedula LIKE ? OR nombre LIKE ?)'
    );
    $stmt->bind_param('iss', $clinic_id, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $db->prepare('SELECT * FROM pacientes WHERE clinic_id = ?');
    $stmt->bind_param('i', $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows > 0) {
    $output .= '<div class="table-responsive">
   <table class="table table-bordered">
    <tr>
     <th>Nombre</th><th>Apellidos</th><th>Cedula</th>
     <th>Teléfono</th><th>Email</th><th>Acciones</th>
    </tr>';
    while ($row = $result->fetch_assoc()) {
        $pid = (int)$row['id'];
        $output .= '<tr>
    <td>' . h($row['nombre']) . '</td>
    <td>' . h($row['apellido']) . '</td>
    <td>' . h($row['cedula']) . '</td>
    <td>' . h($row['telefono']) . '</td>
    <td>' . h($row['email']) . '</td>
    <td class="text-nowrap">
      <a href="edit_paciente.php?id=' . $pid . '" class="btn btn-sm btn-success mr-1">
        <i class="fa fa-pencil"></i> Editar
      </a>
      <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $pid . ')">
        <i class="fa fa-trash"></i> Borrar
      </button>
    </td>
   </tr>';
    }
    $output .= '</table></div>';
    echo $output;
} else {
    echo '<div class="empty-state"><i class="fa fa-search fa-2x mb-3 d-block" aria-hidden="true"></i><p>No se encontraron resultados.</p></div>';
}
