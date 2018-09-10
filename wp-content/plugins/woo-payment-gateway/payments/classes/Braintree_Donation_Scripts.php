<?php
class Braintree_Donation_Scripts{
	
	public static function getModalScript(){
		?>
<script>
/*Script for modal functionality.*/
var DonationModal = function(){
	this.clientToken = document.getElementById('client_token').value;
	this.setupEvents();
};

DonationModal.prototype.setupEvents = function(){
	//$(document.body).on('setup_braintree', this.setup);
	$(document.body).on('click', '#modal_button', this.modalButtonClicked);
	$(document.body).on('click', '#cancel_donation', this.cancelDonationClicked);
	$(document.body).on('keyup', '#donation-form input', this.clearInvalidEntries)
}

/*Setup the Braintree Dropin*/
DonationModal.prototype.setup = function(){
	if(donationModal.integration){
		return;
	}
	braintree.setup(donationModal.clientToken, 'dropin', {
			container: 'dropin-container',
			form: 'donation-form',
			onReady: function(integration){
				donationModal.onReady(integration);
			},
			onPaymentMethodReceived: function(response){
				donationModal.onPaymentMethodReceived(response);
			}
	});
}

/*Handle the onReady callback*/
DonationModal.prototype.onReady = function(integration){
	donationModal.integration = integration;
}

/*Handle the resposne from the onPaymentMethodReceived callback.*/
DonationModal.prototype.onPaymentMethodReceived = function(response){
	donationModal.paymentMethodReceived = true;
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
		$('#donation-form').append(element);
	}
	donationModal.validateInputFields();
}

DonationModal.prototype.teardown = function(){
	if(donationModal.integration){
		donationModal.integration.teardown();
		donationModal.integration = null;
	}
}

DonationModal.prototype.submitPayment = function(){
	var data = $('#donation-form').serialize();
	var url = $('#ajax_url').val();
	$('.overlay-payment-processing').addClass('active');
	$.ajax({
			type:'POST',
			url: url,
			dataType: 'json',
			data: data
	}).done(function(response){
		$('.overlay-payment-processing').removeClass('active');
		if(response.result === 'success'){
			donationModal.redirect(response.url);
		}
		else{
			donationModal.showErrorMessage(response.message);
		}
	}).fail(function(response){
		$('.overlay-payment-processing').removeClass('active');
		donationModal.showErrorMessage(response.message);
	});
}

DonationModal.prototype.redirect = function(url){
	window.location.replace(url);
}

DonationModal.prototype.showErrorMessage = function(message){
	$('#error_messages').html(message);
}

DonationModal.prototype.modalButtonClicked = function(){
	if(! donationModal.integration){
		donationModal.setup();
	}
	donationModal.displayDonationForm();
}

DonationModal.prototype.displayDonationForm = function(callback){
	//$('#donation_container').fadeIn(400, callback);
	donationModal.displayOverlay(callback);
}

DonationModal.prototype.displayOverlay = function(callback){
	$('#donation_overlay').fadeIn(400, callback);
}

DonationModal.prototype.hideOverlay = function(callback){
	$('#donation_overlay').fadeOut(400, callback);
}

DonationModal.prototype.cancelDonationClicked = function(){
	donationModal.teardown();
	donationModal.hideOverlay();
	donationModal.clearErrorMessages();
}

/*Validate the inputs*/
DonationModal.prototype.validateInputFields = function(){
	var hasFailures = false;
	$('#donation-form input').each(function(){
		if($(this).val() === ""){
			$(this).parent().find('div.invalid-input-field').show().addClass('active');
			hasFailures = true;
		}
	});
	if(! hasFailures){
		donationModal.submitPayment();
	}
}

DonationModal.prototype.clearInvalidEntries = function(){
	$(this).parent().find('div.invalid-input-field').hide().removeClass('active');
}

DonationModal.prototype.clearErrorMessages = function(){
	$('#error_messages').empty();
}


var $ = jQuery;
var donationModal = new DonationModal();
</script>
		<?php 
	}
	
	public static function getInlineScript(){
?>
<script>
var InlineDonation = function(){
	this.clientToken = document.getElementById('client_token').value;
	this.setupEvents();
}

InlineDonation.prototype.setupEvents = function(){
	$(document.body).on('keyup', '#donation-form input', this.clearInvalidEntries);
	this.setup();
}

InlineDonation.prototype.setup = function(){
	braintree.setup(this.clientToken, 'dropin',{
		container: 'dropin-container',
		form: 'donation-form',
		onReady: function(integration){
			inlineDonation.integration = integration;
		},
		onPaymentMethodReceived: function(response){
			inlineDonation.onPaymentMethodReceived(response);
		}
	})
}

InlineDonation.prototype.onPaymentMethodReceived = function(response){
	inlineDonation.paymentMethodReceived = true;
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
		$('#donation-form').append(element);
	}
	inlineDonation.validateInputFields();
}

InlineDonation.prototype.validateInputFields = function(){
	var hasFailures = false;
	$('#donation-form input').each(function(){
		if($(this).val() === ""){
			$(this).parent().find('div.invalid-input-field').show().addClass('active');
			hasFailures = true;
		}
	});
	if(! hasFailures){
		inlineDonation.submitPayment();
	}
}

InlineDonation.prototype.submitPayment = function(){
	var data = $('#donation-form').serialize();
	var url = $('#ajax_url').val();
	$('.overlay-payment-processing').addClass('active');
	$.ajax({
			type:'POST',
			url: url,
			dataType: 'json',
			data: data
	}).done(function(response){
		$('.overlay-payment-processing').removeClass('active');
		if(response.result === 'success'){
			inlineDonation.redirect(response.url);
		}
		else{
			inlineDonation.showErrorMessage(response.message);
		}
	}).fail(function(response){
		$('.overlay-payment-processing').removeClass('active');
		inlineDonation.showErrorMessage(response.message);
	});
}

InlineDonation.prototype.redirect = function(url){
	window.location.replace(url);
}

InlineDonation.prototype.showErrorMessage = function(message){
	$('#error_messages').html(message);
}

InlineDonation.prototype.clearInvalidEntries = function(){
	$(this).parent().find('div.invalid-input-field').hide().removeClass('active');
}

InlineDonation.prototype.clearErrorMessages = function(){
	$('#error_messages').empty();
}

InlineDonation.prototype.clearInvalidEntries = function(){
	$(this).parent().find('div.invalid-input-field').hide().removeClass('active');
}

InlineDonation.prototype.displayOverlay = function(callback){
	$('#donation_overlay').fadeIn(400, callback);
}

InlineDonation.prototype.hideOverlay = function(callback){
	$('#donation_overlay').fadeOut(400, callback);
}

var $ = jQuery;

var inlineDonation = new InlineDonation();
</script>
<?php 
	}
}