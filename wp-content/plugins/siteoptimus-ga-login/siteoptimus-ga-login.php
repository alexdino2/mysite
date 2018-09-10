echo 
"<form action='' method='post'> 
<input type='submit' name='use_button' value='something' /> 
</form>"; 

if(isset($_POST['use_button'])) 
{ 
    setApplicationName("SiteOptimus GA Login");
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
    $client->setAccessType('offline');   // Gets us our refreshtoken


    //For loging out.
    if ($_GET['logout'] == "1") {
	unset($_SESSION['token']);
       }
    

    // Step 2: The user accepted your access now you need to exchange it.
    if (isset($_GET['code'])) {
        
    	$client->authenticate($_GET['code']);  
    	$_SESSION['token'] = $client->getAccessToken();
    	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    // Step 1:  The user has not authenticated we give them a link to login    
    if (!$client->getAccessToken() && !isset($_SESSION['token'])) {

    	$authUrl = $client->createAuthUrl();

    	print "Connect Me!";
        }    
    

    // Step 3: We have access we can now create our service
    if (isset($_SESSION['token'])) {
        print "LogOut
";


        print "Access from google: " . $_SESSION['token']."
"; 
        
    	$client->setAccessToken($_SESSION['token']);
    	$service = new Google_Service_Analytics($client);    

        // request user accounts
        $accounts = $service->management_accountSummaries->listManagementAccountSummaries();


        foreach ($accounts->getItems() as $item) {

		echo "Account: ",$item['name'], "  " , $item['id'], "
 \n";
		
		foreach($item->getWebProperties() as $wp) {
			echo '-----WebProperty: ' ,$wp['name'], "  " , $wp['id'], "
 \n";    
			$views = $wp->getProfiles();
			if (!is_null($views)) {
                                // note sometimes a web property does not have a profile / view

				foreach($wp->getProfiles() as $view) {

					echo '----------View: ' ,$view['name'], "  " , $view['id'], "
 \n";    
				}  // closes profile
			}
		} // Closes web property
		
	} // closes account summaries
    }



} 

