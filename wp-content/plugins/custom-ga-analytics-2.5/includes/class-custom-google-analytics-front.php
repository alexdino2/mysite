<?php
class Custom_Google_Analytics_Front {
 
    protected $version;
 
    public function __construct( $version ) {
        $this->version = $version;
    }
    
	/**
	 * Enqueues the style sheet responsible for styling the contents of this
	 * meta boxes and custom pages.
	 */
    public function cga_front_enqueue_scripts() {
        wp_enqueue_script(
            'custom-google-analytics-jquery-min',
            CGA_PLUGIN_BASEURL . 'assets/js/jquery-1.12.1.min.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_script(
            'custom-google-analytics-jquery-migrate',
            CGA_PLUGIN_BASEURL . 'assets/js/jquery-migrate-1.3.0.min.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_script(
            'custom-google-analytics-jquery-ui-min-js',
            CGA_PLUGIN_BASEURL . 'assets/js/jquery-ui.min.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_script(
            'custom-ga-js',
            CGA_PLUGIN_BASEURL . 'assets/js/custom-google-analytics.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_script(
            'custom-google-analytics-dataTabes-js',
            'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_style(
            'custom-google-analytics-dataTabes-css',
            'https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_style(
            'custom-google-analytics-jquery-ui-min-css',
            CGA_PLUGIN_BASEURL . 'assets/css/jquery-ui.min.css',
            array(),
            $this->version,
            FALSE
        );
        wp_enqueue_style(
            'custom-google-analytics-custom-css',
            CGA_PLUGIN_BASEURL . 'assets/css/url-rewrite-manager-front.css',
            array(),
            $this->version,
            FALSE
        );
    }
 
    /**
     * Return custom page template for login and dashboard.
     */
    public function cga_custom_page_template($page_template) {
		global $post;
		if($post->post_type != 'page')
			return $page_template;
		$cgaDashSetting = get_option('cga_dashSetting');
		if($cgaDashSetting != '')
			$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
		$page_id = $post->ID;
        if ( $page_id == $cgaDashSetting['setting']['loginPage'] ) {
			$page_template = CGA_PLUGIN_BASEPATH . '/templates/custom-cga-login-template.php';
		}
        if ( $page_id == $cgaDashSetting['setting']['dashboardPage'] ) {
			$page_template = CGA_PLUGIN_BASEPATH . '/templates/custom-cga-dashboard-template.php';
		}
		if ( $page_id == $cgaDashSetting['setting']['goalCyclePage'] ) {
			$page_template = CGA_PLUGIN_BASEPATH . '/templates/custom-cga-goal-cycle-template.php';
		}
        if ( $page_id == $cgaDashSetting['setting']['siteMetaPage'] ) {
			$page_template = CGA_PLUGIN_BASEPATH . '/templates/custom-cga-site-metaData-template.php';
		}
		if ( $page_id == $cgaDashSetting['setting']['goalCycleMetaPage'] ) {
			$page_template = CGA_PLUGIN_BASEPATH . '/templates/custom-cga-goal-cycle-metaData-template.php';
		}
		return $page_template;
    }
    
    /**
     * Initiate Google Auth and Analytics
     */
    public function cga_initiate_auth_analytic() {
		require_once realpath(CGA_PLUGIN_BASEPATH . 'includes/class-google-auth-api.php');
		require_once realpath(CGA_PLUGIN_BASEPATH . 'includes/class-google-analytics-api.php');
    }
 
}
