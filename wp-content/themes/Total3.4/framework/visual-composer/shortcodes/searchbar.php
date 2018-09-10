<?php
/**
 * Visual Composer Searchbar
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.5
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.1.0
 */
class WPBakeryShortCode_vcex_searchbar extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_searchbar.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 2.1.0
 */
function vcex_searchbar_vc_map() {
	return array(
		'name' => esc_html__( 'Search Bar', 'total' ),
		'description' => esc_html__( 'Custom search form', 'total' ),
		'base' => 'vcex_searchbar',
		'icon' => 'vcex-searchbar vcex-icon fa fa-search',
		'category' => wpex_get_theme_branding(),
		'params' => array(
			// General
			array(
				'type' => 'textfield',
				'admin_label' => true,
				'heading' => esc_html__( 'Unique Id', 'total' ),
				'param_name' => 'unique_id',
			),
			array(
				'type' => 'textfield',
				'admin_label' => true,
				'heading' => esc_html__( 'Classes', 'total' ),
				'param_name' => 'classes',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'value' => array_flip( wpex_visibility() ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Appear Animation', 'total'),
				'param_name' => 'css_animation',
				'value' => array_flip( wpex_css_animations() ),
			),
			// Query
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Advanced Search', 'total' ),
				'param_name' => 'advanced_query',
				'group' => esc_html__( 'Query', 'total' ),
				'description' => esc_html__( 'Example: ', 'total' ) . 'post_type=portfolio&taxonomy=portfolio_category&term=advertising',
			),
			// Widths
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Input Width', 'total' ),
				'param_name' => 'input_width',
				'group' => esc_html__( 'Widths', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Button Width', 'total' ),
				'param_name' => 'button_width',
				'group' => esc_html__( 'Widths', 'total' ),
			),

			// Input
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Placeholder', 'total' ),
				'param_name' => 'placeholder',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'input_color',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Font Size', 'total' ),
				'param_name' => 'input_font_size',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Letter Spacing', 'total' ),
				'param_name' => 'input_letter_spacing',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Text Transform', 'total' ),
				'param_name' => 'input_text_transform',
				'group' => esc_html__( 'Input', 'total' ),
				'value' => array_flip( wpex_text_transforms() ),
				'std' => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Font Weight', 'total' ),
				'param_name' => 'input_font_weight',
				'value' => array_flip( wpex_font_weights() ),
				'std'  => '',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'css_editor',
				'heading' => esc_html__( 'Design', 'total' ),
				'param_name' => 'css',
				'group' => esc_html__( 'Input', 'total' ),
			),
			// Submit
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Button Text', 'total' ),
				'param_name' => 'button_text',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Text Transform', 'total' ),
				'param_name' => 'button_text_transform',
				'group' => esc_html__( 'Submit', 'total' ),
				'value' => array_flip( wpex_text_transforms() ),
				'std'  => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Font Weight', 'total' ),
				'param_name' => 'button_font_weight',
				'value' => array_flip( wpex_font_weights() ),
				'group' => esc_html__( 'Submit', 'total' ),
				'std'  => '',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Font Size', 'total' ),
				'param_name' => 'button_font_size',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Letter Spacing', 'total' ),
				'param_name' => 'button_letter_spacing',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'button_border_radius',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background', 'total' ),
				'param_name' => 'button_bg',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background: Hover', 'total' ),
				'param_name' => 'button_bg_hover',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'button_color',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color: Hover', 'total' ),
				'param_name' => 'button_color_hover',
				'group' => esc_html__( 'Submit', 'total' ),
			),
		)
	);
}
vc_lean_map( 'vcex_searchbar', 'vcex_searchbar_vc_map' );