<?php
/**
 * Visual Composer Searchbar
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

// Sanitize
$placeholder = $placeholder ? $placeholder : esc_html__( 'Keywords...', 'total' );
$button_text = $button_text ? $button_text : esc_html__( 'Search', 'total' );

// Wrap Classes
$wrap_classes = 'vcex-searchbar clr';
if ( $visibility ) {
	$wrap_classes .= ' '. $visibility;
}
if ( $classes ) {
	$wrap_classes .= $this->getExtraClass( $classes );
}
if ( $css_animation ) {
	$wrap_classes .= $this->getCSSAnimation( $css_animation );
}

// Form classes
$input_classes = 'vcex-searchbar-input';
$input_classes .= ' '. vc_shortcode_custom_css_class( $css );

// Input style
$input_style = vcex_inline_style( array(
	'color'          => $input_color,
	'font_size'      => $input_font_size,
	'text_transform' => $input_text_transform,
	'letter_spacing' => $input_letter_spacing,
	'font_weight'    => $input_font_weight,
) );

// Button style
$button_style = vcex_inline_style( array(
	'width'          => $button_width,
	'background'     => $button_bg,
	'color'          => $button_color,
	'font_size'      => $button_font_size,
	'text_transform' => $button_text_transform,
	'letter_spacing' => $button_letter_spacing,
	'font_weight'    => $button_font_weight,
	'border_radius'  => $button_border_radius,
) );

// Button classes and data
$button_classes = 'vcex-searchbar-button';
$button_data = '';
if ( $button_bg_hover ) {
	$button_data .= ' data-hover-background="'. $button_bg_hover .'"';
}
if ( $button_color_hover ) {
	$button_data .= ' data-hover-color="'. $button_color_hover .'"';
}
if ( $button_bg_hover || $button_color_hover ) {
	$button_classes .= ' wpex-data-hover';
	vcex_inline_js( 'data_hover' );
} ?>

<div class="<?php echo $wrap_classes; ?>">

	<form method="get" class="vcex-searchbar-form" action="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo $input_style; ?>>

		<input type="search" class="<?php echo $input_classes; ?>" name="s" placeholder="<?php echo $placeholder; ?>"<?php echo vcex_inline_style( array( 'width' => $input_width ) ); ?> />
		
		<?php if ( $advanced_query ) {

			// Sanitize
			$advanced_query = trim( $advanced_query );
			$advanced_query = html_entity_decode( $advanced_query );

			// Convert to array
			$advanced_query = parse_str( $advanced_query, $advanced_query_array );

			// If array is valid loop through params
			if ( $advanced_query_array ) { ?>

				<?php foreach( $advanced_query_array as $key => $val ) : ?>

				   <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>">

				<?php endforeach; ?>

			<?php } ?>

		<?php } ?>

		<button type="submit" class="<?php echo $button_classes; ?>"<?php echo $button_data;?><?php echo $button_style ?>>
			<?php echo $button_text; ?>
		</button>

	</form><!-- .searchform -->

</div><!-- .vcex-searchbar-wrap -->