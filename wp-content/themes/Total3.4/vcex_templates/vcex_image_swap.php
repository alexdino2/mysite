<?php
/**
 * Visual Composer Image Swap
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

// Fallbacks (old atts)
$link_title  = isset( $atts['link_title'] ) ? $atts['link_title'] : '';
$link_target = isset( $atts['link_target'] ) ? $atts['link_target'] : '';

// Get and extract shortcode attributes
extract( vc_map_get_attributes( $this->getShortcode(), $atts ) );

// Primary and secondary imags required
if ( ! $primary_image || ! $secondary_image ) {
	return;
}

// Add styles
$wrapper_inline_style = vcex_inline_style( array(
	'width' => $container_width,
) );
$image_style = vcex_inline_style( array(
	'border_radius' => $border_radius,
), false );

// Add classes
$wrapper_classes = array( 'vcex-image-swap', 'clr' );
if ( $classes ) {
	$wrapper_classes[] = $this->getExtraClass( $classes );
}
if ( $css_animation ) {
	$wrapper_classes[] = $this->getCSSAnimation( $css_animation );
}
$wrapper_classes = implode( ' ', $wrapper_classes ); ?>

<?php if ( $primary_image && $secondary_image ) : ?>

	<?php if ( $css ) : ?>
		<div class="<?php echo vc_shortcode_custom_css_class( $css ); ?>">
	<?php endif; ?>

	<div class="<?php echo $wrapper_classes; ?>"<?php echo $wrapper_inline_style; ?><?php vcex_unique_id( $unique_id ); ?>>

		<?php if ( $link ) { ?>

			<?php
			// Link attributes
			$link_atts = vc_build_link( $link );
			if ( ! empty( $link_atts['url'] ) ) {
				$link        = isset( $link_atts['url'] ) ? $link_atts['url'] : $link;
				$link_title  = isset( $link_atts['title'] ) ? $link_atts['title'] : '';
				$link_target = isset( $link_atts['target'] ) ? $link_atts['target'] : '';
			}

			// Sanitize link vars
			$link_classes = 'vcex-image-swap-link';
			if ( in_array( $link_tooltip, array( 'yes', 'true' ) ) ) {
				$link_classes .= ' tooltip-up';
			}

			// Display link
			if ( $link ) { ?>

				<a href="<?php echo esc_url( $link ); ?>" class="<?php echo $link_classes; ?>"<?php echo vcex_html( 'title_attr', $link_title ); ?><?php echo vcex_html( 'target_attr', $link_target ); ?>>

			<?php } ?>

		<?php } ?>

			<?php
			// Primary image
			wpex_post_thumbnail( array(
				'attachment' => $primary_image,
				'size'       => $img_size,
				'crop'       => $img_crop,
				'width'      => $img_width,
				'height'     => $img_height,
				'class'      => 'vcex-image-swap-primary',
				'style'      => $image_style,
			) ); ?>

			<?php
			// Secondary image
			wpex_post_thumbnail( array(
				'attachment' => $secondary_image,
				'size'       => $img_size,
				'crop'       => $img_crop,
				'width'      => $img_width,
				'height'     => $img_height,
				'class'      => 'vcex-image-swap-secondary',
				'style'      => $image_style,
			) ); ?>

		<?php if ( $link ) echo '</a>'; ?>

	</div><!-- .vcex-image-swap -->

	<?php if ( $css ) echo '</div><!-- .css-wrapper -->'; ?>

<?php endif; ?>