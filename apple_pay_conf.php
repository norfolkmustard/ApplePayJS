<?php
//update these with the real location of your two .pem files. keep them above/outside your webroot folder
define('PRODUCTION_CERTIFICATE_KEY', '/your/path/to/applepay_includes/ApplePay.key.pem');
define('PRODUCTION_CERTIFICATE_PATH', '/your/path/to/applepay_includes/ApplePay.crt.pem');

// This is the password you were asked to create in terminal when you extracted ApplePay.key.pem
define('PRODUCTION_CERTIFICATE_KEY_PASS', 'your password here'); 

define('PRODUCTION_MERCHANTIDENTIFIER', openssl_x509_parse( file_get_contents( PRODUCTION_CERTIFICATE_PATH ))['subject']['UID'] ); //if you have a recent version of PHP, you can leave this line as-is. http://uk.php.net/openssl_x509_parse will parse your certificate and retrieve the relevant line of text from it e.g. merchant.com.mydomain or merchant.com.mydomain.shop
define('PRODUCTION_DOMAINNAME', $_SERVER["HTTP_HOST"]); //e.g. shop.mydomain.com or mydomain.com


define('PRODUCTION_CURRENCYCODE', 'GBP');	//https://en.wikipedia.org/wiki/ISO_4217
define('PRODUCTION_COUNTRYCODE', 'GB');		//https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
define('PRODUCTION_DISPLAYNAME', 'My Test Shop');

define('DEBUG', 'true');
?>
