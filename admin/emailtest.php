<?php
// send email
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require 'vendor/autoload.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'clinicadentalanguizola@hotmail.com';                 // SMTP username
    $mail->Password = 'CLINICAdental2019';                           // SMTP password
    $mail->SMTPSecure = 'STARTTLS';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('clinicadentalanguizola@hotmail.com', 'clinica Anguizola');
    $mail->addAddress($email, $nombre);     // Add a recipient

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Factura Clinica Anguizola';
    $mail->Body    = '<div class="invoice-card">
      <img src="https://clinicaanguizola.herokuapp.com/Main/img/logo-color.png" style="width:50px;">
      <div class="invoice-title">
        <div id="main-title">
          <h4>Clinica Anguizola</h4>
        </div>
        <span id="date"><?php echo "$fecha";?></span>
      </div>

      <div class="invoice-details">
        <p></p>
        <table class="invoice-table">
          <thead>
            <tr class="row-data">
              <td>nombre: <?php echo "$nombre"; ?></td>
              <td></td>
            </tr>
          </thead>

          <tbody>
            <tr class="row-data">
              <td>Cantidad pagada:</td>
              <td><?php echo "$cantidad"; ?></td>
            </tr>
            <tr class="row-data">
              <td>Tipo de pago:</td>
              <td><?php echo "$tipopago"; ?></td>
            </tr>
            <tr class="row-data">
              <td>Saldo restante:</td>
              <td><?php echo "$saldo"; ?></td>
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
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
?>
