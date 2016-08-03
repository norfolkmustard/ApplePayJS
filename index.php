<?php
require_once ('/var/www/applepay_includes/apple_pay_conf.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-gb" xml:lang="en-gb">
<head>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script type="text/javascript">

$( document ).ready( function( )
{
	if (window.ApplePaySession) {
	   var merchantIdentifier = '<?=PRODUCTION_MERCHANTIDENTIFIER?>';
	   var promise = ApplePaySession.canMakePaymentsWithActiveCard(merchantIdentifier);
	   promise.then(function (canMakePayments) {
		  if (canMakePayments) {
		     $("#applePay").show();
			 console.log('hi, I can do ApplePay');
		  } else {   
		     $("#applePay").after('<p>ApplePay is possible on this browser, but not currently activated.</p>');
			 console.log('ApplePay is possible on this browser, but not currently activated.');
		  }
		}); 
	} else {
		console.log('ApplePay not available on this browser');
		$("#applePay").after('ApplePay not available on this browser');
	}
	
	$("#applePay").click( function(evt) {
	
		 var paymentRequest = {
		   currencyCode: 'GBP',
		   countryCode: 'GB',
		   lineItems: [{label: 'Annual Membership', amount: '25'}, {label: 'P&P', amount: '15'}, {label: 'Tax', amount: '2'}],
		   shippingMethods: [{label: 'Express Shipping', amount: '15', detail: '1-5 days', identifier: 'exp'}, {label: 'Standard Shipping', amount: '6', detail: '5-10 days', identifier: 'std'}],
		   total: {
			  label: '<?=PRODUCTION_DISPLAYNAME?>',
			  amount: '42'
		   },
		   supportedNetworks: ['amex', 'masterCard', 'visa' ],
		   //currently fails if you include this next line
		   //
		   merchantCapabilities: [ 'supports3DS', 'supportsEMV', 'supportsCredit', 'supportsDebit' ]
		};
		
		var session = new ApplePaySession(1, paymentRequest);
		
		// Merchant Validation
		session.onvalidatemerchant = function (event) {
			console.log(event);
			var promise = performValidation(event.validationURL);
			promise.then(function (merchantSession) {
		  		session.completeMerchantValidation(merchantSession);
		  	}); 
		}
		

		function performValidation(valURL) {
		  	return new Promise(function(resolve, reject) {
				var xhr = new XMLHttpRequest();
				xhr.onload = function() {
					var data = JSON.parse(this.responseText);
					console.log(data);
					resolve(data);
				};
				xhr.onerror = reject;
				xhr.open('GET', 'apple_pay_comm.php?u=' + valURL);
				xhr.send();
		  	});
		}

		session.onshippingcontactselected = function(event) {
			console.log('starting session.onshippingcontactselected');
			console.log(event);
		}
		
		session.onshippingmethodselected = function(event) {
			console.log('starting session.onshippingmethodselected');
			console.log(event);

			var newPPAmt = '15';
			var newSubTotal = '42';
			if ( event.shippingMethod.identifier == "std"){
				newPPAmt = '6';
				newSubTotal = '33';
			}
			
			var status = ApplePaySession.STATUS_SUCCESS;
			var newTotal = { type: 'final', label: '<?=PRODUCTION_DISPLAYNAME?>', amount: newSubTotal };
			var newLineItems =[{type: 'final',label: 'Annual Membership', amount: '25'}, {type: 'final',label: 'P&P', amount: newPPAmt}, {type: 'final',label: 'Tax', amount: '2'}];
			
			session.completeShippingMethodSelection(status, newTotal, newLineItems );
		   	
			
		}
		
		//at the time of writing, if you include this callback the paysheet will fail to proceed to 'Pay with touch id' step
		//session.onpaymentmethodselected = function(event) {
		//	console.log('starting session.onpaymentmethodselected');
		//	console.log(event);
		//}
		
 		session.onpaymentauthorized = function (event) {
 
   			console.log('starting session.onpaymentauthorized');
   			console.log(event);

   			var promise = sendPaymentToken(event.payment.token);
   			promise.then(function (success) {	
      			var status;
      			if (success)
         			status = ApplePaySession.STATUS_SUCCESS;
				else
         			status = ApplePaySession.STATUS_FAILURE;
      			session.completePayment(status);
      			showConfirmation();
   			});
		}

		function sendPaymentToken(paymentToken) {
			return new Promise(function(resolve, reject) {
				console.log('starting function sendPaymentToken()');
				console.log(paymentToken);
				resolve;
		  	});
		}
		
		session.oncancel = function(event) {
			console.log('starting session.cancel');
			console.log(event);
		}
		
		session.begin();
	
	});

});
</script>
<style>
#applePay {  
	width: 280px;  
	height: 64px;  
	display: inline-block;  
	border: 1px solid black;  
	box-sizing: border-box;  
} 
</style>
</head>
<body>
<div>
<button type="button" id="applePay" >ApplePay test button</button>
</div>
</body>
</html>
