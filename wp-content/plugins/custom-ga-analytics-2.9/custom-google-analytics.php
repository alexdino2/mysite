<?php
/*
 * Plugin Name:       Site Optimus Backend
 * Plugin URI:        
 * Description:       Wordpress Plugin That Analyzes Google Analytics Data.
 * Version:           2.9
 * Author:            Alex Destino
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*changed redirect in class-google-auth-analytics-api to goal cycle page*/
/*2.7 renamed plugin and added dynamodb support*/
/*2.9 changed memcached server name*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'cga_Analitics' ) ) :

final class cga_Analitics{

	/**
	 * @var string
	 */
	public $version = '2.9';
	
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
