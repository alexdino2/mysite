<?php
//update_option( 'siteurl', 'https://siteoptimus.com' );
//update_option( 'home', 'https://siteoptimus.com' );
/**
 * Child theme functions
 *
 * When using a child theme (see https://codex.wordpress.org/Theme_Development
 * and https://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: wpex
 * @link https://codex.wordpress.org/Plugin_API
 *
 */
/**
 * Load the parent style.css file
 */
function total_child_enqueue_parent_theme_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
add_action( 'wp_enqueue_scripts', 'total_child_enqueue_parent_theme_style' );


/* Load highcharts */
function js_custom(){

    //if ( is_page('test-boxplot')){  
    if ( ( is_page('test-boxplot') || is_page('opportunity-details')|| is_page('test-box-2') ) ) {  

	// Register and Enqueue a Script
    	// get_stylesheet_directory_uri will look up child theme location
    	//wp_enqueue_script( 'ajax', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' );
    	wp_enqueue_script( 'highcharts', 'https://code.highcharts.com/highcharts.js' );
    	wp_enqueue_script( 'highcharts-more', 'https://code.highcharts.com/highcharts-more.js' );
    	wp_enqueue_script( 'highcharts-export', 'https://code.highcharts.com/modules/exporting.js' );
    }
}	

add_action('wp_enqueue_scripts', 'js_custom');



/* Take user directly to checkout bypassing cart */
add_filter ('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');

function redirect_to_checkout() {
    return wc_get_checkout_url();
}

/*
add_action( 'gform_after_submission_5', 'so_add_entry_to_db', 10, 2 );
function so_add_entry_to_db($entry, $form) {

	//uncomment to see the entry object 
	echo '<pre>';
	var_dump($entry);
	echo '</pre>';

	$source = $entry['source_url'];

	$goal = 	$entry[5];
	$acct = 	$entry[1];
	$stdate = 	$entry[3];
	$enddate = 	$entry[4];
	$whichdim = 	$entry[2];


        global $wpdb;

        // add form data to custom database table
        $wpdb->insert(
              'tbl_code_run_log',
              array(
                    'typeid' => 1,
                    'source' => $source,
                    'goal' => $goal,
                    'dim' => $whichdim,
                    'acct' => $acct,
                    'stdate' => $stdate,
                    'enddate' => $enddate,
                    'DateTime' => current_time( 'mysql' )
               )
         );

}
*/

/*Site Optimus functions from development*/

function checkdefault($dimrow)
{
    global $dim;
    if ($dimrow == $dim){
            $result = "selected";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultprofile($profileid)
{
    global $acct;
    if ($profileid == $acct){
            $result = "selected";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultgoal($goalrow)
{
    global $goal;
    if ($goalrow == $goal){
            $result = "checked";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultdenset($densetrow)
{
    global $denset;
    if ($densetrow == $denset){
            $result = "checked";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function getfilter($var){
	$filter_a = array();
        if (isset($_SESSION[$var])){
                    return $_SESSION[$var];
                    $filter_a = unserialize($_SESSION['filter']);
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $var = $_COOKIE[$var];
                        if($var='filter'){
                        $filter_a = unserialize($_COOKIE['filter']);
                        }
                }
                else {
                        $var = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    
}

function so_getvar($var)
{

    if(!isset($var))
    {
            
        if (isset($_SESSION[$var])){
                    $var = $_SESSION[$var];
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $var = $_COOKIE[$var];
                }
                else {
                        $var = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    }
    return $var;
}

function so_setvar($var)
{
	if($var='filter'){
		$filter_a = array();
	}
    if(!isset($var))
    {
            
        if (isset($_SESSION[$var])){
                    $acct = $_SESSION[$var];
                    if($var='filter'){
                    	$filter_a = unserialize($_SESSION['filter']); 
                    }
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $acct = $_COOKIE[$var];
                        if($var='filter'){
                        	$filter_a = unserialize($_COOKIE['filter']);
                        }
                }
                else {
                        $acct = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    }
}

/*Query denset*/
function runstdset($filter)
{

	global $wpdb;
        
        $startdatetime =  time();
        $currdatetime = date("Y-m-d H:i:s", $startdatetime);

        //need to modify filter so that it keeps adding filters
        //need to hide additional submit area
        global $acct;
        $acct=so_getvar($acct);

        global $dim;
        $dim=so_getvar($dim);
        echo $dim;
        
        global $whichdim;
        $whichdim=so_getvar($whichdim);
        echo $whichdim;
        
        global $goal;
        $goal = so_getvar($goal);

        global $startdt;
        $startdt = so_getvar($startdt);
        global $enddt;
        $enddt = so_getvar($enddt);

        global $denset;
        $gapidenset = "get" . $denset;
        global $gapigoal;
        $gapigoal = "get" . $goal;

        //$startdt = "2011-01-01";
        //$enddt = "2011-05-30";

        define('ga_profile_id',$acct);

        global $ga;
        $ga->requestReportData(ga_profile_id,array($dim),array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
        $middatetime =  time();


	$query = 'truncate tbl_dim_den_num';

	$wpdb->query($query);
        //mysqli_query($Algo,"truncate tbl_dim_den_num");

        foreach($ga->getResults() as $result){
                $wpdb->query("insert into tbl_dim_den_num(whichdim,dim,den,num) VALUES ('" . $whichdim. "','" . $result. "','" . $result->$gapidenset() . "','" . $result->$gapigoal() . "')");
        }

        $enddatetime = time();
        $midduration = $middatetime - $startdatetime;
        $duration = $enddatetime - $startdatetime;
        //mysqli_query($Algo,"insert into tbl_code_run_log(typeid,DateTime,goal,dim,acct,filter,midduration,duration) VALUES ('2','" . $currdatetime . "','" . $goal . "','" . $dim . "','" . $acct . "','" . $filter."','" . $midduration ."','" . $duration . "')");
        return true;
}

/*
// Enable the "Media" post settings tab for custom post types
function my_add_meta_media_to_my_custom_type( $array ) {
    $array['media']['post_type'][] = 'services';
    return $array;
}
add_filter( 'wpex_metabox_array', 'my_add_meta_media_to_my_custom_type' );

add_filter( 'gform_confirmation_anchor_1', function() {
    return 35;
} );
 * 
 * 
 */
/*
add_filter ( 'woocommerce_account_menu_items', 'siteoptimus_one_more_link' );
function siteoptimus_one_more_link( $menu_links ){
 
	// we will hook "anyuniquetext123" later
	$new = array( 'access-data' => 'Access Data' );
 
	// array_slice() is good when you want to add an element between the other ones
	$menu_links = array_slice( $menu_links, 0, 1, true ) 
	+ $new 
	+ array_slice( $menu_links, 1, NULL, true );
 
 
	return $menu_links;
 
 
}
 
add_filter( 'woocommerce_get_endpoint_url', 'siteoptimus_hook_endpoint', 10, 4 );
function siteoptimus_hook_endpoint( $url, $endpoint, $value, $permalink ){
 
	if( $endpoint === 'access-data' ) {
 
		// ok, here is the place for your custom URL, it could be external
		$url = 'https://siteoptimus.com/my-account/access-data/';
 
	}
	return $url;
 
}
 * /
 */
/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function my_custom_endpoints() {
	add_rewrite_endpoint( 'access-data', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'my_custom_endpoints' );

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function my_custom_query_vars( $vars ) {
	$vars[] = 'access-data';

	return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars', 0 );

function my_custom_endpoint_content() {
	 
        /*
        echo 'Thank you for your order. Please check your email for instructions to access your data.

        <a href="https://siteoptimus.com/google-analytics-access/">Click here to get started.</a>';

        */

        echo '<h2>Access my analytics</h2>';

        $gAuth = new Google_Auth();
        $gAuth->check_access_code();
        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }
        echo '<a href="'.$gAuth->auth_url().'">Login</a> with Google to access Analytic data.<br/><br/>';
}

add_action( 'woocommerce_account_access-data_endpoint', 'my_custom_endpoint_content' );
/*
 * Step 1. Add Link to My Account menu
 */
add_filter ( 'woocommerce_account_menu_items', 'siteoptimus_access_data_link', 40 );
function siteoptimus_access_data_link( $menu_links ){
 
	$menu_links = array_slice( $menu_links, 0, 5, true ) 
	+ array( 'access-data' => 'Access Data' )
	+ array_slice( $menu_links, 5, NULL, true );
 
	return $menu_links;
 
}
/*
 * Step 2. Register Permalink Endpoint
 */
add_action( 'init', 'siteoptimus_add_endpoint' );
function siteoptimus_add_endpoint() {
 
	// WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
	add_rewrite_endpoint( 'access-data', EP_ROOT | EP_PAGES );
 
}
/*
 * Step 3. Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
 */
