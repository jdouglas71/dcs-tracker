jQuery(document).ready( function() {

    jQuery("div.dcs-tracker-message").hide();
	jQuery("div.dcs-tracker-error-message").hide();

	jQuery("#dcs-tracker-add-discount").click( function() {

		jQuery("div.dcs-tracker-message").hide();
		jQuery("div.dcs-tracker-error-message").hide();

		var discountAmount = jQuery("input#dcs-tracker-discount").val();
		var discountName = jQuery("input#dcs-tracker-discount-name").val().trim();
		var discountType = "flat";

		if( jQuery("#dcs-tracker-discount-type").is(":checked") )
		{
			discountType = "percentage";
		}

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
			type: discountType,
			dcs_tracker_add_discount_nonce: dcs_tracker_script_vars.dcs_tracker_add_discount_nonce
		};
  
		jQuery.post( dcs_tracker_script_vars.ajaxurl, data, function(response) {
			//alert( response );
			jQuery("p#dcs-tracker-message").text( response );
			jQuery("div.dcs-tracker-message").show();
			//location.reload( true );
		});
	});

	jQuery("body").ajaxStart( function() {
		jQuery(this).css({'cursor':'wait'});
	}).ajaxStop(function() {
		jQuery(this).css({'cursor':'default'});
	});

	jQuery("#dcs-tracker-discount-type").click( function() {
		jQuery("label#dcs-tracker-discount").toggleClass("percentage"); 
		if( jQuery("#dcs-tracker-discount-type").is(":checked") )
		{
			//jQuery("#dcs-tracker-discount").step(1);
		}
		else
		{
			//jQuery("#dcs-tracker-discount").step(0.01);
		}
	});

	jQuery("#dcs-tracker-delete-discounts").click( function() {

		var names = [];
		jQuery(".dcs-tracker-delete-checker:checked").each(function() {
			names.push(this.value);
		});

		if( names.length > 0 )
		{
			var data = {
				action: 'dcs_tracker_delete_discounts',
				names: names,
				dcs_tracker_delete_discounts_nonce: dcs_tracker_script_vars.dcs_tracker_delete_discounts_nonce
			};
	
			jQuery.post( dcs_tracker_script_vars.ajaxurl, data, function(response) {
				location.reload( true );
			});
		}
	});
});

