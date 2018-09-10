<?php
/**
 * Visual Composer Milestone
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.0
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

// Sanitize data
$number = isset( $number ) ? $number : '45';
$number = str_replace( ',', '', $number );
$number = round( $number );
$number = str_replace( '.', '', $number );
$number = intval( $number );

// Inline js
vcex_inline_js( 'milestone' );

// Wrapper Classes
$wrap_classes = array( 'vcex-milestone', 'clr' );
if ( 'true' == $animated || 'yes' == $animated ) {
	$wrap_classes[] = 'vcex-animated-milestone';
}
if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $css_animation ) {
	$wrap_classes[] = $this->getCSSAnimation( $css_animation );
}
if ( $hover_animation ) {
	$wrap_classes[] = wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
$wrap_classes[] = vc_shortcode_custom_css_class( $css );
$wrap_classes = implode( ' ', $wrap_classes );

// Wrap style
$wrap_style = vcex_inline_style( array(
	'width'         => $width,
	'border_radius' => $border_radius,
) ); ?>

<?php if ( 'true' == $url_wrap && $url ) : ?>

	<a href="<?php echo esc_url( $url ); ?>" class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_style; ?><?php echo vcex_html( 'rel_attr', $url_rel ); ?><?php echo vcex_html( 'target_attr', $url_target ); ?>>

<?php else : ?>

	<div class="<?php echo $wrap_classes; ?>"<?php vcex_unique_id( $unique_id ); ?><?php echo $wrap_style; ?>>

<?php endif; ?>

	<?php
	// Load custom font
	if ( $number_font_family ) {
		wpex_enqueue_google_font( $number_font_family );
	}

	// Number Style
	$number_style = vcex_inline_style( array(
		'color'         => $number_color,
		'font_size'     => $number_size,
		'margin_bottom' => $number_bottom_margin,
		'font_weight'   => $number_weight,
		'font_family'   => $number_font_family,
	) ); ?>

	<div class="vcex-milestone-number"<?php echo $number_style; ?>>

		<?php if ( $before ) : ?><span class="vcex-milestone-before"><?php echo $before; ?></span><?php endif; ?>
		<span class="vcex-milestone-time" data-from="0" data-to="<?php echo intval( $number ); ?>" data-speed="<?php echo intval( $speed ); ?>" data-refresh-interval="<?php echo intval( $interval ); ?>"><?php echo $number; ?></span><?php if ( $after ) : ?><span class="vcex-milestone-after"><?php echo $after; ?></span><?php endif; ?>

	</div><!-- .vcex-milestone-number -->

	<?php if ( ! empty( $caption ) ) : ?>

		<?php
		// Load custom font
		if ( $caption_font_family ) {
			wpex_enqueue_google_font( $caption_font_family );
		}
		// Caption Style
		$caption_style = vcex_inline_style( array(
			'font_family' => $caption_font_family,
			'color'       => $caption_color,
			'font_size'   => $caption_size,
			'font_weight' => $caption_font,
		) ); ?>
		
		<?php if ( $url && ! $url_wrap ) : ?>

			<a href="<?php echo esc_url( $url ); ?>" class="vcex-milestone-caption"<?php echo vcex_html( 'rel_attr', $url_rel ); ?><?php echo vcex_html( 'target_attr', $url_target ); ?><?php echo $caption_style; ?>><?php echo $caption; ?></a>

		<?php else : ?>

			<div class="vcex-milestone-caption"<?php echo $caption_style; ?>><?php echo $caption; ?></div><!-- .vcex-milestone-caption -->

		<?php endif; ?>
		
	<?php endif; ?>

<?php if ( 'true' == $url_wrap && $url ) : ?>

	</a><!-- .vcex-milestone -->

<?php else : ?>

	</div><!-- .vcex-milestone -->

<?php endif; ?>