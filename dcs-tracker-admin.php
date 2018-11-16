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
    $retval .= '<a href="?page=dcs_tracker&tab=reference-codes" class="nav-tab '.(($active_tab=='reference-codes')?'nav-tab-active':'').'">Reference Codes</a>';
    $retval .= '<a href="?page=dcs_tracker&tab=agent-portal" class="nav-tab '.(($active_tab=='agent-portal')?'nav-tab-active':'').'">Agent Portals</a>';
    $retval .= '<a href="?page=dcs_tracker&tab=google-opts" class="nav-tab '.(($active_tab=='google-opts')?'nav-tab-active':'').'">Google Options</a>';
	$retval .= '</h2>';
	
	if( $active_tab == "reference-codes" )
	{
		if( isset($_GET['created']) )
		{
			$status = "The Reference Code has been created.";
		}
	
		if( isset($_GET['updated']) )
		{
			$status = "The Reference Code has been updated.";
		}

		$ref_codes = get_option("dcs_tracker_discounts", array());
		ksort($ref_codes);

		$retval .= "<h1>Reference Codes/MegaPortals</h1>";
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
		$retval .= "<tr><td><label for='dcs-tracker-code-name'>Reference Code</label></td><td><input name='dcs-tracker-code-name' id='dcs-tracker-code-name'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-allow-international'>Allow International TI</label></td><td><input type='checkbox' name='dcs-tracker-allow-international' id='dcs-tracker-allow-international'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-value'>Discount Value ($)</label></td><td><input name='dcs-tracker-code-value' id='dcs-tracker-code-value'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-type'>Type</label></td><td><select name='dcs-tracker-code-type' id='dcs-tracker-code-type'><option value='flat'>Discount</option><option value='percentage'>Percentage</option><option value='flat_rate'>Flat Rate</option></select></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-code-create-page'>Create Page</label></td><td style='text-align:right;'><input type='checkbox' name='dcs-tracker-code-create-page' id='dcs-tracker-code-create-page'></td></tr>";
		$retval .= "<tr id='dcs-tracker-code-redirect-page-parent' style='display:none;'><td><label for='dcs-tracker-code-redirect-parent'>Parent Redirect Page (portal)</label></td><td><input type='checkbox' name='dcs-tracker-code-redirect-parent' id='dcs-tracker-code-redirect-parent'></td></tr>";		
		$retval .= "<tr id='dcs-tracker-code-redirect-page' style='display:none;'><td><label for='dcs-tracker-code-redirect'>Redirect Page</label></td><td><input name='dcs-tracker-code-redirect' id='dcs-tracker-code-redirect'></td></tr>";		
		$retval .= "<tr><td></td><td style='text-align:right;'><input type='submit' id='dcs-tracker-create-code' value='Create Code'></td></tr>";
		$retval .= "</table>";
		$retval .= "</div>";
		
		$retval .= "<div class='dcs-tracker-ref-codes'>";
		if( $ref_codes == NULL )
		{
			$retval .= "<h2>No Reference Codes Defined.</h2>";		
		}
		else
		{
			$retval .= "<table class='dcs-tracker-ref-codes'>";
			$retval .= "<tr><th>Delete</th><th>Reference Code</th><th>Allow International TI</th><th>Discount Type</th><th>Discount</th><th>Has Page?</th><th>Redirect Page</th><th>Landing Page URL</th></tr>";
			foreach($ref_codes as $name => $values)
			{ 
				$retval .= "<tr>";
				$retval .= "<td><input type='checkbox' class='dcs-tracker-code-delete' value='".$name."'></td>";

				$retval .= "<td>".$name."</td>";
				
				$allow_international = "false";
				if( isset($values['allow_international']) )
				{
					$allow_international = $values['allow_international'];
				}
				$retval .= "<td>".$allow_international."</td>";
								
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
					if( $values['type'] != "percentage" )
					{
						$retval .= "<td>$".number_format($values['amount'], 2)."</td>";
					}
					else
					{
						$retval .= "<td>".number_format($values['amount']*100, 0).'%'."</td>";
					}
				}
				else
				{
					$retval .= "<td></td>";
				}
				$has_page = "false";
				if( isset($values['has_page']) ) $has_page = $values['has_page'];
				$retval .= "<td>".$has_page."</td>";
				
				$redirect = "";
				if( isset($values['redirect']) ) $redirect = $values['redirect'];
				if( $redirect == '' ) $redirect = "Home";
				$retval .= "<td>".$redirect."</td>";
				
				$use_portal_parent = "false";
				if( isset($values['use_portal_parent']) ) $use_portal_parent = $values['use_portal_parent'];
				$parent_page = "/";
				if( $use_portal_parent == "true" ) $parent_page = "/portal/";
				
				if( $has_page == "true" )
					$retval .= "<td><a href='".site_url($parent_page.$name)."'>".site_url($parent_page.$name)."</a></td>";
				else
					$retval .= "<td></td>";
				$retval .= "</tr>";
			}
			$retval .= "<tr><td colspan=6 style='padding-top:100px;text-align:right;'><input type='submit' id='dcs-tracker-update-code' value='Update'></td></tr>";

			$retval .= "</table>";
			$retval .= "</div>";
		}
	}	
	else if( $active_tab == "agent-portal" )
	{
		if( isset($_GET['created']) )
		{
			$status = "The Agent Portal has been created.";
		}
	
		if( isset($_GET['updated']) )
		{
			$status = "The Agent Portal has been updated.";
		}
		
		if( isset($_GET['error']) )
		{
			$status = $_GET['error_message'];
		}

		$agent_portals = get_option("dcs_agent_portals", array());
		ksort($agent_portals);
		$retval .= "<h1>Agent Portals</h1>";
		$retval .= "<hr class='dcs-tracker-line'>";
		
		$retval .= "<div class='dcs-tracker-code'>";
		$retval .= "<table>";
		$retval .= "<tr><td><label for='dcs-tracker-agent-name'>Name</label></td><td><input name='dcs-tracker-agent-name' id='dcs-tracker-agent-name'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-agent-filter'>Agent Filter</label></td><td><input name='dcs-tracker-agent-filter' id='dcs-tracker-agent-filter'></td></tr>";
		$retval .= "<tr><td><label for='dcs-tracker-allow-international'>Allow International TI</label></td><td><input type='checkbox' name='dcs-tracker-allow-international' id='dcs-tracker-allow-international'></td></tr>";
		$retval .= "<tr><td></td><td style='text-align:right;'><input type='submit' id='dcs-tracker-create-agent-portal' value='Create Agent Portal'></td></tr>";
		$retval .= "</table>";
		$retval .= "</div>";

		$retval .= "<div class='dcs-tracker-ref-codes'>";
		if( $agent_portals == NULL )
		{
			$retval .= "<h2>No Agent Portals Defined.</h2>";		
		}
		else
		{
			$retval .= "<table class='dcs-tracker-ref-codes'>";
			$retval .= "<tr><th>Delete</th><th>Name</th><th>Agent Filter</th><th>Allow International TI</th><th>Portal URL</th></tr>";
			foreach($agent_portals as $name => $values)
			{ 
				$retval .= "<tr>";
				$retval .= "<td><input type='checkbox' class='dcs-tracker-agent-delete' value='".$name."'></td>";
				$retval .= "<td>".$name."</td>";
				
				$retval .= "<td>".$values['agent_filter']."</td>";

				$allow_international = "false";
				if( isset($values['allow_international']) )
				{
					$allow_international = $values['allow_international'];
				}
				$retval .= "<td>".$allow_international."</td>";

				
				$retval .= "<td><a href='".site_url("/portal/".$name)."'>".site_url("/portal/".$name)."</a></td>";

				$retval .= "</tr>";
			}
			$retval .= "<tr><td colspan=4 style='text-align:right;padding-top:100px;'><input type='submit' id='dcs-tracker-update-agent-portal' value='Update'></td></tr>";
			
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
	$allow_international = $_POST['allow_international'];
	$amount = $_POST['amount'];
	$type = $_POST['type'];
	$redirect = $_POST['redirect'];
	$has_page = $_POST['has_page'];
	$use_portal_parent = $_POST['use_portal_parent'];
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
		
	if( $has_page == "true" )
	{
		//Create portal parent page if necessary
		$portal_page = get_page_by_title( 'portal' );
		$portal_page_id = 0;
		if( $use_portal_parent == "true" )
		{
			if( $portal_page == NULL )
			{
				$portal_post = array( 
					'post_title' => "portal",
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type' => 'page',
				);
				$portal_page_id = wp_insert_post( $portal_post );
			}
			else
			{
				$portal_page_id = $portal_page->ID;
			}
		}
	
		//Does page with this title already exist?
		$page = get_page_by_title( $name );
		
		$my_post = array(
			'post_title'    => wp_strip_all_tags( $name ),
			'post_content'  => '[dcs_tracker_landing_page tracking_id="'.$name.'" redirect_page="'.$redirect.'"]',
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'post_type'     => 'page',
			'post_parent'   => $portal_page_id,
			'page_template' => "page-templates/page_fullwidth.php",
		);
		
		// Insert the post into the database
		$page_id = wp_insert_post( $my_post );
		if( !is_wp_error($page_id) )
		{
			$status = "&created=1";
			$discountArray[$name]["ID"] = $page_id;
		}
		else
		{
			$status = "&error=1?error_message=".$page_id->get_error_message();
		}	
	}
	
	update_option( "dcs_tracker_discounts", $discountArray );
	
	echo wp_get_referer().$status;
	//echo "https://espn.com";
	die();
}
add_action( 'wp_ajax_dcs_tracker_create_code', 'dcs_tracker_create_code' );
add_action( 'wp_ajax_nopriv_dcs_tracker_create_code', 'dcs_tracker_create_code' );

/**
* Create Agent Portal.
*/
function dcs_tracker_create_agent_portal()
{
	check_ajax_referer( "dcs_tracker_create_agent_portal", "dcs_tracker_create_agent_portal_nonce" );

	$agent_portals = get_option("dcs_agent_portals", array());

	//Do stuff here
	$name = strtolower($_POST['name']);
	$agent_filter = strtolower($_POST['agent_filter']);
	$allow_international = $_POST['allow_international'];
	$status = "";

	$agent_portals[$name] = array( "agent_filter" => $agent_filter, 
								   "allow_international" => $allow_international, 	
								 );
								 
	update_option( "dcs_agent_portals", $agent_portals );
	
	//Create portal parent page if necessary
	$portal_page = get_page_by_title( 'portal' );
	$portal_page_id = 0;
	if( $portal_page == NULL )
	{
		$portal_post = array( 
			'post_title' => "portal",
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_type' => 'page',
		);
		$portal_page_id = wp_insert_post( $portal_post );
	}
	else
	{
		$portal_page_id = $portal_page->ID;
	}
	
	//Does page with this title already exist?
	$page = get_page_by_title( $name );
	
	$my_post = array(
		'post_title'    => wp_strip_all_tags( $name ),
		'post_content'  => '[ripcord_quote_machine agent_filter="'.$agent_filter.'" allow_international_ti="'.$allow_international.'"]',
		'post_status'   => 'publish',
		'post_author'   => get_current_user_id(),
		'post_type'     => 'page',
		'post_parent'   => $portal_page_id,
		'page_template' => "page-templates/page_fullwidth.php",
	);
	
	// Insert the post into the database
	$post_id = wp_insert_post( $my_post );
	$status = "&created=1";
	$agent_portals[$name]['ID'] = $post_id;
	
	echo wp_get_referer().$status;
	
	die();
}
add_action( 'wp_ajax_dcs_tracker_create_agent_portal', 'dcs_tracker_create_agent_portal' );
add_action( 'wp_ajax_nopriv_dcs_tracker_create_agent_portal', 'dcs_tracker_create_agent_portal' );

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
* Update Agents 
*/
function dcs_tracker_update_agents()
{
	check_ajax_referer( "dcs_tracker_update_agents", "dcs_tracker_update_agents_nonce" );
	$agent_portals = get_option("dcs_agent_portals", array());

	$values = $_POST['values'];
	$vals = explode( ";", $values );
	foreach( $vals as $name )
	{
		unset($agent_portals[$name]);
	}
	
	update_option( "dcs_agent_portals", $agent_portals );

	$status = "&updated=1";

	echo wp_get_referer().$status;
	die();
}
add_action( 'wp_ajax_dcs_tracker_update_agents', 'dcs_tracker_update_agents' );
add_action( 'wp_ajax_nopriv_dcs_tracker_update_agents', 'dcs_tracker_update_agents' );

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


