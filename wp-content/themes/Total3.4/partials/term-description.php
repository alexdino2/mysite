<?php
/**
 * Term descriptions
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Display term description if there is one
if ( $term_description = term_description() ) : ?>

	<div class="term-description clr">
	    <?php echo term_description(); ?>
	</div><!-- #term-description -->

<?php endif; ?>