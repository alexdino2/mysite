<?php
/**
 * Plugin Name: Site Optimus GA Results
 * Plugin URI: http://siteoptimus.com
 * Description: This plugin adds some Google Data to Site Optimus. Based on analytify
 * Version: 1.0.0
 * Author: Alex Destino
 * Author URI: http://alexdestino.com
 * License: GPL2
 */

ini_set( 'include_path', dirname(__FILE__) . '/lib/google-api-php-client-master/' );
//ini_set( 'include_path', dirname(__FILE__) . '/lib/GAPI/' );

define( 'ROOT_PATH', dirname(__FILE__) );

class WP_SiteOptimus_Simple{

  
    // Constructor
    function __construct() {

        if ( !class_exists( 'Google_Client' ) ) {

            require_once dirname(__FILE__) . '/lib/google-api-php-client-master/autoload.php';
            require_once dirname(__FILE__) . '/lib/google-api-php-client-master/src/Google/Client.php';
            require_once dirname(__FILE__) . '/lib/google-api-php-client-master/src/Google/Service/Analytics.php';
            //require_once dirname(__FILE__) . '/lib/GAPI/gapi.class.php';
        }

        $this->client = new Google_Client();
        $this->client->setApprovalPrompt( 'force' );
        $this->client->setAccessType( 'offline' );
        $this->client->setClientId( '380690704201-a2noolvrg7g8ok1a4lf9cas17a45uvue.apps.googleusercontent.com' );
        $this->client->setClientSecret( 'OyjPp7TVnLXLIxWaJlYjT8JU' );
        $this->client->setRedirectUri( 'http://ec2-52-87-149-12.compute-1.amazonaws.com/member-home/' );
        $this->client->setScopes( 'https://www.googleapis.com/auth/analytics.readonly' ); 
        //$this->client->setDeveloperKey( 'AIzaSyAn-70Vah_wB9qifJqjrOhkl77qzWhAR_w' ); 

        try{
                
            $this->service = new Google_Service_Analytics( $this->client );
            $this->wpa_connect();
                
        }
        catch ( Google_Service_Exception $e ) {
                
        }


        add_action( 'admin_menu', array( $this, 'wpa_add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wpa_styles') );

        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
    }

    /*
      * Actions perform at loading of admin menu
      */
    function wpa_add_menu() {

        add_menu_page( 'siteoptimus simple', 'siteoptimus', 'manage_options', 'siteoptimus-dashboard', array(
                          __CLASS__,
                         'wpa_page_file_path'
                        ), plugins_url('images/wp-analytics-logo.png', __FILE__),'2.2.9');

        add_submenu_page( 'siteoptimus-dashboard', 'siteoptimus simple' . ' Dashboard', ' Dashboard', 'manage_options', 'siteoptimus-dashboard', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));

        add_submenu_page( 'siteoptimus-dashboard', 'siteoptimus simple' . ' Settings', '<b style="color:#f9845b">Settings</b>', 'manage_options', 'siteoptimus-settings', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));
    }

    /*
     * Actions perform on loading of menu pages
     */
    static function wpa_page_file_path() {
      
        $screen = get_current_screen();

        if ( strpos( $screen->base, 'siteoptimus-settings' ) !== false ) {
            include( dirname(__FILE__) . '/includes/siteoptimus-settings.php' );
        } 
        else {
            include( dirname(__FILE__) . '/includes/siteoptimus-dashboard.php' );
        }

    }

    public function pa_settings_tabs( $current = 'authentication' ) {
            
            $tabs = array( 'authentication' =>  'Authentication', 
                            'profile'       =>  'Profile' 
                    );

            echo '<div class="left-area">';

            echo '<div id="icon-themes" class="icon32"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';

            foreach( $tabs as $tab => $name ) {

                $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=siteoptimus-settings&tab=$tab'>$name</a>";
            }

            echo '</h2>';
    }


    public function wpa_save_data( $access_code ) {

        update_option( 'access_code', $access_code );
        $this->wpa_connect();

        return true;
    }


    public function wpa_connect() {

        $access_token = get_option('access_token');

        if (! empty( $access_token )) {
                    
            $this->client->setAccessToken( $access_token );
                
        } 
        else{
                    
            $authCode = get_option( 'access_code' );
                    
            if ( empty( $authCode ) ) return false;

            try {

                $accessToken = $this->client->authenticate( $authCode );
                print_r($accessToken);
            }
            catch ( Exception $e ) {
                print_r($e->getMessage());
                return false;
            }

            if ( $accessToken ) {
                        
                $this->client->setAccessToken( $accessToken );
                update_option( 'access_token', $accessToken );
                        
                return true;
            }
            else {

                return false;
            }
        }

        $this->token = json_decode($this->client->getAccessToken());
        return true;

    }


    /**
     * Get profiles from user Google Analytics account profiles.
     */
    public function pt_get_analytics_accounts() {

            try {

                if( get_option( 'access_token' ) !='' ) {
                    $profiles = $this->service->management_profiles->listManagementProfiles( "~all", "~all" );
                    return $profiles;
                }
                
                else{
                    echo '<br /><p class="description">' . __( 'You must authenticate to access your web profiles.', 'wp-siteoptimus' ) . '</p>';
                }

            }
            
            catch (Exception $e) {
                die('An error occured: ' . $e->getMessage() . '\n');
            }
    }


    /*
     * This function grabs the data from Google Analytics
     * For dashboard.
     */
    
    public function pa_get_analytics_dashboard($metrics, $startDate, $endDate, $dimensions = false, $sort = false, $filter = false, $limit = false){

            try{

                $this->service = new Google_Service_Analytics($this->client);
                $params        = array();

                if ($dimensions){
                    $params['dimensions'] = $dimensions;
                }
                if ($sort){
                    $params['sort'] = $sort;
                } 
                if ($filter){
                    $params['filters'] = $filter;
                }
                if ($limit){
                    $params['max-results'] = $limit;
                } 
                
                $profile_id = get_option("pt_webprofile_dashboard");
                if (!$profile_id){
                    return false;
                }
                
                return $this->service->data_ga->get('ga:' . $profile_id, $startDate, $endDate, $metrics, $params);

            }

            catch ( Google_Service_Exception $e ) {
                
                // Show error message only for logged in users.
                if ( is_user_logged_in() ) echo $e->getMessage();

            }
        }
    
    public function requestReportData($profileId, $dimensions, $metrics, $sort_metric=null, $filter=null, $start_date=null, $end_date=null, $start_index=1, $max_results=10000)
    {
       $this->report_root_parameters = array();
           $this->results = array(); 
       $parameters=array();
           $metrics_string = '';
      if($dimensions)
      if(is_array($dimensions))
      {
        $dimensions_string = '';
        foreach($dimensions as $dimesion)
        {
          $dimensions_string .= ',ga:' . $dimesion;
        }
        $parameters['dimensions'] = substr($dimensions_string,1);

      }
      else 
      {
        $parameters['dimensions'] = 'ga:'.$dimensions;
      }

      if(is_array($metrics))
      {

        foreach($metrics as $metric)
        {
          $metrics_string .= ',ga:' . $metric;
        }
         $metrics_string = substr($metrics_string,1);
      }
      else 
      {
         $metrics_string = 'ga:'.$metrics;
      }


      if($sort_metric==null && !empty( $metrics_string))
      {
        $parameters['sort'] =  $metrics_string;
      }
      elseif(is_array($sort_metric))
      {
        $sort_metric_string = '';

        foreach($sort_metric as $sort_metric_value)
        {
          //Reverse sort - Thanks Nick Sullivan
          if (substr($sort_metric_value, 0, 1) == "-")
          {
            $sort_metric_string .= ',-ga:' . substr($sort_metric_value, 1); // Descending
          }
          else
          {
            $sort_metric_string .= ',ga:' . $sort_metric_value; // Ascending
          }
        }

        $parameters['sort'] = substr($sort_metric_string, 1);
      }
      else 
      {
        if (substr($sort_metric, 0, 1) == "-")
        {
          $parameters['sort'] = '-ga:' . substr($sort_metric, 1);
        }
        else 
        {
          $parameters['sort'] = 'ga:' . $sort_metric;
        }
      }

      if($filter!=null)
      {
        $filter = $this->processFilter($filter);
        if($filter!==false)
        {
          $parameters['filters'] = $filter;
        }
      }

      if($start_date==null)
      {
        $start_date=date('Y-m-d',strtotime('1 month ago'));
      }



      if($end_date==null)
      {
        $end_date=date('Y-m-d');
      }



      $parameters['max-results'] = $max_results;

      print_r($parameters);

           try {

                          $result=$this->analytics->data_ga->get(
                          'ga:' . $profileId,
                           $start_date,
                           $end_date,
                           $metrics_string,
                           $parameters);

                          //print_r($result);
                          $rows=$result->getRows();
                          //$rows=$result['totalResults'];
                          $this->result = null;
                          //$result = array();

                          $report_root_parameters = array(); 
                          //$result->setUseObjects(true);


                          $totals = $result->gettotalsForAllResults();		 
                          foreach ($totals as  $metricName => $metricTotal )
                          { 
                            $report_root_parameters[str_replace('ga:','',$metricName)]=$metricTotal; 
                          }   

                          $report_root_parameters['totalResults']=$result->gettotalResults();
                          $report_root_parameters['nextLink']=$result->getnextLink();
                          $report_root_parameters['selfLink']=$result->getselfLink();
                          $report_root_parameters['haveData']=count($rows)>0 ? true:false;
                          if(count($rows)>0)
                          {
                          $metrics=Array();$dimensions = array();
                          foreach ($result->getColumnHeaders() as $header) {

                             $metrics[]=str_replace('ga:','',$header->getName());
                             if($header->getColumnType()=='DIMENSION')
                             {
                               $dimensions[]=str_replace('ga:','',$header->getName());
                             }
                      }
                          foreach ($rows as $row) {
                            $metric=Array();$dimension=Array();$i=0;
                            foreach ($row as $cell) {

                                            $metric[$metrics[$i]]=$cell;
                                            $i++;

                            }
                            foreach($dimensions as $v)
                            {
                              $dimension[$v]=$metric[$v];
                              //print($dimension[$v]);
                            }
                           $results[]= new gapiReportEntry ($metric,$dimension);
                          }
                          }

                   $this->report_root_parameters = $report_root_parameters;

                    $this->results = $results;

                  return $results;

      }  catch ( Exception $e) {


            throw new  Exception( $e->getMessage() );

      } 

      return '';

    }
    
    protected function processFilter($filter)
    {
      $valid_operators = '(!~|=~|==|!=|>|<|>=|<=|=@|!@)';

      $filter = preg_replace('/\s\s+/',' ',trim($filter)); //Clean duplicate whitespace
      $filter = str_replace(array(',',';'),array('\,','\;'),$filter); //Escape Google Analytics reserved characters
      $filter = preg_replace('/(&&\s*|\|\|\s*|^)([a-z]+)(\s*' . $valid_operators . ')/i','$1ga:$2$3',$filter); //Prefix ga: to metrics and dimensions
      $filter = preg_replace('/[\'\"]/i','',$filter); //Clear invalid quote characters
      $filter = preg_replace(array('/\s*&&\s*/','/\s*\|\|\s*/','/\s*' . $valid_operators . '\s*/'),array(';',',','$1'),$filter); //Clean up operators

      if(strlen($filter)>0)
      {
        return  ($filter);
      }
      else 
      {
        return false;
      }
    } 
    /**
     * Get Results
     *
     * @return Array
     */
    public function getResults()
    {
      if(is_array($this->results))
      {
        return $this->results;
      }
      else 
      {
        return;
      }
    }
  
    /**
     * Styling: loading stylesheets for the plugin.
     */
    public function wpa_styles( $page ) {

        wp_enqueue_style( 'wp-siteoptimus-style', plugins_url('css/wp-siteoptimus-style.css', __FILE__));
    }

    /*
     * Actions perform on activation of plugin
     */
    function wpa_install() {
      


    }

    /*
     * Actions perform on de-activation of plugin
     */
    function wpa_uninstall() {
      
      
    }

}

new WP_SiteOptimus_Simple();