<?php
/*
 * Plugin Name:       Custom Google Analytics
 * Plugin URI:        
 * Description:       Wordpress Plugin That Grants Web User Access to Google Analytics API.
 * Version:           2.4
 * Author:            Get Your Solutions
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'cga_Analitics' ) ) :

final class cga_Analitics{

	/**
	 * @var string
	 */
	public $version = '1.0';
	
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}
	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		
		
	}
	
	/**
	 * Define WC Constants
	 */
	private function define_constants() {
		$this->define( 'CGA_PLUGIN_BASEPATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'CGA_PLUGIN_BASEURL', plugin_dir_url( __FILE__ ) );
		$this->define( 'CGA_VERSION', $this->version );
	}
	
	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	public function includes() {
		include_once( 'includes/class-custom-google-analytics.php' );
		$cgaLoad = new Custom_Google_Analytics();
		$cgaLoad->run();
	}
	
}

endif;

/**
 * Initiate Plugin
 */
$cgaInitiate = new cga_Analitics();
