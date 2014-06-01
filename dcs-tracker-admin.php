<?php 

	$google_analytics_id;
	$google_analytics_flag;
	$tracking_ids = array();
	$ids = array();
	$value = get_option("dcs_tracker_tracking_ids");
	$discountValues = get_option("dcs_tracker_discounts", array());
	$referralPage = get_option("dcs_tracker_referral_page", "/product/ripcord/");

	if( $value != FALSE )
	{
		$ids = explode(";", $value);
		foreach( $ids as $id )
		{
			$tracking_ids[$id] = get_option( "dcs_tracker_".$id );
		}
	}

	if($_POST['dcs_tracker_hidden'] == 'Y') 
	{
		$today = new DateTime("now");
		//Process table
		foreach( $ids as $id )
		{
			if( isset($_POST[$id]) )
			{
				update_option( "dcs_tracker_".$id, 0 );
				$tracking_ids[$id] = 0;
				update_option( "dcs_tracker_".$id."_lcd", $today->format('l, M d Y') );
			}
		}

		//Process google options.
		$google_analytics_flag = $_POST["google_analytics_flag"];
		$google_analytics_id = $_POST["google_analytics_id"];
		$referralPage = $_POST["referral_page"];

		update_option( "dcs_tracker_google_analytics_flag", $google_analytics_flag );
		update_option( "dcs_tracker_google_analytics_id",   $google_analytics_id );
		update_option( "dcs_tracker_referral_page", $referralPage );

		?>
		<div class="updated"><p><strong><?php _e('Options Updated.' ); ?></strong></p></div>
		<?php
	} 
	else 
	{
		$google_analytics_id = get_option( "dcs_tracker_google_analytics_id" );
		$google_analytics_flag = get_option( "dcs_tracker_google_analytics_flag" );
		$referralPage = get_option("dcs_tracker_referral_page", "/product/ripcord/");
	}
?>

<div class="wrap">
	<?php echo "<p class='dcs-tracker-h1' style=''>"."<img src='http://douglasconsulting.net/favicon.ico' width='32'>". __( 'DCS Tracker Options', 'dcs_tracker_trdom' ) . "</p>"; ?>
	<hr class='dcs-tracker-admin'><br />

   	<?php echo "<p class='dcs-tracker-h2'>".__( 'Referral Codes', 'dcs_tracker_trdom' ) . "</p>"; ?>
   	<hr class='dcs-tracker-admin'><br />
	<div class="updated dcs-tracker-message" style="display:none;"><p id='dcs-tracker-message'></p></div> 
	<div class="error dcs-tracker-error-message" style="display:none;"><p id='dcs-tracker-error-message'></p></div> 
	<label for='dcs-tracker-discount-name'>Name</label><input id='dcs-tracker-discount-name' type='text' class='dcs-tracker-admin'>
	<label id='dcs-tracker-discount' for='dcs-tracker-discount'>Discount Amount</label><input id='dcs-tracker-discount' type='number' class='dcs-tracker-admin' min="0" value="0.00" step="1">
	<input type="checkbox" id="dcs-tracker-discount-type" value="percentage">Percentage
	<button id='dcs-tracker-add-discount'>Generate Referral Code</button> 
	<?php 
		  if( sizeof($discountValues) > 0 )
		  { ?>
			<table border="0" class="dcs-tracker-ids">
				<thead>
					<tr><th>Name</th><th>Discount Amount</th><th>Referral URL</th><tr>
				</thead>
				<tbody>
				<?php
					 foreach($discountValues as $name => $values)
					 { 
						if( !is_array($values) ) 
						{
						?>
							<tr><td><?php echo $name; ?></td><td><?php echo '$'.number_format($values, 2); ?></td><td style=""><a href="<?php echo site_url($referralPage)."?referralCode=".urlencode($name);?>"><?php echo site_url($referralPage)."?referralCode=".urlencode($name);?></a></td></tr>  
					    <?php
						}
						else
						{
							if( $values['type'] == "flat" )
							{
							?>
								<tr><td><?php echo $name; ?></td><td><?php echo '$'.number_format($values['amount'], 2); ?></td><td style=""><a href="<?php echo site_url($referralPage)."?referralCode=".urlencode($name);?>"><?php echo site_url($referralPage)."?referralCode=".urlencode($name);?></a></td></tr>  
							<?php
							}
							else
							{
							?>
								<tr><td><?php echo $name; ?></td><td><?php echo number_format($values['amount']*100, 0).'%'; ?></td><td style=""><a href="<?php echo site_url($referralPage)."?referralCode=".urlencode($name);?>"><?php echo site_url($referralPage)."?referralCode=".urlencode($name);?></a></td></tr>  
							<?php
							}
						}
					 }
				?>
				</tbody>
			</table>
			<?php 
		  } 
		  else
		  { ?>
			 <p>There are no referral codes defined in the database.</p>
		  <?php
		  }
		  ?>

	<form name="dcs_tracker_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="dcs_tracker_hidden" value="Y">
        <p><?php _e("Referral Page: " ); ?><input style="padding-left:10px;" type="text" class="dcs-tracker-admin" name="referral_page" value="<?php echo $referralPage; ?>" size="32"></p>


		<hr class='dcs-tracker-admin'><br />

		<?php echo "<p class='dcs-tracker-h2'>".__( 'Landing Pages', 'dcs_tracker_trdom' ) . "</p>"; ?>
		<hr class="dcs-tracker-admin"><br />
		<?php 
			  if( sizeof($tracking_ids) > 0 )
			  { ?>
				<table border="0" class="dcs-tracker-ids">
					<thead>
						<tr><th>Reset</th><th>Tracking ID</th><th># of Redirects</th><th>Date of Last Reset</th><tr>
					</thead>
					<tbody>
					<?php
						 foreach($tracking_ids as $key => $value)
						 { ?>
							<tr><td><input type="checkbox" name="<?php echo $key ?>"></td><td><?php echo $key; ?></td><td style=""><?php echo $value; ?></td><td style=""><?php echo get_option("dcs_tracker_".$key."_lcd"); ?></td></tr>
						   <?php
						 }
					?>
					</tbody>
				</table>
			    <?php 
			  } 
			  else
			  { ?>
				 <p>There are no tracking ids defined in the database.</p>
			  <?php
			  }
			  ?>
		<br />
		<hr class="dcs-tracker-admin" style=""><br />

		<?php echo "<p class='dcs-tracker-h2'>".__( 'Google Analytics Options', 'dcs_tracker_trdom' ) . "</p>"; ?>
		<hr class='dcs-tracker-admin'><br />

        <p><?php _e("Use Google Analytics: " ); ?><input style="padding-left:10px;" type="checkbox" name="google_analytics_flag" value="1" <?php if($google_analytics_flag == '1') echo 'checked'; ?>></p>
        <p><?php _e("Google Analytics UID: " ); ?><input style="padding-left:10px;" class="dcs-tracker-admin" type="text" name="google_analytics_id" value="<?php echo $google_analytics_id; ?>" size="32"></p>

		<hr class='dcs-tracker-admin'><br />

		<p class="submit" style="padding-left:100px;">
			<input type="submit" name="Submit" style="border-radius: 5px;" value="<?php _e('Update Settings', 'dcs_tracker_trdom' ) ?>" />
		</p>
	</form>
</div>