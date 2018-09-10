<?php
/**
 * Visual Composer Spacing
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.0
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_spacing extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_spacing.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 1.4.1
 */
function vcex_spacing_vc_map() {
	return array(
		'name' => esc_html__( 'Spacing', 'total' ),
		'description' => esc_html__( 'Adds spacing anywhere you need it', 'total' ),
		'base' => 'vcex_spacing',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-spacing vcex-icon fa fa-sort',
		'params' => array(
			array(
				'type' => 'textfield',
				'admin_label' => true,
				'heading'  => esc_html__( 'Spacing', 'total' ),
				'param_name'  => 'size',
				'value'  => '30px',
			),
			array(
				'type' => 'textfield',
				'heading'  => esc_html__( 'Custom Classes', 'total' ),
				'param_name'  => 'class',
			),
			array(
				'type'  => 'dropdown',
				'heading' => esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'value' => array_flip( wpex_visibility() ),
			),
		)
	);
}
vc_lean_map( 'vcex_spacing', 'vcex_spacing_vc_map' );