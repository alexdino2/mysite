<?php //changed the jquery script - probably need to change back
class Custom_Google_Analytics_Admin {
 
    protected $version;
 
    public function __construct( $version ) {
        $this->version = $version;
    }
    
	/**
	 * Enqueues the style sheet responsible for styling the contents of this
	 * meta boxes and custom pages.
	 */
    public function cga_admin_enqueue_scripts() {
        wp_enqueue_style(
            'custom-google-analytics-admin-css',
            CGA_PLUGIN_BASEURL . 'assets/css/custom-google-analytics-admin.css',
            array(),
            $this->version,
            FALSE
        );
        /*wp_enqueue_script( //changed the jquery script - probably need to change back
            'custom-google-analytics-jquery-min',
            CGA_PLUGIN_BASEURL . 'assets/js/jquery-1.12.1.min.js',
            array(),
            $this->version,
            FALSE
        );*/
        wp_enqueue_script(
            'custom-google-analytics-jquery-migrate',
            CGA_PLUGIN_BASEURL . 'assets/js/jquery-migrate-1.3.0.min.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_script(
            'custom-google-analytics-admin-js',
            CGA_PLUGIN_BASEURL . 'assets/js/custom-google-analytics-admin.js',
            array(),
            $this->version,
            FALSE
        );
    }
    
    /**
     * Add the custom menu pages.
     */
    public function cga_add_custom_pages() {
		add_menu_page('General Setting', 'Google Analytics', 'manage_options', 'cgadas-setting', array( $this, 'render_general_setting_page_callback' ) );
    }
    
    /**
     * Requires the file that is used to display setting page of the plugin.
     */
    public function render_general_setting_page_callback() {
        require_once plugin_dir_path( __FILE__ ) . 'views/cgadash-general-setting.php';
    }
 
    
 
}
