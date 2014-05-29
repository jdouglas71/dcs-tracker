<?php
/*
Plugin Name: DCS Tracker
Plugin URI: http://douglasconsulting.net/
Description: An "invisible landing page" that can be used to track where users come from via custom urls. Adding the ability to 
Version: 1.1
Author: Jason Douglas
Author URI: http://douglasconsulting.net
License: GPL
*/

/**
 * Load custom scripts and styles for the admin page.
 */
function dcs_tracker_load_admin_scripts()
{
	wp_register_style( 'dcs_tracker_admin_style', plugin_dir_url(__FILE__).'admin-style.css' );
	wp_enqueue_style( 'dcs_tracker_admin_style' );

	wp_register_script( 'dcs_tracker_script', plugin_dir_url(__FILE__).'dcs-tracker.js', array('jquery') );
	wp_enqueue_script( 'dcs_tracker_script' );
}
add_action( 'admin_enqueue_scripts', 'dcs_tracker_load_admin_scripts' );

/**
 * Shortcode for our landing page. 
 */
function dcs_tracker_landing_page_shortcode($atts, $content=null)
{
	extract( shortcode_atts( array(
								'redirect_page' => 'Home',
								'tracking_id' => 'Tracking ID',
							), $atts ) );

	//$retval = "";
	$today = new DateTime('NOW');
	//Make sure we keep track of all the tracking ids
	$value = get_option( "dcs_tracker_tracking_ids" );
	//$retval .= "Tracking IDs: {$value} <br />";
	if( $value == FALSE )
	{
		//NO IDs YET!
		$value = $tracking_id;
		update_option( "dcs_tracker_tracking_ids", $value );
		update_option( "dcs_tracker_".$value."_lcd", $today->format('l, M d Y') ); 
	}
	else
	{
		//Determine if the tracking id is already in the list.
		$ids = explode(";",$value);

		//Add it if it's not there.
		if( !in_array($tracking_id, $ids) )
		{
			$value .= ";".$tracking_id;
			//$retval .= "Tracking IDs (update 1): {$value} <br />";
			update_option( "dcs_tracker_tracking_ids", $value );
			update_option( "dcs_tracker_".$value."_lcd", $today->format('l, M d Y') ); 
		}
	}

	//$retval .= "Tracking IDs (updated): {$value} <br />";

	//Update the tracking value
	$value = get_option( "dcs_tracker_".$tracking_id );
	if( $value == FALSE )
	{
		$value = 0;
	}
	$value += 1;
	update_option( "dcs_tracker_".$tracking_id, $value );

	//$retval .= "Tracking Numbers: {$value} <br />";

	//return $retval;

	header( "Location: " . site_url('/'.$redirect_page.'/') );
}
add_shortcode( 'dcs_tracker_landing_page', 'dcs_tracker_landing_page_shortcode' );

/**
 * Add our admin menu to the dashboard.
 */
function dcs_tracker_admin_menu()
{
    add_options_page( 'DCS Tracker', 'DCS Tracker', 'administrator', 'dcs_tracker', 'dcs_tracker_admin_page');
}
add_action( 'admin_menu', 'dcs_tracker_admin_menu' );

/**
 * Show the admin page.
 */ 
function dcs_tracker_admin_page()
{
    include( 'dcs-tracker-admin.php' );
}

/**
 * Google Analytics tracker.
 */
function dcs_tracker_google_tracking_code()
{
	//Don't track admin
	if( is_admin() ) return;

	$google_analytics_flag = get_option( "dcs_tracker_google_analytics_flag" );
	$google_analytics_id = get_option( "dcs_tracker_google_analytics_id" );

	if( ($google_analytics_flag == "1") && ($google_analytics_id != "") )
	{
        echo "<script type='text/javascript'>

            var _gaq = _gaq || [];
            _gaq.push(
                ['_setAccount', '" . esc_js( $google_analytics_id ) . "'],
                ['_trackPageview']
            );

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); 
            })();

        </script>";
	}
}
add_action( 'wp_footer', 'dcs_tracker_google_tracking_code' );
