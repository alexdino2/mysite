<?php
/*Plugin Name: Braintree For Woocommerce
 Plugin URI: https://wordpress.paymentplugins.com
 Description: Accept credit card and paypal payments or donations on your wordpress site using your Braintree merchant account. SAQ A compliant. 
 Version: 2.3.2
 Author: Clayton Rogers, mr.clayton@paymentplugins.com
 Author URI: https://wordpress.paymentplugins.com
 Tested up to: 4.4.2
 */
if(PHP_VERSION < 5.4){
	$message = sprintf('Your PHP Version is %s but Braintree requires PHP Version 5.4 or greater. Please update
				your PHP Version to activate this plugin.', PHP_VERSION);
	echo '<div class="error"><p>'.$message.'</p></div>';
	exit();
}

define('WC_BRAINTREE_CLASSES', plugin_dir_path(__FILE__).'payments/classes/');
define('WC_BRAINTREE_ASSETS', plugin_dir_url(__FILE__).'assets/');
define('WC_BRAINTREE_PLUGIN', plugin_dir_path(__FILE__));
define('WC_BRAINTREE_ADMIN_CLASSES', plugin_dir_path(__FILE__).'admin/classes/');
define('BRAINTREE_LICENSE_ACTIVATION_URL', 'https://wordpress.paymentplugins.com/');
define('BRAINTREE_LICENSE_VERIFICATION_KEY', 'gTys$hsjeScg63dDs35JlWqbx7h');
define('BRAINTREE_DROPIN_JS', 'https://js.braintreegateway.com/js/braintree-2.21.0.min.js');
require_once(WC_BRAINTREE_PLUGIN.'Braintree.php');
require_once(WC_BRAINTREE_PLUGIN.'class-loader.php');
