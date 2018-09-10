<?php
/**
 * Visual Composer Icon
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.5
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_icon extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_icon.php' ) );
		return ob_get_clean();
	}
}

/**
 * Parse shortcode attributes and set correct values
 *
 * @since 2.0.0
 */
function vcex_parse_icon_atts( $atts ) {

	// Convert textfield link to vc_link
	if ( ! empty( $atts['link_url'] ) && false === strpos( $atts['link_url'], 'url:' ) ) {
		$url = 'url:'. $atts['link_url'] .'|';
		$link_title = isset( $atts['link_title'] ) ? 'title:' . $atts['link_title'] .'|' : '|';
		$link_target = ( isset( $atts['link_target'] ) && 'blank' == $atts['link_target'] ) ? 'target:_blank' : '';
		$atts['link_url'] = $url . $link_title . $link_target;
	}

	// Update link target
	if ( isset( $atts['link_target'] ) && 'local' == $atts['link_target'] ) {
		$atts['link_local_scroll'] = 'true';
	}

	// Return $atts
	return $atts;
}
add_filter( 'vc_edit_form_fields_attributes_vcex_icon', 'vcex_parse_icon_atts' );

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since Total 1.4.1
 */
function vcex_icon_vc_map() {

	// Reusable strings
	$s_icon = esc_html__( 'Icon', 'total' );

	// Return array
	return array(
		'name' => esc_html__( 'Font Icon', 'total' ),
		'description' => esc_html__( 'Font Icon from various libraries', 'total' ),
		'base' => 'vcex_icon',
		'icon' => 'vcex-font-icon vcex-icon fa fa-bolt',
		'category' => wpex_get_theme_branding(),
		'params' => array(
			// General
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Unique Id', 'total' ),
				'param_name' => 'unique_id',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Classes', 'total' ),
				'param_name' => 'el_class',
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
				'heading' => esc_html__( 'Icon library', 'total' ),
				'param_name' => 'icon_type',
				'description' => esc_html__( 'Select icon library.', 'total' ),
				'value' => array(
					__( 'Font Awesome', 'total' ) => 'fontawesome',
					__( 'Open Iconic', 'total' ) => 'openiconic',
					__( 'Typicons', 'total' ) => 'typicons',
					__( 'Entypo', 'total' ) => 'entypo',
					__( 'Linecons', 'total' ) => 'linecons',
					__( 'Pixel', 'total' ) => 'pixelicons',
					__( 'Mono Social', 'total' ) => 'monosocial',
				),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon',
				'admin_label' => true,
				'value' => 'fa fa-info-circle',
				'settings' => array(
					'emptyIcon' => true,
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_openiconic',
				'std' => '',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'openiconic',
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_typicons',
				'std' => '',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'typicons',
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_entypo',
				'std' => '',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'entypo',
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_linecons',
				'std' => '',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'linecons',
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_pixelicons',
				'std' => '',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'pixelicons',
					'source' => vcex_pixel_icons(),
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'pixelicons' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => $s_icon,
				'param_name' => 'icon_monosocial',
				'settings' => array(
					'emptyIcon' => true,
					'type' => 'monosocial',
					'iconsPerPage' => 4000,
				),
				'dependency' => array( 'element' => 'icon_type', 'value' => 'monosocial' ),
			),
			// Design
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color', 'total' ),
				'param_name' => 'color',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Color: Hover', 'total' ),
				'param_name' => 'color_hover',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background', 'total' ),
				'param_name' => 'background',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background: Hover', 'total' ),
				'param_name' => 'background_hover',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Size', 'total' ),
				'param_name' => 'size',
				'std' => 'normal',
				'value' => array(
					__( 'Inherit', 'total' ) => 'inherit',
					__( 'Extra Large', 'total' ) => 'xlarge',
					__( 'Large', 'total' ) => 'large',
					__( 'Normal', 'total' ) => 'normal',
					__( 'Small', 'total') => 'small',
					__( 'Tiny', 'total' ) => 'tiny',
				),
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Position', 'total' ),
				'param_name' => 'float',
				'value' => array_flip( wpex_alignments() ),
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Custom Size', 'total' ),
				'param_name' => 'custom_size',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'border_radius',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border', 'total' ),
				'param_name' => 'border',
				'description' => esc_html__( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total' ),
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Width', 'total' ),
				'param_name' => 'width',
				'group' => esc_html__( 'Design', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Height', 'total' ),
				'param_name' => 'height',
				'group' => esc_html__( 'Design', 'total' ),
			),
			// Link
			array(
				'type' => 'vc_link',
				'heading' => esc_html__( 'Link', 'total' ),
				'param_name' => 'link_url',
				'group' => esc_html__( 'Link', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Link: Local Scroll', 'total' ),
				'param_name' => 'link_local_scroll',
				'value' => array(
					__( 'False', 'total' ) => 'false',
					__( 'True', 'total' ) => 'true',
				),
				'group' => esc_html__( 'Link', 'total' ),
			),
		)
	);
}
vc_lean_map( 'vcex_icon', 'vcex_icon_vc_map' );