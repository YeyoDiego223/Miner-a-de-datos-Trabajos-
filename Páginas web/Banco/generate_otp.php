<?php
include 'db.php';
require 'vendor/autoload.php';

use OTPHP\TOTP;

if (isset($_GET['correo'])) {
    $correo = $_GET['correo'];

    $sql = "SELECT otp_secret FROM users WHERE correo='$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $totp = TOTP::create($user['otp_secret']);
        echo $totp->now();
    } else {
        echo "Correo no encontrado.";
    }
}
?>
 