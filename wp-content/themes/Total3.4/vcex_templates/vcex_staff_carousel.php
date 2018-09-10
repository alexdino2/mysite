<?php
/**
 * Visual Composer Staff Carousel
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.4.0
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
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Get and extract shortcode attributes
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

// Extract shortcode atts
extract( $atts );

// Build the WordPress query
$atts['post_type'] = 'staff';
$atts['tax_query'] = '';
$wpex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $wpex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$title   = ( ! $title ) ? 'true' : $title;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Load scripts
	$inline_js = array( 'carousel' );

	// Prevent auto play in visual composer
	if ( vc_is_inline() ) {
		$auto_play = 'false';
	}

	// Items to scroll fallback for old setting
	if ( 'page' == $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes
	$wrap_classes = array( 'wpex-carousel', 'wpex-carousel-staff', 'clr', 'owl-carousel' );
	if ( $style ) {
		$wrap_classes[] = $style;
	}
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = $this->getExtraClass( $classes );
	}

	// Entry media classes
	if ( 'true' == $media ) {
		$media_classes = array( 'wpex-carousel-entry-media', 'clr' );
		if ( $img_hover_style ) {
			$media_classes[] = wpex_image_hover_classes( $img_hover_style );
		}
		if ( $img_filter ) {
			$media_classes[] = wpex_image_filter_class( $img_filter );
		}
		if ( $overlay_style ) {
			$media_classes[] = wpex_overlay_classes( $overlay_style );
		}
		if ( 'lightbox' == $thumbnail_link ) {
			$inline_js[] = 'carousel_lightbox';
			$wrap_classes[] = 'wpex-carousel-lightbox';
			vcex_enque_style( 'ilightbox' );
		}
		$media_classes = implode( ' ', $media_classes );
	}

	// Position design
	if ( 'true' == $position ) {
		$position_style = vcex_inline_style( array(
			'font_size'   => $position_size,
			'font_weight' => $position_weight,
			'margin'      => $position_margin,
			'color'       => $position_color,
		) );
	}

	// Content Design
	$content_style = vcex_inline_style( array(
		'background' => $content_background,
		'padding'    => $content_padding,
		'margin'     => $content_margin,
		'border'     => $content_border,
		'font_size'  => $content_font_size,
		'color'      => $content_color,
		'opacity'    => $content_opacity,
		'text_align' => $content_alignment,

	) );

	// Social links style
	if ( 'true' == $social_links ) {
		$social_links_inline_css = vcex_inline_style( array(
			'margin' => $social_links_margin,
		) );
	}

	// Title design
	if ( 'true' == $title ) {
		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'text_transform' => $content_heading_transform,
			'font_weight'    => $content_heading_weight,
			'font_size'      => $content_heading_size,
			'line_height'    => $content_heading_line_height,
		) );
		$heading_link_style = vcex_inline_style( array(
			'color' => $content_heading_color,
		) );
	}

	// Sanitize carousel data
	$arrows                 = wpex_esc_attr( $arrows, 'true' );
	$dots                   = wpex_esc_attr( $dots, 'false' );
	$auto_play              = wpex_esc_attr( $auto_play, 'false' );
	$infinite_loop          = wpex_esc_attr( $infinite_loop, 'true' );
	$center                 = wpex_esc_attr( $center, 'false' );
	$items                  = wpex_intval( $items, 4 );
	$items_scroll           = wpex_intval( $items_scroll, 1 );
	$timeout_duration       = wpex_intval( $timeout_duration, 5000 );
	$items_margin           = wpex_intval( $items_margin, 15 );
	$items_margin           = ( 'no-margins' == $style ) ? 0 : $items_margin;
	$tablet_items           = wpex_intval( $tablet_items, 3 );
	$mobile_landscape_items = wpex_intval( $mobile_landscape_items, 2 );
	$mobile_portrait_items  = wpex_intval( $mobile_portrait_items, 1 );
	$animation_speed        = wpex_intval( $animation_speed, 150 );

	// Disable autoplay
	if ( vc_is_inline() || '1' == count( $wpex_query->posts ) ) {
		$auto_play = 'false';
	}

	// Turn array to strings
	$wrap_classes = implode( ' ', $wrap_classes );

	// Add inline js
	vcex_inline_js( $inline_js ); ?>

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?> data-items="<?php echo $items; ?>" data-slideby="<?php echo $items_scroll; ?>" data-nav="<?php echo $arrows; ?>" data-dots="<?php echo $dots; ?>" data-autoplay="<?php echo $auto_play; ?>" data-loop="<?php echo $infinite_loop; ?>" data-autoplay-timeout="<?php echo $timeout_duration ?>" data-center="<?php echo $center; ?>" data-margin="<?php echo intval( $items_margin ); ?>" data-items-tablet="<?php echo $tablet_items; ?>" data-items-mobile-landscape="<?php echo $mobile_landscape_items; ?>" data-items-mobile-portrait="<?php echo $mobile_portrait_items; ?>" data-smart-speed="<?php echo $animation_speed; ?>">

		<?php
		// Loop through posts
		$loop_count = 0;
		while ( $wpex_query->have_posts() ) :
			$loop_count ++;

			// Get post from query
			$wpex_query->the_post();

			// Create new post object
			$post = new stdClass();
		
			// Post VARS
			$post->ID        = get_the_ID();
			$post->permalink = wpex_get_permalink( $post->ID );
			$post->esc_title = wpex_get_esc_title(); ?>

			<div class="wpex-carousel-slide wpex-clr">

				<?php
				// Media Wrap
				if ( has_post_thumbnail() ) :

					// Generate featured image
					$thumbnail = wpex_get_post_thumbnail( array(
						'size'   => $img_size,
						'crop'   => $img_crop,
						'width'  => $img_width,
						'height' => $img_height,
						'alt'    => wpex_get_esc_title(),
					) ); ?>

					<div class="<?php echo $media_classes; ?>">
						<?php
						// No links
						if ( in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) { ?>
							<?php echo $thumbnail; ?>
						<?php }
						// Lightbox
						elseif ( 'lightbox' == $thumbnail_link ) { ?>
							<a href="<?php wpex_lightbox_image(); ?>" title="<?php echo $post->esc_title; ?>" data-title="<?php echo $post->esc_title; ?>" data-count="<?php echo $loop_count; ?>" class="wpex-carousel-entry-img wpex-carousel-lightbox-item">
								<?php echo $thumbnail; ?>
						<?php }
						// Link to post
						else { ?>
							<a href="<?php echo $post->permalink; ?>" title="<?php echo $post->esc_title; ?>" class="wpex-carousel-entry-img">
								<?php echo $thumbnail; ?>
						<?php } ?>
						<?php
						// Overlay & close link
						if ( ! in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) {
							// Inner Overlay
							if ( $overlay_style ) {
								wpex_overlay( 'inside_link', $overlay_style, $atts );
							}
							// Close link
							echo '</a><!-- .wpex-carousel-entry-img -->';
							// Outside Overlay
							if ( $overlay_style ) {
								wpex_overlay( 'outside_link', $overlay_style, $atts );
							}
						} ?>
					</div><!-- .wpex-carousel-entry-media -->

				<?php endif; ?>

				<?php
				// Title
				if ( 'true' == $title
					|| 'true' == $position
					|| 'true' == $excerpt
					|| 'true' == $social_links
				) : ?>

					<div class="wpex-carousel-entry-details clr"<?php echo $content_style; ?>>

						<?php
						// Title
						if ( 'true' == $title ) : ?>

							<div class="wpex-carousel-entry-title entry-title"<?php echo $heading_style; ?>>
								<a href="<?php echo $post->permalink; ?>" title="<?php echo $post->esc_title; ?>"<?php echo $heading_link_style; ?>><?php the_title(); ?></a>
							</div><!-- .wpex-carousel-entry-title -->

						<?php endif; ?>

						<?php
						// Display staff member position
						if ( 'true' == $position
							&& $get_position = get_post_meta( $post->ID, 'wpex_staff_position', true )
						) : ?>

							<div class="staff-entry-position" <?php echo $position_style; ?>>
								<?php echo apply_filters( 'wpex_staff_entry_position', esc_html( $get_position ) ); ?>
							</div><!-- .staff-entry-position -->

						<?php endif; ?>

						<?php
						// Check if the excerpt is enabled
						if ( 'true' == $excerpt ) : ?>

							<?php
							// Generate excerpt
							$post->excerpt = wpex_get_excerpt( array (
								'length' => intval( $excerpt_length ),
							) );

							// Display excerpt if there is one
							if ( $post->excerpt ) : ?>

								<div class="wpex-carousel-entry-excerpt clr">
									<?php echo $post->excerpt; ?>
								</div><!-- .wpex-carousel-entry-excerpt -->

							<?php endif; ?>

						<?php endif; ?>

						<?php
						// Check if social is enabled
						if ( 'true' == $social_links ) : ?>
							<?php echo wpex_get_staff_social( array(
								'style'     => $social_links_style,
								'font_size' => $social_links_size,
							) ); ?>
						<?php endif; ?>

					</div><!-- .wpex-carousel-entry-details -->

				<?php endif; ?>

			</div><!-- .wpex-carousel-slide -->

		<?php endwhile; ?>

	</div><!-- .wpex-carousel -->

	<?php
	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata(); ?>

<?php
// If no posts are found display message
else : ?>

	<?php
	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts ); ?>

<?php
// End post check
endif; ?>