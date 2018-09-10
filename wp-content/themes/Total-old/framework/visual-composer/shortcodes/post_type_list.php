<?php
/**
 * Post Type Posts List Module
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.3
 */

/**
 * Register shortcode with VC Composer
 *
 * @since 2.0.0
 */
class WPBakeryShortCode_vcex_post_type_list extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_post_type_list.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 1.4.1
 */
function vcex_post_type_list_vc_map() {
	return array(
		'name' => esc_html__( 'Post Types List', 'total' ),
		'description' => esc_html__( 'Posts list with large featured image', 'total' ),
		'base' => 'vcex_post_type_list',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-post-type-grid vcex-icon fa fa-files-o',
		'params' => array(
			// General
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Unique Id', 'total' ),
				'description' => esc_html__( 'Give your main element a unique ID.', 'total' ),
				'param_name' => 'unique_id',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Custom Classes', 'total' ),
				'description' => esc_html__( 'Add additonal classes to the main element.', 'total' ),
				'param_name' => 'classes',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Visibility', 'total' ),
				'param_name' => 'visibility',
				'value' => array_flip( wpex_visibility() ),
				'description' => esc_html__( 'Choose when this module should display.', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Appear Animation', 'total'),
				'param_name' => 'css_animation',
				'value' => array_flip( wpex_css_animations() ),
				'description' => esc_html__( 'If the "filter" is enabled animations will be disabled to prevent bugs.', 'total' ),
			),
			// Query
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Posts Per Page', 'total' ),
				'param_name' => 'posts_per_page',
				'value' => '12',
				'description' => esc_html__( 'You can enter "-1" to display all posts.', 'total' ),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Offset', 'total' ),
				'param_name' => 'offset',
				'group' => esc_html__( 'Query', 'total' ),
				'description' => esc_html__( 'Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. The offset parameter is ignored when posts per page is set to -1.', 'total' ),
			),
			array(
				'type' => 'posttypes',
				'heading' => esc_html__( 'Post types', 'total' ),
				'param_name' => 'post_types',
				'std' => 'post',
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Limit By Post ID\'s', 'total' ),
				'param_name' => 'posts_in',
				'group' => esc_html__( 'Query', 'total' ),
				'description' => esc_html__( 'Seperate by a comma.', 'total' ),
			),
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Limit By Author', 'total' ),
				'param_name' => 'author_in',
				'settings' => array(
					'multiple' => true,
					'min_length' => 1,
					'groups' => false,
					'unique_values' => true,
					'display_inline' => true,
					'delay' => 0,
					'auto_focus' => true,
				),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Query by Taxonomy', 'total' ),
				'param_name' => 'tax_query',
				'value' => array(
					__( 'No', 'total' ) => 'false',
					__( 'Yes', 'total') => 'true',
				),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Taxonomy Name', 'total' ),
				'param_name' => 'tax_query_taxonomy',
				'dependency' => array(
					'element' => 'tax_query',
					'value' => 'true',
				),
				'settings' => array(
					'multiple' => false,
					'min_length' => 1,
					'groups' => false,
					'display_inline' => true,
					'delay' => 0,
					'auto_focus' => true,
				),
				'group' => esc_html__( 'Query', 'total' ),
				'description' => esc_html__( 'If you do not see your taxonomy in the dropdown you can still enter the taxonomy name manually.', 'total' ),
			),
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Terms', 'total' ),
				'param_name' => 'tax_query_terms',
				'dependency' => array( 'element' => 'tax_query', 'value' => 'true' ),
				'settings' => array(
					'multiple' => true,
					'min_length' => 1,
					'groups' => true,
					'display_inline' => true,
					'delay' => 0,
					'auto_focus' => true,
				),
				'group' => esc_html__( 'Query', 'total' ),
				'description' => esc_html__( 'If you do not see your terms in the dropdown you can still enter the term slugs manually seperated by a space.', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order', 'total' ),
				'param_name' => 'order',
				'group' => esc_html__( 'Query', 'total' ),
				'value' => array(
					__( 'Default', 'total' ) => 'default',
					__( 'DESC', 'total' ) => 'DESC',
					__( 'ASC', 'total' ) => 'ASC',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order By', 'total' ),
				'param_name' => 'orderby',
				'value' => vcex_orderby_array(),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Orderby: Meta Key', 'total' ),
				'param_name' => 'orderby_meta_key',
				'group' => esc_html__( 'Query', 'total' ),
				'dependency' => array( 'element' => 'orderby', 'value' => array( 'meta_value_num', 'meta_value' ) ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Post With Thumbnails Only', 'total' ),
				'param_name' => 'thumbnail_query',
				'value' => array(
					__( 'No', 'total' ) => 'false',
					__( 'Yes', 'total') => 'true',
				),
				'group' => esc_html__( 'Query', 'total' ),
			),
			// First Post
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Featured Post?', 'total' ),
				'param_name' => 'featured_post',
				'std' => 'true',
				'value' => array(
					__( 'Yes', 'total') => 'true',
					__( 'No', 'total' ) => 'false',
				),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Size', 'total' ),
				'param_name' => 'featured_post_img_size',
				'std' => 'wpex_custom',
				'value' => vcex_image_sizes(),
				'group' => esc_html__( 'First Post', 'total' ),
				'dependency' => array( 'element' => 'featured_post', 'value' => 'true' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Crop Location', 'total' ),
				'param_name' => 'featured_post_img_crop',
				'std' => 'center-center',
				'value' => array_flip( wpex_image_crop_locations() ),
				'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
				'group' => esc_html__( 'First Post', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Image Crop Width', 'total' ),
				'param_name' => 'featured_post_img_width',
				'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
				'description' => esc_html__( 'Enter a width in pixels.', 'total' ),
				'group' => esc_html__( 'First Post', 'total' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Image Crop Height', 'total' ),
				'param_name' => 'featured_post_img_height',
				'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
				'description' => esc_html__( 'Enter a height in pixels. Leave empty to disable vertical cropping and keep image proportions.', 'total' ),
				'group' => esc_html__( 'First Post', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Image Hover', 'total' ),
				'param_name' => 'featured_post_img_hover_style',
				'value' => array_flip( wpex_image_hovers() ),
				'group' => esc_html__( 'Media', 'total' ),
				'dependency' => array( 'First Post' => 'entry_media', 'value' => 'true' ),
			),
		)
	);
}
vc_lean_map( 'vcex_post_type_list', 'vcex_post_type_list_vc_map' );

// Get autocomplete suggestion
add_filter( 'vc_autocomplete_vcex_post_type_list_tax_query_taxonomy_callback', 'vcex_suggest_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_filter_taxonomy_callback', 'vcex_suggest_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_tax_query_terms_callback', 'vcex_suggest_terms', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_author_in_callback', 'vcex_suggest_users', 10, 1 );

// Render autocomplete suggestions
add_filter( 'vc_autocomplete_vcex_post_type_list_filter_taxonomy_render', 'vcex_render_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_tax_query_taxonomy_render', 'vcex_render_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_tax_query_terms_render', 'vcex_render_terms', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_list_author_in_render', 'vcex_render_users', 10, 1 );