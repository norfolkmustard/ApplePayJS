<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!function_exists('json_last_error_msg')) {
        function json_last_error_msg() {
            static $ERRORS = array(
                JSON_ERROR_NONE => 'No error',
                JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
                JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                JSON_ERROR_SYNTAX => 'Syntax error',
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
            );

            $error = json_last_error();
            return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
        }
    }

$validation_url = isset( $_GET['u'] ) ? $_GET['u'] : "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession";


if( "https" == parse_url($validation_url, PHP_URL_SCHEME) && substr( parse_url($validation_url, PHP_URL_HOST), -10 )  == ".apple.com" ){

	require_once ('/your/path/to/apple_pay_conf.php');
	
	if( !defined( 'DEBUG' ) || DEBUG != 'true' ) { exit( 'this page intentionally left blank' ); }
	
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
	
	//debug options
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$verbose = fopen('php://temp', 'w+');
	curl_setopt($ch, CURLOPT_STDERR, $verbose);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);

	if( $result === false)
	{

		echo "<fieldset style='padding:1em;margin:1em'><legend> cURL Error </legend>";
		echo curl_errno($ch) . " - " . curl_error($ch);
		echo "</fieldset>";
		
	} else {
		
		echo "<fieldset style='padding:1em;margin:1em'><legend> applePay server response </legend>";
		echo $result;
		echo "</fieldset>";
		
		echo "<fieldset style='padding:1em;margin:1em'><legend> applePay server response - JSON decode test </legend>";
		print_r( json_decode( $result, true ) );
		echo "<hr> JSON decode last error :- ";
		echo json_last_error_msg();
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
