<?php
/**
 * Visual Composer Blog Grid
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
$atts['post_type'] = 'post';
$atts['taxonomy']  = 'category';
$atts['tax_query'] = '';

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

	// Sanitize & declare variables
	$wrap_classes       = array( 'vcex-blog-grid-wrap', 'wpex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-blog-grid', 'wpex-clr', 'entries' );
	$grid_data          = array();
	$inline_js          = array( 'ilightbox' );
	$is_isotope         = false;
	$read_more_text     = $read_more_text ? $read_more_text : esc_html__( 'read more', 'total' );
	$css_animation      = $css_animation ? $this->getCSSAnimation( $css_animation ) : '';
	$css_animation      = ( 'true' == $filter ) ? false : $css_animation;
	$equal_heights_grid = ( 'true' == $equal_heights_grid ) ? true : false;
	$equal_heights_grid = ( $equal_heights_grid && $columns > '1' ) ? true : false;
	$overlay_style      = $overlay_style ? $overlay_style : 'none';
	$url_target         = vcex_html( 'target_attr', $url_target );
	$title_tag          = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $atts );
	$title_tag          = $title_tag ? $title_tag : 'h2';

	// Load lightbox script
	if ( 'lightbox' == $thumb_link ) {
		vcex_enque_style( 'ilightbox' );
	}

	// Enable Isotope?
	if ( 'true' == $filter || 'masonry' == $grid_style ) {
		$is_isotope         = true;
		$equal_heights_grid = false;
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

	// Get filter terms
	if ( $filter_taxonomy ) {

		// Get filter terms
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $wpex_query ) );

		// If terms are found
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
			$filter_has_active_cat = in_array( $filter_active_category, $filter_terms_ids ) ? 'test' : false;

			// Add show on load animation when active filter is enabled to prevent double animation
			if ( $filter_has_active_cat ) {
				$grid_classes[] = 'wpex-show-on-load';
			}

		} else {

			$filter = false; // No terms

		}

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

	// Heading style
	if ( 'true' == $title ) {

		$heading_style = vcex_inline_style( array(
			'margin'            => $content_heading_margin,
			'color'             => $content_heading_color,
			'font_size'         => $content_heading_size,
			'font_weight'       => $content_heading_weight,
			'line_height'       => $content_heading_line_height,
			'text_transform'    => $content_heading_transform,
		) );

		$heading_link_style = vcex_inline_style( array(
			'color' => $content_heading_color,
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
		$excerpt_style = vcex_inline_style( array(
			'font_size' => $content_font_size,
		) );
	}

	// Readmore design and classes
	if ( 'true' == $read_more ) {
	
		// Readmore classes
		$readmore_classes = wpex_get_button_classes( $readmore_style, $readmore_style_color );
		if ( $readmore_hover_color || $readmore_hover_background ) {
			$readmore_classes .= ' wpex-data-hover';
		}

		// Readmore style
		$readmore_style = vcex_inline_style( array(
			'background'    => $readmore_background,
			'color'         => $readmore_color,
			'font_size'     => $readmore_size,
			'padding'       => $readmore_padding,
			'border_radius' => $readmore_border_radius,
			'margin'        => $readmore_margin,
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
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}
	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Media classes
	$media_classes = array( 'vcex-blog-entry-media', 'entry-media' );
	if ( $img_filter ) {
		$media_classes[] = wpex_image_filter_class( $img_filter );
	}
	if ( $img_hover_style ) {
		$media_classes[] = wpex_image_hover_classes( $img_hover_style );
	}
	if ( 'none' != $overlay_style ) {
		$media_classes[] = wpex_overlay_classes( $overlay_style );
	}
	$media_classes = implode( ' ', $media_classes );

	// Load inline JS for front-end composer
	if ( $is_isotope ) {
		$inline_js[] = 'isotope';
	}
	if ( $readmore_hover_color || $readmore_hover_background ) {
		$inline_js[] = 'data_hover';
	}
	if ( $equal_heights_grid ) {
		$inline_js[] = 'equal_heights';
	}
	vcex_inline_js( $inline_js );

	// Grid data attributes
	if ( 'true' == $filter ) {
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="'. $filter_speed .'"';
		}
		if ( $filter_has_active_cat ) {
			$grid_data[] = 'data-filter=".cat-'. $filter_active_category .'"';
		}
	} else {
		$grid_data[] = 'data-transition-duration="0.0"';
	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_blog_grid_wrap_classes', $wrap_classes );
	$grid_classes  = apply_filters( 'vcex_blog_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_blog_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : ''; ?>

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?>>

		<?php
		// Display filter links
		if ( $filter_taxonomy && ! empty( $filter_terms ) ) {

			// Sanitize all text
			$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

			// Filter button classes
			$filter_button_classes = wpex_get_button_classes( $filter_button_style, $filter_button_color );

			// Filter font size
			$filter_style = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) ); ?>

			<ul class="vcex-portfolio-filter vcex-filter-links wpex-clr<?php if ( 'yes' == $center_filter ) echo ' center'; ?>"<?php echo $filter_style; ?>>

				<?php if ( 'true' == $filter_all_link ) { ?>

					<li <?php if ( ! $filter_has_active_cat ) echo 'class="active"'; ?>><a href="#" data-filter="*" class="<?php echo $filter_button_classes; ?>"><span><?php echo $all_text; ?></span></a></li>

				<?php } ?>

				<?php
				// Loop through terms to display filter links
				foreach ( $filter_terms as $term ) : ?>

					<li class="filter-cat-<?php echo $term->term_id; ?><?php if ( $filter_active_category == $term->term_id ) echo ' active'; ?>"><a href="#" data-filter=".cat-<?php echo $term->term_id; ?>" class="<?php echo $filter_button_classes; ?>"><?php echo $term->name; ?></a></li>

				<?php endforeach; ?>

			</ul><!-- .vcex-portfolio-filter -->

		<?php } ?>

		<div class="<?php echo esc_attr( $grid_classes ); ?>"<?php echo $grid_data; ?>>

			<?php
			// Define counter var to clear floats
			$count = '';

			// Start loop
			while ( $wpex_query->have_posts() ) :

				// Get post from query
				$wpex_query->the_post();

				// Create new post object.
				$post = new stdClass();

				// Get post data
				$get_post = get_post();

				// Post Data
				$post->ID        = $get_post->ID;
				$post->title     = $get_post->post_title;
				$post->permalink = wpex_get_permalink( $post->ID );
				$post->format    = get_post_format( $post->ID );
				$post->excerpt   = '';

				// Post Excerpt
				if ( 'true' == $excerpt ) {
					$post->excerpt = wpex_get_excerpt( array (
						'length' => intval( $excerpt_length ),
					) );
				}

				// Counter
				$count++;

				// Get video
				if ( 'video' == $post->format ) {
					$post->video        = wpex_get_post_video( $post->ID );
					$post->video_oembed = wpex_get_post_video_html( $post->video );
				}

				// Does entry have details?
				if ( 'true' == $title
					|| ( 'true' == $excerpt && $post->excerpt )
					|| 'true' == $read_more
				) {
					$entry_has_details = true;
				} else {
					$entry_has_details = false;
				}

				// Entry Classes
				$entry_classes   = array( 'vcex-blog-entry' );
				if ( $entry_has_details ) {
					$entry_classes[] = 'entry-has-details';
				}
				$entry_classes[] = 'span_1_of_'. $columns;
				$entry_classes[] = 'col-'. $count;
				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}
				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}
				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}
				if ( $filter_taxonomy ) {
					if ( $post_terms = get_the_terms( $post, $filter_taxonomy ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = 'cat-'. $post_term->term_id;
						}
					}
				} ?>

				<div <?php post_class( $entry_classes ); ?>>

					<div class="vcex-blog-entry-inner entry-inner wpex-clr<?php if ( $entry_css ) echo ' '. $entry_css; ?>">

						<?php
						// If media is enabled
						if ( 'true' == $entry_media ) : ?>

							<?php
							// Display post video if defined and is video format
							if ( 'true' == $featured_video && ! empty( $post->video ) && $post->video_oembed ) : ?>

								<div class="vcex-blog-entry-media entry-media">
									<?php echo $post->video_oembed; ?>
								</div><!-- .vcex-blog-entry-media -->

							<?php
							// Otherwise if post thumbnail is defined
							elseif ( has_post_thumbnail( $post->ID ) ) : ?>

								<div class="<?php echo esc_attr( $media_classes ); ?>">

									<?php
									// Open link tag if thumblink does not equal nowhere
									if ( 'nowhere' != $thumb_link ) : ?>

										<?php
										// Lightbox Links
										if ( $thumb_link == 'lightbox' ) :

											// Video lightbox link
											if ( 'video' == $post->format ) :

												// Try and convert video URL into embed URL
												$embed_url = wpex_sanitize_data( $post->video, 'embed_url' );
												$atts['lightbox_link'] = $embed_url ? $embed_url : $post->video;

												// Data options
												$data_options =  'width:1920,height:1080';

												// Add smart recognition if we can't generate an embed_url
												if ( ! $embed_url ) {
													$data_options .=',smartRecognition:true';
												} ?>

												<a href="<?php echo $atts['lightbox_link']; ?>" title="<?php wpex_esc_title(); ?>" class="wpex-lightbox" data-type="iframe" data-options="<?php echo $data_options; ?>">

											<?php
											// Image lightbox link
											else :

												// Add lightbox attributes
												$atts['lightbox_link'] = wpex_get_lightbox_image(); ?>

												<a href="<?php echo $atts['lightbox_link']; ?>" title="<?php wpex_esc_title(); ?>" class="wpex-lightbox">

											<?php endif; ?>

										<?php else : ?>

											 <a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>"<?php echo $url_target; ?>>

										<?php endif; ?>

									<?php endif; ?>

										<?php
										// Display featured image
										wpex_post_thumbnail( array(
											'size'   => $img_size,
											'width'  => $img_width,
											'height' => $img_height,
											'alt'    => wpex_get_esc_title(),
											'crop'   => $img_crop,
											'class'  => 'vcex-blog-entry-img',
										) ); ?>

										<?php
										// Inner link overlay HTML
										if ( 'none' != $overlay_style ) {
											wpex_overlay( 'inside_link', $overlay_style, $atts );
										} ?>

									<?php
									// Close link tag
									if ( 'nowhere' != $thumb_link ) echo '</a>'; ?>

									<?php
									// Outer link overlay HTML
									if ( 'none' != $overlay_style ) {
										wpex_overlay( 'outside_link', $overlay_style, $atts );
									} ?>

								</div><!-- .blog-entry-media -->

							<?php endif; // Video/thumbnail checks ?>

						<?php endif; // Display media check ?>

						<?php
						// Open entry details div if the $title, $excerpt or $read_more vars are true
						if ( $entry_has_details ) : ?>

							<div class="vcex-blog-entry-details entry-details wpex-clr<?php if ( $content_css ) echo ' '. $content_css; ?>"<?php echo $content_style; ?>>

								<?php
								// Open equal heights div if equal heights is enabled
								if ( $equal_heights_grid ) echo '<div class="match-height-content">'; ?>

								<?php
								// Display title if $title is true
								if ( 'true' == $title ) : ?>

									<<?php echo $title_tag; ?> class="vcex-blog-entry-title entry-title"<?php echo $heading_style; ?>><a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>"<?php echo $url_target; ?><?php echo $heading_link_style; ?>><?php echo $post->title; ?></a></<?php echo $title_tag; ?>>
									
								<?php endif; ?>

								<?php
								// Display date if $date is true
								if ( 'true' == $date ) : ?>

									<div class="vcex-blog-entry-date"<?php echo $date_style; ?>>
										<?php echo get_the_date(); ?>
									</div>

								<?php endif; ?>

								<?php
								// Display excerpt
								if ( 'true' == $excerpt ) : ?>

									<div class="vcex-blog-entry-excerpt entry clr"<?php echo $excerpt_style; ?>>

										<?php
										// Display excerpt
										if ( $excerpt && $post->excerpt ) : ?>

											<?php echo $post->excerpt; ?>

										<?php endif; ?>

									</div><!-- .vcex-blog-entry-excerpt -->

								<?php endif; ?>

								<?php
								// Display read more button if $read_more is true and $read_more_text isn't empty
								if ( 'true' == $read_more ) : ?>

									<div class="vcex-blog-entry-readmore-wrap clr">

										<a href="<?php echo $post->permalink; ?>" title="<?php echo esc_attr( $read_more_text ); ?>" rel="bookmark" class="<?php echo $readmore_classes; ?>"<?php echo $url_target; ?><?php echo $readmore_style; ?><?php echo $readmore_data; ?>>
											<?php echo $read_more_text; ?>
											<?php
											// Display readmore button rarr if enabled
											if ( 'true' == $readmore_rarr ) : ?>
												<span class="vcex-readmore-rarr"><?php echo wpex_element( 'rarr' ); ?></span>
											<?php endif; ?>
										</a>

									</div><!-- .vcex-blog-entry-readmore-wrap -->

								<?php endif; ?>

								<?php
								// Close equal heights div if equal heights is enabled
								if ( $equal_heights_grid ) echo '</div>'; ?>

							</div><!-- .blog-entry-details -->

						<?php endif; // title/excerpt check ?>

					</div>

				</div><!-- .blog-entry -->

			<?php
			// Reset entry counter
			if ( $count == $columns ) $count = '0'; ?>

			<?php
			// End main loop
			endwhile; ?>
			
		</div><!-- .vcex-blog-grid -->

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