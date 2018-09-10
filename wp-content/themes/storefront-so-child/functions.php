<?php
// Load Font Awesome
add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome' );
function enqueue_font_awesome() {

	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );

}

//Load highcharts
add_action('wp_enqueue_scripts', 'mytheme_custom_scripts');

function mytheme_custom_scripts(){
    
    // Register and Enqueue a Script
    // get_stylesheet_directory_uri will look up child theme location
    wp_enqueue_script( 'ajax', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' );
    wp_enqueue_script( 'highcharts', 'https://code.highcharts.com/highcharts.js' );
    wp_enqueue_script( 'highcharts-more', 'https://code.highcharts.com/highcharts-more.js' );
    wp_enqueue_script( 'highcharts-export', 'https://code.highcharts.com/modules/exporting.js' );



    
}

/*

function js_custom(){

    if ( is_page('test-boxplot')){  
	wp_register_script( 'js_custom', get_stylesheet_directory_uri() . '/js/load_highcharts_boxplot.js', false, null);
	wp_enqueue_script( 'js_custom');
    }
}	

add_action('wp_enqueue_scripts', 'js_custom');

*/