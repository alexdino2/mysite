<?php
/**
 * Title Excerpt Hover Overlay
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

// Get excerpt length
$excerpt_length = isset( $args['overlay_excerpt_length'] ) ? $args['overlay_excerpt_length'] : 15;

// Generate Excerpt
$excerpt = wpex_get_excerpt( array(
	'length' => $excerpt_length,
) );

// Make sure excerpt isn't too long when custom
if ( strlen( $excerpt ) > $excerpt_length ) {
	$excerpt = wp_trim_words( $excerpt, $excerpt_length );
} ?>

<div class="overlay-title-excerpt-hover overlay-hide theme-overlay">
	<div class="overlay-title-excerpt-hover-inner clr">
		<div class="overlay-title-excerpt-hover-text clr">
			<h3 class="overlay-title-excerpt-hover-title"><?php the_title(); ?></h3>
			<?php if ( $excerpt ) : ?>
				<div class="overlay-title-excerpt-hover-excerpt"><?php echo $excerpt; ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>