<?php
include '../libraries/phpqrcode/qrlib.php';

// Set the form URL
$form_link = "http://localhost/BARANGAY_CENSUS/src/views/census_form.php";

// Path to save the QR code
$qr_file = "../../public/images/census_qr.png";

// Generate the QR Code
QRcode::png($form_link, $qr_file, QR_ECLEVEL_L, 10);

echo "<h2>Scan the QR Code to Access the Census Form</h2>";
echo "<img src='".$qr_file."' />";
echo "<br><a href='".$form_link."'>Or click here to access the form</a>";
?>
