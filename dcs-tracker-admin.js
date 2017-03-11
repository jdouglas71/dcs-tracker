jQuery(document).ready(function() {

	/** Create code click */
	jQuery('#dcs-tracker-create-code').click( function() {
		var name = jQuery('#dcs-tracker-code-name').val();
		var value = jQuery('#dcs-tracker-code-value').val();
		var type = "flat";
		var redirect = "";
		var has_page = "false";
		if( jQuery('#dcs-tracker-code-type').is(":checked") )
			type = "percentage"; 
			
		if( jQuery('#dcs-tracker-code-create-page').is(":checked") )
		{
			redirect = jQuery('#dcs-tracker-code-redirect').val();
			has_page = "true";
		}			
			
		jQuery("div.dcs-tracker-error-message").hide();
			
		if( name == '' )
		{
			jQuery("p#dcs-tracker-error-message").text( "The discount name cannot be blank." );
			jQuery("div.dcs-tracker-error-message").show();
			return;
		}
		
		var data = {
			name : name,
			amount : value,
			type : type,
			redirect : redirect,
			has_page : has_page,
			action: 'dcs_tracker_create_code',
			dcs_tracker_create_code_nonce: dcs_tracker_admin_script_vars.dcs_tracker_create_code_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
	
	/** Create Agent Portal click */
	jQuery('#dcs-tracker-create-agent-portal').click( function() {
		var name = jQuery('#dcs-tracker-agent-name').val();
		var agent_filter = jQuery('#dcs-tracker-agent-filter').val();
		
		jQuery("div.dcs-tracker-error-message").hide();
			
		if( name == '' )
		{
			jQuery("p#dcs-tracker-error-message").text( "The Agent Portal name cannot be blank." );
			jQuery("div.dcs-tracker-error-message").show();
			return;
		}
		
		var data = {
			name : name,
			agent_filter: agent_filter,
			action: 'dcs_tracker_create_agent_portal',
			dcs_tracker_create_agent_portal_nonce: dcs_tracker_admin_script_vars.dcs_tracker_create_agent_portal_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
});