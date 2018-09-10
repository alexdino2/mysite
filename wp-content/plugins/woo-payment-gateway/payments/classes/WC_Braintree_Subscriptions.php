<?php
use Braintree\Exception;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Braintree class that handles all subscription payment functionality. 
 * @author Clayton Rogers
 *
 */
class WC_Braintree_Subscriptions extends WC_Braintree_Payments{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Assign all necessary actions and filters. 
	 */
	public static function init(){
		/*Cancel the subscription*/
		add_action('woocommerce_subscription_pending-cancel_'.self::gatewayName, __CLASS__.'::cancelSubscription');
		
		/*Cancel the subscription.*/
		add_action('woocommerce_subscription_cancelled_'.self::gatewayName, __CLASS__.'::cancelSubscription', 10, 1);
		
		/*Recurring payment charge.*/
		add_action('woocommerce_scheduled_subscription_payment_'.self::gatewayName, __CLASS__.'::processRecurringPayment', 10, 2);
		
		/*Payment method change.*/
		add_action('woocommerce_subscription_payment_method_updated_to_'.self::gatewayName, __CLASS__.'::updatePaymentMethod', 10, 2);

		/*Cancel subscription if new gateway is used.*/
		add_action('woocommerce_subscription_payment_method_updated_from_'.self::gatewayName, __CLASS__.'::cancelSubscriptionForOldPaymentMethod', 10, 2);
		
		/*Display payment method on account page.*/
		add_action('woocommerce_my_subscriptions_payment_method', __CLASS__.'::displayPaymentMethod', 10, 2);
		
		/*Save the subscription meta if the Braintree Subscriptions option is enabled.*/
		if(BT_Manager()->isActive('braintree_subscriptions')){
			add_action('save_post', __CLASS__.'::saveSubscriptionMeta');
			if(self::subscriptionsActive()){
				add_filter('woocommerce_add_to_cart_validation', __CLASS__.'::validateAddToCart', 10, 3);
				add_filter('woocommerce_add_to_cart_validation', __CLASS__.'::canItemBeAddedToCart', 10, 3);
			}
			else{
				add_filter('woocommerce_add_to_cart_validation', __CLASS__.'::validateCartEntries', 10, 3);
			}
		}
	}
	
	/**
	 * Return true if the order contains a subscription item. 
	 * @param WC_Order|int $order
	 */
	public static function orderContainsSubscription($order){
		$isSubscription = false;
		if(!is_object($order)){
			$order = wc_get_order($order);
		}
		$items = $order->get_items();
		foreach($items as $item){
			$product_id = $item['product_id'];
			if(WC_Subscriptions_Product::is_subscription($product_id)){
				$isSubscription = true;
			}
		}
		return $isSubscription;
	}
	
	/**
	 * Method that is called during subscription cancellation. If the subscription exists in Braintree's system, 
	 * it will be deleted.
	 * @param WC_Subscription $subscription
	 */
	public static function cancelSubscription(WC_Subscription $subscription){
		if(self::isPaymentChangeRequest()){
			return;
		}
		if(self::isBraintreeSubscription($subscription)){
			return BT_Manager()->cancelBraintreeSubscription($subscription);
		}
		else{
			return BT_Manager()->cancelWooCommerceSubscription($subscription);
		}
	}
	
	/**
	 * Return a list of configured Braintree Plans.
	 */
	public static function getBraintreePlans(){
		$result = array();
		$plans = BT_Manager()->getBraintreePlans();
		if($plans)
		foreach($plans as $plan){
			$result[$plan->id] = $plan->id;
		}
		return $result;
	}
	
	/**
	 * Save the subscription meta for the given post_id. This method is used for changes made by the admin to the subscription 
	 * product.
	 * @param int $post_id
	 */
	public static function saveSubscriptionMeta($post_id){
		if(! isset($_POST['product-type'])){
			return;
		}
		$fields = array('braintree_subscription_id', 'braintree_subscription');
		foreach($fields as $field){
			$value = isset($_POST[$field]) ? $_POST[$field] : $_REQUEST[$field];
			update_post_meta($post_id, $field, stripslashes($value));
		}
		
	}
	
	/**
	 * If Braintree subscriptions is enabled, validate that the cart doesn't already contain the same product. If it does,
	 * do not allow the customer to add the product. 
	 * @param bool $is_valid
	 * @param WC_Product $product
	 * @param int $quantity
	 */
	public static function validateAddToCart($is_valid, $product_id, $quantity){
		if($quantity > 1){
			wc_add_notice(__('You may only add one instance of this subscription to your cart.', 'braintree'), 'error');
			$is_valid = false;
		}
		else{
			foreach(WC()->cart->get_cart() as $cart=>$values){
				$_product = $values['data'];
				if(WC_Subscriptions_Product::is_subscription($product_id))
					if($_product->id == $product_id){
						wc_add_notice(__('You cannot add the same subscription item to your cart.', 'braintree'), 'error');
						$is_valid = false;
				}
			}
		}
		return $is_valid;
	}
	
	/**
	 * If the cart contains products do not allow a product to be added. If the cart contains subscriptions,
	 * do not allow products to be added. 
	 * @param bool $is_valid
	 * @param WC_Product $product
	 * @param int $quantity
	 */
	public static function canItemBeAddedToCart($is_valid, $product_id, $quantity){
		$hasProducts = false;
		$hasSubscriptions = false;
		foreach(WC()->cart->get_cart() as $cart=>$values){
			$_product = $values['data'];
			if(WC_Subscriptions_Product::is_subscription($_product)){
				$hasSubscriptions = true;
			}
			else{
				$hasProducts = true;
			}
		}
		if($hasProducts){
			if(WC_Subscriptions_Product::is_subscription($product_id)){
				$is_valid = false;
				wc_add_notice(__('You cannot add a subscription to your cart when there are products. These items must be purchased separately.', 'braintree'), 'error');
			}
		}
		elseif($hasSubscriptions){
			if(!WC_Subscriptions_Product::is_subscription($product_id)){
				$is_valid = false;
				wc_add_notice(__('You cannot add a product to your cart when there are subscriptions. These items must be purchased separately.', 'braintree'), 'error');
			}
		}
		return $is_valid;
	}
	
	/**
	 * Validate that the product being added does not conflict with other products. Braintree Subscriptions can not be 
	 * added with Products but they can be added together. 
	 * @param bool $is_valid
	 * @param int $product_id
	 * @param int $quantity
	 */
	public static function validateCartEntries($is_valid, $product_id, $quantity){
		if(self::isProductSubscription($product_id)){
			if($quantity > 1){
				$is_valid = false;
				wc_add_notice(__('You cannot add more than one subscription to your cart at a time.', 'braintree'), 'error');
				return $is_valid;
			}
			if(WC()->cart->get_cart_contents_count() >= 1){
				$is_valid = false;
				wc_add_notice(__('You cannot have more than one item in your cart when it\'s a subscription.', 'braintree'), 'error');
				return $is_valid;
			}
		}
		$hasProducts = false;
		$hasSubscriptions = false;
		/*Check if the cart contains a product.*/
		foreach(WC()->cart->get_cart() as $cart=>$values){
			$_product = $values['data'];
			if(! self::isProductSubscription($_product->id)){
				$hasProducts = true;
			}
			else{
				$hasSubscriptions = true;
			}
		}
		if($hasProducts){
			if(self::isProductSubscription($product_id)){
				$is_valid = false;
				wc_add_notice(__('You cannot mix products with subscriptions in your cart. These items must be purchased separately.', 'braintree'), 'error');
			}
		}
		elseif($hasSubscriptions){
			if(! self::isProductSubscription($product_id)){
				$is_valid = false;
				wc_add_notice(__('You cannot mix products with subscriptions in your cart. These items must be purchased separately.', 'braintree'), 'error');
			}
		}
		return $is_valid;
	}
	
	/**
	 * Return true if the subscription is a Braintree subscription, false otherwise. 
	 * @param WC_Subscription $subscription
	 * @return bool $isBraintree;
	 */
	public static function isBraintreeSubscription(WC_Order $subscription){
		$isBraintreeSubscription = false;
		$subscriptionType = get_post_meta($subscription->id, '_subscription_type', true);
		if($subscriptionType === 'braintree'){
			$isBraintreeSubscription = true;
		}
		return $isBraintreeSubscription;
	}
	
	/**
	 * If the subscription is of type "braintree" then there is no need to process the payment since recurring payment occurs automatically. If
	 * however, the subscription is not of type "braintree", process the payment. 
	 * @param int $amount
	 * @param WC_Order $order
	 */
	public static function processRecurringPayment($amount, WC_Order $order){
		if(self::isBraintreeSubscription($order)){
			return true;
		}
		$attribs = array(
				'amount'=>$amount,
				'billing'=>array(
						'countryCodeAlpha2'=>$order->billing_country,
						'firstName'=>$order->billing_first_name,
						'lastName'=>$order->billing_last_name,
						'postalCode'=>$order->billing_postcode,
						'streetAddress'=>$order->billing_address_1
				),
				'options'=>array(
						'submitForSettlement'=>true		
				),
				'paymentMethodToken'=>get_post_meta($order->id, '_payment_method_token', true)
		);
		$attribs = self::getMerchantAccountId($attribs);
		$result = BT_Manager()->sale($attribs);
		if($result instanceof Exception){
			$order->add_order_note(sprintf('Recurring payment for subscription failed. Message: ', $result->getMessage()), 0, false);
			$order->update_status(BT_Manager()->get_option('subscriptions_payment_failed_status'));
		}
		else{
			if($result->success){
				$order->add_order_note(sprintf('Recurring payment charged for subscription. Transaction ID: %s', $result->transaction->id), 0, false);
				if($order instanceof WC_Subscription){
					$order->update_status('active');
				}
			}
			else{
				$order->add_order_note(sprintf('Recurring payment for order failed. Message: ', $result->message), 0, false);
				$order->update_status(BT_Manager()->get_option('subscriptions_payment_failed_status'));
			}
		}
	}
	
	/**
	 * Method that is called when a payment method is being updated for the subscription.
	 * @param string $old_payment_method
	 */
	public static function updatePaymentMethod(WC_Subscription $subscription, $old_payment_method){
		$user_id = wp_get_current_user()->ID;
		$result = array();
		if(! $paymentMethod = self::getPaymentMethodFromChangeRequest($subscription)){
			BT_Manager()->log->writeToLog(sprintf('There was an error updating the payment method for user %s', $user_id));
			$result = BT_Manager()->handleWCError(__('There was an error updating the payment method.', 'braintree'));
			return false;
		}
		/*Update the Braintree Subscription*/
		if(self::isBraintreeSubscription($subscription)){
			if(self::updateBraintreeSubscription($subscription, $paymentMethod['token'])){
				update_post_meta($subscription->id, '_payment_method_token', $paymentMethod['token']);
				update_post_meta($subscription->id, '_payment_method_title', $paymentMethod['description']);
				wc_add_notice(__('Your payment method was updated successfully.', 'braintree'), 'success');
			}
			else{
				wc_add_notice(__('Your payment method could not be updated at this time.', 'braintree'), 'error');
			}
		}
		/*If not a Braintree subscription, then simply update the meta data.*/
		else{
			update_post_meta($subscription->id, '_payment_method_token', $paymentMethod['token']);
			update_post_meta($subscription->id, '_payment_method_title', $paymentMethod['description']);
			wc_add_notice(__('Your payment method was updated successfully.', 'braintree'), 'success');
		}
		return $result;
	}
	
	/**
	 * Return the Braintree Subscription ID saved to the subscription meta. 
	 * @param unknown $user_id
	 */
	public static function getBraintreeSubscriptionId($subscription_id){
		return get_post_meta($subscription_id, '_subscription_id', true);
	}
	
	public static function getPaymentMethodFromChangeRequest(WC_Order $order){
		$paymentMethod = array();
		if($token = self::getRequestParameter('selected_payment_method')){
			$paymentMethods = get_user_meta(wp_get_current_user()->ID, 'braintree_payment_methods', true);
			$paymentMethod = $paymentMethods[$token];
		}
		else{
			$response = BT_Manager()->createBraintreePaymentMethod(array(
					'paymentMethodNonce'=>self::getRequestParameter('payment_method_nonce'),
					'customerId'=>BT_Manager()->getBraintreeCustomerId(wp_get_current_user()->ID),
					'billingAddress'=>array(
							'countryCodeAlpha2'=>$order->billing_country,
							'firstName'=>$order->billing_first_name,
							'lastName'=>$order->billing_last_name,
							'postalCode'=>$order->billing_postcode,
							'streetAddress'=>$order->billing_address_1
					),
					'options'=>array(
							'failOnDuplicatePaymentMethod'=>BT_Manager()->isActive('fail_on_duplicate') ? true : false,
							'makeDefault'=>true
					))
			);
			if($response->success){
				$newMethod = $response->paymentMethod;
				if($newMethod instanceof Braintree_CreditCard){
					$paymentMethod['type'] = $newMethod->cardType;
					$paymentMethod['description'] = $newMethod->cardType.' '.$newMethod->maskedNumber;
					$paymentMethod['token'] = $newMethod->token;
				}
				if($newMethod instanceof Braintree_PayPalAccount){
					$paymentMethod['type'] = 'paypal';
					$paymentMethod['description'] = 'PayPal - '.$newMethod->email;
					$paymentMethod['token'] = $newMethod->token;
				}
			}
			else{
				BT_Manager()->log->writeToLog(sprintf('The payment method for userId %s could not be created. Payment method
							creation failed.', $user_id));
			}
		}
		return $paymentMethod;
	}
	
	/**
	 * Create the Braintree Subscription using the WC_Subscription object.
	 * @param WC_Subscription $subscription
	 */
	public static function createBraintreeSubscription(WC_Subscription $subscription, $token){
		if(! $planId = BT_Manager()->getSubscriptionPlanId($subscription)){
			BT_Manager()->handleWCError(__('You cannot use this payment gateway to change the subscription. There is not a valid
					Plan Id configured for the subscription.','braintree'));
			BT_Manager()->log->writeToLog('Method: WC_Braintree_Subscriptions::createBraintreeSubscription(). There are no planId configured for the subscription.');
			return false;
		}
		$attribs = array(
				'paymentMethodToken'=>$token,
				'planId'=>$planId,
				'price'=>BT_Manager()->getSubscriptionPrice($subscription),
				'firstBillingDate'=>BT_Manager()->getSubscriptionDate($subscription,'next_payment')
		);
		$attribs = self::getMerchantAccountId($attribs);
		$attribs = self::getCustomerObject($attribs, $subscription);
		try{
			$response = Braintree_Subscription::create($attribs);
			if($response->success){
				BT_Manager()->saveBraintreeSubscriptionMeta($subscription, $response->subscription);
				$result = true;
			}
			else{
				throw new Exception($response->message);
			}
		}catch(Exception $e){
			BT_Manager()->log->writeErrorToLog($e->getMessage());
			$result = false;
		}
		return $result;
		
	}
	
	/**
	 * Update the Braintree subscription.
	 * @param WC_Subscription $subscription
	 * @param string $token
	 */
	public static function updateBraintreeSubscription(WC_Subscription $subscription, $token){
		$attribs = array('paymentMethodToken'=>$token);
		return BT_Manager()->updateBraintreeSubscription(self::getBraintreeSubscriptionId($subscription->id), $attribs);
	}
	
	/**
	 * Cancel the Braintree subscription, if it exists. 
	 * @param WC_Subscription $subscription
	 * @param unknown $new_payment_method
	 */
	public static function cancelSubscriptionForOldPaymentMethod(WC_Subscription $subscription, $new_payment_method){
		if(self::gatewayName === $new_payment_method){
			return;
		}
		if(self::isBraintreeSubscription($subscription)){
			if(BT_Manager()->cancelBraintreeSubscription($subscription, false)){
				update_post_meta($subscription->id, '_subscription_type', 'woocommerce');
			}
		}
	}
	
	public static function displayPaymentMethod($payment_method_to_display, WC_Subscription $subscription){
		 if($paymentMethod = get_post_meta($subscription->id, '_payment_method_title', true)){
		 	$payment_method_to_display = $paymentMethod;
		 }
		 return $payment_method_to_display;
	}
	
	/**
	 * Method that determines if the post is a Braintree Subscription.
	 * @param int $post_id
	 */
	public static function isProductSubscription($post_id){
		return get_post_meta($post_id, 'braintree_subscription', true) === 'yes';
	}
	
	public static function orderContainsBraintreeSubscription($order_id){
		$order = wc_get_order($order_id);
		$items = $order->get_items();
		$isSubscription = false;
		foreach($items as $item){
			$product_id = $item['product_id'];
			if(self::isProductSubscription($product_id)){
				$isSubscription = true;
			}
		}
		return $isSubscription;
	}
	
	/**
	 * Create the Braintree Subscription using the WC_Order.
	 * @param WC_Order $order
	 */
	public static function createBraintreeOnlySubscription(WC_Order $order){
		if(!wp_get_current_user()->ID){
			return self::wooCoomerceError(__('In order to purchase a subscription, you must first create an account.', 'braintree'));
		}
		$item = current($order->get_items());
		$attribs = array();
		if(self::getRequestParameter('payment_method_nonce')){
			$response = BT_Manager()->createBraintreePaymentMethod(array(
					'paymentMethodNonce'=>self::getRequestParameter('payment_method_nonce'),
					'customerId'=>BT_Manager()->getBraintreeCustomerId(wp_get_current_user()->ID),
					'billingAddress'=>array(
							'countryCodeAlpha2'=>$order->billing_country,
							'firstName'=>$order->billing_first_name,
							'lastName'=>$order->billing_last_name,
							'postalCode'=>$order->billing_postcode,
							'streetAddress'=>$order->billing_address_1
					),
					'options'=>array(
							'failOnDuplicatePaymentMethod'=>BT_Manager()->isActive('fail_on_duplicate') ? true : false,
							'makeDefault'=>true
					)
			));
			if(!$response->success){
				return self::wooCoomerceError($response->message);
			}
			$attribs['paymentMethodToken'] = $response->paymentMethod->token;
		}
		else{
			$attribs = BT_Manager()->getPaymentMethodFromRequest($attribs);
		}
		$product_id = $item['product_id'];
		$planId = get_post_meta($product_id, 'braintree_subscription_id', true);
		if(! $planId){
			BT_Manager()->log->writeToLog('An attempt was made by a customer to add a subscription product but the product does not have a Braintree PlanId assigned yet.');
			return self::wooCoomerceError(__('A Plan Id has not been assigned to this product. Please contact support so they can correct the problem.', 'braintree'), 'error');
		}
		try{
			$attribs['id'] = $order->id;
			$attribs['planId'] = $planId;
			$response = Braintree_Subscription::create($attribs);
			if($response->success){
				$order->update_status(BT_Manager()->get_option('braintree_only_subscriptions_charge_success'));
				WC()->cart->empty_cart();
				BT_Manager()->saveBraintreeSubscriptionMeta($order, $response->subscription);
				update_post_meta($order->id, 'braintree_subscription', 'yes');
				return BT_Manager()->handleResponseSuccess($order);
			}
			else{
				return BT_Manager()->handleResponseError($response);
			}
			}catch(Exception $e){
				return self::wooCoomerceError(__('There was an error processing your subscription payment.', 'error'));
				BT_Manager()->log->writeErrorToLog($e->getMessage());
			}
	
	}
	
}
WC_Braintree_Subscriptions::init();