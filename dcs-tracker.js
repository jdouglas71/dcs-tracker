jQuery(document).ready( function() {

	jQuery("div.dcs-tracker-message").hide();
	jQuery("div.dcs-tracker-error-message").hide();

	jQuery("#dcs-tracker-add-discount").click( function() {

		jQuery("div.dcs-tracker-message").hide();
		jQuery("div.dcs-tracker-error-message").hide();

		var discountAmount = jQuery("input#dcs-tracker-discount").val();
		var discountName = jQuery("input#dcs-tracker-discount-name").val().trim();

		if( !jQuery.isNumeric(discountAmount) )
		{
			jQuery("p#dcs-tracker-error-message").text( "The discount amount must be a number." );
			jQuery("div.dcs-tracker-error-message").show();
			return;
		}

		if( !discountName )
		{
			jQuery("p#dcs-tracker-error-message").text( "The discount name cannot be blank." );
			jQuery("div.dcs-tracker-error-message").show();

			return;
		}

		var data = {
			action: 'dcs_tracker_add_discount',
			amount: discountAmount, 
			name: discountName,
			dcs_tracker_add_discount_nonce: dcs_tracker_script_vars.dcs_tracker_add_discount_nonce
		};
  
		jQuery.post( dcs_tracker_script_vars.ajaxurl, data, function(response) {
			//alert( response );
			jQuery("p#dcs-tracker-message").text( response );
			jQuery("div.dcs-tracker-message").show();
			//location.reload( true );
		});
	});

});

