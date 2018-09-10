<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main admin class for the braintree plugin. Controls admin screens for plugin configuration. 
 * @author Clayton Rogers
 * @since 3/12/16
 */
class WC_Braintree_Admin{
	
	private static $api_view = array('apisettings_title', 'license_status_notice', 'production_environment', 
			'production_merchant_id', 'production_private_key', 'production_public_key', 'sandbox_environment',
			'sandbox_merchant_id', 'sandbox_private_key', 'sandbox_public_key'
	);
	private static $wooCommerce_view = array('woocommerce_title', 'enabled', 'title_text', 'order_status', 'order_prefix', 'payment_methods',
			'fail_on_duplicate', 'paypal_only', 'woocommerce_braintree_merchant_acccounts');
	private static $debugLog_view = array('debug_title', 'enable_debug');
	
	private static $license_view = array('license_title', 'license_status', 'license');
	
	private static $subscriptions_view = array('subscriptions_title','braintree_subscriptions', 'braintree_subscriptions_charge_success', 
			'braintree_subscriptions_charge_failed', 'braintree_subscriptions_active', 'braintree_subscriptions_expired', 'braintree_subscriptions_past_due', 'braintree_subscriptions_cancelled',
			'woocommerce_subscriptions', 'woocommerce_subscriptions_prefix', 'subscriptions_payment_success_status', 'subscriptions_payment_failed_status');
	
	private static $braintree_subscriptions_view = array('braintree_subscriptions_title','braintree_subscriptions', 'braintree_only_subscriptions_charge_success', 
			'braintree_only_subscriptions_charge_failed', 'braintree_only_subscriptions_active', 'braintree_only_subscriptions_expired', 'braintree_only_subscriptions_past_due', 'braintree_only_subscriptions_cancelled');
	
	private static $donations_view = array('donations_title', 'donation_form_layout', 'donation_modal_button_text', 'donation_modal_button_background', 
			'donation_modal_button_border', 'donation_modal_button_text_color', 'donation_button_text', 'donation_button_background', 'donation_button_border', 
			'donation_button_text_color', 'donation_address', 'donation_email', 'donation_default_country', 'donation_merchant_account_id', 'donation_name', 'donation_currency', 'donation_success_url', 'donation_payment_methods');
	
	private static $webhooks_view = array('webhooks_title', 'enable_webhooks', 'webhook_subscription_charged_successfully', 'webhook_subscription_charged_unsuccessfully', 'webhook_subscription_went_active', 
				'webhook_subscription_past_due', 'webhook_subscription_expired', 'webhook_subscription_cancelled');
	
	/**
	 * Set initial values required by the class to function. 
	 */
	public static function init(){
		add_action('admin_enqueue_scripts', __CLASS__.'::loadAdminScripts');
		add_action('admin_menu', __CLASS__.'::braintreeAdminMeu');
		add_action('admin_init', __CLASS__.'::saveBraintreeSettings');
		add_action('wp_ajax_braintree_for_woocommerce_delete_item', __CLASS__.'::deleteSetting');
	}
	
	
	public static function braintreeAdminMeu(){
		add_menu_page('Braintree Payments', 'Braintree Payments', 'manage_options', 'braintree-payments-menu', null, null, '9.134');
		add_submenu_page('braintree-payments-menu', 'Braintree Settings', 'Braintree Settings', 'manage_options', 'braintree-payment-settings', 'WC_Braintree_Admin::showBraintreePaymentsView');
		add_submenu_page('braintree-payments-menu', 'Woocommerce Settings', 'Woocommerce Settings', 'manage_options', 'braintree-woocommerce-settings', 'WC_Braintree_Admin::showWoocommerceView');
		add_submenu_page('braintree-payments-menu', 'Activate License', 'Activate License', 'manage_options', 'braintree-license-page', 'WC_Braintree_Admin::showLicenseView');
		add_submenu_page('braintree-payments-menu', 'Subscriptions', 'Subscriptions', 'manage_options', 'braintree-subscriptions-page', 'WC_Braintree_Admin::showSubscriptionView');
		add_submenu_page('braintree-payments-menu', 'Webhooks', 'Webhooks', 'manage_options', 'braintree-webhooks-page', 'WC_Braintree_Admin::showWebhookView');
		add_submenu_page('braintree-payments-menu', 'Donations', 'Donations', 'manage_options', 'braintree-donations-page', 'WC_Braintree_Admin::showDonationsView');
		add_submenu_page('braintree-payments-menu', 'Debug Log', 'Debug Log', 'manage_options', 'braintree-debug-log', 'WC_Braintree_Admin::showDebugView');
		add_submenu_page('braintree-payments-menu', 'Tutorials', 'Tutorials', 'manage_options', 'braintree-payments-tutorials', 'Braintree_Admin_Pages::showTutorialsView');
		remove_submenu_page('braintree-payments-menu', 'braintree-payments-menu');
	}
	
	public static function loadAdminScripts(){
		wp_enqueue_script('color-picker-colors-script', WC_BRAINTREE_ASSETS.'js/tinyColorPicker-master/colors.js');
		wp_enqueue_script('color-picker-script', WC_BRAINTREE_ASSETS.'js/tinyColorPicker-master/jqColorPicker.js', array('color-picker-colors-script'));
		wp_enqueue_script('braintree-admin-script', WC_BRAINTREE_ASSETS.'js/admin-script.js', array('jquery', 'color-picker-script'));
		wp_enqueue_script('braintree-admin-tutorials', WC_BRAINTREE_ASSETS.'js/admin-tutorial.js', array('jquery'));
		wp_enqueue_style('braintree-admin-style', WC_BRAINTREE_ASSETS.'css/admin-style.css');
	}
	
	public static function saveBraintreeSettings(){
		if(isset($_POST['save_braintree_apisettings'])){
			self::saveSettings(self::$api_settings, 'braintree-payment-settings');
		}
		elseif(isset($_POST['save_braintree_payment_settings']))
		{
			self::saveSettings(self::$api_view, 'braintree-payment-settings');
		}
		elseif(isset($_POST['save_braintree_woocommerce_settings'])){
			add_filter('braintree_for_woocommerce_save_settings', __CLASS__.'::saveMerchantAccounts');
			self::saveSettings(self::$wooCommerce_view, 'braintree-woocommerce-settings');
		}
		elseif(isset($_POST['save_woocommerce_subscription_settings'])){
			self::saveSettings(self::$subscriptions_view, 'braintree-subscriptions-page');
		}
		elseif(isset($_POST['save_braintree_subscription_settings'])){
			self::saveSettings(self::$braintree_subscriptions_view, 'braintree-subscriptions-page');
		}
		elseif(isset($_POST['save_braintree_donation_settings'])){
			self::saveSettings(self::$donations_view, 'braintree-donations-page');
		}
		elseif(isset($_POST['activate_braintree_license'])){
			$license_key = isset($_POST['license']) ? $_POST['license'] : '';
			BT_Manager()->activateLicense($license_key);
			//wp_redirect(admin_url().'admin.php?page=braintree-license-page');
		}
		elseif(isset($_POST['braintree_save_debug_settings'])){
			self::saveSettings(self::$debugLog_view, 'braintree-debug-log');
		}
		elseif(isset($_POST['save_braintree_webhooks'])){
			self::saveSettings(self::$webhooks_view, 'braintree-webhooks-page');
		}
		elseif(isset($_POST['braintree_delete_debug_log'])){
			BT_Manager()->deleteDebugLog();
		}
	}
	
	public static function saveSettings($fields, $page){
		$defaults = array('title'=>'', 'type'=>'', 'value'=>'', 'type'=>'', 'class'=>array(), 'default'=>'');
		$settings = BT_Manager()->settings;
		$required_settings = BT_Manager()->required_settings;
		foreach($fields as $field){
			$value = isset($required_settings[$field]) ? $required_settings[$field] : $defaults;
			$value = wp_parse_args($value, $defaults);
			if(is_array($value['value']) && $value['type'] === 'checkbox'){
				foreach($value['value'] as $k=>$v){
					$settings[$field][$k] = isset($_POST[$k]) ? $_POST[$k] : '';
				}
			}
			else {
				$settings[$field] = isset($_POST[$field]) ? $_POST[$field] : $value['default'];
			}
				
		}
		$settings = apply_filters('braintree_for_woocommerce_save_settings', $settings);
		BT_Manager()->update_settings($settings);
		wp_redirect(get_admin_url().'admin.php?page='.$page);
	}
	
	public static function getAdminHeader(){
		?>
					<div class="braintree-header"><div class="worldpay-logo-inner">
					 <a><img src="<?php echo WC_BRAINTREE_ASSETS.'images/braintree-logo-white.svg'?>" class="braintree-logo-header" /></a>
					 </div>
					 <ul>
					 	<li><a href="?page=braintree-payment-settings">API Settings</a></li>
					 	<li><a href="?page=braintree-woocommerce-settings">WooCommerce Settings</a></li>
					    <li><a href="?page=braintree-subscriptions-page">Subscriptions</a></li>
					 	<li><a href="?page=braintree-donations-page">Donations</a></li>
					 	<li><a href="?page=braintree-debug-log">Debug Log</a></li>
					 	<li><a href="?page=braintree-license-page">Activate License</a></li>
					 </ul>
					<?php //echo BT_Manager()->get_payment_pluginsUrl()?>
					</div>
					<?php 
	}
		
	public static function displaySettingsPage($fields_to_display, $page, $button){
		$form_fields = BT_Manager()->required_settings;
		$html = '<div><form method="POST" action="'.get_admin_url().'admin.php?page='.$page.'">';
		$html .= '<table class="braintree-woocommerce-settings"><tbody>';
		foreach($fields_to_display as $key){
			$value = isset(BT_Manager()->required_settings[$key]) ? BT_Manager()->required_settings[$key] : array();
			$html .= HTML_Helper::buildSettings($key, $value, BT_Manager()->settings);
		}
		$html .= '</tbody></table>';
		if($button != null){
			$html .= '<div><input name="'.$button.'" class="braintree-payments-save" type="submit" value="Save"></div>';
		}
		$html .= '</form></div>';
		echo $html;
	}
		
	public static function showBraintreePaymentsView(){
		self::getAdminHeader();
		self::displaySettingsPage(self::$api_view, 'braintree-payment-settings', 'save_braintree_payment_settings');
	}
	
	public static function showWoocommerceView(){
		self::getAdminHeader();
		if(class_exists('WC_Payment_Gateway')){
			self::displaySettingsPage(self::$wooCommerce_view, 'braintree-woocommerce-settings', 'save_braintree_woocommerce_settings');
		}
		else{
	    	?>
	    	<div>
	    	  <h1 class="warning"><?php echo __('You must activate the WooCommerce Plugin before this screen is available.', 'braintree')?></h1>
	    	</div>
	    	<?php 	
		}
	}
	
	public static function showDonationsView(){
		self::getAdminHeader();
		self::displaySettingsPage(self::$donations_view, 'braintree-donations-page', 'save_braintree_donation_settings');
	}
	
	public static function showDebugView(){
		self::getAdminHeader();
		self::displaySettingsPage(self::$debugLog_view, 'braintree-debug-log', 'braintree_save_debug_settings');
		?>
		<form class="braintree-deletelog-form" name="braintree_woocommerce_form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page=braintree-debug-log') ?>">
			<button name="braintree_delete_debug_log" class="braintree-payments-save" type="submit">Delete Log</button>
		</form>
		<div class="config-separator"></div>
			<div class="braintree-debug-log-container">
				<?php echo BT_Manager()->display_debugLog()?>
			</div>
		<?php 
	}
	
	public static function showLicenseView(){
		self::getAdminHeader();
		self::displaySettingsPage(self::$license_view, 'braintree-license-page', 'activate_braintree_license');
	}
	
	public static function showSubscriptionView(){
		self::getAdminHeader();
		if(class_exists('WC_Subscription')){
			self::displaySettingsPage(self::$subscriptions_view, 'braintree-subscriptions-page', 'save_woocommerce_subscription_settings');
		}
		else{
	    	self::displaySettingsPage(self::$braintree_subscriptions_view, 'braintree-subscriptions-page', 'save_braintree_subscription_settings');
		}
	}
	
	public static function showWebhookView(){
		self::getAdminHeader();
		self::displaySettingsPage(self::$webhooks_view, 'braintree-webhooks-page', 'save_braintree_webhooks');
	}
	
	public static function getLicenseStatus(){
		$html = '';
		$license_status = BT_Manager()->get_option('license_status');
		$license_status = $license_status === 'active' ? 'Active' : 'Inactive';
		$html .= '<div class="license--'.$license_status.'"><span>'.$license_status.'</span>';
		return $html;
	}
	
	public static function getWebhooksUrl(){
		return get_site_url().'/webooks/braintree?woocommerce_braintree_subscription_hook=process';
	}
	
	/**
	 * Generate html selection element based on countries and currency. 
	 * @param string $defaultCountry
	 */
	public static function getCountriesOptions($value){
		$defaultCountry = BT_Manager()->get_option('donation_currency');
		$html = '<select name="donation_currency" id="donation_currency">';
		foreach(Braintree_Currencies::getCurrencies() as $prefix=>$description){
			$selected = $prefix === $defaultCountry ? 'selected' : '';
			$html .= '<option value="'.$prefix.'" '.$selected.'>'.$description.' ('.Braintree_Currencies::getCurrencySymbol($prefix).')</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	/**
	 * Display a list of country currencies that can be associated with the Merchant AccountID's. 
	 */
	public static function displayMerchantAccounts(){
		$json = json_encode(array(
					'merchant_text'=>BT_Manager()->getEnvironment() === 'sandbox' ? __('Sandbox Merchant Account ID (%s)', 'braintree') : __('Merchant Account ID (%s)', 'braintree'),
					'merchant_account_input'=>'woocommerce_braintree_'.BT_Manager()->getEnvironment().'_merchant_account_id'
					)
				);
		ob_start();
		?>
		<script> var merchantParams = <?php echo $json?></script>
		<table class="merchant-account-add">
		  <tr>
		    <th>
		      <div>
		        <select id="merchant_account_currency">
		        <?php foreach (get_woocommerce_currencies() as $currency=>$description){?>
		      	  <option value="<?php echo $currency?>"><?php echo $description .' ( '. Braintree_Currencies::getCurrencySymbol( $currency ).' )'?></option>
		        <?php }?>
		        </select>
		      </div>
		    </th>
		   </tr>
		   <tr>
		    <td>
		      <a class="braintree-admin-button" href="#" id="add_merchant_account"><?php echo __('Add Merchant Account','braintree')?></a>
		    </td>
		   </tr>
		  <table id="merchant_accounts">
		    <?php 
		    	foreach(get_woocommerce_currencies() as $currency=>$description){
		    		$merchantAccount = BT_Manager()->get_option('woocommerce_braintree_'.BT_Manager()->getEnvironment().'_merchant_account_id['.$currency.']');
		    		if(! empty($merchantAccount)){
		    		?>
		    		<tr>
		    		  <th>
		    		    <span><?php echo BT_Manager()->getEnvironment() === 'sandbox' ? __('Sandbox Merchant Account ID', 'braintree').' ('.$currency.')' : __('Merchant Account ID', 'braintree').' ('.$currency.')'?></span>
		    		  </th>
		    		  <td>
		    		  <div>
		    		    <input id="woocommerce_braintree_<?php echo BT_Manager()->getEnvironment()?>_merchant_account_id[<?php echo $currency?>]" name="woocommerce_braintree_<?php echo BT_Manager()->getEnvironment()?>_merchant_account_id[<?php echo $currency?>]" type="text" value="<?php echo $merchantAccount?>"/>
		    		    <span class="dashicons dashicons-trash"></span>
		    		  </div>
		    		  </td>
		    	   </tr>
		    		<?php 
		    	}
		    	
			}?>
		  </table>
		</table>
		<?php 
		$html = ob_get_clean();
		return $html;
	}
	
	public static function saveMerchantAccounts($settings){
		if(isset($_POST['woocommerce_braintree_'.BT_Manager()->getEnvironment().'_merchant_account_id'])){
			$array = $_POST['woocommerce_braintree_'.BT_Manager()->getEnvironment().'_merchant_account_id'];
			foreach($array as $prefix=>$value){
				$settings['woocommerce_braintree_'.BT_Manager()->getEnvironment().'_merchant_account_id['.$prefix.']'] = $value;
			}
		}
		return $settings;
	}
		
	public static function deleteSetting(){
		$name = $_POST['setting'];
		unset(BT_Manager()->settings[$name]);
		BT_Manager()->update_settings();
		wp_send_json(array('result'=>'success'));
	}
}
WC_Braintree_Admin::init();