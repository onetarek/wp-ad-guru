<?php
/**
 * AD SETUP MANAGER
 * @package     WP AD GURU
 * @since       2.1.0
 * @author oneTarek
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Ad_Setup_Manager' ) ) :

class ADGURU_Ad_Setup_Manager{
	public $test = "Hello";
	public function __construct(){

		//add_action('admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Render Setup manager page
	 * @since 2.1.0
	 */
	public function editor_page(){

		include_once( ADGURU_PLUGIN_DIR."includes/admin/ad-setup-manager/ad-setup-manager-page.php");
	}


}//end class
endif;