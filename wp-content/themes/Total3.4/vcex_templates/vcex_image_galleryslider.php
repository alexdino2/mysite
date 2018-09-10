<?php
/**
 * Visual Composer Image Gallery
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.0.0
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

// Set image ids
$image_ids = ( 'true' == $post_gallery ) ? wpex_get_gallery_ids() : $image_ids;

// If there aren't any images lets display a notice
if ( empty( $image_ids ) ) {
	return;
}

// Otherwise if there are images lets turn it into an array
else {

	// Get image ID's
	if ( ! is_array( $image_ids ) ) {
		$attachments = explode( ',', $image_ids );
	} else {
		$attachments = $image_ids;
	}

}

// Turn links into array
if ( $custom_links && 'custom_link' == $thumbnail_link ) {

	// Remove duplicate images
	$attachments = array_unique( $attachments );

	// Turn links into array
	if ( $custom_links ) {
		$custom_links = explode( ',', $custom_links );
	} else {
		$custom_links = array();
	}

	// Count items
	$attachments_count = count( $attachments );
	$custom_links_count   = count( $custom_links );

	// Add empty values to custom_links array for images without links
	if ( $attachments_count > $custom_links_count ) {
		$count = 0;
		foreach( $attachments as $val ) {
			$count++;
			if ( ! isset( $custom_links[$count] ) ) {
				$custom_links[$count] = '#';
			}
		}
	}

	// New custom links count
	$custom_links_count = count( $custom_links );

	// Remove extra custom links
	if ( $custom_links_count > $attachments_count ) {
		$count = 0;
		foreach( $custom_links as $key => $val ) {
			$count ++;
			if ( $count > $attachments_count ) {
				unset( $custom_links[$key] );
			}
		}
	}

	// Set links as the keys for the images
	$attachments = array_combine( $attachments, $custom_links );

} else {

	$attachments = array_combine( $attachments, $attachments );
	
};

// Output gallery
if ( $attachments ) :

	// Load scripts
	$inline_js = array( 'slider_pro' );
	if ( 'lightbox' == $thumbnail_link ) {
		vcex_enque_style( 'ilightbox', $lightbox_skin );
		$inline_js[] = 'ilightbox';
	}
	vcex_inline_js( $inline_js );

	// Sanitize data and declare main vars
	$wrap_attributes    = array();
	$caption_attributes = array();
	$slideshow          = vc_is_inline() ? 'false' : $slideshow;
	$thumbnails_columns = $thumbnails_columns ? $thumbnails_columns : '5';

	// Slider attributes
	$wrap_attributes[] = 'data-thumbnails="true"';
	$wrap_attributes[] = 'data-thumbnail-height="auto"';
	if ( in_array( $animation, array( 'fade', 'fade_slides' ) ) ) {
		$wrap_attributes[] = 'data-fade="true"';
	}
	if ( 'true' == $randomize ) {
		$wrap_attributes[] = 'data-shuffle="true"';
	}
	if ( 'true' == $loop ) {
		$wrap_attributes[] = 'data-loop="true"';
	}
	if ( 'false' == $slideshow ) {
		$wrap_attributes[] = 'data-auto-play="false"';
	}
	if ( $slideshow && $slideshow_speed ) {
		$wrap_attributes[] = 'data-auto-play-delay="'. $slideshow_speed .'"';
	}
	if ( 'false' == $direction_nav ) {
		$wrap_attributes[] = 'data-arrows="false"';
	}
	if ( 'false' == $control_nav ) {
		$wrap_attributes[] = 'data-buttons="false"';
	}
	if ( 'false' == $direction_nav_hover ) {
		$wrap_attributes[] = 'data-fade-arrows="false"';
	}
	if ( $animation_speed ) {
		$wrap_attributes[] = 'data-animation-speed="'. intval( $animation_speed ) .'"';
	}
	if ( $height_animation ) {
		$height_animation = intval( $height_animation );
		$height_animation = 0 == $height_animation ? '0.0' : $height_animation;
		$wrap_data[] = 'data-height-animation-duration="'. $height_animation .'"';
	}

	// Caption attributes
	if ( 'true' == $caption ) {

		// Caption attributes
		if ( $caption_position ) {
			$caption_attributes[] = ' data-position="'. $caption_position .'"';
		}
		if ( $caption_show_transition ) {
			$caption_attributes[] = ' data-show-transition="'. $caption_show_transition .'"';
		}
		if ( $caption_hide_transition ) {
			$caption_attributes[] = ' data-hide-transition="'. $caption_hide_transition .'"';
		}
		if ( $caption_width ) {
			$caption_attributes[] = ' data-width="'. wpex_sanitize_data( $caption_width, 'px-pct' ) .'"';
		}
		if ( $caption_horizontal ) {
			$caption_attributes[] = ' data-horizontal="'. intval( $caption_horizontal ) .'"';
		}
		if ( $caption_vertical ) {
			$caption_attributes[] = ' data-vertical="'. intval( $caption_vertical ) .'"';
		}
		if ( $caption_delay ) {
			$caption_attributes[] = ' data-show-delay="'. intval( $caption_delay ) .'"';
		}
		if ( empty( $caption_show_transition ) && empty( $caption_hide_transition ) ) {
			$caption_attributes[] = ' data-sp-static="false"';
		}
		$caption_attributes = implode( ' ', $caption_attributes );

		// Caption classes
		$caption_classes = array( 'wpex-slider-caption', 'sp-layer', 'sp-padding', 'clr' );
		if ( $caption_visibility ) {
			$caption_classes[] = $caption_visibility;
		}
		if ( $caption_style ) {
			$caption_classes[] = 'sp-'. $caption_style;
		}
		if ( 'true' == $caption_rounded ) {
			$caption_classes[] = 'sp-rounded';
		}
		$caption_classes = implode( ' ', $caption_classes );

		// Caption style
		$caption_inline_style = vcex_inline_style( array(
			'font_size' => $caption_font_size,
			'padding'   => $caption_padding,
		) );

	}

	// Main Classes
	$wrap_classes = array( 'wpex-slider', 'slider-pro', 'no-margin-thumbnails', 'vcex-image-gallery-slider', 'clr' );
	if ( $classes ) {
		$wrap_classes[] = $this->getExtraClass( $classes );
	}
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( 'lightbox' == $thumbnail_link ) {
		$wrap_classes[] = ' lightbox-group';
		if ( $lightbox_skin ) {
			$wrap_attributes[] = 'data-skin="'. $lightbox_skin .'"';
			vcex_enque_style( 'ilightbox', $lightbox_skin );
		}
		if ( $lightbox_path ) {
			$wrap_attributes[] = 'data-path="'. $lightbox_path .'"';
		}
	}

	// Convert arrays to strings
	$wrap_classes       = implode( ' ', $wrap_classes );
	$wrap_attributes    = implode( ' ', $wrap_attributes ); ?>

	<?php
	// Open css wrapper
	if ( $css ) : ?>
		<div class="vcex-image-gallery-slider-css-wrap <?php echo vc_shortcode_custom_css_class( $css ); ?>">
	<?php endif; ?>

	<div class="wpex-slider-preloaderimg">
		<?php wpex_post_thumbnail( array(
			'attachment' => reset( $attachments ),
			'size'       => $img_size,
			'crop'       => $img_crop,
			'width'      => $img_width,
			'height'     => $img_height,
		) ); ?>
	</div><!-- .wpex-slider-preloaderimg -->

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_attributes; ?>>
		<div class="wpex-slider-slides sp-slides">

			<?php
			// Loop through attachments
			foreach ( $attachments as $attachment => $custom_link ) :
			
				// Attachment VARS
				$custom_link      = ( '#' != $custom_link ) ? $custom_link : '';
				$attachment_link  = get_post_meta( $attachment, '_wp_attachment_url', true );
				$attachment_data  = wpex_get_attachment_data( $attachment );
				$caption_type     = $caption_type ? $caption_type : 'caption';
				$caption_output   = $attachment_data[$caption_type];
				$attachment_video = wp_oembed_get( $attachment_data['video'] );
				$attachment_video = wpex_add_sp_video_to_oembed( $attachment_video );

				// Get and crop image if needed
				$attachment_img = wpex_get_post_thumbnail( array(
					'attachment' => $attachment,
					'size'       => $img_size,
					'crop'       => $img_crop,
					'width'      => $img_width,
					'height'     => $img_height,
					'alt'        => $attachment_data['alt'],
				) ); ?>

				<div class="wpex-slider-slide sp-slide">

					<div class="wpex-slider-media">

						<?php if ( $attachment_video ) : ?>

							<div class="wpex-slider-video responsive-video-wrap">
								<?php echo $attachment_video; ?>
							</div><!-- .wpex-slider-video -->

						<?php else : ?>

							<?php if ( 'lightbox' == $thumbnail_link ) { ?>

								<a href="<?php wpex_lightbox_image( $attachment ); ?>" title="<?php echo esc_attr( $attachment_data['alt' ] ); ?>" class="vcex-galleryslider-entry-img wpex-lightbox-group-item">
									<?php echo $attachment_img; ?>
								</a>

						   <?php } elseif ( 'custom_link' == $thumbnail_link ) { ?>

								<?php if ( $custom_link ) { ?>

										<a href="<?php echo esc_url( $custom_link ); ?>"<?php echo vcex_html( 'title_attr', $attachment_data['title'] ); ?><?php echo vcex_html( 'target_attr', $custom_links_target ); ?>>
											<?php echo $attachment_img; ?>
										</a>

									<?php } else { ?>

									   <?php echo $attachment_img; ?>

									<?php } ?>

							<?php } else { ?>

								<?php echo $attachment_img; ?>

							<?php } ?>

						<?php endif; ?>

						<?php if ( empty( $attachment_video ) && 'true' == $caption && $caption_output ) : ?>

							<div class="<?php echo $caption_classes; ?>"<?php echo $caption_attributes; ?><?php echo $caption_inline_style; ?>>

								<?php if ( in_array( $caption_type, array( 'description', 'caption' ) ) ) : ?>
									<?php echo wpautop( $caption_output ); ?>
								<?php else : ?>
									<?php echo $caption_output; ?>
								<?php endif; ?>

							</div><!-- .wpex-slider-caption -->

						<?php endif; ?>

					</div><!-- .wpex-slider-media -->

				</div><!-- .wpex-slider-slide -->

			<?php endforeach; ?>
			
		</div><!-- .wpex-slider-slides -->

		<div class="wpex-slider-thumbnails sp-thumbnails cols-<?php echo $thumbnails_columns; ?>">

			<?php foreach ( $attachments as $attachment => $custom_link ) : ?>

				<?php
				// Output thumbnail image
				wpex_post_thumbnail( array(
					'attachment' => $attachment,
					'size'       => 'wpex_custom',
					'width'      => $img_thumb_width,
					'height'     => $img_thumb_height,
					'class'      => 'wpex-slider-thumbnail sp-thumbnail',
				) ); ?>

			<?php endforeach; ?>

		</div><!-- .wpex-slider-thumbnails -->

	</div><!-- .wpex-slider -->

	<?php
	// Close css wrapper
	if ( $css ) echo '</div>'; ?>

<?php endif; ?>