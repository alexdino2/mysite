<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * 
 * @author Clayton Rogers
 *
 */
class WC_Braintree_Subscription_Scripts{
	
	public static function getPaymentChangeScript(){
		?>
<script>
var $ = jQuery;
var BraintreeUtils = function(clientToken){
	this.constructor(clientToken);
	this.setupEvents();
};

BraintreeUtils.prototype.constructor = function(clientToken){
	this.clientToken = clientToken;
}

BraintreeUtils.prototype.setupEvents = function(){
	$(document.body).on('braintree_form_ready', this.initializeDropinForm);
	$(document.body).on('change', 'input[name="payment_method"]', this.paymentGatewayChange);
	$(document.body).on('click', '#add_new_method', this.clickAddNew);
	$(document.body).on('click', '#cancel_add_new', this.cancelAddNew);
	$(document.body).on('click', '.payment-method-item', this.paymentMethodSelected);
	$(document.body).on('click', '#place_order', this.submitForm);
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

/*Setup the dropin form.*/
BraintreeUtils.prototype.initializeDropinForm = function(){
	if(braintreeUtils.hasSavedPaymentMethods()){
		braintreeUtils.hideNewPaymentsContainer(braintreeUtils.showSavedPaymentsContainer);
		return;
	}
	else{
		braintreeUtils.showNewPaymentsContainer();
		braintreeUtils.setup();
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
	if(braintreeUtils.isDropinFormEmpty()){
		braintreeUtils.emptyDropinForm();
		braintreeUtils.setupCalled = false;
	}
}

/*Check if the dropin for is empty.*/
BraintreeUtils.prototype.isDropinFormEmpty = function(){
	return ! $('#dropin-container').is(':empty');
}

BraintreeUtils.prototype.emptyDropinForm = function(){
	$('#dropin-container').empty();
}

BraintreeUtils.prototype.hasSavedPaymentMethods = function(){
	if(document.getElementById('saved_payment_methods')){
		return true;
	}
	return false;
}

BraintreeUtils.prototype.showSavedPaymentsContainer = function(callback){
	$('#saved_payment_methods').show(400, callback);
}

BraintreeUtils.prototype.showNewPaymentsContainer = function(callback){
	$('#payment_form_container').show(400, callback);
}

BraintreeUtils.prototype.hideSavedPaymentsContainer = function(callback){
	$('#saved_payment_methods').slideUp(400, callback);
}

BraintreeUtils.prototype.hideNewPaymentsContainer = function(callback){
	$('#payment_form_container').slideUp(400, callback);
}

BraintreeUtils.prototype.clickAddNew = function(){
	braintreeUtils.removeSelectedPaymentMethod
	braintreeUtils.setup();
	braintreeUtils.hideSavedPaymentsContainer(braintreeUtils.showNewPaymentsContainer);
}

BraintreeUtils.prototype.cancelAddNew = function(){
	braintreeUtils.teardown();
	braintreeUtils.hideNewPaymentsContainer(braintreeUtils.showSavedPaymentsContainer);
}

BraintreeUtils.prototype.paymentMethodSelected = function(){
	$('.payment-method-item').each(function(index){
		$(this).removeClass('selected');
	});
	$(this).addClass('selected');
	$('#selected_payment_method').val($(this).attr('payment-token'));
}

BraintreeUtils.prototype.removeSelectedPaymentMethod = function(){
	$('#selected_payment_method').val("");
}

BraintreeUtils.prototype.submitForm = function(){
	$('#order_review').submit();
}
</script>
	<?php }
}