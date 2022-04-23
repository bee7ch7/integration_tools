<?php

class TokenController {

	function getAuthToken($code) {

			if ($code) {

			$curl = curl_init();

			  curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://idpb2e.meldm.ml/as/token.oauth2',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,

			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,

			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => "grant_type=authorization_code&client_id=xxx&code=$code&redirect_uri=https%3A%2F%2Fintegrations.meldm.ml%2Fpingid%2Fcallback.php",
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Basic xxx'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$response = json_decode( $response, true );
			$user_access_token = $response;

			return $user_access_token;

			} else {
			return 'no code specified';
			}
	}

	function getUserAuthToken($user_token) {

		if ($user_token) {

				$curl2 = curl_init();

				curl_setopt_array($curl2, array(
				  CURLOPT_URL => 'https://idpb2e.meldm.ml/idp/userinfo.openid',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,

				  CURLOPT_SSL_VERIFYHOST => 0,
				  CURLOPT_SSL_VERIFYPEER => 0,

				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer $user_token"
				  ),
				));

				$user_profile_data = curl_exec($curl2);
				curl_close($curl2);

				$user_profile_data = json_decode( $user_profile_data, true );

				return $user_profile_data;

		} else {
			return 'no code specified';
			}

	}


}

?>
