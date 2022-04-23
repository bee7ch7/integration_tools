<?php
$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
  if (empty($_POST['selected_date_from']) or empty($_POST['selected_date_to'])) {
    $selected_date_from = date("d-m-Y");
    $selected_date_to = date("d-m-Y",strtotime($date1 . "+1 days"));

  } else {
    $selected_date_from=$_POST['selected_date_from'];
    $selected_date_to=$_POST['selected_date_to'];
  }
} elseif ($method == "GET") {
  if (empty($_GET['selected_date_from']) or empty($_GET['selected_date_to'])) {
    $selected_date_from = date("d-m-Y");
    $selected_date_to = date("d-m-Y",strtotime($date1 . "+1 days"));

  } else {
    $selected_date_from=$_GET['selected_date_from'];
    $selected_date_to=$_GET['selected_date_to'];
  }
} else {
	echo "undefined method";
}

if ($_POST['access_token']) {

  $access_token = $_POST['access_token'];

}


?>

<html>
<head>
  <link rel="stylesheet" href="css/jquery-ui.css">
  <link rel="stylesheet" href="css/check.css">
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <script src="js/jquery-1.12.4.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/datepicker.js"></script>
</head>


<?php

include 'keys.php';


function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


$myuuid = guidv4();




$code = $_GET['code'];

// $selected_date_from = $_POST['selected_date_from'];
// $selected_date_to = $_POST['selected_date_to'];






if (empty($code)) {

?>

<div style='padding-top: 40px;' class="d-flex justify-content-center">
<a class="btn btn-success" role="button" href="https://business.ukrsibbank.com/morpheus/authorize?client_id=<?php echo $client_id;?>&redirect_uri=http://integrations.meldm.ml/ukrsib/auth.php&response_type=code&selected_date_from=<?php echo $selected_date_from;?>&selected_date_to=<?php echo $selected_date_to;?>" target='_blank'>Авторизуватись в УКРСИБ <i class="fa fa-money"></i></a>
</div>

<?php
} else {
?>
<div style='padding-top: 40px;' class="d-flex justify-content-center">
<a class="btn btn-success" role="button" href="https://business.ukrsibbank.com/morpheus/authorize?client_id=<?php echo $client_id;?>&redirect_uri=http://integrations.meldm.ml/ukrsib/auth.php&response_type=code&selected_date_from=<?php echo $selected_date_from;?>&selected_date_to=<?php echo $selected_date_to;?>" target='_blank'>Повторити процедуру <i class="fa fa-money"></i></a>
</div>


<?php

}


//echo $client_code."<br>";
if (!empty($code)) {

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://business.ukrsibbank.com/morpheus/token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,

  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,

  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "grant_type=authorization_code&code=$code&client_id=$client_id&client_secret=$client_secret&redirect_uri=http%3A%2F%2Fintegrations.meldm.ml%2Fukrsib%2Fauth.php",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: TS01f17414=0128d3fedb1bfd6bf9607d0ebb301bd3e64daec79835313840899126c88ff34ee32d19a0f0578475b13902774ab1a877bce21a1e01; TS01f17414028=01c44edd2ca70f0a6a59e9e1c3228ab3dfce3b8e478deefb89dabc129bdaf9b0773db65af81dd60bf87b365caae41a21222ec9df4e'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;

$result = json_decode($response, true);

// echo "<pre>";
// print_r($result);
// echo "</pre>";

$access_token = $result['access_token'];

}

if (!empty($access_token)) {

?>

<div style='padding-top: 40px;' class="d-flex justify-content-center">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table>
    <tr>
      <td>
        <input id="date_from" type="text" style="text-align: center;" name="selected_date_from" readonly="readonly" value="<?php echo $selected_date_from;?>">
      </td>
      <td>
        <input id="date_to" type="text" style="text-align: center;"  name="selected_date_to" readonly="readonly" value="<?php echo $selected_date_to;?>">
      </td>
    </tr>
    <tr>
      <td>
        <input id="statements" type="radio" name="export_type" <?php  if ($_POST['export_type'] == 'statements') {echo "checked";} ?> value="statements" />
        <label for="statements">Рахунки</label>
      </td>
      <td>
        <input id="turnover" type="radio" name="export_type" <?php  if ($_POST['export_type'] == 'turnover') {echo "checked";} ?> value="turnover" />
        <label for="turnover">Оборот</label>
      </td>
    </tr>
      <tr>
        <td colspan="2">
          <input type="hidden" name="access_token" value="<?php echo $access_token; ?>" />
          <input type="submit" name="submit" style="width: 100%;" class='btn btn-warning' readonly="readonly" value="Підтвердити експорт">
        </td>
      </tr>
  </table>
</form>
</div>


<?php
}




if (!empty($_POST['access_token']) and !empty($selected_date_from) and !empty($selected_date_to) ) {



  $access_token = $_POST['access_token'];
  $dateFrom = 1000 * strtotime($selected_date_from);
  $dateTo = 1000 * strtotime($selected_date_to);
  //echo "$dateFrom <br> $dateTo";

  $account1 = "x"."_UAH";
  $account2 = "x"."_UAH";
  $account3 = "x"."_UAH";
  $account4 = "x"."_UAH";

  $x_req = $myuuid;

  $firstResult = "0";
  $maxResult = "1000";

  $encode = $account1.",".$account2.",".$account3.",".$account4."|".$dateFrom."|".$dateTo."|".$firstResult."|".$maxResult;

  // echo "<hr>";
  // echo $encode;
  // echo "<hr>";

  $source = $encode;


  $binary_signature = "";

  // At least with PHP 5.2.2 / OpenSSL 0.9.8b (Fedora 7)
  // there seems to be no need to call openssl_get_privatekey or similar.
  // Just pass the key as defined above
  $passphrase = 'SecretPassword';
  $res = openssl_get_privatekey($private_key,$passphrase);

  openssl_sign($source, $binary_signature, $res, OPENSSL_ALGO_SHA512);

  // echo "<hr>";
  // echo $binary_signature;
  // echo "<hr>";

  $binary_signature = base64_encode($binary_signature);

  // echo "<hr>";
  // echo $binary_signature;
  // echo "<hr>";

  if ($_POST['export_type'] == 'statements') {

  $query_endopoint = "https://business.ukrsibbank.com/morpheus/statements";

  } elseif ($_POST['export_type'] == 'turnover') {

    $query_endopoint = "https://business.ukrsibbank.com/morpheus/turnovers";

  } else {

    exit('how did you get so far?');
  }

  $data_array = array(
    "dateFrom" => $dateFrom,
    "dateTo" => $dateTo,
    "accounts" => "$account1,$account2,$account3,$account4",
    "firstResult" => $firstResult,
    "maxResult" => $maxResult
  );

  $post = json_encode($data_array);


$myuuid = guidv4();
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $query_endopoint,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',

  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,

  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $post,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $access_token",
    "X-Request-ID: $myuuid",
    "Sign: $binary_signature",
    'Content-Type: application/json',
    'Cookie: TS01f17414=0128d3fedb1bfd6bf9607d0ebb301bd3e64daec79835313840899126c88ff34ee32d19a0f0578475b13902774ab1a877bce21a1e01; TS01f17414028=01c44edd2ca70f0a6a59e9e1c3228ab3dfce3b8e478deefb89dabc129bdaf9b0773db65af81dd60bf87b365caae41a21222ec9df4e'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

//echo "<br><hr>";
//echo "https://business.ukrsibbank.com/morpheus/statements";
//echo $response;
//echo "<hr>";

$response_array = json_decode($response, 1);


 // echo "<pre>";
 // print_r($response_array);
 // echo "</pre>";



echo "<div style='padding-top: 40px;' class='d-flex justify-content-center'>";
echo "Завантажено ".$response_array['total']. " рахунків.";
echo "</div>";


 if ($_POST['export_type'] == 'statements') {


echo "<table class='table table-sm table-bordered'";
$i = 1;
foreach ($response_array['data'] as $single_invoice) {
echo "<tr>";
                  echo "<td>".$i."</td>";
                   echo "<td>".$single_invoice['amountLocal']."</td>";
                   echo "<td>".$single_invoice['clientBankName']."</td>";
                   echo "<td>".$single_invoice['clientCode']."</td>";
                   echo "<td>".$single_invoice['clientIBAN']."</td>";
                   echo "<td>".$single_invoice['clientName']."</td>";
                   echo "<td>".$single_invoice['correspondentBankMFO']."</td>";
                   echo "<td>".$single_invoice['correspondentBankName']."</td>";
                   echo "<td>".$single_invoice['correspondentCode']."</td>";
                   echo "<td>".$single_invoice['correspondentIBAN']."</td>";
                   echo "<td>".$single_invoice['correspondentName']."</td>";
                   echo "<td>".$single_invoice['credit']."</td>";
                   echo "<td>".$single_invoice['currency']."</td>";
                   echo "<td>".$single_invoice['dateValue']."</td>";
                   echo "<td>".$single_invoice['debit']."</td>";
                   echo "<td>".$single_invoice['docDate']."</td>";
                   echo "<td>".$single_invoice['docNumber']."</td>";
                   echo "<td>".$single_invoice['paymentPurpose']."</td>";
                   echo "<td>".$single_invoice['provDate']."</td>";
echo "</tr>";
$i++;
}
echo "</table>";

} elseif ($_POST['export_type'] == 'turnover') {

  echo "<table class='table table-sm table-bordered'";
  $i = 1;
  foreach ($response_array['data'] as $single_invoice) {
  echo "<tr>";
                    echo "<td>".$i."</td>";
                  echo "<td>".$single_invoice['clientName']."</td>";
                  echo "<td>".$single_invoice['currency']."</td>";
                  echo "<td>".$single_invoice['date']."</td>";
                  echo "<td>".$single_invoice['iban']."</td>";
                  echo "<td>".$single_invoice['incomingRest']."</td>";
                  echo "<td>".$single_invoice['incomingRestLocal']."</td>";
                  echo "<td>".$single_invoice['outgoingRest']."</td>";
                  echo "<td>".$single_invoice['outgoingRestLocal']."</td>";
                  echo "<td>".$single_invoice['turnoverCredit']."</td>";
                  echo "<td>".$single_invoice['turnoverCreditLocal']."</td>";
                  echo "<td>".$single_invoice['turnoverDebit']."</td>";
                  echo "<td>".$single_invoice['turnoverDebitLocal']."</td>";
echo "</tr>";
$i++;
}
echo "</table>";

}
if ($response_array['data'] ) {
  $datetime = date("Ymd");
	file_put_contents("out/".$selected_date_from."_".$selected_date_to.".json", $response);


  sleep(1);

 $ftp_server="xxx.xxx.xxx.xxx";
 $ftp_user_name="xxx";
 $ftp_user_pass="xxx";

// get list of files in folder
$path = 'out';
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));


 foreach ($files as $file_order) {

		// specify each file to copy
		 $file = "out/".$file_order;
		 $remote_file = "/home/ftp/pub/UkrSibJSON/1/".$file_order;

		 // set up basic connection
		 $conn_id = ftp_connect($ftp_server);

		 // login with username and password
		 $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		 ftp_pasv($conn_id, TRUE);
		 // upload a file
		 if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
			echo "";
			//exit;
		 } else {
			$datetime = date("Y-m-d H:i:s");
			echo "$datetime - Помилка завантаження $file\n";
			file_put_contents("logs.log","$datetime - Помилка завантаження $file\n",FILE_APPEND);
		   // exit;
		   include('MailNotification.php');
			exit();
			}
		 // close the connection
		 ftp_close($conn_id);
		 $datetime = date("Y-m-d H:i:s");
		 echo "$datetime - $file_order відправлено на FTP\n";
		 file_put_contents ("logs.log","$datetime - $file_order відправлено на FTP\n",FILE_APPEND);
		 rename($file, "archive/".$file_order);

}




  }



} /*else {

  echo "<div style='padding-top: 40px;' class='d-flex justify-content-center'>";
  echo "Авторизуйся в УКРСИБ";
  echo "</div>";


}
*/

 ?>
