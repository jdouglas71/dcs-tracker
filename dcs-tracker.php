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

    //Register nonce and variables for ajax calls in the page picker.
    wp_localize_script( "dcs_tracker_script", "dcs_tracker_script_vars",
                        array(
								"dcs_tracker_delete_discounts_nonce"=>wp_create_nonce("dcs_tracker_delete_discounts"),
                                "dcs_tracker_add_discount_nonce"=>wp_create_nonce("dcs_tracker_add_discount"),
                                "ajaxurl" => admin_url('admin-ajax.php')
                            )
                      );

}
add_action( 'admin_enqueue_scripts', 'dcs_tracker_load_admin_scripts' );

/**
 * Add Discount
 */
function dcs_tracker_add_discount()
{
	check_ajax_referer( "dcs_tracker_add_discount", "dcs_tracker_add_discount_nonce" );

	$discountArray = get_option("dcs_tracker_discounts", array());

	//Do stuff here
	$name = $_POST['name'];
	$amount = $_POST['amount'];
	$type = $_POST['type'];

	if( $type == "percentage" )
	{
		$amount /= 100;
	}

	if( array_key_exists($name,$discountArray) )
	{
		$retval = "The discount amount for ".$name." has been updated.";
	}
	else 
	{
		$retval = "The discount has been added to the database.";
	}
	$discountArray[$name] = array( "amount" => $amount, "type" => $type );

	update_option( "dcs_tracker_discounts", $discountArray );

	$referralURL = site_url(get_option("dcs_tracker_referral_page","/product/ripcord/"))."?referralCode=" . urlencode($name);

	$retval .= PHP_EOL."The referral URL: " . $referralURL;

	echo $retval;

	die();
}
add_action('wp_ajax_dcs_tracker_add_discount', 'dcs_tracker_add_discount' );

/**
 * Delete Discounts
 */
function dcs_tracker_delete_discounts()
{
	check_ajax_referer( "dcs_tracker_delete_discounts", "dcs_tracker_delete_discounts_nonce" );

	$names = $_POST['names'];

	$discountArray = get_option("dcs_tracker_discounts", array());

	foreach( $names as $name )
	{
		unset( $discountArray[$name] );
	}

	update_option( "dcs_tracker_discounts", $discountArray );

	die();
}
add_action('wp_ajax_dcs_tracker_delete_discounts', 'dcs_tracker_delete_discounts' );


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

	//Add the tracking id to the session
	if( !session_id() )
	{
		session_start();
	}
	$_SESSION["dcs_referral_code"] = $tracking_id;
	error_log( "Added tracking id to session: " . $_SESSION['dcs_referral_code'], 3, plugin_dir_path(__FILE__)."/session.log" );

	wp_redirect( site_url('/'.$redirect_page.'/') );
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
		if (!empty($matches[2]) && in_array('dcs_tracker_landing_page',$matches[2]) && is_user_logged_in()) 
		{
			preg_match_all("/([^,= ]+)=([^,= ]+)/", $matches[3][0], $r); 
			$result = array_combine($r[1], str_replace("\"", "",$r[2]));
			dcs_tracker_landing_page_shortcode( $result );
			//wp_redirect( site_url('/'.$result['redirect_page'].'/') );
		} 
	}
}
add_action('template_redirect','dcs_pre_process_shortcode',1);

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
