<?php
/**
 * Title Push Up Overlay
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

// Add javascript for the VC
vcex_inline_js( 'overlay_popup_title' ); ?>

<div class="overlay-title-push-up theme-overlay">
	<span class="title"><?php the_title(); ?></span>
</div>