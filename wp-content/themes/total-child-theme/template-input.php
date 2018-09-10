<?php
/*
Template Name: Input
*/

// bbPress fix while they update things...
if ( is_singular() || ( function_exists( 'is_bbpress' ) && is_bbpress() ) ) {
	get_template_part( 'singular' );
	return;
}

// Get header
get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content" role="main">
                            
                            <?php wpex_hook_content_top(); ?>
                            
                                <?php
				// YOUR POST LOOP STARTS HERE
				while ( have_posts() ) : the_post(); ?>

					<?php if ( has_post_thumbnail() && wpex_get_mod( 'page_featured_image' ) ) : ?>

						<div id="page-featured-img" class="clr">
							<?php the_post_thumbnail(); ?>
						</div><!-- #page-featured-img -->

					<?php endif; ?>
                                                
					<div class="entry-content entry clr">
						<?php the_content(); ?>
                                                This is a test.
                                                <?php
                                                    //ob_start(); //batches header sending
                                                    //include_once(plugins_url( 'includes/analyticstracking.php', __FILE__ ));
                                                    //require_once('includes/sessionsets.php');
                                                    //require_once('Connections/Algo.php');
                                                    //require_once('includes/functions3.php');
                                                    //require_once('includes/declaregapi.php');
                                            
                                                    add_action( 'gform_after_submission_5', 'so_add_entry_to_db', 10, 2 );
                                                    function so_add_entry_to_db($entry, $form) {

                                                        // uncomment to see the entry object 
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
                                                      
                                                        $wp_siteoptimus = new WP_siteoptimus_Simple();

                                                        $startdatetime =  time();
                                                        $currdatetime = date("Y-m-d H:i:s", $startdatetime);
                                                        $acct = $entry[1];
                                                        //echo $acct;
                                                        $dim = $entry[2];
                                                        $whichdim = $entry[2];
                                                        $startdt = date("Y-m-d", strtotime($entry[3]));
                                                        $enddt = date("Y-m-d", strtotime($entry[4]));
                                                        $denset = 'Visits';
                                                        $goal = $entry[5];
                                                        $gapidenset = "get" . $denset;
                                                        $gapigoal = "get" . $goal;

                                                        define('ga_profile_id',$acct);

                                                        //$filter =$goal.">4";
                                                        //unset($filter_a);
                                                        //$filter_a[0]=$goal.">4";
                                                        $filter="";

                                                        //print_r($filter_a);

                                                        if(isset($filter_a))
                                                        {

                                                            foreach ($filter_a as $key => $value)
                                                            {
                                                                $filter .= $value . "&&";
                                                            }
                                                            $filter = substr($filter,0,strlen($filter)-2);
                                                        }

                                                        else
                                                        {
                                                                $filter="";
                                                        }
                                                        
                                                        require_once dirname(__FILE__) . '/lib/GAPI/gapi.class.php';
                                                        //require 'gapi.class.php';
                                                        //require 'src/Google_Client.php';
                                                        //require_once 'src/contrib/Google_Oauth2Service.php';
                                                        //require 'src/contrib/Google_AnalyticsService.php';


                                                        // Build a new client object to work with authorization.


                                                        global $client;
                                                        $client = new Google_Client();
                                                        $client->setClientId('380690704201.apps.googleusercontent.com');
                                                        $client->setClientSecret('m_zZsGyW7Xi9LpNMDS-O7cQF');
                                                        $client->setRedirectUri('http://siteoptimus.com/Test10.php');
                                                        $client->setApplicationName('test');
                                                        $client->setDeveloperKey('AIzaSyD1u7OjiYa8qbQa_uu_VwQy_SBlwEVdjRc');
                                                        $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

                                                        $client->setUseObjects(true);
                                                        $client->setUseObjects(true);
                                                        $accessToken= $_SESSION['token'];
                                                        if(empty($accessToken))
                                                        {
                                                        header('Location:getGatoken.php');
                                                        }
                                                        $client->setAccessToken($accessToken);
                                                        if($client->isAccessTokenExpired())
                                                        {
                                                            $tokenObj = json_decode($accessToken);
                                                            $client->refreshToken($tokenObj->refresh_token);
                                                            $_SESSION['token']=$client->getAccessToken();
                                                        }
                                                        ob_end_flush(); //sends header info

                                                        global $analytics;
                                                        $analytics = new Google_AnalyticsService($client);

                                                        global $ga;
                                                        $ga = new gapi($analytics); 

                                                        if (isset($_GET['logout'])) {
                                                          unset($_SESSION['token']);
                                                        }

                                                        if (isset($_GET['code'])) {
                                                          $client->authenticate();
                                                          $_SESSION['token'] = $client->getAccessToken();
                                                          $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                                                          header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
                                                        }

                                                        if (isset($_SESSION['token'])) {
                                                          $client->setAccessToken($_SESSION['token']);
                                                        }

                                                        if ($client->getAccessToken()) {
                                                          $props = $analytics->management_webproperties->listManagementWebproperties("~all");

                                                          $accounts = $analytics->management_accounts->listManagementAccounts();


                                                          $segments = $analytics->management_segments->listManagementSegments();


                                                          $goals = $analytics->management_goals->listManagementGoals("~all", "~all", "~all");

                                                        global $profiles;
                                                          $profiles = $analytics->management_profiles->listManagementProfiles("~all", "~all");


                                                          $_SESSION['token'] = $client->getAccessToken();
                                                        } else {
                                                          $authUrl = $client->createAuthUrl();
                                                          print "<a class='login' href='$authUrl'>Connect Me!</a>";
                                                        }

                                                        
                                                        //query for data
                                                        $ga->requestReportData(ga_profile_id,array($dim),array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
                                                        $middatetime =  time();

                                                        $_SESSION['acct'] = $acct;
                                                        $_SESSION['whichdim'] = $whichdim;
                                                        $_SESSION['dim'] = $dim;
                                                        $_SESSION['startdt'] = $startdt;
                                                        $_SESSION['enddt'] = $enddt;
                                                        $_SESSION['denset'] = $denset;
                                                        $_SESSION['goal'] = $goal;
                                                        $_SESSION['filter'] = serialize($filter_a);

                                                        //mysqli_query($Algo,"truncate tbl_dim_den_num");

                                                        foreach($wp_siteoptimus->getResults() as $result)
                                                        {
                                                                //
                                                                //echo ( $result . $result->$gapidenset() . $result->$gapigoal() );
                                                                echo '<pre>';
                                                                print_r($result);    
                                                                echo '</pre>';
                                                                $wpdb->insert(
                                                                        'tbl_dim_den_num',
                                                                        array(
                                                                            'whichdim'=>$whichdim,
                                                                            'dim'=>$result,
                                                                            'den'=>$result->$gapidenset(),
                                                                            'num'=>$result->$gapigoal()
                                                                        ) 
                                                                );
                                                        }

                                                        echo "<br/>Done!</br>";time();
                                                        $enddatetime = time();
                                                        $midduration = $middatetime - $startdatetime;
                                                        $duration = $enddatetime - $startdatetime;


                                                          // add form data to custom database table
                                                          $wpdb->insert(
                                                              'tbl_code_run_log',
                                                              array(
                                                                'typeid' => 1,
                                                                'source' => $source,
                                                                'goal' => $email,
                                                                'dim' => $whichdim,
                                                                'acct' => $acct,
                                                                'stdate' => $stdate,
                                                                'enddate' => $enddate,
                                                                'DateTime' => current_time( 'mysql' )
                                                              )
                                                          );

                                                    }

                                                ?>

                                        </div><!-- .entry-content -->

				<?php
				// YOUR POST LOOP ENDS HERE
				endwhile; ?>
                            
                            
                            
                            <?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

		<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->
	
<?php get_footer(); ?>