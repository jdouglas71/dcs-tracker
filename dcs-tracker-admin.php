<?php 

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
		foreach( $ids as $id )
		{
			if( isset($_POST[$id]) )
			{
				update_option( "dcs_tracker_".$id, 0 );
				$tracking_ids[$id] = 0;
			}
		}

		?>
		<div class="updated"><p><strong><?php _e('Selected Redirect Numbers\'s Reset.' ); ?></strong></p></div>
		<?php
	} 
	else 
	{
	}
?>

<div class="wrap">
	<?php echo "<p style='font:bold 2.0em Verdana;vertical-align:top;'>"."<img src='http://douglasconsulting.net/favicon.ico' width='32'>". __( 'DCS Tracker Options', 'dcs_tracker_trdom' ) . "</p>"; ?>
	
	<form name="dcs_tracker_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="dcs_tracker_hidden" value="Y">
		<?php 
			  if( sizeof($tracking_ids) > 0 )
			  { ?>
				<table border="0" style="text-align:center;padding:5px;padding-right:15px;border-collapse:collapse;">
					<tr><th><h3>Reset</h3></th><th><h3>Tracking ID</h3></th><th><h3># of Redirects</h3></th><tr>
				<?php
					 foreach($tracking_ids as $key => $value)
					 { ?>
						<tr><td><input type="checkbox" name="<?php echo $key ?>"></td><td><?php echo $key; ?></td><td style=""><?php echo $value; ?></td></tr>
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
		<p class="submit" style="padding-left:100px;">
			<input type="submit" name="Submit" value="<?php _e('Reset', 'dcs_tracker_trdom' ) ?>" />
		</p>
	</form>
</div>