<?php
function checkCustomer($single_customer) {

  $params3 = array (
  		'arg0' => array(
  		'searchCriteriaDTO' => '',
  		'discriminatingCriteria' => $single_customer,
  		'buNumber' => '23',
  		'any' => ''
  	)
  );


  //echo "<pre>";
  //print_r($params);


  $wsdl='https://lmorder.meldm.ml/access/WS/customerGlobalWS.xml'; // link to my test server with xml sent to me by Mohamed

  ini_set('soap.wsdl_cache_enabled', 0);
  ini_set('soap.wsdl_cache_ttl', 900);
  ini_set('default_socket_timeout', 600);

  $options = array(
  'uri'=>'http://schemas.xmlsoap.org/wsdl/soap/',
  'style'=>SOAP_RPC,
  'use'=>SOAP_ENCODED,
  'soap_version'=>SOAP_1_1,
  'cache_wsdl'=>WSDL_CACHE_NONE,
  'connection_timeout'=>600,
  'trace'=>true,
  'encoding'=>'UTF-8',
  'exceptions'=>true,
  'stream_context'=> stream_context_create(array(
              'ssl'=> array(
                      'verify_peer'=>false,
                      'verify_peer_name'=>false
                  )
              ))

  );

  try {
  $soap = new SoapClient($wsdl, $options);

  $data = $soap->globalSearch($params3);

  }

  catch(Exception $e) {
  print_r($e->getMessage());
  }

  //var_dump($result);

  //echo $data['return'];
  //echo $data;

  $searchResult = json_decode(json_encode($data), True);

	$customers_found = array();

	if (isset($searchResult['return']['households']) and isset($searchResult['return']['legalEntities'])) {
			$cust_type = "mixed type of customers; multiple customers";
			// $customer = $searchResult['return']['customersFound'];
      $customer = $searchResult['return']['customersFound'][0] ?: $searchResult['return']['customersFound'];

			$corporateName = $searchResult['return']['legalEntities']['legalIdentity']['corporateName'];
			//array_push($customers_found,$customer);
			$customers_found = [];
			$customers_found[$customer]['type'] = $cust_type;
			$customers_found[$customer]['customer_number'] = $customer;
			$customers_found[$customer]['corporateName'] = $corporateName;


		}  elseif (isset($searchResult['return']['legalEntities']) and !isset($searchResult['return']['legalEntities'][0])) {


			$cust_type = "legal";
		//	$customer = $searchResult['return']['customersFound'];
    /*
      if (isset($searchResult['return']['customersFound'][0]) {

          $customer = $searchResult['return']['customersFound'][0];
      } else {
          $customer = $searchResult['return']['customersFound'];
      }
      */

       $customer = $searchResult['return']['customersFound'][0] ?: $searchResult['return']['customersFound'];

			$cust_name = $searchResult['return']['legalEntities']['contacts']['identity']['firstName'];
			$cust_surname = $searchResult['return']['legalEntities']['contacts']['identity']['name'];

			$corporateName = $searchResult['return']['legalEntities']['legalIdentity']['corporateName'];
			$corporateNameFull = $searchResult['return']['legalEntities']['addresses']['detail']['comment'];
			$cust_store = $searchResult['return']['legalEntities']['legalIdentity']['managementEntity'];
			$cust_address_street = $searchResult['return']['legalEntities']['addresses']['detail']['line1'];
			$cust_address_city = $searchResult['return']['legalEntities']['addresses']['detail']['city'];
			$cust_address_postalCode = $searchResult['return']['legalEntities']['addresses']['detail']['postalCode'];
			$cust_subscriptions = $searchResult['return']['legalEntities']['contacts']['optins'];



			if (isset($searchResult['return']['legalEntities']['contacts']['identity']['title'])) {
				$sex = $searchResult['return']['legalEntities']['contacts']['identity']['title'];
					if ($sex == 1) {
						$sex = "Пан";
					} elseif ($sex == 2) {
						$sex = "Пані";
					} else {
						$sex = "?";
					}
			} else {
				$sex = $searchResult['return']['legalEntities']['contacts'][0]['identity']['title'];
					if ($sex == 1) {
						$sex = "Пан";
					} elseif ($sex == 2) {
						$sex = "Пані";
					} else {
						$sex = "?";
					}

			}



					if (isset($searchResult['return']['legalEntities']['contacts']['communications']['detail']['value']))
									{

										if ($searchResult['return']['legalEntities']['contacts']['communications']['detail']['type'] == 3) {
											$cust_email = $searchResult['return']['legalEntities']['contacts']['communications']['detail']['value']; // email
											} else {
											$cust_phoneNumber = $searchResult['return']['legalEntities']['contacts']['communications']['detail']['value']; //phone number
											}


									} else {

											$cc2 = 0;

											while (isset($searchResult['return']['legalEntities']['contacts']['communications'][$cc2]))
											{

											if ($searchResult['return']['legalEntities']['contacts']['communications'][$cc2]['detail']['type'] == 3) {
											$cust_email .= $searchResult['return']['legalEntities']['contacts']['communications'][$cc2]['detail']['value'].";"; // email
											} else {
											$cust_phoneNumber .= $searchResult['return']['legalEntities']['contacts']['communications'][$cc2]['detail']['value'].";"; //phone number
											}

											$cc2++;
											}

									}



			if (isset($cust_subscriptions)) {

					$info = 0;
					while ($cust_subscriptions[$info])
						{
							if ($cust_subscriptions[$info]['communicationType'] == 1) {
									//$subscription_phone = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_phone = 1;} else {$subscription_phone = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 2 ) {
									//$subscription_fax = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_fax = 1;} else {$subscription_fax = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 3) {
									//$subscription_sms = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_sms = 1;} else {$subscription_sms = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 4) {
									//$subscription_email = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_email = 1;} else {$subscription_email = 0;}
								}

							$info++;
						}
					} else {
						$subscription_phone = 0;
						$subscription_fax = 0;
						$subscription_sms = 0;
						$subscription_email = 0;
					}


				if (isset($searchResult['return']['legalEntities']['externalIdentifiers'][0])) {

										$ext = 0;
										while ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]) {

											if ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['type'] == 5){
												$cust_card = $searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['type'] == 2){
												$edrpou = $searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['type'] == 1){
												$ipn = $searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['type'] == 8){
												$vat_id = $searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['type'] == 999){
												$cust_old_number = $searchResult['return']['legalEntities']['externalIdentifiers'][$ext]['value'];

											}
										$ext++;
										}

									}


			/////////////////////
			if (!isset($searchResult['return']['legalEntities']['classifications']['type']))
									{
											$cc2 = 0;

											while (isset($searchResult['return']['legalEntities']['classifications'][$cc2]))
											{


											if ($searchResult['return']['legalEntities']['classifications'][$cc2]['type'] == 4 and $searchResult['return']['legalEntities']['classifications'][$cc2]['code'] == 9)
												{
													$cust_loyc_id = "ПРО";

												} elseif ($searchResult['return']['legalEntities']['classifications'][$cc2]['type'] == 4 and $searchResult['return']['legalEntities']['classifications'][$cc2]['code'] == 8) {
													$cust_loyc_id = "КЛМ";

												}


											$cc2++;
											}
									} else {
										if ($searchResult['return']['legalEntities']['classifications']['type'] == 4
												and
												$searchResult['return']['legalEntities']['classifications']['code'] == 9)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['legalEntities']['classifications']['type'] == 4
												and
												$searchResult['return']['legalEntities']['classifications']['code'] == 8
												) {
													$cust_loyc_id = "КЛМ";
												}
									}
			////////////////////
			//array_push($customers_found,$customer);
		//	array_push($customers_found[$customer]);
      $customers_found = [];
			$customers_found[$customer]['type'] = $cust_type;
			$customers_found[$customer]['customer_number'] = $customer;
			$customers_found[$customer]['cust_old_number'] = $cust_old_number;
			$customers_found[$customer]['sex'] = $sex;
			$customers_found[$customer]['name'] = $cust_name;
			$customers_found[$customer]['surname'] = $cust_surname;
			$customers_found[$customer]['corporateName'] = $corporateName;
			$customers_found[$customer]['store'] = $cust_store;
			$customers_found[$customer]['street'] = $cust_address_street;
			$customers_found[$customer]['city'] = $cust_address_city;
			$customers_found[$customer]['postalCode'] = $cust_address_postalCode;
			$customers_found[$customer]['corporateNameFull'] = $corporateNameFull;
			$customers_found[$customer]['cust_card'] = $cust_card;
			$customers_found[$customer]['cust_loyc_id'] = $cust_loyc_id;
			$customers_found[$customer]['edrpou'] = $edrpou;
			$customers_found[$customer]['ipn'] = $ipn;
			$customers_found[$customer]['vat_id'] = $vat_id;
			$customers_found[$customer]['phoneNumber'] = $cust_phoneNumber;
			$customers_found[$customer]['email'] = $cust_email;

			$customers_found[$customer]['subscription_email'] = $subscription_email;
			$customers_found[$customer]['subscription_fax'] = $subscription_fax;
			$customers_found[$customer]['subscription_phone'] = $subscription_phone;
			$customers_found[$customer]['subscription_sms'] = $subscription_sms;


	  } elseif (isset($searchResult['return']['legalEntities']) and isset($searchResult['return']['legalEntities'][0])) {
	    $cust_type = "legal; mutliple entities";

			$customer = $searchResult['return']['customersFound'][0] ?: $searchResult['return']['customersFound'];

			$corporateName = $searchResult['return']['legalEntities']['legalIdentity']['corporateName'];
			//array_push($customers_found,$customer);
			array_push($customers_found[$customer]);
			$customers_found[$customer]['type'] = $cust_type;
			$customers_found[$customer]['customer_number'] = $customer;
			$customers_found[$customer]['corporateName'] = $corporateName;

	  } elseif (isset($searchResult['return']['households']) and !isset($searchResult['return']['households'][0])) {
			$cust_type = "natural";

			$customer = $searchResult['return']['customersFound'][0] ?: $searchResult['return']['customersFound'];

			$cust_name = $searchResult['return']['households']['contacts']['identity']['firstName'];
			$cust_surname = $searchResult['return']['households']['contacts']['identity']['name'];

      if (empty($cust_name) and empty($cust_surname)) {

        $cust_name = $searchResult['return']['households']['contacts'][0]['identity']['firstName'];
  			$cust_surname = $searchResult['return']['households']['contacts'][0]['identity']['name'];

      }


			$cust_othername = $searchResult['return']['households']['contacts']['identity']['otherName'];
			$cust_date = $searchResult['return']['households']['contacts']['identity']['birthDate'];
			$cust_birth = substr($cust_date,0,10);
			$cust_store = $searchResult['return']['households']['contacts']['identity']['managementEntity'];
			$cust_address_street = $searchResult['return']['households']['addresses']['detail']['line1'];
			$cust_address_id = $searchResult['return']['households']['addresses']['id'];
			$cust_address_city = $searchResult['return']['households']['addresses']['detail']['city'];
			$cust_address_postalCode = $searchResult['return']['households']['addresses']['detail']['postalCode'];
			$cust_subscriptions = $searchResult['return']['households']['contacts']['optins'];


			if (isset($searchResult['return']['households']['contacts']['identity']['title'])) {
				$sex = $searchResult['return']['households']['contacts']['identity']['title'];
					if ($sex == 1) {
						$sex = "Пан";
					} elseif ($sex == 2) {
						$sex = "Пані";
					} else {
						$sex = "?";
					}
			} else {
				$sex = $searchResult['return']['households']['contacts'][0]['identity']['title'];
					if ($sex == 1) {
						$sex = "Пан";
					} elseif ($sex == 2) {
						$sex = "Пані";
					} else {
						$sex = "?";
					}

			}


			if (isset($cust_subscriptions)) {

					$info = 0;
					while ($cust_subscriptions[$info])
						{
							if ($cust_subscriptions[$info]['communicationType'] == 1) {
									//$subscription_phone = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_phone = 1;} else {$subscription_phone = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 2 ) {
									//$subscription_fax = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_fax = 1;} else {$subscription_fax = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 3) {
									//$subscription_sms = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_sms = 1;} else {$subscription_sms = 0;}
								}

							if ($cust_subscriptions[$info]['communicationType'] == 4) {
									//$subscription_email = $cust_subscriptions[$info]['value'];
									if (!empty($cust_subscriptions[$info]['value'])) {$subscription_email = 1;} else {$subscription_email = 0;}
								}

							$info++;
						}
					} else {
						$subscription_phone = 0;
						$subscription_fax = 0;
						$subscription_sms = 0;
						$subscription_email = 0;
					}

										if (!isset($searchResult['return']['households']['contacts']['communications']['detail']['value']))
									{
											$cc2 = 0;
											$cust_phoneNumber="";
											while (isset($searchResult['return']['households']['contacts']['communications'][$cc2]))
											{

											if ($searchResult['return']['households']['contacts']['communications'][$cc2]['detail']['type'] == 3) {
											$cust_email = $searchResult['return']['households']['contacts']['communications'][$cc2]['detail']['value']; // email
											} else {
											$cust_phoneNumber .= $searchResult['return']['households']['contacts']['communications'][$cc2]['detail']['value'].";"; //phone number
											}

											$cc2++;
											}
									} else {
                    if ($searchResult['return']['households']['contacts']['communications']['detail']['type'] == 3) {
										$cust_email = $searchResult['return']['households']['contacts']['communications']['detail']['value'];
                    }
                    if ($searchResult['return']['households']['contacts']['communications']['detail']['type'] == 1) {
										$cust_phoneNumber = $searchResult['return']['households']['contacts']['communications']['detail']['value'];
                    }

									}

									if (isset($searchResult['return']['households']['contacts']['externalIdentifiers'][0])) {

										$ext = 0;
										while ($searchResult['return']['households']['contacts']['externalIdentifiers'][$ext]) {

											if ($searchResult['return']['households']['contacts']['externalIdentifiers'][$ext]['type'] == 5){
												$cust_card = $searchResult['return']['households']['contacts']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['households']['contacts']['externalIdentifiers'][$ext]['type'] == 999){
												$cust_old_number = $searchResult['return']['households']['contacts']['externalIdentifiers'][$ext]['value'];

											}
										$ext++;
										}

									} else {
												$cust_old_number = $searchResult['return']['households']['contacts']['externalIdentifiers']['value'];
									}

										if (!isset($searchResult['return']['households']['contacts']['classifications']['type']))
									{
											$cc2 = 0;

											while (isset($searchResult['return']['households']['contacts']['classifications'][$cc2]))
											{
											if ($searchResult['return']['households']['contacts']['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households']['contacts']['classifications'][$cc2]['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households']['contacts']['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households']['contacts']['classifications'][$cc2]['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												}


											$cc2++;
											}
									} else {
										if ($searchResult['return']['households']['contacts']['classifications']['type'] == 1
												and
												$searchResult['return']['households']['contacts']['classifications']['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households']['contacts']['classifications']['type'] == 1
												and
												$searchResult['return']['households']['contacts']['classifications']['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												} else {
													$cust_loyc_id = "xxx";
												}
									}



                  if (isset($searchResult['return']['households']['contacts'][0])) {

		if (!isset($searchResult['return']['households']['contacts'][0]['communications']['detail']['value']))
									{
											$cc2 = 0;
											$cust_phoneNumber="";
											while (isset($searchResult['return']['households']['contacts'][0]['communications'][$cc2]))
											{

											if ($searchResult['return']['households']['contacts'][0]['communications'][$cc2]['detail']['type'] == 3) {
											$cust_email = $searchResult['return']['households']['contacts'][0]['communications'][$cc2]['detail']['value']; // email
											} else {
											$cust_phoneNumber .= $searchResult['return']['households']['contacts'][0]['communications'][$cc2]['detail']['value'].";"; //phone number
											}

											$cc2++;
											}
									} else {

                    if ($searchResult['return']['households']['contacts'][0]['communications']['detail']['type'] == 3) {
                    $cust_email = $searchResult['return']['households']['contacts']['communications']['detail']['value'];
                    }
                    if ($searchResult['return']['households']['contacts'][0]['communications']['detail']['type'] == 1) {
                    $cust_phoneNumber = $searchResult['return']['households']['contacts']['communications']['detail']['value'];
                    }


									}

									if (isset($searchResult['return']['households']['contacts'][0]['externalIdentifiers'][0])) {

										$ext = 0;
										while ($searchResult['return']['households']['contacts'][0]['externalIdentifiers'][$ext]) {

											if ($searchResult['return']['households']['contacts'][0]['externalIdentifiers'][$ext]['type'] == 5){
												$cust_card = $searchResult['return']['households']['contacts'][0]['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['households']['contacts'][0]['externalIdentifiers'][$ext]['type'] == 999){
												$cust_old_number = $searchResult['return']['households']['contacts'][0]['externalIdentifiers'][$ext]['value'];

											}
										$ext++;
										}

									} else {
												$cust_old_number = $searchResult['return']['households']['contacts'][0]['externalIdentifiers']['value'];
									}

										if (!isset($searchResult['return']['households']['contacts'][0]['classifications']['type']))
									{
											$cc2 = 0;

											while (isset($searchResult['return']['households']['contacts'][0]['classifications'][$cc2]))
											{
											if ($searchResult['return']['households']['contacts'][0]['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households']['contacts'][0]['classifications'][$cc2]['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households']['contacts'][0]['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households']['contacts'][0]['classifications'][$cc2]['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												}


											$cc2++;
											}
									} else {
										if ($searchResult['return']['households']['contacts'][0]['classifications']['type'] == 1
												and
												$searchResult['return']['households']['contacts'][0]['classifications']['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households']['contacts'][0]['classifications']['type'] == 1
												and
												$searchResult['return']['households']['contacts'][0]['classifications']['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												} else {
													$cust_loyc_id = "xxx";
												}
									}
}



		//	array_push($customers_found[$customer]);
      $customers_found = [];
			$customers_found[$customer]['type'] = $cust_type;
			$customers_found[$customer]['customer_number'] = $customer;
			$customers_found[$customer]['cust_old_number'] = $cust_old_number;
			$customers_found[$customer]['cust_card'] = $cust_card;
			$customers_found[$customer]['sex'] = $sex;
			$customers_found[$customer]['name'] = $cust_name;
			$customers_found[$customer]['surname'] = $cust_surname;
			$customers_found[$customer]['othername'] = $cust_othername;
			$customers_found[$customer]['birthDate'] = $cust_birth;
			$customers_found[$customer]['store'] = $cust_store;
			$customers_found[$customer]['street'] = $cust_address_street;
			$customers_found[$customer]['address_id'] = $cust_address_id;
			$customers_found[$customer]['city'] = $cust_address_city;
			$customers_found[$customer]['postalCode'] = $cust_address_postalCode;
			$customers_found[$customer]['phoneNumber'] = $cust_phoneNumber;
			$customers_found[$customer]['email'] = $cust_email;
			$customers_found[$customer]['cust_loyc_id'] = $cust_loyc_id;

			$customers_found[$customer]['subscription_email'] = $subscription_email;
			$customers_found[$customer]['subscription_fax'] = $subscription_fax;
			$customers_found[$customer]['subscription_phone'] = $subscription_phone;
			$customers_found[$customer]['subscription_sms'] = $subscription_sms;

		} elseif (isset($searchResult['return']['households']) and isset($searchResult['return']['households'][0])) {
			$cust_type = "natural; multiple natural customers";

					$cc = 0;
					while (isset($searchResult['return']['households'][$cc]))
					{
						$customer = $searchResult['return']['households'][$cc]['contacts']['customerNumber'];
						//array_push($customers_found,$customer);
						$cust_name = $searchResult['return']['households'][$cc]['contacts']['identity']['firstName'];
						$cust_surname = $searchResult['return']['households'][$cc]['contacts']['identity']['name'];
						$cust_store = $searchResult['return']['households'][$cc]['contacts']['identity']['managementEntity'];
						$cust_address_street = $searchResult['return']['households'][$cc]['addresses']['detail']['line1'];
						$cust_address_city = $searchResult['return']['households'][$cc]['addresses']['detail']['city'];
						$cust_address_postalCode = $searchResult['return']['households'][$cc]['addresses']['detail']['postalCode'];

									if (!isset($searchResult['return']['households'][$cc]['contacts']['communications']['detail']['value']))
									{
											$cc2 = 0;
											$cust_phoneNumber="";
											while (isset($searchResult['return']['households'][$cc]['contacts']['communications'][$cc2]))
											{

											if (strpos($searchResult['return']['households'][$cc]['contacts']['communications'][$cc2]['detail']['value'], '@') == true) {
											//echo $entity['contacts']['communications'][$cc]['detail']['value']."<br>"; // email
											} else {
											//echo "Номер телефону2: ".$searchResult['return']['households'][$cc]['contacts']['communications'][$cc2]['detail']['value']."\n"; //phone number
											$cust_phoneNumber .= $searchResult['return']['households'][$cc]['contacts']['communications'][$cc2]['detail']['value'].";"; //phone number
											}

											$cc2++;
											}
									} else {
										$cust_phoneNumber = $searchResult['return']['households'][$cc]['contacts']['communications']['detail']['value'];

                    
									}

									if (isset($searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][0])) {

										$ext = 0;
										while ($searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][$ext]) {

											if ($searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][$ext]['type'] == 5){
												$cust_card = $searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][$ext]['value'];

											}

											if ($searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][$ext]['type'] == 999){
												$cust_old_number = $searchResult['return']['households'][$cc]['contacts']['externalIdentifiers'][$ext]['value'];

											}

										$ext++;
										}



									}

									if (!isset($searchResult['return']['households'][$cc]['contacts']['classifications']['type']))
									{
											$cc2 = 0;

											while (isset($searchResult['return']['households'][$cc]['contacts']['classifications'][$cc2]))
											{
											if ($searchResult['return']['households'][$cc]['contacts']['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households'][$cc]['contacts']['classifications'][$cc2]['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households'][$cc]['contacts']['classifications'][$cc2]['type'] == 1
												and
												$searchResult['return']['households'][$cc]['contacts']['classifications'][$cc2]['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												} else {
													$cust_loyc_id = "xxx";
												}


											$cc2++;
											}
									} else {
										if ($searchResult['return']['households'][$cc]['contacts']['classifications']['type'] == 1
												and
												$searchResult['return']['households'][$cc]['contacts']['classifications']['code'] == 10)
												{
													$cust_loyc_id = "ПРО";
												} elseif (
												$searchResult['return']['households'][$cc]['contacts']['classifications']['type'] == 1
												and
												$searchResult['return']['households'][$cc]['contacts']['classifications']['code'] == 11
												) {
													$cust_loyc_id = "КЛМ";
												} else {
													$cust_loyc_id = "xxx";
												}
									}

						// array_push($customers_found[$customer]);
            $customers_found = [];
						$customers_found[$customer]['type'] = $cust_type;
						$customers_found[$customer]['customer_number'] = $customer;
						$customers_found[$customer]['cust_old_number'] = $cust_old_number;
						$customers_found[$customer]['cust_card'] = $cust_card;
						$customers_found[$customer]['name'] = $cust_name;
						$customers_found[$customer]['surname'] = $cust_surname;
						$customers_found[$customer]['store'] = $cust_store;
						$customers_found[$customer]['street'] = $cust_address_street;
						$customers_found[$customer]['city'] = $cust_address_city;
						$customers_found[$customer]['postalCode'] = $cust_address_postalCode;
						$customers_found[$customer]['phoneNumber'] = $cust_phoneNumber;
						$customers_found[$customer]['cust_loyc_id'] = $cust_loyc_id;

						$cust_card = '';
						$customer = '';
						$cust_name = '';
						$cust_surname = '';
						$cust_store = '';
						$cust_address_street = '';
						$cust_address_city = '';
						$cust_address_postalCode = '';
						$cust_phoneNumber = '';
						$cust_loyc_id = '';
						$cust_old_number = '';
					$cc++;
					}


	  } elseif (isset($searchResult['return']['households']) and isset($searchResult['return']['legalEntities'])) {
			$cust_type = "mixed type of customers; multiple customers";
					$cc = 0;
					while (isset($searchResult['return']['customersFound'][$cc]))
					{
						$customer = $searchResult['return']['customersFound'][$cc];
						//array_push($customers_found,$customer);
						array_push($customers_found[$customer]);
						$customers_found[$customer]['type'] = $cust_type;
						$customers_found[$customer]['customer_number'] = $customer;

					$cc++;
					}

		} else {
			$cust_type = "xxx";
			$customer = "xxx";
			//array_push($customers_found,$customer);
		//	array_push($customers_found[$customer]);
      $customers_found = [];
			$customers_found[$customer]['type'] = $cust_type;
			$customers_found[$customer]['customer_number'] = $customer;
		}


  return $customers_found;

}


?>
