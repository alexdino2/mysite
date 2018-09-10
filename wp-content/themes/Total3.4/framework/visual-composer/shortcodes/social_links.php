<?php
/**
 * Visual Composer Social Links
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.5
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_social_links extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_social_links.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 2.0.0
 */
function vcex_social_links_vc_map() {
	// Define map array
	$array = array(
		'name' => esc_html__( 'Social Links', 'total' ),
		'description' => esc_html__( 'Display social links using icon fonts', 'total' ),
		'base' => 'vcex_social_links',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-social-links vcex-icon fa fa-user-plus',
	);
	// Create params for array
	$params = array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Unique Id', 'total' ),
			'param_name' => 'unique_id',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Classes', 'total' ),
			'param_name' => 'classes',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'total'),
			'param_name' => 'style',
			'value' => array_flip( wpex_social_button_styles() ),
			'admin_label' => true,
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
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Hover Animation', 'total'),
			'param_name' => 'hover_animation',
			'value' => array_flip( wpex_hover_css_animations() ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Link Target', 'total'),
			'param_name' => 'link_target',
			'value' => array(
				__( 'Self', 'total' ) => '',
				__( 'Blank', 'total' ) => 'blank',
			),
		),
	);
	// Get array of social links to loop through
	$social_links = vcex_social_links_profiles();
	// Loop through social links and add to params
	foreach ( $social_links as $key => $val ) {

		$desc = ( 'email' == $key ) ? esc_html__( 'Format: mailto:email@site.com', 'total' ) : '';

		$params[] = array(
			'type' => 'textfield',
			'heading' => $val['label'],
			'param_name' => $key,
			'group' => esc_html__( 'Profiles', 'total' ),
			'description' => $desc,
		);

	}
	// Add CSS option
	$params[] = array(
		'type' => 'css_editor',
		'heading' => esc_html__( 'CSS', 'total' ),
		'param_name' => 'css',
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Align', 'total' ),
		'param_name' => 'align',
		'value' => array_flip( wpex_alignments() ),
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Font Size', 'total' ),
		'param_name' => 'size',
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Width', 'total' ),
		'param_name' => 'width',
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Height', 'total' ),
		'param_name' => 'height',
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Border Radius', 'total' ),
		'param_name' => 'border_radius',
		'group' => esc_html__( 'Design', 'total' ),
	);
	$params[] = array(
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'total' ),
		'param_name' => 'color',
		'group' => esc_html__( 'Design', 'total' ),
		'dependency' => array( 'element' => 'style', 'value' => array( 'none', '' ) ),
	);
	$params[] = array(
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Hover Background', 'total' ),
		'param_name' => 'hover_bg',
		'group' => esc_html__( 'Design', 'total' ),
	);

	$params[] = array(
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Hover Color', 'total' ),
		'param_name' => 'hover_color',
		'group' => esc_html__( 'Design', 'total' ),
	);
	// Add params to array
	$array['params'] = $params;
	// Return $array
	return $array;
}
vc_lean_map( 'vcex_social_links', 'vcex_social_links_vc_map' );