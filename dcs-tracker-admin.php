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
				  array('jquery', 'jquery-alerts'), "0.5", true);
				  
    //Register nonce values we can use to verify our ajax calls from the editor.
    wp_localize_script( "dcs-tracker-admin-script", "dcs_tracker_admin_script_vars",
                        array(
								"ajaxurl" => admin_url('admin-ajax.php'),
                            )
                      );
}
add_action('admin_enqueue_scripts', 'dcs_tracker_load_admin_scripts');

/**
 * Add our admin menu to the dashboard.
 */
function dcs_tracker_admin_menu()
{
    add_options_page( 'DCS Tracker', 'DCS Tracker', 'administrator', 'dcs_tracker', 'dcs_tracker_admin_page');
}
add_action( 'admin_menu', 'dcs_tracker_admin_menu' );

/**
* Agents page.
*/
function dcs_tracker_admin_page()
{
	$status = NULL;
	
	if( isset($_SESSION['dcs-tracker-status']) ) $status = $_SESSION['dcs-tracker-status'];
	
	//error_log( "Status: ".print_r($_SESSION,true).PHP_EOL,3,dirname(__FILE__)."/tracker.log" );
	
	$retval = "";
	$active_tab = "reference-codes";
	if( isset($_GET['tab']) ) 
	{
    	$active_tab = $_GET['tab'];
	}
	
	$retval .= "<div class='wrap'>";
	
	$retval .= '<h2 class="nav-tab-wrapper">';
    $retval .= '<a href="?page=dcs-tracker-menu&tab=reference-codes" class="nav-tab">Reference Codes</a>';
	$retval .= '</h2>';
	
	if( $active_tab == "reference-codes" )
	{
		$ref_codes = get_option("dcs_tracker_discounts", array());

		$retval .= "<h1>Reference Codes</h1>";
		$retval .= "<hr class='dcs-tracker-line'>";
		
		if( $status == NULL )
		{
			$retval .= "<div class='updated dcs-tracker-message' style='display:none;'><p id='dcs-tracker-message'></p></div>"; 
		}
		else
		{
			$retval .= "<div class='updated dcs-tracker-message'><p id='dcs-tracker-message'>".$status."</p></div>"; 
		}
		$retval .= "<div class='error dcs-tracker-error-message' style='display:none;'><p id='dcs-tracker-error-message'></p></div>";  
		
		$retval .= "<div class='dcs-tracker-code'>";
		$retval .= "<table>";
		$retval .= "<tr><td><label for='dcs-tracker-code-name'>Reference Code</label></td><td><input name='dcs-tracker-code-name' id='dcs-tracker-code-name'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-value'>Discount Value ($)</label></td><td><input name='dcs-tracker-code-value' id='dcs-tracker-code-value'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-type'>Percentage</label></td><td style='text-align:right;'><input type='checkbox' name='dcs-tracker-code-type' id='dcs-tracker-code-type'></td></tr>";
		$retval .= "<tr><td></td><td style='text-align:right;'><input type='submit' id='dcs-tracker-create-code' value='Create Code'></td></tr>";
		$retval .= "</table>";
		$retval .= "</div>";
		
		if( $ref_codes == NULL )
		{
			$retval .= "<h2>No Reference Codes Defined.</h2>";		
		}
		else
		{
			$retval .= "<table class='dcs-tracker-ref-codes'>";
			$retval .= "<tr><th>Reference Code</th><th>Discount</th></tr>";
			foreach($ref_codes as $name => $values)
			{ 
				if( !is_array($values) ) 
				{
					$retval .= "<tr><td>".$name."</td><td>$".number_format($values, 2)."</td></tr>";
				}
				else
				{
					if( $values['type'] == "flat" )
					{
						$retval .= "<tr><td>".$name."</td><td>$".number_format($values['amount'], 2)."</td></tr>";
					}
					else
					{
						$retval .= "<tr><td>".$name."</td><td>".number_format($values['amount']*100, 0).'%'."</td></tr>";
					}
				}
			}
		}
		
		$retval .= "</table>";
	}	
	
	$retval .= "</div>";
		
	echo $retval;
}

/**
* Create Code.
*/
function dcs_tracker_create_code()
{
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

	if( array_key_exists($name,$discountArray) )
	{
		$status = "The discount amount for ".$name." has been updated.";
	}
	else 
	{
		$status = "The discount has been added to the database.";
	}
	$discountArray[$name] = array( "amount" => $amount, "type" => $type );
	
	error_log( "Name: ".$name." Amount: ".$amount." Type: ".$type.PHP_EOL,3,dirname(__FILE__)."/tracker.log" );

	update_option( "dcs_tracker_discounts", $discountArray );
	
	if( session_id() == '' ) session_start();
	$_SESSION['dcs-tracker-status'] = $status;
	session_write_close();

	echo wp_get_referer();
	
	die();
}
add_action( 'wp_ajax_dcs_tracker_create_code', 'dcs_tracker_create_code' );
add_action( 'wp_ajax_nopriv_dcs_tracker_create_code', 'dcs_tracker_create_code' );


