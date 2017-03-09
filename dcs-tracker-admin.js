jQuery(document).ready(function() {

	/** Create code click */
	jQuery('#dcs-tracker-create-code').click( function() {
		var name = jQuery('#dcs-tracker-code-name').val();
		var value = jQuery('#dcs-tracker-code-value').val();
		var type = "flat";
		if( jQuery('#dcs-tracker-code-type').is(":checked") )
			type = "percentage"; 
			
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
			action: 'dcs_tracker_create_code',
			//ripcord_contact_page_submit_nonce: ripcord_contact_page_script_vars.ripcord_contact_page_submit_nonce
		};

		jQuery.post( dcs_tracker_admin_script_vars.ajaxurl, data, function(response) {
			window.open( response, "_self" );
		});
	});
});