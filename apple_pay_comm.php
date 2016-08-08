<?php
$validation_url = $_GET['u'];


if( "https" == parse_url($validation_url, PHP_URL_SCHEME) && substr( parse_url($validation_url, PHP_URL_HOST), -9 )  == "apple.com" ){

	require_once ('/your/path/to/applepay_includes/apple_pay_conf.php');
	
	// create a new cURL resource
	$ch = curl_init();

	$data = '{"merchantIdentifier":"'.PRODUCTION_MERCHANTIDENTIFIER.'", "domainName":"'.PRODUCTION_DOMAINNAME.'", "displayName":"'.PRODUCTION_DISPLAYNAME.'"}';

	curl_setopt($ch, CURLOPT_URL, $validation_url);
	curl_setopt($ch, CURLOPT_SSLCERT, PRODUCTION_CERTIFICATE_PATH);
	curl_setopt($ch, CURLOPT_SSLKEY, PRODUCTION_CERTIFICATE_KEY);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	if(curl_exec($ch) === false)
	{
		echo '{"curlError":"' . curl_error($ch) . '"}';
	}

	// close cURL resource, and free up system resources
	curl_close($ch);
	
}
?>
