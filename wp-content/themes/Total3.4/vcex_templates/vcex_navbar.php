<?php
/**
 * Visual Composer Navbar
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Not needed in admin ever
if ( is_admin() ) {
	return;
}

// Deprecated params
$border_radius = isset( $atts['border_radius'] ) ? $atts['border_radius'] : '';

// Get and extract shortcode attributes
extract( vc_map_get_attributes( $this->getShortcode(), $atts ) );

// Return if no menu defined
if ( ! $menu ) {
	return;
}

// Old style param fallback
if ( isset( $style ) && 'simple' == $style ) {
	$button_style = 'plain-text';
}

// Sanitize vars
$preset_design = $preset_design ? $preset_design : 'none';

// Get current post ID
$post_id = get_the_ID();

// Hover animation
if ( $hover_animation ) {
	$hover_animation = wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}

// CSS class
if ( $css ) {
	$css_class = vc_shortcode_custom_css_class( $css );
} else {
	$css_class = '';
}

// Link Data
$link_hover_class = $link_data = '';
if ( $hover_bg ) {
	$link_data .= ' data-hover-background="'. $hover_bg .'"';
}
if ( $hover_color ) {
	$link_data .= ' data-hover-color="'. $hover_color .'"';
}
if ( $link_data ) {
	$link_hover_class = 'wpex-data-hover';
}

// Border radius
if ( $border_radius ) {
	$border_radius = vcex_get_border_radius_class( $border_radius );
}

// Wrap style
$wrap_style = vcex_inline_style( array(
	'font_size'      => $font_size,
	'letter_spacing' => $letter_spacing,
	'font_family'    => $font_family,
) );

// Load custom fonts
if ( $font_family ) {
	wpex_enqueue_google_font( $font_family );
}

// Classes
$wrap_classes = array( 'vcex-navbar', 'vcex-clr' );
if ( 'none' != $preset_design ) {
	$wrap_classes[] = 'vcex-navbar-'. $preset_design;
}
if ( 'true' == $sticky ) {
	$wrap_classes[] = 'vcex-navbar-sticky';
}
if ( $classes ) {
	$wrap_classes[] = $this->getExtraClass( $classes );
}
if ( $link_color ) {
	$wrap_classes[] = 'wpex-color-'. $link_color;
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $align ) {
	$wrap_classes[] = 'align-'. $align;
}
if ( $css_animation ) {
	$wrap_classes[] = $this->getCSSAnimation( $css_animation );
}
if ( $wrap_css ) {
	$wrap_classes[] = vc_shortcode_custom_css_class( $wrap_css );
}
$wrap_classes = implode( ' ', $wrap_classes );

// Inner classes
$inner_classes = array( 'vcex-navbar-inner', 'vcex-clr' );
if ( 'true' == $full_screen_center ) {
	$inner_classes[] = 'container';
}
$inner_classes = implode( ' ', $inner_classes ); ?>

<nav class="<?php echo esc_attr( $wrap_classes ); ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_style; ?>>

	<div class="<?php echo esc_attr( $inner_classes ); ?>">
		<?php
		// Get menu object
		$menu = wp_get_nav_menu_object( $menu );

		// If menu isn't empty display items
		if ( ! empty( $menu ) ) :

			// Load inline js
			vcex_inline_js( array( 'data_hover' ) );

			// Get menu items
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			// Make sure we have menu items
			if ( $menu_items && is_array( $menu_items ) ) :

				// Inline styles
				$reset_border = ( $hover_bg || $hover_color ) ? 'inherit' : '';
				$active_style = vcex_inline_style( array(
					'background' => $hover_bg,
					'color'      => $hover_color,
				) );

				// Loop through menu items
				foreach ( $menu_items as $menu_item ) :

					// Link Classes
					$link_classes = array( 'vcex-navbar-link' );
					if ( 'none' == $preset_design ) {
						$link_classes[] = wpex_get_button_classes( $button_style, $button_color );
						if ( $button_color ) {
							$link_classes[] = $button_color;
						}
						if ( $button_layout ) {
							$link_classes[] = $button_layout;
						}
					}
					if ( $font_weight ) {
						$link_classes[] = 'wpex-fw-'. $font_weight;
					}
					if ( 'true' == $local_scroll && ! in_array( 'local-scroll', $menu_item->classes ) ) {
						$link_classes[] = 'local-scroll';
					}
					if ( $css_class ) {
						$link_classes[] = $css_class;
					}
					if ( $hover_animation ) {
						$link_classes[] = $hover_animation;
					}
					if ( $hover_bg ) {
						$link_classes[] = 'has-bg-hover';
					}
					if ( $border_radius ) {
						$link_classes[] = $border_radius;
					}
					if ( $link_hover_class ) {
						$link_classes[] = $link_hover_class;
					}
					if ( $menu_item->object_id == $post_id ) {
						$link_classes[] = 'active';
					}
					if ( $menu_item->classes ) {
						$link_classes = array_merge( $link_classes, $menu_item->classes );
					}
					$link_classes = implode( ' ', $link_classes );

					// Sanitize title attribute
					$title_attr = $menu_item->attr_title ? $menu_item->attr_title : $menu_item->title; ?>

					<a href="<?php echo esc_url( $menu_item->url ); ?>" title="<?php echo esc_attr( $title_attr ); ?>" class="<?php echo $link_classes; ?>"<?php echo vcex_html( 'target_attr', $menu_item->target ); ?><?php echo $link_data; ?><?php if ( $menu_item->object_id == $post_id ) echo $active_style; ?>><?php echo $menu_item->title; ?></a>

				<?php endforeach; ?>

			<?php
			// End menu_items check
			endif; ?>

		<?php
		// End menu check
		endif; ?>

	</div><!-- .vcex-navbar-inner -->

</nav><!-- .vcex-navbar -->