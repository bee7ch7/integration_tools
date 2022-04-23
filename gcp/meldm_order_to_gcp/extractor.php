<?php
include('../../inc/databases.php');
include('../../inc/controller.php');



$lmbp_connection = new LMorderReader();

$do = true;

$getList = $lmbp_connection->getDarkStoreOrders($do);

$date = date('Y-m-d');
$file = "in/order_status_". $date .".csv";

  $completed_string = '"store";"order";"delivery_provider";"payment_type";"payment_method";"delivery_type";"npn";"lmorder_creation";"kn_send_status_c";"kn_send_status_l";"kn_canceled";"status_timestamp";"status_code";"status_description";"delivery_cost";"estimate_delivery_date";"error_description";lmorder_created_at';
  file_put_contents("$file", "$completed_string\n",FILE_APPEND);


//   echo "<pre>";
//   print_r($getList);
//   echo "</pre>";

// exit();

foreach ($getList as $order_status) {

  $store = '"'.$order_status['store'].'"';
  $order = '"'.$order_status['order'].'"';
  $order_fr = '"'.$order_status['order_fr'].'"';
  $delivery_provider = '"'.$order_status['delivery_provider'].'"';
  $payment_type = '"'.$order_status['payment_type'].'"';
  $payment_method = '"'.$order_status['payment_method'].'"';
  $delivery_type = '"'.$order_status['delivery_type'].'"';
  $npn = '"'.$order_status['npn'].'"';
  $lmorder_creation = '"'.$order_status['lmorder_creation'].'"';
  $kn_send_status_c = '"'.$order_status['kn_send_status_c'].'"';
  $kn_send_status_l = '"'.$order_status['kn_send_status_l'].'"';
  $kn_canceled = '"'.$order_status['kn_canceled'].'"';
  $status_timestamp = '"'.$order_status['status_timestamp'].'"';
  $status_code = '"'.$order_status['status_code'].'"';
  $status_description = '"'.$order_status['status_description'].'"';
  $delivery_cost = '"'.$order_status['delivery_cost'].'"';
  $estimate_delivery_date = '"'.$order_status['estimate_delivery_date'].'"';
  $error_description = '"'.$order_status['error_description'].'"';
  $lmorder_created_at = '"'.$order_status['lmorder_created_at'].'"';

  $completed_string = "$store;$order;$order_fr;$delivery_provider;$payment_type;$payment_method;$delivery_type;$npn;$lmorder_creation;$kn_send_status_c;$kn_send_status_l;$kn_canceled;$status_timestamp;$status_code;$status_description;$delivery_cost;$estimate_delivery_date;$error_description;$lmorder_created_at";

  file_put_contents("$file", "$completed_string\n",FILE_APPEND);

}


?>
