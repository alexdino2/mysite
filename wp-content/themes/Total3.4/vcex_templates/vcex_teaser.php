<?php
/**
 * Visual Composer Teaser
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Not needed in admin ever
if ( is_admin() ) {
	return;
}

// Get and extract shortcode attributes
extract( vc_map_get_attributes( $this->getShortcode(), $atts ) );

// Add main Classes
$wrap_classes = 'vcex-teaser';
if ( $css_animation ) {
	$wrap_classes .= $this->getCSSAnimation( $css_animation );
}
if ( $style ) {
	$wrap_classes .= ' vcex-teaser-'. $style;
}
if ( $classes ) {
	$wrap_classes .= $this->getExtraClass( $classes );
}
if ( $visibility ) {
	$wrap_classes .= ' '. $visibility;
}
if ( $hover_animation ) {
	$wrap_classes .= ' '. wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
if ( 'two' == $style ) {
	$wrap_classes .= ' wpex-bg-gray';
	$wrap_classes .= ' wpex-padding-20';
	$wrap_classes .= ' wpex-bordered';
	$wrap_classes .= ' wpex-rounded';
} elseif ( 'three' == $style ) {
	$wrap_classes .= ' wpex-bg-gray';
	$wrap_classes .= ' wpex-bordered';
} elseif ( 'four' == $style ) {
	$wrap_classes .= ' wpex-bordered';
}
if ( $css ) {
	$wrap_classes .= ' '. vc_shortcode_custom_css_class( $css );
}

// Add inline style for main div
$wrap_style = '';
if ( $text_align ) {
	$wrap_style .= 'text-align:'. $text_align .';';
}
if ( $padding && 'two' == $style ) {
	$wrap_style .= 'padding:'. $padding .';';
}
if ( $background && 'two' == $style ) {
	$wrap_style .= 'background:'. $background .';';
}
if ( $background && 'three' == $style && '' == $content_background ) {
	$wrap_style .= 'background:'. $background .';';
}
if ( $border_color ) {
	$wrap_style .= 'border-color:'. $border_color .';';
}
if ( $border_radius ) {
	$wrap_style .= 'border-radius:'. $border_radius .';';
}
if ( $wrap_style ) {
	$wrap_style = ' style="'. $wrap_style .'"';
}

// Media classes
$media_classes = 'vcex-teaser-media';
if ( 'three' == $style || 'four' == $style ) {
	$media_classes .= ' no-margin';
}

// Content classes
$content_classes  = 'vcex-teaser-content clr';
if ( 'three' == $style || 'four' == $style ) {
	$content_classes .= ' wpex-padding-20';
}

// Match Height Inline JS
if ( false !== strpos( $classes, 'equal-height-content' ) ) {
	vcex_inline_js( 'equal_height_content' );
} ?>

<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_style; ?>>

	<?php
	// Video
	if ( $video ) : ?>
		<div class="<?php echo $media_classes; ?> responsive-video-wrap">
			<?php echo wp_oembed_get( $video ); ?>
		</div>
	<?php endif; ?>

	<?php
	// Check for and sanitize URL
	if ( $url && '||' != $url ) :

		// Link attributes
		$url_atts = vc_build_link( $url );
		if ( ! empty( $url_atts['url'] ) ) {
			$url        = isset( $url_atts['url'] ) ? $url_atts['url'] : $url;
			$url_title  = isset( $url_atts['title'] ) ? $url_atts['title'] : $url_title;
			$url_target = isset( $url_atts['target'] ) ? $url_atts['target'] : $url_target;
		}

		// Satnitize URL
		$url = esc_url( $url );

		// URL title fallback
		$url_title = $url_title ? $url_title : $heading;

		// Link classes
		$url_classes = 'wpex-td-none';

		// Target blank
		if ( strpos( $url_target, 'blank' ) ) {
			$url_target = ' target="_blank"';
		}

		// Local scroll
		if ( 'true' == $url_local_scroll ) {
			$url_target = 'local';
		}
		if ( 'local' == $url_target ) {
			$url_classes .= ' local-scroll-link';
		} ?>

	<?php endif; ?>

	<?php
	// Image
	if ( $image ) :

		// Image classes
		$image_classes = 'vcex-img-va-bottom '. $media_classes;
		if ( $img_filter ) {
			$image_classes .= ' '. wpex_image_filter_class( $img_filter );
		}
		if ( $img_hover_style ) {
			$image_classes .= ' '. wpex_image_hover_classes( $img_hover_style );
		}
		if ( $img_align ) {
			$image_classes .= ' text'. $img_align;
		}
		if ( 'stretch' == $img_style ) {
			$image_classes .= ' stretch-image';
		} ?>

		<figure class="<?php echo $image_classes; ?>">
			<?php
			// Open URl
			if ( $url ) { ?>
				<a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $url_title ); ?>" class="<?php echo $url_classes; ?>"<?php echo $url_target; ?>>
			<?php } ?>
				<?php
				// Display image
				wpex_post_thumbnail( array(
					'attachment' => $image,
					'size'       => 'wpex_custom',
					'width'      => $img_width,
					'height'     => $img_height,
				) ); ?>
			<?php if ( $url ) echo '</a>'; ?>
		</figure>

	<?php endif; ?>

	<?php
	// Content
	if ( $content || $heading ) :

		// Content area
		$content_style = array(
			'margin'     => $content_margin,
			'padding'    => $content_padding,
			'background' => $content_background,

		);
		if ( $border_radius && ( 'three' == $style || 'four' == $style ) ) {
			$content_style['border_radius'] = $border_radius;
		}
		$content_style = vcex_inline_style( $content_style ); ?>

		<div class="<?php echo $content_classes; ?>"<?php echo $content_style; ?>>

			<?php
			/// Heading
			if ( $heading ) :

				// Load custom font
				if ( $heading_font_family ) {
					wpex_enqueue_google_font( $heading_font_family );
				}

				// Heading style
				$heading_style = vcex_inline_style( array(
					'font_family'    => $heading_font_family,
					'color'          => $heading_color,
					'font_size'      => $heading_size,
					'margin'         => $heading_margin,
					'font_weight'    => $heading_weight,
					'letter_spacing' => $heading_letter_spacing,
					'text_transform' => $heading_transform,
				) ); ?>

				<<?php echo $heading_type; ?> class="vcex-teaser-heading wpex-em-16px no-margin"<?php echo $heading_style; ?>>
					<?php
					// Open URl
					if ( $url ) { ?>
						<a href="<?php echo $url; ?>" title="<?php echo esc_attr( $url_title ); ?>" class="<?php echo $url_classes; ?>"<?php echo $url_target; ?>>
					<?php } ?>
						<?php echo $heading; ?>
					<?php
					// Close URL
					if ( $url ) echo '</a>'; ?>
				</<?php echo $heading_type; ?>>

			<?php endif; ?>

			<?php
			// Content
			if ( $content ) :
				
				$text_style = vcex_inline_style( array(
					'font_size'   => $content_font_size,
					'color'       => $content_color,
					'font_weight' => $content_font_weight,
				) ); ?>

				<div class="vcex-teaser-text remove-last-p-margin clr"<?php echo $text_style; ?>>
					<?php echo do_shortcode( wpautop( $content ) ); ?>
				</div><!-- .vcex-teaser-text -->

			<?php endif; ?>

		</div><!-- .vcex-teaser-content -->

	<?php endif; ?>

</div><!-- .vcex-teaser -->