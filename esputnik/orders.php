<?php
include 'inc/databases.php';
include 'inc/controller.php';
include 'inc/checkCustomerALL.php';
include 'inc/keys.php';


$front_connection = new OrderTypes();

$response_front_cnt = $front_connection->getQtyOrders();


$iterations = ceil($response_front_cnt[0]['qty'] / 1000); // 1000
	echo "Pages to process (limit 100) - ".$iterations."<br>";

$limit = 1000; // 1000

for ($page = 1; $page <= $iterations; $page++) {

	$offset = ($page - 1) * $limit;
	echo "Starting - $limit $offset $page <br>";
	echo "<hr>";

$response_front = $front_connection->getOrders($limit, $offset);

$orders_list = new stdClass();

if (empty($response_front)) {
  $datetime = date("Y-m-d H:i:s");
  file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation.log',"$datetime;---;No orders at this time;\n",FILE_APPEND);
  exit;
}

foreach ($response_front as $order_fr) {

//  echo "<pre>";
//  print_r($order_fr);
//  echo "</pre>";

  $store = $order_fr['store_id'];
  $order = $order_fr['pyxis_order'];
  $order_full = $order_fr['pyxis_order_uid'];

  $status = $order_fr['status'];
  $sputnik_status = $order_fr['sputnik_status'];
  $date_creation = $order_fr['date'];
  $date_formated = $order_fr['date_formated'];
  $customer_number = $order_fr['customer_number'];
  $customer_phone = $order_fr['customer_phone'];
  $payment_type = $order_fr['payment_type'];
  $payment_method = $order_fr['payment_method'];
  $origin = $order_fr['origin'];
  $type = $order_fr['type'];
  $provider = $order_fr['provider'];
  $delivery_description = $order_fr['delivery_description'];

  $recipient_city = $order_fr['recipient_city'];
  $recipient_street_name = $order_fr['recipient_street_name'];
  $recipient_street_number = $order_fr['recipient_street_number'];
  $recipient_apartment = $order_fr['recipient_apartment'];

  $pickup_point_city = $order_fr['pickup_point_city'];
  $pickup_point_address = $order_fr['pickup_point_address'];

  $delivery_price = $order_fr['price'];
  $delivery_address_final = $order_fr['delivery_address_final'];



  $customer_data = checkCustomer($customer_number); // "1200493841"
    $get_key = array_keys($customer_data);
    $key = $get_key[0];

  $cust_email = $customer_data[$key]['email'];
  $cust_name = $customer_data[$key]['name'];
  $cust_surname = $customer_data[$key]['surname'];


  /*
  if ($provider == "NOVA_POSHTA" and $type == "PICKUP_POINT") {
      $delivery_address_string = "$pickup_point_city, $pickup_point_address";
  } elseif ($provider == "AVITEK_INVEST" and $type == "COURIER_ADDRESS") {
      $delivery_address_string = "$recipient_city, $recipient_street_name, $recipient_street_number, $recipient_apartment";
  } elseif ($provider == "LEROY_MERLIN" and $type == "STORE") {
      $delivery_address_string = "$recipient_city";
  } elseif ($provider == "NOVA_POSHTA" and $type == "COURIER_ADDRESS") {
      $delivery_address_string = "$recipient_city, $recipient_street_name, $recipient_street_number, $recipient_apartment";
  } else {
      $delivery_address_string ="";
  }
  */


  switch ($store) {
    case 1:
      $ip = "10.100.14.1";
      break;
    case 2:
      $ip = "10.100.14.2";
      break;
    case 3:
      $ip = "10.100.14.3";
      break;
    case 4:
      $ip = "10.100.14.4";
      break;
    case 5:
      $ip = "10.100.14.5";
      break;
    case 6:
      $ip = "10.100.14.6";
      break;
    case 901:
      $ip = "10.100.14.191";
      break;

    default:
      $ip = "10.100.14.1";
  }

  $pyxis_connection = new PyxisOrderDetails($ip);
  $response_pyxis = $pyxis_connection->getOrderProducts($store, $order);

  $order_total = $response_pyxis[0]['tra_mnt_total'];

  if ($order_total == 0 or empty($order_total) or is_null($order_total))
      {
        $new_order_total = $pyxis_connection->getOrderTotalForCanceledOrder($store, $order);

        if (!empty($new_order_total)) {
          $order_total = $new_order_total[0]['order_total'];
        }

      }


$order_data = new stdClass();

// ОБЯЗАТЕЛЬНЫЕ ПОЛЯ

$order_data->status = $sputnik_status; // Статус заказа. Возможные значения: INITIALIZED, IN_PROGRESS, CANCELLED, DELIVERED, ABANDONED_SHOPPING_CART. Для RFM анализа учитываются только заказы со статусом DELIVERED.
$order_data->date = $date_formated;  // Дата заказа в формате yyyy-MM-ddTHH:mm:ss.
$order_data->externalOrderId = $order_full;  // Идентификатор заказа в Вашей системе.
$order_data->externalCustomerId = $customer_number;  // Идентификатор клиента в Вашей системе. Если вы ходите идентифицировать клиентов по email или номеру телефона, продублируйте значение в этом поле и в соответствующем поле email или phone.
$order_data->totalCost = $order_total;  // Итоговая сумма по заказу.

// НЕОБЯЗАТЕЛЬНЫЕ ПОЛЯ

$order_data->email = $cust_email;  // Email клиента.
$order_data->phone = $customer_phone;  // Номер телефона клиента.
$order_data->firstName = $cust_name; // Имя клиента.
$order_data->lastName = $cust_surname; // Фамилия клиента.
$order_data->storeId = $store;  // Для ситуации, если Вам нужно хранить несколько наборов данных (по разным магазинам) в одной учетной записи eSputnik, иначе можно оставить пустым.
$order_data->shipping = $delivery_price;  // Стоимость доставки (дополнительная информация, при расчётах не учитывается).
$order_data->deliveryMethod = $delivery_description; // Способ доставки заказа.
$order_data->deliveryAddress = $delivery_address_final; // Адрес доставки заказа.
$order_data->taxes = "";  // Налоги (дополнительная информация, при расчётах не учитывается).
$order_data->paymentMethod = $payment_type; // Способ оплаты заказа.
$order_data->discount = "";  // Скидка (дополнительная информация, при расчётах не учитывается).
$order_data->restoreUrl = "";  // Cсылка на восстановление корзины, если необходима такая функциональность.
$order_data->statusDescription = "";  // Дополнительное описание статуса заказа.


//$orders_list->orders = array($order_data);

//$order->items = []; // Список продуктов, входящих в заказ.

foreach ($response_pyxis as $order_products) {

  $description = $order_products['description'];
  $nmc_designation = $order_products['nmc_designation'];
  $total_line = $order_products['total_line'];
  $description = $order_products['description'];
  $lm = $order_products['lm'];
  $quantity = $order_products['quantity'];

  $product_url = "https://www.meldm.ua/p/".$lm."_PimStd_Product";


	$order_data->items[] = array(

		// обязательные поля

		'name' => $description, // Название продукта.
		'cost' => $total_line, // Стоимость единицы продукта.
		'category' => $nmc_designation, // Категория продукта.
		'quantity' => $quantity, // Количество единиц товара.
		'externalItemId' => $lm, // Идентификатор продукта в Вашей системе.
		'url' => $product_url // url

	);

}



/*
  echo "<pre>";
  print_r($response_pyxis);
  echo "</pre>";
*/

  /*

    foreach ($response_pyxis as $order_products) {

                  echo "<pre>";
                  print_r($order_products);
                  echo "</pre>";
                }
    */

  $orders_list->orders[] = $order_data;

  //$order_data->items = null;
  $pyxis_connection = null;

  //break;

}

$cnt = json_encode($orders_list);
$cnt_orders = count($orders_list->orders);
//echo $cnt_orders;


//echo "<pre>";
//echo count($cnt_orders);
//print_r($cnt_orders);


//echo sizeof(json_decode($cnt_orders,1));
echo "<br>";
$ttt = json_encode($orders_list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


echo $ttt;
echo "</pre>";

//exit();


//var_dump($orders_list);
echo "<br>";
echo "<br>";
echo "<br>";
echo "<hr>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<pre>";


//exit();

$add_orders_url = 'https://esputnik.com/api/v1/orders';

$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orders_list));
//curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
curl_setopt($ch, CURLOPT_URL, $add_orders_url);

curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch,CURLOPT_USERPWD, $user.':'.$password);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_SSLVERSION, 6);

$output = curl_exec($ch);
$err = curl_error($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


curl_close($ch);

//echo "<hr>";
//print_r($output);
//echo "<hr>";
//echo $output;

if ($err) {
  echo "ERROR: with cURL Error #:" . $err;
  echo "<br>";

} else {

    if ($http_code == 200) {

      //echo "Замовлення $order успішно $response_ua";
      $datetime = date("Y-m-d H:i:s");
      file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation.log',"$datetime;$http_code;Uploaded orders: $page / $iterations $cnt_orders;\n",FILE_APPEND);
      //file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation_full.log',"$datetime;$http_code;Uploaded orders: $page / $iterations $cnt_orders;".json_encode($orders_list).";\n",FILE_APPEND);

    } else {

      //echo "Замовлення $order вже відмінено або підтверджено!";
      $datetime = date("Y-m-d H:i:s");
      file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation.log',"$datetime;$http_code;Error: $output;\n",FILE_APPEND);
      //file_put_contents('C:/inetpub/wwwroot/integrations/esputnik/logs/validation_full.log',"$datetime;$http_code;Error: $output;".json_encode($orders_list).";\n",FILE_APPEND);

    }

}

}






?>
