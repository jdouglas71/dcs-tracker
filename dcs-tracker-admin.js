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
			//ripcord_contact_page_submit_nonce: ripcord_contact_page_script_vars.ripcord_contact_page_submit_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
	
	/** Create Page click */
	jQuery('#dcs-tracker-code-create-page').click( function() {
		if( jQuery('input#dcs-tracker-code-create-page').is(":checked") )
		{
			jQuery('tr#dcs-tracker-code-redirect-page').show();
		}
		else
		{
			jQuery('tr#dcs-tracker-code-redirect-page').hide();
		}
	});
});