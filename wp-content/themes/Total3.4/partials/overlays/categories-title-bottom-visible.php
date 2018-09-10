<?php
/**
 * Categories + Title Bottom Visible
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.3.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'outside_link' != $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Get terms
if ( $taxonomy ) {
	$terms = wpex_list_post_terms( $taxonomy, $show_links = true, $echo = false );
} ?>

<div class="overlay-cats-title-btm-v theme-overlay">
	<?php if ( ! empty( $terms ) ) : ?>
		<div class="overlay-cats-title-btm-v-cats clr"><?php echo $terms; ?></div>
	<?php endif; ?>
	<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="overlay-cats-title-btm-v-title entry-title"><?php the_title(); ?></a>
</div>