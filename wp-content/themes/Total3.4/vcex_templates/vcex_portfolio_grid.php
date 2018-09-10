<?php
/**
 * Visual Composer Portfolio Grid
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
$atts['post_type'] = 'portfolio';
$atts['taxonomy']  = 'portfolio_category';
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

	// Sanitize data & declare main variables
	$inline_js          = array();
	$grid_data          = array();
	$wrap_classes       = array( 'vcex-portfolio-grid-wrap', 'wpex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-portfolio-grid', 'wpex-clr', 'entries' );
	$is_isotope         = false;
	$excerpt_length     = $excerpt_length ? $excerpt_length : '30';
	$css_animation      = $css_animation ? $this->getCSSAnimation( $css_animation ) : '';
	$css_animation      = ( 'true' == $filter ) ? false : $css_animation;
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;
	$overlay_style      = $overlay_style ? $overlay_style : 'none';
	$title_tag          = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $atts );
	$title_tag          = $title_tag ? $title_tag : 'h2';
	$gallery_slider     = false; // NOT DONE YET

	// Load lightbox scripts
	if ( 'lightbox' == $thumb_link ) {
		wpex_enqueue_ilightbox_skin( $lightbox_skin );
	}

	// Enable Isotope
	if ( 'true' == $filter || 'masonry' == $grid_style || 'no_margins' == $grid_style ) {
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

		// Make sure we have terms before doing things
		if ( $filter_terms ) {

			// Get term ids
			$filter_terms_ids = wp_list_pluck( $filter_terms, 'term_id' );

			// Check url for filter cat
			$filter_url_param = vcex_grid_filter_url_param();
			if ( isset( $_GET[$filter_url_param] ) ) {
				$filter_active_category = esc_html( $_GET[$filter_url_param] );
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
			$filter = false; // no terms
		}

	}

	// Add inline js
	if ( $is_isotope ) {
		$inline_js[] = 'isotope';
	}
	if ( 'lightbox' == $thumb_link ) {
		$inline_js[] = 'ilightbox';
	}
	if ( $readmore_hover_color || $readmore_hover_background ) {
		$inline_js[] = 'data_hover';
	}
	if ( $equal_heights_grid ) {
		$inline_js[] = 'equal_heights';
	}
	if ( 'lightbox' == $thumb_link ) {
		$inline_js[] = 'ilightbox';
	}
	if ( 'true' == $gallery_slider ) {
		$inline_js[] = 'slider_pro';
	}
	if ( $inline_js ) {
		vcex_inline_js( $inline_js );
	}

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Main grid classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-'. $columns_gap;
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}
	if ( 'no_margins' == $grid_style ) {
		$grid_classes[] = 'vcex-no-margin-grid';
	}
	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}
	if ( 'true' == $thumb_lightbox_gallery ) {
		$grid_classes[] = ' lightbox-group';
		if ( $lightbox_skin ) {
			$grid_data[] = 'data-skin="'. $lightbox_skin .'"';
		}
		$lightbox_single_class = ' wpex-lightbox-group-item';
	} else {
		$lightbox_single_class = ' wpex-lightbox';
	}

	// Grid data attributes
	if ( 'true' == $filter ) {
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="'. $filter_speed .'"';
		}
		if ( ! empty( $filter_has_active_cat ) ) {
			$grid_data[] = 'data-filter=".cat-'. $filter_active_category .'"';
		}
	} else {
		$grid_data[] = 'data-transition-duration="0.0"';
	}

	// Media classes
	if ( 'true' == $entry_media ) {
		$media_classes = array( 'portfolio-entry-media', 'entry-media', 'wpex-clr' );
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

		// Heading Design
		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'font_size'      => $content_heading_size,
			'color'          => $content_heading_color,
			'font_weight'    => $content_heading_weight,
			'text_transform' => $content_heading_transform,
			'line_height'    => $content_heading_line_height,
		) );

		// Heading Link style
		$heading_link_style = vcex_inline_style( array(
			'color' => $content_heading_color,
		) );

	}

	// Categories style
	if ( 'true' == $show_categories ) {
		$categories_style = vcex_inline_style( array(
			'margin'    => $categories_margin,
			'font_size' => $categories_font_size,
			'color'     => $categories_color,
		) );
		$categories_classes = 'portfolio-entry-categories entry-categories wpex-clr';
		if ( $categories_color ) {
			$categories_classes .= ' wpex-child-inherit-color';
		}
	}

	// Excerpt style
	if ( 'true' == $excerpt ) {

		$excerpt_style = vcex_inline_style( array(
			'font_size' => $content_font_size,
		) );

	}

	// Readmore design
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
		$readmore_data = implode( ' ', $readmore_data );

	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_portfolio_grid_wrap_classes', $wrap_classes );
	$grid_classes  = apply_filters( 'vcex_portfolio_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_portfolio_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : ''; ?>

	<div class="<?php echo $wrap_classes; ?>"<?php echo vcex_unique_id( $unique_id ); ?>>

		<?php
		// Display filter links
		if ( 'true' == $filter && ! empty( $filter_terms ) ) {

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
			$count = 0;

			// Start loop
			while ( $wpex_query->have_posts() ) :

				// Get post from query
				$wpex_query->the_post();

				// Create new post object
				$post = new stdClass();

				// Get post data
				$get_post = get_post();

				// Post Data
				$post->ID           = $get_post->ID;
				$post->permalink    = wpex_get_permalink( $post->ID );
				$post->title        = $get_post->post_title;
				$post->video        = wpex_get_post_video( $post->ID );
				$post->video_output = wpex_get_post_video_html( $post->video );
				$post->excerpt      = '';

				// Post Excerpt
				if ( 'true' == $excerpt || 'true' == $thumb_lightbox_caption ) {
					$post->excerpt = wpex_get_excerpt( array (
						'length' => intval( $excerpt_length ),
					) );
				}

				// Does entry have details?
				if ( 'true' == $title
						|| 'true' == $show_categories
						|| ( 'true' == $excerpt && $post->excerpt )
						|| 'true' == $read_more
				) {
					$entry_has_details = true;
				} else {
					$entry_has_details = false;
				}

				// Add to the counter var
				$count++;

				// Add classes to the entries
				$entry_classes = array( 'portfolio-entry' );
				if ( $entry_has_details ) {
					$entry_classes[] = 'entry-has-details';
				}
				$entry_classes[] = 'span_1_of_'. $columns;
				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}
				if ( $count ) {
					$entry_classes[] = 'col-'. $count;
				}
				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}
				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}
				if ( 'no_margins' == $grid_style ) {
					$entry_classes[] = 'vcex-no-margin-entry';
				}
				if ( $filter_taxonomy ) {
					if ( $post_terms = get_the_terms( $post->ID, $filter_taxonomy ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = 'cat-'. $post_term->term_id;
						}
					}
				} ?>

				<div <?php post_class( $entry_classes ); ?>>

					<div class="portfolio-entry-inner entry-inner wpex-clr<?php if ( $entry_css ) echo ' '. $entry_css; ?>">

						<?php
						// Entry Media
						if ( 'true' == $entry_media ) :

							/* Video
							-------------------------------------------------------------------------------*/
							if ( 'true' == $featured_video && $post->video_output ) : ?>

								<div class="portfolio-entry-media portfolio-featured-video entry-media wpex-clr">
									<?php echo $post->video_output; ?>
								</div><!-- .portfolio-featured-video -->

							<?php
							/* Gallery: Still not sure if I am going to add this or not...too much bloat :(
							-------------------------------------------------------------------------------*/
							elseif ( 'true' == $gallery_slider && $gallery_attachments = wpex_get_gallery_ids( $post->ID ) ) :

								// Get only first x number of items
								$gallery_attachments = array_slice( $gallery_attachments, 0, 3 );

								// Slider args adds a filter so you can easily tweak the slider animation, etc for this slider
								$args = array(
									'filter_tag'                => 'vcex_portfolio_grid_slider_'. $unique_id,
									'fade'                      => 'true',
									'height-animation-duration' => '0.0'
								); ?>

								<div class="vcex-grid-entry-slider wpex-slider slider-pro clr"<?php wpex_slider_data( $args ); ?>>

									<div class="wpex-slider-slides sp-slides <?php if ( 'lightbox' == $thumb_link ) echo 'lightbox-group'; ?>">

										<?php
										// Loop through gallery images
										foreach ( $gallery_attachments as $attachment ) :

											// Get attachment data
											$attachment_data = wpex_get_attachment_data( $attachment ); ?>

											<div class="wpex-slider-slide sp-slide">

												<div class="<?php echo $media_classes; ?><?php if ( 'true' == $thumb_lightbox_gallery ) echo ' wpex-lightbox-group'; ?>">

													<?php
													// Open link tag if thumblink does not equal nowhere
													if ( 'nowhere' != $thumb_link ) : ?>

														<?php
														// Lightbox
														if ( 'lightbox' == $thumb_link ) : ?>

															<?php
															// Get lightbox link
															$atts['lightbox_link'] = wpex_get_lightbox_image( $attachment );

															// Add lightbox attributes
															$atts['lightbox_data'] = array();
															if ( $lightbox_skin ) {
																$atts['lightbox_data'][] = 'data-skin="'. $lightbox_skin .'"';
															}
															if ( 'true' == $thumb_lightbox_title ) {
																$atts['lightbox_data'][] = 'data-title="'. $attachment_data['alt'] .'"';
															}
															$lightbox_data = ' '. implode( ' ', $atts['lightbox_data'] ); ?>
															<a href="<?php echo $atts['lightbox_link']; ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link wpex-lightbox"<?php echo $lightbox_data; ?>>

														<?php 
														// Standard post link
														else : ?>

															<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link"<?php echo vcex_html( 'target_attr', $link_target ); ?>>

														<?php endif; ?>

													<?php endif; ?>

													<?php
													// Display post thumbnail
													wpex_post_thumbnail( array(
														'attachment' => $attachment,
														'width'      => $img_width,
														'height'     => $img_height,
														'crop'       => $img_crop,
														'alt'        => wpex_get_esc_title(),
														'class'      => 'portfolio-entry-img',
														'size'       => $img_size,
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

												</div><!-- .<?php echo $media_classes; ?> -->

											</div><!-- .wpex-slider-slide -->

										<?php endforeach; ?>

									</div><!-- .wpex-slider-slides -->

								</div><!-- .wpex-slider-slier -->

							<?php 
							/* Featured Image
							-------------------------------------------------------------------------------*/
							elseif ( has_post_thumbnail( $post->ID ) ) : ?>

								<div class="<?php echo $media_classes; ?>">

									<?php
									// Open link tag if thumblink does not equal nowhere
									if ( 'nowhere' != $thumb_link ) : ?>

										<?php
										// Lightbox
										if ( 'lightbox' == $thumb_link ) :

											// Save correct lightbox class
											$lightbox_class = $lightbox_single_class;

											// Generate lightbox image
											$lightbox_image = wpex_get_lightbox_image();

											// Get lightbox link
											$atts['lightbox_link'] = $lightbox_image;

											// Define lightbox data attributes
											$atts['lightbox_data'] = array();
											if ( $lightbox_skin ) {
												$atts['lightbox_data'][] = 'data-skin="'. $lightbox_skin .'"';
											}
											if ( 'true' == $thumb_lightbox_title ) {
												$atts['lightbox_data'][] = 'data-title="'. wpex_get_esc_title() .'"';
											}
											if ( 'true' == $thumb_lightbox_caption && $post->excerpt ) {
												$atts['lightbox_data'][] = 'data-caption="'. str_replace( '"',"'", $post->excerpt ) .'"';
											}

											// Check for video
											if ( $post->video = get_post_meta( $post->ID, 'wpex_post_video', true ) ) {
												$embed_url = wpex_sanitize_data( $post->video, 'embed_url' );
												if ( $embed_url ) {
													$atts['lightbox_link']   = $post->video;
													$atts['lightbox_data'][] = 'data-type="iframe"';
													$atts['lightbox_data'][] = 'data-options="thumbnail:\''. $lightbox_image .'\',width:1920,height:1080"';
												}
											}

											// Implode lightbox data
											$lightbox_data = ' '. implode( ' ', $atts['lightbox_data'] ); ?>

											<a href="<?php echo $atts['lightbox_link']; ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link<?php echo $lightbox_class; ?>"<?php echo $lightbox_data; ?>>

										<?php 
										// Standard post link
										else : ?>

											<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link"<?php echo vcex_html( 'target_attr', $link_target ); ?>>

										<?php endif; ?>

									<?php endif; ?>

									<?php
									// Display post thumbnail
									wpex_post_thumbnail( array(
										'width'  => $img_width,
										'height' => $img_height,
										'crop'   => $img_crop,
										'alt'    => wpex_get_esc_title(),
										'class'  => 'portfolio-entry-img',
										'size'   => $img_size,
									) ); ?>

									<?php
									// Inner link overlay HTML
									wpex_overlay( 'inside_link', $overlay_style, $atts ); ?>

									<?php
									// Close link tag
									if ( 'nowhere' != $thumb_link ) echo '</a>'; ?>

									<?php
									// Outer link overlay HTML
									wpex_overlay( 'outside_link', $overlay_style, $atts ); ?>

								</div><!-- .<?php echo $media_classes; ?> -->

							<?php endif; ?>

						<?php endif; ?>

						<?php
						// Display content if needed
						if ( $entry_has_details ) : ?>
						
							<div class="portfolio-entry-details entry-details wpex-clr<?php if ( $content_css ) echo ' '. $content_css; ?>"<?php echo $content_style; ?>>

								<?php
								// Equal height div
								if ( $equal_heights_grid && ! $is_isotope ) echo '<div class="match-height-content">'; ?>

								<?php
								// Display title
								if ( 'true' == $title ) : ?>

									<<?php echo $title_tag; ?> class="portfolio-entry-title entry-title"<?php echo $heading_style; ?>>

										<?php
										// Display title without link
										if ( 'nowhere' == $title_link ) : ?>

											<?php  echo $post->title; ?>

										<?php
										// Link title to lightbox
										elseif ( 'lightbox' == $title_link ) : ?>

											<?php
											$atts['lightbox_data'] = array();
											// Lightbox data
											if ( $lightbox_skin && 'true' !== $thumb_lightbox_gallery ) {
												$atts['lightbox_data'][] = 'data-skin="'. $lightbox_skin .'"';
											}
											if ( 'true' == $thumb_lightbox_title ) {
												$atts['lightbox_data'][] = 'data-title="'. wpex_get_esc_title() .'"';
											}
											// Display lightbox
											if ( 'true' == $thumb_lightbox_caption && $post->excerpt ) {
												$atts['lightbox_data'][] = 'data-caption="'. str_replace( '"',"'", $post->excerpt ) .'"';
											}
											$lightbox_data = ' '. implode( ' ', $atts['lightbox_data'] ); ?>

											<a href="<?php wpex_lightbox_image(); ?>" title="<?php wpex_esc_title(); ?>" class="wpex-lightbox"<?php echo $heading_link_style; ?><?php echo $lightbox_data; ?>>
												<?php echo $post->title; ?>
											</a>

										<?php
										// Link title to post
										else : ?>

											<a href="<?php echo $post->permalink; ?>" title="<?php wpex_esc_title(); ?>"<?php echo $heading_link_style; ?><?php echo vcex_html( 'target_attr', $link_target ); ?>>
												<?php echo $post->title; ?>
											</a>

										<?php endif ?>

									</<?php echo $title_tag; ?>>

								<?php endif; ?>

								<?php
								// Display categories
								if ( 'true' == $show_categories ) : ?>

									<div class="<?php echo $categories_classes; ?>"<?php echo $categories_style; ?>>
										<?php
										// Display categories
										if ( 'true' == $show_first_category_only ) {
											wpex_first_term_link( $post->ID, 'portfolio_category' );
										} else {
											wpex_list_post_terms( 'portfolio_category', true, true );
										} ?>
									</div><!-- .portfolio-entry-categories -->

								<?php endif; ?>

								<?php
								// Display excerpt
								if ( 'true' == $excerpt && $post->excerpt ) : ?>

									<div class="portfolio-entry-excerpt entry-excerpt wpex-clr"<?php echo $excerpt_style; ?>>
										<?php echo $post->excerpt; ?>
									</div><!-- .portfolio-entry-excerpt -->

								<?php endif; ?>

								<?php
								// Display read more button
								if ( 'true' == $read_more ) : ?>

									<div class="portfolio-entry-readmore-wrap entry-readmore-wrap wpex-clr">

										<a href="<?php echo $post->permalink; ?>" title="<?php echo esc_attr( $read_more_text ); ?>" rel="bookmark" class="<?php echo $readmore_classes; ?>"<?php echo $readmore_style; ?><?php echo $readmore_data; ?><?php echo vcex_html( 'target_attr', $link_target ); ?>>
											<?php echo $read_more_text; ?>
											<?php if ( 'true' == $readmore_rarr ) : ?>
												<span class="vcex-readmore-rarr"><?php echo wpex_element( 'rarr' ); ?></span>
											<?php endif; ?>
										</a>

									</div><!-- .portfolio-entry-readmore-wrap -->

								<?php endif; ?>
								
								<?php
								// Close Equal height container
								if ( $equal_heights_grid && ! $is_isotope ) echo '</div>'; ?>

							</div><!-- .portfolio-entry-details -->

						<?php endif; ?>

					</div><!-- .portfolio-entry-inner -->

				</div><!-- .portfolio-entry -->

				<?php
				// Reset entry counter
				if ( $count == $columns ) $count = ''; ?>
			
			<?php
			// End post loop
			endwhile; ?>

		</div><!-- .vcex-portfolio-grid -->
		
		<?php
		// Display pagination if enabled
		if ( 'true' == $pagination ) {
			wpex_pagination( $wpex_query );
		} ?>

	</div><!-- <?php echo $wrap_classes; ?> -->

	<?php
	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata(); ?>

<?php
// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif; ?>