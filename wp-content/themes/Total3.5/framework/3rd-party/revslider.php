<?php
/**
 * RevSlider Config
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.4
 */

// Start Class
if ( ! class_exists( 'WPEX_RevSlider_Config' ) ) {

	class WPEX_RevSlider_Config {
		public $valid;

		/**
		 * Start things up
		 *
		 * @since 3.4.0
		 */
		public function __construct() {
			$this->valid = get_option( 'revslider-valid', 'false' );

			// Admin functions
			if ( is_admin() ) {

				// Remove things when license isn't valid
				if ( 'false' == $this->valid ) {

					// Remove notices
					global $pagenow;
					if ( $pagenow == 'plugins.php' && 'false' == $this->valid ) {
						add_action( 'admin_notices', array( $this, 'remove_plugins_page_notices' ), 9999 );
					}

					// Remove update notice
					if ( 'admin.php' == $pagenow && 'false' == $this->valid ) {
						wpex_remove_class_filter( 'admin_notices', 'RevSliderAdmin', 'add_notices', 10 );
					}

					// Remove addons page
					add_action( 'admin_menu', array( 'WPEX_RevSlider_Config', 'remove_addons_page' ), 999 );

				}

				// Remove activation notice
				wpex_remove_class_filter( 'admin_notices', 'RevSliderAdmin', 'addActivateNotification', 10 );

				// Remove metabox from VC grid builder
				add_action( 'do_meta_boxes', array( 'WPEX_RevSlider_Config', 'remove_metabox' ) );

			}

			// Front end functions
			else {

				// Remove front-end meta generator
				add_filter( 'revslider_meta_generator', '__return_false' );

			}

		}

		/**
		 * Remove Revolution Slider plugin notices
		 *
		 * @since 3.4.0
		 */
		public function remove_plugins_page_notices() {
			$plugin_id = 'revslider/revslider.php';

			// Remove plugin page purchase notice
			remove_action( 'after_plugin_row_'. $plugin_id, array( 'RevSliderAdmin', 'show_purchase_notice' ), 10, 3 );

			// Hide update notice if not valid
			remove_action( 'after_plugin_row_' . $plugin_id, array( 'RevSliderAdmin', 'show_update_notice' ), 10, 3 );

		}

		/**
		 * Remove metabox from VC grid builder
		 *
		 * @since 3.6.0
		 */
		public static function remove_metabox() {
			remove_meta_box( 'mymetabox_revslider_0', array( 'vc_grid_item', 'post', 'page', 'portfolio', 'staff', 'testimonials', 'products', 'tribe_events', 'templatera' ), 'normal' );
		}

		/**
		 * Remove Addons Page
		 *
		 * @since 4.4
		 */
		public static function remove_addons_page() {
			remove_submenu_page( 'revslider', 'rev_addon' );
		}

	}

}
new WPEX_RevSlider_Config();