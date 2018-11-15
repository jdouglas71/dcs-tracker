jQuery(document).ready(function() {

	/** Create code click */
	jQuery('#dcs-tracker-create-code').click( function() {
		var name = jQuery('#dcs-tracker-code-name').val();
		var value = jQuery('#dcs-tracker-code-value').val();
		var type = "flat";
		var redirect = "";
		var has_page = "false";
		var use_portal_parent = "false";
		var allow_international = "false";
		var type = jQuery('#dcs-tracker-code-type').val();
					
		if( jQuery('#dcs-tracker-code-create-page').is(":checked") )
		{
			redirect = jQuery('#dcs-tracker-code-redirect').val();
			has_page = "true";
			if( jQuery('#dcs-tracker-code-redirect-parent').is(":checked") )
				use_portal_parent = "true";
		}			

		if( jQuery('#dcs-tracker-allow-international').is(":checked") )
		{
			allow_international = "true";
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
			use_portal_parent : use_portal_parent,
			allow_international: allow_international,
			action: 'dcs_tracker_create_code',
			dcs_tracker_create_code_nonce: dcs_tracker_admin_script_vars.dcs_tracker_create_code_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
	
	/** Create Page click */
 	jQuery('#dcs-tracker-code-create-page').click( function() {
 		if( jQuery('input#dcs-tracker-code-create-page').is(":checked") )
 		{
 			jQuery('tr#dcs-tracker-code-redirect-page-parent').show();
 			jQuery('tr#dcs-tracker-code-redirect-page').show();
 		}
 		else
 		{
 			jQuery('tr#dcs-tracker-code-redirect-page-parent').hide();
 			jQuery('tr#dcs-tracker-code-redirect-page').hide();
 		}
 	});
	
	/** Create Agent Portal click */
	jQuery('#dcs-tracker-create-agent-portal').click( function() {
		var name = jQuery('#dcs-tracker-agent-name').val();
		var agent_filter = jQuery('#dcs-tracker-agent-filter').val();
		var allow_international = "false";
		
		if( jQuery('#dcs-tracker-allow-international').is(":checked") )
		{
			allow_international = "true";
		}			
		
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
			allow_international: allow_international,
			action: 'dcs_tracker_create_agent_portal',
			dcs_tracker_create_agent_portal_nonce: dcs_tracker_admin_script_vars.dcs_tracker_create_agent_portal_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
	
	/** Update Codes */
	jQuery('#dcs-tracker-update-code').click( function() {
		var values = "";
		jQuery('.dcs-tracker-code-delete:checkbox:checked').each(function () {
			values = values.concat( jQuery(this).val()+";" );
  		});
  		
  		jAlert( values, "Update Codes" );
  		
		var data = {
			values : values,
			action: 'dcs_tracker_update_codes',
			dcs_tracker_update_codes_nonce: dcs_tracker_admin_script_vars.dcs_tracker_update_codes_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
	
	/** Update Agents */
	jQuery('#dcs-tracker-update-agent-portal').click( function() {
		var values = "";
		jQuery('.dcs-tracker-agent-delete:checkbox:checked').each(function () {
			values = values.concat( jQuery(this).val()+";" );
  		});
  		
		var data = {
			values : values,
			action: 'dcs_tracker_update_agents',
			dcs_tracker_update_agents_nonce: dcs_tracker_admin_script_vars.dcs_tracker_update_agents_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});

	/** Update Google Options */
	jQuery('#dcs-tracker-update-google-options').click( function() {
		var flag = "false";
		if( jQuery('#dcs-tracker-google-analytics-flag').is(":checked") )
			flag = "true";
		var code = jQuery('#dcs-tracker-google-analytics-code').val();  		
		
		var data = {
			flag: flag,
			code: code,
			action: 'dcs_tracker_update_google_options',
			dcs_tracker_update_google_options_nonce: dcs_tracker_admin_script_vars.dcs_tracker_update_google_options_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});

});