<?php
/**
 * Visual Composer Milestone
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.5.0
 */

if ( ! class_exists( 'VCEX_Milestone_Shortcode' ) ) {

	class VCEX_Milestone_Shortcode {

		/**
		 * Main constructor
		 *
		 * @since 3.5.0
		 */
		public function __construct() {
			
			// Add shortcode
			add_shortcode( 'vcex_milestone', array( 'VCEX_Milestone_Shortcode', 'output' ) );

			// Map to VC
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'vcex_milestone', array( 'VCEX_Milestone_Shortcode', 'map' ) );
			}

		}

		/**
		 * Shortcode output => Get template file and display shortcode
		 *
		 * @since 3.5.0
		 */
		public static function output( $atts, $content = null ) {
			ob_start();
			include( locate_template( 'vcex_templates/vcex_milestone.php' ) );
			return ob_get_clean();
		}

		/**
		 * Map shortcode to VC
		 *
		 * @since 3.5.0
		 */
		public static function map() {
			$s_number  = esc_html__( 'Number', 'total' );
			$s_caption = esc_html__( 'Caption', 'total' );
			$s_design  = esc_html__( 'Design options', 'total' );
			return array(
				'name' => esc_html__( 'Milestone', 'total' ),
				'description' => esc_html__( 'Animated counter', 'total' ),
				'base' => 'vcex_milestone',
				'icon' => 'vcex-milestone vcex-icon fa fa-medium',
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
						'heading' => esc_html__( 'Extra class name', 'total' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'total' ),
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
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Hover Animation', 'total'),
						'param_name' => 'hover_animation',
						'value' => array_flip( wpex_hover_css_animations() ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Animated', 'total' ),
						'param_name' => 'animated',
						'std' => 'true',
						'value' => array(
							esc_html__( 'Yes', 'total') => 'true',
							esc_html__( 'No', 'total' ) => 'false',
						),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Speed', 'total' ),
						'param_name' => 'speed',
						'value' => '2500',
						'description' => esc_html__('The number of milliseconds it should take to finish counting.','total'),
					),
					// Number
					array(
						'type' => 'textfield',
						'admin_label' => true,
						'heading' => $s_number,
						'param_name' => 'number',
						'std' => '45',
						'group' => $s_number,
						//'dependency' => array( 'element' => 'number_type', 'value' => 'static' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Decimal Places', 'total' ),
						'param_name' => 'decimals',
						'value' => '0',
						'group' => $s_number,
						//'dependency' => array( 'element' => 'number_type', 'value' => 'static' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Before', 'total' ),
						'param_name' => 'before',
						'group' => $s_number,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'After', 'total' ),
						'param_name' => 'after',
						'default' => '%',
						'group' => $s_number,
					),
					array(
						'type'  => 'vcex_font_family_select',
						'heading' => esc_html__( 'Font Family', 'total' ),
						'param_name' => 'number_font_family',
						'group' => $s_number,
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Color', 'total' ),
						'param_name' => 'number_color',
						'group' => $s_number,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Font Size', 'total' ),
						'param_name' => 'number_size',
						'group' => $s_number,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Font Weight', 'total' ),
						'param_name' => 'number_weight',
						'value' => array_flip( wpex_font_weights() ),
						'std' => '',
						'group' => $s_number,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Bottom Margin', 'total' ),
						'param_name' => 'number_bottom_margin',
						'group' => $s_number,
					),
					// caption
					array(
						'type' => 'textfield',
						'class' => 'vcex-animated-counter-caption',
						'heading' => $s_caption,
						'param_name' => 'caption',
						'value' => 'Awards Won',
						'admin_label' => true,
						'group' => $s_caption,
					),
					array(
						'type'  => 'vcex_font_family_select',
						'heading' => esc_html__( 'Font Family', 'total' ),
						'param_name' => 'caption_font_family',
						'group' => $s_caption,
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__(  'Color', 'total' ),
						'param_name' => 'caption_color',
						'group' => $s_caption,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Font Size', 'total' ),
						'param_name' => 'caption_size',
						'group' => $s_caption,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Font Weight', 'total' ),
						'param_name' => 'caption_font',
						'value' => array_flip( wpex_font_weights() ),
						'std' => '',
						'group' => $s_caption,
					),
					// Link
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'URL', 'total' ),
						'param_name' => 'url',
						'group' => esc_html__( 'Link', 'total' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'URL Target', 'total' ),
						'param_name' => 'url_target',
						'value' => array(
							esc_html__( 'Self', 'total') => 'self',
							esc_html__( 'Blank', 'total' ) => 'blank',
						),
						'group' => esc_html__( 'Link', 'total' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'URl Rel', 'total' ),
						'param_name' => 'url_rel',
						'value' => array(
							esc_html__( 'None', 'total') => '',
							esc_html__( 'Nofollow', 'total' ) => 'nofollow',
						),

						'group' => esc_html__( 'Link', 'total' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Link Container Wrap', 'total' ),
						'param_name' => 'url_wrap',
						'value' => array(
							esc_html__( 'Default', 'total' ) => '',
							esc_html__( 'No', 'total' ) => 'false',
							esc_html__( 'Yes', 'total' ) => 'true',
						),
						'group' => esc_html__( 'Link', 'total' ),
						'description' => esc_html__( 'Apply the link to the entire wrapper?', 'total' ),
					),
					// CSS
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'Design', 'total' ),
						'param_name' => 'css',
						'group' => $s_design,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Width', 'total' ),
						'param_name' => 'width',
						'group' => $s_design,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Border Radius', 'total' ),
						'param_name' => 'border_radius',
						'group' => $s_design,
					),
				)
			);
		}

	}
}
new VCEX_Milestone_Shortcode;