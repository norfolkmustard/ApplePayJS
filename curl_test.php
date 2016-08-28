<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$validation_url = isset( $_GET['u'] ) ? $_GET['u'] : "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession";


if( "https" == parse_url($validation_url, PHP_URL_SCHEME) && substr( parse_url($validation_url, PHP_URL_HOST), -10 )  == ".apple.com" ){

	require_once ('/your/path/to/apple_pay_conf.php');
	
	echo "<pre>";
	
	// create a new cURL resource
	$ch = curl_init();

	$data = '{"merchantIdentifier":"'.PRODUCTION_MERCHANTIDENTIFIER.'", "domainName":"'.PRODUCTION_DOMAINNAME.'", "displayName":"'.PRODUCTION_DISPLAYNAME.'"}';
	
	echo "<fieldset style='padding:1em;margin:1em'><legend> data sent to applePay server </legend>$data</fieldset>";

	curl_setopt($ch, CURLOPT_URL, $validation_url);
	//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'ecdhe_rsa_aes_128_gcm_sha_256,rsa_aes_128_gcm_sha_256');
	curl_setopt($ch, CURLOPT_SSLCERT, PRODUCTION_CERTIFICATE_PATH);
	curl_setopt($ch, CURLOPT_SSLKEY, PRODUCTION_CERTIFICATE_KEY);
	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, PRODUCTION_CERTIFICATE_KEY_PASS);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$verbose = fopen('php://temp', 'w+');
	curl_setopt($ch, CURLOPT_STDERR, $verbose);
	
	echo "<fieldset style='padding:1em;margin:1em'><legend> Headers & applePay server response</legend>";

	if(curl_exec($ch) === false)
	{
		echo "</fieldset>";
		echo "<fieldset style='padding:1em;margin:1em'><legend> cURL Error </legend>";
		$ch_error = curl_error($ch);
		echo '{"curlError":' . json_encode( curl_errno($ch) . " - " . mb_convert_encoding( $ch_error, "UTF-8", mb_detect_encoding( $ch_error ) ) ) . '}';
		echo "</fieldset>";
	} else {
		echo "</fieldset>";
	}

	// close cURL resource, and free up system resources
	
	rewind($verbose);
	$verboseLog = stream_get_contents($verbose);
	
	echo "<fieldset style='padding:1em;margin:1em'><legend> Verbose information </legend>";
	echo htmlspecialchars($verboseLog);
	echo "</fieldset>";
	
	$version = curl_version();
	echo "<fieldset style='padding:1em;margin:1em'><legend> curl version </legend>";
	print_r( $version );
	echo "</fieldset></pre>";
		
	curl_close($ch);
	
}
?>
