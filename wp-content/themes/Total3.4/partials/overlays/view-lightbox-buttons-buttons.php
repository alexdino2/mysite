<?php
/**
 * Lightbox Buttons Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for outside position
if ( 'outside_link' != $position ) {
	return;
}

// Load lightbox skin stylesheet
wpex_enqueue_ilightbox_skin();

// Lightbox
$lightbox_link = ! empty( $args['lightbox_link'] ) ? $args['lightbox_link'] : wpex_get_lightbox_image();
$lightbox_data = ! empty( $args['lightbox_data'] ) ? implode( ' ', $args['lightbox_data'] ) : '';

// Link
$link = isset( $args['overlay_link'] ) ? $args['overlay_link'] : wpex_get_permalink();
$link = apply_filters( 'wpex_lightbox_buttons_button_overlay_link', $link );

// Define link target
$target = '';
if ( isset( $args['link_target'] ) ) {
    if ( 'blank' == $args['link_target'] || '_blank' == $args['link_target'] ) {
        $target = 'blank';
    }
}
$target = apply_filters( 'wpex_button_overlay_target', $target );
$target = 'blank' == $target ? ' target="_blank"' : ''; ?>

<div class="overlay-view-lightbox-buttons overlay-hide theme-overlay">
	<div class="overlay-view-lightbox-buttons-inner clr">
		<div class="overlay-view-lightbox-buttons-buttons clr">
			<a href="<?php echo $lightbox_link; ?>" title="<?php wpex_esc_title(); ?>" class="wpex-lightbox"<?php echo $lightbox_data; ?>><span class="fa fa-search"></span></a>
			<a href="<?php echo $link; ?>" class="view-post" title="<?php wpex_esc_title(); ?>"<?php echo $target; ?>><span class="fa fa-arrow-right"></span></a>
		</div>
	</div>
</div>