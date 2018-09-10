<?php 
/**
 * File that is used for Version 2.3.0 update.
 */

$environments = array('sandbox', 'production');

$license_status = BT_Manager()->get_payments_config('braintree_payments_license_status');

$license = BT_Manager()->get_payments_config('braintree_payments_license');

BT_Manager()->settings['license_status'] = ! empty($license_status) ? $license_status : BT_Manager()->settings['license_status'];

BT_Manager()->settings['license'] = ! empty($license) ? $license : BT_Manager()->settings['license'];

$api_keys = BT_Manager()->get_payments_config('braintree_api_keys_config');

$woocommerceConfig = BT_Manager()->get_payments_config('braintree_payments_woocommerce_config');

if(! empty($api_keys)){
	foreach($environments as $environment){
		$merchantId = $api_keys[$environment]['merchantId'];
		$publicKey = $api_keys[$environment]['public_key'];
		$privateKey = $api_keys[$environment]['private_key'];
		BT_Manager()->settings[$environment.'_merchant_id'] = $merchantId;
		BT_Manager()->settings[$environment.'_public_key'] = $publicKey;
		BT_Manager()->settings[$environment.'_private_key'] = $privateKey;
	}
	$activeEnvironment = $api_keys['environment'];
	BT_Manager()->settings[$activeEnvironment.'_environment'] = 'yes';
}

BT_Manager()->settings['enabled'] = 'yes';

BT_Manager()->update_settings();

BT_Manager()->delete_payments_config('braintree_payments_license_status');

BT_Manager()->delete_payments_config('braintree_payments_license');

BT_Manager()->delete_payments_config('braintree_api_keys_config');

BT_Manager()->delete_payments_config('woocommerce_braintree_payment_gateway_settings');

BT_Manager()->delete_payments_config('braintree_payments_woocommerce_config');

BT_Manager()->delete_payments_config('braintree_subscription_config');

delete_option('braintree_payments_next_activation');

?>