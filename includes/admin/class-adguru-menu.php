<?php
/**
 * Ad Guru Admin Menu Manager Class
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Menu' ) ) :

class ADGURU_Menu{
	
	public function __construct(){

		add_action('admin_menu', array( $this, 'admin_menu' ) );
	}


    public function admin_menu(){

        add_menu_page('Ad Guru', 'Ad Guru', 'manage_options', ADGURU_PLUGIN_SLUG , array( $this, 'main_admin_page' ), ADGURU_PLUGIN_URL.'assets/images/icon.png', 26); //Menu position 26 is after comments menu
		
		//ad zones
		add_submenu_page(ADGURU_PLUGIN_SLUG, 'Ad Guru Zones', 'Zones', 'manage_options', 'adguru_zone', array( $this, 'zone_manager_page' ) ); 
		//ad types menus
		$ad_types = adguru()->ad_types->types;
		foreach( $ad_types as $type =>$args)
		{
			add_submenu_page(ADGURU_PLUGIN_SLUG, $args['plural_name'].' Page', $args['plural_name'], 'manage_options', ADGURU_ADMANAGER_PAGE_SLUG_PREFIX.$type, array( $this, 'ad_manager_page' ) );
		}
		add_submenu_page(ADGURU_PLUGIN_SLUG, 'Setup Ads', 'Setup Ads', 'manage_options', 'adguru_setup_ads' , array( $this, 'ad_setup_manager_page') ); 
		add_submenu_page(ADGURU_PLUGIN_SLUG, 'Ad Guru Settings', 'Settings', 'manage_options', 'adguru_settings' , array( $this, 'settings_page') ); 
		//add_submenu_page(ADGURU_PLUGIN_SLUG, 'Ad Guru License', 'License', 'manage_options', 'adguru_license', array( $this, 'license_page') ); 

		$additional_sub_menus = array();
		$additional_sub_menus = apply_filters( 'adguru_additional_sub_menus', $additional_sub_menus );
		foreach( $additional_sub_menus as $menu )
		{ 
			add_submenu_page(ADGURU_PLUGIN_SLUG, $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'] , $menu['callback'] );
		}

    }
	
	public function main_admin_page(){

		include_once(ADGURU_PLUGIN_DIR."includes/admin/main-page.php");
	}
	

	public function ad_manager_page(){

		include_once(ADGURU_PLUGIN_DIR."includes/admin/ad-manager-page.php");
	}
	public function ad_setup_manager_page(){

		adguru()->ad_setup_manager->editor_page();
	}
	public function zone_manager_page(){

		include_once(ADGURU_PLUGIN_DIR."includes/admin/zone-manager/zone-manager-page.php");
	}

	public function settings_page(){

		adguru()->settings->display();
	}

	public function license_page(){

	}	


}//end class

endif;