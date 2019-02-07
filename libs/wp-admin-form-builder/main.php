<?php
/**
 * WordPress Admin Form Builder
 * @version : 1.0.0
 * @author : oneTarek
 * http://onetarek.com
 **/

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

define( "WPAFB_VERSION", "1.0.0" );

/*
 * Detect the url and path of a directory of a file.
 * A file can be stored in a plugin, theme or mu-plugin.
 * @param string file path
 * @return array
 */ 
function wpafb_get_file_dir_detail( $file_path )
{
	$dir_path = dirname( $file_path );
	$themes_dir = untrailingslashit( WP_CONTENT_DIR )."/themes";
	$themes_dir_url = untrailingslashit( WP_CONTENT_URL )."/themes";
	$plugins_dir = untrailingslashit(WP_PLUGIN_DIR);
	$mu_pluins_dir = untrailingslashit(WPMU_PLUGIN_DIR);
	$mu_plugins_url = untrailingslashit(WPMU_PLUGIN_URL);
	$detail = array("module_type"=>"", "path"=>$dir_path, "url"=>"" );
	if( strpos( $dir_path, $plugins_dir ) !== false )
	{
		$detail['module_type'] = "plugin";
		$detail['url'] = plugins_url( '/', $file_path );

	}
	elseif( strpos( $dir_path, $mu_pluins_dir ) !== false )
	{
		$detail['module_type'] = "mu-plugin";
		$cdir = str_replace( $mu_pluins_dir, "", $dir_path );
		$detail['url'] = $mu_plugins_url.$cdir;
	}
	elseif( strpos( $dir_path, $themes_dir ) !== false) 
	{
		$detail['module_type'] = "theme";
		$cdir = str_replace( $themes_dir, "", $dir_path );
		$detail['url'] = $themes_dir_url.$cdir;
	}
	$detail['url'] = untrailingslashit( $detail['url'] );
	return $detail;
}


//Detect the url and path of directory of this library. This library can be used and stored in a plugin,theme or mu-plugin.
$wpafb_dir_detail = wpafb_get_file_dir_detail(__FILE__);

define( "WPAFB_PATH", $wpafb_dir_detail['path'] );
define( "WPAFB_URL", $wpafb_dir_detail['url'] );

require_once(WPAFB_PATH."/class-wpafb-main.php");
require_once(WPAFB_PATH."/class-wpafb-form.php");
