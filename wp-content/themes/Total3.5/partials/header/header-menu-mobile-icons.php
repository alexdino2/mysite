<?php
/**
 * Mobile Icons Header Menu.
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="mobile-menu" class="clr wpex-mobile-menu-toggle wpex-hidden"><?php

	// Define main section vars
	$toggle_icon = $mobile_icons = '';

	// Check menu location and global multisite menu
	$menu_location  = apply_filters( 'wpex_main_menu_location', 'main_menu' );
	$ms_global_menu = apply_filters( 'wpex_ms_global_menu', false );

	// Create menu toggle icon
	if ( has_nav_menu( $menu_location ) || $ms_global_menu ) {

		$toggle_icon .= '<a href="#" class="mobile-menu-toggle">';

			$toggle_icon .= apply_filters( 'wpex_mobile_menu_open_button_text', '<span class="fa fa-navicon" aria-hidden="true"></span>' );

			$toggle_icon .= '<span class="screen-reader-text">' . esc_html__( 'Open Mobile Menu', 'total' ) . '</span>';

		$toggle_icon .= '</a>';

	}

	// Define mobile menu icons output
	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'mobile_menu' ] ) ) {
			
		$menu = wp_get_nav_menu_object( $locations[ 'mobile_menu' ] );
		
		if ( ! empty( $menu ) ) {
			
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			if ( $menu_items ) {

				$menu_has_cart_icon = false;
			
				foreach ( $menu_items as $key => $menu_item ) {
					
					// Only add items if a correct font icon is added for the menu item label
					if ( in_array( $menu_item->title, wpex_get_awesome_icons() ) ) {

						$title      = $menu_item->title;
						$attr_title = $menu_item->attr_title;
						$link_icon  = '<span class="fa fa-' . esc_attr( $title ) . '" aria-hidden="true"></span>';
						$classes    = 'mobile-menu-extra-icons mobile-menu-' . esc_attr( $title );

						if ( 'shopping-cart' == $title || 'shopping-bag' == $title || 'shopping-basket' == $title ) {
							$menu_has_cart_icon = true;
							$is_shop_icon = true;
						} else {
							$is_shop_icon = false;
						}

						if ( $is_shop_icon ) {
							$classes .= ' wpex-shop';
						}
						
						if ( ! empty( $menu_item->classes[0] ) ) {
							$classes .= ' '. implode( ' ', array_filter( $menu_item->classes, 'trim' ) );
						}

						$link_attrs = array(
							'href'  => esc_url( $menu_item->url ),
							'class' => esc_attr( $classes ),
							'title' => $attr_title,
						);

						$reader_text = $attr_title ? $attr_title : $title;
						$reader_text = '<span class="screen-reader-text">' . esc_html__( $reader_text ) . '</span>';

						$inner_html = $link_icon . $reader_text;

						if ( $is_shop_icon && function_exists( 'wpex_mobile_menu_cart_count' ) ) {
							$inner_html .= wpex_mobile_menu_cart_count();
						}

						$mobile_icons .= wpex_parse_html( 'a', $link_attrs, $inner_html ); ?>
				
				<?php }

				}

			}

		}

	}

	// Output main toggle icon
	echo $toggle_icon;

	// Output user-defined mobile icons
	echo $mobile_icons;

?></div><!-- #mobile-menu -->