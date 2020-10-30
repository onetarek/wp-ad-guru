<?php
/**
 * A class to handle the process of Zone registration
 * @author oneTarek
 * @since 2.2.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Zone_Edit_Manager' ) ) :

class ADGURU_Zone_Edit_Manager{
	
	
	
	public function __construct(){

		add_filter( "adguru_zone_editor_init", array( $this, "editor_init" ) );
		add_filter( "adguru_zone_prepare_to_save", array( $this, "prepare_to_save" ), 10, 2 );//$zone, $zone_from_db
		add_action( "adguru_zone_editor_main" , array( $this, "editor_after_basic_fields" ) , 10, 2 ); //adguru_ad_editor_left_before_content_{$this->type} . params  $ad, $error_msgs

		
	}
	
	/**
	 * Initilize form builder and other things related to zone editor. 
	 */
	public function editor_init()
	{
		include_once( dirname(__FILE__)."/editor/form-design.php");
		include_once( dirname(__FILE__)."/editor/form-visibility.php");
		include_once( dirname(__FILE__)."/editor/form-inserter.php");
	}

	/**
	 * Prepare zone Data Before Save to Database
	 * @param array $zone
	 * @return array $zone
	 * @since 2.2.0	 
	 **/ 

	public function prepare_to_save( $zone, $zone_from_db ){
		
		//$ad->design_source = trim( $_POST['design_source'] );
		//$ad->theme_id = intval( $_POST['theme_id'] );

		include( dirname(__FILE__)."/editor/prepare-to-save-forms.php");
		return $zone;	
	}


	/**
	 * Print zone New/Edit Form Elements To Left Column of zone Editor
	 * @param array $zone All data of an zone
	 * @param array $error_msgs messages for error if there is any, error calculated in prepare_zone_to_save()
	 * @return void
	 * @since 2.2.0	 
	 **/
	public function editor_after_basic_fields( $zone, $error_msgs ){
		include( dirname(__FILE__)."/editor/edit-form-after-basic-fields.php");
	}//end function
	
}//end class

endif;