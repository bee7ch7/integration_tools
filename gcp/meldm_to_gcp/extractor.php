<?php
include('../../inc/databases.php');
include('../../inc/controller.php');



$lmbp_connection = new CustomerData();

$do = true;

$getList = $lmbp_connection->getCustomersForCurrentDate($do);

$date = date('Y-m-d');
$file = "in/lmbp_card_types_". $date .".csv";

  $completed_string = '"store_id";"client_id";"card_number";"created_at";"loyc_type"';
  file_put_contents("$file", "$completed_string\n",FILE_APPEND);

foreach ($getList as $customer) {

  $store = $customer['store_id'];
  $client_id = $customer['client_id'];
  $card_number = $customer['card_number'];
  $created_at = $customer['created_at'];
  $loyc_type = $customer['loyc_type'];

  $completed_string = '"'.$store.'";"'.$client_id.'";"'.$card_number.'";"'.$created_at.'";"'.$loyc_type.'"';

  file_put_contents("$file", "$completed_string\n",FILE_APPEND);

}

?>
