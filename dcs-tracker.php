<?php
/*
Plugin Name: DCS Tracker
Plugin URI: http://douglasconsulting.net/
Description: An "invisible landing page" that can be used to track where users come from via custom urls. 
Version: 1.0
Author: Jason Douglas
Author URI: http://douglasconsulting.net
License: GPL
*/

/**
 * Shortcode for our landing page. 
 */
function dcs_tracker_landing_page_shortcode($atts, $content=null)
{
	extract( shortcode_atts( array(
								'redirect_page' => 'Home',
								'tracking_id' => 'Tracking ID',
							), $atts ) );

	//Make sure we keep track of all the tracking ids
	$value = get_option( "dcs_tracker_tracking_ids" );
	$retval .= "Tracking IDs: {$value} <br />";
	if( $value == FALSE )
	{
		$value = $tracking_id;
		update_option( "dcs_tracker_tracking_ids", $value );
	}
	else
	{
		//Determine if the tracking id is already in the list.
		$ids = explode(";",$value);

		if( !in_array($tracking_id, $ids) )
		{
			$value += ";".$tracking_id;
			update_option( "dcs_tracker_tracking_ids", $value );
		}
	}

	$retval .= "Tracking IDs (updated): {$value} <br />";

	//Update the tracking value
	$value = get_option( "dcs_tracker_".$tracking_id );
	if( $value == FALSE )
	{
		$value = 0;
	}
	$value += 1;
	update_option( "dcs_tracker_".$tracking_id, $value );

	$retval .= "Tracking Numbers: {$value} <br />";

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
