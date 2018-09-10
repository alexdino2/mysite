<?php
/**
 * Visual Composer Terms Grid
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

// Taxonomy is required
if ( ! $taxonomy ) {
	return;
}

// Sanitize data
$title_typo              = vcex_parse_typography_param( $title_typo );
$title_font_family       = $title_font_family ? $title_font_family : $title_typo['font_family']; // Fallback
$title_tag               = ! empty( $title_typo['tag'] ) ? $title_typo['tag'] : 'h2';
$description_typo        = vcex_parse_typography_param( $description_typo );
$description_font_family = $description_font_family ? $description_font_family : $description_typo['font_family']; // Fallback

// Remove useless align
if ( isset( $title_typo['text_align'] ) && 'left' == $title_typo['text_align'] ) {
	unset( $title_typo['text_align'] );
}
if ( isset( $description_typo['text_align'] ) && 'left' == $description_typo['text_align'] ) {
	unset( $description_typo['text_align'] );
}

// Load Google Fonts if needed
if ( $title_font_family ) {
	unset( $title_typo['font_family'] ); // Fallback
	wpex_enqueue_google_font( $title_font_family );
}
if ( $description_font_family ) {
	unset( $description_typo['font_family'] ); // Fallback
	wpex_enqueue_google_font( $description_font_family );
}

// Get terms
if ( $parent_terms ) {
	$terms = get_terms( $taxonomy, array( 'parent' => 0 ) );
} else {
	$terms = get_terms( $taxonomy );
}

// Get term thumbnails
$term_data = wpex_get_term_data();

// Define post type based on the taxonomy
$taxonomy  = get_taxonomy( $taxonomy );
$post_type = $taxonomy->object_type[0];

// Grid classes
$grid_classes = array( 'vcex-terms-grid', 'wpex-row', 'clr' );
if ( 'masonry' == $grid_style ) {
	$grid_classes[] = 'vcex-isotope-grid';
	vcex_inline_js( 'isotope' );
}
if ( $columns_gap ) {
	$grid_classes[] = 'gap-'. $columns_gap;
}
if ( $visibility ) {
	$grid_classes[] = $visibility;
}
if ( $classes ) {
	$grid_classes[] = vcex_get_extra_class( $classes );
}
$grid_classes = implode( ' ', $grid_classes );

// Entry classes
$entry_classes = array( 'vcex-terms-grid-entry', 'clr' );
if ( 'masonry' == $grid_style ) {
	$entry_classes[] = 'vcex-isotope-entry';
}
$entry_classes[] = 'span_1_of_'. $columns;
if ( 'false' == $columns_responsive ) {
	$entry_classes[] = 'nr-col';
} else {
	$entry_classes[] = 'col';
} 
if ( $css_animation ) {
	$entry_classes[] = $this->getCSSAnimation( $css_animation );
}
$entry_classes = implode( ' ', $entry_classes );

// Entry CSS wrapper
if ( $entry_css ) {
	$entry_css_class = vc_shortcode_custom_css_class( $entry_css );
}

// Image classes
$media_classes = array( 'vcex-terms-grid-entry-image', 'wpex-clr' );
if ( 'true' == $title_overlay ) {
	$media_classes[] = 'vcex-has-overlay';
}
if ( $img_filter ) {
	$media_classes[] = wpex_image_filter_class( $img_filter );
}
if ( $img_hover_style ) {
	$media_classes[] = wpex_image_hover_classes( $img_hover_style );
}
$media_classes = implode( ' ', $media_classes );

// Title style
$title_style = array(
	'font_family'   => $title_font_family,
	'font_weight'   => $title_font_weight,
	'margin_bottom' => $title_bottom_margin,
);
$title_style = $title_typo + $title_style;
$title_style = vcex_inline_style( $title_style );

// Description style
$description_font_family = array( 'font_family' => $description_font_family );
$description_typo        = $description_typo + $description_font_family;
$description_style       = vcex_inline_style( $description_typo ); ?>

<div class="<?php echo $grid_classes; ?>">
		
	<?php
	// Start counter
	$counter = 0;

	// Loop through terms
	foreach( $terms as $term ) :

		// Add to counter
		$counter++; ?>

		<div class="<?php echo $entry_classes; ?> term-<?php echo $term->term_id; ?> <?php echo $term->slug; ?> col-<?php echo $counter; ?>">

			<?php if ( $entry_css && $entry_css_class ) { ?>
				<div class="<?php echo $entry_css_class; ?>">
			<?php } ?>

				<?php
				// Display image if enabled
				if ( 'true' == $img ) :

					// Check meta for featured image
					$img_id = '';

					// Check wpex_term_thumbnails option for custom category image
					if ( ! empty( $term_data[$term->term_id]['thumbnail'] ) ) {
						$img_id = $term_data[$term->term_id]['thumbnail'];
					}

					// Get woo product image
					elseif ( 'product' == $post_type && function_exists( 'get_woocommerce_term_meta' ) ) {
						$img_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
					}

					// Image not defined via meta, display image from first post in term
					if ( ! $img_id ) : ?>

						<?php
						// Query first post in term
						$my_query = new WP_Query( array(
							'post_type'      => $post_type,
							'posts_per_page' => '1',
							'no_found_rows'  => true,
							'tax_query'      => array(
								array(
									'taxonomy' => $term->taxonomy,
									'field'    => 'id',
									'terms'    => $term->term_id,
								)
							),
						) );

						// Get featured image of first post
						if ( $my_query->have_posts() ) {

							while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

								<?php $img_id = get_post_thumbnail_id(); ?>

							<?php endwhile;

						}

						// Reset query
						wp_reset_postdata(); ?>

					<?php endif; ?>

					<?php if ( $img_id ) : ?>

						<div class="<?php echo $media_classes; ?>">
							<a href="<?php echo get_term_link( $term, $taxonomy ); ?>" title="<?php echo $term->name; ?>">
								<?php
								// Display post thumbnail
								wpex_post_thumbnail( array(
									'attachment' => $img_id,
									'width'      => $img_width,
									'height'     => $img_height,
									'crop'       => $img_crop,
									'alt'        => $term->name,
									'size'       => $img_size,
								) ); ?>
								<?php
								// Overlay title
								if ( 'true' == $title_overlay && 'true' == $title && ! empty( $term->name ) ) : ?>
									<div class="vcex-terms-grid-entry-overlay wpex-clr">
										<div class="vcex-terms-grid-entry-overlay-table wpex-clr">
											<div class="vcex-terms-grid-entry-overlay-cell wpex-clr">
												<<?php echo $title_tag; ?> class="vcex-terms-grid-entry-title entry-title"<?php echo $title_style; ?>>
													<span><?php echo $term->name; ?></span>
													<?php if ( 'true' == $term_count ) { ?>
														<span class="vcex-terms-grid-entry-count">(<?php echo $term->count; ?>)</span>
													<?php } ?>
												</<?php echo $title_tag; ?>>
											</div>
										</div>
									</div>
								<?php endif; ?>
							</a>
						</div><!-- .image -->

					<?php
					// End img ID check
					endif; ?>

				<?php
				// End image check
				endif; ?>

				<?php
				// Inline title and description
				if ( 'false' == $title_overlay ) : ?>

					<?php
					// Show title
					if ( 'true' == $title && ! empty( $term->name ) ) : ?>

						<<?php echo $title_tag; ?> class="vcex-terms-grid-entry-title entry-title"<?php echo $title_style; ?>>
							<a href="<?php echo get_term_link( $term, $taxonomy ); ?>" title="<?php echo $term->name; ?>">
								<?php echo $term->name; ?>
							</a>
						</<?php echo $title_tag; ?>>

					<?php endif; ?>

					<?php
					// Display term description
					if ( 'true' == $description && ! empty( $term->description ) ) : ?>

						<div class="vcex-terms-grid-entry-excerpt clr"<?php echo $description_style; ?>>
							<?php echo $term->description; ?>
						</div><!-- .vcex-terms-grid-entry-excerpt -->

					<?php endif; ?>

				<?php endif; ?>

			</div><!-- .vcex-terms-grid-entry -->

		<?php if ( $entry_css && $entry_css_class ) { ?>
			</div>
		<?php } ?>

		<?php
		// Clear counter
		if ( $counter == $columns ) $counter = 0; ?>

	<?php endforeach; ?>

</div><!-- .vcex-terms-grid -->