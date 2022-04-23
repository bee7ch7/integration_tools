<?php 

$start_time = microtime(true);

session_start();

if(!$_SESSION['integration_bigquery_google_ads']) {
  header("location: index.php");
  die();
}
session_write_close();


$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
	if (empty($_POST['date_from']) or empty($_POST['date_to'])) {
		$date_from = date("Y-m-d");
		$date_to = date("Y-m-d",strtotime($date1 . "+1 days"));

	} else {
		$date_from=$_POST['date_from'];
		$date_to=$_POST['date_to'];
	}
} elseif ($method == "GET") {
if (empty($_GET['date_from']) or empty($_GET['date_to'])) {
	$date_from = date("Y-m-d");
	$date_to = date("Y-m-d",strtotime($date1 . "+1 days"));
} else {
	$date_from=$_GET['date_from'];
	$date_to=$_GET['date_to'];
}
} else {
	echo "undefined method";
}


// $date_from = date("Y-m-d",strtotime($date1 . "-14 days"));
// $date_to = date("Y-m-d");


?>

<html>
<title>[LM] GoogleAds</title>
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

<div class="head">
<div class="img">
	<a href="googleAds.php">
    <img src="https://lmorder.meldm.ml/pics/logo.png"/>
	</a>
</div>
<div class="text">
&#8203;
</div>

<a class="logout" href="https://idpb2e.meldm.ml/idp/startSLO.ping?TargetResource=https://integrations.meldm.ml/gcp/bigQuery/logout.php">Вихід</a><a class="logout" style="color:black;"><?php echo $_SESSION['cn']; ?></a>GoogleAds
</div>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php


echo "<div style=' position: absolute; top: 20%; left: 50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);' class='d-flex justify-content-center'>";
echo "<table class='table table-bordered table-sm' >";
      ?>

      <tr>
      <td>Run query between: </td>
		 <td>
		<input id="date_from" type="text" name="date_from" readonly="readonly" value="<?php echo $date_from;?>">
		</td>
		<td>
		<input id="date_to" type="text" name="date_to" readonly="readonly" value="<?php echo $date_to;?>">
		</td>
      </tr>
      <tr>
      <td colspan='3' style='text-align: center;'>



          <input type="submit" name="submit" value="Run" class='btn btn-success'/>




      <?php

      echo "</td>";
      echo "</tr>";
      echo "</table>";

      echo "</div>";
      echo "</form>";

if ($_POST['submit'] !== 'Run') {

exit();

}

;
require '../vendor/autoload.php';
include 'inc/checkCustomerALL.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=receiver-12000907-c74232cad6a6.json'); //receiver-12000907-c74232cad6a6.json

use Google\Cloud\BigQuery\BigQueryClient;

$projectId = "dfdp-meldm-lmcustomers";

$bigQuery = new BigQueryClient();


$queryJobConfig = $bigQuery->query(
    "SELECT
    '$date_from'  || ' - ' || '$date_to' as search_between,
    ROUND(
      (SELECT COUNT(DISTINCT (NUM_TIC))
      FROM `dfdp-meldm-lmcustomers.customerTransform.SALES_LOYL_2`
        WHERE 1=1
        and DAT_VTE between '$date_from' and '$date_to'
        and SALES_CHAN='STORE'
        AND LOYC_TYPE IS NOT NULL ) /
      (SELECT COUNT(DISTINCT (NUM_TIC))
      FROM `dfdp-meldm-lmcustomers.customerTransform.SALES_LOYL_2`
        where 1=1
        and DAT_VTE between '$date_from' and '$date_to'
      ),2) as LR2,

    *
    FROM `dfdp-meldm-lmcustomers.customerTransform.SalesCustomerID`
    where 1=1

      and SALES_CHAN='STORE'
      and NUM_TYPCLI <> 3
      and EMAIL_1 is not null
      and DAT_VTE between '$date_from' and '$date_to'
      and CA > 0
      ;
    "

);
$queryResults = $bigQuery->runQuery($queryJobConfig);

 $file_name = "out/ads.csv";

 $list_of_orders = null;


echo "<table style='margin-top: 10%;' class='table table-bordered table-sm'>";
echo "<tr>
<td>Email</td>
<td>Email</td>
<td>Email</td>
<td>First Name</td>
<td>Last Name</td>
<td>City</td>
<td>State</td>
<td>Zip</td>
<td>Country</td>
<td>Phone Number</td>
<td>Phone Number</td>
<td>Phone Number</td>
<td>Conversion Name</td>
<td>Conversion Time</td>
<td>Conversion Value</td>
<td>Conversion Currency</td>
<tr>
";

$cnt = 0;
$cnt_emails = 0;
$loyalty_rate = null;



foreach ($queryResults as $row) {
  $cnt++;

  $customer_firstName = $row['LIB_PRE'];
  $customer_surName = $row['LIB_NOM'];

  $customer_city = "";
  $customer_postalCode = "";


  $customer_phoneNumber1 = $row['PHONE_1'];
  $customer_phoneNumber2 = "";

  $customer_email1 = $row['EMAIL_1'];
  $customer_email2 = "";



    if (strlen($customer_phoneNumber1) == 10) {
        $customer_phoneNumber1 = "38".$customer_phoneNumber1;
    } elseif (strlen($customer_phoneNumber1) >= 13 or strlen($customer_phoneNumber1) <= 9)  {
        $customer_phoneNumber1 = '';
    } else {
        $customer_phoneNumber1 = $customer_phoneNumber1;
    }

    if (strlen($customer_phoneNumber2) == 10) {
        $customer_phoneNumber2 = "38".$customer_phoneNumber2;
    } elseif (strlen($customer_phoneNumber2) >= 13 or strlen($customer_phoneNumber2) <= 9) {
        $customer_phoneNumber2 = '';
    } else {
        $customer_phoneNumber2 = $customer_phoneNumber2;
    }

  $loyalty_rate = $row["LR2"];

    $conversion_value = $row["CA"]->get();
    $conversion_time = $row["DAT_VTE"]->get()->format('m-d-Y h:i:s');
    $conversion_currency = "UAH";


      $list_of_orders[] = array($customer_email1, $customer_email2, "", $customer_firstName, $customer_surName, $customer_city, "",$customer_postalCode, "UA",
                      $customer_phoneNumber1, $customer_phoneNumber2, "", "Sale", $conversion_time, $conversion_value, "UAH"
                      );



      unset($customer_identifier);
      unset($customer_data);
      unset($get_key);
      unset($key);
      unset($customer_firstName);
      unset($customer_surName);

      unset($customer_email_list);
      unset($customer_email1);
      unset($customer_email2);
      unset($customer_city);
      unset($customer_postalCode);
      unset($customer_phoneNumber_list);

      unset($customer_phoneNumber1);
      unset($customer_phoneNumber2);
      unset($conversion_value);
      unset($conversion_time);
      unset($conversion_currency);

      $cnt_emails++;


}

echo "<tr>";
echo "<td colspan='8'>";
echo $cnt;
echo "</td>";
echo "<td colspan='8'>";
echo $cnt_emails;
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan='16' style='text-align: center'>";
echo "<a href=\"out/to_upload.csv\">download csv</a>";
echo "</td>";
echo "</tr>";
echo "</table>";



 $fp = fopen('out/to_upload.csv', 'wb');
 $head_line = array("Parameters:TimeZone=+0200;LoyaltyRate=$loyalty_rate;TransactionUploadRate=1.0,,,,,,,,,,,,,,,");
 $table_headers = array('Email','Email','Email','First Name','Last Name','City','State','Zip','Country','Phone Number','Phone Number','Phone Number','Conversion Name','Conversion Time','Conversion Value','Conversion Currency');

 fwrite($fp, implode(',', $head_line) . "\r\n");
 fwrite($fp, implode(',', $table_headers) . "\r\n");


foreach ( $list_of_orders as $line ) {

    fwrite($fp, implode(',', $line) . "\r\n");
}

fclose($fp);
unset($_POST);

?>

<p style='text-align: center;'>
This page was generated in <?php echo(number_format(microtime(true) - $start_time, 2)); ?> seconds.
</p>
