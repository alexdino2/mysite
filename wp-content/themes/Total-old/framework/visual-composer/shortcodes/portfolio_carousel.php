<?php
/**
 * Visual Composer Portfolio Carousel
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.6.0
 */

if ( ! class_exists( 'VCEX_Portfolio_Carousel_Shortcode' ) ) {

	class VCEX_Portfolio_Carousel_Shortcode {

		/**
		 * Main constructor
		 *
		 * @since 3.5.0
		 */
		public function __construct() {
			
			// Add shortcode
			add_shortcode( 'vcex_portfolio_carousel', array( 'VCEX_Portfolio_Carousel_Shortcode', 'output' ) );

			// Map to VC
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'vcex_portfolio_carousel', array( 'VCEX_Portfolio_Carousel_Shortcode', 'map' ) );
			}

			// Admin filters
			if ( is_admin() ) {

				// Set image height to full if crop/width are empty
				add_filter( 'vc_edit_form_fields_attributes_vcex_portfolio_carousel', 'vcex_parse_image_size' );

				// Get autocomplete suggestion
				add_filter( 'vc_autocomplete_vcex_portfolio_carousel_include_categories_callback', 'vcex_suggest_portfolio_categories', 10, 1 );
				add_filter( 'vc_autocomplete_vcex_portfolio_carousel_exclude_categories_callback', 'vcex_suggest_portfolio_categories', 10, 1 );

				// Render autocomplete suggestions
				add_filter( 'vc_autocomplete_vcex_portfolio_carousel_include_categories_render', 'vcex_render_portfolio_categories', 10, 1 );
				add_filter( 'vc_autocomplete_vcex_portfolio_carousel_exclude_categories_render', 'vcex_render_portfolio_categories', 10, 1 );

				// Move content design elements into new entry CSS field
				add_filter( 'vc_edit_form_fields_attributes_vcex_portfolio_carousel', 'vcex_parse_deprecated_grid_entry_content_css' );

			}

		}

		/**
		 * Shortcode output => Get template file and display shortcode
		 *
		 * @since 3.5.0
		 */
		public static function output( $atts, $content = null ) {
			ob_start();
			include( locate_template( 'vcex_templates/vcex_portfolio_carousel.php' ) );
			return ob_get_clean();
		}

		/**
		 * Map shortcode to VC
		 *
		 * @since 3.5.0
		 */
		public static function map() {

			// Save reusable strings
			$s_enable      = esc_html__( 'Enable', 'total' );
			$s_yes         = esc_html__( 'Yes', 'total' );
			$s_no          = esc_html__( 'No', 'total' );
			$s_title       = esc_html__( 'Title', 'total' );
			$s_query       = esc_html__( 'Query', 'total' );
			$s_image       = esc_html__( 'Image', 'total' );
			$s_excerpt     = esc_html__( 'Excerpt', 'total' );
			$s_content_css = esc_html__( 'Content CSS', 'total' );

			// Return array
			return array(
				'name' => esc_html__( 'Portfolio Carousel', 'total' ),
				'description' => esc_html__( 'Recent portfolio posts carousel', 'total' ),
				'base' => 'vcex_portfolio_carousel',
				'category' => wpex_get_theme_branding(),
				'icon' => 'vcex-portfolio-carousel vcex-icon fa fa-folder-open',
				'params' => array(
					// General
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Unique Id', 'total' ),
						'param_name' => 'unique_id',
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
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
						'heading' => esc_html__( 'Appear Animation', 'total'),
						'param_name' => 'css_animation',
						'value' => array_flip( wpex_css_animations() ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Arrows?', 'total' ),
						'param_name' => 'arrows',
						'value' => array( $s_yes => 'true', $s_no => 'false' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Arrows Style', 'total' ),
						'param_name' => 'arrows_style',
						'value' => array_flip( wpex_carousel_arrow_styles() ),
						'dependency' => array( 'element' => 'arrows', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Arrows Position', 'total' ),
						'param_name' => 'arrows_position',
						'value' => array_flip( wpex_carousel_arrow_positions() ),
						'dependency' => array( 'element' => 'arrows', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Dots?', 'total' ),
						'param_name' => 'dots',
						'value' => array( $s_no => 'false', $s_yes => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Items To Display', 'total' ),
						'param_name' => 'items',
						'value' => '4',
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Tablet: Items To Display', 'total' ),
						'param_name' => 'tablet_items',
						'value' => '3',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Mobile Landscape: Items To Display', 'total' ),
						'param_name' => 'mobile_landscape_items',
						'value' => '2',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Mobile Portrait: Items To Display', 'total' ),
						'param_name' => 'mobile_portrait_items',
						'value' => '1',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Items To Scrollby', 'total' ),
						'param_name' => 'items_scroll',
						'value' => '1',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Margin Between Items', 'total' ),
						'param_name' => 'items_margin',
						'value' => '15',
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Auto Play', 'total' ),
						'param_name' => 'auto_play',
						'value' => array(
							$s_yes => 'true',
							$s_no => 'false',
						),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Timeout Duration in milliseconds', 'total' ),
						'param_name' => 'timeout_duration',
						'value' => '5000',
						'dependency' => array( 'element' => 'auto_play', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Infinite Loop', 'total' ),
						'param_name' => 'infinite_loop',
						'value' => array(
							$s_yes => 'true',
							$s_no => 'false',
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Center Item', 'total' ),
						'param_name' => 'center',
						'value' => array(
							$s_no => 'false',
							$s_yes => 'true',
						),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Animation Speed', 'total' ),
						'param_name' => 'animation_speed',
						'value' => '150',
						'description' => esc_html__( 'Default is 150 milliseconds. Enter 0.0 to disable.', 'total' ),
					),
					// Query
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Post Count', 'total' ),
						'param_name' => 'count',
						'value' => '8',
						'group' => $s_query,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Offset', 'total' ),
						'param_name' => 'offset',
						'group' => $s_query,
						'description' => esc_html__( 'Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. The offset parameter is ignored when posts per page is set to -1.', 'total' ),
					),
					array(
						'type' => 'autocomplete',
						'heading' => esc_html__( 'Include Categories', 'total' ),
						'param_name' => 'include_categories',
						'param_holder_class' => 'vc_not-for-custom',
						'admin_label' => true,
						'settings' => array(
							'multiple' => true,
							'min_length' => 1,
							'groups' => true,
							'unique_values' => true,
							'display_inline' => true,
							'delay' => 0,
							'auto_focus' => true,
						),
						'group' => $s_query,
					),
					array(
						'type' => 'autocomplete',
						'heading' => esc_html__( 'Exclude Categories', 'total' ),
						'param_name' => 'exclude_categories',
						'param_holder_class' => 'vc_not-for-custom',
						'admin_label' => true,
						'settings' => array(
							'multiple' => true,
							'min_length' => 1,
							'groups' => true,
							'unique_values' => true,
							'display_inline' => true,
							'delay' => 0,
							'auto_focus' => true,
						),
						'group' => $s_query,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Order', 'total' ),
						'param_name' => 'order',
						'group' => $s_query,
						'value' => array(
							esc_html__( 'Default', 'total' ) => '',
							esc_html__( 'DESC', 'total' ) => 'DESC',
							esc_html__( 'ASC', 'total' ) => 'ASC',
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Order By', 'total' ),
						'param_name' => 'orderby',
						'value' => vcex_orderby_array(),
						'group' => $s_query,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Orderby: Meta Key', 'total' ),
						'param_name' => 'orderby_meta_key',
						'group' => $s_query,
						'dependency' => array(
							'element' => 'orderby',
							'value' => array( 'meta_value_num', 'meta_value' ),
						),
					),
					// Image
					array(
						'type' => 'dropdown',
						'heading' => $s_enable,
						'param_name' => 'media',
						'value' => array( $s_yes => 'true', $s_no => 'false' ),
						'group' => $s_image,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Image Links To', 'total' ),
						'param_name' => 'thumbnail_link',
						'value' => array(
							esc_html__( 'Default', 'total') => '',
							esc_html__( 'Post', 'total') => 'post',
							esc_html__( 'Lightbox', 'total' ) => 'lightbox',
							esc_html__( 'None', 'total' ) => 'none',
						),
						'group' => $s_image,
						'dependency' => array( 'element' => 'media', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Image Size', 'total' ),
						'param_name' => 'img_size',
						'std' => 'wpex_custom',
						'value' => vcex_image_sizes(),
						'group' => $s_image,
						'dependency' => array( 'element' => 'media', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Image Crop Location', 'total' ),
						'param_name' => 'img_crop',
						'std' => 'center-center',
						'value' => array_flip( wpex_image_crop_locations() ),
						'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
						'group' => $s_image,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Image Crop Width', 'total' ),
						'param_name' => 'img_width',
						'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
						'group' => $s_image,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Image Crop Height', 'total' ),
						'param_name' => 'img_height',
						'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
						'description' => esc_html__( 'Enter a height in pixels. Leave empty to disable vertical cropping and keep image proportions.', 'total' ),
						'group' => $s_image,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Image Overlay', 'total' ),
						'param_name' => 'overlay_style',
						'value' => array_flip( wpex_overlay_styles_array() ),
						'group' => $s_image,
						'dependency' => array( 'element' => 'media', 'value' => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Overlay Button Text', 'total' ),
						'param_name' => 'overlay_button_text',
						'group' => $s_image,
						'dependency' => array( 'element' => 'overlay_style', 'value' => 'hover-button' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Overlay Excerpt Length', 'total' ),
						'param_name' => 'overlay_excerpt_length',
						'value' => '15',
						'group' => $s_image,
						'dependency' => array( 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Image Hover', 'total' ),
						'param_name' => 'img_hover_style',
						'value' => array_flip( wpex_image_hovers() ),
						'group' => $s_image,
						'dependency' => array( 'element' => 'media', 'value' => 'true' ),
					),
					// Title
					array(
						'type' => 'dropdown',
						'heading' => $s_enable,
						'param_name' => 'title',
						'value' => array( $s_yes => 'true', $s_no => 'false' ),
						'group' => $s_title,
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Color', 'total' ),
						'param_name' => 'content_heading_color',
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Font Size', 'total' ),
						'param_name' => 'content_heading_size',
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Margin', 'total' ),
						'param_name' => 'content_heading_margin',
						'description' => esc_html__( 'Please use the following format: top right bottom left.', 'total' ),
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Line Height', 'total' ),
						'param_name' => 'content_heading_line_height',
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Font Weight', 'total' ),
						'param_name' => 'content_heading_weight',
						'std' => '',
						'value' => array_flip( wpex_font_weights() ),
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Text Transform', 'total' ),
						'param_name' => 'content_heading_transform',
						'value' => array_flip( wpex_text_transforms() ),
						'group' => $s_title,
						'dependency' => array( 'element' => 'title', 'value' => 'true' ),
					),
					// Excerpt
					array(
						'type' => 'dropdown',
						'heading' => $s_enable,
						'param_name' => 'excerpt',
						'value' => array( $s_yes => 'true', $s_no => 'false' ),
						'group' => $s_excerpt,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Length', 'total' ),
						'param_name' => 'excerpt_length',
						'value' => '30',
						'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "9999".', 'total' ),
						'group' => $s_excerpt,
						'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Font Size', 'total' ),
						'param_name' => 'content_font_size',
						'group' => $s_excerpt,
						'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => esc_html__( 'Color', 'total' ),
						'param_name' => 'content_color',
						'group' => $s_excerpt,
						'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					),
					// Content CSS
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS', 'total' ),
						'param_name' => 'content_css',
						'group' => $s_content_css,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Style', 'total' ),
						'param_name' => 'style',
						'value' => array(
							__( 'Default', 'total') => 'default',
							__( 'No Margins', 'total' ) => 'no-margins',
						),
						'group' => $s_content_css,
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Alignment', 'total' ),
						'param_name' => 'content_alignment',
						'value' => array_flip( wpex_alignments() ),
						'group' => $s_content_css,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Opacity', 'total' ),
						'param_name' => 'content_opacity',
						'description' => esc_html__( 'Enter a value between "0" and "1".', 'total' ),
						'group' => $s_content_css,
					),
					// Hidden/Deprecated fields
					array( 'type' => 'hidden', 'param_name' => 'content_background' ),
					array( 'type' => 'hidden', 'param_name' => 'content_margin' ),
					array( 'type' => 'hidden', 'param_name' => 'content_padding' ),
					array( 'type' => 'hidden', 'param_name' => 'content_border' ),
				),
			);
		}

	}
}
new VCEX_Portfolio_Carousel_Shortcode;