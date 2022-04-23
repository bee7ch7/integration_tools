<?php

include 'keys.php';

$ch = curl_init();
//curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
curl_setopt($ch, CURLOPT_URL, 'https://esputnik.com/api/v1/account/info');
curl_setopt($ch,CURLOPT_USERPWD, $user.':'.$password);

curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch, CURLOPT_SSLVERSION, 6);

$output = curl_exec($ch);
echo "<pre>";
print_r($output);
echo "</pre>";
curl_close($ch);


?>
