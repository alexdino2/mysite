
<?php
/*
 * Template: Custom Google Analytics Goal Cycle MetaData Template
*/



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

function setStringColumnHeader($columnHeader){
	
	$stringHeader = '';
	switch($columnHeader){
		case 'operatingSystem':
			echo 'Operating System';
		break;
		case 'medium':
			echo 'Medium';
		break;
		case 'deviceCategory':
			echo 'Device Category ';
		break;
		case 'mobileDeviceModel':
			echo 'Mobile Device Model ';
		break;
		case 'campaign':
			echo 'Campaign ';
		break;
		case 'browser':
			echo 'Browser ';
		break;
		case 'region':
			echo 'Region ';
		break;
		case 'activeUsers':
			echo 'Active Users ';
		break;
		default:
			echo 'Column Header';
	}
	//return $stringHeader;
}
if(isset($_GET['account'])){
$days=$_GET['days'];
}else{$days=30;}

//$mem = new Memcached();
//$mem->addServer("soredis.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

if(!empty($gaAccounts)){
	if(isset($_GET['account']) && $_GET['account'] != '' && isset($_GET['property']) && $_GET['property'] != '' ){
		//$gAnalytics->getPropertyViews($_GET['account'], $_GET['property'], $_GET['days']);
		
		if(isset($_GET['goalID'])){
			
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days, $_GET['goalID']);
                        if(isset($_GET['filter'])){
                            $gaOpportunities = $gAnalytics->getOppsCyclev3($_GET['account'],$_GET['profileId'], $days, $_GET['goalID'], $_GET['filter']);
                        }
                        else{
                            $gaOpportunities = $gAnalytics->getOppsCyclev3($_GET['account'],$_GET['profileId'], $days, $_GET['goalID'], NULL);
                        }
                        
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities);
                        $gaOpportunitiestd = $gAnalytics->make_tree_data($gaOpportunities);
                        //print_r ($gaOpportunities);
                        //$mem->set('dimdennum',json_encode($gaOpportunitiesf));
                        //$gAnalytics->saveCycleTable($gaOpportunitiesf);
			$goalID = $_GET['goalID'];
		}else {
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days);
                        $gaOpportunities = $gAnalytics->getOppsCyclev3($_GET['account'],$_GET['profileId'], $days, NULL);
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities);
                        $gaOpportunitiestd = $gAnalytics->make_tree_data($gaOpportunities);
                        //
                        //$mem->set('dimdennum',json_encode($gaOpportunitiesf));
                        //$gAnalytics->saveCycleTable($gaOpportunitiesf);
		}

                /*
		$rowValue = array();
		$columHeader = array();
		
		$columHeader = $gaPropereties['modelData']['columnHeaders'];
		$rowValue = $gaPropereties['rows'];
		//echo "<pre>"; print_r($gaPropereties); echo "</pre>";
		
                 * 
                 */
		if(!isset($_GET['goalID']) && !isset($_GET['allGoalComplete'])){
			if($gaPropereties['totalResults']) {
				
				echo '<div class="goalMetaData">';
				
				echo "<h2>Site Name : ".$gaPropereties['siteName']."</h2>";
				echo "<h2>Total Active User : ".$gaPropereties['totalResults']."</h2>";
				
					
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
			echo '<div class="goalMetaData">';
				echo "<h2>Site Name : ".$gaPropereties['siteName']."</h2>";
                                //echo "<h2>ID : ".$gaPropereties['ID']."</h2>";
                                //echo "<h2>Opps : ".print_r($gaOpportunities)."</h2>";
                                //echo "<h2>Oppsf" . " : ".print_r($gaOpportunitiesf)."</h2>";
                                //echo do_shortcode("[wpdatatable id=16]");
                                //echo "<h2>Profile : ".printProfileInformation($gaOpportunities)."</h2>";
                                echo do_shortcode("[wpdatatable id=16]");
                                echo do_shortcode("[wpdatachart id=4]");
                                
			if(isset($gaPropereties['goalMeta'])){
				echo "<b>Sessions :</b> ".$gaPropereties['goalMeta']['totalsForAllResults']['ga:sessions']." <br />";
				echo "<b>Goal Completes :</b> ".$gaPropereties['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']." <br />";
                                //print_r($gaPropereties);
                                //echo "<b>Goal Cycle :</b> ".$gaOpportunities['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']." <br />";
                                //$gAnalytics->printDataTable($gaOpportunities['ga:source']);

                                //$gAnalytics->printCycleTableExp($gaOpportunities);
                                //echo $gAnalytics->calcOpps(123,1124,12344,1325123512);
			}
			if(isset($_GET['allGoalComplete'])){
				echo "<b>Sessions :</b> ".$_GET['session']." <br />";
				echo "<b>All Goal Completions :</b> ".$_GET['allGoalComplete']." <br />";
			}
			echo "</div>";
		}
	}
}
get_footer();
