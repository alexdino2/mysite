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
	public function getOppsv3($accountID,$profileID, $days, $goalID = 0) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);

		//$optParams = array('dimensions' => array('rt:operatingSystem','rt:medium','rt:deviceCategory','rt:mobileDeviceModel', 'rt:campaign', 'rt:browser','rt:region'));
		
		//$optParams = array('dimensions' => 'rt:medium','dimensions' => 'rt:deviceCategory','dimensions' => 'rt:mobileDeviceModel','dimensions' => 'rt:campaign','dimensions' => 'rt:browser','dimensions' => 'rt:region','dimensions' => 'rt:operatingSystem');
		$optParams = array('dimensions' =>'ga:browser');
		
		
		$realtime = $this->analytics->data_realtime->get($profileId = 'ga:'.$profileID,'rt:activeUsers',$optParams);
		$realtime['siteName'] = $properties['modelData']['items'][0]['name'];
		
		if($goalID > 0){
			
			$funnelMeta = array();
			$funnelMeta = $this->analytics->data_ga->get($profileId = 'ga:'.$profileID, $startDate ='2016-07-13', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:goal'.$goalID.'Completions',$optParams);
			$realtime['goalMeta'] = $funnelMeta;
		}
		
		return $realtime['goalMeta'];
		

	}
        public function getOppsCyclev3($accountID,$profileID, $days, $goalID = 0, $filter = NULL) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);

		//$optParams = array('dimensions' => array('rt:operatingSystem','rt:medium','rt:deviceCategory','rt:mobileDeviceModel', 'rt:campaign', 'rt:browser','rt:region'));
		$arr = array('ga:medium', 'ga:deviceCategory','ga:browser','ga:operatingSystem','ga:region', 'ga:campaign','ga:browserSize','ga:interestInMarketCategory','ga:interestAffinityCategory');
                //$arr = array('ga:browser', 'ga:medium', 'ga:deviceCategory');
		//$optParams = array('dimensions' => 'rt:medium','dimensions' => 'rt:deviceCategory','dimensions' => 'rt:mobileDeviceModel','dimensions' => 'rt:campaign','dimensions' => 'rt:browser','dimensions' => 'rt:region','dimensions' => 'rt:operatingSystem');
                

                
                foreach ($arr as $whichdim1) {
                    //$optParams = array('dimensions' =>$whichdim1);
                            if ($filter) {
                                $optParams = array('dimensions' =>$whichdim1,'filters'=>$filter);
                            }
                            else{
                                $optParams = array('dimensions' =>$whichdim1);
                            }
                    $ga = $this->analytics->data_ga->get($profileId = 'ga:'.$profileID, $startDate ='2016-12-01', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:goal'.$goalID.'Completions',$optParams);
                    //$ga['siteName'] = $properties['modelData']['items'][0]['name'];

                    $results[] = array("whichdim"=>$whichdim1,"data"=>$ga);
                }
                
                //$newArray = array();
                //$i=0;
                
		return $results;
                
	}


        //need to modify to cycle through the dims
        public function getOppsv4($accountID,$profileID, $days, $goalID = 0) {
		$properties = $this->analytics->management_webproperties->listManagementWebproperties($accountID);
		//$optParams = array('dimensions' => array('rt:operatingSystem','rt:medium','rt:deviceCategory','rt:mobileDeviceModel', 'rt:campaign', 'rt:browser','rt:region'));
		
		//$optParams = array('dimensions' => 'rt:medium','dimensions' => 'rt:deviceCategory','dimensions' => 'rt:mobileDeviceModel','dimensions' => 'rt:campaign','dimensions' => 'rt:browser','dimensions' => 'rt:region','dimensions' => 'rt:operatingSystem');
		//$optParams = array('dimensions' =>'ga:date,ga:source, ga:medium, ga:deviceCategory,ga:mobileDeviceModel,ga:campaign,ga:landingPagePath,ga:sessionCount,ga:socialNetwork,ga:browser,ga:continent,ga:region,ga:flashVersion,ga:operatingSystem,ga:operatingSystemVersion,ga:networkDomain,ga:networkLocation,ga:screenResolution,ga:javaEnabled,ga:language,ga:yearWeek,ga:searchUsed,ga:userGender,ga:userAgeBracket,ga:interestOtherCategory,ga:interestAffinityCategory,ga:interestInMarketCategory,ga:channelGrouping');
		$VIEWID = '79476238';
                
		// Create the DateRange object.
                $dateRange = new Google_Service_AnalyticsReporting_DateRange();
                $dateRange->setStartDate("7daysAgo");
                $dateRange->setEndDate("today");
                
                // Create the Metrics objects.
                $sessions = new Google_Service_AnalyticsReporting_Metric();
                $sessions->setExpression("ga:sessions");
                $sessions->setAlias("sessions");
                $goal = new Google_Service_AnalyticsReporting_Metric();
                $goal -> setExpression('ga:goal'.$goalID.'Completions');
                $goal -> setAlias('goal'.$goalID.'Completions');
                
                // Create the segment dimension.
                $segmentDimensions = new Google_Service_AnalyticsReporting_Dimension();
                $segmentDimensions->setName("ga:segment");
                $browser = new Google_Service_AnalyticsReporting_Dimension();
                $browser->setName("ga:browser");
                
                // Create the ReportRequest object.
                $request = new Google_Service_AnalyticsReporting_ReportRequest();
                $request->setViewId($VIEWID);
                $request->setDateRanges(array($dateRange));
                $request->setDimensions(array($browser));
                //$request->setSegments(array($segment));
                $request->setMetrics(array($sessions,$goal));
                
                $funnelMeta = $this->analytics->data_ga->get($profileId = 'ga:'.$profileID, $startDate ='2016-07-13', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:pageviews,ga:goal'.$goalID.'Completions');
                echo "funnel:";
                print_r($funnelMeta);
                
                // Create an authorized analytics service object.
                $analytics = new Google_Service_AnalyticsReporting($client);
                $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
                $body->setReportRequests( array( $request) );
                return $analytics->reports->batchGet( $body );
                
                //$startdatetime =  time();
                //$currdatetime = date("Y-m-d H:i:s", $startdatetime);
                
                //echo $acct;
                //$dim = $_POST["selectDim"];
                //$startdt = date("Y-m-d", strtotime($_GET["startdt"]));
                //$enddt = date("Y-m-d", strtotime($_GET["enddt"]));
/*                $denset = 'ga:sessions';

                $gapidenset = "get" . $denset;
                $gapigoal = "get" . $goal;

                define('ga_profile_id',$acct);
                
                $arr = array('ga:source', 'ga:medium');
                //$arr = array('date','ga:source', 'ga:medium', 'deviceCategory','mobileDeviceModel','campaign','landingPagePath','sessionCount','socialNetwork','browser');

                //foreach ($arr as $whichdim2) {
                //$dimset[1]= $whichdim2;
                //$gapidimset1 = "get" . $dimset[1];
                
                foreach ($arr as $whichdim1) {
                    //$whichdim = $value;
                    $dimset[0]=$whichdim1;
                    $gapidimset0 = "get" . $dimset[0];
                    echo "Analyzing " . $whichdim1 . "<br/>"; 
                    //ob_end_flush();
                    //flush();
                    $ga->requestReportData($acct,$dimset,array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
                    $middatetime =  time();

                    foreach($ga->getResults() as $result)
                    {
                        //print_r($result);
                        echo "Analyzing " . $result->$gapidimset0(). "<br/>"; 
                        flush();
                        ob_flush();
                        mysqli_query($Algo,"insert into tbl_dim_den_num(acct,whichdim,dim,den,num) VALUES ('" . $acct. "','" . $whichdim1. "','" . $result->$gapidimset0(). "','" . $result->$gapidenset() . "','" . $result->$gapigoal() . "')");
                    }
                }

                
		//$realtime = $this->analytics->data_realtime->get($profileId = 'ga:'.$profileID,'rt:activeUsers',$optParams);
		//$realtime['siteName'] = $properties['modelData']['items'][0]['name'];
		
	if($goalID > 0){
			
			$funnelMeta = array();
			$funnelMeta = $this->analytics->data_ga->get($profileId = 'ga:'.$profileID, $startDate ='2016-07-13', $endDate = date("Y-m-d"), $metrics = 'ga:sessions,ga:pageviews,ga:goal'.$goalID.'Completions');
			$realtime['goalMeta'] = $funnelMeta;
		}
*/		

		//return $request;

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
	
	function printDataTable(&$results) {
            if (count($results->getRows()) > 0) {
              $table='';
              $table .= '<table>';

              // Print headers.
              $table .= '<tr>';

              foreach ($results->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
              }
              $table .= '</tr>';

              // Print table rows.
              foreach ($results->getRows() as $row) {
                $table .= '<tr>';
                  foreach ($row as $cell) {
                    $table .= '<td>'
                           . htmlspecialchars($cell, ENT_NOQUOTES)
                           . '</td>';
                  }
                $table .= '</tr>';
              }
              $table .= '</table>';

            } else {
              $table .= '<p>No Results Found.</p>';
            }
            print $table;
        }
        //custom function which combines all cycled 
        //dimensions into one table
        function printCycleTable(&$results) {
            if (count($results[0]['data']->getRows()) > 0) {
              $table='';
              $table .= '<table>';

              // Print headers.
              $table .= '<tr>';

              foreach ($results[0]['data']->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
              }
              $table .= '</tr>';

              foreach ($results as $whichdim){
                  
                // Print table rows.
                foreach ($whichdim['data']->getRows() as $row) {
                  $table .= '<tr>';
                    foreach ($row as $cell) {
                      $table .= '<td>'
                             . htmlspecialchars($cell, ENT_NOQUOTES)
                             . '</td>';
                    }
                  $table .= '</tr>';
                }
              }
              $table .= '</table>';

            } else {
              $table .= '<p>No Results Found.</p>';
            }
            print $table;
        }
        function printCycleTableExp(&$results) { //expand cycle table to include computed items
            if (count($results[0]['data']->getRows()) > 0) {
              $table='';
              $table .= '<table>';

              // Print headers.
              $table .= '<tr>';

              foreach ($results[0]['data']->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
              }
              $table .= '<th>' . 'Total sessions' . '</th>';
              $table .= '<th>' . 'Total goals' . '</th>';
              $table .= '<th>' . 'Opportunities' . '</th>';
              $table .= '</tr>';

              foreach ($results as $whichdim){
                  
                // Print table rows.
                foreach ($whichdim['data']->getRows() as $row) {
                  $table .= '<tr>';
                    foreach ($row as $cell) {
                      $table .= '<td>'
                             . htmlspecialchars($cell, ENT_NOQUOTES)
                             . '</td>';
                    }
                  //$table .= '</tr>';
                  //$table .= '<tr>';
                    $table .= '<td>';
                        $table .= htmlspecialchars($whichdim['data']->totalsForAllResults['ga:sessions'], ENT_NOQUOTES);
                    $table .= '</td>';
                    $table .= '<td>';
                        $table .= htmlspecialchars($whichdim['data']['totalsForAllResults']['ga:goal1Completions'], ENT_NOQUOTES); //need to change to allow multiple goals
                    $table .= '</td>';
                    $table .= '<td>';
                        //$table .= htmlspecialchars($row[1], ENT_NOQUOTES);
                        $table .= htmlspecialchars($this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']),ENT_NOQUOTES); //need to change to allow multiple goals
                    $table .= '</td>';
                  $table .= '</tr>';
                }
              }
              $table .= '</table>';

            } else {
              $table .= '<p>No Results Found.</p>';
            }
            print $table;
        }
        function printCycleTableExp2(&$results) { //expand cycle table to include computed items
            if (count($results[0]['data']->getRows()) > 0) {
              $table='';
              $table .= '<table>';

              // Print headers.
              $table .= '<tr>';

              foreach ($results[0]['data']->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
              }
              $table .= '<th>' . 'Total sessions' . '</th>';
              $table .= '<th>' . 'Total goals' . '</th>';
              $table .= '<th>' . 'Opportunities' . '</th>';
              $table .= '</tr>';

              foreach ($results as $whichdim){
                  
                // Print table rows.
                foreach ($whichdim['data']->getRows() as $row) {
                  $table .= '<tr>';
                    foreach ($row as $cell) {
                      $table .= '<td>'
                             . htmlspecialchars($cell, ENT_NOQUOTES)
                             . '</td>';
                    }
                  //$table .= '</tr>';
                  //$table .= '<tr>';
                    $table .= '<td>';
                        $table .= htmlspecialchars($whichdim['data']->totalsForAllResults['ga:sessions'], ENT_NOQUOTES);
                    $table .= '</td>';
                    $table .= '<td>';
                        $table .= htmlspecialchars($whichdim['data']['totalsForAllResults']['ga:goal1Completions'], ENT_NOQUOTES); //need to change to allow multiple goals
                    $table .= '</td>';
                    $table .= '<td>';
                        //$table .= htmlspecialchars($row[1], ENT_NOQUOTES);
                        $table .= htmlspecialchars($this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']),ENT_NOQUOTES); //need to change to allow multiple goals
                    $table .= '</td>';
                  $table .= '</tr>';
                }
              }
              $table .= '</table>';

            } else {
              $table .= '<p>No Results Found.</p>';
            }
            print $table;
        }
	function saveCycleTable(&$res) {
            if (count($res) > 0) {
                //$results = array();
                //$results = $res['data']->getRows();
                //$columns = implode(", ",array_keys($res['data']->getColumnHeaders()));
                $columns = 'whichdim,dim,den,num';
                $escaped_values = array_map('mysql_real_escape_string', array_values($res));
                $values  = implode(", ", $escaped_values);
                $sql = "INSERT INTO `tbl_dim_den_num`($columns) VALUES ($values)";
                
                echo 'sql:'.$sql;
                /*
                //echo 'columns:'.($columns);
                //foreach ($res as $whichdim){
                //    foreach ($whichdim['data']->getRows() as $row) {
                //        $escaped_values = array_map('mysql_real_escape_string', array_values($res['data']->getRows));
                //        $values  = implode(", ", $escaped_values);
                //   }
                //}
                */
                
                //$sql = "INSERT INTO `tbl_dim_den_num`(dim,den,num) VALUES ($values)";
                
                $hostname_Algo = "mysql.destinoanalytics.com:3306";
                $database_Algo = "dinolytics_algo";
                $username_Algo = "alexdino1";
                $password_Algo = "Bigcat8";

                //$Algo = mysql_pconnect($hostname_Algo, $username_Algo, $password_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
                $Algo = mysqli_connect($hostname_Algo, $username_Algo, $password_Algo, $database_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 

                mysqli_query($Algo,$sql);

            } else {

            }

        }
        
        function array_flatten($array) {
            $newarray = array();
            
            foreach($array as $whichdim) {
                
                foreach ($whichdim['data']->getRows() as $row) {
                    if ($row[1]>100) { //only keep if den is greater than 30
                        //need to append other parametercs to support hyperlinking
                        $newarray[]= array('whichdim'=>$whichdim['whichdim'],'dim'=>$row[0],'den'=>$row[1],'num'=>$row[2], 'opps'=>number_format((float)$this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']), 2, '.', ''),'id'=>$whichdim['data']['id']);
                        //$newarray[$whichdim['whichdim'].'.'.$row[0]]= array($row[1],$row[2]);
                        //$newarray[]= array('whichdim'=>$whichdim['whichdim'],'dim'=>$row[0],'num'=>$row[2], 'opps'=>number_format((float)$this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']), 2, '.', ''));
                    }

                }

            }
            
            return $newarray;
        }
        function make_tree_data($array) {
            $newarray = array();
            
            
            foreach($array as $whichdim) {
                
                foreach ($whichdim['data']->getRows() as $row) {
                    if ($row[1]>100) { //only keep if den is greater than 30
                        //need to append other parametercs to support hyperlinking
                        $newarray[]= array('parent=>Top','whichdim'=>$whichdim['whichdim'],'dim'=>$row[0],'den'=>$row[1],'num'=>$row[2], 'opps'=>number_format((float)$this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']), 2, '.', ''),'id'=>$whichdim['data']['id']);
                        //$newarray[$whichdim['whichdim'].'.'.$row[0]]= array($row[1],$row[2]);
                        //$newarray[]= array('whichdim'=>$whichdim['whichdim'],'dim'=>$row[0],'num'=>$row[2], 'opps'=>number_format((float)$this->calcOpps($row[2],$row[1],$whichdim['data']['totalsForAllResults']['ga:goal1Completions'],$whichdim['data']->totalsForAllResults['ga:sessions']), 2, '.', ''));
                    }

                }

            }
            
            return $newarray;
        }
        function sort_array($flat_array) { //needs fixing
            foreach ($data as $key => $row) {
                $volume[$key]  = $row['volume'];
                //$edition[$key] = $row['edition'];
            }

            // Sort the data with volume descending, edition ascending
            // Add $data as the last parameter, to sort by the common key
            array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $data);
        }

        
	function calculateTime ($seconds){
		$days = floor ($seconds / 86400);
		if ($days > 1) // 2 days+, we need days to be in plural
			return $days . ' days ' . gmdate ('H:i:s', $seconds);
		else if ($days > 0) // 1 day+, day in singular
			return $days . ' day ' . gmdate ('H:i:s', $seconds);
		return gmdate ('H:i:s', $seconds);
	}
        
        function calcOpps ($num,$den,$antinumsum,$antidensum) {
                $response = file_get_contents('https://57f1t5ple3.execute-api.us-east-1.amazonaws.com/prod?num='.$num.'&den='.$den.'&antidensum='.$antidensum.'&antinumsum='.$antinumsum);
                $response = json_decode($response);
                return($response);
        }
 

}       

