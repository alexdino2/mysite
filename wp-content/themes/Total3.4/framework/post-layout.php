<?php
/**
 * Returns the correct main layout class for the current post/page/archive/etc
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 */

/**
 * Returns defined post layout for curent post
 *
 * @since 2.0.0
 */
function wpex_get_post_layout() {

	// Get global object
	$obj = wpex_global_obj();

	// Return post layout if defined
	if ( ! empty( $obj->post_layout ) ) {
		return $obj->post_layout;
	}

	// Backup check incase $wpex_theme isn't defined for some reason
	else {
		return wpex_post_layout();
	}

}

/**
 * Sets the correct layout for posts
 *
 * @since Total 1.0.0
 */
function wpex_post_layout( $post_id = '' ) {

	// Get ID if not defined
	$post_id = $post_id ? $post_id : wpex_get_the_id();

	// Define variables
	$class  = 'right-sidebar';
	$meta   = get_post_meta( $post_id, 'wpex_post_layout', true );

	// Check meta first to override and return (prevents filters from overriding meta)
	if ( $meta ) {
		return $meta;
	}

	// Singular Page
	if ( is_page() ) {

		// Blog template
		if ( is_page_template( 'templates/blog.php' ) ) {
			$class = wpex_get_mod( 'blog_archives_layout', 'right-sidebar' );
		}

		// Landing Page
		if ( is_page_template( 'templates/landing-page.php' ) ) {
			$class = 'full-width';
		}

		// Attachment
		elseif ( is_attachment() ) {
			$class = 'full-width';
		}

		// All other pages
		else {
			$class = wpex_get_mod( 'page_single_layout', 'right-sidebar' );
		}

	}

	// Singular Post
	elseif ( is_singular( 'post' ) ) {
		$class = wpex_get_mod( 'blog_single_layout', 'right-sidebar' );
	}

	// Attachment
	elseif ( is_singular( 'attachment' ) ) {
		 $class = 'full-width';
	}

	// Home
	elseif ( is_home() ) {
		$class = wpex_get_mod( 'blog_archives_layout', 'right-sidebar' );
	}

	// Search
	elseif ( is_search() ) {
		$class = wpex_get_mod( 'search_layout', 'right-sidebar' );
	}

	// Standard Categories
	elseif ( is_category() ) {
		$class      = wpex_get_mod( 'blog_archives_layout', 'right-sidebar' );
		$term       = get_query_var( 'cat' );
		$term_data  = get_option( "category_$term" );
		if ( $term_data ) {
			if( ! empty( $term_data['wpex_term_layout'] ) ) {
				$class = $term_data['wpex_term_layout'];
			}
		}
	}

	// Archives
	elseif ( wpex_is_blog_query() ) {
		$class = wpex_get_mod( 'blog_archives_layout', 'right-sidebar' );
	}
	
	// 404 page
	elseif ( is_404() ) {
		$class = 'full-width';
	}

	// All else
	else {
		$class = 'right-sidebar';
	}

	// Class should never be empty
	if ( empty( $class ) ) {
		$class = 'right-sidebar';
	}

	// Apply filters for child theme editing
	$class = apply_filters( 'wpex_post_layout_class', $class );
	
	// Return correct classname
	return $class;
	
}