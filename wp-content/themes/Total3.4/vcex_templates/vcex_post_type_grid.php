<?php
/**
 * Visual Composer Post Type Grid
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

// Get and extract shortcode attributes
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

// Build the WordPress query
$wpex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $wpex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$entry_media = ( ! $entry_media ) ? 'true' : $entry_media;
	$title       = ( ! $title ) ? 'true' : $title;
	$date        = ( ! $date ) ? 'true' : $date;
	$excerpt     = ( ! $excerpt ) ? 'true' : $excerpt;
	$read_more   = ( ! $read_more ) ? 'true' : $read_more;

	// Declare and sanitize variables
	$wrap_classes       = array( 'vcex-post-type-grid-wrap', 'wpex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-post-type-grid', 'entries', 'wpex-clr' );
	$grid_data          = array();
	$inline_js          = array();
	$is_isotope         = false;
	$url_target         = vcex_html( 'target_attr', $url_target );
	$equal_heights_grid = ( 'true' == $equal_heights_grid ) ? true : false;
	$equal_heights_grid = ( $equal_heights_grid && $columns > '1' ) ? true : false;
	$css_animation      = $this->getCSSAnimation( $css_animation );
	$css_animation      = 'true' == $filter ? false : $css_animation;
	$title_tag          = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $atts );
	$title_tag          = $title_tag ? $title_tag : 'h2';

	// Advanced sanitization
	if ( 'true' == $filter || 'masonry' == $grid_style || 'no_margins' == $grid_style ) {
		$is_isotope         = true;
		$equal_heights_grid = false;
	}
	if ( 'true' != $filter && 'masonry' == $grid_style ) {
		$post_count = count( $wpex_query->posts );
		if ( $post_count <= $columns ) {
			$is_isotope = false;
		}
	}

	// Check url for filter cat
	$filter_url_param = vcex_grid_filter_url_param();
	if ( isset( $_GET[$filter_url_param] ) ) {
		if ( 'post_types' == $filter_type ) {
			$filter_active_category = 'post-type-'. $_GET[$filter_url_param];
		}
		// Add show on load animation when active filter is enabled to prevent double animation
		$grid_classes[] = 'wpex-show-on-load';
	} else {
		$filter_active_category = false;
	}

	// Load lightbox scripts
	if ( 'lightbox' == $thumb_link ) {
		wpex_enqueue_ilightbox_skin();
	}

	// Turn post types into array
	$post_types = $post_types ? $post_types : 'post';
	$post_types = explode( ',', $post_types );

	// Add inline JS
	if ( $equal_heights_grid ) {
		$inline_js[] = 'equal_heights';
	}
	if ( $is_isotope ) {
		$inline_js[] = 'isotope';
	}
	if ( $readmore_hover_color || $readmore_hover_background ) {
		$inline_js[] = 'data_hover';
	}
	vcex_inline_js( $inline_js );

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-'. $columns_gap;
	}
	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}
	if ( 'no_margins' == $grid_style ) {
		$grid_classes[] = 'vcex-no-margin-grid';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Entry CSS class
	if ( $entry_css ) {
		$entry_css = vc_shortcode_custom_css_class( $entry_css );
	}

	// Content Design
	$content_style = array(
		'color'      => $content_color,
		'opacity'    => $content_opacity,
		'text_align' => $content_alignment,
	);
	if ( ! $content_css ) {
		if ( isset( $content_background ) ) {
			$content_style['background'] = $content_background;
		}
		if ( isset( $content_padding ) ) {
			$content_style['padding'] = $content_padding;
		}
		if ( isset( $content_margin ) ) {
			$content_style['margin'] = $content_margin;
		}
		if ( isset( $content_border ) ) {
			$content_style['border'] = $content_border;
		}
	} else {
		$content_css = vc_shortcode_custom_css_class( $content_css );
	}
	$content_style = vcex_inline_style( $content_style );

	// Categories style
	if ( 'true' == $show_categories ) {
		$categories_style = vcex_inline_style( array(
			'margin'    => $categories_margin,
			'font_size' => $categories_font_size,
			'color'     => $categories_color,
		) );
		$categories_classes = 'vcex-post-type-entry-categories entry-categories wpex-clr';
		if ( $categories_color ) {
			$categories_classes .= ' wpex-child-inherit-color';
		}
	}

	// Excerpt Design
	if ( 'true' == $excerpt ) {
		$excerpt_style = vcex_inline_style( array(
			'font_size' => $content_font_size,
			'color' => $content_color,
		) );
	}

	// Heading Design
	if ( 'true' == $title ) {
		$heading_style = vcex_inline_style( array(
			'margin' => $content_heading_margin,
			'font_size' => $content_heading_size,
			'color' => $content_heading_color,
			'line_height' => $content_heading_line_height,
			'text_transform' => $content_heading_transform,
			'font_weight' => $content_heading_weight,
		) );
		$heading_link_style = vcex_inline_style( array(
			'color' => $content_heading_color,
		) );
	}

	// Readmore design and classes
	if ( 'true' == $read_more ) {

		// Read more text
		$read_more_text = $read_more_text ? $read_more_text : esc_html__( 'read more', 'total' );

		// Readmore classes
		$readmore_classes = wpex_get_button_classes( $readmore_style, $readmore_style_color );
		if ( $readmore_hover_color || $readmore_hover_background ) {
			$readmore_classes .= ' wpex-data-hover';
		}

		// Readmore style
		$readmore_style = vcex_inline_style( array(
			'background' => $readmore_background,
			'color' => $readmore_color,
			'font_size' => $readmore_size,
			'padding' => $readmore_padding,
			'border_radius' => $readmore_border_radius,
			'margin' => $readmore_margin,
		) );

		// Readmore data
		$readmore_data = array();
		if ( $readmore_hover_color ) {
			$readmore_data[] = 'data-hover-color="'. $readmore_hover_color .'"';
		}
		if ( $readmore_hover_background ) {
			$readmore_data[] = 'data-hover-background="'. $readmore_hover_background .'"';
		}
		$readmore_data = ' '. implode( ' ', $readmore_data );

	}

	// Date design
	if ( 'true' == $date ) {
		$date_style = vcex_inline_style( array(
			'color'     => $date_color,
			'font_size' => $date_font_size,
		) );
	}

	// Data
	if ( 'true' == $filter ) {
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="'. $filter_speed .'"';
		}
		if ( $filter_active_category ) {
			$grid_data[] = 'data-filter=".'. $filter_active_category .'"';
		}
	} else {
		$grid_data[] = 'data-transition-duration="0.0"';
	}

	// Static entry classes
	$static_entry_classes = array( 'vcex-post-type-entry', 'clr' );
	if ( 'false' == $columns_responsive ) {
		$static_entry_classes[] = ' nr-col';
	} else {
		$static_entry_classes[] = ' col';
	}
	$static_entry_classes[] = ' span_1_of_'. $columns;
	if ( $is_isotope ) {
		$static_entry_classes[] = ' vcex-isotope-entry';
	}
	if ( 'no_margins' == $grid_style ) {
		$static_entry_classes[] = ' vcex-no-margin-entry';
	}
	if ( $css_animation ) {
		$static_entry_classes[] = ' '. $css_animation;
	}
	if ( 'true' != $entry_media ) {
		$static_entry_classes[] = ' vcex-post-type-no-media-entry';
	}

	// Entry media classes
	$media_classes = array( 'vcex-post-type-entry-media', 'entry-media', 'wpex-clr' );
	if ( 'true' == $entry_media ) {
		if ( $img_filter ) {
			$media_classes[] = wpex_image_filter_class( $img_filter );
		}
		if ( $img_hover_style ) {
			$media_classes[] = wpex_image_hover_classes( $img_hover_style );
		}
		if ( $overlay_style ) {
			$media_classes[] = wpex_overlay_classes( $overlay_style );
		}
	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_post_type_grid_wrap_classes', $wrap_classes );
	$grid_classes  = apply_filters( 'vcex_post_type_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_post_type_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';
	$media_classes = implode( ' ', $media_classes ); ?>

	<div class="<?php echo $wrap_classes; ?>"<?php echo vcex_unique_id( $unique_id ); ?>>

		<?php
		// Display filter links
		if ( 'true' == $filter ) {

			// Make sure the filter should display
			if ( count( $post_types ) > 1 || 'taxonomy' == $filter_type ) {

				// Filter button classes
				$filter_button_classes = wpex_get_button_classes( $filter_button_style, $filter_button_color );

				// Filter font size
				$filter_style = vcex_inline_style( array(
					'font_size' => $filter_font_size,
				) ); ?>

				<ul class="vcex-post-type-filter vcex-filter-links wpex-clr<?php if ( 'yes' == $center_filter ) echo ' center'; ?>"<?php echo $filter_style; ?>>

					<?php
					// Sanitize all text
					$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' ); ?>

					<li<?php if ( ! $filter_active_category ) echo ' class="active"'; ?>><a href="#" data-filter="*" class="<?php echo $filter_button_classes; ?>"><span><?php echo $all_text; ?></span></a></li>

					<?php
					// Taxonomy style filter
					if ( 'taxonomy' == $filter_type ) :

						// If taxonony exists get terms
						if ( taxonomy_exists( $filter_taxonomy ) ) {

							// Get filter args
							$atts['filter_taxonomy'] = $filter_taxonomy;
							$args = vcex_grid_filter_args( $atts, $wpex_query );
							$terms = get_terms( $filter_taxonomy, $args );

							// Display filter
							if ( ! empty( $terms ) ) { ?>

								<?php foreach ( $terms as $term ) : ?>
									<li class="filter-cat-<?php echo $term->term_id; ?>"><a href="#" data-filter=".cat-<?php echo $term->term_id; ?>" class="<?php echo $filter_button_classes; ?>"><?php echo $term->name; ?></a></li>
								<?php endforeach; ?>

							<?php } ?>

						<?php } ?>

					<?php else : ?>

						<?php
						// Get array of post types in loop so we don't display empty results
						$active_types = array();
						$post_ids = wp_list_pluck( $wpex_query->posts, 'ID' );
						foreach ( $post_ids as $post_id ) {
							$type = get_post_type( $post_id );
							$active_types[$type] = $type;
						}

						// Loop through active types
						foreach ( $active_types as $type ) :
							
							// Get type object
							$obj = get_post_type_object( $type );

							// Define filter item var
							$filter_item = 'post-type-'. $type; ?>

							<li class="vcex-filter-link-<?php echo $type; ?><?php if ( $filter_active_category == $filter_item ) echo ' active'; ?>"><a href="#" data-filter=".<?php echo $filter_item; ?>" class="<?php echo $filter_button_classes; ?>"><?php echo $obj->labels->name; ?></a></li>

						<?php endforeach; ?>

					<?php endif; ?>

				</ul><!-- .vcex-post-type-filter -->

			<?php } ?>

		<?php } ?>

		<div class="<?php echo $grid_classes; ?>"<?php echo $grid_data; ?>>
			<?php
			// Define counter var to clear floats
			$count='';

			// Loop through posts
			while ( $wpex_query->have_posts() ) :

				// Get post from query
				$wpex_query->the_post();

				// Create new post object.
				$post = new stdClass();

				// Get post data
				$get_post = get_post();

				// Add to counter var
				$count++;

				// Post Data
				$post->ID        = $get_post->ID;
				$post->content   = $get_post->post_content;
				$post->type      = get_post_type( $post->ID );
				$post->title     = $get_post->post_title;
				$post->permalink = wpex_get_permalink( $post->ID );
				$post->format    = get_post_format( $post->ID );
				$post->excerpt   = '';
				$post->thumbnail = wp_get_attachment_url( get_post_thumbnail_id() );
				$post->video     = wpex_get_post_video_html();

				// Entry Classes
				$entry_classes  = array();
				$entry_classes[] = 'col-'. $count;
				$entry_classes[] = 'post-type-'. get_post_type( $post->ID );
				if ( taxonomy_exists( $filter_taxonomy ) ) {
					if ( $post_terms = get_the_terms( $post, $filter_taxonomy ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = 'cat-'. $post_term->term_id;
						}
					}
				}
				$entry_classes = array_merge( $static_entry_classes, $entry_classes );

				// Define entry link and entry link classes
				$entry_link = $post->permalink;
				if ( $thumb_link == 'lightbox' ) {
					//$entry_link         = $post->video ? $post->video : $post->thumbnail;
					//$entry_link_classes = $post->video ? 'wpex-lightbox-video' : 'wpex-lightbox';
					$entry_link            = wpex_get_lightbox_image();
					$entry_link_classes    = 'wpex-lightbox';
					$atts['lightbox_link'] = $entry_link;
				}
				$entry_link_classes = ! empty( $entry_link_classes ) ? 'class="'. $entry_link_classes .'"' : '';

				// Entry image output HTMl
				if ( $post->thumbnail ) {
					$entry_image = wpex_get_post_thumbnail( array(
						'size' => $img_size,
						'crop' => $img_crop,
						'width' => $img_width,
						'height' => $img_height,
						'alt' => wpex_get_esc_title(),
					) );
				} ?>

				<div <?php post_class( $entry_classes ); ?>>

					<div class="vcex-post-type-entry-inner entry-inner wpex-clr<?php if ( $entry_css ) echo ' '. $entry_css; ?>">

						<?php if ( 'true' == $entry_media ) : ?>

							<?php
							// Display video
							if ( 'true' == $featured_video && $post->video ) : ?>

								<div class="vcex-post-type-entry-media entry-media clr">

									<div class="vcex-video-wrap">

										<?php echo $post->video; ?>

									</div><!-- .vcex-video-wrap -->

								</div><!-- .vcex-post-type-entry-media -->

							<?php
							// Display featured image
							elseif ( $post->thumbnail ) : ?>

								<div class="<?php echo $media_classes; ?>">

									<?php if ( $thumb_link == 'post' || $thumb_link == 'lightbox' ) : ?>

										<a href="<?php echo $entry_link; ?>" title="<?php wpex_esc_title(); ?>"<?php echo $url_target; ?><?php echo $entry_link_classes; ?>>
											<?php echo $entry_image; ?>
											<?php wpex_overlay( 'inside_link', $overlay_style, $atts ); ?>
										</a>

									<?php else : ?>

										<?php echo $entry_image; ?>

									<?php endif; ?>

									<?php wpex_overlay( 'outside_link', $overlay_style, $atts ); ?>

								</div><!-- .post_type-entry-media -->

							<?php endif; ?>

						<?php endif; ?>

						<?php if ( 'true' == $title
							|| 'true' == $show_categories
							|| 'true' == $excerpt
							|| 'true' == $read_more
						) : ?>

							<div class="vcex-post-type-entry-details entry-details wpex-clr<?php if ( $content_css ) echo ' '. $content_css; ?>"<?php echo $content_style; ?>>

								<?php if ( $equal_heights_grid ) echo '<div class="match-height-content">'; ?>

								<?php
								// Display title
								if ( 'true' == $title ) : ?>

									<<?php echo $title_tag; ?> class="vcex-post-type-entry-title entry-title" <?php echo $heading_style; ?>>
										<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>"<?php echo $url_target; ?><?php echo $heading_link_style; ?>><?php the_title(); ?></a>
									</<?php echo $title_tag; ?> >

								<?php endif; ?>

								<?php
								// Display date
								if ( 'true' == $date ) : ?>

									<div class="vcex-post-type-entry-date"<?php echo $date_style; ?>>
										<?php
										// Display Event Calendar date
										if ( 'tribe_events' == $post->type
											&& function_exists( 'wpex_get_tribe_event_date' )
										) {
											$instance = $unique_id ? $unique_id : 'vcex_post_type_grid';
											echo wpex_get_tribe_event_date( $instance );
										// Display standard date
										} else {
											echo get_the_date();
										} ?>
									</div><!-- .vcex-post-type-entry-date -->

								<?php endif; ?>

								<?php
								// Display categories
								if ( 'true' == $show_categories && taxonomy_exists( $categories_taxonomy ) ) : ?>

									<div class="<?php echo $categories_classes; ?>"<?php echo $categories_style; ?>>
										<?php
										// Display categories
										if ( 'true' == $show_first_category_only ) {
											wpex_first_term_link( $post->ID, $categories_taxonomy );
										} else {
											wpex_list_post_terms( $categories_taxonomy, true, true );
										} ?>
									</div><!-- .portfolio-entry-categories -->

								<?php endif; ?>

								<?php
								// Display excerpt
								if ( 'true' == $excerpt ) : ?>

									<div class="vcex-post-type-entry-excerpt clr"<?php echo $excerpt_style; ?>>

										<?php
										// Display Excerpt
										wpex_excerpt( array (
											'length' => intval( $excerpt_length ),
										) ); ?>

									</div><!-- .vcex-post-type-entry-excerpt -->

								<?php endif; ?>

								<?php
								// Display read more button
								if ( 'true' == $read_more ) : ?>

									<div class="vcex-post-type-entry-readmore-wrap clr">

										<a href="<?php echo $post->permalink; ?>" title="<?php echo esc_attr( $read_more_text ); ?>" rel="bookmark" class="<?php echo $readmore_classes; ?>"<?php echo $url_target; ?><?php echo $readmore_style; ?><?php echo $readmore_data; ?>>
											<?php echo $read_more_text; ?>
											<?php if ( 'true' == $readmore_rarr ) : ?>
												<span class="vcex-readmore-rarr"><?php echo wpex_element( 'rarr' ); ?></span>
											<?php endif; ?>
										</a>

									</div><!-- .vcex-post-type-entry-readmore-wrap -->

								<?php endif; ?>

								<?php if ( $equal_heights_grid ) echo '</div>'; ?>

							</div><!-- .post_type-entry-details -->

						<?php endif; ?>

					</div><!-- .vcex-post-type-entry-inner -->

				</div><!-- .post_type-entry -->

			<?php if ( $count == $columns ) $count = ''; ?>

			<?php endwhile; // End main loop ?>

		</div><!-- .vcex-post-type-grid -->
		
		<?php
		// Display pagination if enabled
		if ( 'true' == $pagination ) : ?>

			 <?php wpex_pagination( $wpex_query ); ?>

		<?php endif; ?>

	</div><!-- <?php echo $wrap_classes; ?> -->

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