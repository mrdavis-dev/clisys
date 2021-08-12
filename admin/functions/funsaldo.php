<?php
error_reporting(0);
//fetch.php
// include("conexion/config.php");
$t = $_GET["trata"];
$c = $_GET["cedula"];

$saldo = "SELECT * from pago where id = (select max(id) from pago where tratamiento ='".$t."' AND cedula = '".$c."')";

$result = $db->query($saldo);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo '
        <label for="">Saldo</label>
        <input type="text" required class="form-control border" value=' . $row["saldo"] . ' autocomplete="off" name="saldo">';
    }
} else {
    echo '
    <label for="">Saldo</label>
    <input type="text" required class="form-control border" autocomplete="off" value="0" name="saldo">
    ';
}
$db->close();
?>