<?php 
$page = $_REQUEST['page'];
$all_ad_type_args = adguru()->ad_types->types;

$current_ad_type = isset( $_GET['ad_type'] ) ? $_GET['ad_type'] : 'banner';

if(! isset( $all_ad_type_args[ $current_ad_type ] ) )
{
	return ;
}
else
{
	$current_ad_type_args = $all_ad_type_args[ $current_ad_type ];
}

$use_zone = isset( $current_ad_type_args['use_zone'] ) ? $current_ad_type_args['use_zone'] : false;
$zone_id = isset( $_GET['zone_id'] ) ? intval( $_GET['zone_id'] ) : 0 ; 
$post_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0 ; 

$this->current_ad_type = $current_ad_type;
$this->current_zone_id = $zone_id;
$this->current_post_id = $post_id;

$this->current_ad_type_args = $current_ad_type_args;
$zone_selection_needed = false;
if( $use_zone )
{ 
	if( ! $this->get_current_zone() )
	{
		$zone_selection_needed = true;
	}
	$editor_title = sprintf( __("Setup %s to Zone", "adguru" ) , $current_ad_type_args['plural_name'] );  
} 
else 
{  
	$editor_title =  sprintf( __("Setup %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ); 
}

if( ! $zone_selection_needed )
{
	$this->prepare();
	$this->print_script();
}

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADGURU_PLUGIN_URL ?>assets/css/ad-setup-manager.css" />
<div class="wrap" id="ad_setup_manger_wrap">
	<h2><?php _e( "Setup Ads", "adguru" ); ?></h2>

	<?php do_action( "adguru_ad_setup_manager_top" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_top_{$current_ad_type}" , $current_ad_type_args ); ?>

	<h2 class="nav-tab-wrapper">
		<?php 
		foreach( $all_ad_type_args as $key => $args )
		{ 
			$tab_class = ( $key == $current_ad_type  )? 'nav-tab nav-tab-active' : 'nav-tab';
			$tab_link = admin_url( 'admin.php?page=adguru_setup_ads&ad_type='.$key );
		?>
		<a class='<?php echo $tab_class?>' href="<?php echo $tab_link ?>"><?php echo $args['name'] ?></a>
		<?php }?>
	</h2>

		
	<?php do_action( "adguru_ad_setup_manager_after_tabs" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_after_tabs_{$current_ad_type}" , $current_ad_type_args ); ?>

	<div id="editor_container">
		<div id="editor_title"><?php echo $editor_title ?></div>
		<?php 
		#Print Zone select dropdown if current ad type uses zone
		if( $use_zone ){

			$zones = adguru()->manager->get_zones();
			?>
			<div id="zone-select-area">
				<form action="" method="get">
					<input type="hidden" name="page" value="<?php echo $page ?>" />
					<input type="hidden" name="ad_type" value="<?php echo $current_ad_type ?>" />
					<strong><?php _e( 'Zone', 'adguru' )?> : </strong> 
					<select id="zone_id_list" name="zone_id" onchange="this.form.submit()">
						<option value="0" <?php echo ( $zone_id == 0 ) ? ' selected="selected" ': ""  ?>><?php echo __( "Select A Zone", "adguru" ) ?></option>
						<?php 
						$valid_zone_id = false;
						foreach($zones as $zone)
						{
							$selected = '';
							$class = '';
							if( $zone->active !=1 ){ $class = ' class="inactive" '; }
							if( $zone_id == $zone->ID ){ $selected = ' selected="selected" '; $valid_zone_id = true; }
							echo '<option value="'.$zone->ID.'"'.$class.$selected.'>'.$zone->name.' - '.$zone->width.'x'.$zone->height.'</option>';
						}
						?>
					</select>
				</form>
			</div>
			<?php 

		}//end if( $use_zone )

		if( ! $zone_selection_needed ) : 
			$current_zone = $this->get_current_zone();
			if(  $current_zone && $current_zone->active != 1 )
			{
				?>
					<div style="text-align:center"><span style="color:red"><?php _e("Selected zone is deactivated, you will not see any output for this zone", "adguru")?></span>, <a href="admin.php?page=adguru_zone&manager_tab=edit&zone_id=<?php echo $current_zone->ID?>"><?php _e("Edit this zone", "adguru") ?></a></div>
				<?php 
			}
		?>
		<div id="top_control_box">
			<span class="collapse_exapnd_all_btn" id="exapnd_all_btn">expand all</span><span id="collapse_all_btn" class="collapse_exapnd_all_btn">collapse all</span>
		</div>
		<div id="condition_sets_box">
			<!-- / all .condition-set will be placed here -->			
		</div><!-- /#condition_sets_box -->
		<div id="add_condition_set_btn_box"><span id="add_condition_set_btn">Add New Ad Set &amp; Condition</span></div>

		<?php $this->render_ad_list_modal(); ?>

	<?php else: //if( ! $zone_selection_needed ) :  ?>
	<div>
		<div style="text-align: center;font-size: 40px; margin-top: 40px;text-transform: uppercase;"><?php _e('Select zone', 'adgur') ?></div>
	</div>
	<?php endif; //if( ! $zone_selection_needed ) :  ?>

	</div><!-- end #editor_container -->

	<?php do_action( "adguru_ad_setup_manager_bottom_{$current_ad_type}" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_bottom" , $current_ad_type_args ); ?>

</div><!-- end .wrap -->
