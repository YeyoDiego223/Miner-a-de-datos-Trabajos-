<?php
require 'vendor/autoload.php';
use OTPHP\TOTP;

function verify_otp($secret, $otp) {
    $totp = TOTP::create($secret);
    return $totp->verify($otp);
}
?>
