<?php
require_once __DIR__ . '/core/Auth.php';
include __DIR__ . '/conexion/config.php';

$output    = '';
$output2   = '';
$clinic_id = Tenant::id();

if (isset($_POST['query'])) {
    $search = '%' . $_POST['query'] . '%';

    $stmt = $db->prepare(
        'SELECT * FROM pacientes WHERE clinic_id = ? AND cedula LIKE ?'
    );
    $stmt->bind_param('is', $clinic_id, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $output .= '<div class="table-responsive">
   <table class="table table-bordered">
    <tr>
    <th>Nombre</th><th>Cedula</th><th>Apellido</th><th>Edad</th>
    <th>Motivo consulta</th><th>Hábitos higiénicos</th>
    <th>Bajo tratamiento</th><th>Hospitalizado quirúrgicamente</th>
    <th>Medicamento o droga</th><th>Alergia</th><th>Enf. cardiaca</th>
    <th>Diabético</th><th>Tuberculosis/hepatitis</th>
    <th>Alteraciones sangrado</th><th>ETS</th><th>Mal hábito</th>
    </tr>';
        while ($row = $result->fetch_assoc()) {
            $output .= '<tr>
    <td>' . h($row['nombre']) . '</td>
    <td>' . h($row['cedula']) . '</td>
    <td>' . h($row['apellido']) . '</td>
    <td>' . h($row['edad']) . '</td>
    <td>' . h($row['motivo_consulta']) . '</td>
    <td>' . h($row['habitos_higienicos']) . '</td>
    <td>' . h($row['esta_bajo_tratamiento_actualmente']) . '</td>
    <td>' . h($row['Ha_sido_hospitalizado_quirurgicamente']) . '</td>
    <td>' . h($row['esta_tomando_algun_medicamento_o_droga']) . '</td>
    <td>' . h($row['presenta_algun_tipo_de_alergia']) . '</td>
    <td>' . h($row['Ha_tenido_algun_tipo_de_enfermedad_cardiaca']) . '</td>
    <td>' . h($row['Es_usted_diabetico_']) . '</td>
    <td>' . h($row['Ha_tenido_tuberculosis_o_hepatitis']) . '</td>
    <td>' . h($row['Ha_presentado_alteraciones_en_el_sangrado']) . '</td>
    <td>' . h($row['Ha_tenido_alguna_enfermedad_de_transmision_sexual']) . '</td>
    <td>' . h($row['Tiene_algun_tipo_de_mal_habito']) . '</td>
   </tr>';
        }
        $output .= '</table></div>';
        echo $output;
    } else {
        echo 'Realizar búsqueda';
    }
    $stmt->close();

    // Odontogram records for same patient
    $stmt2 = $db->prepare(
        'SELECT * FROM consulta WHERE clinic_id = ? AND cedula LIKE ?'
    );
    $stmt2->bind_param('is', $clinic_id, $search);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
        $output2 .= '<div class="table-responsive">
   <table class="table table-bordered">
    <tr><th>Tratamiento</th><th>Odontograma</th></tr>';
        while ($row2 = $result2->fetch_assoc()) {
            $output2 .= '<tr>
      <td>' . h($row2['tratamiento']) . '</td>
      <td><img height="300" width="800" src="data:image/jpeg;base64,' . base64_encode($row2['imageData']) . '"/></td>
   </tr>';
        }
        $output2 .= '</table></div>';
        echo $output2;
    }
    $stmt2->close();
}
