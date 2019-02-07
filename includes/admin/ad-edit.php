<?php
/**
 * Ad editor page
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

//Ad New/Edit form page

$error_msgs = array();
$ad_id = isset( $_REQUEST[ 'ad_id'] )? intval( $_REQUEST[ 'ad_id'] ) : 0;//must use $_REQUEST , ad_id may comes within both GET or POST method
$cp_from_id = isset( $_REQUEST[ 'cp_from_id'] )? intval( $_REQUEST[ 'cp_from_id'] ) : 0;

//Retrieve previous ad data from db 
$ad_from_db = false;
$post_id = 0;
if( $ad_id || $cp_from_id )
{
	//load ad from db
	$post_id = ( $ad_id != 0 ) ? $ad_id : $cp_from_id; 
	$old_ad = adguru()->manager->get_ad( $post_id );
	#be confirm that Ad found and Ad type is current type of ad.
	if( $old_ad && $old_ad->type == $current_ad_type )
	{
		$ad_from_db = $old_ad;
	}
	unset($old_ad);
	
}

//Create A Blank Ad Object
$ad = new ADGURU_Ad($current_ad_type);

$ad->ID = $ad_id;
$ad->name = "";
$ad->description = "";
$ad->content_type = "";


if( ! empty( $_POST ) && isset( $_POST['save'] ) && check_admin_referer( 'adguru_ad_editor_'.$current_ad_type, 'adguru_ad_editor_nonce_'.$current_ad_type )  )
{
	
	$ad->name = stripslashes ( trim( $_POST['ad_name'] ) );
	if( $ad->name == "" )
	{ 
		adguru_set_ad_input_error( 'ad_name' , __( "Name is required", 'adguru' ) ); 
	}
	
	$ad->description = stripslashes ( trim( $_POST['description'] ) );
	if( isset( $_POST[ 'content_type' ] ) && trim( $_POST[ 'content_type' ] ) != "")
	{
		$ad->content_type = trim( $_POST[ 'content_type' ] );
		$ad = apply_filters( "adguru_content_prepare_to_save_{$ad->content_type}", $ad, $ad_from_db );
	}

	
	$ad = apply_filters( "adguru_ad_prepare_to_save_{$current_ad_type}", $ad, $ad_from_db );
	//CHECK FOR ERROR 
	$input_error = false;
	$error_msgs = adguru_get_ad_input_error();

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
		$ad_id = adguru()->manager->save_ad( $ad );
		$ad->ID = $ad_id;
		if( $ad_id )
		{
			$redirect_to = remove_query_arg( 'cp_from_id' );
			$redirect_to = add_query_arg( array( 'ad_id' => $ad_id, "msg"=>1 ), $redirect_to ) ; 
			adguru_html_redirect( $redirect_to ); exit;
		}
	}
}
elseif( $ad_id || $cp_from_id )
{
	if( $ad_from_db )
	{
		$ad = $ad_from_db;
		if( $ad_id == 0 )
		{
			$ad->ID = 0;
		}
		if( !isset( $ad->content_type ) )
		{
			$ad->content_type = "html";
		}
	
	}
	else
	{
		$ad->ID = 0;
		echo '<div class="error"><p>'; echo sprintf( __( 'No %s found for the ID %d , Create new.' , 'adguru' ) , $current_ad_type_args['name'], $post_id ); echo '</p></div>';
		
	}
	
}

if( ( $ad->content_type == "" ) && count( $content_types ) )
{
	//first key of contnet types array
	$ad->content_type = array_keys( $content_types )[0];
}

if( isset( $_REQUEST['msg']) && $_REQUEST['msg'] == 1 && !isset( $_POST['save'] ) )
{
	echo '<div class="updated"><p>'; echo sprintf( __( 'Your %s has been saved successfully' , 'adguru' ) , $current_ad_type_args['name'] ); echo '</p></div>';
} 
?>
<form action="" method="post">
	<input type="hidden" name="ad_id" value="<?php echo $ad->ID ?>" />
	<?php wp_nonce_field( 'adguru_ad_editor_'.$current_ad_type, 'adguru_ad_editor_nonce_'.$current_ad_type ); ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;" >

				<?php do_action( "adguru_ad_editor_left_top", $ad, $error_msgs ) ?>	
				<?php do_action( "adguru_ad_editor_left_top_{$current_ad_type}", $ad, $error_msgs ) ?>

				<table class="widefat" id="ad_editor" style="width:100%; margin-bottom:20px;">
					<thead>
						<tr>
						<th width="150">&nbsp;</th>
						<th>&nbsp;</th>
						</tr>
					</thead>
					<?php $error_class = isset( $error_msgs['ad_name'] )? " adg_error_field" : ""; ?>
					<tr>
						<td><label for="ad_name"><?php echo sprintf( __( '%s Name', 'adguru' ) , $current_ad_type_args['name'] ) ?></label></td>
						<td><input type="text" name="ad_name" id="ad_name" class="input_long<?php echo $error_class;?>" size="30" value="<?php  echo esc_attr( $ad->name );?>" /></td>
					</tr>
					<tr>
						<td><label for="description"><?php echo __( 'Description', 'adguru' ) ?></label></td>
						<td><textarea name="description" id="description"  class="input_long" cols="15" rows="4"><?php  echo $ad->description;?></textarea></td>
					</tr>
				</table>

				<?php do_action( "adguru_ad_editor_left_row_{$current_ad_type}", $ad, $error_msgs ) // this line will be removed after fix all ad types ?>
				
				<?php do_action( "adguru_ad_editor_left_before_content_{$current_ad_type}", $ad, $error_msgs ) ?>

				

				<div id="content_type_box" class="postbox" >
					
					<h2 class='hndle'><span><?php _e( 'Content Type', 'adguru' ) ?></span></h2>
					<div class="inside">
						<p>
							<table style="width:100%;">
								<tr>
									<td width="150"><?php _e( 'Select Contnet Type', 'adguru' ) ?></td>
									<td>
										
										<select id="content_type" name="content_type" class="adguru_toggler_dropdown" to_toggle="content_editor_box">
											<?php foreach( $content_types as $content_type => $args )
											{
											?>
											<option value="<?php echo $content_type?>"<?php echo ($content_type == $ad->content_type )? ' selected="selected"':''?> ><?php echo $args['name'] ?></option>
											<?php 
											}
											?>
										</select>
									</td>
								</tr>
							</table>
						</p>
					</div>

				</div><!-- /.postbox -->

				<?php 
				foreach( $content_types as $content_type => $args )
				{
					$hidden = ($content_type == $ad->content_type ) ? "" : " hidden";
				?>
				<div id="content_editor_box_<?php $content_type ?>" class="postbox content_editor_box content_editor_box_<?php echo $content_type ?><?php echo $hidden ?>">
					
					<h2 class='hndle'><span><?php _e( 'Content Editor', 'adguru' ) ?>: <?php echo $args['name']?></span></h2>
					<div id="content_editor_<?php $content_type ?>" class="inside content_editor content_editor_<?php echo $content_type ?>">

						<?php do_action( "adguru_content_editor_{$content_type}", $ad, $error_msgs ); ?>
						
					</div>

				</div>
				<?php 
				}
				?>
				
				<?php do_action( "adguru_ad_editor_left_after_content_{$current_ad_type}", $ad, $error_msgs ) ?>

				<div id="save_box" class="postbox" >
					
					<h2 class='hndle'><span><!-- section heading --></span></h2>
					<div class="inside">
						<p>
							<input type="submit" name="save" class="button-primary" value="<?php echo esc_attr( __( 'Save', 'adguru' ) ) ?>" style="width:100px;" />
						<p>
					</div>

				</div><!-- /.postbox -->

				<?php do_action( "adguru_ad_editor_left_bottom_{$current_ad_type}", $ad, $error_msgs ) ?>
				<?php do_action( "adguru_ad_editor_left_bottom", $ad, $error_msgs ) ?>			
				
				
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

				<?php do_action( "adguru_ad_editor_sidebar_top", $ad, $error_msgs ) ?>
				<?php do_action( "adguru_ad_editor_sidebar_top_{$current_ad_type}", $ad, $error_msgs ) ?>			
				<?php do_action( "adguru_ad_editor_sidebar_{$current_ad_type}", $ad, $error_msgs ) ?>
				<?php do_action( "adguru_ad_editor_sidebar_bottom_{$current_ad_type}", $ad, $error_msgs ) ?>
				<?php do_action( "adguru_ad_editor_sidebar_bottom", $ad, $error_msgs ) ?>
						 
			</div><!--end #postbox-container-1-->
			<!--End Sidebar-->
		</div><!-- end #post-body-->
		<br class="clear"><br />
	</div> <!--end #poststuff -->
</form>