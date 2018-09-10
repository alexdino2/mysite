<?php
/**
 * Visual Composer Button
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Not needed in admin ever
if ( is_admin() ) {
	return;
}

// Deprecated Attributes
if ( ! empty( $atts['class'] ) && empty( $classes ) ) {
	$atts['classes'] = $atts['class'];
}
if ( isset( $atts['lightbox'] ) && 'true' == $atts['lightbox'] ) {
	$atts['onclick'] = 'lightbox';
}
if ( ! empty( $atts['lightbox_image'] ) ) {
	$atts['image_attachment'] = $atts['lightbox_image'];
}

// Get shortcode attributes
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

// Extract shortcode attributes
extract( $atts );

// Sanitize & declare vars
$content     = ! empty( $content ) ? $content : esc_html__( 'Button Text', 'total' );
$inline_js   = array();
$data_attr   = array();
$url         = $url ? esc_url( $url ) : '#';
$target_html = vcex_html( 'target_attr', $target );
$rel         = vcex_html( 'rel_attr', $rel );

// Load custom font
if ( $font_family ) {
	wpex_enqueue_google_font( $font_family );
}

// Button Classes
$button_classes = array( 'vcex-button' );
$button_classes[] = wpex_get_button_classes( $style, $color, $size, $align );
if ( $layout ) {
	$button_classes[] = $layout;
}
if ( $classes ) {
	$button_classes[] = vcex_get_extra_class( $classes );
}
if ( $hover_animation ) {
	$button_classes[] = wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
} else {
	$button_classes[] = 'animate-on-hover';
}
if ( 'local' == $target ) {
	$button_classes[] = 'local-scroll-link';
}
if ( $css_animation ) {
	$button_classes[] = $this->getCSSAnimation( $css_animation );
}
if ( $classes ) {
	$button_classes[] = $this->getExtraClass( $classes );
}
if ( $visibility ) {
	$button_classes[] = $visibility;
}

// Check for image attachment
if ( 'image' == $onclick || 'lightbox' == $onclick ) {
	$url = $image_attachment ? wp_get_attachment_url( $image_attachment ) : $url;
}

// Lightbox classes and data
if ( 'lightbox' == $onclick ) {

	// Enqueue lightbox style
	vcex_enque_style( 'ilightbox' );

	// Parse lightbox dimensions
	$lightbox_dimensions = vcex_parse_lightbox_dims( $lightbox_dimensions );

	// Set data attributes based on lightbox type
	if ( 'iframe' == $lightbox_type ) {
		
		$button_classes[] = 'wpex-lightbox';
		$data_attr[]      = 'data-type="iframe"';
		$data_attr[]      = 'data-options="'. $lightbox_dimensions .'"';

	} elseif ( 'image' == $lightbox_type ) {

		$button_classes[] = 'wpex-lightbox';
		$data_attr[]      = 'data-type="image"';
		if ( $image_attachment ) {
			$url = wp_get_attachment_url( $image_attachment );
		}
		if ( $lightbox_dimensions ) {
			$data_attr[]      = 'data-options="'. $lightbox_dimensions .'"';
		}
		$inline_js[] = 'ilightbox_single';

	} elseif ( 'video_embed' == $lightbox_type ) {

		$url = wpex_sanitize_data( $url, 'embed_url' );
		$button_classes[] = 'wpex-lightbox';
		$data_attr[]      = 'data-type="iframe"';
		if ( $lightbox_dimensions ) {
			$data_attr[]      = 'data-options="'. $lightbox_dimensions .'"';
		}

	} elseif ( 'html5' == $lightbox_type ) {

		$lightbox_video_html5_webm = $lightbox_video_html5_webm ? $lightbox_video_html5_webm : $url;
		$poster = wp_get_attachment_url( $lightbox_poster_image );
		$button_classes[] = 'wpex-lightbox';
		$data_attr[]      = 'data-type="video"';
		$data_attr[]      = 'data-options="'. $lightbox_dimensions .', html5video: { webm: \''. $lightbox_video_html5_webm .'\', poster: \''. $poster .'\' }"';

	} elseif ( 'quicktime' == $lightbox_type ) {

		$button_classes[] = 'wpex-lightbox';
		$data_attr[]      = 'data-type="video"';
		if ( $lightbox_dimensions ) {
			$data_attr[]      = 'data-options="'. $lightbox_dimensions .'"';
		}

	} else {
		$button_classes[] = 'wpex-lightbox-autodetect';
	}

}

// Wrap classes
$wrap_classes = array();
if ( 'center' == $align ) {
	$wrap_classes[] = 'textcenter';
}
if ( 'block' == $layout ){
	$wrap_classes[] = 'theme-button-block-wrap';
}
if ( 'expanded' == $layout ){
	$wrap_classes[]   = 'theme-button-expanded-wrap';
	$button_classes[] = 'expanded';
}
if ( $wrap_classes ) {
	$wrap_classes[] = 'theme-button-wrap';
	$wrap_classes[] = 'clr';
	$wrap_classes   = implode( ' ', $wrap_classes );
}

// Custom Style
$inline_style = vcex_inline_style( array(
	'background'     => $custom_background,
	'padding'        => $font_padding,
	'color'          => $custom_color,
	'font_size'      => $font_size,
	'font_weight'    => $font_weight,
	'letter_spacing' => $letter_spacing,
	'border_radius'  => $border_radius,
	'margin'         => $margin,
	'width'          => $width,
	'text_transform' => $text_transform,
	'font_family'    => $font_family,
), false );
if ( $custom_color && 'outline' == $style ) {
	$inline_style .= 'border-color:'. $custom_color .';';
}
if ( $inline_style ) {
	$inline_style = ' style="'. esc_attr( $inline_style ) .'"';
}

// Custom hovers
if ( $custom_hover_background || $custom_hover_color ) {
	if ( $custom_hover_background ) {
		$data_attr[] = 'data-hover-background="'. $custom_hover_background .'"';
	}
	if ( $custom_hover_color ) {
		$data_attr[] = 'data-hover-color="'. $custom_hover_color .'"';
	}
	if ( $data_attr ) {
		$button_classes[] = 'wpex-data-hover';
	}
	$inline_js[] = 'data_hover';
}

// Define button icon_classes
$icon_left  = vcex_get_icon_class( $atts, 'icon_left' );
$icon_right = vcex_get_icon_class( $atts, 'icon_right' );

// Icon left style
if ( $icon_left ) {
	$icon_left_style = vcex_inline_style ( array(
		'margin_right' => $icon_left_padding,
	) );
}

// Icon right style
if ( $icon_right ) {
	$icon_right_style = vcex_inline_style ( array(
		'margin_left' => $icon_right_padding,
	) );
}

// Load icon fonts if needed
if ( $icon_left || $icon_right ) {
	vcex_enqueue_icon_font( $icon_type );
}

// Load inline js
vcex_inline_js( $inline_js );

// Turn arrays into strings
$button_classes = implode( ' ', $button_classes );
$data_attr      = implode( ' ', $data_attr );

// Open CSS wrapper
if ( $css_wrap ) echo '<div class="'. vc_shortcode_custom_css_class( $css_wrap ) .' wpex-clr">';

// Open wrapper for specific button styles
if ( $wrap_classes ) echo '<div class="'. esc_attr( $wrap_classes ) .'">'; ?>
	<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $button_classes ); ?>" title="<?php echo esc_attr( $title ); ?>"<?php echo $inline_style; ?><?php echo $target_html; ?><?php echo $rel; ?> <?php echo $data_attr; ?><?php echo vcex_unique_id( $unique_id ); ?>>
		<span class="theme-button-inner"><?php
			// Define output
			$output = '';
			// Display left Icon
			if ( $icon_left ) {
				$output .='<span class="vcex-icon-wrap theme-button-icon-left">
					<span class="'. $icon_left .'"'. $icon_left_style .'></span>
				</span>';
			}
			// Button Text
			$output .= $content;
			// Display Right Icon
			if ( $icon_right ) {
				$output .='<span class="vcex-icon-wrap theme-button-icon-right">
					<span class="'. $icon_right .'"'. $icon_right_style .'></span>
				</span>';
			}
			echo $output; ?></span><!-- .theme-button-inner -->
	</a><!-- <?php echo esc_attr( $button_classes ); ?> -->
<?php
// Close wrapper for specific button styles
if ( $wrap_classes ) echo '</div><!-- '. esc_attr( $wrap_classes ) .' -->';

// Close css wrap div
if ( $css_wrap ) echo '</div>';