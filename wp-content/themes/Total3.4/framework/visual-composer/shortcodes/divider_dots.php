<?php
/**
 * Visual Composer Divider: Dots
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.5
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 3.3.5
 */
class WPBakeryShortCode_vcex_divider_dots extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_divider_dots.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 3.3.5
 */
function vcex_divider_dots_shortcode_vc_map() {
	return array(
		'name' => esc_html__( 'Divider Dots', 'total' ),
		'description' => esc_html__( 'Dot Separator', 'total' ),
		'base' => 'vcex_divider_dots',
		'icon' => 'vcex-dots vcex-icon fa fa-ellipsis-h',
		'category' => wpex_get_theme_branding(),
		'params' => array(
			// General
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Classes', 'total' ),
				'param_name' => 'el_class',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Appear Animation', 'total' ),
				'param_name' => 'css_animation',
				'value' => array(
					__( 'No', 'total' ) => '',
					__( 'Top to bottom', 'total' ) => 'top-to-bottom',
					__( 'Bottom to top', 'total' ) => 'bottom-to-top',
					__( 'Left to right', 'total' ) => 'left-to-right',
					__( 'Right to left', 'total' ) => 'right-to-left',
					__( 'Appear from center', 'total' ) => 'appear'
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'value' => array_flip( wpex_visibility() ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Align', 'total' ),
				'param_name' => 'align',
				'value' => array(
					__( 'Default', 'total' ) => '',
					__( 'Center', 'total' ) => 'center',
					__( 'Right', 'total' ) => 'right',
					__( 'Left', 'total' ) => 'left',
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Count', 'total' ),
				'param_name' => 'count',
				'value' => '3',
				'admin_label' => true,
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Size', 'total' ),
				'param_name' => 'size',
				'description' => esc_html__( 'Default', 'total' ) . ': 5px',
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'color',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Margin Top', 'total' ),
				'param_name' => 'margin_top',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Margin Bottom', 'total' ),
				'param_name' => 'margin_bottom',
			),
		),
	);
}
vc_lean_map( 'vcex_divider_dots', 'vcex_divider_dots_shortcode_vc_map' );