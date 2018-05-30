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
								'redirect_page' => '',
								'tracking_id' => '',
							), $atts ) );
							
	//Add the tracking id to the session
	if( $tracking_id !== '' )
	{
		if( !session_id() )
		{
			session_start();
		}
		$_SESSION["dcs_referral_code"] = $tracking_id;
	}
	
	//JGD: Add the tracking id to the end of the URL so it can be tracked via google analytics.
	//JGD: https://www.newmediacampaigns.com/blog/how-to-track-landing-page-redirects-using-google-analytics
	wp_redirect( site_url('/'.$redirect_page.'?'.$tracking_id) );
	exit();
}
add_shortcode( 'dcs_tracker_landing_page', 'dcs_tracker_landing_page_shortcode' );

/** 
 * Preprocess the content and find our shortcode so we can redirect.
 */
function dcs_pre_process_shortcode() 
{
	if (!is_singular()) return;
	global $post;
	if (!empty($post->post_content)) 
	{
		$regex = get_shortcode_regex();
		preg_match_all('/'.$regex.'/',$post->post_content,$matches);
		if (!empty($matches[2]) && in_array('dcs_tracker_landing_page',$matches[2])) 
		{
			preg_match_all("/([^,= ]+)=([^,= ]+)/", $matches[3][0], $r); 
			$result = array_combine($r[1], str_replace("\"", "",$r[2]));
			dcs_tracker_landing_page_shortcode( $result );
		} 
	}
}
add_action('template_redirect','dcs_pre_process_shortcode', 100);

/**
 * Google Analytics tracker.
 */
function dcs_tracker_google_tracking_code()
{
	//Don't track admin
	if( is_admin() ) return;

	$google_analytics_flag = get_option( "dcs_tracker_google_analytics_flag" );
	$google_analytics_id = get_option( "dcs_tracker_google_analytics_id" );
	
	if( ($google_analytics_flag == "true") && ($google_analytics_id != "") )
	{
        echo "<script type='text/javascript'>

            var _gaq = _gaq || [];
            _gaq.push(
                ['_setAccount', '" . esc_js( $google_analytics_id ) . "'],
                ['_trackPageview'],
                ['_gat._anonymizeIp']
            );

            (function() {
                var ga = document.createElement('script'); 
                ga.type = 'text/javascript'; 
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); 
            })();

        </script>";
	}
}
add_action( 'wp_footer', 'dcs_tracker_google_tracking_code' );
