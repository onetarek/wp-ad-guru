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
	$old_zone = adguru()->manager->get_zone( $post_id );
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


$size_list = array(
	'728x90'=>'Leaderboard',
	'468x60'=>'Banner',
	'234x60'=>'Half Banner',
	'125x125'=>'Button',
	'120x600'=>'Skyscraper',
	'160x600'=>'Wide Skyscraper',
	'120x240'=>'Verticle Banner',
	'200x200'=>'Small square',
	'250x250'=>'Square',
	'120x90'=>'Button 2',
	'120x60'=>'Button 3',
	'180x150'=>'Small Rectangle',
	'300x250' => 'Medium Rectangle',
	'336x280'=>'Large rectangle',
	'300x600'=>'Half page',
	'300x1050'=>'Portrait',
	'320x50'=>'Mobile banner',
	'970x90'=>'Large leaderboard',
	'970x250'=>'Billboard'
);	

						
?>
<form action="" method="post">
	<input type="hidden" name="zone_id" value="<?php echo $zone->ID ?>" />
	<?php wp_nonce_field( 'adguru_zone_edit', 'adguru_zone_editor_nonce' ); ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;" >

				<?php do_action( "adguru_zone_editor_main_top", $zone, $error_msgs ) ?>	

				<table class="widefat" id="zone_editor" style="width:100%; margin-bottom:20px;">
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
						<td><label><?php echo __( 'Active', 'adguru' ) ?></label></td>
						<td><input type="checkbox" name="active" value="1"  <?php echo  ( isset($zone->active) && $zone->active )? 'checked="checked"':''?> /><?php if( ! ( isset($zone->active) && $zone->active ) ) { ?> <span style="color:red"><?php _e("Must activate zone to see output", "adguru")?></span><?php }?></td>
					</tr>

					<tr>
						<td><label><?php echo __( 'Size', 'adguru' ) ?></label></td>
						<td>
						<?php 
						if( ! isset( $zone->width )){ $zone->width = ""; }
						if( ! isset( $zone->height )){ $zone->height = ""; }
						
						if( ( $zone->width == "" || $zone->height == "" ) && !isset( $_POST['save'] ) ){ $zone->width = 300; $zone->height = 250;}
						$size_txt = $zone->width."x".$zone->height;
						if( !in_array( $size_txt, array_keys( $size_list ) ) ){ $custom_size=true; }else{ $custom_size = false; }
						?>
						<select id="size_list" style="width:312px;">
							<?php 
							foreach( $size_list as $size => $size_name )
							{
								$selected = ( $size_txt == $size ) ? ' selected="selected"' : '';
								
								echo '<option value="'.$size.'"'.$selected.'>'.$size_name.' ( '.$size.' )</option>';
							}
							?>
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
					

				</table>
				
				<?php do_action( "adguru_zone_editor_main", $zone, $error_msgs ) ?>

				<?php do_action( "adguru_zone_editor_main_bottom", $zone, $error_msgs ) ?>			
				
				
			</div><!--end #post-body-content-->
			<!--Sidebar-->
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h3 class="hndle"><?php _e('Publishing', 'adguru')?></h3>
					<div class="inside">
						<div class="main" style="text-align:center;">	
							
							<input type="submit" name="save" class="button-primary" value="<?php echo esc_attr( __( 'Save', 'adguru' ) ) ?>" style="width:200px;" />
							

						</div><!-- .main -->
					</div><!-- .inside -->
				</div>
				<?php
				if( $zone->ID )
				{
					$ad_setup_page_link = "admin.php?page=adguru_setup_ads&ad_type=banner&zone_id=".$zone->ID;
				?>
				<div class="postbox">
					<h3 class="hndle"><?php echo __( 'Setup Ads', 'adguru' ) ?></h3>
					<div class="inside">
						<div class="main">
							<a href="<?php echo $ad_setup_page_link?>" ><?php echo __( 'Setup Ads to this zone', 'adguru' ) ?></a>
						</div><!-- .main -->
					</div><!-- .inside -->
				</div>
				<?php 
				}
				else
				{
				?>
				<div class="postbox">
					<h3 class="hndle"><?php echo __( 'Hello', 'adguru' ) ?></h3>
					<div class="inside">
						<div class="main">
							<?php echo __( 'No one has birthday this week!', 'adguru' ) ?>           
						</div><!-- .main -->
					</div><!-- .inside -->
				</div>
				<?php 
				}
				?>
				

			<?php do_action( "adguru_zone_editor_sidebar_top", $zone, $error_msgs ) ?>		
			<?php do_action( "adguru_zone_editor_sidebar", $zone, $error_msgs ) ?>
			<?php do_action( "adguru_zone_editor_sidebar_bottom", $zone, $error_msgs ) ?>
					 
			</div><!--end #postbox-container-1-->
			<!--End Sidebar-->
		</div><!-- end #post-body-->
		<br class="clear"><br />
	</div> <!--end #poststuff -->

</form>