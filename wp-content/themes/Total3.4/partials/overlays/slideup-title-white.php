<?php
/**
 * Slide Up Title White Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
} ?>

<div class="overlay-slideup-title overlay-hide white clr theme-overlay">
	<span class="title">
		<?php if ( 'staff' == get_post_type() ) {
			echo get_post_meta( get_the_ID(), 'wpex_staff_position', true );
		} else {
			the_title();
		} ?>
	</span>
</div>