<?php
require_once __DIR__ . '/core/Csrf.php';
require_once __DIR__ . '/core/Audit.php';
session_start();
Csrf::verify();
require_once __DIR__ . '/conexion/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/autoload.php';

if (isset($_POST['enviar'])) {
    $fecha       = $_POST['fecha']    ?? '';
    $cedula      = $_POST['cedula']   ?? '';
    $nombre      = trim(($_POST['nombre'] ?? '') . ' ' . ($_POST['apellido'] ?? ''));
    $cantidad    = (float)($_POST['cantidad'] ?? 0);
    $tipopago    = $_POST['tipo_pago'] ?? '';
    $saldo       = (float)($_POST['saldo'] ?? 0);
    $saldototal  = $saldo - $cantidad;
    $email       = $_POST['email']    ?? '';
    $tratamiento = $_POST['trata']    ?? '';
    $nota        = $_POST['nota']     ?? '';

    $clinic_id = Tenant::id();
    $stmt = $db->prepare(
        'INSERT INTO pago (clinic_id, fecha, nombre, cedula, monto, tipo_de_pago, saldo, tratamiento, nota)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('issssssss', $clinic_id, $fecha, $nombre, $cedula, $cantidad, $tipopago, $saldototal, $tratamiento, $nota);
    $stmt->execute();
    $pago_id = (string)$db->insert_id;
    $stmt->close();
    Audit::log('insert_pago', 'pago', $pago_id);

    // Fetch the clinic name for use in emails / PDFs
    $clinic_name = 'ClíSys';
    $cstmt = $db->prepare('SELECT name FROM clinics WHERE id = ?');
    $cstmt->bind_param('i', $clinic_id);
    $cstmt->execute();
    $crow = $cstmt->get_result()->fetch_assoc();
    $cstmt->close();
    if (!empty($crow['name'])) {
        $clinic_name = $crow['name'];
    }

    // send email
    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function

//desactivar funcion temporal

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        // Server settings
        // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'STARTTLS';
        $mail->Port       = (int)$_ENV['SMTP_PORT'];




//         //Recipients
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($email, $nombre);     // Add a recipient

//         //Content
$mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Factura ' . $clinic_name;
        $mail->Body    = '<div class="invoice-card">
          <img src="https://c/logo-color.png" style="width:50px;">
          <div class="invoice-title">
            <div id="main-title">
              <h4><?= h($clinic_name) ?></h4>
            </div>
            <span id="date">'.$fecha.'</span>
          </div>

          <div class="invoice-details">
            <p></p>
            <table class="invoice-table">
              <thead>
                <tr class="row-data">
                  <td>nombre: '. $nombre .'</td>
                  <td></td>
                </tr>
              </thead>

              <tbody>
                <tr class="row-data">
                  <td>Cantidad pagada:</td>
                  <td>'.$cantidad.'</td>
                </tr>
                <tr class="row-data">
                  <td>Tipo de pago:</td>
                  <td>'.$tipopago.'</td>
                </tr>
                <tr class="row-data">
                  <td>Tratamiento:</td>
                  <td>'.$tratamiento.'</td>
                </tr>
                <tr class="row-data">
                  <td>Saldo restante:</td>
                  <td>'.$saldototal.'</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="invoice-footer">
            <p></p>
          </div>
        </div>
        <style media="screen">
        @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");

        :root {
        --primary-color: #f5826e;
        }

        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Roboto", sans-serif;
        letter-spacing: 0.5px;
        }

        body {
        background-color: var(--primary-color);
        }

        .invoice-card {
        display: flex;
        flex-direction: column;
        position: absolute;
        padding: 10px 2em;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-height: 25em;
        width: 22em;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0px 10px 30px 5px rgba(0, 0, 0, 0.15);
        }

        .invoice-card > div {
        margin: 5px 0;
        }

        .invoice-title {
        flex: 3;
        }

        .invoice-title #date {
        display: block;
        margin: 8px 0;
        font-size: 12px;
        }

        .invoice-title #main-title {
        display: flex;
        justify-content: space-between;
        margin-top: 2em;
        }

        .invoice-title #main-title h4 {
        letter-spacing: 2.5px;
        }

        .invoice-title span {
        color: rgba(0, 0, 0, 0.4);
        }

        .invoice-details {
        flex: 1;
        border-top: 0.5px dashed grey;
        border-bottom: 0.5px dashed grey;
        display: flex;
        align-items: center;
        }

        .invoice-table {
        width: 100%;
        border-collapse: collapse;
        }

        .invoice-table thead tr td {
        font-size: 12px;
        letter-spacing: 1px;
        color: grey;
        padding: 8px 0;
        }

        .invoice-table thead tr td:nth-last-child(1),
        .row-data td:nth-last-child(1),
        .calc-row td:nth-last-child(1) {
        text-align: right;
        }

        .invoice-table tbody tr td {
        padding: 8px 0;
        letter-spacing: 0;
        }

        .invoice-table .row-data #unit {
        text-align: center;
        }

        .invoice-table .row-data span {
        font-size: 13px;
        color: rgba(0, 0, 0, 0.6);
        }

        .invoice-footer {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        }

        .invoice-footer #later {
        margin-right: 5px;
        }

        .btn {
        border: none;
        padding: 5px 0px;
        background: none;
        cursor: pointer;
        letter-spacing: 1px;
        outline: none;
        }

        .btn.btn-secondary {
        color: rgba(0, 0, 0, 0.3);
        }

        .btn.btn-primary {
        color: var(--primary-color);
        }

        .btn#later {
        margin-right: 2em;
        }
        </style>
        ';
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Message has been sent';

    } catch (Exception $e) {

        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }

//desactiva hasta aqui

    header("Location: historial.php?ok=pago");
}


use Dompdf\Dompdf;
if (isset($_POST['print'])) {
  $fecha = $_POST['fecha'];
  $cedula = $_POST['cedula'];
  $nombre = $_POST['nombre'];
  $cantidad = $_POST['cantidad'];
  $tipopago = $_POST['tipo_pago'];
  $saldo = $_POST['saldo'];


  // Fetch clinic name for PDF header
  $pdf_clinic_name = 'ClíSys';
  $pdf_clinic_id = isset($_SESSION['clinic_id']) ? (int)$_SESSION['clinic_id'] : 1;
  require_once __DIR__ . '/conexion/config.php';
  $pcstmt = $db->prepare('SELECT name FROM clinics WHERE id = ?');
  $pcstmt->bind_param('i', $pdf_clinic_id);
  $pcstmt->execute();
  $pcrow = $pcstmt->get_result()->fetch_assoc();
  $pcstmt->close();
  if (!empty($pcrow['name'])) {
      $pdf_clinic_name = $pcrow['name'];
  }

  $dompdf = new Dompdf();
  // ob_start();
  // include "pdftemplate.php";
  $html = ('<div class="row expanded">
    <main class="columns">
      <div class="inner-container">
      <section class="row">
        <div class="callout large invoice-container">
          <table class="invoice">
            <tr class="header">
              <td class="">
                <!-- <img src="../Main/img/logo-color.png"  /> -->
              </td>

            </tr>
            <tr class="intro">

              <td style="color:black">
                <h2 style="color:#5cb6ac">Factura ' . htmlspecialchars($pdf_clinic_name, ENT_QUOTES) . '</h2>
                ' . htmlspecialchars($pdf_clinic_name, ENT_QUOTES) . '<br>
                '.$fecha.' <br>
                Paciente: '.$nombre.'  <br>
              </td>
              <tr>
                <td><p style="color:#808080">------------------------------------------------------</p></td>
              </tr>

            </tr>

            <tbody>
              <tr>
                <td>Cantidad pagada:</td>
                <td style="color:black">'.$cantidad.'</td>
              </tr>
              <tr>
                <td>Tipo de pago:</td>
                <td style="color:black">'.$tipopago.'</td>
              </tr>
              <tr>
                <td>Saldo restante:</td>
                <td style="color:black">'.$saldototal.'</td>
              </tr>
            </tbody>


          <section class="additional-info">
          <div class="row">
            <div class="columns">
              <h5>Información</h5>
              <p><?= htmlspecialchars($pdf_clinic_name, ENT_QUOTES) ?><br>
                <?= htmlspecialchars($_ENV['SMTP_FROM'] ?? '', ENT_QUOTES) ?>
              </p>
            </div>

          </div>
          </section>
        </div>
      </section>
      </div>
    </main>
  </div>');
  $dompdf->loadHtml($html);
  $dompdf->render();
  header("Content-type: application/pdf");
  header("Content-Disposition: inline; filename=factura.pdf");
  echo $dompdf->output();
}
?>

