/*Braintree checkout script*/
var $ = jQuery;
var BraintreeUtils = function(clientToken){
	this.constructor(document.getElementById('client_token').value);
};

var createBraintreeUtils = function(){
	braintreeUtils = new BraintreeUtils();
	if(! braintreeUtils.hasSavedPaymentMethods()){
		braintreeUtils.setup();
	}
}

/*Contructor for BraintreeUtils class.*/
BraintreeUtils.prototype.constructor = function(clientToken){
	this.clientToken = clientToken;
	this.setForm();
	$('form.checkout').attr('id', 'checkout');
	this.setupEvents();
}

BraintreeUtils.prototype.setForm = function(){
	if($('form.checkout').length > 0){
		this.form = '#'+$('form.checkout').attr('id');
	}
	else{
		this.form = '#'+$('#order_review').attr('id');
	}
}

/*Setup events nevessary for braintree checkout.*/
BraintreeUtils.prototype.setupEvents = function(){
	$(document.body).on('click', '#add_new_method', this.addNewMethod);
	$(document.body).on('click', '#cancel_add_new', this.cancelAddNew);
	$(document.body).on('click', '.payment-method-item', this.paymentMethodSelected);
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
		form: $('form.checkout').length > 0 ? 'checkout' : 'order_review',
		onReady: function(integration){
			if(! braintreeUtils.integration){
				braintreeUtils.integration = integration;
			}
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

BraintreeUtils.prototype.isPaymentMethodSelected = function(){
	if($('#selected_payment_method').length > 0 && $('#selected_payment_method').val() !== ""){
		return true;
	}
	return false;
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
		if(braintreeUtils.isPaymentMethodSelected()){
			return true;
		}
		else{
			if(braintreeUtils.paymentMethodReceived){
				return true;
			}
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
		$(braintreeUtils.form).append(element);
	}
	$(braintreeUtils.form).submit();
}

BraintreeUtils.prototype.checkoutError = function(){
	braintreeUtils.teardown();
	braintreeUtils.setup();
	braintreeUtils.paymentMethodReceived = false;
}

BraintreeUtils.prototype.teardown = function(){
	if(braintreeUtils.integration){
		braintreeUtils.integration.teardown();
		braintreeUtils.integration = null;
		braintreeUtils.setupCalled = false;
	}
}

BraintreeUtils.prototype.addNewMethod = function(){
	braintreeUtils.removeSelectedPaymentMethod();
	braintreeUtils.hidePaymentMethods();
	braintreeUtils.setup();
	braintreeUtils.showDropinContainer();
}

BraintreeUtils.prototype.cancelAddNew = function(){
	braintreeUtils.teardown();
	braintreeUtils.hideDropinContainer();
	braintreeUtils.showPaymentMethods();
}

BraintreeUtils.prototype.showDropinContainer = function(){
	$('.save-payment-method-label').slideDown(400);
	$('#dropin-container').slideDown(400);
}

BraintreeUtils.prototype.hideDropinContainer = function(){
	$('.save-payment-method-label').slideUp(400);
	$('#dropin-container').slideUp(400);
}

BraintreeUtils.prototype.hidePaymentMethods = function(){
	$('#saved_payment_methods').slideUp(400);
}

BraintreeUtils.prototype.showPaymentMethods = function(){
	$('#saved_payment_methods').slideDown(400);
}

BraintreeUtils.prototype.removeSelectedPaymentMethod = function(){
	$('#selected_payment_method').val("");
}

BraintreeUtils.prototype.paymentMethodSelected = function(){
	$('.payment-method-item').each(function(){
		$(this).removeClass('selected');
	});
	$('#selected_payment_method').val($(this).attr('payment-token'));
	$(this).addClass('selected');
}

BraintreeUtils.prototype.isChangePaymentRequest = function(){
	return $('#order_review').length > 0 && ! document.getElementById('checkout');
}


BraintreeUtils.prototype.submit = function(e){
	if(braintreeUtils.isGatewaySelected()){
		if(braintreeUtils.isPaymentMethodSelected()){
			return true;
		}
		else{
			if($(this).trigger('checkout_place_order') !== false){
				$(this).off('submit', braintreeUtils.submit);
				$(braintreeUtils.form).submit();
			}
			else{
				return false;
			}
		}
	}
}

BraintreeUtils.prototype.hasSavedPaymentMethods = function(){
	return $('#saved_payment_methods').length > 0;
}

var braintreeUtils = null;

$(document.body).on('dropin_container_ready', createBraintreeUtils);