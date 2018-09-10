<?php
/**
 * Footer Builder Content
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php wpex_hook_footer_before(); ?>

	<?php
	// Get page ID
	$page_id = wpex_get_mod( 'footer_builder_page_id' );

	// WPML fix
	if ( function_exists( 'icl_object_id' ) ) {
		$page_id = icl_object_id( $page_id, 'page' );
	}

	// Display footer builder
	if ( $page_id ) : ?>

		<div id="footer-builder" class="footer-builder clr">
			<div class="footer-builder-content clr container">
				<?php echo do_shortcode( get_post_field( 'post_content', $page_id ) ); ?>
			</div><!-- .footer-builder-content -->
		</div><!-- .footer-builder -->

	<?php endif; ?>

<?php wpex_hook_footer_after(); ?>