<?php
/**
 * These functions are used to load template parts (partials) when used within action hooks,
 * and they probably should never be updated or modified.
 *
 * @package Total WordPress Theme
 * @subpackage Hooks
 * @version 3.3.2
 */

/*-------------------------------------------------------------------------------*/
/* -  Toggle Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get togglebar layout template part if enabled.
 *
 * @since 1.0.0
 */
function wpex_toggle_bar() {
	if ( wpex_global_obj( 'has_togglebar' ) ) {
		get_template_part( 'partials/togglebar/togglebar-layout' );
	}
}

/**
 * Get togglebar button template part.
 *
 * @since 1.0.0
 */
function wpex_toggle_bar_button() {
	if ( wpex_global_obj( 'has_togglebar' ) ) {
		get_template_part( 'partials/togglebar/togglebar-button' );
	}

}

/*-------------------------------------------------------------------------------*/
/* -  Top Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get Top Bar layout template part if enabled.
 *
 * @since 1.0.0
 */
function wpex_top_bar() {
	if ( wpex_global_obj( 'has_top_bar' ) || wpex_global_obj( 'top_bar_social_alt' ) ) {
		get_template_part( 'partials/topbar/topbar-layout' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Header
/*-------------------------------------------------------------------------------*/

/**
 * Get the header template part if enabled.
 *
 * @since Total 1.5.3
 */
function wpex_header() {
	if ( wpex_global_obj( 'has_header' ) ) {
		get_template_part( 'partials/header/header-layout' );
	}
}

/**
 * Get the header logo template part.
 *
 * @since 1.0.0
 */
function wpex_header_logo() {
	get_template_part( 'partials/header/header-logo' );
}

/**
 * Get the header aside content template part.
 *
 * @since Total 1.5.3
 */
function wpex_header_aside() {
	if ( wpex_header_supports_aside() ) {
		get_template_part( 'partials/header/header-aside' );
	}
}

/**
 * Get header search dropdown template part.
 *
 * @since 1.0.0
 */
function wpex_search_dropdown() {

	// Make sure site is set to dropdown style
	if ( 'drop_down' != wpex_global_obj( 'menu_search_style' ) ) {
		return;
	}

	// Get header style
	$header_style = wpex_global_obj( 'header_style' );

	// Get current filter
	$filter = current_filter();

	// Set get variable to false by default
	$get = false;

	// Check current filter against header style
	if ( 'wpex_hook_header_inner' == $filter ) {
		if ( 'one' == $header_style || 'five' == $header_style ) {
			$get = true;
		}
	} elseif ( 'wpex_hook_main_menu_bottom' == $filter ) {
		if ( 'two' == $header_style || 'three' == $header_style || 'four' == $header_style ) {
			$get = true;
		}
	}

	// Get search dropdown template part
	if ( $get ) {
		get_template_part( 'partials/search/header-search-dropdown' );
	}

}

/**
 * Get header search replace template part.
 *
 * @since 1.0.0
 */
function wpex_search_header_replace() {
	if ( 'header_replace' == wpex_global_obj( 'menu_search_style' ) ) {
		get_template_part( 'partials/search/header-search-replace' );
	}
}

/**
 * Gets header search overlay template part.
 *
 * @since 1.0.0
 */
function wpex_search_overlay() {
	if ( 'overlay' == wpex_global_obj( 'menu_search_style' ) ) {
		get_template_part( 'partials/search/header-search-overlay' );
	}
}

/**
 * Overlay Header Wrap Open
 *
 * @since 3.2.0
 */
function wpex_overlay_header_wrap_open() {
	if ( wpex_global_obj( 'has_overlay_header' ) ) {
		echo '<div id="overlay-header-wrap" class="clr">';
	}
}

/**
 * Overlay Header Wrap Close
 *
 * @since 3.2.0
 */
function wpex_overlay_header_wrap_close() {
	if ( wpex_global_obj( 'has_overlay_header' ) ) {
		echo '</div><!-- .overlay-header-wrap -->';
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Menu
/*-------------------------------------------------------------------------------*/

/**
 * Outputs the main header menu
 *
 * @since 1.0.0
 */
function wpex_header_menu() {

	// Set vars
	$header_style = wpex_global_obj( 'header_style' );
	$filter       = current_filter();
	$get          = false;

	// Header Inner Hook
	if ( 'wpex_hook_header_inner' == $filter ) {
		if ( ( 'one' == $header_style || 'five' == $header_style || 'six' == $header_style ) ) {
			$get = true;
		}
	}

	// Header Top Hook
	elseif ( 'wpex_hook_header_top' == $filter ) {
		if (  'four' == $header_style ) {
			$get = true;
		}
	}

	// Header bottom hook
	elseif ( 'wpex_hook_header_bottom' == $filter ) {
		if ( ( 'two' == $header_style || 'three' == $header_style ) ) {
			$get = true;
		}
	}

	// Get menu template part
	if ( $get ) {
		get_template_part( 'partials/header/header-menu' );
	}

}

/*-------------------------------------------------------------------------------*/
/* -  Menu > Mobile
/*-------------------------------------------------------------------------------*/

/**
 * Gets the template part for the fixed top mobile menu style
 *
 * @since 3.0.0
 */
function wpex_mobile_menu_fixed_top() {
	if ( wpex_global_obj( 'responsive' )
		&& wpex_global_obj( 'has_mobile_menu' )
		&& 'fixed_top' == wpex_global_obj( 'mobile_menu_toggle_style' )
	) {
		get_template_part( 'partials/header/header-menu-mobile-fixed-top' );
	}
}

/**
 * Gets the template part for the navbar mobile menu_style
 *
 * @since 3.0.0
 */
function wpex_mobile_menu_navbar() {

	// Get var
	$get = false;

	// Current filter
	$filter = current_filter();

	// Check overlay header
	$has_overlay_header = wpex_global_obj( 'has_overlay_header' );

	// Overlay header should display above and others below
	if ( $filter == 'wpex_outer_wrap_before' && $has_overlay_header ) {
		$get = true;
	} elseif ( $filter == 'wpex_hook_header_bottom' && ! $has_overlay_header ) {
		$get = true;
	}

	// Get mobile menu navbar
	if ( $get
		&& wpex_global_obj( 'responsive' )
		&& wpex_global_obj( 'has_mobile_menu' )
		&& 'navbar' == wpex_global_obj( 'mobile_menu_toggle_style' )
	) {
		get_template_part( 'partials/header/header-menu-mobile-navbar' );
	}

}

/**
 * Gets the template part for the "icons" style mobile menu.
 *
 * @since 1.0.0
 */
function wpex_mobile_menu_icons() {
	$style = wpex_global_obj( 'mobile_menu_toggle_style' );
	if ( wpex_global_obj( 'responsive' )
		&& wpex_global_obj( 'has_mobile_menu' )
		&& ( 'icon_buttons' == $style || 'icon_buttons_under_logo' == $style )
	) {
		get_template_part( 'partials/header/header-menu-mobile-icons' );
	}
}

/**
 * Get mobile menu alternative if enabled.
 *
 * @since 1.3.0
 */
function wpex_mobile_menu_alt() {
	if ( wpex_global_obj( 'responsive' )
		&& wpex_global_obj( 'has_mobile_menu' )
		&& has_nav_menu( 'mobile_menu_alt' )
	) {
		get_template_part( 'partials/header/header-menu-mobile-alt' );
	}
}


/**
 * Sidr Close button
 *
 * @since 3.2.0
 */
function wpex_sidr_close() { ?>
	<?php if ( 'sidr' == wpex_global_obj( 'mobile_menu_style' ) ) : ?>
		<div id="sidr-close"><a href="#sidr-close" class="toggle-sidr-close"></a></div>
	<?php endif; ?>
<?php }

/*-------------------------------------------------------------------------------*/
/* -  Page Header
/*-------------------------------------------------------------------------------*/

/**
 * Get page header template part if enabled.
 *
 * @since 1.5.2
 */
function wpex_page_header() {
	if ( wpex_global_obj( 'has_page_header' ) ) {
		get_template_part( 'partials/page-header' );
	}
}

/**
 * Get page header title template part if enabled.
 *
 * @since 1.0.0
 */
function wpex_page_header_title() {
	if ( wpex_global_obj( 'has_page_header_title' ) ) {
		get_template_part( 'partials/page-header-title' );
	}
}

/**
 * Get post heading template part.
 *
 * @since 1.0.0
 */
function wpex_page_header_subheading() {
	if ( wpex_global_obj( 'has_page_header_subheading' ) ) {
		get_template_part( 'partials/page-header-subheading' );
	}
}

/**
 * Open wrapper around page header content to vertical align things
 *
 * @since 3.3.3
 */
function wpex_page_header_title_table_wrap_open() {
	if ( 'background-image' == wpex_global_obj( 'page_header_style' ) ) {
		echo '<div class="page-header-table clr"><div class="page-header-table-cell">';
	}
}

/**
 * Close wrapper around page header content to vertical align things
 *
 * @since 3.3.3
 */
function wpex_page_header_title_table_wrap_close() {
	if ( 'background-image' == wpex_global_obj( 'page_header_style' ) ) {
		echo '</div></div>';
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Content
/*-------------------------------------------------------------------------------*/

/**
 * Gets sidebar template
 *
 * @since 2.1.0
 */
function wpex_get_sidebar_template() {
	if ( ! in_array( wpex_global_obj( 'post_layout' ), array( 'full-screen', 'full-width' ) ) ) {
		get_sidebar( apply_filters( 'wpex_get_sidebar_template', null ) );
	}
}

/**
 * Displays correct sidebar
 *
 * @since 1.6.5
 */
function wpex_display_sidebar() {
	if ( $sidebar = wpex_get_sidebar() ) {
		dynamic_sidebar( $sidebar );
	}
}

/**
 * Get term description.
 *
 * @since 1.0.0
 */
function wpex_term_description() {
	if ( wpex_has_term_description_above_loop() ) {
		get_template_part( 'partials/term-description' );
	}
}

/**
 * Get next/previous links.
 *
 * @since 1.0.0
 */
function wpex_next_prev() {
	if ( wpex_has_next_prev() ) {
		get_template_part( 'partials/next-prev' );
	}
}

/**
 * Get next/previous links.
 *
 * @since 1.0.0
 */
function wpex_post_edit() {
	if ( wpex_has_post_edit() ) {
		get_template_part( 'partials/post-edit' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Blog
/*-------------------------------------------------------------------------------*/

/**
 * Blog single media above content
 *
 * @since 1.0.0
 */
function wpex_blog_single_media_above() {

	// Only needed for blog posts
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	// Media position
	$blog_single_media_position = get_post_meta( get_the_ID(), 'wpex_post_media_position', true );
	$blog_single_media_position = apply_filters( 'wpex_blog_single_media_position', $blog_single_media_position );

	// Display the post media above the post (this is a meta option)
	if ( 'above' == $blog_single_media_position && ! post_password_required() ) {

		// Get post format.
		$post_format = get_post_format() ? get_post_format() : 'thumbnail';

		// Get correct media template part
		get_template_part( 'partials/blog/media/blog-single', $post_format );

	}

}

/*-------------------------------------------------------------------------------*/
/* -  Footer
/*-------------------------------------------------------------------------------*/

/**
 * Gets the footer callout template part.
 *
 * @since 1.0.0
 */
function wpex_footer_callout() {
	if ( wpex_global_obj( 'has_footer_callout' ) ) {
		get_template_part( 'partials/footer/footer-callout' );
	}
}

/**
 * Gets the footer layout template part.
 *
 * @since 2.0.0
 */
function wpex_footer() {
	if ( wpex_global_obj( 'has_footer' ) ) {
		get_template_part( 'partials/footer/footer-layout' );
	}
}

/**
 * Get the footer widgets template part.
 *
 * @since 1.0.0
 */
function wpex_footer_widgets() {
	get_template_part( 'partials/footer/footer-widgets' );
}

/**
 * Gets the footer bottom template part.
 *
 * @since 1.0.0
 */
function wpex_footer_bottom() {
	if ( wpex_get_mod( 'footer_bottom', true ) ) {
		get_template_part( 'partials/footer/footer-bottom' );
	}
}

/**
 * Gets the scroll to top button template part.
 *
 * @since 1.0.0
 */
function wpex_scroll_top() {
	if ( wpex_get_mod( 'scroll_top', true ) ) {
		get_template_part( 'partials/scroll-top' );
	}
}

/**
 * Footer reaveal open code
 *
 * @since 2.0.0
 */
function wpex_footer_reveal_open() {
	if ( wpex_global_obj( 'has_footer_reveal' ) ) {
		get_template_part( 'partials/footer-reveal-open' );
	}
}

/**
 * Footer reaveal close code
 *
 * @since 2.0.0
 */
function wpex_footer_reveal_close() {
	if ( wpex_global_obj( 'has_footer_reveal' ) ) {
		get_template_part( 'partials/footer-reveal-close' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Other
/*-------------------------------------------------------------------------------*/

/**
 * Get togglebar layout template part if enabled.
 *
 * @since 3.4.0
 */
function wpex_ls_top() {
	echo '<span data-ls_id="#site_top"></span>';
}

/**
 * Returns social sharing template part
 *
 * @since 2.0.0
 */
function wpex_social_share() {
	get_template_part( 'partials/social-share' );
}

/**
 * Adds a hidden searchbox in the footer for use with the mobile menu
 *
 * @since 1.5.1
 */
function wpex_mobile_searchform() {
	if ( wpex_get_mod( 'mobile_menu_search', true ) ) {
		get_template_part( 'partials/search/mobile-searchform' );
	}
}

/**
 * Outputs page/post slider based on the wpex_post_slider_shortcode custom field
 *
 * @since Total 1.0.0
 */
function wpex_post_slider( $post_id = '', $postion = '' ) {

	// Return if there isn't a slider defined
	if ( ! wpex_global_obj( 'has_post_slider' ) ) {
		return;
	}

	// Get current filter
	$filter = current_filter();

	// Define get variable
	$get = false;

	// Get slider position
	$position = wpex_global_obj( 'post_slider_position' );

	// Get current filter against slider position
	if ( 'above_topbar' == $position && 'wpex_hook_topbar_before' == $filter ) {
		$get = true;
	} elseif ( 'above_header' == $position && 'wpex_hook_header_before' == $filter ) {
		$get = true;
	} elseif ( 'above_menu' == $position && 'wpex_hook_header_bottom' == $filter ) {
		$get = true;
	} elseif ( 'above_title' == $position && 'wpex_hook_page_header_before' == $filter ) {
		$get = true;
	} elseif ( 'below_title' == $position && 'wpex_hook_main_top' == $filter ) {
		$get = true;
	}

	// Return if $get is still false after checking filters
	if ( $get ) {
		get_template_part( 'partials/post-slider' );
	}

}