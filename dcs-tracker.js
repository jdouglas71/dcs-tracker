jQuery(document).ready( function() {

	jQuery("#dcs-tracker-add-discount").click( function() {

		var discountAmount = jQuery("input#dcs-tracker-discount").val();

		if( !jQuery.isNumeric(discountAmount) )
		{
			alert( "The discount value must be a number." );
			return;
		}

		var data = {
			action: 'dcs_tracker_add_discount',
			amount: discountAmount,   
			dcs_tracker_add_discount_nonce: dcs_tracker_script_vars.dcs_tracker_add_discount_nonce
		};
  
		jQuery.post( dcs_tracker_script_vars.ajaxurl, data, function(response) {
			alert( response );
			location.reload( true );
		});
	});

});

