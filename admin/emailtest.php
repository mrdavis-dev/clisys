<?php
/**
 * emailtest.php — Manual SMTP connectivity test.
 *
 * What it does:
 *   Sends a single test invoice-style HTML email via PHPMailer/SMTP.
 *   SMTPDebug = 2 is intentionally enabled so the full SMTP conversation
 *   is printed to the browser, making it easy to diagnose delivery issues.
 *
 * Usage:
 *   Open in a browser while logged in (or run from CLI).
 *   The recipient is read from $email / $nombre below — set them before running.
 *   SMTP credentials are loaded from .env (never hardcode them here).
 *
 * WARNING: This file is a development/debug tool. Restrict access in production.
 */

require_once __DIR__ . '/core/env.php';
loadEnv(__DIR__ . '/../.env');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

// --- Test recipient (change as needed) ---
$email  = 'test@example.com';
$nombre = 'Test User';
$fecha  = date('Y-m-d');
$cantidad  = '50.00';
$tipopago  = 'Efectivo';
$saldo     = '0.00';
// -----------------------------------------

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USERNAME'];
    $mail->Password   = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = 'STARTTLS';
    $mail->Port       = (int)$_ENV['SMTP_PORT'];

    $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
    $mail->addAddress($email, $nombre);

    $mail->isHTML(true);
    $clinic_display = htmlspecialchars($_ENV['SMTP_FROM_NAME'] ?? 'ClíSys', ENT_QUOTES);
    $mail->Subject = 'Factura ' . ($clinic_display);
    $mail->Body    = '<div class="invoice-card">
      <div class="invoice-title">
        <div id="main-title">
          <h4>' . $clinic_display . '</h4>
        </div>
        <span id="date">' . htmlspecialchars($fecha, ENT_QUOTES) . '</span>
      </div>

      <div class="invoice-details">
        <p></p>
        <table class="invoice-table">
          <thead>
            <tr class="row-data">
              <td>nombre: ' . htmlspecialchars($nombre, ENT_QUOTES) . '</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            <tr class="row-data">
              <td>Cantidad pagada:</td>
              <td>' . htmlspecialchars($cantidad, ENT_QUOTES) . '</td>
            </tr>
            <tr class="row-data">
              <td>Tipo de pago:</td>
              <td>' . htmlspecialchars($tipopago, ENT_QUOTES) . '</td>
            </tr>
            <tr class="row-data">
              <td>Saldo restante:</td>
              <td>' . htmlspecialchars($saldo, ENT_QUOTES) . '</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <style media="screen">
    @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
    :root { --primary-color: #f5826e; }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Roboto", sans-serif; letter-spacing: 0.5px; }
    body { background-color: var(--primary-color); }
    .invoice-card { display: flex; flex-direction: column; position: absolute; padding: 10px 2em; top: 50%; left: 50%; transform: translate(-50%, -50%); min-height: 25em; width: 22em; background-color: #fff; border-radius: 5px; box-shadow: 0px 10px 30px 5px rgba(0,0,0,0.15); }
    .invoice-card > div { margin: 5px 0; }
    .invoice-title { flex: 3; }
    .invoice-title #date { display: block; margin: 8px 0; font-size: 12px; }
    .invoice-title #main-title { display: flex; justify-content: space-between; margin-top: 2em; }
    .invoice-title #main-title h4 { letter-spacing: 2.5px; }
    .invoice-title span { color: rgba(0,0,0,0.4); }
    .invoice-details { flex: 1; border-top: 0.5px dashed grey; border-bottom: 0.5px dashed grey; display: flex; align-items: center; }
    .invoice-table { width: 100%; border-collapse: collapse; }
    .invoice-table thead tr td { font-size: 12px; letter-spacing: 1px; color: grey; padding: 8px 0; }
    .invoice-table tbody tr td { padding: 8px 0; letter-spacing: 0; }
    </style>';

    $mail->AltBody = 'Test email from ClíSys email test script.';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}
?>
