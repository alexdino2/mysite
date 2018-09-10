<?php
class Custom_Google_Analytics {
 
    protected $loader;
 
    protected $plugin_slug;
 
    protected $version;
 
    public function __construct() {
 
        $this->plugin_slug = 'custom-google-analytics';
        $this->version = '1.0';
        $this->load_dependencies();
        if(is_admin())
			$this->define_admin_hooks();
        else
			$this->define_frontEnd_hooks();
 
    }
 
    private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-custom-google-analytics-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-google-analytics-front.php';
                
 
        require_once plugin_dir_path( __FILE__ ) . 'class-custom-google-analytics-loader.php';
        $this->loader = new Custom_Google_Analytics_Loader();
    }
 
    private function define_admin_hooks() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/custom-filters-actions.php';
		$admin = new Custom_Google_Analytics_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'cga_admin_enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $admin, 'cga_add_custom_pages' );
        //$this->loader->add_filter( 'manage_users_columns', $front, 'cga_custom_user_columns' );
        //$this->loader->add_filter( 'manage_users_custom_column', $front, 'cga_custom_user_columns_data' );
 
    }
    private function define_frontEnd_hooks() {
		$front = new Custom_Google_Analytics_Front( $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $front, 'cga_front_enqueue_scripts' );
        $this->loader->add_action( 'wp_loaded', $front, 'cga_initiate_auth_analytic' );
        $this->loader->add_filter( 'page_template', $front, 'cga_custom_page_template' );
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/populate_forms.php';
        
    }
 
    public function run() {
		$this->loader->run();
    }
 
    public function get_version() {
        return $this->version;
    }
 
}
