<?php 

	$google_analytics_id;
	$google_analytics_flag;
	$tracking_ids = array();
	$ids = array();
	$value = get_option("dcs_tracker_tracking_ids");
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

		update_option( "dcs_tracker_google_analytics_flag", $google_analytics_flag );
		update_option( "dcs_tracker_google_analytics_id",   $google_analytics_id );

		?>
		<div class="updated"><p><strong><?php _e('Options Updated.' ); ?></strong></p></div>
		<?php
	} 
	else 
	{
		$google_analytics_id = get_option( "dcs_tracker_google_analytics_id" );
		$google_analytics_flag = get_option( "dcs_tracker_google_analytics_flag" );
	}
?>

<div class="wrap">
	<?php echo "<p class='dcs-tracker-h1' style=''>"."<img src='http://douglasconsulting.net/favicon.ico' width='32'>". __( 'DCS Tracker Options', 'dcs_tracker_trdom' ) . "</p>"; ?>
	<hr class='dcs-tracker-admin'><br />

   	<?php echo "<p class='dcs-tracker-h2'>".__( 'Referral Codes', 'dcs_tracker_trdom' ) . "</p>"; ?>
   	<hr class='dcs-tracker-admin'><br />
	<label id='dcs-tracker-discount' for='dcs-tracker-discount'>Discount Amount</label><input id='dcs-tracker-discount' type='text' class='dcs-tracker-admin'>
	<button id='dcs-tracker-add-discount'>Generate Referral Code</button> 

   	<hr class='dcs-tracker-admin'><br />
	<?php echo "<p class='dcs-tracker-h2'>".__( 'Landing Pages', 'dcs_tracker_trdom' ) . "</p>"; ?>
	<hr class="dcs-tracker-admin">
	<form name="dcs_tracker_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="dcs_tracker_hidden" value="Y">
		<?php 
			  if( sizeof($tracking_ids) > 0 )
			  { ?>
				<table border="0" class="dcs-tracker-ids">
					<tr><th><h3>Reset</h3></th><th><h3>Tracking ID</h3></th><th><h3># of Redirects</h3></th><th><h3>Date of Last Reset</h3></th><tr>
				<?php
					 foreach($tracking_ids as $key => $value)
					 { ?>
						<tr><td><input type="checkbox" name="<?php echo $key ?>"></td><td><?php echo $key; ?></td><td style=""><?php echo $value; ?></td><td style=""><?php echo get_option("dcs_tracker_".$key."_lcd"); ?></td></tr>
					   <?php
					 }
				?>
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
        <p><?php _e("Google Analytics UID: " ); ?><input style="padding-left:10px;" type="text" name="google_analytics_id" value="<?php echo $google_analytics_id; ?>" size="32"></p>

		<hr class='dcs-tracker-admin'><br />

		<p class="submit" style="padding-left:100px;">
			<input type="submit" name="Submit" style="border-radius: 5px;" value="<?php _e('Update Settings', 'dcs_tracker_trdom' ) ?>" />
		</p>
	</form>
</div>