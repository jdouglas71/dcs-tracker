<?php
/*
Plugin Name: DCS Tracker
Plugin URI: http://douglasconsulting.net/
Description: Creates a tracking code that can be also be used as coupons. 
Version: 2.0
Author: Jason Douglas
Author URI: http://douglasconsulting.net
License: GPL
*/

require_once(dirname(__FILE__)."/dcs-tracker-admin.php");

/**
 * Shortcode for our landing page. 
 */
function dcs_tracker_landing_page_shortcode($atts, $content=null)
{
	extract( shortcode_atts( array(
								'redirect_page' => 'Home',
								'tracking_id' => 'Tracking ID',
							), $atts ) );

	header( "Location: " . site_url('/'.$redirect_page.'/') );
}
add_shortcode( 'dcs_tracker_landing_page', 'dcs_tracker_landing_page_shortcode' );

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
