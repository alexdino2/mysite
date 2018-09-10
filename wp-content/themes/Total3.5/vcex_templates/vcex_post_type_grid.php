<?php
/**
 * Visual Composer Post Type Grid
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Required VC functions
if ( ! function_exists( 'vc_map_get_attributes' ) || ! function_exists( 'vc_shortcode_custom_css_class' ) ) {
	vcex_function_needed_notice();
	return;
}

// Define output var
$output = '';

// Get and extract shortcode attributes
$atts = vc_map_get_attributes( 'vcex_post_type_grid', $atts );
extract( $atts );

// Build the WordPress query
$wpex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $wpex_query->have_posts() ) :

	// Define entry blocks output
	$entry_blocks = apply_filters( 'vcex_post_type_grid_entry_blocks', vcex_filter_grid_blocks_array( array(
		'media'      => $entry_media,
		'title'      => $title,
		'date'       => $date,
		'categories' => $show_categories,
		'excerpt'    => $excerpt,
		'read_more'  => $read_more,
	) ), $atts );

	// Declare and sanitize useful variables
	$wrap_classes       = array( 'vcex-module', 'vcex-post-type-grid-wrap', 'wpex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-post-type-grid', 'entries', 'wpex-clr' );
	$grid_data          = array();
	$is_isotope         = false;
	$url_target         = vcex_html( 'target_attr', $url_target );
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;
	$css_animation      = vcex_get_css_animation( $css_animation );
	$css_animation      = 'true' == $filter ? false : $css_animation;
	$title_tag          = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $atts );
	$title_tag          = $title_tag ? $title_tag : 'h2';

	// Advanced sanitization
	if ( 'true' == $filter || 'masonry' == $grid_style || 'no_margins' == $grid_style ) {
		$is_isotope = true;
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
	if ( 'lightbox' == $thumb_link || 'lightbox_gallery' == $thumb_link ) {
		wpex_enqueue_ilightbox_skin();
	}

	// Turn post types into array
	$post_types = $post_types ? $post_types : 'post';
	$post_types = explode( ',', $post_types );

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

		// Entry CSS class
	if ( $entry_css ) {
		$entry_css = vc_shortcode_custom_css_class( $entry_css );
	}

	// Content Design
	$content_style = array(
		'color'   => $content_color,
		'opacity' => $content_opacity,
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

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_post_type_grid_wrap_classes', $wrap_classes ); // @todo deprecate?
	$grid_classes  = apply_filters( 'vcex_post_type_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_post_type_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC filter
	$wrap_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $wrap_classes, 'vcex_post_type_grid', $atts );

	// Start output
	$output .= '<div class="'. $wrap_classes .'"'. vcex_get_unique_id( $unique_id ) .'>';

		// Display filter links
		if ( 'true' == $filter ) :

			// Make sure the filter should display
			if ( count( $post_types ) > 1 || 'taxonomy' == $filter_type ) {

				// Filter button classes
				$filter_button_classes = wpex_get_button_classes( $filter_button_style, $filter_button_color );

				// Filter font size
				$filter_style = vcex_inline_style( array(
					'font_size' => $filter_font_size,
				) );

				$filter_classes = 'vcex-post-type-filter vcex-filter-links clr';
				if ( 'yes' == $center_filter ) {
					$filter_classes .= ' center';
				}

				$output .= '<ul class="'. $filter_classes .'"'. $filter_style .'>';

					// Sanitize all text
					$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

					$output .= '<li';
						if ( ! $filter_active_category ) {
							$output .= ' class="active"';
						}
					$output .= '>';

						$output .= '<a href="#" data-filter="*" class="'. $filter_button_classes .'"><span>'. $all_text .'</span></a>';

					$output .= '</li>';

					// Taxonomy style filter
					if ( 'taxonomy' == $filter_type ) :

						// If taxonony exists get terms
						if ( taxonomy_exists( $filter_taxonomy ) ) :

							// Get filter args
							$atts['filter_taxonomy'] = $filter_taxonomy;
							$args  = vcex_grid_filter_args( $atts, $wpex_query );
							$terms = get_terms( $filter_taxonomy, $args );

							// Set correct filter class prefix
							$filter_prefix = $atts['filter_taxonomy'];
							if ( 'post_tag' == $filter_prefix ) {
								$filter_prefix = $filter_prefix;
							} elseif ( 'category' == $filter_prefix ) {
								$filter_prefix = str_replace( 'category', 'cat', $filter_prefix );
							} else {
								$parse_types   = wpex_theme_post_types();
								$parse_types[] = 'post';
								foreach ( $parse_types as $type ) {
									if ( strpos( $filter_prefix, $type ) !== false ) {
										$search  = array( $type .'_category', 'category', $type .'_tag' );
										$replace = array( 'cat', 'cat', 'tag' );
										$filter_prefix = str_replace( $search, $replace, $filter_prefix );
									}
								}
							}

							// Display filter
							if ( ! empty( $terms ) ) :

								foreach ( $terms as $term ) :

									$output .= '<li class="filter-cat-'. $term->term_id .'">';
										
										$output .= '<a href="#" data-filter=".'. $filter_prefix .'-'. $term->term_id .'" class="'. $filter_button_classes .'">';
											
											$output .= $term->name;
										
										$output .= '</a>';
									
									$output .= '</li>';

								endforeach;

							endif; // Terms check

						endif; // Taxonomy exists check

					// Post types filter
					else :

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

							$output .= '<li class="vcex-filter-link-'. $type;
								if ( $filter_active_category == 'post-type-'. $type ) {
									$output .= ' active';
								}
							$output .= '">';

							$output .= '<a href="#" data-filter=".type-'. $type .'" class="'. $filter_button_classes .'">';
								$output .= $obj->labels->name;
							$output .= '</a></li>';

						endforeach;

					endif;

				$output .= '</ul>';

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) { 
					$output .= $vcex_after_grid_filter;
				}

			}

		endif; // End filter

		$output .= '<div class="'. $grid_classes .'"'. $grid_data .'>';

			// Categories style
			if ( isset( $entry_blocks['categories'] ) ) {
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
			if ( isset( $entry_blocks['excerpt'] ) ) {
				$excerpt_style = vcex_inline_style( array(
					'font_size' => $content_font_size,
					'color'     => $content_color,
				) );
			}

			// Heading Design
			if ( isset( $entry_blocks['title'] ) ) {
				$heading_style = vcex_inline_style( array(
					'margin'         => $content_heading_margin,
					'font_size'      => $content_heading_size,
					'color'          => $content_heading_color,
					'line_height'    => $content_heading_line_height,
					'text_transform' => $content_heading_transform,
					'font_weight'    => $content_heading_weight,
				) );
				$heading_link_style = vcex_inline_style( array(
					'color' => $content_heading_color,
				) );
			}

			// Readmore design and classes
			if ( isset( $entry_blocks['read_more'] ) ) {

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
				$readmore_data = ' '. implode( ' ', $readmore_data );

			}

			// Date design
			if ( isset( $entry_blocks['date'] ) ) {
				$date_style = vcex_inline_style( array(
					'color'     => $date_color,
					'font_size' => $date_font_size,
				) );
			}

			// Static entry classes
			$static_entry_classes = array( 'vcex-post-type-entry', 'clr' );
			if ( 'false' == $columns_responsive ) {
				$static_entry_classes[] = 'nr-col';
			} else {
				$static_entry_classes[] = 'col';
			}
			$static_entry_classes[] = vcex_get_grid_column_class( $atts );
			if ( $is_isotope ) {
				$static_entry_classes[] = 'vcex-isotope-entry';
			}
			if ( 'no_margins' == $grid_style ) {
				$static_entry_classes[] = 'vcex-no-margin-entry';
			}
			if ( $css_animation ) {
				$static_entry_classes[] = $css_animation;
			}
			if ( $content_alignment ) {
				$static_entry_classes[] = 'text'. $content_alignment;
			}

			// Entry media classes
			$media_classes = array( 'vcex-post-type-entry-media', 'entry-media', 'wpex-clr' );
			if ( isset( $entry_blocks['media'] ) ) {
				if ( $img_filter ) {
					$media_classes[] = wpex_image_filter_class( $img_filter );
				}
				if ( $img_hover_style ) {
					$media_classes[] = wpex_image_hover_classes( $img_hover_style );
				}
				if ( $overlay_style ) {
					$media_classes[] = wpex_overlay_classes( $overlay_style );
				}
			} else {
				$static_entry_classes[] = 'vcex-post-type-no-media-entry';
			}
			$media_classes = implode( ' ', $media_classes );

			// Define counter var to clear floats
			$count=0;

			/**** Loop Start ***/
			while ( $wpex_query->have_posts() ) :

				// Get post from query
				$wpex_query->the_post();

				// Add to counter var
				$count++;

				// Set post ID
				$atts['post_id'] = get_the_ID();
				$post_id = $atts['post_id'];

				// Get post data
				$atts['post_type']         = get_post_type( $post_id );
				$atts['post_title']        = get_the_title();
				$atts['post_esc_title']    = wpex_get_esc_title();
				$atts['post_permalink']    = wpex_get_permalink( $post_id );
				$atts['post_format']       = get_post_format( $post_id );
				$atts['post_excerpt']      = '';
				$atts['post_thumbnail_id'] = get_post_thumbnail_id( $post_id );
				$atts['post_video_html']   = ( 'true' == $featured_video ) ? wpex_get_post_video_html() : '';
				$atts['lightbox_data']     = array();

				// Entry Classes
				$entry_classes   = array();
				$entry_classes[] = 'col-'. $count;
				$entry_classes   = array_merge( $static_entry_classes, $entry_classes );

				// Entry image output HTML
				if ( $atts['post_thumbnail_id'] ) {
					$entry_image = wpex_get_post_thumbnail( array(
						'attachment'    => $atts['post_thumbnail_id'],
						'size'          => $img_size,
						'crop'          => $img_crop,
						'width'         => $img_width,
						'height'        => $img_height,
						'alt'           => $atts['post_esc_title'],
						'apply_filters' => 'vcex_post_type_grid_thumbnail_args',
					) );
				}

				// Get and save Lightbox data for use with Overlays, media, title, etc
				$oembed_video_url = wpex_get_post_video_oembed_url( $post_id );
				$embed_url = $oembed_video_url ? wpex_get_video_embed_url( $oembed_video_url ) : '';
				if ( $embed_url ) {
					$atts['lightbox_link']                 = $embed_url;
					$atts['lightbox_data']['data-type']    = 'data-type="iframe"';
					$atts['lightbox_data']['data-options'] = 'data-options="iframeType:\'video\',thumbnail:\''. $atts['lightbox_link'] .'\'"';
				} else {
					$atts['lightbox_link'] = wpex_get_lightbox_image( $atts['post_thumbnail_id'] );
				}

				// Apply filters to attributes
				$latts = apply_filters( 'vcex_shortcode_loop_atts', $atts );

				// Begin entry output
				$output .= '<div '. vcex_grid_get_post_class( $entry_classes, $post_id ) .'>';

					// Inner entry classes
					$classes = 'vcex-post-type-entry-inner entry-inner clr';
					if ( $entry_css ) {
						$classes .= ' '. $entry_css;
					}

					// Inner entry output
					$output .= '<div class="'. $classes .'">';

						// Display media
						if ( isset( $entry_blocks['media'] ) ) {

							// Custom output
							if ( is_callable( $entry_blocks['media'] ) ) {
								$output .= call_user_func( $entry_blocks['media'] );
							}

							// Default module output
							else {

								// Display video
								if ( $latts['post_video_html'] ) {

									$output .= '<div class="vcex-post-type-entry-media entry-media clr">';

										$output .= '<div class="vcex-video-wrap">';

											$output .= $latts['post_video_html'];

										$output .= '</div>';

									$output .= '</div>';

								// Display featured image
								} elseif ( $latts['post_thumbnail_id'] ) {

									$media_link_attrs = array(
										'href'   => $atts['post_permalink'],
										'title'  => $latts['post_esc_title'],
										'target' => $latts['url_target'],
										'class'  => '',
									);

									$output .= '<div class="'. $media_classes .'">';

										// Image with link
										if ( $thumb_link == 'post'
											|| $thumb_link == 'lightbox'
											|| $thumb_link == 'lightbox_gallery'
										) {

											// Lightbox
											if ( $thumb_link == 'lightbox' || 'lightbox_gallery' == $latts['thumb_link'] ) {

												// Lightbox gallery
												if ( 'lightbox_gallery' == $latts['thumb_link'] && $lightbox_gallery_imgs = wpex_get_gallery_images( $latts['post_id'], 'lightbox' ) ) {
													$media_link_attrs['class'] .= ' wpex-lightbox-gallery';
													$media_link_attrs['data']   = 'data-gallery="'. implode( ',', $lightbox_gallery_imgs ) .'"';
												}

												// Singular lightbox
												elseif ( ! empty( $latts['lightbox_link'] ) ) {
													$media_link_attrs['class'] .= ' wpex-lightbox';
													$media_link_attrs['href']   = $latts['lightbox_link'];
													$media_link_attrs['data']   = $latts['lightbox_data'];
													$media_link_attrs['target'] = '';
												}

											} else {

												// Lightbox disabled
												$latts['lightbox_link'] = null; // prevents issues w/ overlay button hover

											}

											$media_link_attrs = wpex_parse_attrs( $media_link_attrs );

											$output .= '<a '. $media_link_attrs .'>';

												$output .= $entry_image;

												if ( $overlay_style && 'none' != $overlay_style ) {
													ob_start();
													wpex_overlay( 'inside_link', $overlay_style, $latts );
													$output .= ob_get_clean();
												}

											$output .= '</a>';

										// Just the image
										} else {

											// Display image
											$output .= $entry_image;

											// Inside overlay
											if ( $overlay_style && 'none' != $overlay_style ) {
												ob_start();
												wpex_overlay( 'inside_link', $overlay_style, $latts );
												$output .= ob_get_clean();
											}

										}

										// Outside link overlay
										if ( $overlay_style && 'none' != $overlay_style ) {
											ob_start();
											wpex_overlay( 'outside_link', $overlay_style, $latts );
											$output .= ob_get_clean();
										}

									$output .= '</div>';

								}

							}

						} // End media check

						// Display entry details (title, date, categories, excerpt, button )
						if ( isset( $entry_blocks['title'] )
							|| isset( $entry_blocks['date'] )
							|| isset( $entry_blocks['categories'] )
							|| isset( $entry_blocks['excerpt'] )
							|| isset( $entry_blocks['read_more'] )
						) {

							$classes = 'vcex-post-type-entry-details entry-details wpex-clr';
							if ( $content_css ) {
								$classes .= ' '. $content_css;
							}

							$output .= '<div class="'. $classes .'"'. $content_style .'>';

								// Open equal heights wrapper
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								// Entry blocks (excerpt media since it's inside it's own wrapper)
								foreach ( $entry_blocks as $k => $v ) :

									// Media shouldn't be here
									if ( 'media' == $k ) {
										continue;
									}

									// Custom output
									elseif ( $v && is_callable( $v ) ) {
										$output .= call_user_func( $v );
									}

									// Entry title
									elseif ( 'title' == $k ) {
									
										$output .= '<'. $title_tag .' class="vcex-post-type-entry-title entry-title" '. $heading_style .'>';
										
										if ( 'post' == $title_link ) {
										
											$output .= '<a href="'. $latts['post_permalink'] .'" title="'. $latts['post_esc_title'] .'"'. $url_target .''. $heading_link_style .'>';
												$output .= $latts['post_title'];
											$output .= '</a>';

										} else {

											$output .= $latts['post_title'];
											
										}

										$output .= '</'. $title_tag .' >';

									}


									// Entry date
									elseif ( 'date' == $k ) {

										$output .= '<div class="vcex-post-type-entry-date"'. $date_style .'>';

											// Get Tribe Events date
											if ( 'tribe_events' == $latts['post_type']
												&& function_exists( 'wpex_get_tribe_event_date' )
											) {
												$instance = $unique_id ? $unique_id : 'vcex_post_type_grid';
												$latts['post_date'] = wpex_get_tribe_event_date( $instance );

											// Get standard date
											} else {
												$latts['post_date'] = get_the_date();
											}

											// Output date
											$output .= $latts['post_date'];

										$output .= '</div>';

									}

									// Display categories
									elseif ( 'categories' == $k ) {

										if ( taxonomy_exists( $categories_taxonomy ) ) {

											$output .= '<div class="'. $categories_classes .'"'. $categories_style .'>';
												// Display categories
												if ( 'true' == $show_first_category_only ) {
													$output .= wpex_get_first_term_link( $latts['post_id'], $categories_taxonomy );
												} else {
													$output .= wpex_get_list_post_terms( $categories_taxonomy, true, true );
												}
											$output .= '</div>';

										}

									}

									// Display excerpt
									elseif ( 'excerpt' == $k ) {

										$output .= '<div class="vcex-post-type-entry-excerpt entry-excerpt clr"'. $excerpt_style .'>';

											// Display Excerpt
											$output .= wpex_get_excerpt( array(
												'length'  => $excerpt_length,
												'context' => 'vcex_post_type_grid',
											) );

										$output .= '</div>';

									}

									// Display read more button
									elseif ( 'read_more' == $k ) {

										$output .= '<div class="vcex-post-type-entry-readmore-wrap entry-readmore-wrap clr">';

											$output .= '<a href="'. $latts['post_permalink'] .'" title="'. esc_attr( $read_more_text ) .'" rel="bookmark" class="'. $readmore_classes .'"'. $url_target .''. $readmore_style .''. $readmore_data .'>';
												$output .= $read_more_text;
												if ( 'true' == $readmore_rarr ) :
													$output .= '<span class="vcex-readmore-rarr">'. wpex_element( 'rarr' ) .'</span>';
												endif;
											$output .= '</a>';

										$output .= '</div>';

									}
									
								// End entry blocks
								endforeach;

								// Close equal heights wrap
								if ( $equal_heights_grid ) {
									$output .= '</div>';
								}

							$output .= '</div>';

						}

					$output .= '</div>';

				$output .= '</div>';

			// Reset count clear floats
			if ( $count == $columns ) {
				$count = 0;
			}

			endwhile;

		$output .= '</div>';
		
		// Display pagination if enabled
		if ( 'true' == $pagination
			|| ( 'true' == $atts['custom_query'] && ! empty( $wpex_query->query['pagination'] ) )
		) {
			$output .= wpex_pagination( $wpex_query, false );
		}

	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

	// Output shortcode HTML
	echo $output;

// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;