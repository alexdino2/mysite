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
echo '<a href="'.$gAuth->auth_url().'">Login</a> with Google to access Analytic data.';
get_footer();
