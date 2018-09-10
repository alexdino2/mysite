<?php
/**
 * Loop Top : Blog / Standard entries
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'woocommerce_get_template_part' ) ) {
	get_template_part( 'partials/loop-top' );
} ?>

<div class="woocommerce clr">
	<ul class="products wpex-row clr">