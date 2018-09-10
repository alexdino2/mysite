<?php
/**
 * storefront engine room
 *
 * @package storefront
 */

/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woothemes/theme-customisations
 */

add_filter('add_to_cart_redirect', 'themeprefix_add_to_cart_redirect');
function themeprefix_add_to_cart_redirect() {
 global $woocommerce;
 $checkout_url = $woocommerce->cart->get_checkout_url();
 return $checkout_url;
}

//Add New Pay Button Text
add_filter( 'woocommerce_product_single_add_to_cart_text', 'themeprefix_cart_button_text' ); 
 
function themeprefix_cart_button_text() {
 return __( 'Singup Now', 'woocommerce' );
}

add_filter( 'wc_product_sku_enabled', '__return_false' );