<?php
/**
 * Title Date Hover Overlay
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
} ?>

<div class="overlay-title-date-hover overlay-hide theme-overlay">
	<div class="overlay-title-date-hover-inner clr">
		<div class="overlay-title-date-hover-text clr">
			<div class="overlay-title-date-hover-title"><?php the_title(); ?></div>
			<div class="overlay-title-date-hover-date"><?php echo get_the_date(); ?></div>
		</div>
	</div>
</div>