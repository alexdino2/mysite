<?php
    require 'gapi.class.php';
    require_once dirname(__FILE__) . '/tools/src/Google/autoload.php';
    //require 'src/Google_Client.php';
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

?>