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
	$retval = "";


	return $retval;
}
add_shortcode( 'dcs_tracker_landing_page', 'dcs_tracker_landing_page_shortcode' );


