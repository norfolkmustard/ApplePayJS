# ApplePayJS
Working draft of ApplePayJS with php

using the apple reference pages here https://developer.apple.com/reference/applepayjs

To check if your site is using one of the supported cipher suites https://developer.apple.com/reference/applepayjs#2166536
 
use https://www.ssllabs.com/ssltest/

* Have you had Apple verify your domain name where you'll be using applepay?

* have you got a MerchantID ( e.g. merchant.com.blurg.shop  ) recorded with apple

* have you got a Merchant (Payment processing ) certificate from apple? (you may need this signed with your payment processing partner's csr rather than your own - e.g. Stripe.com )

* have you got a Merchant (session) certificate from apple (using your own csr & private key)

 
here's how to do the last bit..
* Go here https://developer.apple.com/account/ios/identifier/merchant
* click + to create a merchant ID if you haven't already done so. If you have one already, click it, then click "edit"
* on the next screen you have 3 things to do.
* 1 create a Payment Processing Certificate (use your 3rd party payment provider's csr for this)
* 2 add a Merchant Domain
* 3 create an Apple Pay Merchant Identity (which is another certificate).

In that third section "Apple Pay Merchant Identity"...

* Click "Create Certificate"
* Follow the "Create a CSR file. (Optional)" method then hit "Continue"
* You'll see at the top of the next page that the act of using KeychainAccess.app to create a CSR, actually creates a private key and certificate (aka public key) pair. These are both kept in keychainaccess.app on your mac. The public key/cert is also saved to disk when you create it, it's this xxx.certSigningRequest file which you'll upload to apple next
* Once you upload your public key ( xxx.certSigningrequest file), apple will use it to generate your Apple Pay Merchant Identity (certificate) - a file called merchant_id.cer
* download this merchant_id.cer file, and double-click it to insert it into keychain access.app. this should automatically get appended to the existing entry for your Private key in keychain access.app
* right-click that certificate (probably named "Merchant ID: merchant...." from within keychain access.app (you may need to expand the private key entry to see the certificate under it) and select "Export 'Merchant ID merchant....' ". This will default to exporting a xxxx.p12 file to your desktop.

it's this xxx.p12 file which you then use openssl in terminal.app on your mac ......
 
openssl pkcs12 -in ApplePayMerchantIdentity_and_privatekey.p12 -out ApplePay.crt.pem -clcerts -nokeys  
openssl pkcs12 -in ApplePayMerchantIdentity_and_privatekey.p12 -out ApplePay.key.pem -nocerts -nodes 

...to create two .pem files. These are the files your webserver will use to authenticate its conversations with Apple, requesting a session etc for each ApplePay transaction your customers make. 

If at all possible, keep these two .pem files outside your root/public web folder. e.g. if your root web folder is /var/www/html/ then store these in /var/www/applepay_includes and include(); them in your php script.

throughout index.php I've sprinkled loads of console.log(). Plug your iphone into your Mac, goto your applepay test page on safari on your iPhone. Goto Safari on your Mac and select your iPhone from the Develop menu in Safari - voila - you now see what your phone's safari browser is telling you via the javascript console.

remove these console.log() lines once you see how it all works and before you go-live with it.
