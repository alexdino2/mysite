<?php
/**
 * Visual Composer Feature Box
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

// Get and extract shortcode attributes
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

// Sanitize vars
$image         = $image ? $image : 'dummy';
$heading_type  = $heading_type ? $heading_type : 'h2';
$equal_heights = $video ? 'false' : $equal_heights;

// Load inline js for the VC composer
if ( 'true' == $equal_heights ) {
	vcex_inline_js( 'equal_heights' );
}

// Add style
$wrap_style = vcex_inline_style( array(
	'padding'    => $padding,
	'background' => $background,
	'border'     => $border,
	'text_align' => $text_align,
) );

// Classes
$wrap_classes = array( 'vcex-feature-box', 'clr' );
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $css_animation ) {
	$wrap_classes[] = $this->getCSSAnimation( $css_animation );
}
if ( $classes ) {
	$wrap_classes[] = $this->getExtraClass( $classes );
}
if ( $style ) {
	$wrap_classes[] = $style;
}
if ( 'true' == $equal_heights ) {
	$wrap_classes[] = 'vcex-feature-box-match-height';
}
if ( $tablet_widths ) {
	$wrap_classes[] = 'tablet-fullwidth-columns';
}
if ( $phone_widths ) {
	$wrap_classes[] = 'phone-fullwidth-columns';
}
$wrap_classes = implode( ' ', $wrap_classes ); ?>

<div class="<?php echo esc_attr( $wrap_classes ); ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_style; ?>>
	<?php
	// Image/Video check
	if ( $image || $video ) :

		// Image args
		$image_args = array(
			'attachment'    => $image,
			'size'          => $img_size,
			'width'         => $img_width,
			'height'        => $img_height,
			'crop'          => $img_crop,
		);

		// Add classes
		$inner_classes = array( 'vcex-feature-box-media', 'clr' );
		if ( 'true' == $equal_heights ) {
			$inner_classes[] = 'vcex-match-height';
		}
		$inner_classes = implode( ' ', $inner_classes );

		// Media style
		$media_style = vcex_inline_style( array(
			'width' => $media_width,
		) ); ?>

		<div class="<?php echo $inner_classes; ?>" <?php echo $media_style; ?>>

			<?php
			// Display Video
			if ( $video ) : ?>

				<div class="responsive-video-wrap">
					<?php echo wp_oembed_get( esc_url( $video ) ); ?>
				</div><!-- .vcex-feature-box-media -->

			<?php
			// Display Image
			elseif ( $image ) :

				// Get image
				$image_alt = strip_tags( get_post_meta( $image, '_wp_attachment_image_alt', true ) );

				// Image inline CSS
				$image_style = vcex_inline_style( array(
					'border_radius' => $img_border_radius,
				) );

				// Image classes
				$image_classes = array( 'vcex-feature-box-image' );
				if ( $img_filter ) {
					$image_classes[] = wpex_image_filter_class( $img_filter );
				}
				if ( $img_hover_style && 'true' != $equal_heights ) {
					$image_classes[] = wpex_image_hover_classes( $img_hover_style );
				}

				// Image URL
				if ( $image_url || 'image' == $image_lightbox ) {

					// Standard URL
					$link     = vc_build_link( $image_url );
					$a_href   = isset( $link['url'] ) ? $link['url'] : '';
					$a_title  = isset( $link['title'] ) ? $link['title'] : '';
					$a_target = isset( $link['target'] ) ? $link['target'] : '';
					$a_target = ( false !== strpos( $a_target, 'blank' ) ) ? ' target="_blank"' : '';

					// Image lightbox
					$data_attributes = '';

					if ( $image_lightbox ) {
						vcex_enque_style( 'ilightbox' );
						vcex_inline_js( 'ilightbox' );
						if ( 'image' == $image_lightbox ) {
							$image_classes[] = 'wpex-lightbox';
							$data_attributes .= ' data-type="image"';
						} elseif ( 'url' == $image_lightbox ) {
							$image_classes[] = ' wpex-lightbox';
							$data_attributes .= ' data-type="iframe"';
							$data_attributes .= ' data-options="width:1920,height:1080"';
						} elseif ( 'auto-detect' == $image_lightbox ) {
							$image_classes[] = ' wpex-lightbox-autodetect';
						} elseif ( 'video_embed' == $image_lightbox ) {
							$a_href = wpex_sanitize_data( $a_href, 'embed_url' );
							$image_classes[] = ' wpex-lightbox';
							$data_attributes .= ' data-type="iframe"';
							$data_attributes .= ' data-options="width:1920,height:1080"';
						} elseif ( 'html5' == $image_lightbox ) {
							$poster = wp_get_attachment_image_src( $img_id, 'large');
							$poster = $poster[0];
							$image_classes[] = ' wpex-lightbox';
							$data_attributes .= ' data-type="video"';
							$data_attributes .= ' data-options="width:848, height:480, html5video: { webm: \''. $lightbox_video_html5_webm .'\', poster: \''. $poster .'\' }"';
						} elseif ( 'quicktime' == $image_lightbox ) {
							$image_classes[] = ' wpex-lightbox';
							$data_attributes .= ' data-type="video"';
							$data_attributes .= ' data-options="width:1920,height:1080"';
						} else {
						   $data_attributes .= ' data-options="smartRecognition:true,width:1920,height:1080"';
						}
					}

				}

				// Turn image classes into string
				$image_classes = implode( ' ', $image_classes );

				// Open link if defined
				if ( ! empty( $a_href ) ) { ?>

					<a href="<?php echo esc_url( $a_href ); ?>" title="<?php echo esc_attr( $a_title ); ?>" class="vcex-feature-box-image-link <?php echo esc_attr( $image_classes ); ?>"<?php echo $image_style; ?><?php echo $data_attributes; ?><?php echo $a_target; ?>>

				<?php

				// Link isn't defined open div
				} else { ?>

					<div class="<?php echo $image_classes; ?>" <?php echo $image_style; ?>>

				<?php } ?>

				<?php
				// Display image
				wpex_post_thumbnail( $image_args ); ?>

				<?php
				// Close link
				if ( isset( $a_href ) && $a_href ) { ?>

					</a><!-- .vcex-feature-box-image -->

				<?php
				// Link not defined, close div
				} else { ?>

					</div><!-- .vcex-feature-box-image -->

				<?php } ?>

				<?php endif; // End video check ?>

			</div><!-- .vcex-feature-box-media -->

		<?php endif; // $video or $image check ?>

		<?php
		// Content area
		if ( $content || $heading ) {
			$add_classes = 'vcex-feature-box-content clr';
		if ( 'true' == $equal_heights ) {
			$add_classes .=' vcex-match-height';
		}
		$content_style = vcex_inline_style( array(
			'width'      => $content_width,
			'background' => $content_background
		) ); ?>

		<div class="<?php echo $add_classes; ?>"<?php echo $content_style; ?>>

			<?php if ( $content_padding ) : ?>

				<div class="vcex-feature-box-padding-container clr" style="padding:<?php echo $content_padding; ?>;">

			<?php endif; ?>

			<?php
			// Heading
			if ( $heading ) {

				// Load custom font
				if ( $heading_font_family ) {
					wpex_enqueue_google_font( $heading_font_family );
				}

				// Heading style
				$heading_style = vcex_inline_style( array(
					'font_family'    => $heading_font_family,
					'color'          => $heading_color,
					'font_size'      => $heading_size,
					'font_weight'    => $heading_weight,
					'margin'         => $heading_margin,
					'letter_spacing' => $heading_letter_spacing,
					'text_transform' => $heading_transform,
				) );

				// Heading URL
				$a_href = '';
				if ( $heading_url && '||' != $heading_url ) {
					$link     = vc_build_link( $heading_url );
					$a_href   = isset( $link['url'] ) ? $link['url'] : '';
					$a_title  = isset( $link['title'] ) ? $link['title'] : '';
					$a_target = isset( $link['target'] ) ? $link['target'] : '';
					$a_target = ( false !== strpos( $a_target, 'blank' ) ) ? ' target="_blank"' : '';
				}

				if ( isset( $a_href ) && $a_href ) { ?>

					<a href="<?php echo esc_url( $a_href ); ?>" title="<?php echo esc_attr( $a_title ); ?>"class="vcex-feature-box-heading-link"<?php echo $a_target; ?>>

				<?php } ?> 

				<<?php echo $heading_type; ?> class="vcex-feature-box-heading"<?php echo $heading_style; ?>><?php echo do_shortcode( $heading ); ?></<?php echo $heading_type; ?>>

				<?php if ( isset( $a_href ) && $a_href ) echo '</a>'; ?>

			<?php
			}
			// Text
			if ( $content ) {

				$text_style = vcex_inline_style( array(
					'font_size'     => $content_font_size,
					'color'         => $content_color,
					'font_weight'   => $content_font_weight
				) ); ?>

				<div class="vcex-feature-box-text clr"<?php echo $text_style; ?>>
					<?php echo apply_filters(  'the_content', $content ); ?>
				</div><!-- .vcex-feature-box-text -->

			<?php } ?>

			<?php if ( $content_padding ) { ?>
				</div><!-- .vcex-feature-box-padding-container -->
			<?php } ?>

		</div><!-- .vcex-feature-box-content -->

	<?php } ?>

</div><!-- .vcex-feature -->