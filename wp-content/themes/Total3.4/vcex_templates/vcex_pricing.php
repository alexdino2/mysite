<?php
/**
 * Visual Composer Pricing
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

// Sanitize vars
$inline_js = array();
$button_url = $custom_button ? false : $button_url;

// Wrapper classes
$time_start = microtime( true );
$wrapper_classes = array( 'vcex-pricing' );
if ( 'yes' == $featured ) {
	$wrapper_classes[] = 'featured';
}
if ( $css_animation ) {
	$wrapper_classes[] = $this->getCSSAnimation( $css_animation );
}
if ( $el_class ) {
	$wrapper_classes[] = $this->getExtraClass( $el_class );
}
if ( $visibility ) {
	$wrapper_classes[] = $visibility;
}
if ( $hover_animation ) {
	$wrapper_classes[] = wpex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
$wrapper_classes = implode( ' ', $wrapper_classes );

// Plan style
if ( $plan ) {
	$plan_style = vcex_inline_style( array(
		'margin'         => $plan_margin,
		'padding'        => $plan_padding,
		'background'     => $plan_background,
		'color'          => $plan_color,
		'font_size'      => $plan_size,
		'font_weight'    => $plan_weight,
		'letter_spacing' => $plan_letter_spacing,
		'border'         => $plan_border,
		'text_transform' => $plan_text_transform,
	) );
}

// Cost Wrap style
if ( $cost ) {
	$cost_wrap_style = vcex_inline_style( array(
		'background' => $cost_background,
		'padding'    => $cost_padding,
		'border'    => $cost_border,
	) );
	$cost_style = vcex_inline_style( array(
		'color'       => $cost_color,
		'font_size'   => $cost_size,
		'font_weight' => $cost_weight,
	) );
}

// Per style
if ( $per ) {
	$per_style = vcex_inline_style( array(
		'display'        => $per_display,
		'font_size'      => $per_size,
		'color'          => $per_color,
		'font_weight'    => $per_weight,
		'text_transform' => $per_transform,
	) );
}

// Features Style
if ( $content ) {
	 $features_style = vcex_inline_style( array(
		'padding'    => $features_padding,
		'background' => $features_bg,
		'border'     => $features_border,
		'color'      => $font_color,
		'font_size'  => $font_size,
	) );
}

// Button URL & attributes
if ( $button_url ) {

	$button_url_temp = $button_url;
	$button_url      = vcex_get_link_data( 'url', $button_url_temp );

	if ( $button_url ) {

		$button_title  = vcex_get_link_data( 'title', $button_url_temp );
		$button_target = vcex_get_link_data( 'target', $button_url_temp );
		$button_target = vcex_html( 'target_attr', $button_target );

	}

}

// Button Icons, Classes & Style
if ( $button_url || $custom_button ) {

	// Button Wrap Style
	$button_wrap_style = vcex_inline_style( array(
		'padding'    => $button_wrap_padding,
		'border'     => $button_wrap_border,
		'background' => $button_wrap_bg,
	) );

	// VCEX button styles
	if ( $button_url ) {

		// Get correct icon classes
		$button_icon_left  = vcex_get_icon_class( $atts, 'button_icon_left' );
		$button_icon_right = vcex_get_icon_class( $atts, 'button_icon_right' );

		if ( $button_icon_left || $button_icon_right ) {
			vcex_enqueue_icon_font( $icon_type );
		}

		// Button Classes
		$button_classes = wpex_get_button_classes( $button_style, $button_style_color );
		if ( 'true' == $button_local_scroll ) {
			$button_classes .= ' local-scroll-link'; 
		}
		if ( $button_transform ) {
			$button_classes .= ' text-transform-'. $button_transform;
		}
		if ( $button_hover_bg_color || $button_hover_color ) {
			$button_classes .= ' wpex-data-hover';
			$inline_js[] = 'data_hover';
		}

		// Button Data attributes
		$button_data = array();
		if ( $button_hover_bg_color ) {
			$button_data[] = 'data-hover-background="'. $button_hover_bg_color .'"';
		}
		if ( $button_hover_color ) {
			$button_data[] = 'data-hover-color="'. $button_hover_color .'"';
		}
		$button_data = implode( ' ', $button_data );

		// Button Style
		$border_color = ( 'outline' == $button_style ) ? $button_color : '';
		$button_style = vcex_inline_style( array(
			'background'     => $button_bg_color,
			'color'          => $button_color,
			'letter_spacing' => $button_letter_spacing,
			'font_size'      => $button_size,
			'padding'        => $button_padding,
			'border_radius'  => $button_border_radius,
			'font_weight'    => $button_weight,
			'border_color'   => $border_color,
		) );

	}

}

// Load inline js for the front-end composer
if ( ! empty( $inline_js ) ) {
	vcex_inline_js( $inline_js );
} ?>

<div class="<?php echo $wrapper_classes; ?>"<?php vcex_unique_id( $unique_id ); ?>>

	<?php
	// Display plan
	if ( $plan ) : ?>

		<div class="vcex-pricing-header clr"<?php echo $plan_style; ?>>
			<?php echo $plan; ?>
		</div><!-- .vcex-pricing-header -->

	<?php endif; ?>

	<?php
	// Display cost
	if ( $cost ) : ?>

		<div class="vcex-pricing-cost clr"<?php echo $cost_wrap_style; ?>>
			<div class="vcex-pricing-ammount" <?php echo $cost_style; ?>>
				<?php echo $cost; ?>
			</div><!-- .vcex-pricing-ammount -->
			<?php if ( $per ) { ?>
				<div class="vcex-pricing-per"<?php echo $per_style; ?>>
					<?php echo $per; ?>
				</div><!-- .vcex-pricing-per -->
			<?php } ?>
		</div><!-- .vcex-pricing-cost -->

	<?php endif; ?>

	<?php
	// Display content
	if ( $content ) : ?>

		<div class="vcex-pricing-content"<?php echo $features_style; ?>>
			<?php echo do_shortcode( $content ); ?>
		</div><!-- .vcex-pricing-content -->

	<?php endif; ?>
	
	<?php
	// Display button
	if ( $button_url || $custom_button ) : ?>

		<div class="vcex-pricing-button"<?php echo $button_wrap_style; ?>>

			<?php if ( $custom_button = vcex_parse_textarea_html( $custom_button ) ) : ?>

				<?php echo do_shortcode( $custom_button ); ?>

			<?php elseif ( $button_url ) : ?>

				<a href="<?php echo esc_url( $button_url ); ?>" title="<?php esc_attr( $button_title ); ?>" class="<?php echo $button_classes; ?>"<?php echo $button_target; ?><?php echo $button_style; ?><?php echo $button_data; ?>>
					<?php if ( $button_icon_left ) { ?>
						<span class="vcex-icon-wrap left"><span class="<?php echo $button_icon_left; ?>"></span></span>
					<?php } ?>
					<?php echo $button_text; ?>
					<?php if ( $button_icon_right ) { ?>
						<span class="vcex-icon-wrap right"><span class="<?php echo $button_icon_right; ?>"></span></span>
					<?php } ?>
				</a>
				
			<?php endif; ?>

		</div><!-- .vcex-pricing-button -->

	<?php endif; ?>

</div><!-- .vcex-pricing -->