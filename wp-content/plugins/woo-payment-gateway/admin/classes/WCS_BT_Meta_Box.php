<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Static class used to add html to meta boxes required by admin configuration.
 * @author Clayton Rogers
 *
 */
class WCS_BT_Meta_Box{
	
	/**
	 * Setup all filters for functionality. 
	 */
	public static function init(){
		if(BT_Manager()->isActive('braintree_subscriptions')){
			if(BT_Manager()->subscriptionsActive()){
				add_action('woocommerce_subscriptions_product_options_pricing', __CLASS__.'::doSubscriptionOutput', 1);
			}
			else add_action('woocommerce_product_options_general_product_data', __CLASS__.'::doSubscriptionOutput', 1);
		}
	}
	
	/**
	 * Output the Braintree Plan ID's to the WooCommerce production box. 
	 */
	public static function doSubscriptionOutput(){
		echo '<div class="options_group braintree_plans_options">';
		if(! WC_Braintree_Payments::subscriptionsActive()){
			woocommerce_wp_checkbox(array(
					'label'=>__('Sell As Subscription', 'braintree'),
					'name'=>'braintree_subscription',
					'id'=>'braintree_subscription',
					'cbvalue'=>'yes',
					'desc_tip'=>true,
					'description'=>__('If you want this product to be sold as a subsription, then click the checkbox and select the planId associated with the subsciption.', 'braintree')
			));
		}
		woocommerce_wp_select(array(
			'id'=>'braintree_subscription_id',
			'class'=>'form-field',
			'label'=>__('Braintree Plan ID\'s', 'braintree'),
			'options'=>WC_Braintree_Subscriptions::getBraintreePlans(),
			'description'=>__('In order to assign a Braintree Plan Id to the product, you must first configure
				a recurring plan inside your Braintree account.', 'braintree'),
			'desc_tip'=>true
		));
		echo '</div>';
	}
}
add_action('admin_init', 'WCS_BT_Meta_Box::init');