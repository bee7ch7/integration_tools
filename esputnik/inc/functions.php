<?php

function send_request($url, $json_value, $user, $password, $page, $iterations, $cust_counter) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_value));
	// curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_USERPWD, $user.':'.$password);

  curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);


	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_SSLVERSION, 6);
	$output = curl_exec($ch);

	$err = curl_error($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);
	echo($output);

	if ($err) {
	  echo "ERROR: with cURL Error #:" . $err;
	  echo "<br>";

	} else {

	    if ($http_code == 200) {

	      //echo "Замовлення $order успішно $response_ua";
	      $datetime = date("Y-m-d H:i:s");
	      file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation_clients.log',"$datetime;$http_code;Uploaded customers: ;$output;Out $page of $iterations;$cust_counter;\n",FILE_APPEND);

	    } else {

	      //echo "Замовлення $order вже відмінено або підтверджено!";
	      $datetime = date("Y-m-d H:i:s");
	      file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation_clients.log',"$datetime;$http_code;Error: $output;Out $page of $iterations;$cust_counter;\n",FILE_APPEND);

	    }
	}
}

?>
