<?php
/**
 * Title Category Visible Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Get terms
if ( $taxonomy ) {
	$terms = wpex_list_post_terms( $taxonomy, $show_links = false, $echo = false );
} ?>

<div class="overlay-title-category-visible theme-overlay">
	<div class="overlay-title-category-visible-inner clr">
		<div class="overlay-title-category-visible-text clr">
			<div class="overlay-title-category-visible-title">
				<?php the_title(); ?>
			</div>
			<?php if ( ! empty( $terms ) ) : ?>
				<div class="overlay-title-category-visible-category">
					<?php echo $terms; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>