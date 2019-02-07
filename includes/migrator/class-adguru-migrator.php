<?php
/**
 * Migration process to take old data from 'wp ad guru lite' and 'wp ad guru' pro. 
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

/*
 * ======== Mrgration Process ========
 * --- On plugin activation hook ---
 * Check flag adguru_migration_completed is set with 1, if 1 then return 
 * Check flag adguru_migration_needed is set with 1 , if 1 then return
 * Check if old plugin tables are exists
 * If table found 
 	set flag  adguru_migration_needed with 1
 * --- on normal page view ----
 * check the flag adguru_migration_needed
 * if flag found include this ADGURU_Migrator class file
 * Show admin notice "Migration needed"
 * Complete the migration process from mrgration admin page
 * Set flag adguru_migration_completed with 1
 * Set flag adguru_migration_needed with 0
 * rename all old tables ( wp_adguru_ads, wp_adguru_zones, wp_adguru_links ) with additional text '_old_backup' 
 *
 */


if( ! class_exists( 'ADGURU_Migrator' ) ) :

class ADGURU_Migrator{

	private $status = array();
	
	public function __construct(){

		add_filter("adguru_additional_sub_menus", array( $this, "add_submenu") );
		add_action( "admin_init", array( $this, "add_admin_notice" ) );
		add_action( 'wp_ajax_adguru_do_migration', array( $this, 'do_migration') );
		add_action( 'wp_ajax_adguru_do_not_migrate', array( $this, 'do_not_migrate') );

	}

	public function add_submenu( $menus ){

		$menus[] = array(
				"page_title" => "Adguru Migrator",
				"menu_title" => '<span style="color:red; font-weight:bold;">Migrator<span>',
				"capability" => "manage_options",
				"menu_slug"	 => "adguru_migrator",
				"callback" 	 => array( $this, "show_menu_page")
			);
		return $menus;
	}

	public function show_menu_page(){

		include_once(ADGURU_PLUGIN_DIR."includes/migrator/migrator-page.php");
	}

	public function add_admin_notice(){

		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : "";
		if( $page != "adguru_migrator")
		{
			adguru()->admin_notice->add('WP AD GURU : old database is detected, You can either migrate old data to this new system or not. [ <b><a style="color:red;" href="admin.php?page=adguru_migrator">Take action</a></b> ]', array("type"=>"error"));
		}
	}

	public function do_migration(){

		ignore_user_abort(true); //// Ignore user aborts and allow the script
		update_option("adguru_migration_running", 1 );

		$all_done = 0;
		$response = array();
		$response['complete'] = 0;
		$response['status'] = "fail";
		$response['msg'] = "";
		
		if( ! adguru()->user->is_permitted_to('migration') )
		{
			$response['status'] = "fail";
			$response['msg'] = "Current user is not permitted to do this";
			wp_send_json( $response );
			return;
		}

		if( get_option( "adguru_migration_completed", 0 ) == 1 )
		{
			$response['status'] = "success";
			$response['complete'] = 1;
			$this->delete_flags();
			wp_send_json( $response );
			return;
		}
		
		$this->status = get_option("adguru_migration_status", array() );
		$current_task = isset( $this->status["current_task"] ) ? $this->status["current_task"] : $this->next_task("start"); 
		$this->status["current_task"] = $current_task;
		if( ! isset( $this->status["log"] ) ){ $this->status["log"] = array(); }
		$log_count = count( $this->status["log"] );
		$this->update_status();
		
		$current_task_completed = 0;
		switch( $current_task['name'] )
		{
			case 'copy_zones': 
			{
				$current_task_completed = $this->copy_zones();	
				$response['status'] = "success";
				break;
			}
			case 'update_widgets': 
			{
				$current_task_completed = $this->update_widget();
				$response['status'] = "success";
				break;
			}
			case 'copy_ads':
			{
				$current_task_completed = $this->copy_ads();	
				$response['status'] = "success";
				break;
			}
			case 'copy_links':
			{
				$current_task_completed = $this->copy_links();
				$response['status'] = "success";
				break;
			}
			case 'rename_old_tables':
			{
				$current_task_completed = $this->rename_old_tables();
				$response['status'] = "success";
				break;
			}
			case 'finalize':
			{
				$current_task_completed = $this->finalize();
				$response['status'] = "success";
				break;
			}
		}//end switch

		if( $log_count == 0 )
		{
			$this->status["log"][] = $current_task['msg1'];
		}
		elseif( $log_count && $this->status["log"][ $log_count-1 ] != $current_task['msg1']  )
		{
			$this->status["log"][$log_count-1] = $current_task['msg1'];
		}

		if( $current_task_completed )
		{
			$this->status["log"][] = $current_task['msg2'];
			$this->status["current_task"] = $this->next_task( $this->status["current_task"] );
			if( $this->status["current_task"] === false )
			{
				$response['complete'] = 1;
				update_option( "adguru_migration_completed", 1 );
				$all_done = 1;
			}
			else
			{
				$this->status["log"][] = $this->status["current_task"]['msg1'];
			}

		}

		if( !$all_done ) { $this->update_status(); }
		$response['status_log'] = $this->get_status_log_string();
		wp_send_json( $response );
		return;
	}//end method do_migration


	private function delete_flags(){

		delete_option( "adguru_migration_status" );
		delete_option( "adguru_migration_zone_id_map" );
		delete_option( "adguru_migration_ad_id_map" );
		delete_option( "adguru_migration_running" );
		update_option( "adguru_migration_needed", 0 );
	}

	private function task_list(){

		$tasks = array(
			array( "name"=>"copy_zones","msg1"=>"Copying zones..", "msg2"=>"Zones copy done."),
			array( "name"=>"update_widgets","msg1"=>"Updateing widgets to set new zones..", "msg2"=>"Widget update done."),
			array( "name"=>"copy_ads","msg1"=>"Copying ads..", "msg2"=>"Ads copy done."),
			array( "name"=>"copy_links","msg1"=>"Copying links..", "msg2"=>"Links copy done."),
			array( "name"=>"rename_old_tables","msg1"=>"Renaming old data tables..", "msg2"=>"Renaming tables done."),
			array( "name"=>"finalize","msg1"=>"Finalizing...", "msg2"=>"All done")
		);
		return $tasks;
	}

	private function next_task( $current ){

		$tasks = $this->task_list();
		if( $current == "start" )
		{
			return $tasks[0];
		}
		elseif( $current['name'] == "finalize" )
		{
			return false;
		}
		else
		{
			$n = 0;
			foreach( $tasks as $t )
			{
				$n++;
				if( $t['name'] == $current['name'] )
				{
					break;
				}

			}
			return $tasks[ $n ];
		}
	}

	private function copy_zones(){

		$offset = isset( $this->status['zone_copy_offset'] ) ? intval( $this->status['zone_copy_offset'] ) : 0;
		
		global $wpdb;
		$zones_table = $wpdb->prefix.'adguru_zones'; 
		$zones = $wpdb->get_results( "SELECT * FROM ".$zones_table." LIMIT ".$offset.", 5");
		if( count( $zones ) )
		{
			foreach( $zones as $old_zone )
			{
				$offset ++;
				//create_new_zone_from_old_zone_data
				$new_zone = new ADGURU_Zone();
				$new_zone->name = $old_zone->name;
				$new_zone->description = $old_zone->description;
				$new_zone->width = $old_zone->width;
				$new_zone->height = $old_zone->height;
				$new_zone->active = ( $old_zone->active != 1 ) ? 0 : 1;

				$new_zone_id = adguru()->manager->save_zone( $new_zone );
				if( $new_zone_id )
				{
					$old_zone_id = $old_zone->id;
					$map = get_option( "adguru_migration_zone_id_map", array() );
					$map[ $old_zone_id ] = $new_zone_id;
					update_option( "adguru_migration_zone_id_map", $map, false );
				}
				
			}
			
		}
		else
		{
			return true;//true means task is completed
		}
		$this->status['zone_copy_offset'] = $offset;
		return false;
	}

	private function copy_ads(){
		$offset = isset( $this->status['ad_copy_offset'] ) ? intval( $this->status['ad_copy_offset'] ) : 0;
		
		global $wpdb;
		$ads_table = $wpdb->prefix.'adguru_ads'; 
		$ads = $wpdb->get_results( "SELECT * FROM ".$ads_table." LIMIT ".$offset.", 5");
		if( count( $ads ) )
		{
			foreach( $ads as $old_ad )
			{
				$offset ++;
				//create_new_ad_from_old_ad_data
				$new_ad = new ADGURU_Ad( $old_ad->ad_type );
				$new_ad->name = $old_ad->name;
				$new_ad->description = $old_ad->description;
				

				$new_ad->content_type = 'html';
				if( $old_ad->code_type == 'html' )
				{
					$new_ad->content_type = 'html';
					$new_ad->content_html = array( 'html' => $old_ad->html_code );
				}
				elseif( $old_ad->code_type ==  'link_with_image' )
				{
					$new_ad->content_type = 'image';
					$new_ad->content_image = array( 
						'source_url' => $old_ad->image_source,
						'link_url' => $old_ad->image_link,
						'link_target' => $old_ad->link_target 
						);
				}
				elseif( $old_ad->code_type ==  'link_in_iframe' )
				{
					if( $old_ad->ad_type == "window_popup" )
					{
						$new_ad->content_type = 'url';
						$new_ad->content_url = array( 
							'url' => $old_ad->iframe_source
							);
					}
					else
					{
						$new_ad->content_type = 'iframe';
						$new_ad->content_iframe = array( 
							'source_url' => $old_ad->iframe_source,
							'scrolling' => 'yes'
							);
					}
				}
				elseif( $old_ad->code_type ==  'create_your_own' )
				{
					$new_ad->content_type = 'wysiwyg';
					$new_ad->content_wysiwyg = array( 
						'html' => $old_ad->own_html 
						);
				}


				if( $old_ad->ad_type == "banner" )
				{
					$new_ad->sizing = array(
						'width' => $old_ad->width,
						'height' => $old_ad->height,
					);

				}
				elseif( $old_ad->ad_type == "modal_popup"  )
				{
					$new_ad->design_source = 'theme';
					$new_ad->theme_id = get_option('adguru_mp_default_theme_id', 0 );
					$new_ad->sizing = array(
						'mode' => 'custom',
						'custom_width' => $old_ad->width,
						'custom_width_unit' => 'px',
						'custom_height' => $old_ad->height,
						'custom_height_unit' => 'px',
					);
					
					$auto_open_delay = intval( $old_ad->popup_timing );
					if( $auto_open_delay == -1 )
					{
						$auto_open_delay = 3;
					}

					$popup_options = unserialize($old_ad->popup_options);
					
					$repeat_mode = ( isset($popup_options['repeat_mode'] ) ) ? $popup_options['repeat_mode'] : 'day';
					$cookie_duration = ( isset($popup_options['cookie_duration'] ) ) ? $popup_options['cookie_duration'] : 7;
					$cookie_num_view = ( isset($popup_options['cookie_num_view'] ) ) ? $popup_options['cookie_num_view'] : 1;

					$limitation_show_always = ( $repeat_mode == 'always' ) ? 1 : 0;
					$limitation_reset_count_after_days = 7;
					$limitation_showing_count = 1;
					if( $repeat_mode == 'view' )
					{
						$limitation_showing_count = $cookie_num_view;
						$limitation_reset_count_after_days = 365;
						
					}
					if( $repeat_mode == 'day' )
					{
						$limitation_showing_count = 1;
						$limitation_reset_count_after_days = $cookie_duration;
						
					}

					$new_ad->triggering = array(
						'auto_open_enable' => 1,
						'auto_open_delay' => $auto_open_delay, 
						'limitation_show_always' => $limitation_show_always,
						'limitation_showing_count' => $limitation_showing_count,
						'limitation_reset_count_after_days' => $limitation_reset_count_after_days,
						'limitation_apply_for_individual_page' => 0
					);

				}
				elseif( $old_ad->ad_type == "window_popup" )
				{
					
					$new_ad->sizing = array(
						'mode' => 'custom',
						'custom_width' => $old_ad->width,
						'custom_height' => $old_ad->height
					);
					
					$auto_open_delay = intval( $old_ad->popup_timing );
					if( $auto_open_delay == -1 )
					{
						$auto_open_delay = 3;
					}

					$popup_options = unserialize($old_ad->popup_options);
					
					$repeat_mode = ( isset($popup_options['repeat_mode'] ) ) ? $popup_options['repeat_mode'] : 'day';
					$cookie_duration = ( isset($popup_options['cookie_duration'] ) ) ? $popup_options['cookie_duration'] : 7;
					$cookie_num_view = ( isset($popup_options['cookie_num_view'] ) ) ? $popup_options['cookie_num_view'] : 1;

					$limitation_show_always = ( $repeat_mode == 'always' ) ? 1 : 0;
					$limitation_reset_count_after_days = 7;
					$limitation_showing_count = 1;
					if( $repeat_mode == 'view' )
					{
						$limitation_showing_count = $cookie_num_view;
						$limitation_reset_count_after_days = 365;
						
					}
					if( $repeat_mode == 'day' )
					{
						$limitation_showing_count = 1;
						$limitation_reset_count_after_days = $cookie_duration;
						
					}

					$new_ad->triggering = array(
						'open_on_body_click_enable' => 1,
						'auto_open_delay' => $auto_open_delay, 
						'limitation_show_always' => $limitation_show_always,
						'limitation_showing_count' => $limitation_showing_count,
						'limitation_reset_count_after_days' => $limitation_reset_count_after_days,
						'limitation_apply_for_individual_page' => 0
					);

					$new_ad->popup_options = array(
						'window_title' => '',
						'window_options' => array(
							'titlebar' => 1,
							'location' => ( isset( $popup_options['locationbar'] ) ) ? intval( $popup_options['locationbar'] ) : 0,
							'menubar' => ( isset( $popup_options['menubar'] ) ) ? intval( $popup_options['menubar'] ) : 0,
							'resizable' => ( isset( $popup_options['locationbar'] ) ) ? intval( $popup_options['locationbar'] ) : 1,
							'scrollbars' => ( isset( $popup_options['scrollbar'] ) ) ? intval( $popup_options['scrollbar'] ) : 0,
							'status' => ( isset( $popup_options['statusbar'] ) ) ? intval( $popup_options['statusbar'] ) : 0,
							'toolbar' => ( isset( $popup_options['toolbar'] ) ) ? intval( $popup_options['toolbar'] ) : 0,
						)

					);
				}

				$new_ad_id = adguru()->manager->save_ad( $new_ad );
				if( $new_ad_id )
				{
					$old_ad_id = $old_ad->id;
					$map = get_option( "adguru_migration_ad_id_map", array() );
					$map[ $old_ad_id ] = $new_ad_id;
					update_option( "adguru_migration_ad_id_map", $map, false );
				}
				
			}//end foreach
			
		}
		else
		{
			return true;//true means task is completed
		}
		$this->status['ad_copy_offset'] = $offset;
		return false;
	}

	private function update_widget(){
		
		$widgets = get_option( "widget_adguru-zone-widget" );
		if( ! is_array( $widgets ) ){ return true; }//true means task complete.

		$zone_id_map = get_option( "adguru_migration_zone_id_map", array() );
		
		if( count( $zone_id_map ) == 0 ){ return true; }

		$new_widgets = array();
		
		foreach( $widgets as $key => $widget )
		{
			if( is_array( $widget ) && isset( $widget['zone_id'] ) )
			{
				if( isset( $zone_id_map[ $widget['zone_id'] ] ) )
				{
					$widget['zone_id'] = $zone_id_map[ $widget['zone_id'] ];
				}
			} 		
			$new_widgets[ $key ] = $widget;
		}
		update_option( "widget_adguru-zone-widget" , $new_widgets );
		return true;

	}

	private function copy_links(){

		$offset = isset( $this->status['links_copy_offset'] ) ? intval( $this->status['links_copy_offset'] ) : 0;
		global $wpdb;
		$old_links_table = $wpdb->prefix.'adguru_links';

		$links = $wpdb->get_results( "SELECT * FROM ".$old_links_table." LIMIT ".$offset.", 5");
		if( count($links ) )
		{
			$zone_id_map = get_option( "adguru_migration_zone_id_map", array() );
			$ad_id_map = get_option( "adguru_migration_ad_id_map", array() );
			foreach( $links as $link )
			{
				$offset ++;	
				$zid = $link->zone_id;
				if( $zid != 0 )
				{
					if( ! isset( $zone_id_map[ $zid ] ) )
					{
						continue;
					}
					else
					{
						$zid = $zone_id_map[ $zid ];
					}
				}

				if( ! isset( $ad_id_map[ $link->ad_id ] ) )
				{
					continue;
				}
				else
				{
					$aid = $ad_id_map[ $link->ad_id ] ;
				}

				$SQL="INSERT INTO ".ADGURU_LINKS_TABLE." (ad_type, zone_id, page_type, taxonomy, term, object_id, country_code, slide, ad_id, percentage) 
				VALUES(
					'".$link->ad_type."', 
					".$zid.",  
					'".$link->page_type."', 
					'".$link->taxonomy."', 
					'".$link->term."', 
					".$link->object_id.", 
					'".$link->country_code."', 
					".$link->slide.", 
					".$aid.", 
					".$link->percentage."   	
					)";
					
				$res = $wpdb->query($SQL);
			}//end foreach
			

		}
		else
		{
			return true; //true means task is completed.
		}
		$this->status['links_copy_offset'] = $offset;
		return false;

	}

	private function rename_old_tables(){

		global $wpdb;
		$ads_table 		= $wpdb->prefix.'adguru_ads';
		$ads_table_n 	= $wpdb->prefix.'adguru_ads_old_backup';
		$zones_table 	= $wpdb->prefix.'adguru_zones';
		$zones_table_n 	= $wpdb->prefix.'adguru_zones_old_backup';
		$links_table 	= $wpdb->prefix.'adguru_links';
		$links_table_n 	= $wpdb->prefix.'adguru_links_old_backup';

		$wpdb->query( "RENAME TABLE `".$ads_table."` TO `".$ads_table_n."`" );
		$wpdb->query( "RENAME TABLE `".$zones_table."` TO `".$zones_table_n."`" );
		$wpdb->query( "RENAME TABLE `".$links_table."` TO `".$links_table_n."`" );
		return true;

	}

	private function finalize(){

		update_option("adguru_migration_completed", 1 );
		$this->delete_flags();
		return true;
	}

	private function update_status(){

		update_option( "adguru_migration_status", $this->status, false );
	}

	private function get_status_log_string(){

		$log = ( isset( $this->status['log'] ) && is_array( $this->status['log'] ) ) ? $this->status['log'] : array();
		return implode("<br>", $log );
	}
	
	public function do_not_migrate(){

		ignore_user_abort(true); //// Ignore user aborts and allow the script

		$response = array();
		$response['complete'] = 0;
		$response['status'] = "fail";
		$response['msg'] = "";

		if( ! adguru()->user->is_permitted_to('migration') )
		{
			$response['status'] = "fail";
			$response['msg'] = "Current user is not permitted to do this";
			wp_send_json( $response );
			return;
		}

		if( get_option( "adguru_migration_completed", 0 ) == 1 )
		{
			$response['status'] = "success";
			$response['complete'] = 1;
			wp_send_json( $response );
			return;
		}
		
		$this->rename_old_tables();
		
		update_option("adguru_migration_completed", 1 );
		update_option("adguru_migration_user_did_not_want", 1 );
		update_option("adguru_migration_needed", 0 );
		$response['status'] = "success";
		$response['complete'] = 1;
		
		wp_send_json( $response );
		return;

	}//end do_not_migration

}

endif;