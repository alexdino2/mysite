<?php
/**
 * Visual Composer Callout
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
class WPBakeryShortCode_vcex_callout extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_callout.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since Total 1.4.1
 */
function vcex_callout_vc_map() {
	return array(
		'name' => esc_html__( 'Callout', 'total' ),
		'description' => esc_html__( 'Call to action section with or without button', 'total' ),
		'base' => 'vcex_callout',
		'icon' => 'vcex-callout vcex-icon fa fa-bullhorn',
		'deprecated' => '3.0.0',
		'category' => wpex_get_theme_branding(),
		'params' => array(
			array(
				'type' => 'textfield',
				'admin_label' => true,
				'heading' => esc_html__( 'Unique Id', 'total' ),
				'description' => esc_html__( 'Give your main element a unique ID.', 'total' ),
				'param_name' => 'unique_id',
			),
			array(
				'type' => 'textfield',
				'admin_label' => true,
				'heading' => esc_html__( 'Classes', 'total' ),
				'description' => esc_html__( 'Add additonal classes to the main element.', 'total' ),
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
				'heading' => esc_html__( 'Appear Animation', 'total' ),
				'param_name' => 'css_animation',
				'value' => array_flip( wpex_css_animations() ),
			),
			// Content
			array(
				'type' => 'textarea_html',
				'holder' => 'div',
				'class' => 'vcex-callout',
				'heading' => esc_html__( 'Callout Content', 'total' ),
				'param_name' => 'content',
				'value' => esc_html__( 'Enter your content here.', 'total' ),
				'group' => esc_html__( 'Content', 'total' ),
			),
			// Button
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'URL', 'total' ),
				'param_name' => 'button_url',
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Text', 'total' ),
				'param_name' => 'button_text',
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Button Style', 'total' ),
				'param_name' => 'button_style',
				'value' => array_flip( wpex_button_styles() ),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'button_color',
				'std' => '',
				'value' => array_flip( wpex_button_colors() ),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'button_border_radius',
				'description' => esc_html__( 'Please enter a px value.', 'total' ),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Link Target', 'total' ),
				'param_name' => 'button_target',
				'value' => array(
					__( 'Self', 'total' ) => '',
					__( 'Blank', 'total' ) => 'blank',
				),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Rel', 'total' ),
				'param_name' => 'button_rel',
				'value' => array(
					__( 'None', 'total' ) => 'none',
					__( 'Nofollow', 'total' ) => 'nofollow',
				),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Icon Left', 'total' ),
				'param_name' => 'button_icon_left',
				'value' => wpex_get_awesome_icons(),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Icon Right', 'total' ),
				'param_name' => 'button_icon_right',
				'value' => wpex_get_awesome_icons(),
				'group' => esc_html__( 'Button', 'total' ),
			),
			array(
				'type' => 'css_editor',
				'heading' => esc_html__( 'CSS', 'total' ),
				'param_name' => 'css',
				'group' => esc_html__( 'Design options', 'total' ),
			),
		)
	);
}
vc_lean_map( 'vcex_callout', 'vcex_callout_vc_map' );