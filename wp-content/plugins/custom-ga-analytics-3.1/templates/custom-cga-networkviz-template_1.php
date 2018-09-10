
<?php
/*
 * Template: Custom Google Analytics Goal Cycle MetaData Template
*/

set_time_limit ( 300 );

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
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


get_header();

$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

if ( is_user_logged_in() ) {
        // Current user is logged in,
        // so let's get current user info
        global $current_user;
        $current_user = wp_get_current_user();
        // User ID
        $user_id = $current_user->ID;
        $mem->set('userid',$user_id);
}

?>

<link rel="stylesheet" type="text/css" href=<?php echo (CGA_PLUGIN_BASEURL . "assets/css/styles.css"); ?>>
	<style>
    body {

    }

    .chart {
       
    }

    .link {
      fill: none;
      stroke: #3498db;
      stroke-linecap: round;
    }

    circle {
      fill: #3498db;
      stroke-width: 1.5;
    }

    .high circle {
      fill: #e74c3c;
    }

    .med circle {
      fill: #f39c12;
    }

    .low circle {
      fill: #2ecc71;
    }

    .info {
      position: absolute;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    .info .value {
      font-weight: bold;
      font-size: 1.5em;
      padding: 4px;
    }
 
  </style>
	<script src=<?php echo (CGA_PLUGIN_BASEURL . "assets/js/d3.v4.min.js"); ?>></script>
	

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content clr">

				<?php wpex_hook_content_top(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wpex_get_template_part( 'page_single_blocks' ); ?>

				<?php endwhile; ?>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->


        <script src=<?php echo (CGA_PLUGIN_BASEURL . "assets/js/tree.js"); ?>></script>
	<div class="container">
		<div class="control">
			<button class="reset">Reset</button>
		</div>
		<div class="chart-container">
			<svg class="chart"></svg>
		</div>
	</div>
	
	<script>
		(function() {
			show("http://siteoptimus.com/memc_test.php");
		})();
	</script>
                            
<?php
//echo '<a href="http://siteoptimus.com/site-optimus-dashboard/#">Analyze different domain</a></br></br>';

if(isset($_GET['account'])){
    $days=$_GET['days'];
}
else
{
$days=30;

}

if(!empty($gaAccounts)){
	if(isset($_GET['account']) && $_GET['account'] != '' && isset($_GET['property']) && $_GET['property'] != '' ){
		//$gAnalytics->getPropertyViews($_GET['account'], $_GET['property'], $_GET['days']);
		
		if(isset($_GET['goalID'])){
			
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days, $_GET['goalID']);
                        if(isset($_GET['filter'])){
                            $gaOpportunities = $gAnalytics->getOppsCyclev31($_GET['account'],$_GET['profileId'], date('Y-m-d', strtotime($_GET['startDate'])), date('Y-m-d', strtotime($_GET['endDate'])), $_GET['goalID'], $_GET['filter']);
                        }
                        else{
                            $gaOpportunities = $gAnalytics->getOppsCyclev31($_GET['account'],$_GET['profileId'], date('Y-m-d', strtotime($_GET['startDate'])), date('Y-m-d', strtotime($_GET['endDate'])), $_GET['goalID'], NULL);
                        }
                        
                        $goalID = $_GET['goalID'];

                        if(isset($_GET['goalcompletes'])){
                            $mem->set($user_id.'goalcompletes',$_GET['goalcompletes']);
                        }
                        if(isset($_GET['sessions'])){
                            $mem->set($user_id.'sessions',$_GET['sessions']);
                        }
                        
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities,$goalID);

			
                        //$mem->set($user_id.'dimdennum',json_encode($gaOpportunitiesf));
                        
		}else {
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days);
                        $gaOpportunities = $gAnalytics->getOppsCyclev3($_GET['account'],$_GET['profileId'], $days, NULL);
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities);

                        //$mem->set($user_id.'dimdennum',json_encode($gaOpportunitiesf));
                        
		}

		if(!isset($_GET['goalID']) && !isset($_GET['allGoalComplete'])){
			if($gaPropereties['totalResults']) {
				
				echo '<div class="goalMetaData">';
				echo "<h2>Site Name: ".$gaPropereties['siteName']."</h2>";
				echo "<h2>Total Active User: ".$gaPropereties['totalResults']."</h2>";
				
					
					$last_names = array_column($columHeader, 'name');
					$i = 1;
					foreach($rowValue as $item){
						
						echo "<h3>User ".$i."</h3>";
						$dataArray = array_combine($last_names, $item);
						foreach($dataArray as $key => $value){
							
							$key = str_replace('rt:','',$key);
							if($key != 'activeUsers'){
								
								$key = setStringColumnHeader($key);
								echo '<b>'.$key.'</b> : '.$value." <br />";
							}
						}
						echo "<br /> ";
						$i++;
					}
				echo "</div>";
			}else {
				echo "<center><h1>There are no results during this time period</h1></center>";
			}
		}
		
		if(isset($_GET['goalID']) || isset($_GET['allGoalComplete'])){
			//echo '<div class="goalMetaData">';
				echo "Site Name: ".$gaPropereties['siteName'];
                                //echo "<h2>ID : ".$gaPropereties['ID']."</h2>";
                                //echo "<h2>Opps : ".print_r($gaOpportunities)."</h2>";
                                //echo "<h2>Oppsf" . " : ".print_r($gaOpportunitiesf)."</h2>";
                                //echo do_shortcode("[wpdatatable id=16]");
                                //echo "<h2>Profile : ".printProfileInformation($gaOpportunities)."</h2>";
                                //echo do_shortcode("[wpdatatable id=17]");
                                //echo do_shortcode("[wpdatachart id=4]");
                                //echo '<audio autoplay>';
                                //echo '<source src="http://siteoptimus.com/MagicMoment.mp3" type="audio/mpeg">';
                                //echo '</audio>';
                                
			if(isset($gaPropereties['goalMeta'])){
                                //$mem->set('sessions',$gaPropereties['goalMeta']['totalsForAllResults']['ga:sessions']);	
                                //echo "<b>Sessions :</b> ".$gaPropereties['goalMeta']['totalsForAllResults']['ga:sessions']." <br />";
                                echo "<b>Sessions :</b> ".$mem->get($user_id.'sessions')." <br />";
                                //$mem->set('goalcompletes',$gaPropereties['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']);
				echo "<b>Goal Completes :</b> ".$mem->get($user_id.'goalcompletes')." <br />";
                                //print_r($gaPropereties);
                                //echo "<b>Goal Cycle :</b> ".$gaOpportunities['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']." <br />";
                                //$gAnalytics->printDataTable($gaOpportunities['ga:source']);

                                //$gAnalytics->printCycleTableExp($gaOpportunities);
                                //echo $gAnalytics->calcOpps(123,1124,12344,1325123512);
			}
			if(isset($_GET['allGoalComplete'])){
				echo "<b>Sessions :</b> ".$mem->get($user_id.'sessions')." <br />";
                                //echo "<b>Sessions :</b> ".$_GET['session']." <br />";
                                echo "<b>All Goal Completions :</b> ".$mem->get($user_id.'goalcompletes')." <br />";
				//echo "<b>All Goal Completions :</b> ".$_GET['allGoalComplete']." <br />";
			}
			//echo "</div>";
		}
	}
}
?>

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->

<?php
get_footer();
$mem->quit();
