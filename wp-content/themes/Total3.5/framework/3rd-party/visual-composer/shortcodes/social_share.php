<?php
/**
 * Visual Composer Divider
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 4.41
 */

if ( ! class_exists( 'VCEX_Social_Share_Shortcode' ) ) {

	class VCEX_Social_Share_Shortcode {

		/**
		 * Main constructor
		 *
		 * @since 4.4.1
		 */
		public function __construct() {
			add_shortcode( 'vcex_social_share', array( 'VCEX_Social_Share_Shortcode', 'output' ) );
			vc_lean_map( 'vcex_social_share', array( 'VCEX_Social_Share_Shortcode', 'map' ) );
		}

		/**
		 * Shortcode output => Get template file and display shortcode
		 *
		 * @since 4.4.1
		 */
		public static function output( $atts, $content = null ) {
			ob_start();
			include( locate_template( 'vcex_templates/vcex_social_share.php' ) );
			return ob_get_clean();
		}

		/**
		 * Map shortcode to VC
		 *
		 * @since 4.4.1
		 */
		public static function map() {
			return array(
				'name' => __( 'Social Share', 'total' ),
				'description' => __( 'Display post social share.', 'total' ),
				'base' => 'vcex_social_share',
				'icon' => 'vcex-social-share vcex-icon fa fa-share-alt',
				'category' => wpex_get_theme_branding(),
				'params' => array(
					array(
						'type' => 'vcex_visibility',
						'heading' => __( 'Visibility', 'total' ),
						'param_name' => 'visibility',
					),
					array(
						'type' => 'vcex_select_buttons',
						'heading' => __( 'Style', 'total' ),
						'param_name' => 'style',
						'std' => 'flat',
						'choices' => array(
							'flat' => __( 'Flat', 'total' ),
							'minimal' => __( 'Minimal', 'total' ),
							'three-d' => __( '3D', 'total' ),
							'rounded' => __( 'Rounded', 'total' ),
						),
					),
					// Sites
					array(
						'type' => 'param_group',
						'param_name' => 'sites',
						'value' => urlencode( json_encode( array(
							array(
								'site' => 'twitter',
							),
							array(
								'site' => 'facebook',
							),
							array(
								'site' => 'google_plus',
							),
							array(
								'site' => 'pinterest',
							),
							array(
								'site' => 'linkedin',
							),
						) ) ),
						'params' => array(
							array(
								'type' => 'dropdown',
								'heading' => __( 'Site', 'total' ),
								'param_name' => 'site',
								'admin_label' => true,
								'value' => array(
									'Twitter' => 'twitter',
									'Facebook' => 'facebook',
									'Google Plus' => 'google_plus',
									'Pinterest' => 'pinterest',
									'LinkedIn' => 'linkedin',
								),
							),
						),
					),
				)
			);
		}
	}
}
new VCEX_Social_Share_Shortcode;