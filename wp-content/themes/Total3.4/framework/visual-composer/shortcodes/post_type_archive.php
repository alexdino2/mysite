<?php
/**
 * Post Type Archive
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
class WPBakeryShortCode_vcex_post_type_archive extends WPBakeryShortCode {
	protected function content( $atts, $content = null ) {
		ob_start();
		include( locate_template( 'vcex_templates/vcex_post_type_archive.php' ) );
		return ob_get_clean();
	}
}

/**
 * Adds the shortcode to the Visual Composer
 *
 * @since 1.4.1
 */
function vcex_post_type_archive_vc_map() {
	$post_types = array();
	if ( is_admin() ) {
		$post_types = vcex_get_post_types();
	}
	return array(
		'name' => esc_html__( 'Post Types Archive', 'total' ),
		'description' => esc_html__( 'Custom post type archive', 'total' ),
		'base' => 'vcex_post_type_archive',
		'category' => wpex_get_theme_branding(),
		'icon' => 'vcex-post-type-grid vcex-icon fa fa-files-o',
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
				'heading' => esc_html__( 'Custom Classes', 'total' ),
				'param_name' => 'classes',
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
				'type' => 'dropdown',
				'heading' => esc_html__( 'Pagination', 'total' ),
				'param_name' => 'pagination',
				'value' => array(
					__( 'False', 'total') => '',
					__( 'True', 'total' ) => 'true',
				),
				'group' => esc_html__( 'Query', 'total' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Post Type', 'total' ),
				'param_name' => 'post_type',
				'value' => $post_types,
				'group' => esc_html__( 'Query', 'total' ),
				'admin_label' => true,
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
					__( 'No', 'total' ) => '',
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
					//'unique_values' => true,
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
				'dependency' => array(
					'element' => 'tax_query',
					'value' => 'true',
				),
				'settings' => array(
					'multiple' => true,
					'min_length' => 1,
					'groups' => true,
					//'unique_values' => true,
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
					__( 'Default', 'total' ) => '',
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
				'dependency' => array(
					'element' => 'orderby',
					'value' => array( 'meta_value_num', 'meta_value' ),
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Post With Thumbnails Only', 'total' ),
				'param_name' => 'thumbnail_query',
				'value' => array(
					__( 'No', 'total' ) => '',
					__( 'Yes', 'total') => 'true',
				),
				'group' => esc_html__( 'Query', 'total' ),
			),

		)
	);
}
vc_lean_map( 'vcex_post_type_archive', 'vcex_post_type_archive_vc_map' );

// Get autocomplete suggestion
add_filter( 'vc_autocomplete_vcex_post_type_archive_tax_query_taxonomy_callback', 'vcex_suggest_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_archive_tax_query_terms_callback', 'vcex_suggest_terms', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_archive_author_in_callback', 'vcex_suggest_users', 10, 1 );

// Render autocomplete suggestions
add_filter( 'vc_autocomplete_vcex_post_type_archive_tax_query_taxonomy_render', 'vcex_render_taxonomies', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_archive_tax_query_terms_render', 'vcex_render_terms', 10, 1 );
add_filter( 'vc_autocomplete_vcex_post_type_archive_author_in_render', 'vcex_render_users', 10, 1 );