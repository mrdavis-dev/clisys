<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output    = '';
$clinic_id = Tenant::id();

$perPage = 20;
$page    = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
$offset  = ($page - 1) * $perPage;

if (isset($_POST['query']) && trim($_POST['query']) !== '') {
    $search = '%' . trim($_POST['query']) . '%';
    $countStmt = $db->prepare(
        'SELECT COUNT(*) AS total FROM pacientes WHERE clinic_id = ? AND (cedula LIKE ? OR nombre LIKE ?)'
    );
    $countStmt->bind_param('iss', $clinic_id, $search, $search);
    $countStmt->execute();
    $total = (int)$countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $stmt = $db->prepare(
        'SELECT * FROM pacientes WHERE clinic_id = ? AND (cedula LIKE ? OR nombre LIKE ?) ORDER BY id DESC LIMIT ? OFFSET ?'
    );
    $stmt->bind_param('issii', $clinic_id, $search, $search, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $countStmt = $db->prepare('SELECT COUNT(*) AS total FROM pacientes WHERE clinic_id = ?');
    $countStmt->bind_param('i', $clinic_id);
    $countStmt->execute();
    $total = (int)$countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $stmt = $db->prepare('SELECT * FROM pacientes WHERE clinic_id = ? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('iii', $clinic_id, $perPage, $offset);
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
      <a href="edit_paciente.php?id=' . $pid . '&tab=notas" class="btn btn-sm btn-primary mr-1">
        <i class="fa fa-stethoscope"></i> Atender
      </a>
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

    $totalPages = (int)ceil($total / $perPage);
    if ($totalPages > 1) {
        $window = 2;
        $start  = max(1, $page - $window);
        $end    = min($totalPages, $page + $window);

        $output .= '<nav aria-label="Paginación de pacientes"><ul class="pagination justify-content-center">';

        $prevDisabled = $page === 1 ? ' disabled' : '';
        $output .= '<li class="page-item' . $prevDisabled . '"><a href="#" class="page-link pago-page" data-page="' . max(1, $page - 1) . '" aria-label="Anterior">&laquo;</a></li>';

        if ($start > 1) {
            $output .= '<li class="page-item"><a href="#" class="page-link pago-page" data-page="1">1</a></li>';
            if ($start > 2) {
                $output .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
            }
        }

        for ($p = $start; $p <= $end; $p++) {
            $active = $p === $page ? ' active' : '';
            $output .= '<li class="page-item' . $active . '"><a href="#" class="page-link pago-page" data-page="' . $p . '">' . $p . '</a></li>';
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $output .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
            }
            $output .= '<li class="page-item"><a href="#" class="page-link pago-page" data-page="' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        $nextDisabled = $page === $totalPages ? ' disabled' : '';
        $output .= '<li class="page-item' . $nextDisabled . '"><a href="#" class="page-link pago-page" data-page="' . min($totalPages, $page + 1) . '" aria-label="Siguiente">&raquo;</a></li>';

        $output .= '</ul></nav>';
    }

    echo $output;
} else {
    echo '<div class="empty-state"><i class="fa fa-search fa-2x mb-3 d-block" aria-hidden="true"></i><p>No se encontraron resultados.</p></div>';
}
