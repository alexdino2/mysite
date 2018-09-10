<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation class used for processing updates. 
 * @author Clayton Rogers
 * @since 3/20/2016
 */
class Braintree_Install{
	
	private static function getUpdates(){
		return array(
			'2.3.0'=>WC_BRAINTREE_PLUGIN.'admin/updates/braintree-update-2.3.0.php'
		);
	}
	
	public static function init(){
		
		/*Run updates when admin_init action is called.*/
		add_action('admin_init', __CLASS__.'::checkVersion');
	}
	
	/**
	 * Check the version of the current installation.
	 */
	public static function checkVersion(){
		if(get_option('braintree_for_woocommerce_version') < BT_Manager()->version){
			self::update();
			add_action('admin_notices', __CLASS__.'::updateNotice');
		}
	}
	
	public static function update(){
		foreach(self::getUpdates() as $version=>$update){
			require_once($update);
		}
		update_option('braintree_for_woocommerce_version', BT_Manager()->version);
	}
	
	public static function updateNotice(){
		?>
		<div clas="braintree-update-message">
		  <p><?php echo sprintf(__('Thank you for updating Braintree For Woocommerce to version %s.', 'braintree'), BT_Manager()->version)?></p>
		</div>
		<?php 
	}
}
Braintree_Install::init();