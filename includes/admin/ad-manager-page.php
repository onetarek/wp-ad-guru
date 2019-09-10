<?php
/**
 * Ad management main page
 *
 * @package     WP AD GURU
 * @subpackage  Admin/admanager
 * @copyright   Copyright (c) 2019, oneTarek
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author 		oneTarek
 * @since       2.0.0
 */
 
 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
	
	#SETUP SOME VARIABLES THOSE CAN BE USED IN ALL TAB FILE
	$ad_types_obj = adguru()->ad_types;
	$page = $_REQUEST['page'];
	$current_ad_type = str_replace( ADGURU_ADMANAGER_PAGE_SLUG_PREFIX , "", $page );
	$current_ad_type_args = $ad_types_obj->types[ $current_ad_type ];
	$use_zone = isset( $current_ad_type_args['use_zone'] ) ? $current_ad_type_args['use_zone'] : false; 
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
		"adguru_ad_manager_tabs_{$current_ad_type}", 
		array(
			'all' =>array( 
				'slug'	=> 'all', 
				'text'	=> sprintf( __("All %s", "adguru" ) , $current_ad_type_args['plural_name'] ), 
				'link'	=>'admin.php?page='.$page.'&manager_tab=all',
				'file' 	=> ADGURU_PLUGIN_DIR."includes/admin/ad-list.php",
				'callback' => '' 
				),
			'edit' =>array( 
				'slug'	=> 'edit', 
				'text'	=> sprintf( __("Add new %s", "adguru" ) , $current_ad_type_args['name'] ), 
				'link'	=> 'admin.php?page='.$page.'&manager_tab=edit',
				'file' 	=> ADGURU_PLUGIN_DIR."includes/admin/ad-edit.php",
				'callback' => '' 
				),
			/*
			'links' =>array( 
				'slug'	=> 'links', 
				'text'	=> ( $use_zone == true )? sprintf( __("Set %s to zone", "adguru" ) , $current_ad_type_args['plural_name'] ) : sprintf( __("Set %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ), 
				'link'	=> 'admin.php?page='.$page.'&manager_tab=links',
				'file' 	=> ADGURU_PLUGIN_DIR."includes/admin/links-editor/links-editor-page.php",
				'callback' => '' 
				),
			*/
			'setup' =>array( 
				'slug'	=> 'setup', 
				//'text'	=> __("Setup", "adguru" ), 
				'text'	=> ( $use_zone == true )? sprintf( __("Set %s to zone", "adguru" ) , $current_ad_type_args['plural_name'] ) : sprintf( __("Set %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ),
				'link'	=> 'admin.php?page=adguru_setup_ads&ad_type='.$current_ad_type,
				'callback' => array( adguru()->ad_setup_manager, 'editor_page' ) 
				),									
		), 
		$current_ad_type_args
	);

	$current_manager_tab = isset( $_REQUEST[ 'manager_tab'] ) ? $_REQUEST[ 'manager_tab'] : "all";
	#BASE URL OF CURRENT TAB WITHOUT ALL OTHER PARAMETERS
	$base_url = admin_url( 'admin.php?page='.$page.'&manager_tab='.$current_manager_tab );
	
	//Run some code before opening the ad editor page. Speially we need this hook to initialize the form builder for ad editor.
	if( $current_manager_tab == 'edit')
	{
		$content_types = adguru()->content_types->types;
		// Filter contnet types list. 
		$content_types = apply_filters( "adguru_ad_editor_content_types", $content_types, $current_ad_type );
		$content_types = apply_filters( "adguru_ad_editor_content_types_{$current_ad_type}", $content_types );

		//We need this , example : Keep content type 'url' only for ad type window_popup
		if( isset($content_types['url']) && $current_ad_type != "window_popup" )
		{
			unset($content_types['url']);
		}
		foreach( $content_types as $content_type => $args )
		{
			do_action( "adguru_content_editor_init_{$content_type}", $current_ad_type );
		}
		do_action( "adguru_ad_editor_init" );
		do_action( "adguru_ad_editor_init_{$current_ad_type}" );
	}
	elseif( $current_manager_tab == 'edit_theme' && $current_ad_type == 'modal_popup' )
	{
		do_action( "adguru_modal_popuup_theme_editor_init" );
	}
	


	//SETUP AN ARRAY CONTAINING ABOVE VARIABLES TO PASS WITH TAB CALLBACK FUNCTION and ACTION/FILTER HOOOKS
	$current_manager_vars = array(
		"ad_types_obj" 			=> $ad_types_obj,
		"page" 					=> $page,
		"current_ad_type" 		=> $current_ad_type,
		"current_ad_type_args" 	=> $current_ad_type_args,
		"manager_tabs" 			=> $manager_tabs,
		"current_manager_tab" 	=> $current_manager_tab,
		"base_url" 				=> $base_url
	);
	
	?>
	<div class="wrap">
		<h2><?php echo  $current_ad_type_args['name']; ?></h2>
		<?php do_action( "adguru_ad_manager_top" , $current_ad_type_args ); ?>
		<?php do_action( "adguru_ad_manager_top_{$current_ad_type}" , $current_ad_type_args ); ?>
		
		<h2 class="nav-tab-wrapper">
			<?php 
			foreach( $manager_tabs as $key => $mtab )
			{ 
				$tab_class = ( $key == $current_manager_tab  )? 'nav-tab nav-tab-active' : 'nav-tab';
				$tab_link = ( isset( $mtab['link'] ) && $mtab['link'] != "") ? $mtab['link'] : admin_url( 'admin.php?page='.$page.'&manager_tab='.$key );
			?>
			<a class='<?php echo $tab_class?>' href="<?php echo $tab_link ?>"><?php echo $mtab['text'] ?></a>
			<?php do_action( "adguru_ad_manager_tab_list_{$current_ad_type}" ); ?>
			<?php }?>
		</h2>

		<?php do_action( "adguru_ad_manager_after_tabs" , $current_manager_vars ); ?>
		<?php do_action( "adguru_ad_manager_after_tabs_{$current_ad_type}" , $current_manager_vars ); ?>		
		<?php do_action( "adguru_ad_manager_{$current_manager_tab}_top" , $current_manager_vars ); ?>
		<?php do_action( "adguru_ad_manager_{$current_manager_tab}_top_{$current_ad_type}" , $current_manager_vars ); ?>
		
		<?php
			if( isset( $manager_tabs[ $current_manager_tab ] ) )
			{
				if( isset( $manager_tabs[ $current_manager_tab ]['file'] ) && file_exists( $manager_tabs[ $current_manager_tab ]['file'] ) )
				{
					include( $manager_tabs[ $current_manager_tab ]['file'] );
				} 
				elseif( isset( $manager_tabs[ $current_manager_tab ]['callback'] ) &&  is_callable( $manager_tabs[ $current_manager_tab ]['callback'] ) )
				{
					call_user_func_array( $manager_tabs[ $current_manager_tab ]['callback'] , array( $current_manager_vars ) );					
				}
				else
				{
					echo "No file or callback function found for this tab";
				}								
			
			}
			else
			{
				echo "<h2>Nothing to show</h2>";
			}			
		?>
		
		<?php do_action( "adguru_ad_manager_{$current_manager_tab}_bottom_{$current_ad_type}" , $current_manager_vars ); ?>
		<?php do_action( "adguru_ad_manager_{$current_manager_tab}_bottom" , $current_manager_vars ); ?>
		<?php do_action( "adguru_ad_manager_bottom_{$current_ad_type}" , $current_manager_vars ); ?>
		<?php do_action( "adguru_ad_manager_bottom" , $current_manager_vars ); ?>
		
	</div>
