<?php
/**
 * Single Custom Post Type Media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Thumbnail args
$args = apply_filters( 'wpex_'. get_post_type() .'_single_thumbnail_args', array(
	'size'          => 'full',
	'alt'           => wpex_get_esc_title(),
	'schema_markup' => true
) );

// Get thumbnail
$thumbnail = wpex_get_post_thumbnail( $args );

// Display featured image
if ( $thumbnail ) : ?>

	<div id="post-media" class="wpex-clr">
		<?php echo $thumbnail; ?>
	</div><!-- #post-media -->

<?php endif; ?>