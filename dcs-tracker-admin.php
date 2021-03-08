<?php
/**
* Admin Page for the DCS Tracker Plugin.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* Scripts and style sheets for the admin page.	
*/
function dcs_tracker_load_admin_scripts()
{
	wp_register_style( 'dcs-tracker-style', plugins_url('dcs-tracker.css', __FILE__), array() );
	wp_register_style( 'jquery-alerts-style', (WP_PLUGIN_URL.'/dcs-tracker/js/jquery.alerts.css'), array(), "2.11" );
	wp_register_style( 'jquery-qtip-style', (WP_PLUGIN_URL.'/dcs-tracker/js/jquery.qtip.css'), array(), "2.0" );

	wp_enqueue_style( 'dcs-tracker-style' );
	wp_enqueue_style( 'jquery-alerts-style' );
	wp_enqueue_style( 'jquery-qtip-style' );
		
	wp_enqueue_script('jquery-qtip', (WP_PLUGIN_URL.'/dcs-tracker/js/jquery.qtip.js'), array('jquery'), false, true);
	wp_enqueue_script('jquery-alerts', (WP_PLUGIN_URL.'/dcs-tracker/js/jquery.alerts.js'), array('jquery'), "1.11", true);
	wp_enqueue_script('dcs-tracker-admin-script', (WP_PLUGIN_URL.'/dcs-tracker/dcs-tracker-admin.js'), 
				  array('jquery', 'jquery-alerts'), "0.86", true);
				  
    //Register nonce values we can use to verify our ajax calls from the editor.
    wp_localize_script( "dcs-tracker-admin-script", "dcs_tracker_admin_script_vars",
                        array(
								"ajaxurl" => admin_url('admin-ajax.php'),
								"dcs_tracker_create_code_nonce"=>wp_create_nonce("dcs_tracker_create_code"),
								"dcs_tracker_create_agent_portal_nonce"=>wp_create_nonce("dcs_tracker_create_agent_portal"),
								"dcs_tracker_update_agents_nonce"=>wp_create_nonce("dcs_tracker_update_agents"),
								"dcs_tracker_update_codes_nonce"=>wp_create_nonce("dcs_tracker_update_codes"),
								"dcs_tracker_update_google_options_nonce"=>wp_create_nonce("dcs_tracker_update_google_options"),
                            )
                      );
}
add_action('admin_enqueue_scripts', 'dcs_tracker_load_admin_scripts');

/**
 * Add our admin menu to the dashboard.
 */
function dcs_tracker_admin_menu()
{
    add_menu_page( 'DCS Tracker', 'DCS Tracker', 'administrator', 'dcs_tracker', 'dcs_tracker_admin_page');
}
add_action( 'admin_menu', 'dcs_tracker_admin_menu' );

/**
* Agents page.
*/
function dcs_tracker_admin_page()
{
	$status = NULL;

	$retval = "";
	$active_tab = "reference-codes";
	if( isset($_GET['tab']) ) 
	{
    	$active_tab = $_GET['tab'];
	}
	
	$retval .= "<div class='wrap'>";
	
	$retval .= '<h2 class="nav-tab-wrapper">';
    $retval .= '<a href="?page=dcs_tracker&tab=reference-codes" class="nav-tab '.(($active_tab=='reference-codes')?'nav-tab-active':'').'">Offer Codes</a>';
    //$retval .= '<a href="?page=dcs_tracker&tab=google-opts" class="nav-tab '.(($active_tab=='google-opts')?'nav-tab-active':'').'">Google Options</a>';
	$retval .= '</h2>';
	
	if( $active_tab == "reference-codes" )
	{
		if( isset($_GET['created']) )
		{
			$status = "The Offer Code has been created.";
		}
	
		if( isset($_GET['updated']) )
		{
			$status = "The Offer Code has been updated.";
		}

		$ref_codes = get_option("dcs_tracker_discounts", array());
		ksort($ref_codes);

		$retval .= "<h1>Offer Codes</h1>";
		$retval .= "<hr class='dcs-tracker-line'>";
		
		if( $status == NULL )
		{
			$retval .= "<div class='updated dcs-tracker-message' style='display:none;'><p id='dcs-tracker-message'>STATUS</p></div>"; 
		}
		else
		{
			$retval .= "<div class='updated dcs-tracker-message'><p id='dcs-tracker-message'>".$status."</p></div>"; 
		}
		$retval .= "<div class='error dcs-tracker-error-message' style='display:none;'><p id='dcs-tracker-error-message'></p></div>";  
		
		$retval .= "<div class='dcs-tracker-code'>";
		$retval .= "<table>";
		$retval .= "<tr><td><label for='dcs-tracker-code-name'>Offer Code</label></td><td><input name='dcs-tracker-code-name' id='dcs-tracker-code-name'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-value'>Value ($)</label></td><td><input name='dcs-tracker-code-value' id='dcs-tracker-code-value'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-type'>Type</label></td><td><select name='dcs-tracker-code-type' id='dcs-tracker-code-type'><option value='percentage'>Percentage</option><option value='flat_rate'>Flat Rate</option><option value='upcharge'>Upcharge</option></select></td></tr>";
		$retval .= "<tr><td></td><td style='text-align:right;'><input type='submit' id='dcs-tracker-create-code' value='Create Code'></td></tr>";
		$retval .= "</table>";
		$retval .= "</div>";
		
		$retval .= "<div class='dcs-tracker-ref-codes'>";
		if( $ref_codes == NULL )
		{
			$retval .= "<h2>No Offer Codes Defined.</h2>";		
		}
		else
		{
			$retval .= "<table class='dcs-tracker-ref-codes'>";
			$retval .= "<tr><th>Delete</th><th>Offer Code</th><th>Discount Type</th><th>Discount</th></tr>";
			foreach($ref_codes as $name => $values)
			{ 
				$retval .= "<tr>";
				$retval .= "<td><input type='checkbox' class='dcs-tracker-code-delete' value='".$name."'></td>";

				$retval .= "<td>".$name."</td>";
				
				if( $values['amount'] != '' )
				{
					$retval .= "<td>".$values['type']."</td>";
				}
				else
				{
					$retval .= "<td>N/A</td>";
				}
				
				if( is_numeric($values['amount']) )
				{
					if( $values['type'] == "percentage" )
					{
						$retval .= "<td>".number_format($values['amount']*100, 0).'%'."</td>";
					}
					else if( $values['type'] == "upcharge" )
					{
						$retval .= "<td  style='color:green;'>+$".number_format($values['amount'], 2)."</td>";
					}
					else
					{
						$retval .= "<td style='color:red;'>-$".number_format($values['amount'], 2)."</td>";
					}
				}
				else
				{
					$retval .= "<td></td>";
				}
			}
			$retval .= "<tr><td colspan=6 style='padding-top:100px;text-align:right;'><input type='submit' id='dcs-tracker-update-code' value='Update'></td></tr>";

			$retval .= "</table>";
			$retval .= "</div>";
		}
	}	
	else if( $active_tab == "google-opts" )
	{
		$google_analytics_flag = get_option( "dcs_tracker_google_analytics_flag" );
		$google_analytics_id = get_option( "dcs_tracker_google_analytics_id" );

		$retval .= "<h1>Google Options</h1>";
		$retval .= "<hr class='dcs-tracker-line'>";
		$retval .= "<div class='dcs-tracker-code'>";
		
		$retval .= "<table>";
		$retval .= "<tr><td><label for='dcs-tracker-google-analytics-flag'>Embed Google Analytics</label></td><td><input type='checkbox' name='dcs-tracker-google-analytics-flag' id='dcs-tracker-google-analytics-flag' ".($google_analytics_flag=="true"?"checked":"")."></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-google-analytics-code'>Google Analytics Code</label></td><td><input name='dcs-tracker-google-analytics-code' id='dcs-tracker-google-analytics-code' value='".$google_analytics_id."'></td></tr>";
		$retval .= "<tr><td></td><td style='text-align:right;'><input type='submit' id='dcs-tracker-update-google-options' value='Update Google Options'></td></tr>";
		$retval .= "</table>";
		$retval .= "</div>";
	}
	
	$retval .= "</div>";
		
	echo $retval;
}

/**
* Create Code.
*/
function dcs_tracker_create_code()
{
	check_ajax_referer( "dcs_tracker_create_code", "dcs_tracker_create_code_nonce" );

	$discountArray = get_option("dcs_tracker_discounts", array());

	//Do stuff here
	$name = strtolower($_POST['name']);
	$amount = $_POST['amount'];
	$type = $_POST['type'];
	$status = "";

	if( $type == "percentage" )
	{
		$amount /= 100;
	}

	$discountArray[$name] = array( "amount" => $amount, 
								   "type" => $type, 
								   "redirect" => $redirect, 
								   "has_page" => $has_page,
								   "allow_international" => $allow_international,
								   "use_portal_parent" => $use_portal_parent );

	update_option( "dcs_tracker_discounts", $discountArray );

	echo wp_get_referer().$status;
	//echo "https://espn.com";
	die();
}
add_action( 'wp_ajax_dcs_tracker_create_code', 'dcs_tracker_create_code' );
add_action( 'wp_ajax_nopriv_dcs_tracker_create_code', 'dcs_tracker_create_code' );

/**
* Update Codes 
*/
function dcs_tracker_update_codes()
{
	check_ajax_referer( "dcs_tracker_update_codes", "dcs_tracker_update_codes_nonce" );
	$discountArray = get_option("dcs_tracker_discounts", array());

	$values = $_POST['values'];
	$vals = explode( ";", $values );
	foreach( $vals as $name )
	{
		unset($discountArray[$name]);
	}
	
	update_option( "dcs_tracker_discounts", $discountArray );
	
	$status = "&updated=1";

	echo wp_get_referer().$status;
	die();
}
add_action( 'wp_ajax_dcs_tracker_update_codes', 'dcs_tracker_update_codes' );
add_action( 'wp_ajax_nopriv_dcs_tracker_update_codes', 'dcs_tracker_update_codes' );

/**
* Update Google Options 
*/
function dcs_tracker_update_google_options()
{
	check_ajax_referer( "dcs_tracker_update_google_options", "dcs_tracker_update_google_options_nonce" );

	$flag = $_POST['flag'];
	$code = $_POST['code'];
			
	update_option( "dcs_tracker_google_analytics_flag", $flag );
	update_option( "dcs_tracker_google_analytics_id", $code );

	$status = "&updated=1";

	echo wp_get_referer().$status;
	die();
}
add_action( 'wp_ajax_dcs_tracker_update_google_options', 'dcs_tracker_update_google_options' );
add_action( 'wp_ajax_nopriv_dcs_tracker_update_google_options', 'dcs_tracker_update_google_options' );


