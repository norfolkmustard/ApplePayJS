<?php

//update these with the real location of your two .pem files. keep them above/outside your webroot folder
define('PRODUCTION_CERTIFICATE_KEY', '/var/www/applepay_includes/ApplePay.key.pem'); 
define('PRODUCTION_CERTIFICATE_PATH', '/var/www/applepay_includes/ApplePay.crt.pem');

define('PRODUCTION_CURRENCYCODE', 'GBP');	//https://en.wikipedia.org/wiki/ISO_4217
define('PRODUCTION_COUNTRYCODE', 'GB');		//https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
define('PRODUCTION_MERCHANTIDENTIFIER', 'merchant.com.blurg.shop');
define('PRODUCTION_DISPLAYNAME', 'My Test Shop');

define('PRODUCTION_DOMAINNAME', $_SERVER["HTTP_HOST"]);	
?>
