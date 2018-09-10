<?php
/**
 * Recommends plugins for use with the theme via the TGMA Script
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.4.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_tgmpa_register() {

	// Get array of recommended plugins
	// See framework/core-functions.php
	$plugins = wpex_recommended_plugins();

	// Dismissable is true by default (lets users dismiss the notice completely)
	$dismissable = true;

	// Prevent dismiss for Visual Composer
	// And remove VC plugin from recommended list if it has a valid license
	// active on the site to prevent update issues between TGMPA and VC plugin
	if ( WPEX_VC_ACTIVE ) {
		if ( vcex_theme_mode_check() ) {
			$dismissable = wpex_vc_is_supported() ? true : false;
		} else {
			unset( $plugins['js_composer'] );
		}
	} else {
		$dismissable = true; // VC not active
	}

	// Register notice
	tgmpa( $plugins, array(
		'id'           => 'wpex_theme',
		'domain'       => 'total',
		'menu'         => 'install-required-plugins',
		'has_notices'  => true,
		'is_automatic' => true,
		'dismissable'  => $dismissable,
	) );

}
add_action( 'tgmpa_register', 'wpex_tgmpa_register' );