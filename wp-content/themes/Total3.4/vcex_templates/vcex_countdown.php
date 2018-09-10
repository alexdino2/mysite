<?php
/**
 * Visual Composer Countdown
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 3.3.2
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

// Display countdown if date is defined
if ( $date ) :

	// Add random ID
	$unique_id = $unique_id ? $unique_id : 'vcex-countdown-'. rand( 100, 500 );

	// Load countdown script
	wp_enqueue_script( 'vcex-final-countdown' );

	// Get inline CSS
	$inline_style = vcex_inline_style( array(
		'font_size'   => $font_size,
		'color'       => $color,
		'text_align'  => $align,
		'font_family' => $font_family,
		'font_weight' => $font_weight,
	) ); ?>

	<script type="text/javascript">
	jQuery( function( $ ) {
		if($.fn.countdown!=undefined){
			$( '#<?php echo esc_attr( $unique_id ); ?>' ).countdown( '<?php echo esc_html( $date ); ?>' ).on('update.countdown', function(event) {
				var $this = $(this).html( event.strftime( ''
				+ '<div class="day"><span class="inner"><span class="count">%-D</span> <span class="label">Day%!D</span></span></div> '
				+ '<div class="hour"><span class="inner"><span class="count">%H</span> <span class="label">Hour%!H</span></span></div> '
				+ '<div class="minutes"><span class="inner"><span class="count">%M</span> <span class="label">Minute%!M</span></span></div> '
				+ '<div class="seconds"><span class="inner"><span class="count">%S</span> <span class="label">Second%!S</span></span></div>' ) );
			});
		}
	});
	</script>

	<div id="<?php echo esc_attr( $unique_id ); ?>" class="vcex-countdown vcex-countdown-<?php echo $style; ?>"<?php echo $inline_style; ?>></div>

<?php endif; ?>