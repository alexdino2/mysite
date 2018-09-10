/*Admin Scripts for Braintree Payment Plugin*/
jQuery(document).ready(function(){
	
var $ = jQuery;

var BraintreeAdmin = function(){
	this.constructor();
};

BraintreeAdmin.prototype.constructor = function(){
	$(document.body).on('change', '#sandbox_environment', this.switchEnvironment);
	$(document.body).on('change', '#production_environment', this.switchEnvironment);
	$(document.body).on('change', '#braintree_subscriptions', this.switchSubscriptions);
	$(document.body).on('change', '#woocommerce_subscriptions', this.switchSubscriptions);
	$(document.body).on('change', '#donation_form_layout', this.displayModalOptions);
	$(document.body).on('click', '.donationColor', this.displayColorPicker);
	$(document.body).on('change', '#donation_address', this.handleAddressClick);
	$(document.body).on('click', '#add_merchant_account', this.addMerchantAccount);
	$('.dashicons.dashicons-trash').on('click', this.deleteItem);
}

BraintreeAdmin.prototype.switchEnvironment = function(){
	var id = $(this).attr('id');
	var sandbox = "sandbox_environment";
	var production = "production_environment";
	var isChecked = $(this).checked;
	if(id === sandbox){
		if(isChecked){
			document.getElementById(production).checked = true;
			$(this).checked = false;
		}
		else{
			document.getElementById(production).checked = false;
			$(this).checked = true;
		}
	}
	else{
		if(isChecked){
			document.getElementById(sandbox).checked = true;
			$(this).checked = false;
		}
		else{
			document.getElementById(sandbox).checked = false;
			$(this).checked = true;
		}
	}
	
};

BraintreeAdmin.prototype.switchSubscriptions = function(){
	if($(this).attr('id') === 'braintree_subscriptions'){
		if($(this).checked){
			$(this).checked = false;
			$('#woocommerce_subscriptions').attr('checked', true);
		}
		else{
			$(this).checked = true;
			$('#woocommerce_subscriptions').attr('checked', false);
		}
	}
	else{
		if($(this).checked){
			$(this).checked = false;
			$('#braintree_subscriptions').attr('checked', true);
		}
		else{
			$(this).checked = true;
			$('#braintree_subscriptions').attr('checked', false);
		}
	}
}

BraintreeAdmin.prototype.displayModalOptions = function(){
	if($(this).val() === 'modal'){
		$('.modalOption').each(function(){
			$(this).closest('tr').slideDown(200);
		})
	}
	else{
		$('.modalOption').each(function(){
			$(this).closest('tr').slideUp(200);
		})
	}
}

BraintreeAdmin.prototype.initializeModalOptions = function(){
	if($('#donation_form_layout').val() === 'modal'){
		$('.modalOption').each(function(){
			$(this).closest('tr').slideDown(200);
		})
	}
	else{
		$('.modalOption').each(function(){
			$(this).closest('tr').slideUp(200);
		})
	}
}

BraintreeAdmin.prototype.updateSubItems = function(){
	$('.subItem').each(function(index){
		$(this).closest('tr').addClass('tr--subItem');
	});
}

BraintreeAdmin.prototype.displayColorPicker = function(){
	$('.donationColor').colorPicker();

}

BraintreeAdmin.prototype.handleAddressClick = function(){
	if($(this).is(':checked')){
		$('.addressOption').each(function(){
			$(this).closest('tr').show();
		});
	}
	else{
		$('.addressOption').each(function(){
			$(this).closest('tr').hide();
		});
	}
}

BraintreeAdmin.prototype.initializeColorPickers = function(){
	$('.donationColor').each(function(){
		braintreeAdmin.displayColorPicker();
	})
}

BraintreeAdmin.prototype.initializeAddressOptions = function(){
	if($('#donation_address').val() !== 'yes'){
		$('.addressOption').each(function(){
			$(this).closest('tr').hide();
		})
	}
}

/*Add an input field for the merchant account.*/
BraintreeAdmin.prototype.addMerchantAccount = function(e){
	e.preventDefault();
	var currency = $('#merchant_account_currency').val();
	var inputName = merchantParams.merchant_account_input;
	if(document.getElementById(inputName + '[' + currency + ']')){
		return;
	}
	var id = inputName + '['+currency+']';
	var name = inputName + '['+currency+']';
	var title = '<th><span>' + merchantParams.merchant_text.replace('%s', currency) + '</span></th>';
    var div = '<td><div><input type="text" value="" id="'+id+'" name="'+name+'"/>' + 
    '<span class="dashicons dashicons-trash"></span></div></td>';
	var html = title + div;
    $('#merchant_accounts').append('<tr>' + html + '</tr>');
    braintreeAdmin.constructor();
}

BraintreeAdmin.prototype.deleteItem = function(){
	var settingName = $(this).prev('input').attr('name');
	braintreeAdmin.ajaxDeleteItem(settingName);
	$(this).closest('tr').remove();
	
}

BraintreeAdmin.prototype.ajaxDeleteItem = function(name){
	var url = ajaxurl;
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {action: 'braintree_for_woocommerce_delete_item', setting: name}
	}).done(function(response){
		if(response.result === 'success'){
			return true;
		}
		else{
			return false;
		}
	}).fail(function(response){
		return false;
	});
		
}

var braintreeAdmin = new BraintreeAdmin();

braintreeAdmin.initializeColorPickers();
braintreeAdmin.updateSubItems();
braintreeAdmin.initializeModalOptions();
braintreeAdmin.initializeAddressOptions();

/*$(function(){
	$('.donationColor').colorPicker();
});*/
})