<?php
$code = "";
$access_token = "";
$refresh_token = "";
$client_id = "";
$client_secret = "";
function insert_record($firstname, $lastname, $email, $phone)
{

	global $access_token;

	$postdata = [

		"data" => [
			[
				"Company" => "NA",
				"Last_Name" => $lastname,
				"First_Name" => $firstname,
				"Email" => $email,
				"Phone" => $phone
			]
		],
		"trigger" => [
			"approval",
			"workflow",
			"blueprint"
		]
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://www.zohoapis.com/crm/v2/Leads');

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch,  CURLOPT_POSTFIELDS, json_encode($postdata));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Zoho-oauthtoken ' . $access_token, 'content-type:application/x-www-form-urlencoded'));

	$response = curl_exec($ch);

	$response = json_decode($response);

	if (isset($response->data)) {

		if ($response->data[0]->status == 'success') {

			return $response->data[0];
		}
	}

	if (isset($response->code)) {

		if ($response->code == 'AUTHENTICATION_FAILURE') {

			generate_access_token();

			$result = insert_record($firstname, $lastname, $email, $phone);

			if ($result !== null) {
				return $result;
			}
		}


		if ($response->code == 'INVALID_TOKEN') {

			generate_access_token();

			$result = insert_record($firstname, $lastname, $email, $phone);

			if ($result !== null) {
				return $result;
			}
		}

		return null;
	}

	echo "<pre>";
	print_r($response);


	//return $response->data[0]->details->id;


}


$Firstname = "asif_youtube2";

$lastname = "Ali";

$email = "asifali13@gmail.com";

$phone = "1234567890";







function generate_refresh_token()
{
	global $code, $refresh_token, $client_id, $client_secret;
	$post = [
		'code' => $code,
		'redirect_uri' => 'https://localhost/zoho',
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'authorization_code'
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://accounts.zoho.com/oauth/v2/token');

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch,  CURLOPT_POSTFIELDS, http_build_query($post));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type:application/x-www-form-urlencoded'));

	$response = curl_exec($ch);

	$response = json_decode($response, true);
	$refresh_token = $response['refresh_token'];
}




function generate_access_token()
{
	global $refresh_token, $access_token, $client_id, $client_secret;
	$post = [
		'refresh_token' => $refresh_token,
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'refresh_token'
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://accounts.zoho.com/oauth/v2/token');

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch,  CURLOPT_POSTFIELDS, http_build_query($post));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type:application/x-www-form-urlencoded'));

	$response = curl_exec($ch);

	$response = json_decode($response);

	$access_token = $response->access_token;

	//return $response->access_token;


}
generate_refresh_token();
generate_access_token();
insert_record($Firstname, $lastname, $email, $phone);
