<?php
/**
 * Visual Composer Carousel
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

// Define vars
$atts['post_type'] = 'post';
$atts['taxonomy']  = 'category';
$atts['tax_query'] = '';

// Extract attributes
extract( $atts );

// Build the WordPress query
$wpex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $wpex_query->have_posts() ) :

	// Inline JS for front-end composer
	$inline_js = array( 'carousel' );

	// Sanitize & declare variables
	$overlay_style = $overlay_style ? $overlay_style : 'none';

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$media   = ( ! $media ) ? 'true' : $media;
	$title   = ( ! $title ) ? 'true' : $title;
	$date    = ( ! $date ) ? 'true' : $date;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Main Classes
	$wrap_classes = array( 'wpex-carousel', 'wpex-carousel-blog', 'owl-carousel', 'clr' );
	if ( $style ) {
		$wrap_classes[] = $style;
	}
	if ( $css_animation ) {
		$wrap_classes[] = $this->getCSSAnimation( $css_animation );
	}
	if ( $classes ) {
		$wrap_classes[] = $this->getExtraClass( $classes );
	}
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
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

	// Content Design
	$content_style = vcex_inline_style( array(
		'background' => $content_background,
		'padding'    => $content_padding,
		'margin'     => $content_margin,
		'border'     => $content_border,
		'opacity'    => $content_opacity,
		'text_align' => $content_alignment,
	) );

	// Title design
	if ( 'true' == $title ) {
		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'font_size'      => $content_heading_size,
			'font_weight'    => $content_heading_weight,
			'text_transform' => $content_heading_transform,
			'line_height'    => $content_heading_line_height,
			'color'          => $content_heading_color,
		) );
	}

	// Date design
	if ( 'true' == $date ) {
		$date_style = vcex_inline_style( array(
			'color'     => $date_color,
			'font_size' => $date_font_size,
		) );
	}

	// Excerpt style
	if ( 'true' == $excerpt ) {
		$excerpt_styling = vcex_inline_style( array(
			'color'     => $content_color,
			'font_size' => $content_font_size,
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

	// Convert arrays to strings
	$wrap_classes = implode( ' ', $wrap_classes );

	// Inline js
	vcex_inline_js( $inline_js ); ?>

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?> data-items="<?php echo $items; ?>" data-slideby="<?php echo $items_scroll; ?>" data-nav="<?php echo $arrows; ?>" data-dots="<?php echo $dots; ?>" data-autoplay="<?php echo $auto_play; ?>" data-loop="<?php echo $infinite_loop; ?>" data-autoplay-timeout="<?php echo $timeout_duration ?>" data-center="<?php echo $center; ?>" data-margin="<?php echo intval( $items_margin ); ?>" data-items-tablet="<?php echo $tablet_items; ?>" data-items-mobile-landscape="<?php echo $mobile_landscape_items; ?>" data-items-mobile-portrait="<?php echo $mobile_portrait_items; ?>" data-smart-speed="<?php echo $animation_speed; ?>">
		<?php
		// Start loop
		$count = 0;
		while ( $wpex_query->have_posts() ) :
			$count++;

			// Get post from query
			$wpex_query->the_post();

			// Create new post object.
			$post = new stdClass();

			// Get post data
			$get_post = get_post();
		
			// Post VARS
			$post->ID              = $get_post->ID;
			$post->permalink       = wpex_get_permalink( $post->ID );
			$post->title           = get_the_title();
			$post->esc_title       = wpex_get_esc_title();
			$post->thumbnail       = get_post_thumbnail_id( $post->ID );
			$post->thumbnail_link  = $post->permalink;

			// Lets store the dynamic $post->ID into the shortcodes attributes
			$atts['post_id'] = $post->ID;

			// Only display carousel item if there is content to show
			if ( ( 'true' == $media && has_post_thumbnail() )
				|| 'true' == $title
				|| 'true' == $date
				|| 'true' == $excerpt
			) : ?>

				<div class="wpex-carousel-slide">
				
					<?php
					// Display thumbnail if enabled and defined
					if ( 'true' == $media && has_post_thumbnail() ) : ?>

						<div class="<?php echo $media_classes; ?>">

							<?php
							// If thumbnail link doesn't equal none
							if ( 'none' != $thumbnail_link ) :

								// Lightbox thumbnail
								if ( 'lightbox' == $thumbnail_link ) {
									$atts['lightbox_link'] = wpex_get_lightbox_image( $post->thumbnail );
									$post->thumbnail_link  = $atts['lightbox_link'];
								}

								// Link attributes
								$link_attrs = array(
									'href'  => esc_url( $post->thumbnail_link ),
									'title' => $post->esc_title,
									'class' => 'wpex-carousel-entry-img',
								);
								// Add lightbox link
								if ( 'lightbox' == $thumbnail_link ) {
									$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
									$link_attrs['data-title'] = $post->esc_title;
									$link_attrs['data-count'] = $count;
								} ?>

							<a <?php echo wpex_parse_attrs( $link_attrs ); ?>>

							<?php endif; ?>

							<?php
							// Display post thumbnail
							wpex_post_thumbnail( array(
								'width'  => $img_width,
								'height' => $img_height,
								'size'   => $img_size,
								'crop'   => $img_crop,
								'alt'    => $post->esc_title,
							) ); ?>

							<?php
							// Inner link overlay html
							wpex_overlay( 'inside_link', $overlay_style, $atts ); ?>

							<?php
							// Close link tag
							if ( 'none' != $thumbnail_link ) echo '</a>'; ?>

							<?php
							// Outer link overlay HTML
							wpex_overlay( 'outside_link', $overlay_style, $atts ); ?>

						</div><!-- .wpex-carousel-entry-media -->

					<?php endif; ?>

					<?php
					// Open details element if the title or excerpt are true
					if ( 'true' == $title
						|| 'true' == $date
						|| 'true' == $excerpt
					) : ?>

						<div class="wpex-carousel-entry-details clr"<?php echo $content_style; ?>>

							<?php
							// Display title if $title is true and there is a post title
							if ( 'true' == $title ) : ?>

								<div class="wpex-carousel-entry-title entry-title"<?php echo $heading_style; ?>>
									<a href="<?php echo $post->permalink; ?>" title="<?php echo $post->esc_title; ?>"><?php echo $post->title; ?></a>
								</div>

							<?php endif; ?>

							<?php
							// Display publish date if $date is enabled
							if ( 'true' == $date ) : ?>

								<div class="vcex-blog-entry-date"<?php echo $date_style; ?>>
									<?php echo get_the_date(); ?>
								</div><!-- .vcex-blog-entry-date -->

							<?php endif; ?>

							<?php
							// Display excerpt if $excerpt is true
							if ( 'true' == $excerpt ) : ?>

								<div class="wpex-carousel-entry-excerpt clr"<?php echo $excerpt_styling; ?>>
									<?php wpex_excerpt( $excerpt_length ); ?>
								</div><!-- .wpex-carousel-entry-excerpt -->
								
							<?php endif ?>

						</div><!-- .wpex-carousel-entry-details -->

					<?php endif; ?>

				</div><!-- .wpex-carousel-slide -->

			<?php endif; ?>

		<?php
		// End entry loop
		endwhile; ?>

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