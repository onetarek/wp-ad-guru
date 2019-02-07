<?php
/**
 * Adguru Installation
 * On plugin activation : Create database tables and set a schedule_event hook adguru_daily_event_hook to run once a day
 * on plugin deactivation : remove schedule_event hook adguru_daily_event_hook
 * @author : onetarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

function adguru_create_db_tables()
{

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$adguru_links_table = ADGURU_LINKS_TABLE;

   	$adguru_links_sql = "CREATE TABLE $adguru_links_table (
	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `ad_type` varchar(20) NOT NULL DEFAULT 'banner',
	  `zone_id` bigint(20) NOT NULL DEFAULT '0',
	  `page_type` varchar(20) NOT NULL DEFAULT '--',
	  `taxonomy` varchar(50) NOT NULL DEFAULT '--',
	  `term` varchar(50) NOT NULL DEFAULT '--',
	  `object_id` bigint(20) NOT NULL DEFAULT '0',
	  `country_code` varchar(3) NOT NULL DEFAULT '--',
	  `slide` tinyint(4) DEFAULT '1',
	  `ad_id` bigint(20) NOT NULL,
	  `percentage` tinyint(4) DEFAULT '100',
	  UNIQUE KEY id (id)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	";
   dbDelta( $adguru_links_sql );
		

}#end function adGuru_db_tables_create

//check if old plugin's databse tables are exits
function adguru_check_old_database()
{
	return adguru_check_table_exists("adguru_ads");
}

function adguru_check_table_exists( $table_name )//table name without prefix like wp_
{
	global $wpdb;
	$table = $wpdb->prefix.$table_name;
	$found = $wpdb->get_row("SHOW TABLES LIKE '".$table."'");
	if( empty( $found ) )
	{
		return false;
	}
	else
	{
		return true;
	}
}

function adguru_check_for_migration_and_set_flag()
{
	if( get_option( "adguru_migration_completed", 0 ) )
	{
		return ;
	}

	if( get_option("adguru_migration_needed", 0 ))
	{
		return;
	}

	if( adguru_check_old_database() )
	{
		update_option( "adguru_migration_needed", 1, true );
	}
}

function adguru_activation()
{
	update_option('adguru_version', ADGURU_VERSION );
	adguru_check_for_migration_and_set_flag();
	adguru_create_db_tables();
	wp_schedule_event( time(), 'daily', 'adguru_daily_event_hook' );
	do_action('adguru_activation');
}


function adguru_deactivation()
{
	wp_clear_scheduled_hook( 'adguru_daily_event_hook' );
	do_action('adguru_deactivation');
}

register_activation_hook( ADGURU_PLUGIN_FILE, 'adguru_activation' ); 

register_deactivation_hook( ADGURU_PLUGIN_FILE, 'adguru_deactivation' );
