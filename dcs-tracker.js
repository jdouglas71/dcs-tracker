jQuery(document).ready( function() {

	jQuery("div.dcs-tracker-message").hide();

	jQuery("#dcs-tracker-add-discount").click( function() {

		var discountAmount = jQuery("input#dcs-tracker-discount").val();
		var discountName = jQuery("input#dcs-tracker-discount-name").val().trim();

		if( !jQuery.isNumeric(discountAmount) )
		{
			alert( "The discount amount must be a number." );
			return;
		}

		if( !discountName )
		{
			alert( "The discount name cannot be blank." );
			return;
		}

		var data = {
			action: 'dcs_tracker_add_discount',
			amount: discountAmount, 
			name: discountName,
			dcs_tracker_add_discount_nonce: dcs_tracker_script_vars.dcs_tracker_add_discount_nonce
		};
  
		jQuery.post( dcs_tracker_script_vars.ajaxurl, data, function(response) {
			alert( response );
			jQuery("p#dcs-tracker-message").text( response );
			jQuery("div.dcs-tracker-message").show();
			//location.reload( true );
		});
	});

});

