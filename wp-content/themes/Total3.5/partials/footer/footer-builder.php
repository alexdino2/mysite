<?php
/**
 * Footer builder output
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_id = wpex_footer_builder_id();

if ( ! $page_id ) {
	return;
} ?>

<div id="footer-builder" class="footer-builder clr">
	<div class="footer-builder-content clr container entry">
		<?php if ( wpex_vc_is_inline() && wpex_is_footer_builder_page() ) {
			while ( have_posts() ) : the_post();
				the_content();
			endwhile;
		} else {
			echo do_shortcode( get_post_field( 'post_content', $page_id ) );
		} ?>
	</div>
</div>