<?php
/**
 * Visual Composer Login Form
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.4.0
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_login_form extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_login_form.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 1.4.1
 */
function vcex_login_form_vc_map() {
	return array(
		'name' => esc_html__( 'Login Form', 'total' ),
		'description' => esc_html__( 'Adds a WordPress login form', 'total' ),
		'base' => 'vcex_login_form',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-login-form vcex-icon fa fa-unlock-alt',
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Unique Id', 'total' ),
				'param_name' => 'unique_id',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Custom Classes', 'total' ),
				'param_name' => 'classes',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'CSS Animation', 'total' ),
				'param_name' => 'css_animation',
				'value' => array_flip( wpex_css_animations() ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Remember Me', 'total' ),
				'param_name' => 'remember',
				'value' => array(
					esc_html__( 'Yes', 'total' ) => 'true',
					esc_html__( 'No', 'total' ) => 'false',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Lost Password', 'total' ),
				'param_name' => 'lost_password',
				'value' => array(
					esc_html__( 'Yes', 'total' ) => 'true',
					esc_html__( 'No', 'total' ) => 'false',
				),
			),

			// Labels
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Username Label', 'total' ),
				'param_name' => 'label_username',
				'group' =>  esc_html__( 'Labels', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Password Label', 'total' ),
				'param_name' => 'label_password',
				'group' =>  esc_html__( 'Labels', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Remember Me Label', 'total' ),
				'param_name' => 'label_remember',
				'group' =>  esc_html__( 'Labels', 'total' ),
				'dependency' => array( 'element' => 'remember', 'value' => 'true' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Lost Password Label', 'total' ),
				'param_name' => 'lost_password_label',
				'dependency' => array( 'element' => 'lost_password', 'value' => 'true' ),
				'group' =>  esc_html__( 'Labels', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Button Label', 'total' ),
				'param_name' => 'label_log_in',
				'group' =>  esc_html__( 'Labels', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Redirect', 'total' ),
				'param_name' => 'redirect',
				'description' => esc_html__( 'Enter a URL to redirect the user after they successfully log in. Leave blank to redirect to the current page.','total'),
			),

			// Logged In Content
			array(
				'type' => 'textarea_html',
				'heading' => esc_html__( 'Logged in Content', 'total' ),
				'param_name' => 'content',
				'value' => esc_html__('You are currently logged in','total'),
				'description' => esc_html__( 'The content to displayed for logged in users.','total'),
			),

			// CSS
			array(
				'type' => 'css_editor',
				'heading' => esc_html__( 'CSS', 'total' ),
				'param_name' => 'css',
				'group' => esc_html__( 'CSS', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Font Size', 'total' ),
				'param_name' => 'text_font_size',
				'group' => esc_html__( 'CSS', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'text_color',
				'group' => esc_html__( 'CSS', 'total' ),
			),
		)
	);
}
vc_lean_map( 'vcex_login_form', 'vcex_login_form_vc_map' );