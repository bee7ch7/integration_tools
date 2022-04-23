<?php
ini_set("SMTP", "xx.xx.xx.xx");
ini_set("sendmail_from", "xxx@meldm.ml");

$message = "UKRSIBBANK Export Failed.<br> $datetime - Помилка завантаження $file на FTP\n";
$headers = "From: xxx@meldm.ml";

mail("xxx@meldm.ml", "UKRSIBBANK Export Failed", $message, $headers);

?>