<?php $start_time = microtime(true); ?>

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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

//echo "<pre>";
require '../vendor/autoload.php';
include 'inc/checkCustomerALL.php';

//putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_lmbp.json');
putenv('GOOGLE_APPLICATION_CREDENTIALS=receiver-12000907-c74232cad6a6.json'); //receiver-12000907-c74232cad6a6.json

use Google\Cloud\BigQuery\BigQueryClient;

$projectId = "dfdp-meldm-lmcustomers";
//$projectId = "meldm-reports-dtep";

$bigQuery = new BigQueryClient();

$queryJobConfig = $bigQuery->query(
    '
    SELECT
    *
    FROM `dfdp-meldm-lmcustomers.customerTransform.LOYALTY_RATE`
    where 1=1
    AND CLIENT_ID IS NOT NULL limit 10;
    '
    // limit 100;
  //  'SELECT * FROM `meldm-reports-dtep.023_01_000001.PRX_VTE` limit 100;'
);
$queryResults = $bigQuery->runQuery($queryJobConfig);

 $file_name = "out/ads.csv";

 $list_of_orders = null;

$cnt = 0;
$cnt_emails = 0;
$loyalty_rate = null;

foreach ($queryResults as $row) {
  $cnt++;

    $customer_identifier =  $customer_identifier = $row['CARD_NUMBER'] ?: $row['CLIENT_ID'];
    $customer_data = checkCustomer($customer_identifier);

  $get_key = array_keys($customer_data);
  $key = $get_key[0];

  $customer_firstName = $customer_data[$key]['name'];
  $customer_surName = $customer_data[$key]['surname'];
  $customer_email = $customer_data[$key]['email'];
  $customer_city = $customer_data[$key]['city'];
  $customer_postalCode = $customer_data[$key]['postalCode'];
  $customer_phoneNumber_list = explode(";",$customer_data[$key]['phoneNumber']);

  $customer_phoneNumber1 = $customer_phoneNumber_list[0];
  $customer_phoneNumber2 = $customer_phoneNumber_list[1];

  $customer_email_list = explode(";",$customer_data[$key]['email']);

  $customer_email1 = $customer_email_list[0];
  $customer_email2 = $customer_email_list[1];



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

  $loyalty_rate = $row["LR"];


    $conversion_value = $row["CA"]->get();
    $conversion_time = $row["DAT_VTE"]->get()->format('Y-m-d h:i:s');
    $conversion_currency = "UAH";

    if (empty($customer_email)) { continue; }



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


 $fp = fopen('out/to_upload.csv', 'wb');
 $head_line = array("Parameters:TimeZone=+0200;LoyaltyRate=$loyalty_rate;TransactionUploadRate=1.0");
 $table_headers = array('Email','Email','Email','First Name','Last Name','City','State','Zip','Country','Phone Number','Phone Number','Phone Number','Conversion Name','Conversion Time','Conversion Value','Conversion Currency');

 fputcsv($fp, $head_line);
 fputcsv($fp, $table_headers,";");

foreach ( $list_of_orders as $line ) {

    fputcsv($fp, $line,";");
}

fclose($fp);




?>
This page was generated in <?php echo(number_format(microtime(true) - $start_time, 2)); ?> seconds.
