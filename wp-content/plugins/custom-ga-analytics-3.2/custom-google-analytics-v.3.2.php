<?php
/*
 * Plugin Name:       Site Optimus Backend
 * Plugin URI:        
 * Description:       Wordpress Plugin That Analyzes Google Analytics Data.
 * Version:           3.1
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
	public $version = '3.0';
	
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
                //include_once( 'includes/class-gf-web-api-wrapper.php' );
		$cgaLoad = new Custom_Google_Analytics();
		$cgaLoad->run();
	}
	
}

endif;

/**
 * Initiate Plugin
 */
$cgaInitiate = new cga_Analitics();

/*                
//from https://github.com/stevehenty/gf-api-demo-2         
define( 'GF_WEB_API_DEMO_2_VERSION', '1.0.1' );
add_action( 'gform_loaded', array( 'GF_Web_Api_Demo_2_Bootstrap', 'load' ), 5 );
class GF_Web_Api_Demo_2_Bootstrap {
	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}
		require_once( 'includes/class-gf-web-api-demo-2.php' );
		GFAddOn::register( 'GF_Web_Api_Demo_2' );
	}
}
function gf_web_api_demo_2() {
	return GF_Web_Api_Demo_2::get_instance();
}


define( 'GF_WEB_API_DEMO_2_VERSION', '1.0.1' );
add_action( 'gform_loaded', array( 'GF_Web_Api_Demo_2_Bootstrap', 'load' ), 5 );
class GF_Web_Api_Bootstrap {
	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}
		require_once( 'includes/class-gf-web-api-wrapper.php' );
		GFAddOn::register( 'GF_Web_Api_Demo_2' );
	}
}
function gf_web_api_demo_2() {
	return GF_Web_Api_Demo_2::get_instance();
}

$public_key="d1291d7703";
$private_key="c937086c815b87c";
$api_url="http://siteoptimus.com/gravityformsapi/forms/9/?api_key=d1291d7703&signature=vqMqL%2F4CWlrlYcZ4nHQcXRJmS4E%3D&expires=1520453103";
require_once( 'includes/class-gf-web-api-wrapper.php' );
$api = new GFWebAPIWrapper($api_url, $public_key, $private_key);
// returns a Form object
$api->get_form(9);
//echo $api;


//start get form

require_once( 'includes/class-gf-web-api-wrapper.php' );

function calculate_signature( $string, $private_key ) {
    $hash = hash_hmac( 'sha1', $string, $private_key, true );
    $sig = rawurlencode( base64_encode( $hash ) );
    return $sig;
}
 
//set API keys
$api_key = 'd1291d7703';
$private_key = 'c937086c815b87c';
 
//set route
$route = 'forms/9';
 
//creating request URL
$expires = strtotime( '+60 mins' );
$string_to_sign = sprintf( '%s:%s:%s:%s', $api_key, 'GET', $route, $expires );
$sig = calculate_signature( $string_to_sign, $private_key );
$url = 'http://siteoptimus.com/gravityformsapi/' . $route . '?api_key=' . $api_key . '&signature=' . $sig . '&expires=' . $expires;
 
//retrieve data
$response = wp_remote_request( $url, array( 'method' => 'GET' ) );
if ( wp_remote_retrieve_response_code( $response ) != 200 || ( empty( wp_remote_retrieve_body( $response ) ) ) ){
    //http request failed
    die( 'There was an error attempting to access the API.' );
}
 
//result is in the response "body" and is json encoded.
$body = json_decode( wp_remote_retrieve_body( $response ), true );
 
if( $body['status'] > 202 ){
    die( "Could not retrieve forms." );
}
 
//forms retrieved successfully
$form = $body['response'];

//end get single form

*/
//from http://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
 
// Creating the widget 
class wpb_widget extends WP_Widget {

    function __construct() {
    parent::__construct(

    // Base ID of your widget
    'wpb_widget', 

    // Widget name will appear in UI
    __('SiteOptimus Controls', 'wpb_widget_domain'), 

    // Widget description
    array( 'description' => __( 'Sidebar widget based on WPBeginner Tutorial', 'wpb_widget_domain' ), ) 
    );
    }

    // Creating widget front-end

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output
        $gAuth = new Google_Auth();
        $gAnalytics = new Google_Analytics();
        If(isset($_SESSION['CGA_sessionData'])){
                $accessData = json_decode($_SESSION['CGA_sessionData']['token']);
                if(!$gAuth->isAccessTokenExpired()){
                        $gaAccounts = $gAnalytics->getAccounts();
                }else{
                        wp_redirect(get_bloginfo('url').'/ga-login');
                }
        }else{
                wp_redirect(get_bloginfo('url').'/ga-login');
        }
        echo '<form action="" method="GET" class="account_form">
                        Choose Account';
                        echo '<select name="account">
                                <option value="">Select Account</option>';
                        foreach($gaAccounts as $item){
                                if($_GET['account'] == $item['id'])
                                        $selected = 'selected="selected"';
                                else
                                        $selected = '';
                                echo '<option value="'.$item['id'].'" '.$selected.'>'.$item['name'].'</option>';
                        }
                        echo '</select>';
                        echo '<input type="submit" value="submit">
                </form>';

        /*

        //gravity form api                
        function calculate_signature( $string, $private_key ) {
            $hash = hash_hmac( 'sha1', $string, $private_key, true );
            $sig = rawurlencode( base64_encode( $hash ) );
            return $sig;
        }

        //set API keys
        $api_key = 'd1291d7703';
        $private_key = 'c937086c815b87c';

        //set route
        $route = 'forms/9';

        //creating request URL
        $expires = strtotime( '+60 mins' );
        $string_to_sign = sprintf( '%s:%s:%s:%s', $api_key, 'GET', $route, $expires );
        $sig = calculate_signature( $string_to_sign, $private_key );
        $url = 'http://siteoptimus.com/gravityformsapi/' . $route . '?api_key=' . $api_key . '&signature=' . $sig . '&expires=' . $expires;

        //retrieve data
        $response = wp_remote_request( $url, array( 'method' => 'GET' ) );
        if ( wp_remote_retrieve_response_code( $response ) != 200 || ( empty( wp_remote_retrieve_body( $response ) ) ) ){
            //http request failed
            die( 'There was an error attempting to access the API.' );
        }

        //result is in the response "body" and is json encoded.
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if( $body['status'] > 202 ){
            die( "Could not retrieve forms." );
        }

        //forms retrieved successfully
        $form = $body['response'];

        //display results in a simple page
        ?>
        <html>
        <body>
        <form>
            <p>Forms</p>
            <div>
                <?php
                if ( $form ) {
                    echo '<table border="1"><th>Form ID</th><th>Form Title</th><th>Field Count</th>';
                    //foreach ( $forms as $form ) {
                        $fields = $form['fields'];
                        echo '<tr><td>' . $form['id'] . '</td><td>' . $form['title'] . '</td><td>' . count( $fields ) . '</td></tr>';
                        if ( $fields ){
                            echo '<tr><td colspan="3"><table border="1"><th>Field ID</th><th>Field Label</th><th>Field Type</th>';
                            foreach ( $fields as $field ){
                                echo '<tr><td>' . $field['id'] . '</td><td>' . GFCommon::get_label( $field ) . '</td><td>' . $field['type'] . '</td></tr>';
                            }
                            echo '</table></td></tr>';
                        }
                        echo '<tr><td colspan="3">&nbsp;</td></tr>';
                    //}
                    echo '</table>';
                }
                ?>
            </div>
            <br/>
            <div>JSON Response:<br/><textarea style="vertical-align: top" cols="125" rows="10"> <?php echo $response['body']; ?></textarea></div>
        </form>

        $form_id = '9';
        $form = GFAPI::get_form( $form_id );

        var_dump( $form );

        //echo __( 'Hello, World!', 'wpb_widget_domain' );
        echo $args['after_widget'];
        }

        // Widget Backend 
        public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
        }
        else {
        $title = __( 'Selections', 'wpb_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
        }

        // Updating widget replacing old instances with new
        public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;*/
    }

} // Class wpb_widget ends here