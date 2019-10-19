<?php
/**
 * Zone management main page
 *
 * @package     WP AD GURU
 * @subpackage  Admin/admanager
 * @copyright   Copyright (c) 2019, oneTarek
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */
 
 // Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
	
#SETUP SOME VARIABLES CAN BE USED IN ALL TAB FILE
$page = $_REQUEST['page'];
/*
	TAB ITEM SETTINGS
	A SINGLE TAB = 
		'unique_tab_slug' =>array( 
			'slug'=>'all', 
			'text'=> 'text to display with link', 
			'link'=>'url',//Optional , use this if you want to link the tab with a different page
			'file' => "file path", //Required if 'callback' is blank | File will be included for this tab output. 
			'callback'=>'function' //Required if 'file' is blank | Function will be called for this tab output.
			),
*/

$manager_tabs = apply_filters( 
	"adguru_zone_manager_tabs", 
	array(
		'all' =>array( 
			'slug'	=> 'all', 
			'text'	=> __("All Zones", "adguru" ), 
			'link'	=> 'admin.php?page='.$page.'&manager_tab=all',
			'file' 	=> ADGURU_PLUGIN_DIR."includes/admin/zone-manager/zone-list.php",
			'callback' => '' 
			),
		'edit' =>array( 
			'slug'	=> 'edit', 
			'text'	=>  __("Add new zone", "adguru" ), 
			'link'	=> 'admin.php?page='.$page.'&manager_tab=edit',
			'file' 	=> ADGURU_PLUGIN_DIR."includes/admin/zone-manager/zone-edit.php",
			'callback' => '' 
			)								
	)
);

$current_manager_tab = isset( $_REQUEST[ 'manager_tab'] ) ? $_REQUEST[ 'manager_tab'] : "all";
#BASE URL OF CURRENT TAB WITHOUT ALL OTHER PARAMETERS
$base_url = admin_url( 'admin.php?page='.$page.'&manager_tab='.$current_manager_tab );

//Run some code before opening the zone editor page. Speially we need this hook to initialize the form builder for zone editor.
if( $current_manager_tab == 'edit')
{		
	do_action( "adguru_zone_editor_init" );
}

//SETUP AN ARRAY CONTAINING ABOVE VARIABLES TO PASS WITH TAB CALLBACK FUNCTION and ACTION/FILTER HOOOKS
$current_manager_vars = array(
	"page" => $page,
	"manager_tabs" => $manager_tabs,
	"current_manager_tab" => $current_manager_tab,
	"base_url" => $base_url, 
	
);

?>
<div class="wrap">
	<h2><?php echo __("Zones", "adguru" ) ?></h2>
	<?php do_action( "adguru_zone_manager_top" ); ?>
	
	<h2 class="nav-tab-wrapper">
		<?php 
		foreach($manager_tabs as $key => $mtab )
		{ 
			$tab_class = ( $key == $current_manager_tab  )? 'nav-tab nav-tab-active' : 'nav-tab';
			$tab_link = ( isset( $mtab['link'] ) && $mtab['link'] != "") ? $mtab['link'] : admin_url( 'admin.php?page='.$page.'&manager_tab='.$key );
		?>
		<a class='<?php echo $tab_class?>' href="<?php echo $tab_link ?>"><?php echo $mtab['text'] ?></a>
		<?php do_action( "adguru_zone_manager_tab_list" ); ?>
		<?php }?>
	</h2>
	<?php do_action( "adguru_zone_manager_after_tabs" , $current_manager_vars ); ?>		
	<?php do_action( "adguru_zone_manager_{$current_manager_tab}_top" , $current_manager_vars ); ?>
	<?php
		if( isset( $manager_tabs[ $current_manager_tab ] ) )
		{
			if( isset( $manager_tabs[ $current_manager_tab ]['file'] ) && file_exists( $manager_tabs[ $current_manager_tab ]['file'] ) )
			{
				include( $manager_tabs[ $current_manager_tab ]['file'] );
			} 
			elseif( isset( $manager_tabs[ $current_manager_tab ]['callback'] ) &&  is_callable( $manager_tabs[ $current_manager_tab ]['callback'] ))
			{
				call_user_func_array( $manager_tabs[ $current_manager_tab ]['callback'] , array( $current_manager_vars ) );					
			}
			else
			{
				echo __( 'No file or callback function found for this tab', 'adguru' );
			}								
		
		}
		else
		{
			echo "<h2>".__( 'Nothing to show', 'adguru' )."</h2>";
		}			
	?>
	
	<?php do_action( "adguru_zone_manager_{$current_manager_tab}_bottom" , $current_manager_vars ); ?>
	<?php do_action( "adguru_zone_manager_bottom" , $current_manager_vars ); ?>
	
</div>
