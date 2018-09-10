<?php
/**
 * Visual Composer Post Type Grid
 *
 * UNDER CONSTRUCTION !!!
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.0
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

	// Declare and sanitize variables
	$css_animation = $this->getCSSAnimation( $css_animation );

	// Turn post types into array
	$post_types = $post_types ? $post_types : 'post';
	$post_types = explode( ',', $post_types );

	// Wrap classes
	$wrap_classes = array( 'vcex-post-type-list', 'wpex-clr' );
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}
	$wrap_classes = implode( ' ', $wrap_classes ); ?>

	<div class="<?php echo esc_attr( $wrap_classes ); ?>"<?php echo vcex_unique_id( $unique_id ); ?>>

		<?php
		// Declare counter var
		$count = 0;

		// Loop through posts
		while ( $wpex_query->have_posts() ) :

			// Add to counter
			$count ++;

			// Get post from query
			$wpex_query->the_post();

			// Get post data
			$get_post = get_post();

			// Post Data
			$post_id        = $get_post->ID;
			$post_content   = $get_post->post_content;
			$post_title     = $get_post->post_title;
			$post_type      = $get_post->post_type;
			$post_title_esc = wpex_get_esc_title();
			$post_permalink = wpex_get_permalink( $post_id );
			$post_format    = get_post_format( $post_id );
			$post_thumbnail = wp_get_attachment_url( get_post_thumbnail_id() ); ?>

			<div class="vcex-post-type-list-entry vcex-clr vcex-count-<?php echo $count; ?>">

				<?php
				// Featured post
				if ( 'true' == $featured_post && '1' == $count ) :

					// Featured post => image
					if ( $post_thumbnail ) {
						$post_thumbnail = wpex_get_post_thumbnail( array(
							'size' => $featured_post_img_size,
							'crop' => $featured_post_img_crop,
							'width' => $featured_post_img_width,
							'height' => $featured_post_img_height,
							'alt' => $post_title_esc,
						) );
					} ?>

					<div class="vcex-post-type-list-thumbnail vcex-clr">
						<?php echo $post_thumbnail; ?>
					</div><!-- .vcex-post-type-list-thumbnail -->

					<div class="vcex-post-type-list-title vcex-clr">
						<h2 class="entry-title"><?php echo $post_title; ?></h2>
					</div><!-- .vcex-post-type-list-title -->

					<div class="vcex-post-type-list-meta vcex-clr">
						meta here
					</div><!-- .vcex-post-type-list-meta -->

					<div class="vcex-post-type-list-excerpt vcex-clr">
						excerpt here
					</div><!-- .vcex-post-type-list-excerpt -->


				<?php
				// Standard posts
				else : ?>



				<?php endif; ?>

			</div>

		<?php endwhile; // End main loop ?>

	</div>

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