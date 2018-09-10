<?php class Google_Analytics {
 
	
    public function __construct() {
        require_once realpath(CGA_PLUGIN_BASEPATH . '/tools/src/Google/autoload.php');
        require_once realpath(CGA_PLUGIN_BASEPATH . '/includes/class-google-auth-api.php');
		$this->gAuth = new Google_Auth;
		$this->analytics = new Google_Service_Analytics($this->gAuth->getClientObject());
    }
    
    public function getAccounts() {
		$accessData = json_decode($_SESSION['CGA_sessionData']['token']);
		$accounts = $this->analytics->management_accounts->listManagementAccounts();
		$allAccounts = array();
		if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			foreach($items as $item){
				$allAccounts[] = array(
					'id'		=>	$item->id,
					'name'		=>	$item->name,
					'selfLink'	=>	$item->selfLink
				);
			}
		
		}
		return $allAccounts;
    }
    
    public function getListProperties($accountID, $days) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);
		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
		}
		$allProperties = array();
		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
			foreach($items as $item){
				$metadata = $this->getSiteMetadata($accountID, $item->id, $days);
				$metaResult = array();
				if(!empty($metadata)){
					$metaResult = array(
						'sessions'				=>	$metadata->totalsForAllResults['ga:sessions'],
						'sessionDuration'		=>	$this->calculateTime($metadata->totalsForAllResults['ga:avgSessionDuration']),
						'goalConversionRate'	=>	number_format($metadata->totalsForAllResults['ga:goalConversionRateAll'], 2).'%',
						'bounceRate'			=>	number_format($metadata->totalsForAllResults['ga:bounceRate'], 2).'%'
					);
				}
				$allProperties[] = array(
					'id'			=>	$item->id,
					'name'			=>	$item->name,
					'selfLink'		=>	$item->selfLink,
					'accountId'		=>	$item->accountId,
					'websiteUrl'	=>	$item->websiteUrl,
					'metaData'		=>	$metaResult
				);
			}
		}
		return $allProperties;
	}
	
	
    public function getListGoals($accountID, $days) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);
		//$optParams = array('dimensions' => 'rt:date','dimensions' => 'rt:source','dimensions' => 'rt:medium');
		//$realtime = $this->analytics->data_realtime->get('ga:83047211','rt:activeVisitors',$optParams);
		//return $realtime;
		
		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
		}
		$allProperties = array();
		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
			foreach($items as $item){
				
				//$metadata = $this->getSiteMetadataGoalCycle($accountID, $webPropertyId = 'UA-48705008-1', $days);
				$metadata = $this->getSiteMetadata($accountID, $item->id, $days);

				$properties1 = $this->analytics->management_goals->listManagementGoals($accountID, $webPropertyId = $metadata['modelData']['profileInfo']['webPropertyId'], $profileId = $metadata['modelData']['profileInfo']['profileId']); 
				
				$goalData = $properties1['metaData']; 
				
				$funnelMeta = array();
				foreach($properties1 as $data){
					
					$id = $data['id'];
					$funnelMeta[] = $this->analytics->data_ga->get($profileId = 'ga:'.$metadata['modelData']['profileInfo']['profileId'], $startDate ='2016-07-13', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:pageviews,ga:goal'.$id.'Completions'); 
				}
				//$properties1 = $properties1['modelData']['items'];
				//$goalMeta = $this->analytics->management_goals->get($accountID, $webPropertyId = $metadata['modelData']['profileInfo']['webPropertyId'], $profileId = $metadata['modelData']['profileInfo']['profileId'], '1');

				
				if(!isset($_SESSION['profileId'])){
					$_SESSION['profileId'] = $metadata['modelData']['profileInfo']['profileId'];
				}
				
				$metaResult = array();
				if(!empty($metadata)){
					$metaResult = array(
						'sessions'				=>	$metadata->totalsForAllResults['ga:sessions'],
						'sessionDuration'		=>	$this->calculateTime($metadata->totalsForAllResults['ga:avgSessionDuration']),
						'goalConversionRate'	=>	number_format($metadata->totalsForAllResults['ga:goalConversionRateAll'], 2).'%',
						'bounceRate'			=>	number_format($metadata->totalsForAllResults['ga:bounceRate'], 2).'%'
					);
				}
				$allProperties[] = array(
					'id'			=>	$item->id,
					'name'			=>	$item->name,
					'selfLink'		=>	$item->selfLink,
					'accountId'		=>	$item->accountId,
					'websiteUrl'	=>	$item->websiteUrl,
					'metaResult'	=>	$metaResult,
					'metaData'		=>	$properties1,
					//'goalData'		=>	$funnelMeta,
					'goalData'		=>	$funnelMeta,
					'profileID'		=>	$metadata['modelData']['profileInfo']['profileId'],
				);
			}
		}
		return $allProperties;
	}
	
	// created by-rahul - For Goal Source Meta Data
	public function getListGoalsMeta($accountID,$profileID, $days, $goalID = 0) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);

		//$optParams = array('dimensions' => array('rt:operatingSystem','rt:medium','rt:deviceCategory','rt:mobileDeviceModel', 'rt:campaign', 'rt:browser','rt:region'));
		
		//$optParams = array('dimensions' => 'rt:medium','dimensions' => 'rt:deviceCategory','dimensions' => 'rt:mobileDeviceModel','dimensions' => 'rt:campaign','dimensions' => 'rt:browser','dimensions' => 'rt:region','dimensions' => 'rt:operatingSystem');
		$optParams = array('dimensions' =>'rt:operatingSystem,rt:medium,rt:deviceCategory,rt:mobileDeviceModel,rt:campaign,rt:browser,rt:region');
		
		
		$realtime = $this->analytics->data_realtime->get($profileId = 'ga:'.$profileID,'rt:activeUsers',$optParams);
		$realtime['siteName'] = $properties['modelData']['items'][0]['name'];
		
		if($goalID > 0){
			
			$funnelMeta = array();
			$funnelMeta = $this->analytics->data_ga->get($profileId = 'ga:'.$profileID, $startDate ='2016-07-13', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:pageviews,ga:goal'.$goalID.'Completions');
			$realtime['goalMeta'] = $funnelMeta;
		}
		
		return $realtime;
		
		/*if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
		}
		$realtime = array();
		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
			foreach($items as $item){
				
				//$metadata = $this->getSiteMetadataGoalCycle($accountID, $webPropertyId = 'UA-48705008-1', $days);
				$metadata = $this->getSiteMetadata($accountID, $item->id, $days);
				
				$metaResult = array();
				if(!empty($metadata)){
					$metaResult = array(
						'sessions'				=>	$metadata->totalsForAllResults['ga:sessions'],
						'sessionDuration'		=>	$this->calculateTime($metadata->totalsForAllResults['ga:avgSessionDuration']),
						'goalConversionRate'	=>	number_format($metadata->totalsForAllResults['ga:goalConversionRateAll'], 2).'%',
						'bounceRate'			=>	number_format($metadata->totalsForAllResults['ga:bounceRate'], 2).'%'
					);
				}
				$allProperties[] = array(
					'id'			=>	$item->id,
					'name'			=>	$item->name,
					'selfLink'		=>	$item->selfLink,
					'accountId'		=>	$item->accountId,
					'websiteUrl'	=>	$item->websiteUrl,
					'metaResult'	=>	$metaResult,
					'metaData'		=>	$properties1,
					'goalData'		=>	$funnelMeta,
				);
			}
		}
		return $allProperties;*/
	}
	
	function getReport(&$analytics) {

	  // Replace with your view ID. E.g., XXXX.
	  $VIEW_ID = "77928420";
	
	  // Create the DateRange object.
	  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
	  $dateRange->setStartDate("7daysAgo");
	  $dateRange->setEndDate("today");

	  // Create the Metrics object.
	  $sessions = new Google_Service_AnalyticsReporting_Metric();
	  $sessions->setExpression("ga:sessions");
	  $sessions->setAlias("sessions");

	  // Create the ReportRequest object.
	  $request = new Google_Service_AnalyticsReporting_ReportRequest();
	  $request->setViewId($VIEW_ID);
	  $request->setDateRanges($dateRange);
	  $request->setMetrics(array($sessions));

	  //$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
	  //$body->setReportRequests( array( $request) );
	  //return $analytics->reports->batchGet( $body );
	  return  $request ;
	}
	
    public function getPropertyViews($accountID, $propertyID, $days) {
		$profiles = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID);
		if (count($profiles->getItems()) > 0) {
			$items = $profiles->getItems();
			$results = $this->getResults($this->analytics, $items[0]->getId(), $days);
			$this->printResults($results, $accountID, $propertyID);
		}else{
			throw new Exception('No views (profiles) found for this user.');
		}
	}
	
    public function getSiteMetadata($accountID, $propertyID, $days) {
		$profiles = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID);
		$results = array();
		if (count($profiles->getItems()) > 0) {
			$items = $profiles->getItems();
			$results = $this->getResults($this->analytics, $items[0]->getId(), $days);
			return $results;
		} else {
			return $results;
		}
	}
	
	public function getSiteMetadataGoalCycle($accountID, $propertyID, $days) {		// For Goal Cycle Meta Data
		//$profiles = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID);
		$profiles = $this->analytics->management_goals->listManagementGoals($accountID, $propertyID);
		return $profiles;
		
		$results = array();
		if (count($profiles->getItems()) > 0) {
			$items = $profiles->getItems();
			$results = $this->getResults($this->analytics, $items[0]->getId(), $days);
			return $results;
		} else {
			return $results;
		}
	}
	
	function getResults(&$analytics, $profileId, $days) {
	  // Calls the Core Reporting API and queries for the number of sessions
	  // for the last seven days.
	   return $analytics->data_ga->get(
		   'ga:' . $profileId,
		   $days.'daysAgo',
		   'today',
		   'ga:sessions,ga:avgSessionDuration,ga:goalConversionRateAll,ga:bounceRate,ga:pageviews,ga:users,ga:pageviewsPerSession,ga:uniquePageviews');
	}
	function printResults(&$results, $accountID, $propertyID) {
		// Parses the response from the Core Reporting API and prints
		// the profile name and total sessions.
		//echo '<pre>';
		//print_r($results);
		//die;
		if (count($results->getRows()) > 0) {
			$profiles = $this->analytics->management_profiles->listManagementProfiles($accountID, $propertyID);
			// Get the profile name.
			$profileName = $results->getProfileInfo()->getProfileName();

			// Get the entry for the first entry in the first row.
			$rows = $results->getRows();
			$sessions = $rows[0][0];
			// Print the results.
			echo "Site Name: ".$_GET['propertyname']."\n <br />";
			echo "ID: ".$_GET['property']."\n <br />";
			print "First view (profile) found: $profileName\n <br />";
			print "Total sessions: $sessions\n  <br />";
			print "Total users: ".$results->totalsForAllResults['ga:users']."\n  <br />";
			print "Total pageviews: ".$results->totalsForAllResults['ga:pageviews']."\n  <br />";
			print "Total unique pageviews: ".$results->totalsForAllResults['ga:uniquePageviews']."\n  <br />";
			print "Pages / Session: ".number_format($results->totalsForAllResults['ga:pageviewsPerSession'], 2)."\n  <br />";
			print "Avg. Session Duration: ".$this->calculateTime($results->totalsForAllResults['ga:avgSessionDuration'])."\n  <br />";
			print "Bounce Rate: ".number_format($results->totalsForAllResults['ga:bounceRate'], 2).'%'."\n  <br />";
			print "Total users: ".$results->totalsForAllResults['ga:users']."\n  <br />";
		} else {
			print "No results found.\n";
		}
	}
	
	
	
	function calculateTime ($seconds){
		$days = floor ($seconds / 86400);
		if ($days > 1) // 2 days+, we need days to be in plural
			return $days . ' days ' . gmdate ('H:i:s', $seconds);
		else if ($days > 0) // 1 day+, day in singular
			return $days . ' day ' . gmdate ('H:i:s', $seconds);
		return gmdate ('H:i:s', $seconds);
	}

 
}
