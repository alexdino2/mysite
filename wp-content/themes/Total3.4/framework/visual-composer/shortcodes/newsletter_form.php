<?php
/**
 * Visual Composer Newsletter Form
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 3.3.0
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_newsletter_form extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_newsletter_form.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since Total 1.4.1
 */
function vcex_newsletter_form_vc_map() {
	return array(
		'name' => esc_html__( 'Mailchimp Form', 'total' ),
		'description' => esc_html__( 'Newsletter subscription form', 'total' ),
		'base' => 'vcex_newsletter_form',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-newsletter vcex-icon fa fa-envelope',
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
				'heading' => esc_html__( 'CSS Animation', 'total' ),
				'param_name' => 'css_animation',
				'value' => array_flip( wpex_css_animations() ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Mailchimp Form Action', 'total' ),
				'param_name' => 'mailchimp_form_action',
				'admin_label' => true,
				'value' => '//domain.us1.list-manage.com/subscribe/post?u=numbers_go_here',
				'description' => esc_html__( 'Enter the MailChimp form action URL.', 'total' ) .' <a href="http://docs.shopify.com/support/configuration/store-customization/where-do-i-get-my-mailchimp-form-action?ref=wpexplorer" target="_blank">'. esc_html__( 'Learn More', 'total' ) .' &rarr;</a>',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Placeholder Text', 'total' ),
				'param_name' => 'placeholder_text',
				'std' => esc_html__( 'Enter your email address', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Submit Button Text', 'total' ),
				'param_name' => 'submit_text',
				'std' => esc_html__( 'Go', 'total' ),
			),
			// Input
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background', 'total' ),
				'param_name' => 'input_bg',
				'dependency' => array(
					'element' => 'mailchimp_form_action',
					'not_empty' => true
				),
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
				'heading' => esc_html__( 'Width', 'total' ),
				'param_name' => 'input_width',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Height', 'total' ),
				'param_name' => 'input_height',
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Padding', 'total' ),
				'param_name' => 'input_padding',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border', 'total' ),
				'param_name' => 'input_border',
				'description' => esc_html__( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total' ),
				'group' => esc_html__( 'Input', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'input_border_radius',
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
				'heading' => esc_html__( 'Font Weight', 'total' ),
				'param_name' => 'input_weight',
				'group' => esc_html__( 'Input', 'total' ),
				'std' => '',
				'value' => array_flip( wpex_font_weights() ),
			),
			// Submit
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background', 'total' ),
				'param_name' => 'submit_bg',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background: Hover', 'total' ),
				'param_name' => 'submit_hover_bg',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'submit_color',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color: Hover', 'total' ),
				'param_name' => 'submit_hover_color',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Margin Right', 'total' ),
				'param_name' => 'submit_position_right',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Height', 'total' ),
				'param_name' => 'submit_height',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Padding', 'total' ),
				'param_name' => 'submit_padding',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border', 'total' ),
				'param_name' => 'submit_border',
				'description' => esc_html__( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total' ),
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'submit_border_radius',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Font Size', 'total' ),
				'param_name' => 'submit_font_size',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Letter Spacing', 'total' ),
				'param_name' => 'submit_letter_spacing',
				'group' => esc_html__( 'Submit', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Font Weight', 'total' ),
				'param_name' => 'submit_weight',
				'group' => esc_html__( 'Submit', 'total' ),
				'std' => '',
				'value' => array_flip( wpex_font_weights() ),
			),
		)
	);
}
vc_lean_map( 'vcex_newsletter_form', 'vcex_newsletter_form_vc_map' );