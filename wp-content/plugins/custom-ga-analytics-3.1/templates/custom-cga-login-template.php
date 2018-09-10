<?php
/*
 * Template:       Custom Google Analytics Login Template
*/
$gAuth = new Google_Auth();
$gAuth->check_access_code();
get_header();

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="'.$gAuth->auth_url().'">Login</a> with Google to enable Site Optimus to provide you with actionable insights into your Google Analytics data.</br></br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo 'Site Optimus does not store your data which is always kept at Google. You need to grant access to the profile so that we can automatically analyze the data for you. </br>'
. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Within minutes we will generate actionable insights for you.</p></br></br>';
get_footer();
