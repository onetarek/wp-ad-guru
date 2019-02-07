<?php

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

//Ad New/Edit form page

$error_msgs = array();
$zone_id = isset( $_REQUEST[ 'zone_id'] ) ? intval( $_REQUEST[ 'zone_id'] ) : 0;//must use $_REQUEST , zone_id may comes wiht both GET or POST method
$cp_from_id = isset( $_REQUEST[ 'cp_from_id'] ) ? intval( $_REQUEST[ 'cp_from_id'] ) : 0;

//Retrieve previous ad data from db 
$zone_from_db = false;
$post_id = 0;
if( $zone_id || $cp_from_id )
{
	//load ad from db
	$post_id = ( $zone_id != 0 ) ? $zone_id : $cp_from_id; 
	$old_zone = adguru()->manager->get_ad( $post_id );
	#be confirm that zone is found.
	if( $old_zone )
	{
		$zone_from_db = $old_zone;
	}
	unset($old_zone);
	
}

//Set a Blank Zone Object
$zone = new ADGURU_Zone();
$zone->ID = $zone_id;
$zone->name = "";
$zone->description = "";

if( ! empty( $_POST ) && isset( $_POST['save'] ) && check_admin_referer( 'adguru_zone_edit', 'adguru_zone_editor_nonce' )  )
{

	$zone->name = stripslashes ( trim($_POST['zone_name'] ) );
	if( $zone->name == "" ){ adguru_set_zone_input_error( 'zone_name' , __( "Name is required", 'adguru' ) ); }
	
	$zone->description = stripslashes ( trim($_POST['description'] ) );

	$zone->width = intval ( $_POST['width'] );
	if( $zone->width == 0 ){ adguru_set_zone_input_error( 'width' , __( "Width is required", 'adguru' ) ); }
	
	$zone->height = intval ( $_POST['height'] );
	if( $zone->height == 0 ){ adguru_set_zone_input_error( 'height' , __( "Height is required", 'adguru' ) ); }
	
	$zone->active = isset( $_POST['active'] )? intval ( $_POST['active'] ): 0;

	$zone = apply_filters( "adguru_zone_prepare_to_save", $zone, $zone_from_db );
	
	//CHECK FOR ERROR 
	$input_error = false;
	$error_msgs = adguru_get_zone_input_error();

	if( is_array( $error_msgs ) && count( $error_msgs ) )
	{
		$input_error = true;
		//print error messages
		echo '<div class="error">';
		foreach( $error_msgs as $field => $msg )
		{
			echo '<p>'.$msg.'</p>';
		}
		echo '</div>';	
	
	}
	else
	{
		//update or insert new ad
		$zone_id = adguru()->manager->save_zone( $zone );
		$zone->ID = $zone_id;
		if( $zone_id )
		{
			$redirect_to = add_query_arg( array( 'zone_id' => $zone_id, "msg"=>1 ) );
			adguru_html_redirect( $redirect_to ); exit;
		}
		else
		{
			echo '<div class="error"><p>'.__('An unknown error happend on saving zone', 'adguru').'</p></div>';
		}
	}
}
elseif( $zone_id || $cp_from_id )
{
	if( $zone_from_db )
	{
		$zone = $zone_from_db;
		if( $zone_id == 0 )
		{
			$zone->ID = 0;
		}
	
	}
	else
	{
		$zone->ID = 0;
		echo '<div class="error"><p>'; echo sprintf( __( 'No zone found for the ID %d , Create new.' , 'adguru' ), $post_id ); echo '</p></div>';
		
	}
	
}

if( isset( $_REQUEST['msg']) && $_REQUEST['msg'] == 1 && !isset( $_POST['save'] ) )
{
	echo '<div class="updated"><p>'; echo __( 'Your zone has been saved successfully' , 'adguru' ); echo '</p></div>';
} 

?>
<form action="" method="post">
	<input type="hidden" name="zone_id" value="<?php echo $zone->ID ?>" />
	<?php wp_nonce_field( 'adguru_zone_edit', 'adguru_zone_editor_nonce' ); ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;" >

				<?php do_action( "adguru_zone_editor_left_top", $zone, $error_msgs ) ?>	

				<table class="widefat" id="zone_editor" style="width:100%;">
					<thead>
						<tr>
						<th width="150">&nbsp;</th>
						<th>&nbsp;</th>
						</tr>
					</thead>
					<?php $error_class = isset( $error_msgs['zone_name'] )? " adg_error_field" : ""; ?>
					<tr>
						<td><label for="zone_name"><?php echo  __( 'Zone Name', 'adguru' ) ?></label></td>
						<td><input type="text" name="zone_name" id="zone_name" class="input_long<?php echo $error_class;?>" size="30" value="<?php  echo esc_attr( $zone->name );?>" /></td>
					</tr>
					<tr>
						<td><label for="description"><?php echo __( 'Description', 'adguru' ) ?></label></td>
						<td><textarea name="description" id="description"  class="input_long" cols="15" rows="4"><?php  echo $zone->description;?></textarea></td>
					</tr>
					
					<tr>
						<td><label><?php echo __( 'Size', 'adguru' ) ?></label></td>
						<td>
						<?php 
						if( ! isset( $zone->width )){ $zone->width = ""; }
						if( ! isset( $zone->height )){ $zone->height = ""; }
						
						if( ( $zone->width == "" || $zone->height == "" ) && !isset( $_POST['save'] ) ){ $zone->width = 300; $zone->height = 250;}
						$size_txt = $zone->width."x".$zone->height;
						if( !in_array( $size_txt, array( "300x250", "468x60", "120x600", "728x90", "120x90", "160x600", "120x60", "125x125", "180x150" ) ) ){ $custom_size=true; }else{ $custom_size = false; }
						?>
						<select id="size_list" style="width:312px;">
							<option value="300x250" <?php echo ($size_txt=="300x250")?' selected="selected"':'';?>>Medium Rectangle (300 x 250)</option>                                    
							<option value="468x60" <?php echo ($size_txt=="468x60")?' selected="selected"':'';?>>Full Banner (468 x 60)</option>
							<option value="120x600" <?php echo ($size_txt=="120x600")?' selected="selected"':'';?>>Skyscraper (120 x 600)</option>
							<option value="728x90" <?php echo ($size_txt=="728x90")?' selected="selected"':'';?>>Leaderboard (728 x 90)</option>
							<option value="120x90" <?php echo ($size_txt=="120x90")?' selected="selected"':'';?>>Button 1 (120 x 90)</option>
							<option value="160x600" <?php echo ($size_txt=="160x600")?' selected="selected"':'';?>>Wide Skyscraper (160 x 600)</option>
							<option value="120x60" <?php echo ($size_txt=="120x60")?' selected="selected"':'';?>>Button 2 (120 x 60)</option>
							<option value="125x125" <?php echo ($size_txt=="125x125")?' selected="selected"':'';?>>Square Button (125 x 125)</option>
							<option value="180x150" <?php echo ($size_txt=="180x150")?' selected="selected"':'';?>>Rectangle (180 x 150)</option>
							<option value="custom" <?php echo ($custom_size)?' selected="selected"':'';?>>Custom</option>
						</select>
						<span id="custom_size_box">
						<?php $error_class = isset( $error_msgs['width'] )? " adg_error_field" : ""; ?>
						<?php echo __( 'Width', 'adguru' ) ?> <input type="text" name="width"  id="width" size="4"  value="<?php echo $zone->width;?>" class="<?php echo $error_class;?>" <?php echo (!$custom_size)?' readonly="readonly"':'';?> /> 
						<?php $error_class = isset( $error_msgs['height'] )? " adg_error_field" : ""; ?>
						<?php echo __( 'Height', 'adguru' ) ?> <input type="text" name="height" id="height" size="4" value="<?php echo $zone->height;?>" class="<?php echo $error_class;?>" <?php echo (!$custom_size)?' readonly="readonly"':'';?>/>
						</span>
						</td>
					</tr>
					<tr>
						<td><label><?php echo __( 'Active', 'adguru' ) ?></label></td>
						<td><input type="checkbox" name="active" value="1"  <?php echo  ( isset($zone->active) && $zone->active )? 'checked="checked"':''?> /></td>
					</tr>
								
					
					<?php do_action( "adguru_zone_editor_left_row", $zone, $error_msgs ) ?>


					<tr>
						<td colspan="2"><input type="submit" name="save" class="button-primary" value="Save" style="width:100px;" /></td>
					</tr>
				</table>
				
				<?php do_action( "adguru_zone_editor_left_bottom", $zone, $error_msgs ) ?>			
				
				
			</div><!--end #post-body-content-->
			<!--Sidebar-->
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h3 class="hndle"><?php echo __( 'Hello', 'adguru' ) ?></h3>
					<div class="inside">
						<div class="main">
							<?php echo __( 'No one has birthday this week!', 'adguru' ) ?>           
						</div><!-- .main -->
					</div><!-- .inside -->
				</div>

			<?php do_action( "adguru_zone_editor_sidebar_top", $zone, $error_msgs ) ?>		
			<?php do_action( "adguru_zone_editor_sidebar", $zone, $error_msgs ) ?>
			<?php do_action( "adguru_zone_editor_sidebar_bottom", $zone, $error_msgs ) ?>
					 
			</div><!--end #postbox-container-1-->
			<!--End Sidebar-->
		</div><!-- end #post-body-->
		<br class="clear"><br />
	</div> <!--end #poststuff -->

</form>