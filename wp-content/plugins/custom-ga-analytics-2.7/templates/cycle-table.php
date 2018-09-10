<?php
/*
 * Template: Custom Google Analytics Goal Cycle Data Template
*/


//include('./wp-blog-header.php');


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
//get_header();

$mem = new Memcached();
$mem->addServer("soredis.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

$result=$mem->get('dimdennum');
//$mem->json_decode($mem);

if($result){
    echo $result;
}
else {
    echo "error";
}


