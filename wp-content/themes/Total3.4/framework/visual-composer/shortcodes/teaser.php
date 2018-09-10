<?php
/**
 * Visual Composer Teaser
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
class WPBakeryShortCode_vcex_teaser extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_teaser.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 1.4.1
 */
function vcex_teaser_vc_map() {
	return array(
		'name' => esc_html__( 'Teaser Box', 'total' ),
		'description' => esc_html__( 'A teaser content box', 'total' ),
		'base' => 'vcex_teaser',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-teaser vcex-icon fa fa-file-text-o',
		'params' => array(
			// General
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
				'std' => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Text Align', 'total' ),
				'param_name' => 'text_align',
				'value' => array(
					__( 'Default', 'total' ) => '',
					__( 'Center', 'total' ) => 'center',
					__( 'Left', 'total' ) => 'left',
					__( 'Right', 'total' ) => 'right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Style', 'total' ),
				'param_name' => 'style',
				'value' => array(
					__( 'Default', 'total' ) => '',
					__( 'Plain', 'total' ) => 'one',
					__( 'Boxed 1 - Legacy', 'total' ) => 'two',
					__( 'Boxed 2 - Legacy', 'total' ) => 'three',
					__( 'Outline - Legacy', 'total' ) => 'four',
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Padding', 'total' ),
				'param_name' => 'padding',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'dependency' => array( 'element' => 'style', 'value' => 'two' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background Color', 'total' ),
				'param_name' => 'background',
				'dependency' => array( 'element' => 'style', 'value' => array( 'two', 'three' ) ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Border Color', 'total' ),
				'param_name' => 'border_color',
				'dependency' => array( 'element' => 'style', 'value' => array( 'two', 'three', 'four' ) ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Border Radius', 'total' ),
				'param_name' => 'border_radius',
				'dependency' => array( 'element' => 'style', 'value' => array( 'two', 'three', 'four' ) ),
			),
			// Heading
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Heading', 'total' ),
				'param_name' => 'heading',
				'value' => 'Sample Heading',
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Heading Color', 'total' ),
				'param_name' => 'heading_color',
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Heading Type', 'total' ),
				'param_name' => 'heading_type',
				'group' => esc_html__( 'Heading', 'total' ),
				'value' => array(
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'div' => 'div',
				),
			),
			array(
				'type'  => 'vcex_font_family_select',
				'heading' => esc_html__( 'Font Family', 'total' ),
				'param_name' => 'heading_font_family',
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Heading Font Weight', 'total' ),
				'param_name' => 'heading_weight',
				'std' => '',
				'value' => array_flip( wpex_font_weights() ),
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Heading Text Transform', 'total' ),
				'param_name' => 'heading_transform',
				'group' => esc_html__( 'Heading', 'total' ),
				'value' => array_flip( wpex_text_transforms() ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Heading Font Size', 'total' ),
				'param_name' => 'heading_size',
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Heading Margin', 'total' ),
				'param_name' => 'heading_margin',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'group' => esc_html__( 'Heading', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Heading Letter Spacing', 'total' ),
				'param_name' => 'heading_letter_spacing',
				'group' => esc_html__( 'Heading', 'total' ),
			),
			// Content
			array(
				'type' => 'textarea_html',
				'holder' => 'div',
				'heading' => esc_html__( 'Content', 'total' ),
				'param_name' => 'content',
				'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed faucibus feugiat convallis. Integer nec eros et risus condimentum tristique vel vitae arcu.',
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Content Margin', 'total' ),
				'param_name' => 'content_margin',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Content Padding', 'total' ),
				'param_name' => 'content_padding',
				'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Content Font Size', 'total' ),
				'param_name' => 'content_font_size',
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Content Font Weight', 'total' ),
				'param_name' => 'content_font_weight',
				'std' => '',
				'value' => array_flip( wpex_font_weights() ),
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Content Font Color', 'total' ),
				'param_name' => 'content_color',
				'group' => esc_html__( 'Content', 'total' ),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Content Background', 'total' ),
				'param_name' => 'content_background',
				'group' => esc_html__( 'Content', 'total' ),
			),
			// Media
			array(
				'type' => 'attach_image',
				'heading' => esc_html__( 'Image', 'total' ),
				'param_name' => 'image',
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Video link', 'total' ),
				'param_name' => 'video',
				'description' => esc_html__( 'Enter in a video URL that is compatible with WordPress\'s built-in oEmbed feature.', 'total' ),
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Style', 'total' ),
				'param_name' => 'img_style',
				'value' => array(
					__( 'Default', 'total' ) => '',
					__( 'Stretch', 'total' ) => 'stretch',
				),
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Align', 'total' ),
				'param_name' => 'img_align',
				'value' => array(
					__( 'Default', 'total' ) => '',
					__( 'Left', 'total' ) => 'left',
					__( 'Center', 'total' ) => 'center',
					__( 'Right', 'total' ) => 'right',
				),
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Image Crop Width', 'total' ),
				'param_name' => 'img_width',
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Image Crop Height', 'total' ),
				'param_name' => 'img_height',
				'description' => esc_html__( 'Enter a height in pixels. Leave empty to disable vertical cropping and keep image proportions.', 'total' ),
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Filter', 'total' ),
				'param_name' => 'img_filter',
				'value' => array_flip( wpex_image_filters() ),
				'group' => esc_html__( 'Media', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'CSS3 Image Hover', 'total' ),
				'param_name' => 'img_hover_style',
				'value' => array_flip( wpex_image_hovers() ),
				'group' => esc_html__( 'Media', 'total' ),
			),
			// Link
			array(
				'type' => 'vc_link',
				'heading' => esc_html__( 'URL', 'total' ),
				'param_name' => 'url',
				'group' => esc_html__( 'Link', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Link: Local Scroll', 'total' ),
				'param_name' => 'url_local_scroll',
				'group' => esc_html__( 'Link', 'total' ),
				'value' => array(
					__( 'False', 'total' ) => '',
					__( 'True', 'total' ) => 'true',
				),
			),
			// CSS
			array(
				'type' => 'css_editor',
				'heading' => esc_html__( 'Design', 'total' ),
				'param_name' => 'css',
				'group' => esc_html__( 'Design', 'total' ),
			),
		)
	);
}
vc_lean_map( 'vcex_teaser', 'vcex_teaser_vc_map' );