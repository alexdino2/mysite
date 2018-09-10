<?php
/**
 * Single Page Layout
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<article class="wpex-clr">

	<?php
	// Check if page should display featured image
	if ( has_post_thumbnail() && wpex_get_mod( 'page_featured_image' ) ) : ?>

		<div id="page-featured-img" class="clr">
			<?php
			// Dislpay full featured image
			wpex_post_thumbnail( array(
				'size'  => 'full',
				'alt'   => wpex_get_esc_title(),
			) ); ?>
		</div><!-- #page-featured-img -->

	<?php endif; ?>

	<div class="entry-content entry clr">

		<?php
		// Output page content
		the_content(); ?>

		<?php
		// Output page pagination
		wp_link_pages( array(
			'before'        => '<div class="page-links clr">',
			'after'         => '</div>',
			'link_before'   => '<span>',
			'link_after'    => '</span>',
		) ); ?>

	</div><!-- .entry-content -->

	<?php
	// Get social sharing template part
	get_template_part( 'partials/social', 'share' ); ?>

</article><!-- #post -->

<?php
// Display comments if enabled
if ( wpex_get_mod( 'page_comments', false ) ) :
	comments_template();
endif; ?>