# ApplePayJS
Working draft of ApplePayJS with php

using the apple reference pages here https://developer.apple.com/reference/applepayjs

To check if your site is using one of the supported cipher suites https://developer.apple.com/reference/applepayjs#2166536
 
use https://www.ssllabs.com/ssltest/

Have you had Apple verify your domain name where you'll be using applepay?
have you got a MerchantID ( e.g. merchant.com.blurg.shop  ) recorded with apple
have you got a Merchant (Payment processing ) certificate from apple? (you may need this signed with your payment processing partner's csr rather than your own - e.g. Stripe.com )
Merchant (session) certificate from apple (using your own csr & private key)

 
Once you have your Merchant ID (session) certificate from apple, import that into the keychain Access.app on your mac, export the combined private-key and cert as a .p12 file, something like ApplePayMerchantIdentity_and_privatekey.p12 then, in terminal:-
 
openssl pkcs12 -in ApplePayMerchantIdentity_and_privatekey.p12 -out ApplePay.crt.pem -clcerts -nokeys  
openssl pkcs12 -in ApplePayMerchantIdentity_and_privatekey.p12 -out ApplePay.key.pem -nocerts -nodes 
