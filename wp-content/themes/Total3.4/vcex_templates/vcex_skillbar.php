<?php
/**
 * Visual Composer Skillbar
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
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

// Load inline js
vcex_inline_js( array( 'skillbar' ) );

// Classes
$wrapper_classes = 'vcex-skillbar clr';
if ( 'false' == $box_shadow ) {
   $wrapper_classes .= ' disable-box-shadow';
}
if ( $visibility ) {
    $wrapper_classes .= ' '. $visibility;
}
if ( $css_animation ) {
	$wrapper_classes .= $this->getCSSAnimation( $css_animation );
}
if ( $classes ) {
	$wrapper_classes .= $this->getExtraClass( $classes );
}

// Set icon and enqueue font styles
if ( 'true' == $show_icon ) {
	$icon = vcex_get_icon_class( $atts, 'icon' );
	if ( $icon && 'fontawesome' != $icon_type ) {
		vcex_enqueue_icon_font( $icon_type );
	}
}

// Style
$wrapper_style = vcex_inline_style( array(
	'background' => $background,
	'font_size' => $font_size,
	'height_px' => $container_height,
	'line_height_px' => $container_height,
) );
$title_style = vcex_inline_style( array(
	'background' => $color,
	'padding_left' => $container_padding_left,
) );
$bar_style = vcex_inline_style( array(
	'background' => $color,
) ); ?>

<div class="<?php echo $wrapper_classes; ?>" data-percent="<?php echo intval( $percentage ); ?>&#37;"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrapper_style; ?>>

	<div class="vcex-skillbar-title"<?php echo $title_style; ?>>

		<div class="vcex-skillbar-title-inner">
			<?php if ( 'true' == $show_icon && $icon ) : ?>
				<span class="vcex-icon-wrap"><span class="<?php echo $icon; ?>"></span></span>
			<?php endif; ?>
			<?php echo $title; ?>
		</div><!-- .vcex-skillbar-title-inner -->

	</div><!-- .vcex-skillbar-title -->

	<?php if ( $percentage ) : ?>
		<div class="vcex-skillbar-bar"<?php echo $bar_style; ?>>
			<?php if ( 'true' == $show_percent ) : ?>
				<div class="vcex-skill-bar-percent"><?php echo intval( $percentage ); ?>&#37;</div>
			<?php endif; ?>
		</div><!-- .vcex-skillbar -->
	<?php endif; ?>

</div><!-- .vcex-skillbar -->