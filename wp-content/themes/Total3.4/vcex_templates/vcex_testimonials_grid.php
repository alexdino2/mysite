<?php
/**
 * Visual Composer Testimonials Grid
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.4
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
extract( $atts );

// Define user-generated attributes
$atts['post_type'] = 'testimonials';
$atts['taxonomy']  = 'testimonials_category';
$atts['tax_query'] = '';

// Build the WordPress query
$wpex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $wpex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$entry_media = ( ! $entry_media ) ? 'true' : $entry_media;
	$title       = ( ! $title ) ? 'true' : $title;
	$excerpt     = ( ! $excerpt ) ? 'true' : $excerpt;
	$read_more   = ( ! $read_more ) ? 'true' : $read_more;

	// Declare and sanitize vars
	$inline_js     = array();
	$wrap_classes  = array( 'vcex-testimonials-grid-wrap', 'clr' );
	$grid_classes  = array( 'wpex-row', 'vcex-testimonials-grid', 'clr' );
	$grid_data     = array();
	$css_animation = $this->getCSSAnimation( $css_animation );
	$css_animation = ( 'true' == $filter ) ? false : $css_animation;
	$title_tag     = $title_tag ? $title_tag : 'div';

	// Is Isotope var
	if ( 'true' == $filter || 'masonry' == $grid_style ) {
		$is_isotope = true;
	} else {
		$is_isotope = false;
	}

	// No need for masonry if not enough columns and filter is disabled
	if ( 'true' != $filter && 'masonry' == $grid_style ) {
		$post_count = count( $wpex_query->posts );
		if ( $post_count <= $columns ) {
			$is_isotope = false;
		}
	}

	// Get filter taxonomy
	if ( 'true' == $filter ) {
		$filter_taxonomy = apply_filters( 'vcex_filter_taxonomy', $atts['taxonomy'], $atts );
		$filter_taxonomy = taxonomy_exists( $filter_taxonomy ) ? $filter_taxonomy : '';
		if ( $filter_taxonomy ) {
			$atts['filter_taxonomy'] = $filter_taxonomy; // Add to array to pass on to vcex_grid_filter_args()
		}
	} else {
		$filter_taxonomy = null;
	}

	// Get filter categories
	if ( $filter_taxonomy ) {

		// Get filter terms
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $wpex_query ) );

		// Make sure we have terms before doing things
		if ( $filter_terms ) {

			// Get term ids
			$filter_terms_ids = wp_list_pluck( $filter_terms, 'term_id' );

			// Check url for filter cat
			$filter_url_param = vcex_grid_filter_url_param();
			if ( isset( $_GET[$filter_url_param] ) ) {
				$filter_active_category = $_GET[$filter_url_param];
				if ( ! is_numeric( $filter_active_category ) ) {
					$get_term = get_term_by( 'name', $filter_active_category, $filter_taxonomy );
					if ( $get_term ) {
						$filter_active_category = $get_term->term_id;
					}
				}
			}

			// Check if filter active cat exists on current page
			$filter_has_active_cat = in_array( $filter_active_category, $filter_terms_ids ) ? true : false;

			// Add show on load animation when active filter is enabled to prevent double animation
			if ( $filter_has_active_cat ) {
				$grid_classes[] = 'wpex-show-on-load';
			}

		} else {

			$filter = false; // No terms so we can't have a filter

		}

	}

	// Output script for inline JS for the Visual composer front-end builder
	if ( $is_isotope ) {
		$inline_js[] = 'isotope';
	}

	// Image Style
	$img_style = vcex_inline_style( array(
		'border_radius' => $img_border_radius,
	), false );

	// Image classes
	$img_classes = '';
	if ( $img_width || $img_height || 'wpex_custom' != $img_size ) {
		$img_classes = 'remove-dims';
	}

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid Classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-'. $columns_gap;
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}

	// Data
	if ( $is_isotope && 'true' == $filter ) {
		if ( 'no_margins' != $grid_style && $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="'. $masonry_layout_mode .'"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="'. $filter_speed .'"';
		}
		if ( ! empty( $filter_has_active_cat ) ) {
			$grid_data[] = 'data-filter=".cat-'. $filter_active_category .'"';
		}
	}

	// Load inline js
	if ( $inline_js ) {
		vcex_inline_js( $inline_js );
	}

	// Load Google fonts if needed
	if ( $title_font_family ) {
		wpex_enqueue_google_font( $title_font_family );
	}

	// Title style
	$title_style = '';
	if ( 'true' == $title ) {
		$title_style = vcex_inline_style( array(
			'font_size'     => $title_font_size,
			'font_family'   => $title_font_family,
			'color'         => $title_color,
			'margin_bottom' => $title_bottom_margin,
		) );
	}

	// Excerpt style
	$content_style = vcex_inline_style( array(
		'font_size' => $content_font_size,
		'color'     => $content_color,
	) );

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_testimonials_grid_wrap_classes', $wrap_classes );
	$grid_classes  = apply_filters( 'vcex_testimonials_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_testimonials_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : ''; ?>

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?>>

		<?php
		// Display filter links
		if ( ! empty( $filter_terms ) ) {

			// Sanitize vars
			$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

			// Filter button classes
			$filter_button_classes = wpex_get_button_classes( $filter_button_style, $filter_button_color );

			// Filter font size
			$filter_style = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) ); ?>

			<ul class="vcex-testimonials-filter vcex-filter-links clr<?php if ( 'yes' == $center_filter ) echo ' center'; ?>"<?php echo $filter_style; ?>>

				<?php if ( 'true' == $filter_all_link ) { ?>

					<li <?php if ( ! $filter_has_active_cat ) echo 'class="active"'; ?>><a href="#" data-filter="*" class="<?php echo $filter_button_classes; ?>"><span><?php echo $all_text; ?></span></a></li>

				<?php } ?>

				<?php foreach ( $filter_terms as $term ) : ?>

					<li class="filter-cat-<?php echo $term->term_id; ?><?php if ( $filter_active_category == $term->term_id ) echo ' active'; ?>"><a href="#" data-filter=".cat-<?php echo $term->term_id; ?>" class="<?php echo $filter_button_classes; ?>"><?php echo $term->name; ?></a></li>

				<?php endforeach; ?>
				
			</ul><!-- .vcex-testimonials-filter -->

		<?php } ?>

		<div class="<?php echo $grid_classes; ?>"<?php echo $grid_data; ?>>

			<?php
			// Define counter var to clear floats
			$count = 0;

			// Start loop
			while ( $wpex_query->have_posts() ) :

				// Get post from query
				$wpex_query->the_post();

				// Add to the counter var
				$count++;

				// Create new post object
				$testimonial = new stdClass();

				// Get post data
				$testimonial->ID      = get_the_ID();
				$testimonial->author  = get_post_meta( get_the_ID(), 'wpex_testimonial_author', true );
				$testimonial->company = get_post_meta( get_the_ID(), 'wpex_testimonial_company', true );
				$testimonial->url     = get_post_meta( get_the_ID(), 'wpex_testimonial_url', true );

				// Add classes to the entries
				$entry_classes = array( 'testimonial-entry' );
				$entry_classes[] = 'span_1_of_'. $columns;
				$entry_classes[] = 'col-'. $count;
				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}
				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}
				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}
				if ( $filter_taxonomy ) {
					if ( $post_terms = get_the_terms( $testimonial->ID, 'testimonials_category' ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = 'cat-'. $post_term->term_id;
						}
					}
				} ?>

				<div <?php post_class( $entry_classes ); ?>>

					<div class="testimonial-entry-content clr">

						<span class="testimonial-caret"></span>

						<?php
						// Display title
						if ( 'true' == $title ) : ?>

							<<?php echo $title_tag; ?> class="testimonial-entry-title entry-title"<?php echo $title_style; ?>><?php the_title(); ?></<?php echo $title_tag; ?>>

						<?php endif; ?>

						<div class="testimonial-entry-details clr"<?php echo $content_style; ?>>

							<?php
							// Display excerpt if enabled (default dispays full content )
							if ( 'true' == $excerpt ) :

								// Custom readmore text
								if ( 'true' == $read_more ) :

									// Add arrow
									if ( 'false' != $read_more_rarr ) {
										$read_more_rarr_html = '<span>&rarr;</span>';
									} else {
										$read_more_rarr_html = '';
									}

									// Read more text
									if ( is_rtl() ) {
										$read_more_link = '...<a href="'. wpex_get_permalink() .'" title="'. esc_attr( $read_more_text ) .'">'. $read_more_text .'</a>';
									} else {
										$read_more_link = '...<a href="'. wpex_get_permalink() .'" title="'. esc_attr( $read_more_text ) .'">'. $read_more_text . $read_more_rarr_html .'</a>';
									}

								else :

									$read_more_link = '...';

								endif;

								// Custom Excerpt function
								wpex_excerpt( array(
									'post_id' => $testimonial->ID,
									'length'  => intval( $excerpt_length ),
									'more'    => $read_more_link,
								) );

							// Display full post content
							else :

								the_content();
							
							endif; ?>

						</div><!-- .entry -->

					</div><!-- .home-testimonial-entry-content-->

					<div class="testimonial-entry-bottom">

						<?php
						// Check if post thumbnail is defined
						if ( has_post_thumbnail( $testimonial->ID ) && 'true' == $entry_media ) : ?>

							<div class="testimonial-entry-thumb">

								<?php
								// Display post thumbnail
								wpex_post_thumbnail( array(
									'attachment' => get_post_thumbnail_id( $testimonial->ID ),
									'size'       => $img_size,
									'width'      => $img_width,
									'height'     => $img_height,
									'class'      => $img_classes,
									'style'      => $img_style,
									'crop'       => $img_crop,
								) ); ?>

							</div><!-- /testimonial-thumb -->

						<?php endif; ?>

						<div class="testimonial-entry-meta">

							<?php
							// Display testimonial author
							if ( 'true' == $author && $testimonial->author ) : ?>

								<span class="testimonial-entry-author entry-title">
									<?php echo $testimonial->author; ?>
								</span>

							<?php endif; ?>

							<?php
							// Display testimonial company
							if ( 'true' == $company && $testimonial->company ) : ?>

								<?php
								// Display testimonial company with URL
								if ( $testimonial->url ) : ?>

									<a href="<?php echo esc_url( $testimonial->url ); ?>" class="testimonial-entry-company" title="<?php echo $testimonial->company; ?>" target="_blank">
										<?php echo $testimonial->company; ?>
									</a>

								<?php
								// Display testimonial company without URL since it's not defined
								else : ?>

									<span class="testimonial-entry-company">
										<?php echo $testimonial->company; ?>
									</span>

								<?php endif; ?>

							<?php endif; ?>

						</div><!-- .testimonial-entry-meta -->

					</div><!-- .home-testimonial-entry-bottom -->

				</div><!-- .testimonials-entry -->

				<?php
				// Reset post loop counter
				if ( $count == $columns ) $count = ''; ?>

			<?php endwhile; ?>

		</div><!-- .vcex-testimonials-grid -->
		
		 <?php
		// Display pagination if enabled
		if ( 'true' == $pagination ) : ?>
			<?php wpex_pagination( $wpex_query ); ?>
		<?php endif; ?>

	</div><!-- <?php echo $wrap_classes; ?> -->

	<?php
	// Remove post object from memory
	$testimonial = null;

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