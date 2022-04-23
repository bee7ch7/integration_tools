<?php

include 'inc/databases.php';
include 'inc/controller.php';
include 'inc/checkCustomerALL.php';
include 'inc/functions.php';

include 'inc/keys.php';


$lmbp_connection = new CustomerData();
$response_lmbp_cnt = $lmbp_connection->getQtyCustomers();


$import_contacts_url = 'https://esputnik.com/api/v1/contacts';



$iterations = ceil($response_lmbp_cnt[0]['qty'] / 3000); // 1000
	echo "Pages to process (limit 100) - ".$iterations."<br>";

$limit = 3000; // 1000

for ($page = 1; $page <= $iterations; $page++) {

	$offset = ($page - 1) * $limit;
	echo "Starting - $limit $offset $page <br>";
	echo "<hr>";

	$response_lmbp = $lmbp_connection->getCustomers($limit, $offset);

	$request_entity = new stdClass();

	$cust_counter = 0;
	foreach ($response_lmbp as $single_customer) {



	$cust_fio =  explode(" ", $single_customer['client_name']);
	$cust_email =  $single_customer['email'];
	$cust_phone =  $single_customer['phone'];
	$cust_address =  $single_customer['address'];
	$cust_postal_code =  $single_customer['postal_code'];
	$cust_store_id =  $single_customer['store_id'];
	$client_id =  $single_customer['client_id'];


	switch($cust_store_id) {
		case 1:
			$store_address = "xxx";
			break;
		case 2:
			$store_address = "xxx";
			break;
		case 3:
				$store_address = "xxx";
				break;
		case 4:
				$store_address = "xxx";
				break;
		case 5:
				$store_address = "xxx";
				break;
		case 6:
				$store_address = "xxx";
				break;
		default:
		$store_address = "";
	}


	$last_name = $cust_fio[0];
	$first_name = $cust_fio[1];
	$email = $cust_email;	// email контакта
	$sms = $cust_phone;	// номер телефона

	//$store_address = "Magazin #".$cust_store_id;


	$contact = new stdClass();
	$contact->firstName = $first_name;
	$contact->lastName = $last_name;
	$contact->channels = array(
		array('type'=>'email', 'value' => $email),
	  array('type'=>'sms', 'value' => $sms)
		);
	$contact->address = array(

	    "address" => $cust_address,
	    "postcode" => $cust_postal_code
	  );

	$contact->fields = array(
	  array('id'=>'193016', 'value' => 'Да'),
	  array('id'=>'193019', 'value' => $store_address),
	  array('id'=>'205365', 'value' => $client_id)
	);

	$request_entity->contacts[] = $contact;

		$cust_counter++;
	}




	$request_entity->dedupeOn = 'email';
	$request_entity->contactFields = array('firstName', 'lastName', 'email');
	$request_entity->customFieldsIDs = array(193016, 193019, 205365);
	$request_entity->groupNames = array('KLM');


		echo "<hr>";
		//echo "$import_contacts_url, $user, $password, $page, $iterations, $cust_counter";
		 echo "Out $page of $iterations;$cust_counter";
		echo "<hr>";

	send_request($import_contacts_url, $request_entity, $user, $password, $page, $iterations, $cust_counter);


	$cust_counter = 0;


	//if ($page == 3) { break; }


}






exit();





?>
