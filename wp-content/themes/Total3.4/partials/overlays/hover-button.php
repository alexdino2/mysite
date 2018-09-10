<?php
/**
 * Hover Button Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.3.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only used for outside_link position
if ( 'outside_link' != $position ) {
    return;
}

// Lightbox
$lightbox_link = ! empty( $args['lightbox_link'] ) ? $args['lightbox_link'] : '';
$lightbox_data = ! empty( $args['lightbox_data'] ) ? $args['lightbox_data'] : '';
$lightbox_data = ( is_array( $lightbox_data ) ) ? implode( ' ', $lightbox_data ) : $lightbox_data;

// Link
$link = $lightbox_link ? $lightbox_link : wpex_get_permalink();
$link = ! empty( $args['overlay_link'] ) ? $args['overlay_link'] : $link;
$link = apply_filters( 'wpex_hover_button_overlay_link', $link );

// Text
$text = ! empty( $args['overlay_button_text'] ) ? $args['overlay_button_text'] : esc_html__( 'View Post', 'total' );
$text = ( 'post_title' == $text ) ? get_the_title() : $text;
$text = apply_filters( 'wpex_hover_button_overlay_text', $text );

// Define link target
$target = '';
if ( isset( $args['link_target'] ) ) {
    if ( 'blank' == $args['link_target'] || '_blank' == $args['link_target'] ) {
        $target = 'blank';
    }
}
$target = apply_filters( 'wpex_button_overlay_target', $target );
$target = 'blank' == $target ? ' target="_blank"' : ''; ?>

<div class="overlay-hover-button overlay-hide theme-overlay">
    <div class="overlay-hover-button-inner clr">
        <div class="overlay-hover-button-text clr">
            <a href="<?php echo $link; ?>" class="overlay-hover-button-link theme-button minimal-border white<?php if ( $lightbox_link ) echo ' wpex-lightbox'; ?>" title="<?php echo esc_attr( $text ); ?>"<?php echo $target; ?><?php echo $lightbox_data; ?>>
                <?php echo $text; ?>
            </a>
        </div>
    </div>
</div>