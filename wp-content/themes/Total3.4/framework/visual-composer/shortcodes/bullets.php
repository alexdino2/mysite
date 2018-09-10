<?php
/**
 * Visual Composer Bullets
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
class WPBakeryShortCode_vcex_bullets extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_bullets.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since Total 1.4.1
 */
function vcex_bullets_shortcode_vc_map() {
	return array(
		'name' => esc_html__( 'Bullets', 'total' ),
		'description' => esc_html__( 'Styled bulleted lists', 'total' ),
		'base' => 'vcex_bullets',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-bullets vcex-icon fa fa-dot-circle-o',
		'params' => array(
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Style', 'total' ),
				'param_name' => 'style',
				'admin_label' => true,
				'value' => array(
					__( 'Check', 'total') => 'check',
					__( 'Blue', 'total' ) => 'blue',
					__( 'Gray', 'total' ) => 'gray',
					__( 'Purple', 'total' ) => 'purple',
					__( 'Red', 'total' ) => 'red',
				),
			),
			array(
				'type' => 'textarea_html',
				'heading' => esc_html__( 'Insert Unordered List', 'total' ),
				'param_name' => 'content',
				'value' => '<ul><li>List 1</li><li>List 2</li><li>List 3</li><li>List 4</li></ul>',
			),
		)
	);
}
vc_lean_map( 'vcex_bullets', 'vcex_bullets_shortcode_vc_map' );