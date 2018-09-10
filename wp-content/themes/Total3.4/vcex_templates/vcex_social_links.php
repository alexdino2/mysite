<?php
/**
 * Visual Composer Social Links
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

// Get array of social links to loop through
$social_links = vcex_social_links_profiles();

// Return if $sociail_links is empty
if ( empty ( $social_links ) ) {
	return;
}

// Get and extract shortcode attributes
extract( vc_map_get_attributes( $this->getShortcode(), $atts ) );

// Wrap classes
if ( $style ) {
	$wrap_classes = 'wpex-social-btns';
} else {
	$wrap_classes = 'vcex-social-links';
}
if ( $align ) {
	$wrap_classes .= ' text'. $align;
}
if ( $visibility ) {
	$wrap_classes .= ' '. $visibility;
}
if ( $css_animation ) {
	$wrap_classes .= $this->getCSSAnimation( $css_animation );
}
if ( $classes ) {
	$wrap_classes .= $this->getExtraClass( $classes );
}

// Wrap style
$wrap_style = vcex_inline_style( array(
	'color'         => $color,
	'font_size'     => $size,
	'border_radius' => $border_radius,
) );

// Link style
$link_style = vcex_inline_style( array(
	'width'       => $width,
	'height'      => $height,
	'line_height' => $height ? intval( $height ) .'px' : '',
) );

// Link Attributes
$attributes = '';
if ( $link_style ) {
	$attributes .= $link_style;
}
if ( 'blank' == $link_target || '_blank' == $link_target ) {
	$attributes .= ' target="_blank"';
}
if ( $hover_bg ) {
	$attributes .= ' data-hover-background="'. $hover_bg .'"';
}
if ( $hover_color ) {
	$attributes .= ' data-hover-color="'. $hover_color .'"';
}

// Link Classes
if ( $style ) {
	$a_classes = wpex_get_social_button_class( $style );
} else {
	$a_classes = 'vcex-social-link';
}
if ( $width || $height ) {
	$a_classes .= ' no-padding';
}
if ( $hover_bg || $hover_color ) {
   $a_classes .= ' wpex-data-hover';
   vcex_inline_js( array( 'data_hover' ) );
}
if ( $hover_animation ) {
	$a_classes .= ' '. wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
if ( $css ) {
	$a_classes .= ' '. vc_shortcode_custom_css_class( $css );
} ?>

<div class="<?php echo $wrap_classes; ?>"<?php echo $wrap_style; ?><?php vcex_unique_id( $unique_id ); ?>>

	<?php
	// Loop through social options and display if set
	foreach ( $social_links as $key => $val ) : ?>

		<?php
		// Sanitize classname
		$profile_class = $key;
		$profile_class = 'googleplus' == $key ? 'google-plus' : $key;

		// If url field is empty check next profile
		if ( empty( $atts[$key] ) ) continue; ?>

		<a href="<?php echo esc_url( $atts[$key] ); ?>" class="<?php echo $a_classes; ?> wpex-<?php echo $profile_class; ?>"<?php echo $attributes; ?>><span class="<?php echo $val['icon_class']; ?>"></span></a>

	<?php endforeach; ?>

</div><!-- .<?php echo $wrap_classes; ?> -->