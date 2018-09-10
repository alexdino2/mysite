<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once realpath(CGA_PLUGIN_BASEPATH . '/tools/src/Google/autoload.php');
/*$client_id = '503203966289-a9i272p88upaodnspruelks6kr1s41dt.apps.googleusercontent.com';
$client_secret = 'AgEpYPu2PPGUBSlO_rG2tMhh';
$redirect_uri = 'http://demo.getyoursolutions.com/custom_ga/ga-login/';
$homeUrl = 'http://demo.getyoursolutions.com/custom_ga/ga-login/';*/
$cgaDashSetting = get_option('cga_dashSetting');
if($cgaDashSetting != '')
	$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
$loginURL = get_permalink($cgaDashSetting['setting']['loginPage']);
$gClient = new Google_Client();
$gClient->setClientId($cgaDashSetting['setting']['clientID']);
$gClient->setClientSecret($cgaDashSetting['setting']['clientSecret']);
$gClient->setRedirectUri($loginURL);

$gClient->setScopes(array(
	'https://www.googleapis.com/auth/userinfo.email',
	'https://www.googleapis.com/auth/userinfo.profile',
	'https://www.googleapis.com/auth/analytics',
	'https://www.googleapis.com/auth/analytics.provision'
));

