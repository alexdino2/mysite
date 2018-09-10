<?php
/**
 * Page subheading output
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Display subheading if there is one
if ( $subheading = wpex_global_obj( 'get_page_subheading' ) ) : ?>

	<div class="clr page-subheading">
		<?php echo do_shortcode( $subheading ); ?>
	</div><!-- .page-subheading -->

<?php endif; ?>