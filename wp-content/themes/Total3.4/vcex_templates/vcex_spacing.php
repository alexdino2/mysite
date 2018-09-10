<?php
/**
 * Visual Composer Spacing
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Not needed in admin ever
if ( is_admin() ) {
    return;
}

// Get and extract shortcode attributes
extract( vc_map_get_attributes( $this->getShortcode(), $atts ) );

// Core class
$classes = 'vcex-spacing';

// Custom Class
if ( $class ) {
    $classes .= $this->getExtraClass( $class );
}

// Visiblity Class
if ( $visibility ) {
    $classes .= ' '. $visibility;
}

// Front-end composer class
if ( vc_is_inline() ) {
    $classes .= ' vc-spacing-shortcode';
} ?>

<div class="<?php echo $classes; ?>" style="height:<?php echo wpex_sanitize_data( $size, 'px-pct' ); ?>"></div>