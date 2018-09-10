<?php
/**
 * Visual Composer Recent News
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

// Deprecated Attributes
$term_slug = isset( $atts['term_slug'] ) ? $atts['term_slug'] : '';

// Get and extract shortcode attributes
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

// Define non-vc attributes
$atts['tax_query']  = '';
$atts['taxonomies'] = 'category';

// Extract shortcode atts
extract( $atts );

// IMPORTANT: Fallback required from VC update when params are defined as empty
// AKA - set things to enabled by default
$title     = ( ! $title ) ? 'true' : $title;
$date      = ( ! $date ) ? 'true' : $date;
$excerpt   = ( ! $excerpt ) ? 'true' : $excerpt;
$read_more = ( ! $read_more ) ? 'true' : $read_more;

// Fallback for term slug
if ( ! empty( $term_slug ) && empty( $include_categories ) ) {
	$include_categories = $term_slug;
}

// Custom taxonomy only for standard posts
if ( 'custom_post_types' == $get_posts ) {
	$atts['include_categories'] = $atts['exclude_categories'] = '';
}

// Get Standard posts
if ( 'standard_post_types' == $get_posts ) {
	$atts['post_types'] = 'post';
}

// Build the WordPress query
$wpex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $wpex_query->have_posts() ) :

	// Sanitize data + declare vars
	$inline_js = array();
	$grid_columns = $grid_columns ? $grid_columns : '1';
	
	// Wrapper Classes
	$wrap_classes = 'vcex-recent-news clr';
	if ( $classes ) {
		$wrap_classes .= $this->getExtraClass( $classes );
	}
	if ( $visibility ) {
		$wrap_classes .= ' '. $visibility;
	}
	if ( '1' != $grid_columns ) {
		$wrap_classes .= ' wpex-row';
	}
	if ( $css ) {
		$wrap_classes .= ' '. vc_shortcode_custom_css_class( $css );
	}

	// Entry Classes
	$entry_classes = array( 'vcex-recent-news-entry', 'clr' );
	if ( 'true' != $date ) {
		$entry_classes[] = 'no-left-padding';
	}
	if ( $css_animation ) {
		$entry_classes[] = $this->getCSSAnimation( $css_animation );
	}

	// Entry Style
	$entry_style = vcex_inline_style( array(
		'border_color' => $entry_bottom_border_color
	) );

	// Heading style
	if ( 'true' == $title ) {
		$heading_style = vcex_inline_style( array(
			'font_size'      => $title_size,
			'font_weight'    => $title_weight,
			'text_transform' => $title_transform,
			'line_height'    => $title_line_height,
			'margin'         => $title_margin,
			'color'          => $title_color,
		) );
	}

	// Excerpt style
	if ( 'true' == $excerpt ) {
		$excerpt_style = vcex_inline_style( array(
			'font_size' => $excerpt_font_size,
			'color' => $excerpt_color,
		) );
	}

	// Month Style
	if ( 'true' == $date ) {
		$month_style = vcex_inline_style( array(
			'background_color' => $month_background,
			'color' => $month_color,
		) );
	}

	// Readmore design and classes
	if ( 'true' == $read_more ) {

		// Readmore text
		$read_more_text = $read_more_text ? $read_more_text : esc_html__( 'read more', 'total' );

		// Readmore classes
		$readmore_classes = wpex_get_button_classes( $readmore_style, $readmore_style_color );
		if ( $readmore_hover_color || $readmore_hover_background ) {
			$readmore_classes .= ' wpex-data-hover';
		}

		// Read more style
		$readmore_border_color  = ( 'outline' == $readmore_style ) ? $readmore_color : '';
		$readmore_style = vcex_inline_style( array(
			'background' => $readmore_background,
			'color' => $readmore_color,
			'border_color' => $readmore_border_color,
			'font_size' => $readmore_size,
			'padding' => $readmore_padding,
			'border_radius' => $readmore_border_radius,
			'margin' => $readmore_margin,
		) );

		// Readmore data
		$readmore_data = '';
		if ( $readmore_hover_color ) {
			$readmore_data .= ' data-hover-color="'. $readmore_hover_color .'"';
		}
		if ( $readmore_hover_background ) {
			$readmore_data .= ' data-hover-background="'. $readmore_hover_background .'"';
		}
	}

	// Hover js
	if ( $readmore_hover_color || $readmore_hover_background ) {
		 $inline_js[] = 'data_hover';
	}

	// Load inline js
	if ( ! empty( $inline_js ) ) {
		vcex_inline_js( $inline_js );
	} ?>
	
	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?>>
	
		<?php
		// Display header if enabled
		if ( $header ) :
			wpex_heading( array(
				'content' => $header,
				'tag'     => 'h2',
				'classes' => array( 'vcex-recent-news-header' ),
			) );
		endif; ?>

		<?php
		// Loop through posts
		$count = '0';
		while ( $wpex_query->have_posts() ) :

			// Get post from query
			$wpex_query->the_post();

			// Add to counter
			$count++;

			// Create new post object.
			$post = new stdClass();
		
			// Post VARS
			$post->ID = get_the_ID();
			$post->permalink = wpex_get_permalink( $post->ID );
			$post->the_title = get_the_title( $post->ID );
			$post->the_title_esc = esc_attr( the_title_attribute( 'echo=0' ) );
			$post->type = get_post_type( $post->ID );
			$post->video_embed = wpex_get_post_video_html();
			$post->format = get_post_format( $post->ID ); ?>

			<?php if ( $grid_columns > '1' ) : ?>
				<div class="col span_1_of_<?php echo $grid_columns; ?> vcex-recent-news-entry-wrap col-<?php echo $count; ?>">
			<?php endif; ?>

			<article <?php echo post_class( $entry_classes ); ?><?php echo $entry_style; ?>>

				<?php if ( 'true' == $date ) : ?>

					<div class="vcex-recent-news-date">

						<span class="day"><?php

						// Standard day display
						$day = get_the_time( 'd', $post->ID );

						// Filter day display for tribe events calendar plugin
						// @todo move to events config file
						if ( 'tribe_events' == $post->type && function_exists( 'tribe_get_start_date' ) ) {
							echo tribe_get_start_date( $post->ID, false, 'd' );
						}

						// Echo the day
						echo apply_filters( 'vcex_recent_news_day_output', $day ); ?></span><!-- .day -->

						<span class="month"<?php echo $month_style; ?>><?php

						// Standard month year display
						$month_year = '<span>'. get_the_time( 'M', $post->ID ) .'</span>';
						$month_year .= ' <span class="year">'. get_the_time( 'y', $post->ID ) .'</span>';

						// Filter month/year display for tribe events calendar plugin
						// @todo move to events config file
						if ( 'tribe_events' == $post->type && function_exists( 'tribe_get_start_date' ) ) {
							$month_year = '<span>'. tribe_get_start_date( $post->ID, false, 'M' ) .'</span>';
							$month_year .= ' <span class="year">'. tribe_get_start_date( $post->ID, false, 'y' ) .'</span>';
						}

						// Echo the month/year
						echo apply_filters( 'vcex_recent_news_month_year_output', $month_year ); ?></span><!-- .month -->

					</div><!-- .vcex-recent-news-date -->

				<?php endif; ?>

				<div class="vcex-news-entry-details clr">

					<?php if ( 'true' == $featured_image ) : ?>

						<?php if ( 'true' == $featured_video && $post->video_embed ) : ?>

							<div class="vcex-news-entry-video clr">
								<?php echo $post->video_embed; ?>
							</div><!-- .vcex-news-entry-video -->

						<?php elseif ( has_post_thumbnail( $post->ID ) ) : ?>

							<div class="vcex-news-entry-thumbnail clr">
								<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>">
									<?php
									// Display thumbnail
									wpex_post_thumbnail( array(
										'size'   => $img_size,
										'crop'   => $img_crop,
										'width'  => $img_width,
										'height' => $img_height,
										'alt'    => wpex_get_esc_title(),
									) ); ?>
								</a>
							</div><!-- .vcex-news-entry-thumbnail -->

						<?php endif; ?>

					<?php endif; ?>

					<?php if ( 'true' == $title ) : ?>

						<header class="vcex-recent-news-entry-title entry-title">
							<<?php echo $title_tag; ?> class="vcex-recent-news-entry-title-heading"<?php echo $heading_style; ?>>
								<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>"><?php the_title(); ?></a>
							</<?php echo $title_tag; ?>>
						</header><!-- .vcex-recent-news-entry-title -->

					<?php endif; ?>

					<?php if ( 'true' == $excerpt || 'true' == $read_more ) : ?>

						<div class="vcex-recent-news-entry-excerpt clr">

							<?php if ( 'true' == $excerpt ) : ?> 
								<div class="entry"<?php echo $excerpt_style; ?>>
									<?php
									// Output excerpt
									wpex_excerpt( array (
										'length' => $excerpt_length,
									) ); ?>
								</div><!-- .entry -->
							<?php endif; ?>

							<?php
							// Display readmore link
							if ( 'true' == $read_more ) : ?>

								<a href="<?php echo $post->permalink; ?>" title="<?php echo esc_attr( $read_more_text ); ?>" rel="bookmark" class="<?php echo $readmore_classes; ?>"<?php echo $readmore_style; ?><?php echo $readmore_data; ?>>
									<?php echo $read_more_text; ?>
									<?php if ( 'true' == $readmore_rarr ) { ?>
										<span class="vcex-readmore-rarr"><?php echo wpex_element( 'rarr' ); ?></span>
									<?php } ?>
								</a>

							<?php endif; ?>

						</div><!-- .vcex-recent-news-entry-excerpt -->

					<?php endif; ?>

				</div><!-- .vcex-recent-news-entry-details -->

			</article><!-- .vcex-recent-news-entry -->

			<?php if ( $grid_columns > '1' ) echo '</div>'; ?>

			<?php if ( $count == $grid_columns ) $count = ''; ?>

		<?php endwhile; ?>

		<?php
		// Display pagination
		if ( 'true' == $pagination ) : ?>
			<div class="wpex-clear"></div>
			<?php wpex_pagination( $wpex_query ); ?>
		<?php endif; ?>
	
	</div><!-- .vcex-recent-news -->

	<?php
	// Remove post object from memory
	$post = null;

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