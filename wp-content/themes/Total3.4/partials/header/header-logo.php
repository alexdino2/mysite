<?php
/**
 * Header Logo
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define variables
$logo_url     = wpex_header_logo_url();
$logo_img     = wpex_global_obj( 'header_logo' );
$overlay_logo = wpex_global_obj( 'header_overlay_logo' );
$logo_icon    = wpex_header_logo_icon();
$logo_title   = wpex_header_logo_title(); ?>

<div id="site-logo" class="<?php echo wpex_header_logo_classes(); ?>">
	<div id="site-logo-inner" class="clr">
		<?php if ( $logo_img || $overlay_logo ) : ?>
			<?php
			// Custom site-wide image logo
			if ( $logo_img && ! $overlay_logo ) : ?>
				<a href="<?php echo esc_url( $logo_url ); ?>" title="<?php echo esc_attr( $logo_title ); ?>" rel="home" class="main-logo"><img src="<?php echo esc_url( $logo_img ); ?>" alt="<?php echo esc_attr( $logo_title ); ?>" width="<?php echo wpex_get_header_logo_width(); ?>" height="<?php echo wpex_get_header_logo_height(); ?>" data-no-retina /></a>
			<?php endif; ?>
			<?php
			// Custom header-overlay logo
			if ( $overlay_logo ) : ?>
				<a href="<?php echo esc_url( $logo_url ); ?>" title="<?php echo esc_attr( $logo_title ); ?>" rel="home" class="overlay-header-logo"><img src="<?php echo esc_url( $overlay_logo ); ?>" alt="<?php echo esc_attr( $logo_title ); ?>" width="<?php echo wpex_get_header_logo_width(); ?>" height="<?php echo wpex_get_header_logo_height(); ?>" data-no-retina /></a>
			<?php endif; ?>
		<?php else : ?>
			<a href="<?php echo esc_url( $logo_url ); ?>" title="<?php echo esc_attr( $logo_title ); ?>" rel="home" class="site-logo-text"><?php echo $logo_icon; ?><?php echo $logo_title; ?></a>
		<?php endif; ?>
	</div><!-- #site-logo-inner -->
</div><!-- #site-logo -->