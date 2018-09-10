<?php
/*
 * Template: Custom Google Analytics Site MetaData Template
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
		wp_redirect(get_bloginfo('url').'/google-analytics-login');
	}
}else{
	wp_redirect(get_bloginfo('url').'/google-analytics-login');
}
get_header();?>

<div id="content-wrap" class="container clr">

<?php

if(!empty($gaAccounts)){
	if(isset($_GET['account']) && $_GET['account'] != '' && isset($_GET['property']) && $_GET['property'] != '' ){
		$gAnalytics->getPropertyViews($_GET['account'], $_GET['property'], $_GET['days']);
	}
}
?>
</div><!-- .container -->
<?php 
get_footer();
?>