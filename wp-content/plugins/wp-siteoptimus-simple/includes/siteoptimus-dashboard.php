<?php
$wp_siteoptimus = new WP_siteoptimus_Simple();

$start_date_val =   strtotime("- 30 days"); 
$end_date_val   =   strtotime("now");
$start_date     =   date( "Y-m-d", $start_date_val);
$end_date       =   date( "Y-m-d", $end_date_val);

?>
<div class="wrap">
  <h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url('wp-siteoptimus-simple/images/wp-analytics-logo.png');?>" alt=""></span>
    <?php echo __( 'siteoptimus Simple Dashboard', 'wp-siteoptimus' ); ?>
  </h2>
  <?php

  $acces_token  = get_option( "access_code" );
  if( $acces_token ) {
  
  ?>
  <div id="col-container">
    <div class="metabox-holder">
      <div class="postbox" style="width:100%;">
          <div id="main-sortables" class="meta-box-sortables ui-sortable">
            <div class="postbox ">
              <div class="handlediv" title="Click to toggle"><br />
              </div>
              <h3 class="hndle">
                <span>
                    <?php 
                    echo _e('Complete Statistics Starting From ', 'wp-siteoptimus'); 
                    echo _e(date("jS F, Y", strtotime($start_date))); 
                    echo _e(' to ', 'wp-siteoptimus'); 
                    echo _e(date("jS F, Y", strtotime($end_date))); 
                    ?>
                </span>
              </h3>
              <div class="inside">
                <?php

                // Country stats //
                
                $country_stats = $wp_siteoptimus->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', false, 5);
                if ( isset( $country_stats->totalsForAllResults )) {
                  include ROOT_PATH . '/views/country-stats.php'; 
                  pa_include_country($wp_siteoptimus,$country_stats);
                }
              
                // End Country stats //

                $city_stats = $wp_siteoptimus->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', false, 5);
                if ( isset( $city_stats->totalsForAllResults )) {
                  include ROOT_PATH . '/views/city-stats.php'; 
                  pa_include_city($wp_siteoptimus,$city_stats);
                }


                // Browser stats //

                $browser_stats = $wp_siteoptimus->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',false,5);
                if ( isset( $browser_stats->totalsForAllResults ) ) {
                  include ROOT_PATH . '/views/browser-stats.php'; 
                  pa_include_browser( $wp_siteoptimus,$browser_stats );
                }

                // End Browser stats //

                ?>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php 
  } 
  else{
    print(_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-siteoptimus' ));
  }
  ?>
</div>