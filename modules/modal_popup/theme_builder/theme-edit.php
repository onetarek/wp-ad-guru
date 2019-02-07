<?php

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

//Theme New/Edit form page
global $adguru_mp_theme_manager;
$error_msgs = array();
$theme_id = isset( $_REQUEST[ 'theme_id'] )? intval( $_REQUEST[ 'theme_id'] ) : 0;//must use $_REQUEST , theme_id may comes within both GET or POST method
$cp_from_id = isset( $_REQUEST[ 'cp_from_id'] )? intval( $_REQUEST[ 'cp_from_id'] ) : 0;

//Retrieve previous theme data from db 
$theme_from_db = false;
$post_id = 0;
if( $theme_id || $cp_from_id )
{
	//load ad from db
	$post_id = ( $theme_id != 0 ) ? $theme_id : $cp_from_id; 
	$old_theme = adguru()->manager->get_ad( $post_id );
	#be confirm that theme is found.
	if( $old_theme )
	{
		$theme_from_db = $old_theme;
	}
	unset($old_theme);
	
}

//Create A Blank theme Object
$theme = new ADGURU_Modal_Popup_Theme();

$theme->ID = $theme_id;
$theme->name = "";
$theme->description = "";

if( ! empty( $_POST ) && isset( $_POST['save'] ) && check_admin_referer( 'adguru_mp_theme_editor', 'adguru_mp_theme_editor_nonce' )  )
{
	
	$theme->name = stripslashes ( trim($_POST['theme_name']) );
	if( $theme->name == "" )
	{ 
		adguru_set_ad_input_error( 'theme_name' , __( "Name is required", 'adguru' ) ); 
	}
	
	$theme->description = stripslashes ( trim($_POST['description']) );
	
	$theme = apply_filters( "adguru_modal_popup_theme_prepare_to_save", $theme, $theme_from_db );
	//CHECK FOR ERROR 
	$input_error = false;
	$error_msgs = adguru_get_ad_input_error();

	if( is_array( $error_msgs ) && count( $error_msgs ) )
	{
		$input_error = true;
		//print error messages
		echo '<div class="error">';
		foreach( $error_msgs as $field=>$msg )
		{
			echo '<p>'.$msg.'</p>';
		}
		echo '</div>';	
	
	}
	else
	{
		//update or insert new ad
		$theme_id = $adguru_mp_theme_manager->save_theme( $theme );
		$theme->ID = $theme_id;
		if( $theme_id )
		{
			$redirect_to = remove_query_arg( 'cp_from_id' );
			$redirect_to = add_query_arg( array( 'theme_id' => $theme_id, "msg"=>1 ), $redirect_to ) ; 
			adguru_html_redirect( $redirect_to ); exit;
		}
	}
}
elseif( $theme_id || $cp_from_id )
{

	if( $theme_from_db )
	{
		$theme = $theme_from_db;
		if( $theme_id == 0 )
		{
			$theme->ID = 0;
		}
	
	}
	else
	{
		$theme->ID = 0;
		echo '<div class="error"><p>'; echo sprintf( __( 'No theme found for the ID %d , Create new.' , 'adguru' ) , $post_id ); echo '</p></div>';
		
	}
	
}

if( isset( $_REQUEST['msg']) && $_REQUEST['msg'] == 1 && !isset( $_POST['save'] ) )
{
	echo '<div class="updated"><p>'; echo __( 'Your theme has been saved successfully' , 'adguru' ); echo '</p></div>';
} 
?>
<form action="" method="post">
	<input type="hidden" name="theme_id" value="<?php echo $theme->ID ?>" />
	<?php wp_nonce_field( 'adguru_mp_theme_editor', 'adguru_mp_theme_editor_nonce' ); ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;" >

				<?php do_action( "adguru_modal_popup_theme_editor_left_top", $theme, $error_msgs ) ?>	

				<table class="widefat" id="ad_editor" style="width:100%; margin-bottom:20px;">
					<thead>
						<tr>
						<th width="150">&nbsp;</th>
						<th>&nbsp;</th>
						</tr>
					</thead>
					<?php $error_class = isset( $error_msgs['theme_name'] )? " adg_error_field" : ""; ?>
					<tr>
						<td><label for="theme_name"><?php echo __( 'Theme Name', 'adguru' ) ?></label></td>
						<td><input type="text" name="theme_name" id="theme_name" class="input_long<?php echo $error_class;?>" size="30" value="<?php  echo esc_attr( $theme->name );?>" /></td>
					</tr>
					<tr>
						<td><label for="description"><?php echo __( 'Description', 'adguru' ) ?></label></td>
						<td><textarea name="description" id="description"  class="input_long" cols="15" rows="4"><?php  echo $theme->description;?></textarea></td>
					</tr>
				</table>

				
				<?php do_action( "adguru_modal_popup_theme_editor_left_after_basic", $theme, $error_msgs ) ?>

				
				<div id="save_box" class="postbox" >
					
					<h2 class='hndle'><span><!-- section heading --></span></h2>
					<div class="inside">
						<p>
							<?php if(isset($theme->builtin)&&$theme->builtin==1){ ?>
							<input type="submit" name="save" class="button-primary" value="<?php _e('Save', 'adguru' )?>" style="width:100px;" disabled /><br>
							<?php _e('This is a <b>builtin theme</b>,You can not modify.', 'adguru' )?> 
							<?php } else { ?>
							<input type="submit" name="save" class="button-primary" value="<?php _e('Save', 'adguru' )?>" style="width:100px;" />
							<?php } ?>
						<p>
					</div>

				</div><!-- /.postbox -->

				<?php do_action( "adguru_modal_popup_theme_editor_left_bottom", $theme, $error_msgs ) ?>			
				
				
			</div><!--end #post-body-content-->
			<!--Sidebar-->
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h3 class="hndle">&nbsp;</h3>
					<div class="inside">
						<div class="main" style="text-align:center;">	
							
							<?php if(isset($theme->builtin)&&$theme->builtin==1){ ?>
							<input type="submit" name="save" class="button-primary" value="<?php _e('Save', 'adguru' )?>" style="width:200px;" disabled /><br>
							<?php _e('This is a <b>builtin theme</b>,You can not modify.', 'adguru' )?> 
							<?php } else { ?>
							<input type="submit" name="save" class="button-primary" value="<?php _e('Save', 'adguru' )?>" style="width:200px;" />
							<?php } ?>
							

						</div><!-- .main -->
					</div><!-- .inside -->
				</div>
				<div class="postbox">
					<h3 class="hndle">Hello</h3>
					<div class="inside">
						<div class="main">
								
							No one has birthday this week!

						</div><!-- .main -->
					</div><!-- .inside -->
				</div>

				<?php do_action( "adguru_modal_popup_theme_editor_sidebar_top", $theme, $error_msgs ) ?>
				<?php do_action( "adguru_modal_popup_theme_editor_sidebar", $theme, $error_msgs ) ?>
				<?php do_action( "adguru_modal_popup_theme_editor_sidebar_bottom", $theme, $error_msgs ) ?>
						 
			</div><!--end #postbox-container-1-->
			<!--End Sidebar-->
		</div><!-- end #post-body-->
		<br class="clear"><br />
	</div> <!--end #poststuff -->
</form>