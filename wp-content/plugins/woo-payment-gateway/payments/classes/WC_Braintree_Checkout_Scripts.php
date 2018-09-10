<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * 
 * @author Clayton Rogers
 *
 */
class WC_Braintree_Checkout_Scripts{
	
	public static function getCheckoutScript(){
		?>
<script>
/*Braintree checkout script*/
var $ = jQuery;
var BraintreeUtils = function(clientToken){
	this.constructor(clientToken);
};

/*Contructor for BraintreeUtils class.*/
BraintreeUtils.prototype.constructor = function(clientToken){
	this.clientToken = clientToken;
	$('form.checkout').attr('id', 'checkout');
	this.setupEvents();
}

/*Setup events nevessary for braintree checkout.*/
BraintreeUtils.prototype.setupEvents = function(){
	$(document.body).on('braintree_form_ready', this.setup);
	$('form.checkout').on('checkout_place_order', this.checkoutPlaceOrder);
	$(document.body).on('checkout_error', this.checkoutError);
	$(document.body).on('change', 'input[name="payment_method"]', this.paymentGatewayChange);
}

/*Setup the Braintree integration.*/
BraintreeUtils.prototype.setup = function(){
	if(braintreeUtils.integration){
		return false;
	}
	if(braintreeUtils.setupCalled){
		return false;
	}
	braintree.setup(braintreeUtils.clientToken, 'dropin', {
		container:'dropin-container',
		form:'checkout',
		onReady: function(integration){
			braintreeUtils.integration = integration
		},
		onPaymentMethodReceived:function(response){
			braintreeUtils.onPaymentMethodReceived(response);
		}
	});
	braintreeUtils.setupCalled = true;
}

/*Check if the payment gateway is selected.*/
BraintreeUtils.prototype.isGatewaySelected = function(){
	return document.getElementById('payment_method_braintree_payment_gateway').checked;
}

/*If the payment gateway that is selected is not Braintree, remove checkout validation trigger.*/
BraintreeUtils.prototype.paymentGatewayChange = function(){
	if($(this).val() !== 'braintree_payment_gateway'){
		$(document.body).off('checkout_place_order', braintreeUtils.checkoutPlaceOrder);
	}
	else{
		$(document.body).on('checkout_place_order', braintreeUtils.checkoutPlaceOrder);
	}
}

/*Validate the Braintree has created the nonce. If no nonce, then return false.*/
BraintreeUtils.prototype.checkoutPlaceOrder = function(){
	if(braintreeUtils.isGatewaySelected()){
		if(braintreeUtils.paymentMethodReceived){
			return true;
		}
		else{
			return false;
		}
	}
}

/*Handle the Braintree response when the payment method is received.*/
BraintreeUtils.prototype.onPaymentMethodReceived = function(response){
	braintreeUtils.paymentMethodReceived = true;
	var element = document.getElementById('payment_method_nonce');
	if(element != null){
		element.value = response.nonce;
	}
	else{
		element = document.createElement('input');
		element.type = 'hidden';
		element.name = 'payment_method_nonce';
		element.id = 'payment_method_nonce';
		element.value = response.nonce;
		$('form.checkout').append(element);
	}
	$('form.checkout').submit();
}

BraintreeUtils.prototype.checkoutError = function(){
	braintreeUtils.paymentMethodReceived = false;
}
</script>
<?php 
	}
	
	public static function getPayPalScript(){
?>
<script>
var $ = jQuery;
var PayPalCheckout = function(clientToken){
	this.clientToken = clientToken;
	this.setupEvents();
}

PayPalCheckout.prototype.setupEvents = function(){
	$(document.body).on('paypal_container_ready', this.setup);
	$('form.checkout').on('checkout_place_order', this.checkoutPlaceOrder);
	$(document.body).on('checkout_error', this.checkoutError);
}

PayPalCheckout.prototype.setup = function(){
	if(paypalCheckout.integration){
		return;
	}
	if(paypalCheckout.setupCalled){
		return;
	}
	paypalCheckout.setupCalled = true;
	braintree.setup(paypalCheckout.clientToken, "paypal",{
			container: 'dropin-container',
			onPaymentMethodReceived: function(response){
				paypalCheckout.onPaymentMethodReceived(response);
			},
			onReady: function(integration){
				paypalCheckout.intregration = integration;
			}
	});		
}

PayPalCheckout.prototype.onPaymentMethodReceived = function(response){
	paypalCheckout.paymentMethodReceived = true;
	var element = document.getElementById('payment_method_nonce');
	if(element != null){
		element.value = response.nonce;
	}
	else{
		element = document.createElement('input');
		element.type = 'hidden';
		element.name = 'payment_method_nonce';
		element.id = 'payment_method_nonce';
		element.value = response.nonce;
		$('form.checkout').append(element);
	}
}

PayPalCheckout.prototype.teardown = function(){
	if(paypalCheckout.integration){
		paypalCheckout.integration.teardown();
		paypalCheckout.integration = null;
	}
}

PayPalCheckout.prototype.checkoutPlaceOrder = function(){
	if(paypalCheckout.isGatewaySelected){
		if(paypalCheckout.paymentMethodReceived){
			return true;
		}
		else{
			return false;
		}
	}

}

PayPalCheckout.prototype.isGatewaySelected = function(){
	return document.getElementById('payment_method_braintree_payment_gateway').checked;
}

PayPalCheckout.prototype.checkoutError = function(){
	paypalCheckout.paymentMethodReceieved = false;
}
</script>
<?php 		
		
	}
	
	public static function getPayForOrderScript(){
?>
<script>
var $ = jQuery;
var BraintreeUtils = function(clientToken){
	this.clientToken = clientToken;
	this.setupEvents();
};

BraintreeUtils.prototype.setupEvents = function(){
	$(document.body).on('braintree_form_ready', this.setup);
	$(document.body).on('change', 'input[name="payment_method"]', this.paymentGatewayChange);
}

BraintreeUtils.prototype.setup = function(){
	if(braintreeUtils.integration){
		return false;
	}
	if(braintreeUtils.setupCalled){
		return false;
	}
	braintree.setup(braintreeUtils.clientToken, 'dropin', {
		container:'dropin-container',
		form:'order_review',
		onReady: function(integration){
			braintreeUtils.integration = integration
		},
		onPaymentMethodReceived:function(response){
			braintreeUtils.onPaymentMethodReceived(response);
		}
	});
	braintreeUtils.setupCalled = true;
}

BraintreeUtils.prototype.onPaymentMethodReceived = function(response){
	braintreeUtils.paymentMethodReceived = true;
	var element = document.getElementById('payment_method_nonce');
	if(element != null){
		element.value = response.nonce;
	}
	else{
		element = document.createElement('input');
		element.type = 'hidden';
		element.name = 'payment_method_nonce';
		element.id = 'payment_method_nonce';
		element.value = response.nonce;
		$('#order_review').append(element);
	}
	$('#order_review').submit();
}

/*If the selected payment gateway is not Braintree, teardown the integration.*/
BraintreeUtils.prototype.paymentGatewayChange = function(){
	if($(this).val() !== 'braintree_payment_gateway'){
		braintreeUtils.teardown();
	}
	else{
		braintreeUtils.initializeDropinForm();
	}
}

BraintreeUtils.prototype.teardown = function(){
	if(braintreeUtils.integration){
		braintreeUtils.integration.teardown();
		braintreeUtils.integration = null;
		braintreeUtils.setupCalled = false;
	}
}

BraintreeUtils.prototype.paymentGatewayChange = function(){
	if($(this).val() !== 'braintree_payment_gateway'){
		braintreeUtils.teardown();
	}
	else{
		braintreeUtils.setup();
	}
}
</script>
<?php 	
	}
}